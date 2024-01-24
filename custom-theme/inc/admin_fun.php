<?php
//to add menu option in admin panel
function register_snipshot_menu()
{
    register_nav_menu('header-menu', __('header'));
}
add_action('init', 'register_snipshot_menu');

// department and team lead fields hide for employee and Team lead
add_action('admin_head', 'hs_hide_fields');
function hs_hide_fields()
{
    if (current_user_can('administrator')) {
        return;
    }
?>
    <style>
        .hs_hide_fiels {
            display: none;
        }
    </style>
<?php
}
// to design admin login page add css in style tag
function admin_design()
{
?>
    <style type="text/css">
        div#login {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            width: 35%;
            padding: unset;
        }
        div#login h1 {
            margin-top: 20px;
        }
        div#login h1,
        div#login form {
            width: 100%;
        }
        form#loginform {
            padding: 60px 30px;
            border-radius: 20px;
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
        }
        div#login p#nav {
            margin: 16px 0px;
        }
        input#wp-submit {
            padding: 2px 100px;
        }
        p.forgetmenot {
            padding: 6px 0;
        }
        .login h1 a {
            background-image: url('http://epm-dev.techarchsoftwares.com/wp-content/uploads/2023/11/image.webp') !important;
            width: 160px !important;
            height: 65px !important;
            background-size: unset !important;
        }
        #backtoblog {
            display: none;
        }
        @media(max-width:767px) {
            div#login {
                width: 90%;
            }
            form#loginform {
                padding: 30px 20px;
                border-radius: 20px;
                box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
            }
        }
    </style>
<?php
}
add_action('login_enqueue_scripts', 'admin_design');
// Add heading to the WordPress login form
function login_form_heading()
{
?>
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
            var loginDiv = document.getElementById("login");
            if (loginDiv) {
                var existingHeading = document.querySelector("h1");
                var newContent = document.createElement('span');
                newContent.innerHTML = 'Employee Performance Management';
                existingHeading.appendChild(newContent);
                var myLink = document.getElementsByTagName("a")[0];
                myLink.setAttribute("href", "https://techarchsoftwares.com/");
            }
        });
    </script>
<?php
}
add_action('login_head', 'login_form_heading');
// Add Department field to user profile
function add_department_field($user)
{
    // If $user is a string, set it to an empty array to avoid errors
    $user_id = is_object($user) ? $user->ID : '';
?>
    <table class="form-table hs_hide_fiels">
        <tr>
            <th><label for="department">Department</label></th>
            <td>
                <select name="department" id="department">
                    <?php
                    global $wpdb;
                    $depatment_name_table_name = $wpdb->prefix . 'depatment_name';
                    $departments = $wpdb->get_results(
                        "SELECT * FROM $depatment_name_table_name"
                    );
                    foreach ($departments as $department) {
                    ?>
                        <option value="<?php echo $department->id; ?>" <?php selected(get_user_meta($user_id, 'department', true), $department->id); ?>><?php echo $department->depatment_name; ?></option>
                    <?php
                    }
                    ?>
                </select>
            </td>
        </tr>
    </table>
<?php
}
add_action('show_user_profile', 'add_department_field');
add_action('edit_user_profile', 'add_department_field');
add_action('user_new_form', 'add_department_field');
//save departmet field data 
function save_department_field($user_id)
{
    if (current_user_can('edit_user', $user_id)) {
        update_user_meta($user_id, 'department', sanitize_text_field($_POST['department']));
    }
}
add_action('personal_options_update', 'save_department_field');
add_action('edit_user_profile_update', 'save_department_field');

//  Add TL field for add/update User
function add_tl_field($user)
{
    // If $user is a string, set it to an empty array to avoid errors
    $user_id = is_object($user) ? $user->ID : '';
?>
    <table class="form-table hs_hide_fiels">
        <tr>
            <th><label for="team_lead">Team Lead</label></th>
            <td>
                <select name="team_lead" id="team_lead">
                    <?php
                    $team_leads = get_users(array('role' => 'contributor'));
                    foreach ($team_leads as $team_lead) {
                    ?>
                        <option value="<?php echo  $team_lead->ID; ?>" <?php selected(get_user_meta($user_id, 'team_lead', true), $team_lead->ID); ?>><?php echo get_userdata($team_lead->ID)->display_name; ?></option>
                    <?php
                    }
                    ?>
                </select>
            </td>
        </tr>
    </table>
<?php
}
add_action('show_user_profile', 'add_tl_field');
add_action('edit_user_profile', 'add_tl_field');
add_action('user_new_form', 'add_tl_field');
//save team lead field data 
function save_team_lead_field($user_id)
{
    if (current_user_can('edit_user', $user_id)) {
        update_user_meta($user_id, 'team_lead', sanitize_text_field($_POST['team_lead']));
    }
}
add_action('personal_options_update', 'save_team_lead_field');
add_action('edit_user_profile_update', 'save_team_lead_field');
//change user role name
function wps_change_role_name()
{
    global $wp_roles;
    if (!isset($wp_roles))
        $wp_roles = new WP_Roles();
    $wp_roles->roles['contributor']['name'] = 'Team Lead';
    $wp_roles->role_names['contributor'] = 'Team Lead';
    $wp_roles->roles['subscriber']['name'] = 'Employee';
    $wp_roles->role_names['subscriber'] = 'Employee';
}
add_action('init', 'wps_change_role_name');
//redirect user
function custom_redirect_subscriber($redirect_to, $request, $user)
{
    if (isset($user->roles) && is_array($user->roles)) {
        if (in_array('subscriber', $user->roles)) {
            return home_url('/performance-form');
        }
    }
    return $redirect_to;
}
add_filter('login_redirect', 'custom_redirect_subscriber', 10, 3);
// Add Customizer Section
function custom_theme_customizer_sections($wp_customize)
{
    $wp_customize->add_section('copyright_section', array(
        'title'    => __('Copyright', 'your_theme_textdomain'),
        'priority' => 200,
    ));
    // Add Copyright Setting
    $wp_customize->add_setting('copyright_content', array(
        'default'           => '',
        'sanitize_callback' => 'wp_kses_post',
    ));
    // Add Copyright Control
    $wp_customize->add_control('copyright_content', array(
        'label'    => __('Copyright Content', 'your_theme_textdomain'),
        'section'  => 'copyright_section',
        'type'     => 'textarea',
    ));
}
add_action('customize_register', 'custom_theme_customizer_sections');
add_role('bde_role', 'Business Development Executive', array(
    'read' => true,
    'level_0' => true,
    'read_private_pages' => true,
    'read_private_posts' => true,
    'read_private_media' => true,
));

// Assign BDE Role
function custom_add_menu_access()
{
    $new_role = get_role('bde_role');
    $new_role->add_cap('manage_options');
}
add_action('init', 'custom_add_menu_access');

// Redirect users to the home page after login
function custom_redirect_after_login()
{
    if (!current_user_can('administrator')) {
        wp_redirect(home_url());
        exit();
    }
}
add_action('wp_login', 'custom_redirect_after_login');
