<?php
/**
 * Plugin Name: Woo Setup Pages (One-Time)
 * Description: Cria páginas base do WooCommerce e configura permalinks na primeira execução.
 * Version: 0.1.0
 */

if (!defined('ABSPATH')) { exit; }

add_action('init', function () {
	if (get_option('ponto_woo_pages_setup_done')) {
		return;
	}

	// Garantir que a função wc já não seja necessária apenas para páginas
	$pages = [
		'shop'     => [ 'title' => 'Loja',       'content' => '' ], // conteúdo vazio
		'cart'     => [ 'title' => 'Carrinho',   'content' => '[woocommerce_cart]' ],
		'checkout' => [ 'title' => 'Finalizar compra', 'content' => '[woocommerce_checkout]' ],
		'account'  => [ 'title' => 'Minha conta','content' => '[woocommerce_my_account]' ],
	];

	$created_ids = [];
	foreach ($pages as $key => $def) {
		// Tenta localizar por título se já existe
		$page = get_page_by_title($def['title']);
		if ($page && $page->post_status !== 'trash') {
			$created_ids[$key] = $page->ID;
			continue;
		}
		$created_ids[$key] = wp_insert_post([
			'post_title'   => $def['title'],
			'post_content' => $def['content'],
			'post_status'  => 'publish',
			'post_type'    => 'page',
		]);
	}

	// Salva opções esperadas pelo WooCommerce
	if (!empty($created_ids['shop'])) {
		update_option('woocommerce_shop_page_id', (int)$created_ids['shop']);
	}
	if (!empty($created_ids['cart'])) {
		update_option('woocommerce_cart_page_id', (int)$created_ids['cart']);
	}
	if (!empty($created_ids['checkout'])) {
		update_option('woocommerce_checkout_page_id', (int)$created_ids['checkout']);
	}
	if (!empty($created_ids['account'])) {
		update_option('woocommerce_myaccount_page_id', (int)$created_ids['account']);
	}

	// Configura estrutura de links permanentes /%postname%/
	$structure = get_option('permalink_structure');
	if ($structure !== '/%postname%/') {
		update_option('permalink_structure', '/%postname%/');
		add_action('shutdown', function () { flush_rewrite_rules(); });
	}

	update_option('ponto_woo_pages_setup_done', current_time('mysql'));
});
