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
?>

<div class="wrap ae-connect-admin">

    <h2><?php echo esc_html(get_admin_page_title()); ?></h2>

    <p class="intro">
        <strong>AE Connect (© Copyright Musichype Inc 2018)</strong> is the curabitur vitae cras adipiscing parturient ullamcorper mi a ridiculus a condimentum a primis senectus fringilla viverra.
    </p>

    <a class="help-link" href="https://docs.google.com/document/d/11bfWTmsilCbiUlPpRD84P5TlLKeBHZElXN6JjFFGwtI/edit" target="_blank"><span class="dashicons-before dashicons-editor-help"></span>Get Help for our AE Connect Wordpress Plugin</a>

    <!--    utilizes the Wordpress settings API-->
    <form action="options.php" method="post">
        <?php
        // $options_group is A settings group name. This should match the group name used in register_setting().
        $options_group = $this->plugin_name;

        settings_fields($options_group);
        // Takes the name of your PAGE as the argument
        do_settings_sections($options_group);

        submit_button();
        ?>
    </form>

</div>
