<?php

/**
 * functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * 
 */
if (!defined('_T_VERSION')) {
    define('_T_VERSION', date("d-m-Y H:i:s"));
}
/* ------ Enqueue Styles And Scripts ------ */
if (!function_exists('style_script')):
    function style_script()
    {
        /* CSS */
        wp_enqueue_style('main_css', get_stylesheet_uri(), array(), _T_VERSION, false);
        wp_enqueue_style('bootstrap_css', get_template_directory_uri() . '/assets/css/bootstrap.css', _T_VERSION, false);
        wp_enqueue_style('select2', get_template_directory_uri() . '/assets/css/select2.css', _T_VERSION, false);
        wp_enqueue_style('theme_css', get_template_directory_uri() . '/assets/css/custom-style.css', _T_VERSION, false);
        /* JS */
        wp_enqueue_script('jquery_js', get_template_directory_uri() . '/assets/js/jquery.min.js', array(), _T_VERSION, false);
        wp_enqueue_script('bootstrap_js', get_template_directory_uri() . '/assets/js/bootstrap.js', array(), _T_VERSION, true);
        wp_enqueue_script('select2_js', get_template_directory_uri() . '/assets/js/select2.js', array(), _T_VERSION, true);
        wp_enqueue_script('chart', get_template_directory_uri() . '/assets/js/chart.js', array(), _T_VERSION, true);
        wp_enqueue_script('functions', get_template_directory_uri() . '/assets/js/functions.js', array(), _T_VERSION, true);
    }
    add_action('wp_enqueue_scripts', 'style_script');
endif;
include get_template_directory() . '/inc/mail_functions.php';
include get_template_directory() . '/inc/admin_fun.php';
/* ------ Title Support & Post-thumbnails Support & Post-Formats Support ------ */
if (!function_exists('custom__theme__setup')):
    function custom__theme__setup()
    {
        add_theme_support('title_tag');
        add_theme_support('post-thumbnails');
        // Register Theme Custom Logo
        $defaults = array(
            'height' => 100,
            'width' => 400,
            'flex-height' => true,
            'flex-width' => true,
            'header-text' => array('site-title', 'site-description'),
            'unlink-homepage-logo' => true,
        );
        add_theme_support('custom-logo', $defaults);
    }
    add_action('after_setup_theme', 'custom__theme__setup');
endif;
//global veriables
global $wpdb;
$current_user_id = get_current_user_id();
$project_table_name = $wpdb->prefix . 'projects';
$current_date = date('Y-m-d');
$profile_table_name = $wpdb->prefix . 'profile_name';
$user_dept_obj = get_user_meta(get_current_user_id(), 'department');
$user_dept = $user_dept_obj[0];
if ($user_dept == 1) {
    $performance_table_name = $wpdb->prefix . 'dev_performance_data';
} else if ($user_dept == 2) {
    $performance_table_name = $wpdb->prefix . 'marketing_performance_data';
} else if ($user_dept == 3) {
    $performance_table_name = $wpdb->prefix . 'sales_performance_data';
}
$current_user = wp_get_current_user();
//ajax functions 
// Add AJAX action edit performance handler
add_action('wp_ajax_update_performance_entry', 'update_performance_entry_callback');
function update_performance_entry_callback()
{
    global $wpdb;
    $user_dept_obj = get_user_meta(get_current_user_id(), 'department');
    $user_dept = $user_dept_obj[0];
    $project_table_name = $wpdb->prefix . 'projects';
    if ($user_dept == 1) {
        $performance_table_name = $wpdb->prefix . 'dev_performance_data';
    } else if ($user_dept == 2) {
        $performance_table_name = $wpdb->prefix . 'marketing_performance_data';
    } else if ($user_dept == 3) {
        $performance_table_name = $wpdb->prefix . 'sales_performance_data';
    }
    $id = sanitize_text_field($_POST['entry_id']);
    $project_name = sanitize_text_field($_POST['project_name']);
    $online_offline = sanitize_text_field($_POST['online_offline']);
    $number_of_hours = sanitize_text_field($_POST['number_of_hours']);
    $billing_status = sanitize_text_field($_POST['billing_status']);
    $profile_name = sanitize_text_field($_POST['profile_name']);
    $notes = sanitize_text_field($_POST['notes']);
    $reviewed_by = $wpdb->get_row($wpdb->prepare("SELECT team_lead FROM $project_table_name WHERE id = %d", $project_name), ARRAY_A);
    $is_valid = true;
    if ($online_offline == 'Online' && $profile_name == '') {
        $is_valid = false;
    }
    if ($is_valid) {
        $is_updated = $wpdb->update(
            $performance_table_name,
            array(
                'project_id' => $project_name,
                'online_offline' => $online_offline,
                'number_of_hours' => $number_of_hours,
                'billing_status' => $billing_status,
                'profile_name' => $profile_name,
                'notes' => $notes,
                'reviewed_by' => $reviewed_by["team_lead"],
            ),
            array('id' => $id)
        );
        if (false === $is_updated) {
            echo 'error';
        } else {
            $profile_table_name = $wpdb->prefix . 'profile_name';
            $project_sql_select = "SELECT * FROM $project_table_name  where `depatment_id` = '$user_dept' ";
            $project_data_results = $wpdb->get_results($project_sql_select, ARRAY_A);
            $tr_project = $wpdb->prepare("SELECT * FROM $project_table_name WHERE id = %d", $project_name);
            $tr_project_results = $wpdb->get_results($tr_project);
            $tr_project = $tr_project_results[0]->project;
            $hs_cal = floatval($number_of_hours);
            $hs_hour = floor($hs_cal);
            $hs_min = round(($hs_cal - $hs_hour) * 60);
            $profile_name_id = $profile_name;
            $profile_name = $wpdb->get_row($wpdb->prepare("SELECT profile_name FROM $profile_table_name WHERE id = %d", $profile_name), ARRAY_A);
            $profile_ids = $tr_project_results[0]->profile_ids;
            $profile_query = $wpdb->prepare("SELECT * FROM $profile_table_name WHERE id IN ( $profile_ids)", $profile_ids);
            $profile_name_arr = $wpdb->get_results($profile_query, ARRAY_A);
            $html = '<td><span class="data-field" data-field="project_name">' . $tr_project . '</span><div style="max-width: 200px !important;" class="hs_hide_tr_select"><select class="edit-field form-control hs_select" data-field="project_name" style="display: none;" name="project_name">';
            foreach ($project_data_results as $project_row) {
                $html .= '<option value="' . $project_row["id"] . '" ' . ($project_name == $project_row["id"] ? "selected" : "") . ' data-profile="' . $project_row["profile_ids"] . '">' . $project_row["project"] . '</option>';
            }
            $html .= '</select></div></td><td><span class="data-field" data-field="online_offline">' . $online_offline . '</span><select class="edit-field form-control" data-field="online_offline" style="display:none;"><option value="online" ' . ($online_offline == "online" ? "selected" : "") . '>Online</option><option value="offline" ' . ($online_offline == "offline" ? "selected" : "") . '>Offline</option></select></td><td><span class="data-field" data-field="hs_number_of_hours">' . $hs_hour . '</span><div class="hs_custom_time"><select name="hs_hour" class="form-control edit-field" data-field="hs_number_of_hours" data-id="hs_hour" style="display:none;"><option ' . ($hs_hour == 0 ? "selected" : "") . ' value="0">0</option><option ' . ($hs_hour == 1 ? "selected" : "") . ' value="1">1</option><option ' . ($hs_hour == 2 ? "selected" : "") . ' value="2">2</option><option ' . ($hs_hour == 3 ? "selected" : "") . ' value="3">3</option><option ' . ($hs_hour == 4 ? "selected" : "") . ' value="4">4</option><option ' . ($hs_hour == 5 ? "selected" : "") . ' value="5">5</option><option ' . ($hs_hour == 6 ? "selected" : "") . ' value="6">6</option><option ' . ($hs_hour == 7 ? "selected" : "") . ' value="7">7</option><option ' . ($hs_hour == 8 ? "selected" : "") . ' value="8">8</option><option ' . ($hs_hour == 9 ? "selected" : "") . ' value="9">9</option><option ' . ($hs_hour == 10 ? "selected" : "") . ' value="10">10</option><option ' . ($hs_hour == 11 ? "selected" : "") . ' value="11">11</option><option ' . ($hs_hour == 12 ? "selected" : "") . ' value="12">12</option></select><input type="hidden" name="number_of_hours" class="edit_h number_of_hour" data-hs_field="number_of_hours" value="' . $number_of_hours . '" disabled></div></td><td><span class="data-field" data-field="number_of_min">' . $hs_min . '</span><div class="hs_custom_time "><select name="hs_min" class="form-control edit-field" data-field="number_of_min" data-id="hs_min" style="display:none;"><option ' . ($hs_min == 0 ? "selected" : "") . ' value="0" selected>00</option><option ' . ($hs_min == 10 ? "selected" : "") . ' value="10">10</option><option ' . ($hs_min == 20 ? "selected" : "") . ' value="20">20</option><option ' . ($hs_min == 30 ? "selected" : "") . ' value="30">30</option><option ' . ($hs_min == 40 ? "selected" : "") . ' value="40">40</option><option ' . ($hs_min == 50 ? "selected" : "") . ' value="50">50</option></select></div></td><td><span class="data-field" data-field="billing_status">' . $billing_status . '</span><select class="edit-field form-control" data-field="billing_status" style="display:none;"><option value="billable" ' . ($billing_status == "billable" ? "selected" : "") . '>Billable</option><option value="non-billable" ' . ($billing_status == "non-billable" ? "selected" : "") . '>Non Billable</option><option value="no-work" ' . ($billing_status == "no-work" ? "selected" : "") . '>No Work</option></select></td><td><span class="data-field" data-field="profile_name">' . $profile_name["profile_name"] . '</span><div style="max-width: 200px !important;" class="hs_hide_tr_select"><select name="profile_name" class="edit-field form-control hs_select" data-field="profile_name" style="display:none;"><option value="">Select Profile Name</option>';
            foreach ($profile_name_arr as $profile_names) {
                $html .= '<option value="' . $profile_names["id"] . '" ' . ($profile_name_id == $profile_names["id"] ? 'selected' : '') . '>' . $profile_names["profile_name"] . '</option>';
            }
            $html .= '</select></div></td><td><span class="data-field" data-field="notes">' . $notes . '</span><textarea class="edit-field form-control" data-field="notes" style="display:none;">' . $notes . '</textarea></td><td><div class="action_td"><button class="edit-entry btn btn-info" data-entry-id="' . $wpdb->insert_id . '"><span class="dashicons dashicons-edit"></span></button><button class="update-entry btn btn-warning" data-entry-id="' . $wpdb->insert_id . '" style="display:none;"><span class="dashicons dashicons-saved"></span></button><button class="delete-entry btn btn-danger" data-entry-id="' . $wpdb->insert_id . '"><span class="dashicons dashicons-trash"></span></button></div></td>';
            echo $html;
        }
    } else {
        echo 'error';
    }
    wp_die();
}
// Add AJAX action delete performance handler
add_action('wp_ajax_delete_performance_entry', 'delete_performance_entry_callback');
function delete_performance_entry_callback()
{
    global $wpdb;
    $entry_id = sanitize_text_field($_POST['entry_id']);
    $user_dept_obj = get_user_meta(get_current_user_id(), 'department');
    $user_dept = $user_dept_obj[0];
    $user_id = sanitize_text_field($_POST['user_id']);
    if ($user_dept == 1) {
        $performance_table_name = $wpdb->prefix . 'dev_performance_data';
    } else if ($user_dept == 2) {
        $performance_table_name = $wpdb->prefix . 'marketing_performance_data';
    } else if ($user_dept == 3) {
        $performance_table_name = $wpdb->prefix . 'sales_performance_data';
    }
    $sql = $wpdb->prepare("DELETE FROM `$performance_table_name` WHERE `id` = %d  AND `user_id` = %d", $entry_id, $user_id);
    try {
        $result = $wpdb->query($sql);
        if ($result !== false && $result > 0) {
            echo 'deleted';
        } else {
            echo 'No matching rows found';
        }
    } catch (Exception $e) {
        echo 'Error! ' . $wpdb->last_error;
    }
    wp_die();
}
// Add AJAX action submit performance handler
add_action('wp_ajax_submit_performance_entry', 'submit_performance_entry_callback');
function submit_performance_entry_callback()
{
    global $wpdb;
    $number_of_hours = sanitize_text_field($_POST['number_of_hours']);
    $c_date = sanitize_text_field($_POST['c_date']);
    $user_dept_obj = get_user_meta(get_current_user_id(), 'department');
    $user_dept = $user_dept_obj[0];
    $user_id = sanitize_text_field($_POST['user_id']);
    if ($user_dept == 1) {
        $performance_table_name = $wpdb->prefix . 'dev_performance_data';
    } else if ($user_dept == 2) {
        $performance_table_name = $wpdb->prefix . 'marketing_performance_data';
    } else if ($user_dept == 3) {
        $performance_table_name = $wpdb->prefix . 'sales_performance_data';
    }
    if ($number_of_hours < 8) {
        $project_table_name = $wpdb->prefix . 'projects';
        $query = $wpdb->prepare("SELECT * FROM $project_table_name WHERE project = 'No Work' AND depatment_id = %d", $user_dept);
        $project_id = $wpdb->get_row($query);
        $wpdb->insert(
            $performance_table_name,
            array(
                'date' => $c_date,
                'user_id' => $user_id,
                'project_id' => $project_id->id,
                'online_offline' => 'offline',
                'number_of_hours' => 8 - $number_of_hours,
                'billing_status' => 'no-work',
                'profile_name' => 0,
                'notes' => '',
                'reviewed_by' => 0,
            )
        );
    }
    $sql = $wpdb->prepare("UPDATE `$performance_table_name` SET is_submit = 1 WHERE  `date` = '" . $c_date . "' AND `user_id` = '" . $user_id . "'");
    try {
        $result = $wpdb->query($sql);
        if ($result !== false && $result > 0) {
            echo 'submitted';
        }
    } catch (Exception $e) {
        echo 'Error! ' . $wpdb->last_error;
    }
    send_email($c_date);
    wp_die();
}
// Add AJAX action add handler for add performance entry
add_action('wp_ajax_add_performance_entry', 'add_performance_entry_callback');
function add_performance_entry_callback()
{
    $current_user_id = get_current_user_id();
    $current_date = date('Y-m-d');
    $user_dept_obj = get_user_meta(get_current_user_id(), 'department');
    $user_dept = $user_dept_obj[0];
    global $wpdb;
    if ($user_dept == 1) {
        $performance_table_name = $wpdb->prefix . 'dev_performance_data';
    } else if ($user_dept == 2) {
        $performance_table_name = $wpdb->prefix . 'marketing_performance_data';
    } else if ($user_dept == 3) {
        $performance_table_name = $wpdb->prefix . 'sales_performance_data';
    }
    $project_name = sanitize_text_field($_POST['project_name']);
    $online_offline = sanitize_text_field($_POST['online_offline']);
    $number_of_hours = sanitize_text_field($_POST['number_of_hours']);
    $billing_status = sanitize_text_field($_POST['billing_status']);
    $profile_name = sanitize_text_field($_POST['profile_name']);
    $notes = sanitize_text_field($_POST['notes']);
    $project_table_name = $wpdb->prefix . 'projects';
    $reviewed_by = $wpdb->get_row($wpdb->prepare("SELECT team_lead FROM $project_table_name WHERE id = %d", $project_name), ARRAY_A);
    $is_inserted = $wpdb->insert(
        $performance_table_name,
        array(
            'date' => $current_date,
            'user_id' => $current_user_id,
            'project_id' => $project_name,
            'online_offline' => $online_offline,
            'number_of_hours' => $number_of_hours,
            'billing_status' => $billing_status,
            'profile_name' => $profile_name,
            'notes' => $notes,
            'reviewed_by' => $reviewed_by["team_lead"],
        )
    );
    if (false === $is_inserted) {
        echo 'error';
    } else {
        $profile_table_name = $wpdb->prefix . 'profile_name';
        $project_sql_select = "SELECT * FROM $project_table_name  where `depatment_id` = '$user_dept' ";
        $project_data_results = $wpdb->get_results($project_sql_select, ARRAY_A);
        $tr_project = $wpdb->prepare("SELECT * FROM $project_table_name WHERE id = %d", $project_name);
        $tr_project_results = $wpdb->get_results($tr_project);
        $tr_project = $tr_project_results[0]->project;
        $hs_cal = floatval($number_of_hours);
        $hs_hour = floor($hs_cal);
        $hs_min = round(($hs_cal - $hs_hour) * 60);
        $profile_name_id = $profile_name;
        $profile_name = $wpdb->get_row($wpdb->prepare("SELECT profile_name FROM $profile_table_name WHERE id = %d", $profile_name), ARRAY_A);
        $profile_query = $wpdb->prepare("SELECT * FROM $profile_table_name WHERE id IN ($profile_name)", $profile_name);
        $profile_name_arr = $wpdb->get_results($profile_query, ARRAY_A);
        $profile_ids = $tr_project_results[0]->profile_ids;
        $profile_query = $wpdb->prepare("SELECT * FROM $profile_table_name WHERE id IN ( $profile_ids)", $profile_ids);
        $profile_name_arr = $wpdb->get_results($profile_query, ARRAY_A);
        $html = '<tr><td><span class="data-field" data-field="project_name">' . $tr_project . '</span><div style="max-width: 200px !important;" class="hs_hide_tr_select"><select class="edit-field form-control hs_select" data-field="project_name" style="display: none;" name="project_name">';
        foreach ($project_data_results as $project_row) {
            $html .= '<option value="' . $project_row["id"] . '" ' . ($project_name == $project_row["id"] ? "selected" : "") . ' data-profile="' . $project_row["profile_ids"] . '">' . $project_row["project"] . '</option>';
        }
        $html .= '</select></div></td><td><span class="data-field" data-field="online_offline">' . $online_offline . '</span><select class="edit-field form-control" data-field="online_offline" style="display:none;"><option value="online" ' . ($online_offline == "online" ? "selected" : "") . '>Online</option><option value="offline" ' . ($online_offline == "offline" ? "selected" : "") . '>Offline</option></select></td><td><span class="data-field" data-field="hs_number_of_hours">' . $hs_hour . '</span><div class="hs_custom_time"><select name="hs_hour" class="form-control edit-field" data-field="hs_number_of_hours" data-id="hs_hour" style="display:none;"><option ' . ($hs_hour == 0 ? "selected" : "") . ' value="0">0</option><option ' . ($hs_hour == 1 ? "selected" : "") . ' value="1">1</option><option ' . ($hs_hour == 2 ? "selected" : "") . ' value="2">2</option><option ' . ($hs_hour == 3 ? "selected" : "") . ' value="3">3</option><option ' . ($hs_hour == 4 ? "selected" : "") . ' value="4">4</option><option ' . ($hs_hour == 5 ? "selected" : "") . ' value="5">5</option><option ' . ($hs_hour == 6 ? "selected" : "") . ' value="6">6</option><option ' . ($hs_hour == 7 ? "selected" : "") . ' value="7">7</option><option ' . ($hs_hour == 8 ? "selected" : "") . ' value="8">8</option></select><input type="hidden" name="number_of_hours" class="edit_h number_of_hour" data-hs_field="number_of_hours" value="' . $number_of_hours . '" disabled></div></td><td><span class="data-field" data-field="number_of_min">' . $hs_min . '</span><div class="hs_custom_time "><select name="hs_min" class="form-control edit-field" data-field="number_of_min" data-id="hs_min" style="display:none;"><option ' . ($hs_min == 0 ? "selected" : "") . ' value="0" selected>00</option><option ' . ($hs_min == 10 ? "selected" : "") . ' value="10">10</option><option ' . ($hs_min == 20 ? "selected" : "") . ' value="20">20</option><option ' . ($hs_min == 30 ? "selected" : "") . ' value="30">30</option><option ' . ($hs_min == 40 ? "selected" : "") . ' value="40">40</option><option ' . ($hs_min == 50 ? "selected" : "") . ' value="50">50</option></select></div></td><td><span class="data-field" data-field="billing_status">' . $billing_status . '</span><select class="edit-field form-control" data-field="billing_status" style="display:none;"><option value="billable" ' . ($billing_status == "billable" ? "selected" : "") . '>Billable</option><option value="non-billable" ' . ($billing_status == "non-billable" ? "selected" : "") . '>Non Billable</option><option value="no-work" ' . ($billing_status == "no-work" ? "selected" : "") . '>No Work</option></select></td><td><span class="data-field" data-field="profile_name">' . $profile_name["profile_name"] . '</span><div style="max-width: 200px !important;" class="hs_hide_tr_select"><select name="profile_name" class="edit-field form-control hs_select" data-field="profile_name" style="display:none;"><option value="">Select Profile Name</option>';
        foreach ($profile_name_arr as $profile_names) {
            $html .= '<option value="' . $profile_names["id"] . '" ' . ($profile_name_id == $profile_names["id"] ? 'selected' : '') . '>' . $profile_names["profile_name"] . '</option>';
        }
        $html .= '</select></div></td><td><span class="data-field" data-field="notes">' . $notes . '</span><textarea class="edit-field form-control" data-field="notes" style="display:none;">' . $notes . '</textarea></td><td><div class="action_td"><button class="edit-entry btn btn-info" data-entry-id="' . $wpdb->insert_id . '"><span class="dashicons dashicons-edit"></span></button><button class="update-entry btn btn-warning" data-entry-id="' . $wpdb->insert_id . '" style="display:none;"><span class="dashicons dashicons-saved"></span></button><button class="delete-entry btn btn-danger" data-entry-id="' . $wpdb->insert_id . '"><span class="dashicons dashicons-trash"></span></button></div></td></tr>';
        echo $html;
    }
    wp_die();
}
// Add Action for approve performance request//
add_action('wp_ajax_approve_fun', 'approve_fun_callback');
function approve_fun_callback()
{
    $date = sanitize_text_field($_POST['date']);
    $user_id = sanitize_text_field($_POST['user_id']);
    $is_approve = sanitize_text_field($_POST['is_approve']);
    $note = sanitize_text_field($_POST['note']);
    $user_dept_obj = get_user_meta($user_id, 'department');
    $user_dept = $user_dept_obj[0];
    global $wpdb;
    if ($user_dept == 1) {
        $performance_table_name = $wpdb->prefix . 'dev_performance_data';
    } else if ($user_dept == 2) {
        $performance_table_name = $wpdb->prefix . 'marketing_performance_data';
    } else if ($user_dept == 3) {
        $performance_table_name = $wpdb->prefix . 'sales_performance_data';
    }
    if ($is_approve == 1) {
        $is_submit = 1;
    } else {
        $is_submit = 0;
    }
    $is_updated = $wpdb->update(
        $performance_table_name,
        array(
            'is_approve' => $is_approve,
            'is_submit' => $is_submit,
            'reviewed_by' => get_current_user_id(),
        ),
        array('date' => $date, 'user_id' => $user_id)
    );
    if (false === $is_updated) {
        echo 'error';
    }
    if ($is_approve == 2) {
        send_reject_email($user_id, $note);
    } else {
        send_approve_email($user_id, $note);
    }
    wp_die();
}
// add action for get profile//
add_action('wp_ajax_get_profile', 'get_profile_callback');
function get_profile_callback()
{
    global $wpdb;
    $project_table_name = $wpdb->prefix . 'projects';
    $profile_table_name = $wpdb->prefix . 'profile_name';
    $user_dept_obj = get_user_meta(get_current_user_id(), 'department');
    $user_dept = $user_dept_obj[0];
    if ($user_dept == 1) {
        $performance_table_name = $wpdb->prefix . 'dev_performance_data';
    } else if ($user_dept == 2) {
        $performance_table_name = $wpdb->prefix . 'marketing_performance_data';
    } else if ($user_dept == 3) {
        $performance_table_name = $wpdb->prefix . 'sales_performance_data';
    }
    $profile_id = sanitize_text_field($_POST['profile_id']);
    $project_id = sanitize_text_field($_POST['project_id']);
    $profile_query = $wpdb->prepare("SELECT * FROM $profile_table_name WHERE id IN ($profile_id)", $profile_id);
    $profile_name = $wpdb->get_results($profile_query, ARRAY_A);
    $project_query = $wpdb->prepare("SELECT allocated_hours FROM $project_table_name WHERE id ='" . $project_id . "'");
    $allocated_hours = $wpdb->get_row($project_query, ARRAY_A);
    $number_of_hours = $wpdb->get_var("SELECT sum(number_of_hours) FROM $performance_table_name  where billing_status = 'billable' AND project_id='" . $project_id . "' ");
    $hs_date = ['allocated_hours' => $allocated_hours['allocated_hours'], 'profile_name' => $profile_name, 'number_of_hours' => $number_of_hours];
    echo json_encode($hs_date);
    wp_die();
}
// add action for save user info // 
add_action('wp_ajax_hs_emp_view', 'hs_emp_view_callback');
function hs_emp_view_callback()
{
    global $wpdb;
    $user_dept_obj = get_user_meta(get_current_user_id(), 'department');
    $user_dept = $user_dept_obj[0];
    if ($user_dept == 1) {
        $performance_table_name = $wpdb->prefix . 'dev_performance_data';
    } else if ($user_dept == 2) {
        $performance_table_name = $wpdb->prefix . 'marketing_performance_data';
    } else if ($user_dept == 3) {
        $performance_table_name = $wpdb->prefix . 'sales_performance_data';
    }
    $project_table_name = $wpdb->prefix . 'projects';
    $profile_table_name = $wpdb->prefix . 'profile_name';
    $current_user_id = get_current_user_id();
    $start_date = sanitize_text_field($_POST['start_date']);
    $end_date = sanitize_text_field($_POST['end_date']);
    $tr_user_id = sanitize_text_field($_POST['user_id']);
    $date_q_str = ' ="' . $start_date . '"';
    if ($end_date) {
        $date_q_str = " BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
    }
    if ($tr_user_id) {
        $current_user_id = $tr_user_id;
    }
    $performance_sql_select = "SELECT * FROM $performance_table_name INNER JOIN $project_table_name ON $performance_table_name.project_id = $project_table_name.id INNER JOIN $profile_table_name ON $performance_table_name.profile_name = $profile_table_name.id WHERE $performance_table_name.`user_id` = '$current_user_id' AND $performance_table_name.`date` $date_q_str ";
    $performance_data_results = $wpdb->get_results($performance_sql_select, ARRAY_A);
    $performance_sql_select = "SELECT * FROM $performance_table_name INNER JOIN $project_table_name ON $performance_table_name.project_id = $project_table_name.id WHERE $performance_table_name.`user_id` = '$current_user_id' AND profile_name='0' AND $performance_table_name.`date` $date_q_str ";
    $performance_data_results_2 = $wpdb->get_results($performance_sql_select, ARRAY_A);
    echo json_encode(array_merge($performance_data_results, $performance_data_results_2));
    wp_die();
}
// add action for employee search //
add_action('wp_ajax_hs_emp_search', 'hs_emp_search_callback');
function hs_emp_search_callback()
{
    $html = '';
    $start_date = sanitize_text_field($_POST['start_date']);
    $end_date = sanitize_text_field($_POST['end_date']);
    $month = new DateTime(date($start_date));
    $today = new DateTime(date($end_date));
    global $wpdb;
    $user_dept_obj = get_user_meta(get_current_user_id(), 'department');
    $user_dept = $user_dept_obj[0];
    if ($user_dept == 1) {
        $performance_table_name = $wpdb->prefix . 'dev_performance_data';
    } else if ($user_dept == 2) {
        $performance_table_name = $wpdb->prefix . 'marketing_performance_data';
    } else if ($user_dept == 3) {
        $performance_table_name = $wpdb->prefix . 'sales_performance_data';
    }
    $current_user_id = get_current_user_id();
    while ($today->format('Y-m-d') >= $month->format('Y-m-d')) {
        $tr_date = $today->format('Y-m-d');
        $today->modify('-1 day');
        if ($today->format('w') == 6) {
            $html .= '<tr><td colspan="5" class="non_working_day">Non Working Day</td></tr>';
        } else {
            $productive_hours = $wpdb->get_var("SELECT sum(number_of_hours) FROM $performance_table_name  where billing_status = 'billable' AND `user_id` = '$current_user_id'  AND date ='" . $tr_date . "'");
            $non_productive_hours = $wpdb->get_var("SELECT sum(number_of_hours) FROM $performance_table_name  where billing_status = 'non-billable' AND `user_id` = '$current_user_id'  AND date ='" . $tr_date . "'");
            $no_work_hours = $wpdb->get_var("SELECT sum(number_of_hours) FROM $performance_table_name  where billing_status = 'no-work' AND `user_id` = '$current_user_id'  AND date ='" . $tr_date . "'");
            $html .= ' <tr><td>' . $tr_date . '</td><td>' . $productive_hours . '</td><td>' . $non_productive_hours . '</td><td>' . $no_work_hours . '</td><td><button type="button" class="btn btn-primary hs_emp_view" data-bs-toggle="modal" data-bs-target="#hs_emp_modal" data-date="' . $tr_date . '" >View</button></td></tr>';
        }
    }
    echo $html;
    wp_die();
}
//add action for team lead search //
add_action('wp_ajax_hs_emp_search_tl', 'hs_emp_search_tl_callback');
function hs_emp_search_tl_callback()
{
    $html = '';
    $start_date = sanitize_text_field($_POST['start_date']);
    $end_date = sanitize_text_field($_POST['end_date']);
    $user_id = sanitize_text_field($_POST['user_id']);
    global $wpdb;
    $user_dept_obj = get_user_meta(get_current_user_id(), 'department');
    $user_dept = $user_dept_obj[0];
    if ($user_dept == 1) {
        $performance_table_name = $wpdb->prefix . 'dev_performance_data';
    } else if ($user_dept == 2) {
        $performance_table_name = $wpdb->prefix . 'marketing_performance_data';
    } else if ($user_dept == 3) {
        $performance_table_name = $wpdb->prefix . 'sales_performance_data';
    }
    $td_data = 'data-start_date="' . $start_date . '"';
    $date_q_str = ' ="' . $start_date . '"';
    if ($end_date) {
        $date_q_str = " BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
        $td_data = 'data-start_date="' . $start_date . '"data-end_date="' . $end_date . '"';
    }
    if ($user_id) {
        $productive_hours = $wpdb->get_var("SELECT sum(number_of_hours) FROM $performance_table_name  where billing_status = 'billable' AND `user_id` = '$user_id'  AND date $date_q_str AND is_submit ='1'");
        $non_productive_hours = $wpdb->get_var("SELECT sum(number_of_hours) FROM $performance_table_name  where billing_status = 'non-billable' AND `user_id` = '$user_id'  AND date $date_q_str AND is_submit ='1'");
        $no_work_hours = $wpdb->get_var("SELECT sum(number_of_hours) FROM $performance_table_name  where billing_status = 'no-work' AND `user_id` = '$user_id'  AND date $date_q_str AND is_submit ='1'");
        $html .= ' <tr><td>' . get_userdata($user_id)->display_name . '</td><td>' . $productive_hours . '</td><td>' . $non_productive_hours . '</td><td>' . $no_work_hours . '</td> ';
        if (!$end_date) {
            $html .= '<td>';
            $status = $wpdb->get_row("SELECT * FROM $performance_table_name  where  `user_id` = '$user_id'  AND date $date_q_str AND is_submit ='1'");
            if ($status) {
                if ($status->is_approve == 0) {
                    $html .= 'Pending';
                } else if ($status->is_approve == 1) {
                    $html .= 'Aproved';
                } else if ($status->is_approve == 2 && $status->is_submit == 1) {
                    $html .= 'Rejected';
                }
            }
            $html .= '</td>';
        }
        $html .= '<td><button type="button" class="btn btn-primary hs_team_lead_view" data-bs-toggle="modal" data-bs-target="#team_Lead_modal" ' . $td_data . ' data-user_id="' . $user_id . '" data-user="' . get_userdata($user_id)->display_name . '">View</button></td></tr>';
    } else {
        $team_users = get_users(
            array(
                'department' => $user_dept,
            )
        );
        foreach ($team_users as $team_user) {
            $productive_hours = $wpdb->get_var("SELECT sum(number_of_hours) FROM $performance_table_name  where billing_status = 'billable' AND `user_id` = '$team_user->ID'  AND date $date_q_str AND is_submit ='1'");
            $non_productive_hours = $wpdb->get_var("SELECT sum(number_of_hours) FROM $performance_table_name  where billing_status = 'non-billable' AND `user_id` = '$team_user->ID'  AND date $date_q_str AND is_submit ='1'");
            $no_work_hours = $wpdb->get_var("SELECT sum(number_of_hours) FROM $performance_table_name  where billing_status = 'no-work' AND `user_id` = '$team_user->ID'  AND date $date_q_str AND is_submit ='1'");
            $html .= ' <tr><td>' . $team_user->display_name . '</td><td>' . $productive_hours . '</td><td>' . $non_productive_hours . '</td><td>' . $no_work_hours . '</td>';
            if (!$end_date) {
                $html .= '<td>';
                $status = $wpdb->get_row("SELECT * FROM $performance_table_name  where  `user_id` = '$team_user->ID'  AND date $date_q_str AND is_submit ='1'");
                if ($status) {
                    if ($status->is_approve == 0) {
                        $html .= 'Pending';
                    } else if ($status->is_approve == 1) {
                        $html .= 'Aproved';
                    } else if ($status->is_approve == 2 && $status->is_submit == 1) {
                        $html .= 'Rejected';
                    }
                }
                $html .= '</td>';
            }
            $html .= '<td><button type="button" class="btn btn-primary hs_team_lead_view" data-bs-toggle="modal" data-bs-target="#team_Lead_modal" ' . $td_data . ' data-user_id="' . $team_user->ID . '" data-user="' . $team_user->display_name . '">View</button></td></tr>';
        }
    }
    echo $html;
    wp_die();
}
