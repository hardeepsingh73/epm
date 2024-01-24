<?php
// display form
function display_project_form()
{
    global $wpdb;
    $delete_id = isset($_GET['delete_id']) ? (int) $_GET['delete_id'] : 0;
    if ($delete_id > 0) {
        project_delete_project($delete_id);
        echo '<div class="notice notice-success is-dismissible">
        <p>Project deleted successfully!</p>
        </div>';
    }
    $edit_id = isset($_GET['edit_id']) ? (int) $_GET['edit_id'] : 0;
    $project_data = array('project' => '', 'depatment_id' => '');
    if ($edit_id > 0) {
        $project_data = get_project_data($edit_id);
    }
    echo '<div class="notice notice-error is-dismissible hs_err" style="display:none;">
        <p>Please fill in all fields!</p>
        </div>    <form method="post" class="hs_form">';
    if ($edit_id > 0) {
        echo '<input type="hidden" name="edit_id" value="' . $edit_id . '">';
    }
    ?>
    <div class="wrap">
        <link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__); ?>assets/jquery-ui.css">
        <script src="<?php echo plugin_dir_url(__FILE__); ?>assets/select2.js">
        </script>
        <style>
            input,
            textarea,
            select {
                width: 100%;
            }
        </style>
        <link href="<?php echo plugin_dir_url(__FILE__); ?>assets/select2.css" rel="stylesheet" />
        <h1 class="wp-heading-inline">Add/Edit Project</h1>
        <a href="<?php echo admin_url('admin.php?page=project_list'); ?>" class="page-title-action">Project List</a>
        <hr class="wp-header-end">
        <h2 class="screen-reader-text">project list</h2>
        <table class="wp-list-table widefat fixed striped table-view-list posts">
            <thead>
                <tr>
                    <th scope="col" id="author" class="manage-column column-author">Project Name</th>
                    <th scope="col" id="author" class="manage-column column-author">Depatment</th>
                    <th scope="col" id="author" class="manage-column column-author">Team Lead</th>
                    <th scope="col" id="author" class="manage-column column-author">Project Profile</th>
                    <th scope="col" id="author" class="manage-column column-author">Allocated Hours</th>
                    <th scope="col" id="author" class="manage-column column-author">Action</th>
                </tr>
            </thead>
            <tbody id="the-list">
                <tr id="post-1"
                    class="iedit author-self level-0 post-1 type-post status-publish format-standard hentry category-uncategorized">
                    <td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
                        <input type="text" name="name" value="<?php echo esc_attr($project_data['project']); ?>" required>
                    </td>
                    <td class="author column-author" data-colname="Author">
                        <select name="depatment_id" required>
                            <?php
                            $depatment_name_table_name = $wpdb->prefix . 'depatment_name';
                            $departments = $wpdb->get_results(
                                "SELECT * FROM $depatment_name_table_name"
                            );
                            foreach ($departments as $department) {
                                ?>
                                <option value="<?php echo $department->id; ?>" <?php selected($project_data['depatment_id'], $department->id); ?>>
                                    <?php echo $department->depatment_name; ?>
                                </option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                    <td class="author column-author" data-colname="Author">
                        <select name="team_lead" required>
                            <?php
                            $args = array(
                                'role' => 'contributor',
                            );
                            $contributors = get_users($args);
                            foreach ($contributors as $contributor) {
                                ?>
                                <option value="<?php echo $contributor->data->ID; ?>" <?php selected($project_data['team_lead'], $contributor->data->ID); ?>>
                                    <?php echo $contributor->data->display_name; ?>
                                </option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                    <td class="author column-author" data-colname="Author">
                        <select name="profile_ids" required>
                            <?php
                            global $wpdb;
                            $profile_name_table_name = $wpdb->prefix . 'profile_name';
                            $profiles = $wpdb->get_results(
                                "SELECT * FROM $profile_name_table_name"
                            );
                            foreach ($profiles as $profile) {
                                ?>
                                <option value="<?php echo $profile->id; ?>" <?php selected($project_data['depatment_id'], $profile->id); ?>>
                                    <?php echo $profile->profile_name; ?>
                                </option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                    <td class="date column-date" data-colname="Date">
                        <input type="number" name="allocated_hours"
                            value="<?php echo esc_attr($project_data['allocated_hours']); ?>"><span id="errmsg"></span>
                    </td>
                    <td class="date column-date" data-colname="Date">
                        <input type="submit" name="submit_project" class="button button-primary"
                            value="<?php echo ($edit_id > 0 ? 'Update Project' : 'Add Project'); ?>">
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th scope="col" id="author" class="manage-column column-author">Project Name</th>
                    <th scope="col" id="author" class="manage-column column-author">Depatment</th>
                    <th scope="col" id="author" class="manage-column column-author">Team Lead</th>
                    <th scope="col" id="author" class="manage-column column-author">Project Profile</th>
                    <th scope="col" id="author" class="manage-column column-author">Allocated Hours</th>
                    <th scope="col" id="author" class="manage-column column-author">Action</th>
                </tr>
            </tfoot>
        </table>
    </div>
    </form>
    <?php
    if ($edit_id > 0) {
        $profile_name_table_name = $wpdb->prefix . 'profile_name';
        $profile_names = $wpdb->get_results(
            "SELECT * FROM $profile_name_table_name WHERE id IN (" . $project_data['profile_ids'] . ")"
        );
    }
    ?>
    <script>
        jQuery(document).ready(function ($) {
            $('input[name="allocated_hours"]').keypress(function (e) {
                console.log('test');
                if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                    $("#errmsg").html("Digits Only").show().fadeOut("slow");
                    return false;
                }
            });
            var profile_name = jQuery('select[name="profile_ids"]').select2({
                multiple: true,
                placeholder: 'Select Profile Name',
                allowClear: false,
                tags: true
            });
            <?php
            if ($edit_id > 0) {
                ?>
                profile_name.val(<?php echo json_encode(wp_list_pluck($profile_names, 'id')); ?>).trigger('change');
                <?php
            }
            ?>
            jQuery('.hs_form').submit(function (e) {
                e.preventDefault();
                let id = '';
                let name = jQuery('input[name="name"]').val();
                let depatment_id = jQuery('select[name="depatment_id"]').val();
                let profile_ids = jQuery('select[name="profile_ids"]').val();
                let team_lead = jQuery('select[name="team_lead"]').val();
                let allocated_hours = jQuery('input[name="allocated_hours"]').val();
                if (name != '' && profile_ids.length > 0) {
                    <?php
                    if ($edit_id > 0) {
                        ?>
                        id = jQuery("input[name='edit_id']").val();
                        <?php
                    }
                    ?>
                    jQuery.ajax({
                        type: 'POST',
                        url: "<?php echo admin_url('admin-ajax.php'); ?>",
                        data: {
                            action: 'add_project',
                            id: id,
                            name: name,
                            team_lead: team_lead,
                            depatment_id: depatment_id,
                            profile_ids: profile_ids,
                            allocated_hours: allocated_hours,
                        },
                        success: function (response) {
                            if (response == 'inserted') {
                                window.location.replace('/wp-admin/admin.php?page=project_list');
                            }
                        }
                    });
                } else {
                    jQuery('.hs_err').show();
                }
            });
        });
    </script>
    <?php
}
// get single data
function get_project_data($id)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'projects';
    return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id), ARRAY_A);
}
// display list
function display_project_list()
{
    global $wpdb;
    // Fetch projects from the database
    $table_name = $wpdb->prefix . 'projects';
    $projects = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
    // Display your project list here
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">
            Project List</h1>
        <a href="<?php echo admin_url('admin.php?page=add_project'); ?>" class="page-title-action">Add New Project</a>
        <hr class="wp-header-end">
        <ul class="subsubsub">
            <li class="all">
                <a href="<?php echo admin_url('admin.php?page=project_list'); ?>" class="current" aria-current="page">All
                    <span class="count"> (
                        <?php echo count($projects); ?>)
                    </span>
                </a>
            </li>
        </ul>
        <form id="posts-filter" method="get">
            <h2 class="screen-reader-text">project list</h2>
            <table class="wp-list-table widefat fixed striped table-view-list posts">
                <thead>
                    <tr>
                        <th scope="col" id="author" class="manage-column column-author">Project Name</th>
                        <th scope="col" id="author" class="manage-column column-author">Depatment</th>
                        <th scope="col" id="author" class="manage-column column-author">Team Lead</th>
                        <th scope="col" id="author" class="manage-column column-author">Project Profile</th>
                        <th scope="col" id="author" class="manage-column column-author">Allocated Hours</th>
                        <th scope="col" id="author" class="manage-column column-author">Created At</th>
                    </tr>
                </thead>
                <tbody id="the-list">
                    <?php
                    if (!empty($projects)) {
                        foreach ($projects as $project) {
                            $dateTime = new DateTime($project['created_at']);
                            $formattedDate = $dateTime->format('Y/m/d \a\t g:i a');


                            ?>
                            <tr id="post-1"
                                class="iedit author-self level-0 post-1 type-post status-publish format-standard hentry category-uncategorized">
                                <td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
                                    <strong>
                                        <?php echo $project['project']; ?>
                                    </strong>
                                    <div class="row-actions">
                                        <span class="edit">
                                            <a href="<?php echo admin_url('admin.php?page=add_project&edit_id=' . $project['id'] . ''); ?>"
                                                aria-label="Edit “<?php echo $project['project']; ?>”">Edit</a> | </span>
                                        <span class="trash">
                                            <a href="<?php echo admin_url('admin.php?page=add_project&delete_id=' . $project['id'] . ''); ?>"
                                                class="submitdelete"
                                                aria-label="Move “<?php echo $project['project']; ?>” to the Trash">Delete</a>
                                        </span>
                                    </div>
                                </td>
                                <?php
                                $depatment_name_table_name = $wpdb->prefix . 'depatment_name';
                                $department = $wpdb->get_row(
                                    "SELECT depatment_name FROM $depatment_name_table_name WHERE id = '" . $project['depatment_id'] . "'"
                                );
                                ?>
                                <td class="author column-author" data-colname="Author">
                                    <?php echo $department->depatment_name; ?>
                                </td>
                                <td class="author column-author" data-colname="Author">
                                    <?php echo get_userdata($project['team_lead'])->display_name; ?>
                                </td>
                                <?php
                                $profile_name_table_name = $wpdb->prefix . 'profile_name';
                                $profile_names = $wpdb->get_results(
                                    "SELECT profile_name FROM $profile_name_table_name WHERE id IN (" . $project['profile_ids'] . ")"
                                );
                                $profile_names = array_map(function ($item) {
                                    return $item->profile_name;
                                }, $profile_names);
                                ?>
                                <td class="author column-author" data-colname="Author">
                                    <?php echo implode(',', $profile_names); ?>
                                </td>
                                <td class="author column-author" data-colname="allocated">
                                    <?php echo $project['allocated_hours'] ?>
                                </td>
                                <td class="date column-date" data-colname="Date">
                                    <?php echo $formattedDate; ?>
                                </td>

                            </tr>
                            <?php
                        }
                        ?>
                        <?php
                    } else {
                        echo '<tr class="no-items">
                        <td class="colspanchange" colspan="4">No posts found.</td>
                        </tr>';
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th scope="col" id="author" class="manage-column column-author">Project Name</th>
                        <th scope="col" id="author" class="manage-column column-author">Depatment</th>
                        <th scope="col" id="author" class="manage-column column-author">Team Lead</th>
                        <th scope="col" id="author" class="manage-column column-author">Project Profile</th>
                        <th scope="col" id="author" class="manage-column column-author">Allocated Hours</th>
                        <th scope="col" id="author" class="manage-column column-author">Created At</th>
                    </tr>
                </tfoot>
            </table>
        </form>
    </div>
    <?php
}
//delete function
function project_delete_project($id)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'projects';
    // Delete project from the database
    $wpdb->delete($table_name, array('id' => $id));
    // Check for errors
    if ($wpdb->last_error) {
        wp_die('Database error: ' . $wpdb->last_error);
    }
}
// Add project AJAX callback
add_action('wp_ajax_add_project', 'add_project_callback');
function add_project_callback()
{
    global $wpdb;
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $project = isset($_POST['name']) ? $_POST['name'] : null;
    $depatment_id = isset($_POST['depatment_id']) ? $_POST['depatment_id'] : null;
    $team_lead = isset($_POST['team_lead']) ? $_POST['team_lead'] : null;
    $profile_ids = isset($_POST["profile_ids"]) ? $_POST["profile_ids"] : null;
    $allocated_hours = isset($_POST["allocated_hours"]) ? $_POST["allocated_hours"] : null;
    $projects_table_name = $wpdb->prefix . 'projects';
    if ($id > 0) {
        $wpdb->update(
            $projects_table_name,
            array(
                'project' => $project,
                'profile_ids' => implode(",", $profile_ids),
                'depatment_id' => $depatment_id,
                'team_lead' => $team_lead,
                'allocated_hours' => $allocated_hours
            ),
            array('id' => $id)
        );
    } else {
        $wpdb->insert(
            $projects_table_name,
            array(
                'project' => $project,
                'profile_ids' => implode(",", $profile_ids),
                'team_lead' => $team_lead,
                'depatment_id' => $depatment_id,
                'allocated_hours' => $allocated_hours,
            )
        );
    }
    if (!$wpdb->last_error) {
        echo 'inserted';
    }
    wp_die();
}
