<?php
// Plugin Name:  Project Form
// Description: for project name and type.
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
if (!defined('PROJECT_PLUGIN_PATH')) {
    define('PROJECT_PLUGIN_PATH', plugin_dir_path(__FILE__));
}
function project_plugin_activation()
{
    /*** Plugin Table ***/
    global $wpdb;
    $projects_table_name = $wpdb->prefix . 'projects';
    $depatment_name_table_name = $wpdb->prefix . 'depatment_name';
    $profile_name_table_name = $wpdb->prefix . 'profile_name';

    if ($wpdb->get_var("SHOW TABLES LIKE '$depatment_name_table_name'") != $depatment_name_table_name) {
        $sql = "CREATE TABLE $depatment_name_table_name (
            id int(11) NOT NULL auto_increment,
            depatment_name varchar(500) NOT NULL,
            created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

        require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    if ($wpdb->get_var("SHOW TABLES LIKE '$projects_table_name'") != $projects_table_name) {
        $sql = "CREATE TABLE $projects_table_name (
            id int(11) NOT NULL auto_increment,
            project varchar(500) NOT NULL,
            depatment_id int(11) NOT NULL,
            team_lead int(11) NOT NULL,
            profile_ids varchar(255) NOT NULL,
            allocated_hours float NOT NULL DEFAULT 0,
            created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            CONSTRAINT fk_department FOREIGN KEY (depatment_id) REFERENCES {$wpdb->prefix}depatment_name(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

        require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    if ($wpdb->get_var("SHOW TABLES LIKE '$profile_name_table_name'") != $profile_name_table_name) {
        $sql = "CREATE TABLE $profile_name_table_name (
            id int(11) NOT NULL auto_increment,
            profile_name varchar(500) NOT NULL,
            created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";

        require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}
//Register Activation Hook 
register_activation_hook(__FILE__, 'project_plugin_activation');
//Create Menus
function project_plugin_menu_function()
{
    add_menu_page('Projects', 'Projects', 'manage_options', 'project_list', 'display_project_list', 'dashicons-portfolio');
    add_submenu_page('project_list', 'Add Project', 'Add Project', 'manage_options', 'add_project', 'display_project_form');
    add_submenu_page('project_list', 'Project Profile Settings', 'Project Profile Settings', 'manage_options', 'project_profile_settings', 'project_profile_settings');
    add_submenu_page('project_list', 'Project Depatment Settings', 'Project Depatment Settings', 'manage_options', 'project_depatment_settings', 'project_depatment_settings');
}
add_action('admin_menu', 'project_plugin_menu_function');
//plugin deactivation 
function project_plugin_deactivation()
{
    /*** Drop Database Table ***/
    global $wpdb, $table_prefix;
    $table_name = $table_prefix . 'projects';
    $truncate_table_query = "drop TABLE $table_name;";
    $wpdb->query($truncate_table_query);
    $table_name = $table_prefix . 'depatment_name';
    $truncate_table_query = "drop TABLE $table_name;";
    $wpdb->query($truncate_table_query);
    $table_name = $table_prefix . 'profile_name';
    $truncate_table_query = "drop TABLE $table_name;";
    $wpdb->query($truncate_table_query);
}
//Register Deactivation Hook 
register_deactivation_hook(__FILE__, 'project_plugin_deactivation');
// add menu function file 
require_once PROJECT_PLUGIN_PATH . 'menu_functions.php';
require_once PROJECT_PLUGIN_PATH . 'department_menu_fun.php';
require_once PROJECT_PLUGIN_PATH . 'profile_menu_fun.php';
