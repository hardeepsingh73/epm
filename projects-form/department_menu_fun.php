<?php
// Function to display project department settings
function project_depatment_settings()
{
?>
    <div class="wrap">
        <h2 class="nav-tab-wrapper">
            <a href="javascript:void(0);" data-id="tab-1" class="nav-tab nav-tab-active">Depatment Name</a>
        </h2>
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
        global $wpdb;
        $depatment_name_table_name = $wpdb->prefix . 'depatment_name';
        $departments = $wpdb->get_results(
            "SELECT * FROM $depatment_name_table_name"
        );
        ?>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.js">
        </script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css" rel="stylesheet" />
        <section id="tab-1" class="tab-content active">
            <form action="" method="post" id="depatment_name_form">
                <select name="depatment_name" id="depatment_name">
                    <?php
                    foreach ($departments as $department) {
                    ?>
                        <option value="<?php echo $department->depatment_name; ?>">
                            <?php echo $department->depatment_name; ?>
                        </option>
                    <?php
                    }
                    ?>
                </select>
                <div style="text-align:end;margin: 20px;">
                    <input type="submit" value="Submit" name="submit" class="page-title-action">
                </div>
            </form>
        </section>
    </div>
    <script>
        jQuery(document).ready(function() {
            var depatment_name = jQuery('#depatment_name').select2({
                multiple: true,
                placeholder: 'Add Depatment Name',
                allowClear: false,
                tags: true
            });
            depatment_name.val(<?php echo json_encode(wp_list_pluck($departments, 'depatment_name')); ?>).trigger('change');
            jQuery('#depatment_name_form').submit(function(e) {
                e.preventDefault();
                jQuery.ajax({
                    type: 'POST',
                    url: "<?php echo admin_url('admin-ajax.php'); ?>",
                    data: {
                        action: 'update_depatment_name',
                        depatment_name: jQuery('#depatment_name').val(),
                    }
                });
            });
        });
    </script>
<?php
}
// Function to update department name
add_action('wp_ajax_update_depatment_name', 'update_depatment_name_callback');
function update_depatment_name_callback()
{
    global $wpdb;
    $depatment_name_table_name = $wpdb->prefix . 'depatment_name';
    foreach ($_POST['depatment_name'] as $value) {
        $existing_department = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $depatment_name_table_name WHERE depatment_name = %s", $value)
        );
        if (!$existing_department) {
            $wpdb->insert(
                $depatment_name_table_name,
                array(
                    'depatment_name' => $value,
                )
            );
        }
    }
    wp_die();
}
