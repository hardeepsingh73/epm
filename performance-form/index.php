<?php
// Plugin Name:  Performance Form
// Description:  Performance Form For User.
// Version: 1.0
// Author: Hardeep Singh
if (!defined('WPINC')) {
    header("Location: /");
    die();
}
if (!defined('ABSPATH')) {
    header('Location: /');
    die;
}
//Get Plugin Folder Path
if (!defined('PERFORMANCE_PLUGIN_PATH')) {
    define('PERFORMANCE_PLUGIN_PATH', plugin_dir_path(__FILE__));
}
// activate function 
function performance_plugin_activation()
{
    global $wpdb;
    $performance_table_name = $wpdb->prefix . 'dev_performance_data';
    $column = '(id int(11) NOT NULL auto_increment,user_id int(11) NOT NULL,project_id int(11) NOT NULL,  date DATE NOT NULL,online_offline varchar(500) NOT NULL,number_of_hours decimal(10,2) NOT NULL,billing_status varchar(500) NOT NULL,profile_name int(11),notes varchar(500),reviewed_by varchar(500) NOT NULL,is_approve BOOLEAN DEFAULT false, is_submit BOOLEAN DEFAULT false, created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
    if ($wpdb->get_var("SHOW TABLES LIKE '$performance_table_name'") != $performance_table_name) {
        $sql = "CREATE TABLE $performance_table_name $column";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    $performance_table_name = $wpdb->prefix . 'sales_performance_data';
    if ($wpdb->get_var("SHOW TABLES LIKE '$performance_table_name'") != $performance_table_name) {
        $sql = "CREATE TABLE $performance_table_name $column";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    $performance_table_name = $wpdb->prefix . 'marketing_performance_data';
    if ($wpdb->get_var("SHOW TABLES LIKE '$performance_table_name'") != $performance_table_name) {
        $sql = "CREATE TABLE $performance_table_name $column";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}
//Register Activation Hook 
register_activation_hook(__FILE__, 'performance_plugin_activation');
//deactivate function
function performance_plugin_deactivation()
{
    /*** Drop Database Table ***/
    global $wpdb, $table_prefix;
    $table_name = $table_prefix . 'dev_performance_data';
    $truncate_table_query = "drop TABLE $table_name;";
    $wpdb->query($truncate_table_query);
    $table_name = $table_prefix . 'sales_performance_data';
    $truncate_table_query = "drop TABLE $table_name;";
    $wpdb->query($truncate_table_query);
    $table_name = $table_prefix . 'marketing_performance_data';
    $truncate_table_query = "drop TABLE $table_name;";
    $wpdb->query($truncate_table_query);
}
//Register Deactivation Hook 
// register_deactivation_hook(__FILE__, 'performance_plugin_deactivation');
// add menu file 
require_once PERFORMANCE_PLUGIN_PATH . 'performance_menu_fun.php';
require_once PERFORMANCE_PLUGIN_PATH . 'performance_ajax_fun.php';
require_once PERFORMANCE_PLUGIN_PATH . 'user_performance.php';
require_once PERFORMANCE_PLUGIN_PATH . 'profile_performance.php';
//Create Menus
function performance_plugin_menu_function()
{
    add_menu_page('Performance', 'Performance', 'manage_options', 'performance_list', 'display_performance_list', 'dashicons-media-spreadsheet');
    add_submenu_page('performance_list', 'Profile Performance', 'Profile Performance', 'manage_options', 'profile_performance_list', 'profile_display_performance_list');
    add_submenu_page('performance_list', 'All Performance', 'All Performance', 'manage_options', 'all_performance', 'display_all_performance');
    // add_submenu_page('performance_list', 'Add Performance', 'Add Performance', 'manage_options', 'add_performance', 'display_performance_form');
}
add_action('admin_menu', 'performance_plugin_menu_function');
