<?php
/**
 * The main template file
 */

get_header(); ?>

<div class="site">
    <main class="main">
        <?php
        if ( have_posts() ) :
            while ( have_posts() ) : the_post();
                ?>
                <article>
                    <h2><?php the_title(); ?></h2>
                    <?php the_content(); ?>
                </article>
                <?php
            endwhile;
        else :
            echo '<p>No posts found.</p>';
        endif;
        ?>
    </main>
</div>

<?php get_footer(); ?>

