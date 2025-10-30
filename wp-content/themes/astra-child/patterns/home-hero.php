<?php
/**
 * Title: Home - Hero Banner
 * Slug: pdg-home/hero
 * Categories: pdg-home
 */

$fallback = get_stylesheet_directory_uri() . '/assets/images/banner-default.svg';

register_block_pattern(
    'pdg-home/hero',
    [
        'title'       => __('Home - Hero Banner', 'astra-child-pdg'),
        'categories'  => ['pdg-home'],
        'content'     => '<!-- wp:cover {"url":"' . esc_url($fallback) . '","dimRatio":20,"minHeight":480,"contentPosition":"center center","isDark":false} -->
<div class="wp-block-cover is-light" style="min-height:480px"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-20 has-background-dim"></span><img class="wp-block-cover__image-background" alt="" src="' . esc_url($fallback) . '" data-object-fit="cover"/>
  <div class="wp-block-cover__inner-container">
    <!-- wp:heading {"textAlign":"center","level":1,"style":{"typography":{"fontSize":"48px"}}} -->
    <h1 class="wp-block-heading has-text-align-center" style="font-size:48px">Coleção de Golfe</h1>
    <!-- /wp:heading -->

    <!-- wp:paragraph {"align":"center","fontSize":"lg"} -->
    <p class="has-text-align-center has-lg-font-size">Roupas e artigos para seu melhor jogo.</p>
    <!-- /wp:paragraph -->

    <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
    <div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"brand-accent","textColor":"dark","style":{"border":{"radius":"6px"}},"className":"is-style-fill"} -->
      <div class="wp-block-button is-style-fill"><a class="wp-block-button__link has-dark-color has-brand-accent-background-color has-text-color has-background wp-element-button" href="/loja" style="border-radius:6px">Ver Loja</a></div>
      <!-- /wp:button --></div>
    <!-- /wp:buttons -->
  </div>
</div>
<!-- /wp:cover -->'
    ]
);


