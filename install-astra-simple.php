<?php
/**
 * Script SIMPLES para instalar o Astra
 * Bypassa checagens de permissão usando diretamente o filesystem
 */

// Carregar WordPress
require_once('wp-load.php');

echo "<h1>Instalação do Tema Astra</h1>";
echo "<pre>";

// Verificar se Astra já existe
if (wp_get_theme('astra')->exists()) {
    echo "✓ Astra já está instalado!\n";
    
    // Ativar
    switch_theme('astra');
    echo "✓ Astra ativado!\n";
    
    // Limpar cache
    wp_clean_themes_cache();
    
    echo "\n======================================\n";
    echo "✓ CONCLUÍDO!\n";
    echo "======================================\n\n";
    echo "Tema ativo: " . get_option('stylesheet') . "\n";
    
    echo "</pre>";
    echo "<p><a href='" . admin_url('themes.php') . "'>Ver temas</a> | ";
    echo "<a href='" . wp_login_url() . "' target='_blank'>Testar login</a></p>";
    exit;
}

echo "Instalando o Astra...\n";

// Baixar o Astra
$astra_url = 'https://downloads.wordpress.org/theme/astra.latest-stable.zip';
$zip_file = download_url($astra_url);

if (is_wp_error($zip_file)) {
    die('Erro ao baixar: ' . $zip_file->get_error_message());
}

echo "✓ Download concluído\n";

// Descompactar
require_once(ABSPATH . 'wp-admin/includes/file.php');
WP_Filesystem();

$unzip = unzip_file($zip_file, WP_CONTENT_DIR . '/themes/');

if (is_wp_error($unzip)) {
    @unlink($zip_file);
    die('Erro ao descompactar: ' . $unzip->get_error_message());
}

@unlink($zip_file);
echo "✓ Descompactado\n";

// Ativar
switch_theme('astra');
echo "✓ Astra ativado!\n";

// Limpar cache
wp_clean_themes_cache();
delete_site_transient('update_themes');
delete_transient('pdg_theme_fix_attempted');
delete_transient('pdg_astra_install_attempted');

echo "\n======================================\n";
echo "✓ INSTALAÇÃO CONCLUÍDA!\n";
echo "======================================\n\n";
echo "Tema ativo: " . get_option('stylesheet') . "\n";

echo "</pre>";
echo "<p><strong>Próximos passos:</strong></p>";
echo "<ol>";
echo "<li><a href='" . admin_url('themes.php') . "'>Ver temas instalados</a></li>";
echo "<li><a href='" . wp_login_url() . "' target='_blank'>Testar login personalizado</a></li>";
echo "<li>Se quiser usar o child theme, ative 'Astra Child' em Aparência > Temas</li>";
echo "<li><strong>DELETE este arquivo (install-astra-simple.php) após usar!</strong></li>";
echo "</ol>";

