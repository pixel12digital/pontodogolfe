<?php
/**
 * Plugin Name: Bling Woo Sync (MU)
 * Description: Sincronização completa de produtos do Bling para WooCommerce (upsert por SKU).
 * Version: 0.1.0
 */

if (!defined('ABSPATH')) { exit; }

// Pode carregar antes do base `bling-sync.php`. Funções checam dependência em runtime.

/**
 * Verifica se WooCommerce está ativo.
 */
function bling_is_wc_active(): bool {
    return class_exists('WooCommerce');
}

// O menu e handlers ficam disponíveis mesmo sem WooCommerce;
// a execução avisará se o WooCommerce não estiver ativo.

/**
 * Busca valor de um produto Bling tentando várias chaves.
 */
function bling_get_field(array $item, array $candidates, $default = '') {
    foreach ($candidates as $key) {
        if (isset($item[$key]) && $item[$key] !== '' && $item[$key] !== null) {
            return $item[$key];
        }
    }
    return $default;
}

/**
 * Itera por todas as páginas de produtos do Bling e aplica um callback por item.
 */
function bling_iterate_all_products(callable $onItem, int $limit = 100, int $maxPages = 1000) {
    if (!function_exists('bling_api_request')) {
        return new WP_Error('bling_missing_base', 'Dependência bling-sync ausente.');
    }
    $page = 1;
    $totalProcessed = 0;
    while ($page <= $maxPages) {
        $res = bling_api_request('GET', '/produtos', [ 'pagina' => $page, 'limite' => $limit ]);
        if (is_wp_error($res)) { return $res; }
        $data = is_array($res['body']) ? $res['body'] : [];
        $items = [];
        if (isset($data['data']) && is_array($data['data'])) {
            $items = $data['data'];
        } elseif (!empty($data) && isset($data[0])) {
            $items = $data; // fallback caso a API traga array direto
        }

        if (empty($items)) { break; }
        foreach ($items as $it) {
            $onItem(is_array($it) ? $it : []);
            $totalProcessed++;
        }
        if (count($items) < $limit) { break; }
        $page++;
    }
    return $totalProcessed;
}

/**
 * Cria/atualiza um produto no WooCommerce a partir do item do Bling.
 */
function bling_upsert_wc_product_from_bling(array $item): int {
    if (!bling_is_wc_active()) { return 0; }
    // Tenta usar SKU do Bling; se ausente, cria SKU determinístico a partir do ID/nome
    $sku  = (string) bling_get_field($item, [ 'codigo', 'sku', 'codigoProduto' ], '');
    if ($sku === '') {
        $blingId = (string) bling_get_field($item, [ 'id', 'idProduto', 'id_produto' ], '');
        if ($blingId !== '') {
            $sku = 'BLG-' . preg_replace('/[^A-Za-z0-9\-]/', '', (string) $blingId);
        } else {
            $name = (string) bling_get_field($item, [ 'nome', 'descricaoCurta', 'descricao' ], 'PRD');
            $hash = substr(hash('crc32b', $name), 0, 8);
            $sku  = 'BLG-NO-SKU-' . strtoupper($hash);
        }
    }

    $title = (string) bling_get_field($item, [ 'nome', 'descricaoCurta', 'descricao' ], 'Produto sem nome');
    $desc  = (string) bling_get_field($item, [ 'descricao', 'descricaoCompleta' ], '');
    $price = (float)  bling_get_field($item, [ 'preco', 'precoVenda', 'valor' ], 0);
    $stock = (int)    bling_get_field($item, [ 'estoque', 'saldo', 'quantidade' ], 0);

    $productId = wc_get_product_id_by_sku($sku);
    if (!$productId) {
        $productId = wp_insert_post([
            'post_title'   => $title,
            'post_content' => $desc,
            'post_status'  => 'publish',
            'post_type'    => 'product',
        ]);
        if (is_wp_error($productId) || !$productId) { return 0; }
        update_post_meta($productId, '_sku', $sku);
        wp_set_object_terms($productId, 'simple', 'product_type');
    } else {
        // Atualiza título/descrição se mudarem
        wp_update_post([
            'ID'           => $productId,
            'post_title'   => $title,
            'post_content' => $desc,
        ]);
    }

    // Preço e estoque
    update_post_meta($productId, '_regular_price', (string) $price);
    update_post_meta($productId, '_price', (string) $price);
    update_post_meta($productId, '_manage_stock', 'yes');
    update_post_meta($productId, '_stock', max(0, $stock));
    update_post_meta($productId, '_stock_status', $stock > 0 ? 'instock' : 'outofstock');

    // Imagem destacada (se houver URL)
    $imageUrl = bling_get_field($item, [ 'imagem', 'urlImagem', 'image', 'img' ], '');
    if ($imageUrl) {
        bling_set_product_featured_image_from_url($productId, $imageUrl);
    }

    return (int) $productId;
}

/**
 * Sideload de imagem e setar como destacada.
 */
function bling_set_product_featured_image_from_url(int $productId, string $url): void {
    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    $attachmentId = media_sideload_image($url, $productId, null, 'id');
    if (!is_wp_error($attachmentId) && $attachmentId) {
        set_post_thumbnail($productId, $attachmentId);
    }
}

/**
 * Sincronização completa: pagina todos os produtos e faz upsert.
 */
function bling_products_full_sync(): array {
    if (!function_exists('bling_api_request')) {
        return [ 'ok' => false, 'error' => 'Dependência bling-sync ausente.' ];
    }
    if (!bling_is_wc_active()) {
        return [ 'ok' => false, 'error' => 'WooCommerce não está ativo.' ];
    }
    $created = 0; $updated = 0; $processed = 0; $errors = 0;
    $result = bling_iterate_all_products(function (array $it) use (&$created, &$updated, &$processed, &$errors) {
        $sku = (string) bling_get_field($it, [ 'codigo', 'sku', 'codigoProduto' ], '');
        if ($sku === '') { $errors++; return; }
        $beforeId = wc_get_product_id_by_sku($sku);
        $id = bling_upsert_wc_product_from_bling($it);
        if ($id) {
            if ($beforeId) { $updated++; } else { $created++; }
        } else {
            $errors++;
        }
        $processed++;
    }, 100, 2000);

    if (is_wp_error($result)) {
        return [ 'ok' => false, 'error' => $result->get_error_message() ];
    }
    $summary = [
        'ok' => true,
        'processed' => $processed,
        'created' => $created,
        'updated' => $updated,
        'errors' => $errors,
        'time' => time(),
    ];
    set_transient('bling_products_last_full_sync', $summary, DAY_IN_SECONDS);
    return $summary;
}

// CLI
if (defined('WP_CLI') && WP_CLI) {
    WP_CLI::add_command('bling:products-full-sync', function () {
        $res = bling_products_full_sync();
        if (empty($res['ok'])) {
            WP_CLI::error('Falha: ' . ($res['error'] ?? 'desconhecida'));
        }
        WP_CLI::log('Processados: ' . ($res['processed'] ?? 0));
        WP_CLI::log('Criados: ' . ($res['created'] ?? 0) . ' | Atualizados: ' . ($res['updated'] ?? 0) . ' | Erros: ' . ($res['errors'] ?? 0));
        WP_CLI::success('Sincronização concluída.');
    });
}

// Admin page para execução manual (somente administradores)
add_action('admin_menu', function () {
    // Página visível em Ferramentas → Bling Sync
    add_management_page(
        'Bling Sync',
        'Bling Sync',
        'manage_options',
        'bling-sync',
        function () {
            if (!current_user_can('manage_options')) { wp_die('Sem permissão.'); }
            if (!bling_is_wc_active()) {
                echo '<div class="wrap"><h1>Bling → WooCommerce</h1><div class="notice notice-error"><p>WooCommerce não está ativo. Ative o plugin para executar a sincronização.</p></div></div>';
                return;
            }
            $ran  = false;
            $resp = null;
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bling_sync_action'])) {
                check_admin_referer('bling_sync_run');
                $resp = bling_products_full_sync();
                $ran  = true;
            }
            echo '<div class="wrap">';
            echo '<h1>Bling → WooCommerce</h1>';
            echo '<p>Execute a sincronização completa de produtos.</p>';
            echo '<form method="post">';
            wp_nonce_field('bling_sync_run');
            echo '<input type="hidden" name="bling_sync_action" value="run" />';
            submit_button('Executar sincronização agora');
            echo '</form>';
            if ($ran) {
                echo '<h2>Resultado</h2><pre>' . esc_html(print_r($resp, true)) . '</pre>';
            } else {
                $last = get_transient('bling_products_last_full_sync');
                if ($last) {
                    echo '<h2>Última execução</h2><pre>' . esc_html(print_r($last, true)) . '</pre>';
                }
            }
            echo '</div>';
        }
    );

    add_submenu_page(
        null,
        'Executar Sync Bling',
        'Executar Sync Bling',
        'manage_options',
        'bling-run-sync',
        function () {
            if (!current_user_can('manage_options')) { wp_die('Sem permissão.'); }
            check_admin_referer('bling_run_sync');
            if (!bling_is_wc_active()) {
                echo '<div class="wrap"><h1>Sync Bling</h1><div class="notice notice-error"><p>WooCommerce não está ativo.</p></div></div>';
                return;
            }
            $res = bling_products_full_sync();
            echo '<div class="wrap"><h1>Sync Bling</h1><pre>' . esc_html(print_r($res, true)) . '</pre></div>';
        }
    );
});

// Cron diário
add_action('bling_products_full_sync_daily', function () { bling_products_full_sync(); });
if (!wp_next_scheduled('bling_products_full_sync_daily')) {
    wp_schedule_event(time() + 600, 'daily', 'bling_products_full_sync_daily');
}


