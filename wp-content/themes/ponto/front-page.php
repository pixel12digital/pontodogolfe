<?php if (!defined('ABSPATH')) { exit; } ?>
<?php if ( ! is_user_logged_in() ) : ?>
<?php get_header(); ?>
<section style="text-align:center; padding:80px 0;">
	<h2 style="font-size:32px; margin:0 0 12px;">Estamos preparando algo especial</h2>
	<p style="margin:0 0 28px; font-size:18px; color:#555;">Site em construção. Em breve, novidades do Ponto do Golfe Outlet.</p>
	<a class="button" href="mailto:contato@pontodogolfeoutlet.com.br">Fale conosco</a>
</section>
<?php get_footer(); ?>
<?php else: ?>
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
<?php endif; ?>
