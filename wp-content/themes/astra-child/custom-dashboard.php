<?php
/**
 * Personalização do Dashboard para Gerentes de Loja
 * Foco em: Vendas, Estoque, Pedidos, Atividades importantes
 */

// Remover widgets padrão não essenciais
add_action('wp_dashboard_setup', 'pdg_remove_default_dashboard_widgets', 999);

function pdg_remove_default_dashboard_widgets() {
    // Remover widgets padrão do WordPress
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');        // Rascunho rápido
    remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side');      // Rascunhos recentes
    remove_meta_box('dashboard_primary', 'dashboard', 'side');            // Notícias do WordPress
    remove_meta_box('dashboard_secondary', 'dashboard', 'side');          // Eventos do WordPress
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');  // Comentários recentes
    remove_meta_box('dashboard_activity', 'dashboard', 'normal');         // Atividade (regenerada depois)
    remove_meta_box('dashboard_right_now', 'dashboard', 'normal');        // Em resumo (info do WP)
    remove_meta_box('dashboard_site_health', 'dashboard', 'normal');      // Saúde do Site
    remove_meta_box('dashboard_php_nag', 'dashboard', 'normal');          // Avisos de PHP
    
    // Remover widgets do WooCommerce
    if (class_exists('WooCommerce')) {
        remove_meta_box('wc_admin_dashboard_setup', 'dashboard', 'normal');  // Setup do WooCommerce
        remove_meta_box('woocommerce_dashboard_status', 'dashboard', 'normal'); // Status genérico
        remove_meta_box('woocommerce_dashboard_recent_reviews', 'dashboard', 'normal'); // Reviews recentes
    }
}

// Adicionar widgets customizados para loja
add_action('wp_dashboard_setup', 'pdg_add_store_dashboard_widgets');

function pdg_add_store_dashboard_widgets() {
    // Widget de resumo de vendas (principal) - normal
    wp_add_dashboard_widget(
        'pdg_sales_summary',
        '📊 Resumo de Vendas Hoje',
        'pdg_dashboard_sales_summary',
        null,
        null,
        'normal',
        'high' // Prioridade alta para ficar no topo
    );
    
    // Widget de pedidos pendentes - normal
    wp_add_dashboard_widget(
        'pdg_pending_orders',
        '🛒 Pedidos Pendentes',
        'pdg_dashboard_pending_orders',
        null,
        null,
        'normal',
        'core' // Prioridade normal
    );
    
    // Widget de produtos com estoque baixo - normal
    wp_add_dashboard_widget(
        'pdg_low_stock',
        '⚠️ Estoque Baixo',
        'pdg_dashboard_low_stock',
        null,
        null,
        'normal',
        'core' // Prioridade normal
    );
    
    // Widget de atividade recente (simplificado) - side para ocupar lateral
    wp_add_dashboard_widget(
        'pdg_store_activity',
        '📝 Atividade Recente',
        'pdg_dashboard_store_activity',
        null,
        null,
        'side',
        'high' // Prioridade alta
    );
    
    // Widget de links rápidos - side para ocupar lateral
    wp_add_dashboard_widget(
        'pdg_quick_links',
        '🔗 Links Rápidos',
        'pdg_dashboard_quick_links',
        null,
        null,
        'side',
        'high' // Prioridade alta para ficar no topo
    );
}

/**
 * Widget: Resumo de Vendas
 */
function pdg_dashboard_sales_summary() {
    if (!class_exists('WooCommerce')) {
        echo '<p>WooCommerce não está instalado.</p>';
        return;
    }
    
    $today = date('Y-m-d');
    $orders = wc_get_orders([
        'status' => ['processing', 'completed'],
        'date_created' => strtotime($today),
        'limit' => -1
    ]);
    
    $total_sales = 0;
    $total_orders = count($orders);
    
    foreach ($orders as $order) {
        $total_sales += $order->get_total();
    }
    
    $avg_order_value = $total_orders > 0 ? $total_sales / $total_orders : 0;
    
    ?>
    <div style="padding: 10px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px;">
            <div style="background: linear-gradient(135deg, #1F5D3F 0%, #719B57 100%); color: white; padding: 15px; border-radius: 8px; text-align: center;">
                <div style="font-size: 32px; font-weight: bold; margin-bottom: 5px;"><?php echo $total_orders; ?></div>
                <div style="font-size: 12px; opacity: 0.9;">Pedidos Hoje</div>
            </div>
            
            <div style="background: linear-gradient(135deg, #2c6e49 0%, #8aad6d 100%); color: white; padding: 15px; border-radius: 8px; text-align: center;">
                <div style="font-size: 28px; font-weight: bold; margin-bottom: 5px;"><?php echo wc_price($total_sales); ?></div>
                <div style="font-size: 12px; opacity: 0.9;">Vendas Hoje</div>
            </div>
            
            <div style="background: #f8f9fa; border: 2px solid #1F5D3F; padding: 15px; border-radius: 8px; text-align: center;">
                <div style="font-size: 24px; font-weight: bold; color: #1F5D3F; margin-bottom: 5px;"><?php echo wc_price($avg_order_value); ?></div>
                <div style="font-size: 12px; color: #666;">Ticket Médio</div>
            </div>
        </div>
        
        <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd;">
            <a href="<?php echo admin_url('admin.php?page=wc-reports&tab=orders&range=today'); ?>" 
               style="display: inline-block; background: #1F5D3F; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px;">
                Ver Relatórios →
            </a>
        </div>
    </div>
    <?php
}

/**
 * Widget: Pedidos Pendentes
 */
function pdg_dashboard_pending_orders() {
    if (!class_exists('WooCommerce')) {
        echo '<p>WooCommerce não está instalado.</p>';
        return;
    }
    
    $orders = wc_get_orders([
        'status' => 'pending',
        'limit' => 10,
        'orderby' => 'date',
        'order' => 'DESC'
    ]);
    
    if (empty($orders)) {
        echo '<div style="padding: 15px; text-align: center; color: #719B57;">✓ Nenhum pedido pendente!</div>';
        return;
    }
    
    echo '<ul style="margin: 0; padding: 0; list-style: none;">';
    foreach ($orders as $order) {
        $customer_name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
        $order_date = $order->get_date_created()->date_i18n('d/m/Y H:i');
        $order_total = $order->get_total();
        ?>
        <li style="padding: 12px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <strong><a href="<?php echo $order->get_edit_order_url(); ?>">#<?php echo $order->get_id(); ?></a></strong>
                <br>
                <small style="color: #666;"><?php echo esc_html($customer_name); ?></small>
                <br>
                <small style="color: #999;"><?php echo $order_date; ?></small>
            </div>
            <div style="text-align: right;">
                <strong style="color: #1F5D3F;"><?php echo wc_price($order_total); ?></strong>
            </div>
        </li>
        <?php
    }
    echo '</ul>';
    ?>
    <div style="padding: 10px; background: #f8f9fa; border-top: 1px solid #ddd; margin-top: 10px;">
        <a href="<?php echo admin_url('edit.php?post_type=shop_order&post_status=wc-pending'); ?>" 
           style="color: #1F5D3F; text-decoration: none; font-weight: bold;">
            Ver todos os pedidos →
        </a>
    </div>
    <?php
}

/**
 * Widget: Produtos com Estoque Baixo
 */
function pdg_dashboard_low_stock() {
    if (!class_exists('WooCommerce')) {
        echo '<p>WooCommerce não está instalado.</p>';
        return;
    }
    
    $low_stock_products = [];
    
    $products = wc_get_products([
        'status' => 'publish',
        'limit' => 100,
        'stock_status' => 'instock'
    ]);
    
    foreach ($products as $product) {
        if ($product->managing_stock() && $product->get_stock_quantity() < 10 && $product->get_stock_quantity() > 0) {
            $low_stock_products[] = $product;
        }
    }
    
    if (empty($low_stock_products)) {
        echo '<div style="padding: 15px; text-align: center; color: #719B57;">✓ Estoque em dia!</div>';
        return;
    }
    
    echo '<ul style="margin: 0; padding: 0; list-style: none;">';
    foreach ($low_stock_products as $product) {
        $stock_quantity = $product->get_stock_quantity();
        $stock_percent = ($stock_quantity / 10) * 100;
        ?>
        <li style="padding: 10px; border-bottom: 1px solid #eee;">
            <strong><a href="<?php echo get_edit_post_link($product->get_id()); ?>"><?php echo esc_html($product->get_name()); ?></a></strong>
            <div style="margin-top: 5px;">
                <div style="background: #e9ecef; border-radius: 10px; height: 8px; overflow: hidden;">
                    <div style="background: #ffc107; height: 100%; width: <?php echo $stock_percent; ?>%;"></div>
                </div>
                <small style="color: #666;"><?php echo $stock_quantity; ?> unidades restantes</small>
            </div>
        </li>
        <?php
    }
    echo '</ul>';
    ?>
    <div style="padding: 10px; background: #f8f9fa; border-top: 1px solid #ddd; margin-top: 10px;">
        <a href="<?php echo admin_url('admin.php?page=wc-admin&path=/products'); ?>" 
           style="color: #1F5D3F; text-decoration: none; font-weight: bold;">
            Gerenciar produtos →
        </a>
    </div>
    <?php
}

/**
 * Widget: Atividade Recente
 */
function pdg_dashboard_store_activity() {
    $activities = [];
    
    // Últimos pedidos
    if (class_exists('WooCommerce')) {
        $recent_orders = wc_get_orders([
            'limit' => 5,
            'orderby' => 'date',
            'order' => 'DESC'
        ]);
        
        foreach ($recent_orders as $order) {
            $activities[] = [
                'type' => 'order',
                'date' => $order->get_date_created()->getTimestamp(),
                'text' => 'Novo pedido #' . $order->get_id() . ' - ' . wc_price($order->get_total()),
                'url' => $order->get_edit_order_url(),
                'icon' => '🛒'
            ];
        }
    }
    
    // Ordenar por data (mais recentes primeiro)
    usort($activities, function($a, $b) {
        return $b['date'] - $a['date'];
    });
    
    // Limitar a 5 itens
    $activities = array_slice($activities, 0, 5);
    
    if (empty($activities)) {
        echo '<div style="padding: 15px; text-align: center; color: #999;">Sem atividade recente.</div>';
        return;
    }
    
    echo '<ul style="margin: 0; padding: 0; list-style: none;">';
    foreach ($activities as $activity) {
        $time_ago = human_time_diff($activity['date'], current_time('timestamp')) . ' atrás';
        ?>
        <li style="padding: 10px; border-bottom: 1px solid #eee;">
            <span style="font-size: 18px; margin-right: 8px;"><?php echo $activity['icon']; ?></span>
            <a href="<?php echo $activity['url']; ?>" style="text-decoration: none; color: #333;">
                <?php echo esc_html($activity['text']); ?>
            </a>
            <br>
            <small style="color: #999; margin-left: 26px;"><?php echo $time_ago; ?></small>
        </li>
        <?php
    }
    echo '</ul>';
}

/**
 * Widget: Links Rápidos
 */
function pdg_dashboard_quick_links() {
    $links = [
        'Novo Produto' => admin_url('post-new.php?post_type=product'),
        'Todos os Produtos' => admin_url('edit.php?post_type=product'),
        'Pedidos' => admin_url('edit.php?post_type=shop_order'),
        'Relatórios' => admin_url('admin.php?page=wc-reports'),
        'Cupons' => admin_url('edit.php?post_type=shop_coupon'),
        'Configurações' => admin_url('admin.php?page=wc-settings'),
    ];
    
    echo '<div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; padding: 10px;">';
    foreach ($links as $label => $url) {
        ?>
        <a href="<?php echo esc_url($url); ?>" 
           style="display: block; padding: 12px; background: #f8f9fa; border: 2px solid #1F5D3F; border-radius: 5px; text-align: center; text-decoration: none; color: #1F5D3F; transition: all 0.3s;">
            <?php echo esc_html($label); ?>
        </a>
        <?php
    }
    echo '</div>';
}

// =====================================================
// REMOVER ELEMENTOS DA BARRA DE ADMINISTRAÇÃO
// =====================================================

// Remover itens desnecessários da admin bar (barra superior)
add_action('admin_bar_menu', 'pdg_remove_admin_bar_nodes', 999);

function pdg_remove_admin_bar_nodes($wp_admin_bar) {
    // Remover sempre (para todos os usuários exceto super admins em multisite)
    $wp_admin_bar->remove_node('wp-logo');          // Logo do WordPress
    $wp_admin_bar->remove_node('about');            // Sobre o WordPress
    $wp_admin_bar->remove_node('wporg');            // WordPress.org
    $wp_admin_bar->remove_node('documentation');    // Documentação
    $wp_admin_bar->remove_node('support-forums');   // Fóruns de suporte
    $wp_admin_bar->remove_node('feedback');         // Feedback
    $wp_admin_bar->remove_node('comments');         // Link de comentários
    $wp_admin_bar->remove_node('customize');        // Personalizar
    
    // Remover apenas para usuários que não são admins
    if (!current_user_can('manage_options')) {
        $wp_admin_bar->remove_node('updates');      // Atualizações
        $wp_admin_bar->remove_node('new-content');  // Adicionar Novo
    }
}

// =====================================================
// PERSONALIZAR BARRA DE ADMINISTRAÇÃO
// =====================================================

// Adicionar logo Ponto do Golfe na admin bar
add_action('admin_bar_menu', 'pdg_add_custom_admin_bar_logo', 1);

function pdg_add_custom_admin_bar_logo($wp_admin_bar) {
    $wp_admin_bar->add_node([
        'id'    => 'pdg-logo',
        'title' => '<span style="background: linear-gradient(135deg, #1F5D3F 0%, #719B57 100%); color: white; padding: 5px 12px; border-radius: 4px; font-weight: bold;">⛳ PONTO DO GOLFE</span>',
        'href'  => home_url('/'),
        'meta'  => [
            'target' => '_blank',
            'title'  => 'Ir para o site'
        ]
    ]);
}

// =====================================================
// REMOVER MENUS NÃO ESSENCIAIS
// =====================================================

// Remover itens desnecessários do menu lateral
add_action('admin_menu', 'pdg_remove_admin_menu_items', 999);

function pdg_remove_admin_menu_items() {
    // Remover para todos os usuários
    remove_menu_page('edit.php');                    // Posts/Artigos
    remove_menu_page('edit-comments.php');           // Comentários
    
    // Remover apenas para usuários que não são administradores
    if (!current_user_can('manage_options')) {
        remove_menu_page('themes.php');              // Aparência
        remove_menu_page('plugins.php');             // Plugins
        remove_menu_page('tools.php');               // Ferramentas
        remove_menu_page('options-general.php');     // Configurações gerais
    }
}

// =====================================================
// CSS PERSONALIZADO PARA ADMIN
// =====================================================

// Adicionar estilos customizados no admin
add_action('admin_head', 'pdg_custom_admin_styles');

function pdg_custom_admin_styles() {
    ?>
    <style type="text/css">
        /* Personalizar cores do admin */
        #wpadminbar {
            background: linear-gradient(135deg, #1F5D3F 0%, #719B57 100%) !important;
        }
        
        #wpadminbar .ab-item,
        #wpadminbar .ab-empty-item {
            color: white !important;
        }
        
        #wpadminbar .ab-item:hover {
            background-color: rgba(255, 255, 255, 0.1) !important;
        }
        
        /* Remover "Olá, Nome" e substituir */
        #wpadminbar #wp-admin-bar-my-account > .ab-item::before {
            content: "👤 ";
            padding-right: 5px;
        }
        
        /* Personalizar dashboard */
        .wp-core-ui .button-primary {
            background: #1F5D3F !important;
            border-color: #1F5D3F !important;
            text-shadow: none !important;
        }
        
        .wp-core-ui .button-primary:hover {
            background: #719B57 !important;
            border-color: #719B57 !important;
        }
        
        /* Remover footer do WordPress */
        #footer-left,
        #footer-upgrade {
            display: none !important;
        }
        
        /* Personalizar títulos */
        .wp-heading-inline {
            color: #1F5D3F;
        }
        
        /* Esconder avisos de "Obrigado por criar com WordPress" */
        #footer-thankyou {
            display: none !important;
        }
        
        /* Esconder badges do WooCommerce */
        .woocommerce-store-alerts__container {
            display: none !important;
        }
        
        /* Personalizar navegação lateral */
        #adminmenu .wp-menu-arrow {
            display: none !important;
        }
        
        #adminmenu li.current > a,
        #adminmenu li.wp-has-current-submenu > a {
            background-color: #1F5D3F !important;
        }
        
        #adminmenu li.wp-has-current-submenu .wp-submenu li.current a,
        #adminmenu li.wp-has-current-submenu .wp-submenu li a:focus,
        #adminmenu li.wp-has-current-submenu .wp-submenu li a:hover {
            background-color: #719B57 !important;
        }
    </style>
    <?php
}

// Remover rodapé do WordPress
add_filter('admin_footer_text', '__return_empty_string');
add_filter('update_footer', '__return_empty_string', 11);

