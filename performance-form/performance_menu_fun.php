<?php
// Performance all list Data View
function display_all_performance()
{
    global $wpdb;
    $project_table_name = $wpdb->prefix . 'projects';
    $profile_table_name = $wpdb->prefix . 'profile_name';
    $tab = isset($_GET['tab']) ? $_GET['tab'] : null;
    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
    $project_id = isset($_GET['project_id']) ? $_GET['project_id'] : null;
    $billing_status = isset($_GET['billing_status']) ? $_GET['billing_status'] : null;
    $profile_id = isset($_GET['profile_id']) ? $_GET['profile_id'] : null;
    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
    $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : null;
?>
    <div class="wrap">
        <h1 class="wp-heading-inline">All Performance List</h1>
        <nav class="nav-tab-wrapper">
            <a href="?page=all_performance" class="nav-tab <?php if ($tab === null) : ?>nav-tab-active<?php endif; ?>">Dev Department</a>
            <a href="?page=all_performance&tab=marketing_department" class="nav-tab <?php if ($tab === 'marketing_department') : ?>nav-tab-active<?php endif; ?>">Marketing Department</a>
            <a href="?page=all_performance&tab=sales_department" class="nav-tab <?php if ($tab === 'sales_department') : ?>nav-tab-active<?php endif; ?>">Sales Department</a>
        </nav>
        <?php
        if ($tab == 'marketing_department') {
            $meta_key = 'department';
            $meta_value = '2';
            $performance_table_name = $wpdb->prefix . 'marketing_performance_data';
        } elseif ($tab == 'sales_department') {
            $meta_key = 'department';
            $meta_value = '3';
            $performance_table_name = $wpdb->prefix . 'sales_performance_data';
        } else {
            $meta_key = 'department';
            $meta_value = '1';
            $performance_table_name = $wpdb->prefix . 'dev_performance_data';
        }
        $project_data_results = $wpdb->get_results("SELECT * FROM $project_table_name  where `depatment_id` = '$meta_value' ", ARRAY_A);
        $profile_data_results = $wpdb->get_results("SELECT * FROM $profile_table_name ", ARRAY_A);
        $users = get_users(array(
            'meta_key'     => $meta_key,
            'meta_value'   => $meta_value,
            'meta_compare' => '=',
        ));
        $performance_query = 'SELECT * FROM ' . $performance_table_name . ' ';
        if ($user_id) {
            $performance_query .= ' where user_id="' . $user_id . '"';
        }
        if ($project_id) {
            if ($user_id) {
                $performance_query .= ' AND project_id="' . $project_id . '"';
            } else {
                $performance_query .= ' where project_id="' . $project_id . '"';
            }
        }
        if ($profile_id) {
            if ($user_id || $project_id) {
                $performance_query .= ' AND profile_name="' . $profile_id . '"';
            } else {
                $performance_query .= ' where profile_name="' . $profile_id . '"';
            }
        }
        if ($billing_status) {
            if ($user_id || $project_id || $profile_id) {
                $performance_query .= ' AND billing_status="' . $billing_status . '"';
            } else {
                $performance_query .= ' where billing_status="' . $billing_status . '"';
            }
        }
        if ($start_date && $end_date) {
            if ($user_id || $project_id || $profile_id || $billing_status) {
                $performance_query .= " AND date BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            } else {
                $performance_query .= " where date BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            }
        } else {
            if ($start_date) {
                if ($user_id || $project_id || $profile_id) {
                    $performance_query .= ' AND date >="' . $start_date . '"';
                } else {
                    $performance_query .= ' where date >="' . $start_date . '"';
                }
            }
            if ($end_date) {
                if ($user_id || $project_id || $profile_id || $start_date) {
                    $performance_query .= ' AND date <= "' . $end_date . '"';
                } else {
                    $performance_query .= ' where date <= "' . $end_date . '"';
                }
            }
        }
        $all_performance = $wpdb->get_results($performance_query, ARRAY_A);
        ?>
        <div class="tab-content">
            <!-- <a href="<?php echo admin_url('admin.php?page=add_performance'); ?>" class="page-title-action">Add New Performance</a> -->
            <hr class="wp-header-end">
            <ul class="sub">
                <li class="all">
                    <a href="<?php echo admin_url('admin.php?page=all_performance'); ?>" class="current" aria-current="page">All <span class="count"> (<?php echo count($all_performance); ?>)</span>
                    </a>
                </li>
            </ul>
            <form id="rows-filter" method="get">
                <div class="tablenav top">
                    <input type="hidden" name="page" value="all_performance">
                    <?php
                    if ($tab) {
                    ?>
                        <input type="hidden" name="tab" value="<?php echo $tab; ?>">
                    <?php
                    }
                    ?>
                    <select name="user_id">
                        <option value="">Select User</option>
                        <?php
                        foreach ($users as $user) {
                        ?>
                            <option value="<?php echo  $user->ID ?>" <?php echo ($user->ID == $user_id) ? 'selected' : ""; ?>><?php echo  get_userdata($user->ID)->display_name; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <select name=" project_id">
                        <option value="">Select Project</option>
                        <?php
                        foreach ($project_data_results as $project) {
                        ?>
                            <option value="<?php echo  $project['id']; ?>" <?php echo ($project['id'] == $project_id) ? 'selected' : ""; ?>><?php echo  $project['project']; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <select name="profile_id">
                        <option value="">Select Profile</option>
                        <?php
                        foreach ($profile_data_results as $profile) {
                        ?>
                            <option value="<?php echo  $profile['id']; ?>" <?php echo ($profile['id'] == $profile_id) ? 'selected' : ""; ?>><?php echo  $profile['profile_name']; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <select name="billing_status">
                        <option value="">Select Billing Status</option>
                        <option value="billable" <?php echo ($billing_status == "billable") ? 'selected' : ''; ?>>Billable</option>
                        <option value="non-billable" <?php echo ($billing_status == "non-billable") ? 'selected' : ''; ?>>Non Billable</option>
                        <option value="no-work" <?php echo ($billing_status == "no-work") ? 'selected' : ''; ?>>No Work</option>
                    </select>
                    Start Date
                    <input type="date" name="start_date" value="<?php echo $start_date; ?>">
                    End Date
                    <input type="date" name="end_date" value="<?php echo $end_date; ?>">
                    <button class="button">Filter</button>
                </div>
            </form>
            <h2 class="screen-reader-text">All Performance list</h2>
            <table class="wp-list-table widefat fixed striped table-view-list rows">
                <thead>
                    <tr>
                        <th scope="col" id="author" class="manage-column column-author">User</th>
                        <th scope="col" id="author" class="manage-column column-author">Date</th>
                        <th scope="col" id="author" class="manage-column column-author">Project Name</th>
                        <th scope="col" id="author" class="manage-column column-author">Online/Offline</th>
                        <th scope="col" id="author" class="manage-column column-author">Number of Hours</th>
                        <th scope="col" id="author" class="manage-column column-author">Billing Status</th>
                        <th scope="col" id="author" class="manage-column column-author">Profile Name</th>
                        <th scope="col" id="author" class="manage-column column-author">Notes</th>
                        <th scope="col" id="author" class="manage-column column-author">Reviewed By</th>
                    </tr>
                </thead>
                <tbody id="the-list">
                    <?php
                    $all_t = 0;
                    if (!empty($all_performance)) {
                        foreach ($all_performance as $performance_row) {
                            if ($performance_row['project_id'] != 0) {
                                $tr_project = $wpdb->prepare("SELECT * FROM $project_table_name WHERE id = %d", $performance_row['project_id']);
                                $tr_project_results = $wpdb->get_results($tr_project);
                                $tr_project = $tr_project_results[0]->project;
                            } else {
                                $tr_project = 'No Work';
                            }
                    ?>
                            <tr class="edit author-self  type-post status-publish format-standard hentry category-uncategorized">
                                <td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
                                    <strong>
                                        <?php echo get_userdata($performance_row['user_id'])->user_login; ?>
                                    </strong>
                                    <div class="row-actions">
                                        <span class="hs_edid edit">
                                            <a href="javascript:void(0);" data-user_id="<?php echo $performance_row['user_id']; ?>" data-id="<?php echo $performance_row['id']; ?>">Edit</a>
                                        </span>
                                        <!-- <span class="edit">
                                                <a href="<?php echo admin_url('admin.php?page=add_performance&edit_id=' . $performance_row['id'] . '');
                                                            ?>" aria-label="Edit ">Edit</a> | </span>
                                            <span class="trash">
                                                <a href="<?php echo admin_url('admin.php?page=add_performance&delete_id=' . $performance_row['id'] . '');
                                                            ?>" class="submitdelete" aria-label="Move to the Trash">Delete</a> </span> -->
                                    </div>
                                </td>
                                <td class="author column-author" data-colname="Author">
                                    <span class=' data-field' data-field='date'>
                                        <?php echo $performance_row['date']; ?>
                                    </span>
                                </td>
                                <td class="author column-author" data-colname="Author">
                                    <span class='data-field' data-field='project_name'>
                                        <?php echo $tr_project; ?>
                                    </span>
                                </td>
                                <td class="author column-author" data-colname="Author">
                                    <span class='data-field' data-field='online_offline'>
                                        <?php echo $performance_row['online_offline']; ?>
                                    </span>
                                </td>
                                <td class="author column-author" data-colname="Author">
                                    <span class='data-field' data-field='number_of_hours'>
                                        <?php echo $performance_row['number_of_hours'];
                                        $all_t += $performance_row['number_of_hours']; ?>
                                    </span>
                                </td>
                                <td class="author column-author" data-colname="Author">
                                    <span class='data-field' data-field='billing_status'>
                                        <?php echo $performance_row['billing_status']; ?>
                                    </span>
                                    <select class='data-field form-control' name='billing_status' style="display: none;">
                                        <option value='billable' <?php selected($performance_row['billing_status'], 'billable'); ?>>Billable</option>
                                        <option value='non-billable' <?php selected($performance_row['billing_status'], 'non-billable'); ?>>Non Billable</option>
                                        <option value='no-work' <?php selected($performance_row['billing_status'], 'no-work'); ?>>No Work</option>
                                    </select>
                                </td>
                                <td class="author column-author" data-colname="Author">
                                    <span class='data-field' data-field='profile_name'>
                                        <?php
                                        $profile_name = $wpdb->get_row($wpdb->prepare("SELECT profile_name FROM $profile_table_name WHERE id = %d", $performance_row['profile_name']), ARRAY_A);
                                        if ($profile_name) {
                                            echo $profile_name['profile_name'];
                                        } ?>
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
                                    <span class='data-field' data-field='reviewed_by'> <?php if ($performance_row['is_approve'] != 0) {
                                                                                            echo get_userdata($performance_row['reviewed_by'])->display_name;
                                                                                        } ?>
                                    </span>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    <?php
                    } else {
                        echo '<tr class="no-items">
                            <td class="colspanchange" colspan="9">No rows found.</td>
                            </tr>';
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th scope="col" id="author" class="manage-column column-author">User</th>
                        <th scope="col" id="author" class="manage-column column-author">Date</th>
                        <th scope="col" id="author" class="manage-column column-author">Project Name</th>
                        <th scope="col" id="author" class="manage-column column-author">Online/Offline</th>
                        <th scope="col" id="author" class="manage-column column-author">Total :- <?php echo $all_t; ?></th>
                        <th scope="col" id="author" class="manage-column column-author">Billing Status</th>
                        <th scope="col" id="author" class="manage-column column-author">Profile Name</th>
                        <th scope="col" id="author" class="manage-column column-author">Notes</th>
                        <th scope="col" id="author" class="manage-column column-author">Reviewed By</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <script>
            jQuery(document).ready(function($) {
                $(".hs_edid a").click(function() {
                    $("span[data-field='billing_status']").show();
                    $("tr select[name='billing_status']").hide();
                    $(this).closest('tr').find("select[name='billing_status']").show();
                    $(this).closest('tr').find("span[data-field='billing_status']").hide();
                });
                $('select[name="billing_status"]').change(function() {
                    let id = $(this).closest('tr').find(".hs_edid a").data('id');
                    let user_id = $(this).closest('tr').find(".hs_edid a").data('user_id');
                    let value = $(this).val();
                    let span = $(this).closest('tr').find("span[data-field='billing_status']");
                    jQuery.ajax({
                        url: '<?php echo admin_url('admin-ajax.php'); ?>',
                        type: 'POST',
                        data: {
                            action: 'admin_billing_status',
                            entry_id: id,
                            user_id: user_id,
                            value: value
                        },
                        success: function(response) {
                            span.html(value);
                            $("span[data-field='billing_status']").show();
                            $("tr select[name='billing_status']").hide();
                        },
                    });
                });
            });
        </script>
    </div>
<?php
}

// Performance Form View
function display_performance_form()
{
    global $wpdb;
    $performance_table_name = $wpdb->prefix . 'performance_data';
    $delete_id = isset($_GET['delete_id']) ? (int)$_GET['delete_id'] : 0;
    $project_table_name = $wpdb->prefix . 'projects';
    $project_sql_select = "SELECT * FROM $project_table_name  ";
    $project_data_results = $wpdb->get_results($project_sql_select, ARRAY_A);
    if ($delete_id > 0) {
        performance_delete_performance($delete_id);
        echo '<div class="notice notice-success is-dismissible">
        <p>Performance deleted successfully!</p>
        </div>';
    }
    $edit_id = isset($_GET['edit_id']) ? (int)$_GET['edit_id'] : 0;
    $performance_row = array('performance' => '', 'performance_type' => '');
    if ($edit_id > 0) {
        $performance_row = get_performance_data($edit_id);
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_performance'])) {
        $date = isset($_POST['date']) ? sanitize_text_field($_POST['date']) : '';
        $user_id = isset($_POST['user_id']) ? sanitize_text_field($_POST['user_id']) : '';
        $project_name = isset($_POST['project_name']) ? sanitize_text_field($_POST['project_name']) : '';
        $online_offline = isset($_POST['online_offline']) ? sanitize_text_field($_POST['online_offline']) : '';
        $number_of_hours = isset($_POST['number_of_hours']) ? sanitize_text_field($_POST['number_of_hours']) : '';
        $billing_status = isset($_POST['billing_status']) ? sanitize_text_field($_POST['billing_status']) : '';
        $profile_name = isset($_POST['profile_name']) ? sanitize_text_field($_POST['profile_name']) : '';
        $notes = isset($_POST['notes']) ? sanitize_textarea_field($_POST['notes']) : '';
        $reviewed_by = isset($_POST['reviewed_by']) ? sanitize_text_field($_POST['reviewed_by']) : '';
        $is_submit = isset($_POST['is_submit']) ? sanitize_text_field($_POST['is_submit']) : '';
        if (!empty($date) && !empty($user_id)) {
            if ($edit_id > 0) {
                $wpdb->update(
                    $performance_table_name,
                    array(
                        'date' => $date,
                        'user_id' => $user_id,
                        'project_id' => $project_name,
                        'online_offline' => $online_offline,
                        'number_of_hours' => $number_of_hours,
                        'billing_status' => $billing_status,
                        'profile_name' => $profile_name,
                        'notes' => $notes,
                        'reviewed_by' => $reviewed_by,
                        'is_submit' => $is_submit,
                    ),
                    array('id' => $edit_id)
                );
                if ($wpdb->last_error) {
                    wp_die('Database error: ' . $wpdb->last_error);
                } else {
                    echo '<div class="notice notice-success is-dismissible">
                    <p>Project updated successfully!</p>
                    </div>';
                }
            } else {
                $wpdb->insert(
                    $performance_table_name,
                    array(
                        'date' => $date,
                        'user_id' => $user_id,
                        'project_id' => $project_name,
                        'online_offline' => $online_offline,
                        'number_of_hours' => $number_of_hours,
                        'billing_status' => $billing_status,
                        'profile_name' => $profile_name,
                        'notes' => $notes,
                        'reviewed_by' => $reviewed_by,
                    )
                );
                if ($wpdb->last_error) {
                    wp_die('Database error: ' . $wpdb->last_error);
                } else {
                    echo '<div class="notice notice-success is-dismissible">
                    <p>Project added successfully!</p>
                    </div>';
                }
            }
        } else {
            echo '<div class="notice notice-error is-dismissible">
            <p>Please fill in all fields!</p>
            </div>';
        }
    }
    $Users = get_users();
    echo '
    <form method="post">';
    if ($edit_id > 0) {
        echo '<input type="hidden" name="edit_id" value="' . $edit_id . '">';
    }
?>
    <div class="wrap">
        <style>
            input,
            textarea,
            select {
                max-width: 100%;
                width: 100%;
            }
        </style>
        <h1 class="wp-heading-inline">Add/Edit Performance</h1>
        <a href="<?php echo admin_url('admin.php?page=all_performance'); ?>" class="page-title-action">All Performance List</a>
        <hr class="wp-header-end">
        <h2 class="screen-reader-text">All performance list</h2>
        <table class="wp-list-table widefat fixed striped table-view-list rows">
            <thead>
                <tr>
                    <th scope="col" id="author" class="manage-column column-author">User</th>
                    <th scope="col" id="author" class="manage-column column-author">Date</th>
                    <th scope="col" id="author" class="manage-column column-author">Project Name</th>
                    <th scope="col" id="author" class="manage-column column-author">Online/Offline</th>
                    <th scope="col" id="author" class="manage-column column-author">Number of Hours</th>
                    <th scope="col" id="author" class="manage-column column-author">Billing Status</th>
                    <th scope="col" id="author" class="manage-column column-author">Profile Name</th>
                    <th scope="col" id="author" class="manage-column column-author">Notes</th>
                    <th scope="col" id="author" class="manage-column column-author">Reviewed By</th>
                    <th scope="col" id="author" class="manage-column column-author">Is Submit</th>
                    <th scope="col" id="author" class="manage-column column-author">Action</th>
                </tr>
            </thead>
            <tbody id="the-list">
                <tr class="edit author-self  type-post status-publish format-standard hentry category-uncategorized">
                    <td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
                        <select name="user_id" required>
                            <?php foreach ($Users as $user) {
                            ?>
                                <option value='<?php echo $user->ID; ?>' <?php $edit_id > 0 ? selected($performance_row['user_id'], $user->ID) : '' ?>>
                                    <?php echo $user->user_login; ?>
                                </option>
                            <?php
                            }
                            ?>
                        </select>
                    </td>
                    <td class="author column-author" data-colname="Author">
                        <input type="date" name="date" required value="<?php echo ($edit_id > 0 ?  esc_attr($performance_row['date']) : ''); ?>">
                    </td>
                    <td class="author column-author" data-colname="Author">
                        <select name="project_name" required>
                            <?php foreach ($project_data_results as $project_row) {
                            ?>
                                <option value='<?php echo $project_row['id']; ?>' <?php $edit_id > 0 ? selected($performance_row['project_id'], $project_row['id']) : '' ?>>
                                    <?php echo $project_row['project']; ?>
                                </option>
                            <?php
                            }
                            ?>
                        </select>
                    </td>
                    <td class="author column-author" data-colname="Author">
                        <select class='add-field form-control' name='online_offline' required>
                            <option value='Online' <?php $edit_id > 0 ? selected($performance_row['online_offline'], 'Online') : ''; ?>>Online</option>
                            <option value='offline' <?php $edit_id > 0 ? selected($performance_row['online_offline'], 'Offline') : ''; ?>>Offline</option>
                        </select>
                    </td>
                    <td class="author column-author" data-colname="Author">
                        <input type='number' class='add-field form-control' name='number_of_hours' step='0.01' required value="<?php echo ($edit_id > 0 ?  esc_attr($performance_row['number_of_hours']) : ''); ?>">
                    </td>
                    <td class="author column-author" data-colname="Author">
                        <select class='add-field form-control' name='billing_status' required>
                            <option value='billable' <?php $edit_id > 0 ? selected($performance_row['billing_status'], 'billable') : ''; ?>>Billable</option>
                            <option value='non-billable' <?php $edit_id > 0 ? selected($performance_row['billing_status'], 'non-billable') : ''; ?>>Non Billable</option>
                            <option value='no-work' <?php echo ($performance_row['billing_status'] == 'no-work') ? 'selected' : ''; ?>>No Work</option>
                        </select>
                    </td>
                    <td class="author column-author" data-colname="Author">
                        <input type='text' class='add-field form-control' name='profile_name' value="<?php echo ($edit_id > 0 ?  esc_attr($performance_row['profile_name']) : ''); ?>">
                    </td>
                    <td class="author column-author" data-colname="Author">
                        <textarea class='add-field form-control' name='notes'>
                            <?php echo ($edit_id > 0 ?  esc_attr($performance_row['notes']) : ''); ?>
                    </textarea>
                    </td>
                    <td class="author column-author" data-colname="Author">
                        <input type='text' class='add-field form-control' name='reviewed_by' required value="<?php echo ($edit_id > 0 ?  esc_attr($performance_row['reviewed_by']) : ''); ?>">
                    </td>
                    <td class="author column-author" data-colname="Author">
                        <input type='text' class='add-field form-control' name='is_submit' required value="<?php echo ($edit_id > 0 ?  esc_attr($performance_row['is_submit']) : ''); ?>">
                    </td>
                    <td class="date column-date" data-colname="Date">
                        <input type="submit" name="submit_performance" class="button button-primary" value="<?php echo ($edit_id > 0 ? 'Update' : 'Add'); ?>">
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th scope="col" id="author" class="manage-column column-author">User</th>
                    <th scope="col" id="author" class="manage-column column-author">Date</th>
                    <th scope="col" id="author" class="manage-column column-author">Project Name</th>
                    <th scope="col" id="author" class="manage-column column-author">Online/Offline</th>
                    <th scope="col" id="author" class="manage-column column-author">Number of Hours</th>
                    <th scope="col" id="author" class="manage-column column-author">Billing Status</th>
                    <th scope="col" id="author" class="manage-column column-author">Profile Name</th>
                    <th scope="col" id="author" class="manage-column column-author">Notes</th>
                    <th scope="col" id="author" class="manage-column column-author">Reviewed By</th>
                    <th scope="col" id="author" class="manage-column column-author">Is Submit</th>
                    <th scope="col" id="author" class="manage-column column-author">Action</th>
                </tr>
            </tfoot>
        </table>
    </div>
<?php
    echo '</form>';
}
function get_performance_data($id)
{
    global $wpdb;
    $performance_table_name = $wpdb->prefix . 'performance_data';
    return $wpdb->get_row($wpdb->prepare("SELECT * FROM $performance_table_name WHERE id = %d", $id), ARRAY_A);
}
function performance_delete_performance($id)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'performance_data';
    $wpdb->delete($table_name, array('id' => $id));
    if ($wpdb->last_error) {
        wp_die('Database error: ' . $wpdb->last_error);
    }
}
