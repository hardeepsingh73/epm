jQuery(document).ready(function ($) {
    // Tab3 click to update a chart start js//
    var tab3Chart = '';
    //calculate productive,non-productive hours //
    jQuery("#tab3-tab").click(function () {
        let productive_hours = 0;
        let non_productive_hours = 0;
        let no_work_hours = 0;
        $('#tab3 table tbody tr').each(function () {
            var pTd = $(this).find('td:eq(1)');
            var npTd = $(this).find('td:eq(2)');
            var nwTd = $(this).find('td:eq(3)');
            productive_hours += parseFloat(pTd.text()) || 0;
            non_productive_hours += parseFloat(npTd.text()) || 0;
            no_work_hours += parseFloat(nwTd.text()) || 0;
        });
        if (productive_hours > non_productive_hours && productive_hours > no_work_hours) {
            total_hours = productive_hours;
        } else if (non_productive_hours > productive_hours && non_productive_hours > no_work_hours) {
            total_hours = non_productive_hours;
        } else {
            total_hours = no_work_hours;
        }
        totalhours = productive_hours + non_productive_hours + no_work_hours;
        if (totalhours > 0) {
            $("#tab3 .chart_non_productive").html(Math.round((non_productive_hours / totalhours) * 100) + '%');
            $("#tab3 .chart_productive").html(Math.round((productive_hours / totalhours) * 100) + '%');
            $("#tab3 .chart_no_work").html(Math.round((no_work_hours / totalhours) * 100) + '%');
        } else {
            $("#tab3 .chart_non_productive").html('0%');
            $("#tab3 .chart_productive").html('0%');
            $("#tab3 .chart_no_work").html('0%');
        }
        var data = {
            labels: ['Billable ' + productive_hours + ')', 'Non-Billable (' + non_productive_hours + ')', 'No-Work (' + no_work_hours + ')'],
            datasets: [{
                label: 'Team Performance Today',
                data: [productive_hours, non_productive_hours, no_work_hours],
                maxBarThickness: 60,
                backgroundColor: [
                    '#334960',
                    '#f46524',
                    '#b1b1b1'
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
                        max: total_hours
                    }
                }
            }
        };
        var ctx = jQuery('.hide_chart_team');
        tab3Chart = Chart.getChart(ctx);
        if (tab3Chart) {
            tab3Chart.destroy();
        }
        tab3Chart = new Chart(ctx, config);
    });
    // tab3 click to update a chart js end //
    // Trim  the length of textarea field start js //
    $('textarea').on('input', function () {
        $(this).val($(this).val().trim());
        var currentLength = $(this).val().length;
        if (currentLength > 50) {
            $(this).val($(this).val().slice(0, 50));
        }
    });
    // Trim  the length of textarea field js end  //
    // Function to handle AJAX approval requests start //
    function approve_fun(date, user_id, is_approve) {
        let note = $('#mail_note').val();
        $.ajax({
            url: ajax_url,
            type: 'POST',
            data: {
                action: 'approve_fun',
                date: date,
                user_id: user_id,
                note: note,
                is_approve: is_approve
            },
            success: function (response) {
                $('#team_Lead_modal').modal('hide');
                $('#mail_note').val('');
            },
        });
    }
    // Handle click on approve and reject buttons start //
    $('.aprove_btn').click(function () {
        let date = $(".hs_user").data('date');
        let user_id = $(".hs_user").data('user_id');
        approve_fun(date, user_id, 1);
    });
    $('.reject_btn').click(function () {
        let date = $(".hs_user").data('date');
        let user_id = $(".hs_user").data('user_id');
        approve_fun(date, user_id, 2);
    });
    // Handle click on approve and reject buttons end //
    // Hide select dropdowns js start//
    $(".edit-field.hs_select").parent().hide();
    $(document).on("click", ".performance_data_results_wrap .edit-entry", function () {
        $(".edit-field.hs_select").parent().hide();
        $('.alert').hide();
        var row = $(this).closest("tr");
        $(".performance_data_results_wrap tbody tr").each(function () {
            var pre_dataFields = $(this).closest("tr").find(".data-field");
            var pre_editFields = $(this).closest("tr").find(".edit-field");
            var pre_editButton = $(this).closest("tr").find(".edit-entry");
            var pre_updateButton = $(this).closest("tr").find(".update-entry");
            pre_dataFields.show();
            pre_editFields.hide();
            pre_editButton.show();
            pre_updateButton.hide();
        });
        var dataFields = row.find(".data-field");
        var editFields = row.find(".edit-field");
        var editButton = row.find(".edit-entry");
        var updateButton = row.find(".update-entry");
        row.find(".edit-field.hs_select").parent().show();;
        dataFields.hide();
        editFields.show();
        editButton.hide();
        updateButton.show();
        row.find('.hs_hide_tr_select').show();
    });
    // Hide select dropdowns js end //
    // Update entry  button js start//
    $(document).on("click", ".performance_data_results_wrap .update-entry", function () {
        $('.alert').hide();
        var row = $(this).closest("tr");
        var editFields = row.find(".edit-field");
        var dataFields = row.find(".data-field");
        var editButton = row.find(".edit-entry");
        var updateButton = row.find(".update-entry");
        var entryId = $(this).data('entry-id');
        var project_name = row.find(".edit-field[ data-field='project_name']").val();
        var online_offline = row.find(".edit-field[ data-field='online_offline']").val();
        var number_of_hours = row.find(".number_of_hour[ data-hs_field='number_of_hours']").val();
        var billing_status = row.find(".edit-field[ data-field='billing_status']").val();
        var profile_name = row.find(".edit-field[ data-field='profile_name']").val();
        var reviewed_by = row.find(".edit-field[ data-field='reviewed_by']").val();
        var notes = row.find(".edit-field[ data-field='notes']").val();
        var is_valid = true;
        if (number_of_hours == '' || number_of_hours <= 0) {
            is_valid = false;
            row.find(".hs_custom_time select").addClass('hs_err');
        }
        if (online_offline == 'online' && profile_name == '') {
            is_valid = false;
            row.find(".edit-field[data-field='profile_name']").addClass('hs_err');
            row.find(".edit-field[data-field='online_offline']").addClass('hs_err');
        }
        let project_label = $(".add-field[name='project_name']").find(":selected").text();
        if (online_offline == 'online' && project_label == 'No Work') {
            is_valid = false;
            $(".add-field[name='profile_name']").addClass('hs_err');
            $(".add-field[name='online_offline']").addClass('hs_err');
        }
        if (billing_status != 'no-work' && project_label == 'No Work') {
            is_valid = false;
            $(".add-field[name='profile_name']").addClass('hs_err');
            $(".add-field[name='billing_status']").addClass('hs_err');
        }
        if (!is_valid) {
            $(".hs_alert span").html('Check Your Inputs');
            $(".hs_alert").show();
        } else {
            $.ajax({
                url: ajax_url,
                type: 'POST',
                data: {
                    action: 'update_performance_entry',
                    entry_id: entryId,
                    project_name: project_name,
                    online_offline: online_offline,
                    number_of_hours: number_of_hours,
                    billing_status: billing_status,
                    profile_name: profile_name,
                    reviewed_by: reviewed_by,
                    notes: notes,
                },
                success: function (response) {
                    if (response != 'error') {
                        $(".hs_updated_alert").show();
                        row.html(response);
                        $('.hs_select').select2();
                        $('.hs_hide_tr_select').hide();
                    } else {
                        $(".hs_alert span").html('somthing went wrong');
                        $(".hs_alert").show();
                    }
                },
                error: function (error) {
                    $(".hs_alert span").html('somthing went wrong');
                    $(".hs_alert").show();
                }
            });
            editFields.each(function (index) {
                if ($(this).data('field') == 'date') {
                    dataFields.eq(index).text($(this).html());
                } else if (($(this).data('field') == 'project_name') || ($(this).data('field') == 'reviewed_by') || ($(this).data('field') == 'billing_status')) {
                    dataFields.eq(index).text($(this).find('option:selected').text());
                } else {
                    dataFields.eq(index).text($(this).val());
                }
                $(this).hide();
                dataFields.eq(index).show();
                editButton.show();
                updateButton.hide();
            });
        }
    });
    //update entry button js end//
    //Delete entry button js start//
    $(document).on("click", ".performance_data_results_wrap .delete-entry", function () {
        $('.alert').hide();
        var entryId = $(this).data('entry-id');
        var d_tr = $(this).closest("tr");
        $.ajax({
            url: ajax_url,
            type: 'POST',
            data: {
                action: 'delete_performance_entry',
                entry_id: entryId,
                c_date: c_date,
                user_id: userId
            },
            success: function (response) {
                if (response == 'deleted') {
                    $(".hs_alert").show();
                    d_tr.remove();
                    if ($('#tab1 table tbody tr').length < 2) {
                        $("#tab1 table tbody").append('<tr><td colspan="9" style="text-align: center;"> No results found.</td></tr > ');
                        $('.submit-entry').hide();
                    }
                } else {
                    $(".hs_alert span").html('somthing went wrong');
                    $(".hs_alert").show();
                }
            },
            error: function (error) {
                $(".hs_alert span").html('somthing went wrong');
                $(".hs_alert").show();
            }
        });
    });
    //Delete entry button js end//
    //change project name js start //
    $(document).on("change", 'select[name="project_name"]', function () {
        $('.ap_time_limit').html('');
        $('.ap_time_limit').hide();
        var select = $(this);
        $('.hs_added').remove();
        var profileValue = $(this).find('option:selected').data('profile');
        var project_id = $(this).find('option:selected').val();

        $.ajax({
            url: ajax_url,
            type: 'POST',
            data: {
                action: 'get_profile',
                profile_id: profileValue,
                project_id: project_id,
            },
            success: function (response) {
                response = $.parseJSON(response);
                let allocated_hours = response.allocated_hours;
                if (allocated_hours > 0) {
                    let remainingHours = response.allocated_hours - response.number_of_hours;
                    if (remainingHours > 0 || remainingHours == 0) {
                        $('.ap_time_limit').html('Pending Hours is ' + remainingHours);
                    } else {
                        $('.ap_time_limit').html('Overbilled Hours is  ' + remainingHours);
                    }
                    $('.ap_time_limit').show();
                }
                $.each(response.profile_name, function (index, item) {
                    select.closest('tr').find('select[name="profile_name"]').append("<option class='hs_added' value='" + item.id + "'>" + item.profile_name + "</option>");
                });
            }
        });
    });
    //change project name js end//
    //Add new row js start//
    $(".performance_data_results_wrap .add_new_tr").click(function () {
        $('input').removeClass('hs_err');
        $('.alert').hide();
        var project_name = $(".add-field[name='project_name']").val();
        var online_offline = $(".add-field[name='online_offline']").val();
        var number_of_hours = $(".add-field[name='number_of_hours']").val();
        var billing_status = $(".add-field[name='billing_status']").val();
        var profile_name = $(".add-field[name='profile_name']").val();
        var notes = $(".add-field[name='notes']").val();
        var reviewed_by = $(".add-field[name='reviewed_by']").val();
        var is_valid = true;
        if (number_of_hours == '' || number_of_hours <= 0) {
            is_valid = false;
            $(".add-field[name='number_of_hours']").closest('tr').find(".hs_custom_time select").addClass('hs_err');
        }
        if (online_offline == 'online' && profile_name == '') {
            is_valid = false;
            $(".add-field[name='profile_name']").addClass('hs_err');
            $(".add-field[name='online_offline']").addClass('hs_err');
        }
        let project_label = $(".add-field[name='project_name']").find(":selected").text();
        if (online_offline == 'online' && project_label == 'No Work') {
            is_valid = false;
            $(".add-field[name='profile_name']").addClass('hs_err');
            $(".add-field[name='online_offline']").addClass('hs_err');
        }
        if (billing_status != 'no-work' && project_label == 'No Work') {
            is_valid = false;
            $(".add-field[name='profile_name']").addClass('hs_err');
            $(".add-field[name='billing_status']").addClass('hs_err');
        }
        if (!is_valid) {
            $(".hs_alert span").html('Check Your Inputs');
            $(".hs_alert").show();
        } else {
            $.ajax({
                url: ajax_url,
                type: 'POST',
                data: {
                    action: 'add_performance_entry',
                    user_id: userId,
                    c_date: c_date,
                    project_name: project_name,
                    online_offline: online_offline,
                    number_of_hours: number_of_hours,
                    billing_status: billing_status,
                    profile_name: profile_name,
                    notes: notes,
                    reviewed_by: reviewed_by,
                },
                success: function (response) {
                    if (response != 'error') {
                        $(".hs_updated_alert span").show('New Entery Added');
                        $(".hs_updated_alert").show();
                        if ($('#tab1 table td[colspan="9"]')) {
                            $('#tab1 table td[colspan="9"]').remove();
                        }
                        $("#tab1 table tbody").append(response);
                        $('.hs_select').select2();
                        $('.submit-entry').show();
                        $('.hs_hide_tr_select').hide();
                        $(".add-field[name='online_offline']").removeClass('hs_err');
                        $(".add-field[name='profile_name']").removeClass('hs_err');
                        $(".add-field[name='billing_status']").removeClass('hs_err');
                        $(".add-field[name='notes']").val('');
                        $("table tr:first Select option:first").prop("selected", true);
                        $("table tr:first Select").trigger("change");
                    } else {
                        $(".hs_alert span").html('somthing went wrong');
                        $(".hs_alert").show();
                    }
                },
                error: function (error) {
                    $(".hs_alert span").html('somthing went wrong');
                    $(".hs_alert").show();
                }
            });
        }
    });
    //add new row js end //
    //Submission of entries js start //
    $(".performance_data_results_wrap .submit-entry").click(function () {
        $('.alert').hide();
        var number_of_hours = 0;
        $(".edit_h").each(function () {
            number_of_hours += parseFloat($(this).val());
        });
        sub_date = $(this).data('sub_date');
        $.ajax({
            url: ajax_url,
            type: 'POST',
            data: {
                action: 'submit_performance_entry',
                number_of_hours: number_of_hours,
                c_date: sub_date,
                user_id: userId
            },
            success: function (response) {
                if (response == 'submitted') {
                    $(".hs_updated_alert span").html('The Record Was submitted');
                    $(".hs_updated_alert").show();
                    setTimeout(function () {
                        location.reload();
                    }, 200);
                } else {
                    $(".hs_alert span").html('somthing went wrong');
                    $(".hs_alert").show();
                }
            },
            error: function (error) {
                $(".hs_alert span").html('somthing went wrong');
                $(".hs_alert").show();
            }
        });
    });
    //Submission of entries js end //
    //Custom time calculation on select change js start//
    $(document).on("change", '.hs_custom_time select', function () {
        let row = $(this).closest('tr');
        var hs_hour = row.find('.hs_custom_time select[data-id="hs_hour"]').val();
        var hs_min = row.find('.hs_custom_time select[data-id="hs_min"]').val();
        var hs_cal = ((parseInt(hs_min) / 60) + parseInt(hs_hour)).toFixed(2);
        row.find(".hs_custom_time input[name='number_of_hours']").val(hs_cal);
    });
    $('.hs_select').select2();
    $(document).on("click", ".hs_emp_view", function () {
        $('.hs_loader').show();
        $('.emp_modal_table').hide();
        let tr_date = $(this).data('date');
        $(".hs_tr_filter_date").html(tr_date);
        $.ajax({
            url: ajax_url,
            type: 'POST',
            data: {
                action: 'hs_emp_view',
                start_date: tr_date
            },
            success: function (response) {
                response = $.parseJSON(response);
                let table_new_html = '';
                if (response.length > 0) {
                    $.each(response, function (index, item) {
                        let hs_cal = parseFloat(item.number_of_hours);
                        let hs_hour = Math.floor(hs_cal);
                        let hs_min = Math.round((hs_cal - hs_hour) * 60);
                        let hs_p_name = '';
                        if (item.profile_name != '0' && item.profile_name != 0 && item.profile_name != '') {
                            hs_p_name = item.profile_name;
                        }
                        table_new_html += '<tr><td>' + item.project + '</td><td>' + item.online_offline + '</td><td>' + hs_hour + '</td><td>' + hs_min + '</td><td>' + item.billing_status + '</td><td>' + hs_p_name + '</td><td>' + item.notes + '</td></tr>';
                    });
                } else {
                    table_new_html += '<tr><td colspan="7">No Result Found</td></tr>';
                }
                $(".emp_modal_table tbody").html(table_new_html)
                $(".emp_modal_table ").show();
                $('.hs_loader').hide();
            },
        });
    });
    let today = new Date().toISOString().split('T')[0];
    $('.from_wrap  input[name="start_date"]').val(today);
    $('.from_wrap  input[name="end_date"]').val(today);
    $('#tab4 .from_wrap  input[name="end_date"]').val(today);
    $("#tab4 .from_wrap .hs_search_btn").click(function () {
        var currentDate = new Date().toISOString().split('T')[0];
        let start_date = $('#tab4 .from_wrap  input[name="start_date"]').val()
        let end_date = $('#tab4 .from_wrap  input[name="end_date"]').val()
        if ((start_date <= currentDate) && (end_date <= currentDate) && start_date <= end_date) {
            $.ajax({
                url: ajax_url,
                type: 'POST',
                data: {
                    action: 'hs_emp_search',
                    start_date: start_date,
                    end_date: end_date
                },
                success: function (response) {
                    $('#tab4 table tbody').html(response);
                },
            });
        } else {
            alert("Please select valid date range.");
        }
    });
    //Custom time calculation on select change js END//
    //Team lead view and search functionality start //
    $(document).on("click", ".hs_team_lead_view", function () {
        let tab = $(this).closest('.hs_tl_search_wrap ');
        $('.hs_loader').show();
        $('#team_Lead_modal .modal-body').hide();
        let tr_start_date = $(this).data('start_date');
        let tr_user = $(this).data('user');
        let tr_user_id = $(this).data('user_id');
        $(".hs_user").attr('data-user_id', tr_user_id);
        $(".hs_user").attr('data-date', tr_start_date);
        $(".hs_user").html(tr_user);
        let tr_end_date = $(this).data('end_date');
        $.ajax({
            url: ajax_url,
            type: 'POST',
            data: {
                action: 'hs_emp_view',
                start_date: tr_start_date,
                end_date: tr_end_date,
                user_id: tr_user_id,
            },
            success: function (response) {
                response = $.parseJSON(response);
                let table_new_html = '';
                if (response.length > 0) {
                    $.each(response, function (index, item) {
                        let hs_cal = parseFloat(item.number_of_hours);
                        let hs_hour = Math.floor(hs_cal);
                        let hs_min = Math.round((hs_cal - hs_hour) * 60);
                        let hs_p_name = '';
                        if (item.profile_name != '0' && item.profile_name != 0 && item.profile_name != '') {
                            hs_p_name = item.profile_name;
                        }
                        table_new_html += '<tr><td>' + item.project + '</td><td>' + item.online_offline + '</td><td>' + hs_hour + '</td><td>' + hs_min + '</td><td>' + item.billing_status + '</td><td>' + hs_p_name + '</td><td>' + item.notes + '</td></tr>';
                    });
                    $('.modal_team_table_btn_wrap').show();
                } else {
                    table_new_html += '<tr><td colspan="7">No Result Found</td></tr>';
                    $('.modal_team_table_btn_wrap').hide();
                }
                $(".team_modal_table tbody").html(table_new_html);
                if (tab.attr('id') != 'tab3') {
                    $('.modal_team_table_btn_wrap').hide();
                }
                $("#team_Lead_modal .modal-body").show();
                $('.hs_loader').hide();
            },
        });
    });
    //Team lead search button functionality start //
    $(".hs_tl_search_wrap .hs_tl_search_btn").click(function () {
        let tab = $(this).closest('.hs_tl_search_wrap');
        var currentDate = new Date().toISOString().split('T')[0];
        let start_date = tab.find('.from_wrap  input[name="start_date"]').val();
        let end_date = tab.find('.from_wrap  input[name="end_date"]').val();
        let user_id = tab.find('.from_wrap  select[name="user_id"]').val();
        if (start_date <= currentDate) {
            $.ajax({
                url: ajax_url,
                type: 'POST',
                data: {
                    action: 'hs_emp_search_tl',
                    start_date: start_date,
                    end_date: end_date,
                    user_id: user_id
                },
                success: function (response) {
                    tab.find('table tbody').html(response);
                    if (!end_date) {
                        let productive_hours = 0;
                        let non_productive_hours = 0;
                        let no_work_hours = 0;
                        tab.find('table tbody tr').each(function () {
                            var pTd = $(this).find('td:eq(1)');
                            var npTd = $(this).find('td:eq(2)');
                            var nwTd = $(this).find('td:eq(3)');
                            productive_hours += parseFloat(pTd.text()) || 0;
                            non_productive_hours += parseFloat(npTd.text()) || 0;
                            no_work_hours += parseFloat(nwTd.text()) || 0;
                        });
                        totalhours = productive_hours + non_productive_hours + no_work_hours;
                        if (totalhours > 0) {
                            tab.find(".chart_non_productive").html(Math.round((non_productive_hours / totalhours) * 100) + '%');
                            tab.find(".chart_productive").html(Math.round((productive_hours / totalhours) * 100) + '%');
                            tab.find(".chart_no_work").html(Math.round((no_work_hours / totalhours) * 100) + '%');
                        } else {
                            tab.find(".chart_non_productive").html('0%');
                            tab.find(".chart_productive").html('0%');
                            tab.find(".chart_no_work").html('0%');
                        }
                        if (productive_hours > non_productive_hours && productive_hours > no_work_hours) {
                            total_hours = productive_hours;
                        } else if (non_productive_hours > productive_hours && non_productive_hours > no_work_hours) {
                            total_hours = non_productive_hours;
                        } else {
                            total_hours = no_work_hours;
                        }
                        tab3Chart.data.labels = ['Billable ' + productive_hours + ')', 'Non-Billable (' + non_productive_hours + ')', 'No-Work (' + no_work_hours + ')'];
                        tab3Chart.data.datasets[0].data = [productive_hours, non_productive_hours, no_work_hours];
                        tab3Chart.options.scales.y.max = total_hours;
                        tab3Chart.update();
                    }
                },
            });
        } else {
            alert("Please select valid date range.");
        }
    });
    //Team lead search functionality end//
});
