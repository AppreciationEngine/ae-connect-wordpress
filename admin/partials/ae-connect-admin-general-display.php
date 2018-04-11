<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       avery.dev.appreciationengine.com
 * @since      1.0.0
 *
 * @package    Ae_Connect
 * @subpackage Ae_Connect/admin/partials
 */
function render_opt_in_fields() {
    settings_fields('ae-connect-general-opt-ins');

    // Base opt-in array structure
    // $base_array = array( array('label' => '', 'segmentid' => '', 'checked' => '') )
    $opt_ins_array = get_option('ae-connect-general-opt-ins', array(array()));

    if (!array_key_exists('label', $opt_ins_array[0]))
        $opt_ins_array[0]['label'] = '';

    if (!array_key_exists('segmentid', $opt_ins_array[0]))
        $opt_ins_array[0]['segmentid'] = '';

    if (!array_key_exists('checked', $opt_ins_array[0]))
        $opt_ins_array[0]['checked'] = false;

    // var_dump($opt_ins_array);

    echo '<h2>Opt Ins</h2>';
    echo '<table class="form-table" id="opt-ins-table">'; // Start of table

    $i = 0;
    foreach ($opt_ins_array as $opt_in) {
        echo '<tbody class="opt-in">';

        echo '<tr><th>Opt In Fields</th>
            <td><button onclick="return removeParent(this);" name="remove_opt_in" id="remove_opt_in" >Remove</button></td>
        </tr>';

        echo '<tr>'; // Label row

        echo '<th scope="row">Label</th>';
        // echo '<div class="ae_opt-in">';

        echo '<td><input required type="text" name="ae-connect-general-opt-ins[' . $i . '][label]"
        value="' . htmlentities($opt_ins_array[$i]['label'], ENT_QUOTES, 'UTF-8') . '"' . '></td>';

        echo '</tr>'; // End label row


        echo '<tr>'; // Segmentid row

        echo '<th scope="row">Segment ID</th>';

        echo '<td><input required type="text" name="ae-connect-general-opt-ins[' . $i . '][segmentid]"
        value="' . htmlentities($opt_ins_array[$i]['segmentid'], ENT_QUOTES, 'UTF-8') . '"' . ' ></td>';

        echo '</tr>'; // End Segmentid row


        echo '<tr>'; // Checked row

        echo '<th scope="row">Enable By Default</th>';

        if (empty($opt_ins_array[$i]['checked'])) {
            $opt_ins_array[$i]['checked'] = false;
        }

        $checked = htmlentities($opt_ins_array[$i]['checked'], ENT_QUOTES, 'UTF-8') ? 'checked' : '';

        if (empty($checked)) {
            $opt_ins_array[$i]['checked'] = false;
        }


        echo '<td><input type="checkbox" name="ae-connect-general-opt-ins[' . $i . '][checked]"
        value="true" ' . $checked . '></td>';

        echo '</tr>'; // End checked row

        echo '</tbody>';
        $i++;
    }

    echo '</table>';

    echo '<p><button onclick="return addNewOptIn();" name="add_opt_in" id="add_opt_in" >Add a New Opt In</button></p>';
    submit_button();
}
?>

<div class="wrap">

    <h2><?php echo esc_html(get_admin_page_title()); ?></h2>
    <?php
// $options_group is A settings group name. This should match the group name used in register_setting().
    $options_groups = array(
        'settings' => $this->plugin_name . '-general-settings',
        'social' => $this->plugin_name . '-general-social',
        'user_fields' => $this->plugin_name . '-general-user-fields',
        'email' => $this->plugin_name . '-general-email',
        'opt-ins' => $this->plugin_name . '-general-opt-ins'
    );
    ?>

    <?php
    $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'settings';
    ?>

    <h2 class="nav-tab-wrapper">
        <?php foreach ($options_groups as $tab => $options_group): ?>
            <a href="?page=<?php echo $options_groups['settings']; ?>&tab=<?php echo $tab; ?>"
               class="nav-tab <?php
               if ($active_tab == $tab) {
                   echo 'nav-tab-active';
               } else {
                   echo '';
               }
               ?>">
                   <?php echo str_replace('_', ' ', ucwords($tab)); ?>
            </a>
        <?php endforeach; ?>
    </h2>
    <?php settings_errors(); ?>

    <!--    utilizes the Wordpress settings API-->
    <form action="options.php" method="post">
        <?php
        if ($active_tab == 'opt-ins') {
            render_opt_in_fields();
        } else {
            foreach ($options_groups as $tab => $options_group) {
                if ($active_tab == $tab) {
                    settings_fields($options_group);
                    do_settings_sections($options_group);
                    submit_button();
                }
            }
        }
        ?>
    </form>

</div>

<?php
function ae_custom_admin_notice() { ?>

	<div class="notice notice-success is-dismissible">
		<p><?php echo 'Successfully updated fields.'; ?></p>
	</div>

<?php }
add_action('admin_notices', 'shapeSpace_custom_admin_notice');
?>
