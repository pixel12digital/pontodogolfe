<?php
// Tema Ponto do Golfe - funções básicas

if (!defined('ABSPATH')) { exit; }

add_action('after_setup_theme', function () {
	add_theme_support('title-tag');
	add_theme_support('post-thumbnails');
	add_theme_support('html5', ['search-form','comment-form','comment-list','gallery','caption']);
	register_nav_menus([
		'primary' => __('Menu Principal', 'ponto'),
	]);
});

add_action('wp_enqueue_scripts', function () {
	$theme_version = wp_get_theme()->get('Version');
	wp_enqueue_style('ponto-style', get_stylesheet_uri(), [], $theme_version);
});
