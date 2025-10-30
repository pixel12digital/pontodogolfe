<?php
/**
 * Script para instalar Astra forçadamente
 * Acesse: http://seudominio.com.br/force-install-astra.php?key=INSTALL_ASTRA_2025
 * DELETE APÓS USO!
 */

// Segurança básica (mude a chave se necessário)
$required_key = 'INSTALL_ASTRA_2025';
if (!isset($_GET['key']) || $_GET['key'] !== $required_key) {
    die('Acesso negado. Use: ?key=INSTALL_ASTRA_2025');
}

// Carregar WordPress
define('WP_USE_THEMES', false);
require_once('wp-load.php');

// Verificar se é admin
if (!is_user_logged_in() || !current_user_can('install_themes')) {
    die('Você precisa estar logado como administrador.');
}

// Limpar transient que pode estar bloqueando
delete_transient('pdg_astra_install_attempted');

// Verificar se já existe
if (wp_get_theme('astra')->exists()) {
    echo '<h1 style="color: green;">✓ Astra já está instalado!</h1>';
    echo '<p><a href="' . admin_url('themes.php') . '">Ir para Temas</a></p>';
    exit;
}

echo '<h1>Instalando Astra...</h1>';
echo '<style>body { font-family: Arial; padding: 20px; } .success { color: green; } .error { color: red; }</style>';

// Funções necessárias
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/misc.php');

$astra_url = 'https://downloads.wordpress.org/theme/astra.latest-stable.zip';

echo '<p>Passo 1: Baixando Astra...</p>';
flush();

// Baixar
$temp_file = download_url($astra_url, 300);

if (is_wp_error($temp_file)) {
    echo '<p class="error">Erro ao baixar: ' . esc_html($temp_file->get_error_message()) . '</p>';
    exit;
}

echo '<p class="success">✓ Download concluído!</p>';
echo '<p>Passo 2: Extraindo tema...</p>';
flush();

// Extrair
$unzip_result = unzip_file($temp_file, WP_CONTENT_DIR . '/themes/');

if (is_wp_error($unzip_result)) {
    @unlink($temp_file);
    echo '<p class="error">Erro ao extrair: ' . esc_html($unzip_result->get_error_message()) . '</p>';
    
    // Mostrar detalhes do erro
    if (function_exists('wp_filesystem')) {
        global $wp_filesystem;
        if (empty($wp_filesystem)) {
            WP_Filesystem();
        }
    }
    
    exit;
}

// Limpar arquivo temporário
@unlink($temp_file);

// Verificar se foi instalado
if (wp_get_theme('astra')->exists()) {
    echo '<div style="background: #d4edda; border: 1px solid #c3e6cb; padding: 20px; margin: 20px 0; border-radius: 5px;">';
    echo '<h2 class="success">✓ ASTRA INSTALADO COM SUCESSO!</h2>';
    echo '<p><strong>Agora você pode:</strong></p>';
    echo '<ul>';
    echo '<li><a href="' . admin_url('themes.php') . '" style="font-size: 18px; font-weight: bold;">Ver Temas</a></li>';
    echo '<li><a href="' . admin_url('themes.php?action=activate&stylesheet=astra&_wpnonce=' . wp_create_nonce('switch-theme_astra')) . '" style="font-size: 18px; font-weight: bold; color: green;">ATIVAR ASTRA AGORA</a></li>';
    echo '</ul>';
    echo '</div>';
    
    echo '<div style="background: #fff3cd; border: 1px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 5px;">';
    echo '<p><strong>⚠ IMPORTANTE:</strong> Delete este arquivo <code>force-install-astra.php</code> após o uso por segurança!</p>';
    echo '</div>';
} else {
    echo '<p class="error">Instalação concluída mas o tema não foi encontrado. Verifique as permissões da pasta wp-content/themes/</p>';
}

