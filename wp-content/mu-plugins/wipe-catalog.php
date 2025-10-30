<?php
/**
 * Plugin Name: Wipe Catalog (Temporary MU)
 * Description: Remove TODOS os produtos/variações/termos do WooCommerce e todos os anexos (mídias). USE COM CAUTELA.
 * Version: 0.1.0
 */

if (!defined('ABSPATH')) { exit; }

add_action('admin_menu', function () {
	add_management_page(
		'Wipe Catalog',
		'Wipe Catalog',
		'manage_options',
		'wipe-catalog',
		'wipe_catalog_render'
	);
});

function wipe_catalog_render() {
	if (!current_user_can('manage_options')) {
		wp_die('Sem permissão.');
	}

	$step = isset($_GET['step']) ? sanitize_text_field(wp_unslash($_GET['step'])) : '';
	$nonce = isset($_REQUEST['_wpnonce']) ? sanitize_text_field(wp_unslash($_REQUEST['_wpnonce'])) : '';
	
	echo '<div class="wrap"><h1>Wipe Catalog</h1>';
	if (!$step) {
		$counts = wipe_catalog_count();
		echo '<p><strong>Atenção:</strong> esta ação apagará definitivamente o catálogo e as mídias.</p>';
		echo '<ul>';
		echo '<li>Produtos: ' . intval($counts['products']) . '</li>';
		echo '<li>Variações: ' . intval($counts['variations']) . '</li>';
		echo '<li>Termos (categorias/tags): ' . intval($counts['terms']) . '</li>';
		echo '<li>Mídias: ' . intval($counts['attachments']) . '</li>';
		echo '</ul>';
		$url = wp_nonce_url(admin_url('tools.php?page=wipe-catalog&step=products'), 'wipe_catalog');
		echo '<p><a class="button button-primary" href="' . esc_url($url) . '">Apagar agora</a></p>';
		echo '</div>';
		return;
	}

	if (!wp_verify_nonce($nonce, 'wipe_catalog')) {
		wp_die('Nonce inválido.');
	}

	switch ($step) {
		case 'products':
			wipe_catalog_delete_posts(['product','product_variation'], 200);
			$next = admin_url('tools.php?page=wipe-catalog&step=products&_wpnonce=' . $nonce);
			if (!wipe_catalog_has_posts(['product','product_variation'])) {
				$next = admin_url('tools.php?page=wipe-catalog&step=terms&_wpnonce=' . $nonce);
			}
			break;
		case 'terms':
			wipe_catalog_delete_terms();
			$next = admin_url('tools.php?page=wipe-catalog&step=media&_wpnonce=' . $nonce);
			break;
		case 'media':
			wipe_catalog_delete_posts(['attachment'], 200, true);
			$next = admin_url('tools.php?page=wipe-catalog&step=media&_wpnonce=' . $nonce);
			if (!wipe_catalog_has_posts(['attachment'])) {
				$next = admin_url('tools.php?page=wipe-catalog&step=done&_wpnonce=' . $nonce);
			}
			break;
		case 'done':
			delete_transient('wc_ptr');
			wp_cache_flush();
			echo '<div class="notice notice-success"><p>Catálogo e mídias apagados com sucesso.</p></div>';
			echo '<p><a class="button" href="' . esc_url(admin_url('tools.php?page=wipe-catalog')) . '">Voltar</a></p>';
			echo '</div>';
			return;
		default:
			$next = admin_url('tools.php?page=wipe-catalog');
	}

	echo '<p>Processando... Se não redirecionar automaticamente, <a href="' . esc_url($next) . '">clique aqui</a>.</p>';
	echo '<script>setTimeout(function(){window.location.href=' . json_encode($next) . '}, 800);</script>';
	echo '</div>';
}

function wipe_catalog_has_posts($types) {
	$q = new WP_Query([
		'post_type'      => $types,
		'posts_per_page' => 1,
		'post_status'    => 'any',
		'fields'         => 'ids',
	]);
	return $q->have_posts();
}

function wipe_catalog_delete_posts($types, $batch = 200, $force = false) {
	$q = new WP_Query([
		'post_type'      => $types,
		'posts_per_page' => $batch,
		'post_status'    => 'any',
		'fields'         => 'ids',
		'orderby'        => 'ID',
		'order'          => 'ASC',
	]);
	if ($q->have_posts()) {
		foreach ($q->posts as $id) {
			if (get_post_type($id) === 'attachment') {
				wp_delete_attachment($id, true);
			} else {
				wp_delete_post($id, true);
			}
		}
	}
}

function wipe_catalog_delete_terms() {
	$taxes = ['product_cat','product_tag','product_brand'];
	foreach ($taxes as $tax) {
		$terms = get_terms(['taxonomy' => $tax, 'hide_empty' => false]);
		if (!is_wp_error($terms)) {
			foreach ($terms as $term) {
				wp_delete_term($term->term_id, $tax);
			}
		}
	}
}

function wipe_catalog_count() {
	global $wpdb;
	$products   = (int) $wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->posts} WHERE post_type='product'");
	$variations = (int) $wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->posts} WHERE post_type='product_variation'");
	$attachments= (int) $wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->posts} WHERE post_type='attachment'");
	$terms      = 0;
	$taxes = ['product_cat','product_tag','product_brand'];
	foreach ($taxes as $tax) {
		$terms += (int) wp_count_terms(['taxonomy' => $tax, 'hide_empty' => false]);
	}
	return compact('products','variations','attachments','terms');
}
