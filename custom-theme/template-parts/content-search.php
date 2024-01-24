<?php

/**
 * Template part for displaying results in search pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Custom Theme
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<h2 class="post-title"><a href="<?php echo esc_url(get_permalink()); ?>"><?php echo the_title(); ?></a></h2>

	<?php if ('post' === get_post_type()) : ?>

	<?php endif; ?>


	<div class="post-summary">
		<?php the_excerpt(); ?>
	</div><!-- .post-summary -->
</article><!-- #post-<?php the_ID(); ?> -->