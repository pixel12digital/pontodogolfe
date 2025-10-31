<?php
/**
 * Ativar o Astra Child automaticamente
 */

require_once('wp-load.php');

echo "<h1>Ativando Astra Child</h1>";
echo "<pre>";

// Verificar se Astra Child existe
if (!wp_get_theme('astra-child')->exists()) {
    die("❌ Erro: Tema Astra Child não encontrado!\n");
}

echo "✓ Astra Child encontrado\n";

// Ativar o tema
$result = switch_theme('astra-child');

if (is_wp_error($result)) {
    die("❌ Erro ao ativar: " . $result->get_error_message() . "\n");
}

echo "✓ Astra Child ativado com sucesso!\n";

// Limpar cache
wp_clean_themes_cache();
delete_site_transient('update_themes');

echo "\n======================================\n";
echo "✓ CONCLUÍDO!\n";
echo "======================================\n\n";

echo "Tema ativo: " . get_option('stylesheet') . "\n";
echo "Personalização do login já está ativa!\n";

echo "</pre>";
echo "<p><a href='" . admin_url('themes.php') . "'>Ver temas</a> | ";
echo "<a href='" . wp_login_url() . "' target='_blank'>Testar login personalizado</a></p>";

