<?php
/**
 * Script para remover temas não utilizados
 * Mantém apenas: Astra e Astra Child
 */

// Carregar WordPress
require_once('wp-load.php');

echo "<h1>Remoção de Temas Não Utilizados</h1>";
echo "<pre>";

// Temas que DEVEM ser mantidos
$keep_themes = ['astra', 'astra-child'];

// Carregar funções necessárias
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/misc.php');
require_once(ABSPATH . 'wp-admin/includes/theme.php');

// Listar todos os temas
$themes = wp_get_themes();

echo "Temas instalados:\n";
echo "==================\n";

foreach ($themes as $theme_slug => $theme) {
    $name = $theme->get('Name');
    $status = ($theme_slug === get_option('stylesheet')) ? '(ATIVO)' : '';
    
    echo "- {$name} ({$theme_slug}) {$status}\n";
}

echo "\n";

// Remover temas
$themes_to_remove = ['ponto', 'twenty-twenty-four'];

foreach ($themes_to_remove as $theme_slug) {
    if (!isset($themes[$theme_slug])) {
        echo "Tema '{$theme_slug}' não encontrado. Pulando...\n";
        continue;
    }
    
    echo "Removendo tema: {$theme_slug}...\n";
    
    // Deletar tema
    $deleted = delete_theme($theme_slug);
    
    if (is_wp_error($deleted)) {
        echo "✗ Erro ao remover: " . $deleted->get_error_message() . "\n";
    } else {
        echo "✓ Tema '{$theme_slug}' removido com sucesso!\n";
    }
}

// Limpar cache
wp_clean_themes_cache();
delete_site_transient('update_themes');

echo "\n======================================\n";
echo "✓ LIMPEZA CONCLUÍDA!\n";
echo "======================================\n\n";

echo "Temas mantidos:\n";
$remaining_themes = wp_get_themes();
foreach ($remaining_themes as $theme_slug => $theme) {
    echo "- " . $theme->get('Name') . "\n";
}

echo "</pre>";

echo "<p><strong>Próximos passos:</strong></p>";
echo "<ol>";
echo "<li><a href='" . admin_url('themes.php') . "'>Ver temas restantes</a></li>";
echo "<li>Se quiser, ative 'Astra Child (Ponto do Golfe)' para usar as personalizações</li>";
echo "<li><strong>DELETE este arquivo (remove-unused-themes.php) após usar!</strong></li>";
echo "</ol>";

