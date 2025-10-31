<?php
/**
 * Plugin Name: Force Install & Activate Astra
 * Description: Força a instalação e ativação do tema Astra. DELETE APÓS USO!
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) exit;

// Verificar se Astra existe
if (wp_get_theme('astra')->exists()) {
    // Astra já existe - apenas ativar se necessário
    add_action('admin_init', 'pdg_maybe_activate_astra', 1);
} else {
    // Astra não existe - instalar e ativar
    add_action('admin_init', 'pdg_force_install_astra', 1);
}

function pdg_maybe_activate_astra() {
    // Só executar uma vez
    if (get_transient('pdg_astra_activation_attempted')) {
        return;
    }
    
    $current_theme = get_option('stylesheet');
    
    // Se já está ativo, não fazer nada
    if ($current_theme === 'astra') {
        set_transient('pdg_astra_activation_attempted', true, DAY_IN_SECONDS);
        return;
    }
    
    // Marcar como tentado
    set_transient('pdg_astra_activation_attempted', true, MINUTE_IN_SECONDS * 5);
    
    // Ativar o Astra
    switch_theme('astra');
    
    // Mostrar notificação
    add_action('admin_notices', function() {
        echo '<div class="notice notice-success is-dismissible">';
        echo '<p><strong>Tema Astra ativado!</strong> O login já está personalizado com as cores da marca.</p>';
        echo '</div>';
    });
}

function pdg_force_install_astra() {
    // Só executar uma vez por sessão
    if (get_transient('pdg_astra_install_attempted')) {
        return;
    }
    
    // Só executar para admins
    if (!current_user_can('install_themes')) {
        return;
    }
    
    // Marcar como tentado para não executar múltiplas vezes
    set_transient('pdg_astra_install_attempted', true, MINUTE_IN_SECONDS * 5);
    
    // Instalar o Astra diretamente
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    
    $astra_url = 'https://downloads.wordpress.org/theme/astra.latest-stable.zip';
    
    // Baixar
    $temp_file = download_url($astra_url);
    
    if (is_wp_error($temp_file)) {
        error_log('Astra download error: ' . $temp_file->get_error_message());
        return;
    }
    
    // Extrair
    WP_Filesystem();
    $unzipfile = unzip_file($temp_file, WP_CONTENT_DIR . '/themes/');
    
    if (is_wp_error($unzipfile)) {
        @unlink($temp_file);
        error_log('Astra extract error: ' . $unzipfile->get_error_message());
        return;
    }
    
    // Limpar
    @unlink($temp_file);
    
    // Ativar o Astra
    switch_theme('astra');
    
    // Sucesso - mostrar notificação
    add_action('admin_notices', function() {
        echo '<div class="notice notice-success is-dismissible">';
        echo '<p><strong>Astra instalado e ativado automaticamente!</strong> O login já está personalizado com as cores da marca.</p>';
        echo '</div>';
    });
}

