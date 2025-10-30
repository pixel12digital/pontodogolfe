<?php
/**
 * Plugin Name: Install Astra Admin (Temporary)
 * Description: Página admin temporária para instalar o Astra. DELETE APÓS USO!
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) exit;

// Adicionar página admin
add_action('admin_menu', function() {
    add_theme_page(
        'Instalar Astra',
        'Instalar Astra',
        'install_themes',
        'install-astra-temp',
        'pdg_install_astra_page'
    );
});

function pdg_install_astra_page() {
    // Verificar se já está instalado
    if (wp_get_theme('astra')->exists()) {
        echo '<div class="wrap">';
        echo '<h1>Astra já está instalado!</h1>';
        echo '<p><a href="' . admin_url('themes.php') . '" class="button">Ver Temas</a></p>';
        echo '</div>';
        return;
    }

    // Processar instalação se solicitado
    if (isset($_POST['install_astra']) && check_admin_referer('install_astra_action')) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        
        $astra_url = 'https://downloads.wordpress.org/theme/astra.latest-stable.zip';
        
        echo '<div class="wrap">';
        echo '<h1>Instalando Astra...</h1>';
        echo '<p>Baixando tema...</p>';
        flush();
        
        $temp_file = download_url($astra_url);
        
        if (is_wp_error($temp_file)) {
            echo '<div class="notice notice-error"><p>Erro ao baixar: ' . $temp_file->get_error_message() . '</p></div>';
            echo '</div>';
            return;
        }
        
        echo '<p>Extraindo tema...</p>';
        flush();
        
        WP_Filesystem();
        $unzipfile = unzip_file($temp_file, WP_CONTENT_DIR . '/themes/');
        
        if (is_wp_error($unzipfile)) {
            @unlink($temp_file);
            echo '<div class="notice notice-error"><p>Erro ao extrair: ' . $unzipfile->get_error_message() . '</p></div>';
            echo '</div>';
            return;
        }
        
        @unlink($temp_file);
        
        echo '<div class="notice notice-success"><p><strong>✓ Astra instalado com sucesso!</strong></p></div>';
        echo '<p><a href="' . admin_url('themes.php') . '" class="button button-primary">Ver Temas</a></p>';
        echo '<p><a href="' . admin_url('themes.php?action=activate&stylesheet=astra&_wpnonce=' . wp_create_nonce('switch-theme_astra')) . '" class="button button-primary">ATIVAR ASTRA AGORA</a></p>';
        echo '</div>';
        return;
    }

    // Mostrar formulário
    ?>
    <div class="wrap">
        <h1>Instalar Tema Astra</h1>
        <div class="card">
            <h2>Instalação do Tema Astra</h2>
            <p>Este assistente instalará o tema Astra no seu WordPress.</p>
            
            <form method="post">
                <?php wp_nonce_field('install_astra_action'); ?>
                <p>
                    <input type="submit" name="install_astra" class="button button-primary button-large" value="Instalar Astra" />
                </p>
            </form>
            
            <div class="notice notice-warning" style="margin-top: 20px;">
                <p><strong>⚠ IMPORTANTE:</strong> Após instalar, delete este arquivo: <code>wp-content/mu-plugins/install-astra-admin.php</code></p>
            </div>
        </div>
    </div>
    <?php
}

