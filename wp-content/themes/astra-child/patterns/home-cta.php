<?php
/**
 * Title: Home - CTA Newsletter/Promo
 * Slug: pdg-home/cta
 * Categories: pdg-home
 */

register_block_pattern(
    'pdg-home/cta',
    [
        'title'       => __('Home - CTA Promo', 'astra-child-pdg'),
        'categories'  => ['pdg-home'],
        'content'     => '<!-- wp:group {"style":{"border":{"radius":"8px"},"spacing":{"padding":{"top":"24px","right":"24px","bottom":"24px","left":"24px"}}},"backgroundColor":"brand-accent","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-brand-accent-background-color has-background" style="border-radius:8px;padding-top:24px;padding-right:24px;padding-bottom:24px;padding-left:24px"><!-- wp:columns {"verticalAlignment":"center"} -->
<div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:heading -->
<h2 class="wp-block-heading">Receba ofertas para golfistas</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Cadastre-se para novidades, lançamentos e promoções.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:shortcode -->
[contact-form-7]
<!-- /wp:shortcode --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->'
    ]
);


