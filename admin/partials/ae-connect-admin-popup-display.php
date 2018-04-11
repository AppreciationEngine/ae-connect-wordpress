<?php
/**
 * Popup Styling Settings Submenu Page display
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
    <?php
    // $options_group is A settings group name. This should match the group name used in register_setting().
    $options_groups = array(
        'settings' => $this->plugin_name . '-popup-settings',
        'text' => $this->plugin_name . '-popup-text',
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
                   <?php echo ucwords($tab); ?>
            </a>
        <?php endforeach; ?>
    </h2>
    <!--    utilizes the Wordpress settings API-->
    <form action="options.php" method="post">
        <?php
        foreach ($options_groups as $tab => $options_group) {
            if ($active_tab == $tab) {
                settings_fields($options_group);
                do_settings_sections($options_group);
                submit_button();
            }
        }
        ?>
    </form>

</div>
