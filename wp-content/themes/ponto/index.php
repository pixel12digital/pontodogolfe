<?php get_header(); ?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	<article <?php post_class('entry'); ?>>
		<h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<div class="entry-content">
			<?php the_excerpt(); ?>
			<p><a class="button" href="<?php the_permalink(); ?>">Ler mais</a></p>
		</div>
	</article>
<?php endwhile; else: ?>
	<p>Nenhum conteúdo publicado ainda.</p>
<?php endif; ?>

<?php get_footer(); ?>
