<?php
/**
 * Personalização do Dashboard para Gerentes de Loja
 * Foco em: Vendas, Estoque, Pedidos, Atividades importantes
 */

// Remover widgets padrão não essenciais
add_action('wp_dashboard_setup', 'pdg_remove_default_dashboard_widgets', 999);

function pdg_remove_default_dashboard_widgets() {
    // Remover widgets padrão
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');        // Rascunho rápido
    remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side');      // Rascunhos recentes
    remove_meta_box('dashboard_primary', 'dashboard', 'side');            // Notícias do WordPress
    remove_meta_box('dashboard_secondary', 'dashboard', 'side');          // Eventos do WordPress
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');  // Comentários recentes
    remove_meta_box('dashboard_activity', 'dashboard', 'normal');         // Atividade (regenerada depois)
}

// Adicionar widgets customizados para loja
add_action('wp_dashboard_setup', 'pdg_add_store_dashboard_widgets');

function pdg_add_store_dashboard_widgets() {
    // Widget de resumo de vendas (principal)
    wp_add_dashboard_widget(
        'pdg_sales_summary',
        '📊 Resumo de Vendas Hoje',
        'pdg_dashboard_sales_summary'
    );
    
    // Widget de pedidos pendentes
    wp_add_dashboard_widget(
        'pdg_pending_orders',
        '🛒 Pedidos Pendentes',
        'pdg_dashboard_pending_orders'
    );
    
    // Widget de produtos com estoque baixo
    wp_add_dashboard_widget(
        'pdg_low_stock',
        '⚠️ Estoque Baixo',
        'pdg_dashboard_low_stock'
    );
    
    // Widget de atividade recente (simplificado)
    wp_add_dashboard_widget(
        'pdg_store_activity',
        '📝 Atividade Recente',
        'pdg_dashboard_store_activity'
    );
    
    // Widget de links rápidos
    wp_add_dashboard_widget(
        'pdg_quick_links',
        '🔗 Links Rápidos',
        'pdg_dashboard_quick_links'
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

