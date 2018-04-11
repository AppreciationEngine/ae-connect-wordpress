<?php
/**
 * Provide a admin area view for the Performance Page
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       avery.dev.appreciationengine.com
 * @since      1.0.0
 *
 * @package    Ae_Connect
 * @subpackage Ae_Connect/admin/partials
 */
?>

<div class="wrap">

    <h2><?php echo esc_html(get_admin_page_title()); ?></h2>

    <!--    utilizes the Wordpress settings API-->
    <form action="options.php" method="post">
        <?php
        // $options_group is A settings group name. This should match the group name used in register_setting().
        $options_group = $this->plugin_name . '-performance';

        settings_fields($options_group);
        // Takes the name of your PAGE as the argument
        do_settings_sections($options_group);

        submit_button();
        ?>
    </form>

</div>
