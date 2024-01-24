<?php

/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Custom Theme
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<h1><?php echo the_title(); ?></h1>

	<div class="post-content">
		<?php
		the_content();

		?>
	</div><!-- .post-content -->

</article><!-- #post-<?php the_ID(); ?> -->