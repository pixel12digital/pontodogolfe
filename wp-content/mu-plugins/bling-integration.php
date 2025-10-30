<?php
/**
 * Plugin Name: Bling Integration (MU)
 * Description: Callback OAuth2 do Bling, troca de código por token e armazenamento no WP.
 * Version: 0.1.3
 */

if (!defined('ABSPATH')) { exit; }

// Configure aqui (provisório). Em produção, mova para uma tela de settings.
if (!defined('BLING_CLIENT_ID')) {
	define('BLING_CLIENT_ID', '7e8041a70590aa386a1a3efbf7ef599349de6720');
}
if (!defined('BLING_CLIENT_SECRET')) {
	define('BLING_CLIENT_SECRET', '36e3bb564ae8cbefbecbf8f1b2de1ba8d755ac75024c5cedd9652e77dfff');
}

function bling_get_redirect_uri(): string {
	return admin_url('admin.php?page=bling-oauth-callback');
}

function bling_admin_menu() {
	add_submenu_page(
		null,
		'Bling OAuth Callback',
		'Bling OAuth Callback',
		'read',
		'bling-oauth-callback',
		'__return_null'
	);
}
add_action('admin_menu', 'bling_admin_menu');

add_action('admin_init', function () {
	if (!is_admin()) { return; }
	$page = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';
	if ($page !== 'bling-oauth-callback') { return; }
	bling_handle_oauth_callback();
	exit;
});

function bling_handle_oauth_callback() {
	$code = isset($_GET['code']) ? sanitize_text_field(wp_unslash($_GET['code'])) : '';
	if (!$code) {
		echo '<div class="notice notice-error"><p>Código de autorização ausente.</p></div>';
		return;
	}

	$body = [
		'grant_type'   => 'authorization_code',
		'code'         => $code,
		'redirect_uri' => bling_get_redirect_uri(),
	];

	$basic = base64_encode(BLING_CLIENT_ID . ':' . BLING_CLIENT_SECRET);
	$response = wp_remote_post('https://www.bling.com.br/Api/v3/oauth/token', [
		'timeout' => 20,
		'headers' => [
			'Content-Type'  => 'application/x-www-form-urlencoded',
			'Authorization' => 'Basic ' . $basic,
		],
		'body'    => http_build_query($body),
	]);

	if (is_wp_error($response)) {
		echo '<div class="notice notice-error"><p>Erro na requisição de token: ' . esc_html($response->get_error_message()) . '</p></div>';
		return;
	}

	$code_http = (int) wp_remote_retrieve_response_code($response);
	$raw       = wp_remote_retrieve_body($response);
	$data      = json_decode($raw, true);

	if ($code_http >= 200 && $code_http < 300 && is_array($data) && isset($data['access_token'])) {
		$payload = [
			'access_token'  => $data['access_token'],
			'refresh_token' => $data['refresh_token'] ?? '',
			'expires_in'    => (int) ($data['expires_in'] ?? 0),
			'obtained_at'   => time(),
		];
		update_option('bling_oauth', $payload, false);

		echo '<h2>Conexão com o Bling realizada com sucesso.</h2>';
		echo '<p>Token armazenado. Você já pode fechar esta página.</p>';
	} else {
		echo '<div class="notice notice-error"><p>Falha ao obter token. Código HTTP: ' . esc_html((string)$code_http) . '</p><pre style=\"white-space:pre-wrap;\">' . esc_html($raw) . '</pre></div>';
	}
}
