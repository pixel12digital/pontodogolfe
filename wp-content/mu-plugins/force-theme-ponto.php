<?php
/**
 * Plugin Name: Force Theme Ponto (Temporary) - DESABILITADO
 * Description: Força o WordPress a usar o tema 'ponto' enquanto o banco aponta para um tema inexistente.
 * Version: 0.1.0
 */

if (!defined('ABSPATH')) { exit; }

// DESABILITADO - Astra Child será usado agora
/*
if (!defined('FORCE_PONTO_DISABLE') || FORCE_PONTO_DISABLE !== true) {
	add_filter('pre_option_template', function () { return 'ponto'; });
	add_filter('pre_option_stylesheet', function () { return 'ponto'; });
}
*/
