<?php

/**
 * Template part for displaying a message that posts cannot be found
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Custom Theme
 */

?>

<section class="no-results not-found">
    <h1><?php echo the_title(); ?></h1>
    <div class="page-content">
        <?php
        if (is_home() && current_user_can('publish_posts')) :

        ?>
            <p> Ready to publish your first post? <a href="<?php echo admin_url('post-new.php'); ?>">Get started here</a>
            <?php

        elseif (is_search()) :
            ?>

            <p>Sorry, but nothing matched your search terms. Please try again with some different keywords</p>
        <?php
            get_search_form();

        else :
        ?>

            <p>It seems we can't find what you're looking for. Perhaps searching can help</p>
        <?php
            get_search_form();

        endif;
        ?>
    </div><!-- .page-content -->
</section><!-- .no-results -->