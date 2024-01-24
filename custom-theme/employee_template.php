<?php
/* Template Name: Employee Template */
get_header();
$project_sql_select = "SELECT * FROM $project_table_name  where `depatment_id` = '$user_dept' ";
$project_data_results = $wpdb->get_results($project_sql_select, ARRAY_A);
?>
<div class="user_info_wrap">
    <div class="u_name">Hello <?php echo get_userdata($current_user_id)->display_name; ?>,</div>
    <div class="c_date">Date:- <?php echo $current_date; ?>
    </div>
</div>
<div class="tab_wrap">
    <ul class="nav nav-tabs" id="myTabs">
        <li class="nav-item">
            <a class="nav-link active" id="tab1-tab" data-bs-toggle="tab" href="#tab1">Today</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="tab2-tab" data-bs-toggle="tab" href="#tab2">This Month</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="tab4-tab" data-bs-toggle="tab" href="#tab4">Search By Date</a>
        </li>
        <?php
        if (in_array('contributor', $current_user->roles)) {
        ?>
            <li class="nav-item">
                <a class="nav-link" id="tab3-tab" data-bs-toggle="tab" href="#tab3">Team Performance</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab5-tab" data-bs-toggle="tab" href="#tab5">Team Performance Search By Date</a>
            </li>
        <?php
        }
        ?>
    </ul>
    <div class="tab-content">
        <?php
        include(get_template_directory() . '/inc/employee_tab.php');
        if (in_array('contributor', $current_user->roles)) {
            $team_users = get_users(array(
                'department'     => $user_dept,
            ));
            include(get_template_directory() . '/inc/team_lead_tab.php');
        }
        ?>
    </div>
</div>

<?php
get_footer();
