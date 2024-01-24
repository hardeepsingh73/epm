<?php

/**
 * The header for our theme <------> This is the template that displays all of the <head> section and everything up until main div
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * 
 */

// redirect to login form 

if (!is_user_logged_in()) {
    auth_redirect();
}

?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset') ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <title><?php echo get_bloginfo('name') ?> | <?php echo get_bloginfo('description') ?></title> -->

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>

    <div class="container-fluid">
        <header>
            <div class="container">
                <div class="logo">
                    <?php $custom_logo_id = get_theme_mod('custom_logo');
                    $custom_logo_url = wp_get_attachment_image_url($custom_logo_id, 'full');
                    ?>
                    <a href="<?php echo home_url(); ?>">
                        <img src="<?php echo esc_url($custom_logo_url); ?>" alt="Site Logo">
                    </a>
                </div>
            </div>
        </header>
        <div class="container">