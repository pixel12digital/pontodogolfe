<?php
/**
 * Plugin Name: Auto Install Astra (Temporary)
 * Description: Instala o Astra automaticamente se não estiver instalado. DELETE APÓS USO!
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) exit;

// Executar apenas uma vez e apenas para admins
add_action('admin_init', 'pdg_auto_install_astra_once', 1);

function pdg_auto_install_astra_once() {
    // Só executar uma vez por sessão
    if (get_transient('pdg_astra_install_attempted')) {
        return;
    }
    
    // Só executar para admins
    if (!current_user_can('install_themes')) {
        return;
    }
    
    // Verificar se Astra já existe
    if (wp_get_theme('astra')->exists()) {
        // Já existe, marcar como tentado e não tentar novamente
        set_transient('pdg_astra_install_attempted', true, HOUR_IN_SECONDS);
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
    
    // Sucesso - mostrar notificação
    add_action('admin_notices', function() {
        echo '<div class="notice notice-success is-dismissible">';
        echo '<p><strong>Astra instalado automaticamente!</strong> Vá em <a href="' . admin_url('themes.php') . '">Aparência > Temas</a> para ativar.</p>';
        echo '</div>';
    });
}

