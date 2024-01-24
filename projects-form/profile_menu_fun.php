<?php
function project_profile_settings()
{
?>
    <!-- Main container for the settings page -->
    <div class="wrap">
                <!-- Navigation tabs -->
        <h2 class="nav-tab-wrapper">
            <a href="javascript:void(0);" data-id="tab-1" class="nav-tab nav-tab-active">Profile Name</a>
        </h2>
        <!-- Styles for select dropdown -->
        <style>
            select {
                min-width: calc(100% - 20px);
            }
            input.select2-search__field {
                padding: 0 10px !important;
            }
        </style>
        <hr>
        <?php
         // Retrieve profile names from the database
        global $wpdb;
        $profile_name_table_name = $wpdb->prefix . 'profile_name';
        $profiles = $wpdb->get_results(
            "SELECT * FROM $profile_name_table_name"
        );
        ?>
        <!-- Include necessary CSS and JS files -->
        <link rel="stylesheet" href="<?php echo  plugin_dir_url(__FILE__); ?>assets/jquery-ui.css">
        <script src="<?php echo  plugin_dir_url(__FILE__); ?>assets/select2.js">
        </script>
        <link href="<?php echo  plugin_dir_url(__FILE__); ?>assets/select2.css" rel="stylesheet" />
        <!-- Profile Name Form Section -->
        <section id="tab-1" class="tab-content active">
            <!-- Dropdown to select profile names -->
            <form action="" method="post" id="profile_name_form">
                <select name="profile_name" id="profile_name">
                    <?php
                    // Display existing profile names in the dropdown
                    foreach ($profiles as $profile) {
                    ?>
                        <option value="<?php echo $profile->profile_name; ?>">
                            <?php echo $profile->profile_name; ?>
                        </option>
                    <?php
                    }
                    ?>
                </select>
                <!-- Submit button -->
                <div style="text-align:end;margin: 20px;">
                    <input type="submit" value="Submit" name="submit" class="page-title-action">
                </div>
            </form>
        </section>
    </div>
    <?php
    global $wpdb;
    $profile_name_table_name = $wpdb->prefix . 'profile_name';
    // Retrieve profile names again for use in JavaScript
    $profile_names = $wpdb->get_results(
        "SELECT * FROM $profile_name_table_name"
    );
    ?>
    <!-- JavaScript for handling profile names with select2 -->
    <script>
        jQuery(document).ready(function() {
            // Initialize select2 with options
            var profile_name = jQuery('#profile_name').select2({
                multiple: true,
                placeholder: 'Add Profile Name',
                allowClear: false,
                tags: true
            });
             // Pre-fill select2 with existing profile names
            profile_name.val(<?php echo json_encode(wp_list_pluck($profile_names, 'profile_name')); ?>).trigger('change');
            // AJAX submission for updating profile names
            jQuery('#profile_name_form').submit(function(e) {
                e.preventDefault();
                jQuery.ajax({
                    type: 'POST',
                    url: "<?php echo admin_url('admin-ajax.php'); ?>",
                    data: {
                        action: 'update_profile_name',
                        profile_name: jQuery('#profile_name').val(),
                    }
                });
            });
        });
    </script>
<?php
}
// Hook the AJAX action for updating profile names
add_action('wp_ajax_update_profile_name', 'update_profile_name_callback');
// Callback function for updating profile names
function update_profile_name_callback()
{
    global $wpdb;
    $profile_name_table_name = $wpdb->prefix . 'profile_name';
    // Loop through posted profile names and insert if not already existing
    foreach ($_POST['profile_name'] as $value) {
        $existing_department = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $profile_name_table_name WHERE profile_name = %s", $value)
        );
        // Insert if profile name does not exist
        if (!$existing_department) {
            $wpdb->insert(
                $profile_name_table_name,
                array(
                    'profile_name' => $value,
                )
            );
        }
    }
}
