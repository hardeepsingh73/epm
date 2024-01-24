<?php

/**
 * The template for displaying the footer
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * 
 */

?>

</div>
<footer>
    <div class="container">
        <?php
        $copyright_content = get_theme_mod('copyright_content', '');

        if ($copyright_content) {
        ?>
            <div class="copyright"><?php echo wp_kses_post($copyright_content); ?> </div>
        <?php }
        ?>
    </div>
</footer>
<?php
$current_user_id = get_current_user_id();
$current_date = date('Y-m-d');
?>
<script>
    ajax_url = "<?php echo admin_url('admin-ajax.php'); ?>";
    var userId = '<?php echo $current_user_id; ?>';
    var c_date = '<?php echo  $current_date; ?>';
</script>
</div>
<?php wp_footer(); ?>
</body>

</html>