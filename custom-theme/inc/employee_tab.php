<!-- Employee Tab Content -->
<div class="tab-pane fade show active" id="tab1">
    <!-- Form for add Task with mentioned hours-->
    <div class="performance_data_results_wrap">
        <div class="alert alert-danger alert-dismissible fade show hs_alert" role="alert" style="display: none;">
            <span>The Record Was Deleted</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            </button>
        </div>
        <div class="alert alert-success alert-dismissible fade show hs_updated_alert" role="alert" style="display: none;">
            <span>The Record Was Updated</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            </button>
        </div>
        <div class="table_wrap">
            <?php
            $performance_sql_select = "SELECT * FROM $performance_table_name  where `user_id` = '$current_user_id' AND is_approve=2 AND is_submit=0";
            $performance_data_results = $wpdb->get_results($performance_sql_select, ARRAY_A);
            if (!empty($performance_data_results)) {
                $is_show = true;
                $show_add_new = true;
                $show_submit = true;
                $sub_date = $performance_data_results[0]['date'];
            } else {
                $performance_sql_select = "SELECT * FROM $performance_table_name  where `user_id` = '$current_user_id' AND `date` = '$current_date' ";
                $performance_data_results = $wpdb->get_results($performance_sql_select, ARRAY_A);
                if (empty($performance_data_results)) {
                    $is_show = true;
                    $show_submit = false;
                    $show_add_new = true;
                } else {
                    if ($performance_data_results[0]['is_approve'] == 2 || $performance_data_results[0]['is_submit'] == 0) {
                        $is_show = true;
                        $show_add_new = true;
                        $show_submit = true;
                    } else {
                        $is_show = false;
                        $show_add_new = false;
                        $show_submit = false;
                        $sub_date = $performance_data_results[0]['date'];
                    }
                }
            }
            ?>
            <table>
                <thead>
                    <tr>
                        <th>Project Name</th>
                        <th>Online/Offline</th>
                        <th>No Hours</th>
                        <th>No Minutes</th>
                        <th>Billing Status</th>
                        <th>Profile Name</th>
                        <th>Notes</th>
                        <?php
                        if ($is_show) {
                        ?>
                            <th>Action</th>
                        <?php
                        }
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($show_add_new) {
                    ?>
                        <tr>
                            <td>
                                <div style="max-width: 200px !important;">
                                    <select class='add-field form-control hs_select' name='project_name' required>
                                        <option value=''>Select Project Name</option>
                                        <?php foreach ($project_data_results as $project_row) {
                                        ?>
                                            <option value='<?php echo $project_row['id']; ?>' data-profile='<?php echo $project_row['profile_ids']; ?>'>
                                                <?php echo $project_row['project']; ?>
                                            </option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <span class="ap_time_limit"></span>
                            </td>
                            <td>
                                <select class="add-field form-control" name="online_offline" required="">
                                    <option value="online">Online</option>
                                    <option value="offline">Offline</option>
                                </select>
                            </td>
                            <td>
                                <div class="hs_custom_time">
                                    <select name="hs_hour" data-id="hs_hour" class="form-control">
                                        <option value="0" selected="">0</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                        <option value="8">8</option>
                                        <option value="9">9</option>
                                        <option value="10">10</option>
                                        <option value="11">11</option>
                                        <option value="12">12</option>
                                    </select>
                                    <input type="hidden" class="add-field form-control" name="number_of_hours" disabled="">
                                </div>
                            </td>
                            <td>
                                <div class="hs_custom_time">
                                    <select name="hs_min" data-id="hs_min" class="form-control">
                                        <option value="0" selected="">00</option>
                                        <option value="10">10</option>
                                        <option value="20">20</option>
                                        <option value="30">30</option>
                                        <option value="40">40</option>
                                        <option value="50">50</option>
                                    </select>
                                </div>
                            </td>
                            <td>
                                <select class="add-field form-control" name="billing_status" required="">
                                    <option value="billable">Billable</option>
                                    <option value="non-billable">Non Billable</option>
                                    <option value="no-work">No Work</option>
                                </select>
                            </td>
                            <td>
                                <div style="max-width: 200px !important;">
                                    <select name="profile_name" class="add-field form-control hs_select">
                                        <option value=''>Select Profile Name</option>
                                    </select>
                                </div>
                            </td>
                            <td>
                                <textarea class="add-field form-control" name="notes"></textarea>
                            </td>
                            <td>
                                <button class="form-control btn btn-success add_new_tr">
                                    <span class="dashicons dashicons-saved">
                                    </span>
                                </button>
                            </td>
                        </tr>
                        <?php
                    }
                    if (!empty($performance_data_results)) {
                        foreach ($performance_data_results as $performance_row) {
                            if ($performance_row['project_id'] != 0) {
                                $tr_project = $wpdb->prepare("SELECT * FROM $project_table_name WHERE id = %d", $performance_row['project_id']);
                                $tr_project_results = $wpdb->get_results($tr_project);
                                $tr_project = $tr_project_results[0]->project;
                            }
                        ?>
                            <tr>
                                <td>
                                    <span class='data-field' data-field='project_name'>
                                        <?php echo $tr_project; ?>
                                    </span>
                                    <?php
                                    if ($is_show) {
                                    ?>
                                        <div class="hs_hide_tr_select" style="max-width: 200px !important;">
                                            <select class='edit-field form-control hs_select' data-field='project_name' style="display: none;" name="project_name">
                                                <?php foreach ($project_data_results as $project_row) {
                                                ?>
                                                    <option value='<?php echo $project_row['id']; ?>' <?php echo ($performance_row['project_id'] == $project_row['id']) ? 'selected' : ''; ?> data-profile='<?php echo $project_row['profile_ids']; ?>'>
                                                        <?php echo $project_row['project']; ?>
                                                    </option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                </td>
                                <td>
                                    <span class='data-field' data-field='online_offline'>
                                        <?php echo $performance_row['online_offline']; ?>
                                    </span>
                                    <?php
                                    if ($is_show) {
                                    ?>
                                        <select class='edit-field form-control' data-field='online_offline' style='display:none;'>
                                            <option value='online' <?php echo ($performance_row['online_offline'] == 'online') ? 'selected' : ''; ?>>Online</option>
                                            <option value='offline' <?php echo ($performance_row['online_offline'] == 'offline') ? 'selected' : ''; ?>>Offline</option>
                                        </select>
                                    <?php
                                    }
                                    ?>
                                </td>
                                <?php
                                $hs_cal = floatval($performance_row['number_of_hours']);
                                $hs_hour = floor($hs_cal);
                                $hs_min = round(($hs_cal - $hs_hour) * 60);
                                ?>
                                <td>
                                    <span class='data-field' data-field='hs_number_of_hours'>
                                        <?php echo $hs_hour; ?>
                                    </span>
                                    <?php
                                    if ($is_show) {
                                    ?>
                                        <div class="hs_custom_time">
                                            <select name="hs_hour" class="form-control  edit-field" data-field='hs_number_of_hours' data-id="hs_hour" style='display:none;'>
                                                <option <?php echo ($hs_hour == 0) ? 'selected' : ''; ?> value="0">0</option>
                                                <option <?php echo ($hs_hour == 1) ? 'selected' : ''; ?> value="1">1</option>
                                                <option <?php echo ($hs_hour == 2) ? 'selected' : ''; ?> value="2">2</option>
                                                <option <?php echo ($hs_hour == 3) ? 'selected' : ''; ?> value="3">3</option>
                                                <option <?php echo ($hs_hour == 4) ? 'selected' : ''; ?> value="4">4</option>
                                                <option <?php echo ($hs_hour == 5) ? 'selected' : ''; ?> value="5">5</option>
                                                <option <?php echo ($hs_hour == 6) ? 'selected' : ''; ?> value="6">6</option>
                                                <option <?php echo ($hs_hour == 7) ? 'selected' : ''; ?> value="7">7</option>
                                                <option <?php echo ($hs_hour == 8) ? 'selected' : ''; ?> value="8">8</option>
                                                <option <?php echo ($hs_hour == 9) ? 'selected' : ''; ?> value="9">9</option>
                                                <option <?php echo ($hs_hour == 10) ? 'selected' : ''; ?> value="10">10</option>
                                                <option <?php echo ($hs_hour == 11) ? 'selected' : ''; ?> value="11">11</option>
                                                <option <?php echo ($hs_hour == 12) ? 'selected' : ''; ?> value="12">12</option>
                                            </select>
                                            <input type="hidden" name="number_of_hours" class="edit_h number_of_hour" data-hs_field='number_of_hours' value='<?php echo $performance_row['number_of_hours']; ?>' disabled>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                </td>
                                <td>
                                    <span class='data-field' data-field='number_of_min'>
                                        <?php echo $hs_min; ?>
                                    </span>
                                    <?php
                                    if ($is_show) {
                                    ?>
                                        <div class="hs_custom_time ">
                                            <select name="hs_min" class="form-control  edit-field" data-field='number_of_min' data-id="hs_min" style='display:none;'>
                                                <option <?php echo ($hs_min == 0) ? 'selected' : ''; ?> value="0" selected>00</option>
                                                <option <?php echo ($hs_min == 10) ? 'selected' : ''; ?> value="10">10</option>
                                                <option <?php echo ($hs_min == 20) ? 'selected' : ''; ?> value="20">20</option>
                                                <option <?php echo ($hs_min == 30) ? 'selected' : ''; ?> value="30">30</option>
                                                <option <?php echo ($hs_min == 40) ? 'selected' : ''; ?> value="40">40</option>
                                                <option <?php echo ($hs_min == 50) ? 'selected' : ''; ?> value="50">50</option>
                                            </select>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                </td>
                                <td>
                                    <span class='data-field' data-field='billing_status'>
                                        <?php echo $performance_row['billing_status']; ?>
                                    </span>
                                    <?php
                                    if ($is_show) {
                                    ?>
                                        <select class='edit-field form-control' data-field='billing_status' style='display:none;'>
                                            <option value='billable' <?php echo ($performance_row['billing_status'] == 'billable') ? 'selected' : ''; ?>>Billable</option>
                                            <option value='non-billable' <?php echo ($performance_row['billing_status'] == 'non-billable') ? 'selected' : ''; ?>>Non Billable</option>
                                            <option value='no-work' <?php echo ($performance_row['billing_status'] == 'no-work') ? 'selected' : ''; ?>>No Work</option>
                                        </select>
                                    <?php
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $profile_name = $wpdb->get_row($wpdb->prepare("SELECT profile_name FROM $profile_table_name WHERE id = %d", $performance_row['profile_name']), ARRAY_A);
                                    ?>
                                    <span class='data-field' data-field='profile_name'>
                                        <?php if ($profile_name) {
                                            echo $profile_name['profile_name'];
                                        }
                                        ?>
                                    </span>
                                    <?php
                                    if ($is_show) {
                                    ?>
                                        <div class="hs_hide_tr_select" style="max-width: 200px !important;">
                                            <select name="profile_name" class='edit-field form-control hs_select' data-field='profile_name' style='display:none;'>
                                                <option value=''>Select Profile Name</option>
                                                <?php
                                                $profile_ids = $tr_project_results[0]->profile_ids;
                                                $profile_query = $wpdb->prepare("SELECT * FROM $profile_table_name WHERE id IN ( $profile_ids)", $profile_ids);
                                                $profile_name_arr = $wpdb->get_results($profile_query, ARRAY_A);
                                                foreach ($profile_name_arr as $profile_names) {
                                                ?>
                                                    <option value='<?php echo $profile_names['id']; ?>' <?php echo ($performance_row['profile_name'] == $profile_names['id']) ? 'selected' : ''; ?>><?php echo $profile_names['profile_name']; ?></option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                </td>
                                <td>
                                    <span class='data-field' data-field='notes'>
                                        <?php echo $performance_row['notes']; ?>
                                    </span>
                                    <?php
                                    if ($is_show) {
                                    ?>
                                        <textarea class='edit-field form-control' data-field='notes' style='display:none;'><?php echo $performance_row['notes']; ?></textarea>
                                    <?php
                                    }
                                    ?>
                                </td>
                                <?php
                                if ($is_show) {
                                ?>
                                    <td>
                                        <div class="action_td">
                                            <button class='edit-entry btn btn-info' data-entry-id='<?php echo $performance_row['id']; ?>'>
                                                <span class="dashicons dashicons-edit">
                                                </span>
                                            </button>
                                            <button class='update-entry btn btn-warning' data-entry-id='<?php echo $performance_row['id']; ?>' style='display:none;'>
                                                <span class="dashicons dashicons-saved">
                                                </span>
                                            </button>
                                            <button class='delete-entry btn btn-danger' data-entry-id='<?php echo $performance_row['id']; ?>'>
                                                <span class="dashicons dashicons-trash">
                                                </span>
                                            </button>
                                        </div>
                                    </td>
                                <?php
                                }
                                ?>
                            </tr>
                        <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="9" style="text-align: center;"> No results found.</td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div class="hs_btn_wrap" style="text-align: end; margin-top:20px;">
            <button class='submit-entry btn btn-success' <?php echo 'data-sub_date="' . $sub_date . '"';
                                                            if (!$show_submit) {
                                                            ?>style='display:none;' <?php
                                                                                }
                                                                                    ?>>Submit</button>
        </div>
    </div>
</div>
<div class="tab-pane fade" id="tab2">
    <!-- Performance Chart Data -->
    <?php
    $current_month_first_day = date('Y-m-01');
    $current_month_last_day = date('Y-m-t');
    $productive_hours = $wpdb->get_var("SELECT sum(number_of_hours) FROM $performance_table_name  where billing_status = 'billable' AND `user_id` = '$current_user_id'  AND date BETWEEN '$current_month_first_day' AND '$current_month_last_day' ORDER BY date");
    $non_productive_hours = $wpdb->get_var("SELECT sum(number_of_hours) FROM $performance_table_name  where billing_status = 'non-billable' AND `user_id` = '$current_user_id'  AND date BETWEEN '$current_month_first_day' AND '$current_month_last_day' ORDER BY date");
    $no_work_hours = $wpdb->get_var("SELECT sum(number_of_hours) FROM $performance_table_name  where billing_status = 'no-work' AND `user_id` = '$current_user_id'  AND date BETWEEN '$current_month_first_day' AND '$current_month_last_day' ORDER BY date");
    $total_hours = $productive_hours + $non_productive_hours + $no_work_hours;
    ?>
    <script>
        jQuery("#tab2-tab").click(function() {
            var data = {
                labels: ['Billable (<?php echo $productive_hours; ?>)', 'Non-Billable (<?php echo $non_productive_hours; ?>)', 'No-Work (<?php echo $no_work_hours; ?>)'],
                datasets: [{
                    label: 'This Month',
                    data: [<?php echo $productive_hours; ?>, <?php echo $non_productive_hours; ?>, <?php echo $no_work_hours; ?>],
                    maxBarThickness: 60,
                    backgroundColor: [
                        '#334960',
                        '#f46524', '#b1b1b1'
                    ],
                }]
            };
            var config = {
                type: 'bar',
                data: data,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: <?php echo  $total_hours; ?>
                        }
                    }
                }
            };
            var ctx = jQuery('.hide_chart');
            var existingChart = Chart.getChart(ctx);
            if (existingChart) {
                existingChart.destroy();
            }
            var myBarChart = new Chart(ctx, config);
        });
    </script>
    <div class="chart_wrap">
        <canvas class="hide_chart">
        </canvas>
        <div class="hs_calculations">
            <?php
            if ($total_hours > 0) {
            ?>
                <h3>
                    <?php echo round($non_productive_hours / ($total_hours) * 100); ?>% </h3>
                <p>Non-productive hours </p>
                <h3>
                    <?php echo round($productive_hours / ($total_hours) * 100); ?>% </h3>
                <p>Productive hours </p>
                <h3>
                    <?php echo round($no_work_hours / ($total_hours) * 100); ?>% </h3>
                <p>No Work hours </p>
            <?php
            } else {
            ?>
                <h3>0% </h3>
                <p>Non-productive hours </p>
                <h3>0% </h3>
                <p>Productive hours </p>
            <?php
            }
            ?>
        </div>
    </div>
    <div class="table_wrap">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Billable</th>
                    <th>Non Billable</th>
                    <th>No Work</th>
                    <th>View In Detail</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $month = new DateTime();
                $today = new DateTime(date("Y-m-d"));
                $month->modify('first day of this month');
                while ($today->format('Y-m-d') >= $month->format('Y-m-d')) {
                    $tr_date = $today->format('Y-m-d');
                    $today->modify('-1 day');
                    if ($today->format('w') == 6) {
                ?>
                        <tr>
                            <td colspan="5" class="non_working_day">Non Working Day</td>
                        </tr>
                    <?php
                    } else {
                        $productive_hours = $wpdb->get_var("SELECT sum(number_of_hours) FROM $performance_table_name  where billing_status = 'billable' AND `user_id` = '$current_user_id'  AND date ='" . $tr_date . "'");
                        $non_productive_hours = $wpdb->get_var("SELECT sum(number_of_hours) FROM $performance_table_name  where billing_status = 'non-billable' AND `user_id` = '$current_user_id'  AND date ='" . $tr_date . "'");
                        $no_work_hours = $wpdb->get_var("SELECT sum(number_of_hours) FROM $performance_table_name  where billing_status = 'no-work' AND `user_id` = '$current_user_id'  AND date ='" . $tr_date . "'");
                    ?>
                        <tr>
                            <td>
                                <?php echo $tr_date; ?>
                            </td>
                            <td>
                                <?php echo $productive_hours; ?>
                            </td>
                            <td>
                                <?php echo $non_productive_hours; ?>
                            </td>
                            <td>
                                <?php echo $no_work_hours; ?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-primary hs_emp_view" data-bs-toggle="modal" data-bs-target="#hs_emp_modal" data-date="<?php echo $tr_date; ?>">View</button>
                            </td>
                        </tr>
                <?php
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<div class="tab-pane fade" id="tab4">
    <!-- Performance Data view-->
    <div class="from_wrap">
        <div class="row mb-4">
            <div class="start_date col-3">
                <input type="date" name="start_date" class="form-control">
            </div>
            <div class="end_date col-3">
                <input type="date" name="end_date" class="form-control">
            </div>
            <div class="col-3">
                <span class="btn btn-info hs_search_btn">Search</span>
            </div>
        </div>
    </div>
    <div class="table_wrap">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Billable</th>
                    <th>Non Billable</th>
                    <th>No Work</th>
                    <th>View In Detail</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $today = new DateTime(date("Y-m-d"));
                $tr_date = $today->format('Y-m-d');
                if ($today->format('w') == 7) {
                ?>
                    <tr>
                        <td colspan="5" class="non_working_day">Non Working Day</td>
                    </tr>
                <?php
                } else {
                    $productive_hours = $wpdb->get_var("SELECT sum(number_of_hours) FROM $performance_table_name  where billing_status = 'billable' AND `user_id` = '$current_user_id'  AND date ='" . $tr_date . "'");
                    $non_productive_hours = $wpdb->get_var("SELECT sum(number_of_hours) FROM $performance_table_name  where billing_status = 'non-billable' AND `user_id` = '$current_user_id'  AND date ='" . $tr_date . "'");
                    $no_work_hours = $wpdb->get_var("SELECT sum(number_of_hours) FROM $performance_table_name  where billing_status = 'no-work' AND `user_id` = '$current_user_id'  AND date ='" . $tr_date . "'");
                ?>
                    <tr>
                        <td>
                            <?php echo $tr_date; ?>
                        </td>
                        <td>
                            <?php echo $productive_hours; ?>
                        </td>
                        <td>
                            <?php echo $non_productive_hours; ?>
                        </td>
                        <td>
                            <?php echo $no_work_hours; ?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-primary hs_emp_view" data-bs-toggle="modal" data-bs-target="#hs_emp_modal" data-date="<?php echo $tr_date; ?>">View</button>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- employee pop_up html -->
<div class="modal fade" id="hs_emp_modal" tabindex="-1" aria-labelledby="hs_emp_modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="hs_emp_modalLabel">
                    <span class="hs_tr_filter_date">
                    </span>
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <table class='emp_modal_table'>
                    <thead>
                        <tr>
                            <th>Project Name</th>
                            <th>Online/Offline</th>
                            <th>No Hours</th>
                            <th>No Minutes</th>
                            <th>Billing Status</th>
                            <th>Profile Name</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="hs_loader">
        </div>
    </div>
</div>