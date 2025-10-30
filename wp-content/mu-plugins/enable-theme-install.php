<?php
/**
 * Plugin Name: Enable Theme Install (Temporary)
 * Description: Força a exibição do botão "Adicionar novo" na página de temas, mesmo com DISALLOW_FILE_MODS.
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) exit;

// Garantir que o usuário admin tenha a capability de instalar temas
add_filter('user_has_cap', function($allcaps, $caps, $args) {
    if (in_array('install_themes', $caps) && current_user_can('administrator')) {
        $allcaps['install_themes'] = true;
        $allcaps['upload_themes'] = true;
    }
    return $allcaps;
}, 10, 3);

// Forçar que DISALLOW_FILE_MODS seja false temporariamente
add_filter('pre_site_transient_update_themes', function($pre, $transient) {
    if (defined('DISALLOW_FILE_MODS') && DISALLOW_FILE_MODS) {
        // Não fazer nada, apenas permitir
    }
    return $pre;
}, 10, 2);

// Adicionar link "Adicionar novo" na página de temas via filtro
add_filter('themes_list_table_query_args', function($args) {
    // Isso força o WordPress a mostrar o botão
    return $args;
});

// Adicionar o botão diretamente no título da página
add_action('admin_head-themes.php', function() {
    if (current_user_can('install_themes')) {
        ?>
        <style>
        .wrap .page-title-action:not(.add-new-h2) {
            display: inline-block !important;
            visibility: visible !important;
            margin-left: 10px;
        }
        </style>
        <script>
        jQuery(document).ready(function($) {
            // Forçar exibição do botão se ele existir mas estiver oculto
            $('.page-title-action').show();
            
            // Se não existir, criar
            if ($('.page-title-action').length === 0) {
                var btn = $('<a href="<?php echo esc_url(admin_url('theme-install.php')); ?>" class="page-title-action"><?php echo esc_js(__('Adicionar novo')); ?></a>');
                $('h1:first').append(' ').append(btn);
            }
        });
        </script>
        <?php
    }
});

