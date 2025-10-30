<?php
/**
 * Title: Home - Destaque de Categorias
 * Slug: pdg-home/categories
 * Categories: pdg-home
 */

register_block_pattern(
    'pdg-home/categories',
    [
        'title'       => __('Home - Destaque de Categorias', 'astra-child-pdg'),
        'categories'  => ['pdg-home'],
        'content'     => '<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:heading {"textAlign":"center"} -->
<h2 class="wp-block-heading has-text-align-center">Categorias em Destaque</h2>
<!-- /wp:heading -->

<!-- wp:woocommerce/product-category-list /-->

</div>
<!-- /wp:group -->'
    ]
);


