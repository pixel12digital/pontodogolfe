<?php
/**
 * Plugin Name: Bling Sync (MU)
 * Description: Fundações de sincronização com o Bling: refresh do token, cliente API e comandos WP-CLI.
 * Version: 0.1.0
 */

if (!defined('ABSPATH')) { exit; }

// Reaproveita as constantes definidas no MU-plugin de integração.
if (!defined('BLING_CLIENT_ID') || !defined('BLING_CLIENT_SECRET')) {
    return; // Aguarda o outro MU-plugin carregar as credenciais.
}

/**
 * Retorna o array salvo em option `bling_oauth`.
 */
function bling_get_oauth_option(): array {
    $opt = get_option('bling_oauth');
    return is_array($opt) ? $opt : [];
}

/**
 * Verifica se o access_token está expirado ou perto de expirar.
 */
function bling_is_token_expired(array $oauth): bool {
    $obtainedAt = (int) ($oauth['obtained_at'] ?? 0);
    $expiresIn  = (int) ($oauth['expires_in'] ?? 0);
    if ($obtainedAt <= 0 || $expiresIn <= 0) { return true; }
    $now = time();
    $grace = 120; // segundos de folga para renovar antes.
    return ($obtainedAt + $expiresIn - $grace) < $now;
}

/**
 * Atualiza o access_token usando o refresh_token, se disponível.
 */
function bling_refresh_access_token(): array {
    $oauth = bling_get_oauth_option();
    $refreshToken = (string) ($oauth['refresh_token'] ?? '');
    if ($refreshToken === '') { return $oauth; }

    $basic = base64_encode(BLING_CLIENT_ID . ':' . BLING_CLIENT_SECRET);
    $body  = [
        'grant_type'    => 'refresh_token',
        'refresh_token' => $refreshToken,
    ];

    $response = wp_remote_post('https://www.bling.com.br/Api/v3/oauth/token', [
        'timeout' => 20,
        'headers' => [
            'Content-Type'  => 'application/x-www-form-urlencoded',
            'Authorization' => 'Basic ' . $basic,
        ],
        'body' => http_build_query($body),
    ]);

    if (is_wp_error($response)) {
        return $oauth; // mantém como está; chamadas falharão com 401 e serão visíveis no log/CLI.
    }

    $code = (int) wp_remote_retrieve_response_code($response);
    $raw  = wp_remote_retrieve_body($response);
    $data = json_decode($raw, true);
    if ($code >= 200 && $code < 300 && is_array($data) && isset($data['access_token'])) {
        $updated = [
            'access_token'  => $data['access_token'],
            'refresh_token' => $data['refresh_token'] ?? $refreshToken,
            'expires_in'    => (int) ($data['expires_in'] ?? 0),
            'obtained_at'   => time(),
        ];
        update_option('bling_oauth', $updated, false);
        return $updated;
    }

    return $oauth;
}

/**
 * Garante um access_token válido, tentando refresh quando necessário.
 */
function bling_get_valid_access_token(): string {
    $oauth = bling_get_oauth_option();
    if (empty($oauth)) { return ''; }
    if (bling_is_token_expired($oauth)) {
        $oauth = bling_refresh_access_token();
    }
    return (string) ($oauth['access_token'] ?? '');
}

/**
 * Cliente genérico para chamadas à API v3 do Bling.
 * $path deve iniciar com '/'. Ex.: '/produtos'.
 */
function bling_api_request(string $method, string $path, array $query = [], $body = null) {
    $token = bling_get_valid_access_token();
    if ($token === '') {
        return new WP_Error('bling_no_token', 'Token do Bling ausente. Conecte novamente.');
    }

    $url = 'https://www.bling.com.br/Api/v3' . $path;
    if (!empty($query)) {
        $url .= '?' . http_build_query($query);
    }

    $args = [
        'method'  => strtoupper($method),
        'timeout' => 30,
        'headers' => [
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
        ],
    ];

    if ($body !== null) {
        $args['headers']['Content-Type'] = 'application/json';
        $args['body'] = is_string($body) ? $body : wp_json_encode($body);
    }

    $res = wp_remote_request($url, $args);
    if (is_wp_error($res)) { return $res; }
    $status = (int) wp_remote_retrieve_response_code($res);
    $raw    = wp_remote_retrieve_body($res);
    $json   = json_decode($raw, true);
    return [ 'status' => $status, 'body' => $json, 'raw' => $raw ];
}

/**
 * Exemplo de sincronização de produtos: apenas busca a primeira página e guarda um resumo.
 */
function bling_sync_products_sample(): array {
    $r = bling_api_request('GET', '/produtos', [ 'pagina' => 1, 'limite' => 50 ]);
    if (is_wp_error($r)) { return [ 'ok' => false, 'error' => $r->get_error_message() ]; }
    $count = 0;
    if (is_array($r['body'])) {
        // Normaliza contagem de itens, independente do envelope.
        if (isset($r['body']['data']) && is_array($r['body']['data'])) {
            $count = count($r['body']['data']);
        } elseif (isset($r['body'][0]) || empty($r['body'])) {
            $count = is_array($r['body']) ? count($r['body']) : 0;
        }
    }
    set_transient('bling_products_last_sync', [ 'time' => time(), 'count' => $count ], HOUR_IN_SECONDS);
    return [ 'ok' => true, 'status' => $r['status'], 'count' => $count ];
}

// WP-CLI commands para facilitar testes e execuções manuais.
if (defined('WP_CLI') && WP_CLI) {
    WP_CLI::add_command('bling:status', function () {
        $oauth = bling_get_oauth_option();
        $expired = empty($oauth) ? 'no-token' : (bling_is_token_expired($oauth) ? 'expired' : 'valid');
        WP_CLI::log('Token: ' . ($oauth['access_token'] ? 'present' : 'missing'));
        WP_CLI::log('State: ' . $expired);
        WP_CLI::success('Done');
    });

    WP_CLI::add_command('bling:products-sync', function () {
        $res = bling_sync_products_sample();
        if (!isset($res['ok']) || !$res['ok']) {
            WP_CLI::error('Falha: ' . ($res['error'] ?? 'desconhecida'));
        }
        WP_CLI::success('Produtos sincronizados (amostra). Itens: ' . ($res['count'] ?? 0));
    });
}

// Cron básico para rodar de hora em hora (amostra).
add_action('bling_sync_products_hourly', function () { bling_sync_products_sample(); });

if (!wp_next_scheduled('bling_sync_products_hourly')) {
    wp_schedule_event(time() + 300, 'hourly', 'bling_sync_products_hourly');
}


