<?php
/**
 * Plugin Name: Fix Broken Theme
 * Description: Corrige automaticamente temas quebrados e instala o Astra
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) exit;

add_action('admin_init', 'pdg_fix_broken_theme_once', 1);

function pdg_fix_broken_theme_once() {
    // Só executar uma vez
    if (get_transient('pdg_theme_fix_attempted')) {
        return;
    }
    
    // Marcar como tentado
    set_transient('pdg_theme_fix_attempted', true, HOUR_IN_SECONDS);
    
    // Verificar tema atual
    $current_stylesheet = get_option('stylesheet');
    $theme_exists = wp_get_theme($current_stylesheet)->exists();
    
    // Se o tema não existe OU se é "royal-elementor-kit", corrigir
    if (!$theme_exists || $current_stylesheet === 'royal-elementor-kit') {
        // Se Astra existe, ativar
        if (wp_get_theme('astra')->exists()) {
            switch_theme('astra');
            
            add_action('admin_notices', function() {
                echo '<div class="notice notice-success is-dismissible">';
                echo '<p><strong>Tema corrigido!</strong> Astra ativado com sucesso. O login já está personalizado.</p>';
                echo '</div>';
            });
            return;
        }
        
        // Se Astra não existe, instalar e ativar
        if (!current_user_can('install_themes')) {
            return;
        }
        
        // Instalar Astra
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        $astra_url = 'https://downloads.wordpress.org/theme/astra.latest-stable.zip';
        $temp_file = download_url($astra_url);
        
        if (!is_wp_error($temp_file)) {
            WP_Filesystem();
            $unzipfile = unzip_file($temp_file, WP_CONTENT_DIR . '/themes/');
            @unlink($temp_file);
            
            if (!is_wp_error($unzipfile)) {
                // Ativar Astra
                switch_theme('astra');
                
                add_action('admin_notices', function() {
                    echo '<div class="notice notice-success is-dismissible">';
                    echo '<p><strong>Astra instalado e ativado!</strong> O login já está personalizado com as cores da marca.</p>';
                    echo '</div>';
                });
            }
        }
    }
}

