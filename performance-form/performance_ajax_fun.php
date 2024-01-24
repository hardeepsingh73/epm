<?php

// Performance Billing Status Update

add_action('wp_ajax_admin_billing_status', 'admin_billing_status_callback');
function admin_billing_status_callback()
{
    global $wpdb;
    $entry_id = sanitize_text_field($_POST['entry_id']);
    $user_id = sanitize_text_field($_POST['user_id']);
    $value = sanitize_text_field($_POST['value']);
    $user_dept_obj = get_user_meta($user_id, 'department');
    $user_dept = $user_dept_obj[0];
    if ($user_dept == 1) {
        $performance_table_name = $wpdb->prefix . 'dev_performance_data';
    } else if ($user_dept == 2) {
        $performance_table_name = $wpdb->prefix . 'marketing_performance_data';
    } else if ($user_dept == 3) {
        $performance_table_name = $wpdb->prefix . 'sales_performance_data';
    }
    $wpdb->update(
        $performance_table_name,
        array(
            'billing_status' => $value
        ),
        array('id' => $entry_id)
    );
    wp_die();
}

// Performance User List Data Update
add_action('wp_ajax_admin_update_user_list', 'admin_update_user_list_callback');
function admin_update_user_list_callback()
{
    global $wpdb;
    $id = sanitize_text_field($_POST['id']);
    $user_id = sanitize_text_field($_POST['user_id']);
    $number_of_hours = sanitize_text_field($_POST['number_of_hours']);
    $billing_status = sanitize_text_field($_POST['billing_status']);
    $online_offline = sanitize_text_field($_POST['online_offline']);
    $profile_name = sanitize_text_field($_POST['profile_name']);
    $user_dept_obj = get_user_meta($user_id, 'department');
    $user_dept = $user_dept_obj[0];
    if ($user_dept == 1) {
        $performance_table_name = $wpdb->prefix . 'dev_performance_data';
    } else if ($user_dept == 2) {
        $performance_table_name = $wpdb->prefix . 'marketing_performance_data';
    } else if ($user_dept == 3) {
        $performance_table_name = $wpdb->prefix . 'sales_performance_data';
    }
    if ($number_of_hours > 0) {
        $wpdb->update(
            $performance_table_name,
            array(
                'number_of_hours' => $number_of_hours,
                'profile_name' => $profile_name,
                'online_offline' => $online_offline,
                'billing_status' => $billing_status
            ),
            array('id' => $id)
        );
    } else {
        $wpdb->delete($performance_table_name, array('id' => $id));
    }
    wp_die();
}
// Performance Get User Data
add_action('wp_ajax_admin_get_user_detail', 'admin_get_user_detail_callback');
function admin_get_user_detail_callback()
{
    global $wpdb;
    $start_date = sanitize_text_field($_POST['start_date']);
    $end_date = sanitize_text_field($_POST['end_date']);
    $user_id = sanitize_text_field($_POST['user_id']);
    $user_dept_obj = get_user_meta($user_id, 'department');
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
    $performance_query = 'SELECT * FROM ' . $performance_table_name . ' where user_id="' . $user_id . '" AND date BETWEEN "' . $start_date . '" AND "' . $end_date . '" AND is_submit = "1"';
    $performance_list = $wpdb->get_results($performance_query, ARRAY_A);
    if (!empty($performance_list)) {
        foreach ($performance_list as $performance_row) {
            if ($performance_row['project_id'] != 0) {
                $tr_project = $wpdb->prepare("SELECT * FROM $project_table_name WHERE id = %d", $performance_row['project_id']);
                $tr_project_results = $wpdb->get_results($tr_project);
                $tr_project = $tr_project_results[0]->project;
            } else {
                $tr_project = 'No Work';
            }
?>
            <tr data-user_id="<?php echo $user_id; ?>" data-date="<?php echo $start_date; ?>" data-id="<?php echo $performance_row['id']; ?>" class="edit author-self type-post status-publish format-standard hentry category-uncategorized">
                <td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
                    <strong><?php echo $tr_project; ?></strong>
                </td>
                <td class="author column-author" data-colname="Author">
                    <select class='data-field form-control' name='online_offline'>
                        <option value='online' <?php selected($performance_row['online_offline'], 'online'); ?>>Online</option>
                        <option value='offline' <?php selected($performance_row['online_offline'], 'offline'); ?>>Offline</option>
                    </select>
                </td>
                <?php
                $hs_cal = floatval($performance_row['number_of_hours']);
                $hs_hour = floor($hs_cal);
                $hs_min = round(($hs_cal - $hs_hour) * 60);
                ?>
                <td class="author column-author" data-colname="Author">
                    <div class="hs_custom_time">
                        <select name="hs_hour" class="form-control  edit-field" data-field='hs_number_of_hours' data-id="hs_hour">
                            <option <?php echo ($hs_hour == 0) ? 'selected' : ''; ?> value="0">0</option>
                            <option <?php echo ($hs_hour == 1) ? 'selected' : ''; ?> value="1">1</option>
                            <option <?php echo ($hs_hour == 2) ? 'selected' : ''; ?> value="2">2</option>
                            <option <?php echo ($hs_hour == 3) ? 'selected' : ''; ?> value="3">3</option>
                            <option <?php echo ($hs_hour == 4) ? 'selected' : ''; ?> value="4">4</option>
                            <option <?php echo ($hs_hour == 5) ? 'selected' : ''; ?> value="5">5</option>
                            <option <?php echo ($hs_hour == 6) ? 'selected' : ''; ?> value="6">6</option>
                            <option <?php echo ($hs_hour == 7) ? 'selected' : ''; ?> value="7">7</option>
                            <option <?php echo ($hs_hour == 8) ? 'selected' : ''; ?> value="8">8</option>
                        </select>
                        <input type="hidden" name="number_of_hours" class="edit_h number_of_hour" data-hs_field='number_of_hours' value='<?php echo $performance_row['number_of_hours']; ?>' disabled>
                    </div>
                </td>
                <td class="author column-author" data-colname="Author">
                    <div class="hs_custom_time">
                        <select name="hs_min" class="form-control  edit-field" data-field='number_of_min' data-id="hs_min">
                            <option <?php echo ($hs_min == 0) ? 'selected' : ''; ?> value="0" selected>00</option>
                            <option <?php echo ($hs_min == 10) ? 'selected' : ''; ?> value="10">10</option>
                            <option <?php echo ($hs_min == 20) ? 'selected' : ''; ?> value="20">20</option>
                            <option <?php echo ($hs_min == 30) ? 'selected' : ''; ?> value="30">30</option>
                            <option <?php echo ($hs_min == 40) ? 'selected' : ''; ?> value="40">40</option>
                            <option <?php echo ($hs_min == 50) ? 'selected' : ''; ?> value="50">50</option>
                        </select>
                    </div>
                </td>
                <td class="author column-author" data-colname="Author">
                    <select class='data-field form-control' name='billing_status'>
                        <option value='billable' <?php selected($performance_row['billing_status'], 'billable'); ?>>Billable</option>
                        <option value='non-billable' <?php selected($performance_row['billing_status'], 'non-billable'); ?>>Non Billable</option>
                        <option value='no-work' <?php selected($performance_row['billing_status'], 'no-work'); ?>>No Work</option>
                    </select>
                </td>
                <td class="author column-author" data-colname="Author">
                    <span class='data-field' data-field='profile_name'>
                        <?php
                        $profile_names = $wpdb->get_results($wpdb->prepare("SELECT * FROM $profile_table_name "), ARRAY_A);
                        ?>
                        <select name="profile_name">
                            <?php
                            foreach ($profile_names as $profile_name) {
                                echo '<option value="' . $profile_name['id'] . '" ' . selected($performance_row['profile_name'], $profile_name['id']) . '>' . $profile_name['profile_name'] . '</option>';
                            }
                            ?>
                        </select>
                    </span>
                </td>
                <td class="author column-author" data-colname="Author">
                    <span class='data-field' data-field='notes'>
                        <?php
                        if ($performance_row['is_approve'] == 0) {
                            echo 'Record not approve yet!';
                        } elseif ($performance_row['is_approve'] == 2) {
                            echo 'Record rejected by team lead.';
                        } else {
                            echo $performance_row['notes'];
                        }
                        ?>
                    </span>
                </td>
                <td class="author column-author" data-colname="Author">
                    <span class='data-field' data-field='reviewed_by'>
                        <?php if ($performance_row['is_approve'] != 0) {
                            echo get_userdata($performance_row['reviewed_by'])->display_name;
                        } ?>
                    </span>
                </td>
            </tr>
        <?php
        }
        ?>
        <tr>
            <td colspan="8" style="text-align: end;"><a href="javascript:void(0)" class="page-title-action update_user_list" style="width: 300px;text-align:center;">Update</a></td>
        </tr>
        <?php
    } else {
        echo '<tr class="no-items">
            <td class="colspanchange" colspan="9">No rows found.</td>
            </tr>';
    }
    wp_die();
}

// Performance Get User Profile data
add_action('wp_ajax_admin_get_profile_detail', 'admin_get_profile_detail_callback');
function admin_get_profile_detail_callback()
{
    global $wpdb;
    $start_date = sanitize_text_field($_POST['start_date']);
    $end_date = sanitize_text_field($_POST['end_date']);
    $profile_id = sanitize_text_field($_POST['profile_id']);
    $performance_table_name = sanitize_text_field($_POST['table_name']);
    $project_table_name = $wpdb->prefix . 'projects';
    $profile_table_name = $wpdb->prefix . 'profile_name';
    $performance_query = 'SELECT * FROM ' . $performance_table_name . ' where profile_name="' . $profile_id . '" AND date BETWEEN "' . $start_date . '" AND "' . $end_date . '" AND is_submit = "1"';
    echo $performance_query;
    $performance_list = $wpdb->get_results($performance_query, ARRAY_A);
    if (!empty($performance_list)) {
        foreach ($performance_list as $performance_row) {
            if ($performance_row['project_id'] != 0) {
                $tr_project = $wpdb->prepare("SELECT * FROM $project_table_name WHERE id = %d", $performance_row['project_id']);
                $tr_project_results = $wpdb->get_results($tr_project);
                $tr_project = $tr_project_results[0]->project;
            } else {
                $tr_project = 'No Work';
            }
        ?>
            <tr data-user_id="<?php echo $performance_row['user_id']; ?>" data-date="<?php echo $start_date; ?>" data-id="<?php echo $performance_row['id']; ?>" class="edit author-self type-post status-publish format-standard hentry category-uncategorized">
                <td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
                    <strong>
                        <?php echo get_userdata($performance_row['user_id'])->display_name;
                        ?>
                    </strong>
                </td>
                <td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
                    <?php echo $tr_project; ?>
                </td>
                <td class="author column-author" data-colname="Author">
                    <select class='data-field form-control' name='online_offline'>
                        <option value='online' <?php selected($performance_row['online_offline'], 'online'); ?>>Online</option>
                        <option value='offline' <?php selected($performance_row['online_offline'], 'offline'); ?>>Offline</option>
                    </select>
                </td>
                <?php
                $hs_cal = floatval($performance_row['number_of_hours']);
                $hs_hour = floor($hs_cal);
                $hs_min = round(($hs_cal - $hs_hour) * 60);
                ?>
                <td class="author column-author" data-colname="Author">
                    <div class="hs_custom_time">
                        <select name="hs_hour" class="form-control  edit-field" data-field='hs_number_of_hours' data-id="hs_hour">
                            <option <?php echo ($hs_hour == 0) ? 'selected' : ''; ?> value="0">0</option>
                            <option <?php echo ($hs_hour == 1) ? 'selected' : ''; ?> value="1">1</option>
                            <option <?php echo ($hs_hour == 2) ? 'selected' : ''; ?> value="2">2</option>
                            <option <?php echo ($hs_hour == 3) ? 'selected' : ''; ?> value="3">3</option>
                            <option <?php echo ($hs_hour == 4) ? 'selected' : ''; ?> value="4">4</option>
                            <option <?php echo ($hs_hour == 5) ? 'selected' : ''; ?> value="5">5</option>
                            <option <?php echo ($hs_hour == 6) ? 'selected' : ''; ?> value="6">6</option>
                            <option <?php echo ($hs_hour == 7) ? 'selected' : ''; ?> value="7">7</option>
                            <option <?php echo ($hs_hour == 8) ? 'selected' : ''; ?> value="8">8</option>
                        </select>
                        <input type="hidden" name="number_of_hours" class="edit_h number_of_hour" data-hs_field='number_of_hours' value='<?php echo $performance_row['number_of_hours']; ?>' disabled>
                    </div>
                </td>
                <td class="author column-author" data-colname="Author">
                    <div class="hs_custom_time">
                        <select name="hs_min" class="form-control  edit-field" data-field='number_of_min' data-id="hs_min">
                            <option <?php echo ($hs_min == 0) ? 'selected' : ''; ?> value="0" selected>00</option>
                            <option <?php echo ($hs_min == 10) ? 'selected' : ''; ?> value="10">10</option>
                            <option <?php echo ($hs_min == 20) ? 'selected' : ''; ?> value="20">20</option>
                            <option <?php echo ($hs_min == 30) ? 'selected' : ''; ?> value="30">30</option>
                            <option <?php echo ($hs_min == 40) ? 'selected' : ''; ?> value="40">40</option>
                            <option <?php echo ($hs_min == 50) ? 'selected' : ''; ?> value="50">50</option>
                        </select>
                    </div>
                </td>
                <td class="author column-author" data-colname="Author">
                    <select class='data-field form-control' name='billing_status'>
                        <option value='billable' <?php selected($performance_row['billing_status'], 'billable'); ?>>Billable</option>
                        <option value='non-billable' <?php selected($performance_row['billing_status'], 'non-billable'); ?>>Non Billable</option>
                        <option value='no-work' <?php selected($performance_row['billing_status'], 'no-work'); ?>>No Work</option>
                    </select>
                </td>
                <td class="author column-author" data-colname="Author">
                    <span class='data-field' data-field='profile_name'>
                        <?php
                        $profile_names = $wpdb->get_results($wpdb->prepare("SELECT * FROM $profile_table_name "), ARRAY_A);
                        ?>
                        <select name="profile_name">
                            <?php
                            foreach ($profile_names as $profile_name) {
                                echo '<option value="' . $profile_name['id'] . '" ' . selected($performance_row['profile_name'], $profile_name['id']) . '>' . $profile_name['profile_name'] . '</option>';
                            }
                            ?>
                        </select>
                    </span>
                </td>
                <td class="author column-author" data-colname="Author">
                    <span class='data-field' data-field='notes'>
                        <?php
                        if ($performance_row['is_approve'] == 0) {
                            echo 'Record not approve yet!';
                        } elseif ($performance_row['is_approve'] == 2) {
                            echo 'Record rejected by team lead.';
                        } else {
                            echo $performance_row['notes'];
                        }
                        ?>
                    </span>
                </td>
                <td class="author column-author" data-colname="Author">
                    <span class='data-field' data-field='reviewed_by'>
                        <?php if ($performance_row['is_approve'] != 0) {
                            echo get_userdata($performance_row['reviewed_by'])->display_name;
                        } ?>
                    </span>
                </td>
            </tr>
        <?php
        }
        ?>
        <tr>
            <td colspan="9" style="text-align: end;"><a href="javascript:void(0)" class="page-title-action update_profile_list" style="width: 300px;text-align:center;">Update</a></td>
        </tr>
<?php
    } else {
        echo '<tr class="no-items">
            <td class="colspanchange" colspan="9">No rows found.</td>
            </tr>';
    }
    wp_die();
}
