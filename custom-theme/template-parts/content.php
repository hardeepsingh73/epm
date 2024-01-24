<?php

/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Custom Theme
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <?php
    if (is_singular()) :
    ?>
        <h1><?php echo the_title(); ?></h1>
    <?php
    else :
    ?>
        <h2><a href="<?php echo esc_url(get_permalink()); ?>"><?php echo the_title(); ?></a></h2>
    <?php
    endif;
    ?>
    <div class="post-content">
        <?php
        the_content();
        ?>
    </div><!-- .post-content -->

</article><!-- #post-<?php the_ID(); ?> -->