<?php
// User Based Performance list display
function display_performance_list()
{
    global $wpdb;
    $tab = isset($_GET['tab']) ? $_GET['tab'] : null;
    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d');
    $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
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
    if ($user_id) {
        $args = array(
            'include' => array($user_id),
            'fields'  => array('ID')
        );
        $users = get_users($args);
    } else {
        $users = get_users(array(
            'meta_key'     => $meta_key,
            'meta_value'   => $meta_value,
            'meta_compare' => '=',
        ));
    }
?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Performance List</h1>
        <nav class="nav-tab-wrapper">
            <a href="?page=performance_list" class="nav-tab <?php if ($tab === null) : ?>nav-tab-active<?php endif; ?>">Dev Department</a>
            <a href="?page=performance_list&tab=marketing_department" class="nav-tab <?php if ($tab === 'marketing_department') : ?>nav-tab-active<?php endif; ?>">Marketing Department</a>
            <a href="?page=performance_list&tab=sales_department" class="nav-tab <?php if ($tab === 'sales_department') : ?>nav-tab-active<?php endif; ?>">Sales Department</a>
        </nav>
        <div class="tab-content">
            <hr class="wp-header-end">
            <form id="rows-filter" method="get">
                <div class="tablenav top">
                    <input type="hidden" name="page" value="performance_list">
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
                    Start Date
                    <input type="date" name="start_date" value="<?php echo $start_date; ?>">
                    End Date
                    <input type="date" name="end_date" value="<?php echo $end_date; ?>">
                    <button class="button">Filter</button>
                </div>
            </form>
            <table class="wp-list-table widefat fixed striped table-view-list rows">
                <thead>
                    <tr>
                        <th scope="col" id="author" class="manage-column column-author">Employee Name</th>
                        <th scope="col" id="author" class="manage-column column-author">Billable</th>
                        <th scope="col" id="author" class="manage-column column-author">Non Billable </th>
                        <th scope="col" id="author" class="manage-column column-author">No Work </th>
                        <th scope="col" id="author" class="manage-column column-author">View In Detail
                        </th>
                    </tr>
                </thead>
                <tbody id="the-list">
                    <?php
                    $productive_hours_t = 0;
                    $non_productive_hours_t = 0;
                    $no_work_hours_t = 0;
                    if (!empty($users)) {
                        foreach ($users as $user) {
                            $productive_hours = $wpdb->get_var("SELECT sum(number_of_hours) FROM $performance_table_name  where billing_status = 'billable' AND `user_id` = '$user->ID'  AND  date BETWEEN '" . $start_date . "' AND '" . $end_date . "' AND is_submit ='1'");
                            $non_productive_hours = $wpdb->get_var("SELECT sum(number_of_hours) FROM $performance_table_name  where billing_status = 'non-billable' AND `user_id` = '$user->ID'  AND  date BETWEEN '" . $start_date . "' AND '" . $end_date . "' AND is_submit ='1'");
                            $no_work_hours = $wpdb->get_var("SELECT sum(number_of_hours) FROM $performance_table_name  where billing_status = 'no-work' AND `user_id` = '$user->ID'  AND  date BETWEEN '" . $start_date . "' AND '" . $end_date . "' AND is_submit ='1'");
                            $productive_hours_t += $productive_hours;
                            $non_productive_hours_t += $non_productive_hours;
                            $no_work_hours_t += $no_work_hours;
                    ?>
                            <tr>
                                <td class="author column-author"><?php echo  get_userdata($user->ID)->display_name; ?></td>
                                <td> <?php echo  $productive_hours; ?> </td>
                                <td> <?php echo  $non_productive_hours; ?> </td>
                                <td> <?php echo  $no_work_hours; ?> </td>
                                <td> <button class="hs_clickBtn page-title-action" data-start_date="<?php echo $start_date; ?>" data-end_date="<?php echo $end_date; ?>" data-user_id="<?php echo $user->ID; ?>" data-user_name="<?php echo get_userdata($user->ID)->display_name;  ?>">View</button></td>
                            </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='5'>No User Found</td></tr>";
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th scope="col" id="author" class="manage-column column-author">Total</th>
                        <th scope="col" id="author" class="manage-column column-author"><?php echo $productive_hours_t; ?></th>
                        <th scope="col" id="author" class="manage-column column-author"><?php echo $non_productive_hours_t; ?> </th>
                        <th scope="col" id="author" class="manage-column column-author"><?php echo $no_work_hours_t; ?></th>
                        <th scope="col" id="author" class="manage-column column-author"><?php echo $productive_hours_t + $non_productive_hours_t + $no_work_hours_t; ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="popup_wrap">
            <div class="popup-container">
                <div class="popup">
                    <div class="close-popup closeBtn"><span class="dashicons dashicons-no"></span></div>
                    <div class="hs_modal_table">
                        <h1 class="wp-heading-inline"></h1>
                        <table class="wp-list-table widefat fixed striped table-view-list user_list_table">
                            <thead>
                                <tr>
                                    <th scope="col" id="author" class="manage-column column-author">Project Name</th>
                                    <th scope="col" id="author" class="manage-column column-author">Online/Offline</th>
                                    <th scope="col" id="author" class="manage-column column-author">Number of Hours</th>
                                    <th scope="col" id="author" class="manage-column column-author">Number of Minutes</th>
                                    <th scope="col" id="author" class="manage-column column-author">Billing Status</th>
                                    <th scope="col" id="author" class="manage-column column-author">Profile Name</th>
                                    <th scope="col" id="author" class="manage-column column-author">Notes</th>
                                    <th scope="col" id="author" class="manage-column column-author">Reviewed By</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <style>
            select {
                width: 100%;
                max-width: calc(100% - 10px);
            }
            .popup_wrap {
                display: none;
            }
            .close-popup.closeBtn span {
                position: absolute;
                right: 0;
                top: -10px;
            }
            .close-popup.closeBtn {
                position: relative;
            }
            .popup-container {
                height: calc(100vh - 30px);
                width: 100vw;
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                align-items: center;
                background-color: rgba(137, 137, 137, 0.5);
                position: absolute;
                top: 0;
                left: 0;
                z-index: 9999;
                margin-left: calc(100% - 100vw);
            }
            .popup {
                background-color: #ffffff;
                padding: 20px 30px;
                width: calc(100% - 10%);
                border-radius: 15px;
            }
        </style>
        <script>
            jQuery(document).ready(function($) {
                $('input[type="date"]').on('change', function() {
                    var inputDate = $(this).val();
                    var today = new Date().toISOString().split('T')[0];
                    if (inputDate > today) {
                        alert("Please select a date not greater than today.");
                        $(this).val(today);
                    }
                });
                $('.hs_clickBtn').click(function() {
                    $('.hs_modal_table h1').text($(this).data('user_name'));
                    let start_date = $(this).data('start_date');
                    let end_date = $(this).data('end_date');
                    let user_id = $(this).data('user_id');
                    $.ajax({
                        url: '<?php echo admin_url('admin-ajax.php'); ?>',
                        type: 'POST',
                        data: {
                            action: 'admin_get_user_detail',
                            start_date: start_date,
                            end_date: end_date,
                            user_id: user_id
                        },
                        success: function(response) {
                            $('.hs_modal_table tbody').html(response)
                        }
                    });
                    $('.popup_wrap').show();
                });
                // $('.popup_wrap').click(function() {
                //     $('.popup_wrap').hide();
                // });
                $('.closeBtn').click(function() {
                    $('.popup_wrap').hide();
                });
                $(document).on('change', '.hs_custom_time select', function() {
                    let row = $(this).closest('tr');
                    var hs_hour = row.find('.hs_custom_time select[data-id="hs_hour"]').val();
                    var hs_min = row.find('.hs_custom_time select[data-id="hs_min"]').val();
                    var hs_cal = ((parseInt(hs_min) / 60) + parseInt(hs_hour)).toFixed(2);
                    row.find(".hs_custom_time input[name='number_of_hours']").val(hs_cal);
                });
                $(document).on("click", ".update_user_list", function() {
                    $(".user_list_table tbody tr:not(:last)").each(function() {
                        let id = $(this).data('id');
                        let date = $(this).data('date');
                        let user_id = $(this).data('user_id');
                        let number_of_hours = $(this).find('input[name="number_of_hours"]').val();
                        let billing_status = $(this).find("select[name='billing_status']").val();
                        let online_offline = $(this).find("select[name='online_offline']").val();
                        let profile_name = $(this).find("select[name='profile_name']").val();
                        $.ajax({
                            url: '<?php echo admin_url('admin-ajax.php'); ?>',
                            type: 'POST',
                            data: {
                                action: 'admin_update_user_list',
                                id: id,
                                number_of_hours: number_of_hours,
                                billing_status: billing_status,
                                user_id: user_id,
                                online_offline: online_offline,
                                profile_name: profile_name,
                                date: date
                            },
                        });
                        $('.popup_wrap').hide();
                        location.reload();
                    });
                });
            });
        </script>
    </div>
<?php
}
