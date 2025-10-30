<?php if (!defined('ABSPATH')) { exit; } ?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<header class="site-header">
	<div class="container">
		<h1 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" style="color:#fff;"><?php bloginfo('name'); ?></a></h1>
		<nav class="nav">
			<?php wp_nav_menu(['theme_location'=>'primary','fallback_cb'=>false]); ?>
		</nav>
	</div>
</header>
<main class="container">
