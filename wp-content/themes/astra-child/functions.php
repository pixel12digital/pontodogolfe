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


