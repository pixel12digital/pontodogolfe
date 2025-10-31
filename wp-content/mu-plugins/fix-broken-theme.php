<?php
/**
 * Plugin Name: Fix Broken Theme
 * Description: Corrige automaticamente temas quebrados e instala o Astra
 * Version: 1.0.1
 */

if (!defined('ABSPATH')) exit;

// Executar quando o admin carregar OU quando não estiver logado mas Astra falta
add_action('init', 'pdg_fix_broken_theme_once', 1);

function pdg_fix_broken_theme_once() {
    // Só executar se Astra não existe OU tema atual está quebrado
    $astra_exists = wp_get_theme('astra')->exists();
    $current_stylesheet = get_option('stylesheet');
    $current_theme_exists = wp_get_theme($current_stylesheet)->exists();
    
    // Se Astra existe e tema atual é válido, não fazer nada
    if ($astra_exists && $current_theme_exists && $current_stylesheet !== 'royal-elementor-kit') {
        return;
    }
    
    // Se Astra não existe, instalar SEMPRE
    if (!$astra_exists) {
        // Instalar Astra
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/misc.php');
        
        $astra_url = 'https://downloads.wordpress.org/theme/astra.latest-stable.zip';
        $temp_file = download_url($astra_url);
        
        if (!is_wp_error($temp_file)) {
            WP_Filesystem();
            $unzipfile = unzip_file($temp_file, WP_CONTENT_DIR . '/themes/');
            @unlink($temp_file);
        }
    }
    
    // Ativar Astra se existir agora
    if (wp_get_theme('astra')->exists()) {
        switch_theme('astra');
        wp_clean_themes_cache();
        delete_site_transient('update_themes');
        
        // Mostrar notificação se estiver no admin
        if (is_admin()) {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-success is-dismissible">';
                echo '<p><strong>✓ Astra instalado e ativado!</strong> O login já está personalizado com as cores da marca.</p>';
                echo '</div>';
            });
        }
    }
}

