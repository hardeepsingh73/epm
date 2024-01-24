<!-- Tabs Content for Team Lead -->
<div class="tab-pane fade hs_tl_search_wrap" id="tab3">
    <?php
    $team_users = get_users(array(
        'department' => $user_dept,
    ));
    ?>
    <div class="chart_wrap">
        <canvas class="hide_chart_team">
        </canvas>
        <div class="hs_calculations">
            <h3 class="chart_non_productive">0% </h3>
            <p>Non-productive hours </p>
            <h3 class="chart_productive">0% </h3>
            <p>Productive hours </p>
            <h3 class="chart_no_work">0% </h3>
            <p>No Work hours </p>
        </div>
    </div>
    <div class="from_wrap">
        <div class="row mb-4">
            <div class="start_date col-3">
                <input type="date" name="start_date" class="form-control">
            </div>
            <div class="end_date col-3">
                <select name="user_id" class="form-select">
                    <option value="">Select User</option>
                    <?php
                    foreach ($team_users as $team_user) {
                        echo '<option value="' . $team_user->ID . '">' . $team_user->display_name . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="col-3">
                <span class="btn btn-info hs_tl_search_btn">Search</span>
            </div>
        </div>
    </div>
    <table>
        <thead>
            <tr>
                <th>Employee Name</th>
                <th>Billable</th>
                <th>Non Billable</th>
                <th>No Work</th>
                <th>Status</th>
                <th>View In Detail</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($team_users as $team_user) {
                $productive_hours = $wpdb->get_var("SELECT sum(number_of_hours) FROM $performance_table_name  where billing_status = 'billable' AND `user_id` = '$team_user->ID'  AND date ='" . $current_date . "' AND is_submit ='1'");
                $non_productive_hours = $wpdb->get_var("SELECT sum(number_of_hours) FROM $performance_table_name  where billing_status = 'non-billable' AND `user_id` = '$team_user->ID'  AND date ='" . $current_date . "' AND is_submit ='1'");
                $no_work_hours = $wpdb->get_var("SELECT sum(number_of_hours) FROM $performance_table_name  where billing_status = 'no-work' AND `user_id` = '$team_user->ID'  AND date ='" . $current_date . "' AND is_submit ='1'");
                $status = $wpdb->get_row("SELECT * FROM $performance_table_name  where  `user_id` = '$team_user->ID'  AND date ='" . $current_date . "' AND is_submit ='1'");
            ?>
                <tr>
                    <td>
                        <?php echo $team_user->display_name; ?>
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
                        <?php if ($status) {
                            if ($status->is_approve == 0) {
                                echo 'Pending';
                            } else if ($status->is_approve == 1) {
                                echo 'Aproved';
                            } else if ($status->is_approve == 2 ) {
                                echo 'Rejected';
                            }
                        } ?>
                    </td>
                    <td>
                        <button type="button" class="btn btn-primary hs_team_lead_view" data-bs-toggle="modal" data-bs-target="#team_Lead_modal" data-start_date="<?php echo $current_date; ?>" data-end_date="<?php echo $current_date; ?>" data-end_date="<?php echo $current_date; ?>" data-user_id="<?php echo $team_user->ID; ?>" data-user="<?php echo $team_user->display_name; ?>">View</button>
                    </td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
</div>
<div class="tab-pane fade hs_tl_search_wrap" id="tab5">
    <div class="from_wrap">
        <div class="row mb-4">
            <div class="start_date col-3">
                <input type="date" name="start_date" class="form-control">
            </div>
            <div class="end_date col-3">
                <input type="date" name="end_date" class="form-control">
            </div>
            <div class="end_date col-3">
                <select name="user_id" class="form-select">
                    <option value="">Select User</option>
                    <?php
                    foreach ($team_users as $team_user) {
                        echo '<option value="' . $team_user->ID . '">' . $team_user->display_name . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="col-3">
                <span class="btn btn-info hs_tl_search_btn">Search</span>
            </div>
        </div>
    </div>
    <table>
        <thead>
            <tr>
                <th>Employee Name</th>
                <th>Billable</th>
                <th>Non Billable</th>
                <th>No Work</th>
                <th>View In Detail</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($team_users as $team_user) {
                $productive_hours = $wpdb->get_var("SELECT sum(number_of_hours) FROM $performance_table_name  where billing_status = 'billable' AND `user_id` = '$team_user->ID'  AND date ='" . $current_date . "' AND is_submit ='1'");
                $non_productive_hours = $wpdb->get_var("SELECT sum(number_of_hours) FROM $performance_table_name  where billing_status = 'non-billable' AND `user_id` = '$team_user->ID'  AND date ='" . $current_date . "' AND is_submit ='1'");
                $no_work_hours = $wpdb->get_var("SELECT sum(number_of_hours) FROM $performance_table_name  where billing_status = 'no-work' AND `user_id` = '$team_user->ID'  AND date ='" . $current_date . "' AND is_submit ='1'");
            ?>
                <tr>
                    <td>
                        <?php echo $team_user->display_name; ?>
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
                        <button type="button" class="btn btn-primary hs_team_lead_view" data-bs-toggle="modal" data-bs-target="#team_Lead_modal" data-start_date="<?php echo $current_date; ?>" data-end_date="<?php echo $current_date; ?>" data-user_id="<?php echo $team_user->ID; ?>" data-user="<?php echo $team_user->display_name; ?>">View</button>
                    </td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
</div>

<!-- team lead pop_up html -->

<div class="modal fade" id="team_Lead_modal" tabindex="-1" aria-labelledby="team_Lead_modal_label" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="team_Lead_modal_label">
                    <span class="hs_user" data-user_id="" data-date="">
                    </span>
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <table class="team_modal_table">
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
                <div class="modal_team_table_btn_wrap mt-3">
                    <div class="row">
                        <div class="col-6">
                            <textarea name="mail_note" id="mail_note" class="form-control"></textarea>
                        </div>
                        <div class="col-6">
                            <a href="javascript:void(0)" class="btn btn-success aprove_btn">Approve</a>
                            <a href="javascript:void(0)" class="btn btn-danger reject_btn">Reject</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="hs_loader">
        </div>
    </div>
</div>