<?php
/**
 * Script para corrigir a configuração de temas e instalar o Astra
 * Execute este arquivo uma vez pelo navegador
 * 
 * Acesso: http://seu-site.com/fix-themes-config.php
 */

// Carregar WordPress
require_once(__DIR__ . '/wp-load.php');

// Verificar permissões
if (!current_user_can('install_themes') || !current_user_can('switch_themes')) {
    die('Você não tem permissões para executar este script.');
}

echo "<h1>Correção de Configuração de Temas</h1>";
echo "<pre>";

// Verificar qual tema está no banco de dados
$current_stylesheet = get_option('stylesheet');
$current_template = get_option('template');

echo "Tema atual no banco (stylesheet): {$current_stylesheet}\n";
echo "Tema atual no banco (template): {$current_template}\n\n";

// Verificar se o Astra existe
$astra_exists = wp_get_theme('astra')->exists();

if (!$astra_exists) {
    echo "Instalando o tema Astra...\n";
    
    // Carregar funções necessárias
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/misc.php');
    require_once(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');
    
    // URL do Astra
    $astra_url = 'https://downloads.wordpress.org/theme/astra.latest-stable.zip';
    
    // Criar skin silencioso
    class WP_Silent_Upgrader_Skin extends \WP_Upgrader_Skin {
        public function feedback($string, ...$args) {}
    }
    
    // Inicializar filesystem
    $creds = request_filesystem_credentials('', '', false, false, null);
    if (!WP_Filesystem($creds)) {
        die('Erro: Não foi possível acessar o sistema de arquivos.');
    }
    
    // Instalar
    $upgrader = new Theme_Upgrader(new WP_Silent_Upgrader_Skin());
    $result = $upgrader->install($astra_url);
    
    if (is_wp_error($result)) {
        die('Erro ao instalar: ' . $result->get_error_message());
    }
    
    echo "✓ Astra instalado com sucesso!\n\n";
} else {
    echo "✓ Astra já está instalado.\n\n";
}

// Tentar corrigir o tema ativo
echo "Corrigindo tema ativo...\n";

// Se o tema atual não existe, mudar para Astra
if (!wp_get_theme($current_stylesheet)->exists()) {
    echo "Tema atual '{$current_stylesheet}' não existe. Mudando para Astra...\n";
    $result = switch_theme('astra');
    
    if (is_wp_error($result)) {
        die('Erro ao ativar Astra: ' . $result->get_error_message());
    }
    
    echo "✓ Astra ativado com sucesso!\n";
} else {
    echo "Tema atual existe: {$current_stylesheet}\n";
    
    // Mas vamos ativar o Astra de qualquer forma
    echo "Ativando Astra...\n";
    $result = switch_theme('astra');
    
    if (is_wp_error($result)) {
        die('Erro ao ativar Astra: ' . $result->get_error_message());
    }
    
    echo "✓ Astra ativado!\n";
}

// Limpar cache de temas
wp_clean_themes_cache();
delete_site_transient('update_themes');

echo "\n";
echo "======================================\n";
echo "✓ CONFIGURAÇÃO CONCLUÍDA!\n";
echo "======================================\n\n";

echo "Tema ativo agora: " . get_option('stylesheet') . "\n";
echo "Personalização do login já está configurada!\n\n";

echo "</pre>";
echo "<p><strong>Próximos passos:</strong></p>";
echo "<ol>";
echo "<li>Teste a página de login: <a href='" . wp_login_url() . "' target='_blank'>" . wp_login_url() . "</a></li>";
echo "<li>Se quiser usar o child theme, ative 'Astra Child (Ponto do Golfe)' em: <a href='" . admin_url('themes.php') . "'>Aparência > Temas</a></li>";
echo "<li>Delete este arquivo (fix-themes-config.php) após usar</li>";
echo "</ol>";

// Limpar transientes antigos que possam estar bloqueando
delete_transient('pdg_astra_install_attempted');
delete_transient('pdg_astra_activation_attempted');

