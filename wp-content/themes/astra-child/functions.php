<?php

// Enqueue estilos do tema pai e do filho
add_action('wp_enqueue_scripts', function () {
    $parent_style = 'astra-theme-css';
    wp_enqueue_style($parent_style, get_template_directory_uri() . '/style.css', [], wp_get_theme('astra')->get('Version'));
    wp_enqueue_style('astra-child-style', get_stylesheet_directory_uri() . '/style.css', [$parent_style], wp_get_theme()->get('Version'));
});

// Suporte ao WooCommerce e imagens de destaque do hero
add_action('after_setup_theme', function () {
    add_theme_support('woocommerce');
    add_image_size('pdg-hero', 1920, 700, true);
});

// Registrar categoria e arquivos de padrões de blocos (patterns)
add_action('init', function () {
    if (! function_exists('register_block_pattern_category')) {
        return; // WP 5.5+
    }

    register_block_pattern_category(
        'pdg-home',
        ['label' => __('Ponto do Golfe - Home', 'astra-child-pdg')]
    );
});

// Carregar padrões do diretório /patterns automaticamente
add_action('init', function () {
    $patterns_dir = get_stylesheet_directory() . '/patterns';
    if (! is_dir($patterns_dir)) {
        return;
    }
    foreach (glob($patterns_dir . '/*.php') as $pattern_file) {
        require_once $pattern_file;
    }
});

// =====================================================
// CORES DA MARCA - PONTO DO GOLFE
// =====================================================
define('PDG_COLOR_PRIMARY', '#1F5D3F');   // Verde escuro (principal)
define('PDG_COLOR_SECONDARY', '#719B57'); // Verde claro (secundário)
define('PDG_COLOR_WHITE', '#FFFFFF');     // Branco

/**
 * Personalização da página de login do WordPress
 */
add_action('login_head', function() {
    $logo_url = 'https://pontodogolfeoutlet.com.br/wp-content/uploads/2025/10/Captura-de-tela-2025-10-31-085004.png';
    ?>
    <style type="text/css">
        /* Estilos personalizados da página de login */
        #login {
            padding: 40px 0 20px;
        }
        
        /* Logo personalizado */
        .wp-login-logo {
            margin-bottom: 30px;
        }
        
        .wp-login-logo a {
            background-image: url('<?php echo esc_url($logo_url); ?>') !important;
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center center;
            width: 150px;
            height: 150px;
            display: block;
            margin: 0 auto;
            text-indent: -9999px;
            overflow: hidden;
            border-radius: 50%;
            border: 4px solid <?php echo PDG_COLOR_PRIMARY; ?>;
            box-shadow: 0 4px 10px rgba(31, 93, 63, 0.2);
        }
        
        /* Corpo da página */
        body.login {
            background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%);
        }
        
        /* Formulário de login */
        #loginform,
        #lostpasswordform,
        #registerform {
            background: #fff;
            border: 1px solid #e0e0e0;
            box-shadow: 0 2px 10px rgba(31, 93, 63, 0.1);
        }
        
        /* Botões */
        .button-primary,
        .button-primary:hover,
        .button-primary:focus,
        .button-primary:active,
        .button-primary:visited {
            background: <?php echo PDG_COLOR_PRIMARY; ?> !important;
            border-color: <?php echo PDG_COLOR_PRIMARY; ?> !important;
            box-shadow: 0 1px 0 <?php echo PDG_COLOR_PRIMARY; ?> !important;
            color: <?php echo PDG_COLOR_WHITE; ?> !important;
        }
        
        .button-primary:hover,
        .button-primary:focus,
        .button-primary:active {
            background: <?php echo PDG_COLOR_SECONDARY; ?> !important;
            border-color: <?php echo PDG_COLOR_SECONDARY; ?> !important;
            box-shadow: 0 1px 0 <?php echo PDG_COLOR_SECONDARY; ?> !important;
        }
        
        /* Links */
        .login #nav a,
        .login #backtoblog a {
            color: <?php echo PDG_COLOR_PRIMARY; ?> !important;
        }
        
        .login #nav a:hover,
        .login #backtoblog a:hover {
            color: <?php echo PDG_COLOR_SECONDARY; ?> !important;
        }
        
        /* Inputs */
        .input,
        input[type="text"],
        input[type="password"] {
            border-color: #ddd;
        }
        
        .input:focus,
        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: <?php echo PDG_COLOR_PRIMARY; ?> !important;
            box-shadow: 0 0 0 1px <?php echo PDG_COLOR_PRIMARY; ?> !important;
        }
        
        /* Checkbox */
        input[type="checkbox"]:checked:before {
            color: <?php echo PDG_COLOR_PRIMARY; ?> !important;
        }
        
        /* Mensagens de erro e sucesso */
        .login .message,
        .login .success {
            border-left-color: <?php echo PDG_COLOR_PRIMARY; ?> !important;
        }
        
        .login .notice-error {
            border-left-color: #d63638 !important;
        }
        
        /* Efeito de transição suave */
        * {
            transition: all 0.3s ease;
        }
    </style>
    <?php
});

/**
 * Mudar o link do logo para o site
 */
add_filter('login_headerurl', function($url) {
    return home_url();
});

/**
 * Mudar o texto do logo
 */
add_filter('login_headertext', function($text) {
    return 'Ponto do Golfe';
});


