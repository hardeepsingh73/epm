<?php
// Send Mail to TL After submit the Performance sheet
function send_email($current_date)
{
    global $wpdb;
    $current_user_id = get_current_user_id();
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
    $dept_table_name = $wpdb->prefix . 'depatment_name';
    $user_dept = get_user_meta(get_current_user_id(), 'department');
    $user_dept = $user_dept[0];
    $tr_dept = $wpdb->prepare("SELECT * FROM $dept_table_name WHERE id = %d",  $user_dept);
    $tr_dept_results = $wpdb->get_results($tr_dept);
    $tr_dept = $tr_dept_results[0]->depatment_name;
    $args = array(
        'role' => 'contributor',
    );
    $contributors = get_users($args);
    foreach ($contributors as $contributor) {
        $c_dept = get_user_meta($contributor->data->ID, 'department');
        if ($c_dept[0] == $user_dept) {
            $c_id = $contributor->data->ID;
            $performance_sql_select = "SELECT * FROM $performance_table_name  where `user_id` = '$current_user_id' AND `date` = '$current_date' And `reviewed_by`='$c_id'";
            $performance_data_results = $wpdb->get_results($performance_sql_select, ARRAY_A);
            $to = $contributor->data->user_email;
            $subject = 'Employee Performance Sheet';
            $message = '<table width="100%" align="center" cellspacing="0" cellpadding="0">
	<tbody>
		<tr>
			<td>
				<table bgcolor="#ffffff" align="center" width="600"
					style="background-color: #ffffff;max-width: 600px;width: 600px;font-family: Arial,  sans-serif;margin: 0 auto;padding: 0;-webkit-border-horizontal-spacing: 0px;-webkit-border-vertical-spacing: 0px;table-layout: fixed;"
					cellspacing="0" cellpadding="0">
					<tbody>
						<!-- Header start here -->
						<tr>
							<td align="center"
								style="text-align: center;border-bottom: 10px solid white;background: #ffffff; border-top: 10px solid white;padding: 10px 0; vertical-align: middle;"
								valign="middle">
								<a href="https://epm-dev.techarchsoftwares.com/" target="_blank"
									style="display: inline-block;">
									<img src="http://epm-dev.techarchsoftwares.com/wp-content/uploads/2024/01/mail_logo.png"
										alt="Logo" style="width: 150px;" width="150">
								</a>
							</td>
						</tr>
						<!-- table start here -->
						<tr>
							<td>
								<table bgcolor="#ffffff" align="center" width="599"
									style="background-color: #ffffff;max-width: 599px;width: 599px;font-family: Arial,  sans-serif;margin: 0 auto;padding: 0;-webkit-border-horizontal-spacing: 0px;-webkit-border-vertical-spacing: 0px;border-collapse: collapse;"
									cellspacing="0" cellpadding="0">
									<tbody>
										<tr>
											<td style="padding: 10px;color: #333333; width: 50%; vertical-align: top;"
												width="50%" valign="top">
												<p style="margin: 0;font-size: 14px;font-weight: 600;color: #333333;">
													Name :- </p>
											</td>
											<td style="padding: 10px;color: #333333; width: 50%; vertical-align: top;"
												width="50%" valign="top">
												<p style="margin: 0;font-size: 14px;font-weight: 400;color: #333333;">
													' . wp_get_current_user()->display_name . '</p>
											</td>
										</tr>
										<tr>
											<td style="border-top: 1px solid #bfc8d0; padding: 10px;color: #333333; width: 50%; vertical-align: top;"
												width="50%" valign="top">
												<p style="margin: 0;font-size: 14px;font-weight: 600;color: #333333;">
													Email :-</p>
											</td>
											<td style="border-top: 1px solid #bfc8d0; padding: 10px;color: #333333; width: 50%; vertical-align: top;"
												width="50%" valign="top">
												<p style="margin: 0;font-size: 14px;font-weight: 400;color: #333333;">
													<a href="mailto:' . wp_get_current_user()->user_email . '"
														style="margin: 0;font-size: 14px;font-weight: 400;color: #333333; display: inline-block;">
														' . wp_get_current_user()->user_email . '</a>
												</p>
											</td>
										</tr>
										<tr>
											<td style="border-top: 1px solid #bfc8d0; padding: 10px;color: #333333; width: 50%; vertical-align: top;"
												width="50%" valign="top">
												<p style="margin: 0;font-size: 14px;font-weight: 600;color: #333333;">
													Department :-</p>
											</td>
											<td style="border-top: 1px solid #bfc8d0; padding: 10px;color: #333333; width: 50%; vertical-align: top;"
												width="50%" valign="top">
												<p style="margin: 0;font-size: 14px;font-weight: 400;color: #333333;">
													' . $tr_dept . '
												</p>
											</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
						<tr>
							<td align="center"
								style="text-align: center;background-color: #e04a15;padding: 0;border-top: 0px solid #ffffff; border-bottom: 20px solid #ffffff; vertical-align: middle;"
								bgcolor="#e04a15" valign="middle">
								<p
									style="text-align: center;font-size: 18px;font-weight: 700;padding: 0px 15px;color: #ffffff;margin: 10px 0;">
									Performance Form Data</p>
							</td>
						</tr>
						<tr>
							<td>
								<table bgcolor="#ffffff" align="center" width="599"
									style="background-color: #ffffff;max-width: 599px;width: 599px;font-family: Arial,  sans-serif;margin: 0 auto;padding: 0;-webkit-border-horizontal-spacing: 0px;-webkit-border-vertical-spacing: 0px;border-collapse: collapse;"
									cellspacing="0" cellpadding="0">
									<tbody>
										<tr>
											<td style=" padding: 10px;color: #fff;border: 1px solid #333333;background:#e04a15; width: 75%; vertical-align: top;"
												width="75%" valign="top">
												<p style="margin: 0;font-size: 14px;font-weight: 600;color: #fff;">
													Project</p>
											</td>
											<td style="background:#e04a15;border: 1px solid #333333; padding: 10px;color: #fff; width: 25%; vertical-align: top;"
												width="25%" valign="top">
												<p style="margin: 0;font-size: 14px;font-weight: 400;color: #fff;">
													Time</p>
											</td>
										</tr>';
            foreach ($performance_data_results as $performance_row) {
                if ($performance_row['project_id'] != 0) {
                    $tr_project = $wpdb->prepare("SELECT * FROM $project_table_name WHERE id = %d", $performance_row['project_id']);
                    $tr_project_results = $wpdb->get_results($tr_project);
                    $tr_project = $tr_project_results[0]->project;
                }
                $message .= '<tr>
											<td style="border: 1px solid #bfc8d0; padding: 10px;color: #333333; width: 75%; vertical-align: top;"
												width="75%" valign="top">
												<p style="margin: 0;font-size: 14px;font-weight: 600;color: #333333;">
													' . $tr_project . '</p>
											</td>
											<td style="border: 1px solid #bfc8d0; padding: 10px;color: #333333; width: 25%; vertical-align: top;"
												width="25%" valign="top">
												<p style="margin: 0;font-size: 14px;font-weight: 400;color: #333333;">
													' . $performance_row['number_of_hours'] . '
												</p>
											</td>
										</tr>';
            }
            $message .= '</tbody>
								</table>
							</td>
						</tr>
						<!-- Footer start here -->
						<tr>
							<td align="center"
								style="text-align: center;background-color: #e04a15;padding: 0;border-top: 20px solid #ffffff; vertical-align: middle;"
								bgcolor="#e04a15" valign="middle">
								<p
									style="text-align: center;font-size: 12px;padding: 0px 15px;color: #ffffff;margin: 20px 0;">© 2023 Designed by Tech Arch Softwares Pvt. Ltd.</p>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>';
            $headers = array('Content-Type: text/html; charset=UTF-8');
            $result = wp_mail($to, $subject, $message, $headers);
            if (!$result) {
                echo 'Error sending email.';
            }
        }
    }
    $team_lead = get_user_meta(get_current_user_id(), 'team_lead');
    $to = get_userdata($team_lead[0])->user_email;
    $performance_sql_select = "SELECT * FROM $performance_table_name  where `user_id` = '$current_user_id' AND `date` = '$current_date'";
    $performance_data_results = $wpdb->get_results($performance_sql_select, ARRAY_A);
    $subject = 'Employee Performance Sheet';
    $message = '<table width="100%" align="center" cellspacing="0" cellpadding="0">
<tbody>
<tr>
    <td>
        <table bgcolor="#ffffff" align="center" width="600"
            style="background-color: #ffffff;max-width: 600px;width: 600px;font-family: Arial,  sans-serif;margin: 0 auto;padding: 0;-webkit-border-horizontal-spacing: 0px;-webkit-border-vertical-spacing: 0px;table-layout: fixed;"
            cellspacing="0" cellpadding="0">
            <tbody>
                <!-- Header start here -->
                <tr>
                    <td align="center"
                        style="text-align: center;border-bottom: 10px solid white;background: #ffffff; border-top: 10px solid white;padding: 10px 0; vertical-align: middle;"
                        valign="middle">
                        <a href="https://epm-dev.techarchsoftwares.com/" target="_blank"
                            style="display: inline-block;">
                            <img src="http://epm-dev.techarchsoftwares.com/wp-content/uploads/2024/01/mail_logo.png"
                                alt="Logo" style="width: 150px;" width="150">
                        </a>
                    </td>
                </tr>
                <!-- table start here -->
                <tr>
                    <td>
                        <table bgcolor="#ffffff" align="center" width="599"
                            style="background-color: #ffffff;max-width: 599px;width: 599px;font-family: Arial,  sans-serif;margin: 0 auto;padding: 0;-webkit-border-horizontal-spacing: 0px;-webkit-border-vertical-spacing: 0px;border-collapse: collapse;"
                            cellspacing="0" cellpadding="0">
                            <tbody>
                                <tr>
                                    <td style="padding: 10px;color: #333333; width: 50%; vertical-align: top;"
                                        width="50%" valign="top">
                                        <p style="margin: 0;font-size: 14px;font-weight: 600;color: #333333;">
                                            Name :- </p>
                                    </td>
                                    <td style="padding: 10px;color: #333333; width: 50%; vertical-align: top;"
                                        width="50%" valign="top">
                                        <p style="margin: 0;font-size: 14px;font-weight: 400;color: #333333;">
                                            ' . wp_get_current_user()->display_name . '</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border-top: 1px solid #bfc8d0; padding: 10px;color: #333333; width: 50%; vertical-align: top;"
                                        width="50%" valign="top">
                                        <p style="margin: 0;font-size: 14px;font-weight: 600;color: #333333;">
                                            Email :-</p>
                                    </td>
                                    <td style="border-top: 1px solid #bfc8d0; padding: 10px;color: #333333; width: 50%; vertical-align: top;"
                                        width="50%" valign="top">
                                        <p style="margin: 0;font-size: 14px;font-weight: 400;color: #333333;">
                                            <a href="mailto:' . wp_get_current_user()->user_email . '"
                                                style="margin: 0;font-size: 14px;font-weight: 400;color: #333333; display: inline-block;">
                                                ' . wp_get_current_user()->user_email . '</a>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border-top: 1px solid #bfc8d0; padding: 10px;color: #333333; width: 50%; vertical-align: top;"
                                        width="50%" valign="top">
                                        <p style="margin: 0;font-size: 14px;font-weight: 600;color: #333333;">
                                            Department :-</p>
                                    </td>
                                    <td style="border-top: 1px solid #bfc8d0; padding: 10px;color: #333333; width: 50%; vertical-align: top;"
                                        width="50%" valign="top">
                                        <p style="margin: 0;font-size: 14px;font-weight: 400;color: #333333;">
                                            ' . $tr_dept . '
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center"
                        style="text-align: center;background-color: #e04a15;padding: 0;border-top: 0px solid #ffffff; border-bottom: 20px solid #ffffff; vertical-align: middle;"
                        bgcolor="#e04a15" valign="middle">
                        <p
                            style="text-align: center;font-size: 18px;font-weight: 700;padding: 0px 15px;color: #ffffff;margin: 10px 0;">
                            Performance Form Data</p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table bgcolor="#ffffff" align="center" width="599"
                            style="background-color: #ffffff;max-width: 599px;width: 599px;font-family: Arial,  sans-serif;margin: 0 auto;padding: 0;-webkit-border-horizontal-spacing: 0px;-webkit-border-vertical-spacing: 0px;border-collapse: collapse;"
                            cellspacing="0" cellpadding="0">
                            <tbody>
                                <tr>
                                    <td style=" padding: 10px;color: #fff;border: 1px solid #333333;background:#e04a15; width: 75%; vertical-align: top;"
                                        width="75%" valign="top">
                                        <p style="margin: 0;font-size: 14px;font-weight: 600;color: #fff;">
                                            Project</p>
                                    </td>
                                    <td style="background:#e04a15;border: 1px solid #333333; padding: 10px;color: #fff; width: 25%; vertical-align: top;"
                                        width="25%" valign="top">
                                        <p style="margin: 0;font-size: 14px;font-weight: 400;color: #fff;">
                                            Time</p>
                                    </td>
                                </tr>';
    foreach ($performance_data_results as $performance_row) {
        if ($performance_row['project_id'] != 0) {
            $tr_project = $wpdb->prepare("SELECT * FROM $project_table_name WHERE id = %d", $performance_row['project_id']);
            $tr_project_results = $wpdb->get_results($tr_project);
            $tr_project = $tr_project_results[0]->project;
        }
        $message .= '<tr>
                                    <td style="border: 1px solid #bfc8d0; padding: 10px;color: #333333; width: 75%; vertical-align: top;"
                                        width="75%" valign="top">
                                        <p style="margin: 0;font-size: 14px;font-weight: 600;color: #333333;">
                                            ' . $tr_project . '</p>
                                    </td>
                                    <td style="border: 1px solid #bfc8d0; padding: 10px;color: #333333; width: 25%; vertical-align: top;"
                                        width="25%" valign="top">
                                        <p style="margin: 0;font-size: 14px;font-weight: 400;color: #333333;">
                                            ' . $performance_row['number_of_hours'] . '
                                        </p>
                                    </td>
                                </tr>';
    }
    $message .= '</tbody>
                        </table>
                    </td>
                </tr>
                <!-- Footer start here -->
                <tr>
                    <td align="center"
                        style="text-align: center;background-color: #e04a15;padding: 0;border-top: 20px solid #ffffff; vertical-align: middle;"
                        bgcolor="#e04a15" valign="middle">
                        <p
                            style="text-align: center;font-size: 12px;padding: 0px 15px;color: #ffffff;margin: 20px 0;">© 2023 Designed by Tech Arch Softwares Pvt. Ltd.</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </td>
</tr>
</tbody>
</table>';
    $headers = array('Content-Type: text/html; charset=UTF-8');
    $result = wp_mail($to, $subject, $message, $headers);
    if (!$result) {
        echo 'Error sending email.';
    }
    wp_die();
}
// mail if tl reject the record
function send_reject_email($user_id, $note)
{
    $user_obj = get_user_by('id', $user_id);
    $subject = 'Notice of Rejected Submission';
    $team_lead_id = get_user_meta($user_id, 'team_lead', true);
    $team_lead = get_user_by('id', $team_lead_id)->display_name;
    $to = $user_obj->user_email;
    $name = $user_obj->display_name;
    $message = '<table width="100%" align="center" cellspacing="0" cellpadding="0">
    <tbody>
        <tr>
            <td>
                <table bgcolor="#ffffff" align="center" width="600"
                    style="background-color:#ffffff;max-width:600px;width:600px;font-family:Arial,sans-serif;margin:0 auto;padding:0;table-layout:fixed"
                    cellspacing="0" cellpadding="0">
                    <tbody>
                        <tr>
                            <td align="center"
                                style="text-align:center;border-bottom:10px solid white;background:#ffffff;border-top:10px solid white;padding:10px 0;vertical-align:middle"
                                valign="middle">
                                <a href="https://epm-dev.techarchsoftwares.com/" style="display:inline-block"
                                    target="_blank"
                                    data-saferedirecturl="https://www.google.com/url?q=https://epm-dev.techarchsoftwares.com/&amp;source=gmail&amp;ust=1704774934209000&amp;usg=AOvVaw3oqqHB6Bzh-wZZutyUzJWv">
                                    <img src="http://epm-dev.techarchsoftwares.com/wp-content/uploads/2024/01/mail_logo.png"
                                        alt="Logo" style="width:150px" width="150" class="CToWUd" data-bit="iit">
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td align="left"
                                style="text-align:left; padding:0;border-top:20px solid #ffffff;vertical-align:middle"
                                valign="middle">
                                <p style="font-weight:600;padding:0px 10px;">
                                    Dear ' . $name . ',</p>
                                <p style="padding:0px 10px;">
                                    I hope this email finds you well. We appreciate your efforts in submitting the
                                    Performance Sheet. However, after careful
                                    review, we regret to inform you that your submission has been rejected.</p>';
    if ($note) {
        $message .= '<p style="padding:0px 10px;">Reasons for the rejection:- ' . $note . ' </p>';
    }
    $message .= '<p style="padding:0px 10px;">We understand that this may be disappointing, and we
                                    are more than happy to provide feedback or answer any questions you may have.
                                    Please feel free to reach out to ' . $team_lead . '.</p>
                            </td>
                        </tr>
                        <tr>
                            <td align="center"
                                style="text-align:center;background-color:#e04a15;padding:0;border-top:20px solid #ffffff;vertical-align:middle"
                                bgcolor="#e04a15" valign="middle">
                                <p
                                    style="text-align:center;font-size:12px;padding:0px 15px;color:#ffffff;margin:20px 0">
                                    © 2023 Designed by Tech Arch Softwares Pvt. Ltd.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>';
    $headers = array('Content-Type: text/html; charset=UTF-8');
    $result = wp_mail($to, $subject, $message, $headers);
    if (!$result) {
        echo 'Error sending email.';
    }
    wp_die();
}

// mail if tl approve the record
function send_approve_email($user_id, $note)
{
    $user_obj = get_user_by('id', $user_id);
    $subject = 'Approval Granted for Performance Sheet Submission';
    $team_lead_id = get_user_meta($user_id, 'team_lead', true);
    $team_lead = get_user_by('id', $team_lead_id)->display_name;
    $to = $user_obj->user_email;
    $name = $user_obj->display_name;
    $message = '<table width="100%" align="center" cellspacing="0" cellpadding="0">
    <tbody>
        <tr>
            <td>
                <table bgcolor="#ffffff" align="center" width="600"
                    style="background-color:#ffffff;max-width:600px;width:600px;font-family:Arial,sans-serif;margin:0 auto;padding:0;table-layout:fixed"
                    cellspacing="0" cellpadding="0">
                    <tbody>
                        <tr>
                            <td align="center"
                                style="text-align:center;border-bottom:10px solid white;background:#ffffff;border-top:10px solid white;padding:10px 0;vertical-align:middle"
                                valign="middle">
                                <a href="https://epm-dev.techarchsoftwares.com/" style="display:inline-block"
                                    target="_blank"
                                    data-saferedirecturl="https://www.google.com/url?q=https://epm-dev.techarchsoftwares.com/&amp;source=gmail&amp;ust=1704774934209000&amp;usg=AOvVaw3oqqHB6Bzh-wZZutyUzJWv">
                                    <img src="http://epm-dev.techarchsoftwares.com/wp-content/uploads/2024/01/mail_logo.png"
                                        alt="Logo" style="width:150px" width="150" class="CToWUd" data-bit="iit">
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td align="left"
                                style="text-align:left; padding:0;border-top:20px solid #ffffff;vertical-align:middle"
                                valign="middle">
                                <p style="font-weight:600;padding:0px 10px;">
                                    Dear ' . $name . ',</p>
                                <p style="padding:0px 10px;">I trust this message reaches you in good health. We would like to express our gratitude for your prompt submission of the Performance Sheet. After a thorough review, we are pleased to inform you that your submission has been approved.</p>';
    if ($note) {
        $message .= '<p style="padding:0px 10px;">Note:- ' . $note . ' </p>';
    }
    $message .= '<p style="padding:0px 10px;">Your efforts have not gone unnoticed, and we appreciate your commitment to excellence. If you have any further questions or require additional information, please do not hesitate to reach out to ' . $team_lead . '.</p>
                            </td>
                        </tr>
                        <tr>
                            <td align="center"
                                style="text-align:center;background-color:#e04a15;padding:0;border-top:20px solid #ffffff;vertical-align:middle"
                                bgcolor="#e04a15" valign="middle">
                                <p
                                    style="text-align:center;font-size:12px;padding:0px 15px;color:#ffffff;margin:20px 0">
                                    © 2023 Designed by Tech Arch Softwares Pvt. Ltd.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>';
    $headers = array('Content-Type: text/html; charset=UTF-8');
    $result = wp_mail($to, $subject, $message, $headers);
    if (!$result) {
        echo 'Error sending email.';
    }
    wp_die();
}
