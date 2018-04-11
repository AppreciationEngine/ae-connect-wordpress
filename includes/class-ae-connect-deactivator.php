<?php

/**
 * Fired during plugin deactivation
 *
 * @link       avery.dev.appreciationengine.com
 * @since      1.0.0
 *
 * @package    Ae_Connect
 * @subpackage Ae_Connect/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Ae_Connect
 * @subpackage Ae_Connect/includes
 * @author     Appreciation Engine <avery@appreciationengine.com>
 */
class Ae_Connect_Deactivator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function deactivate() {
        // remove all pages programmatically created by AE Connect
        $ae_connect_pages = get_option('ae_connect_custom_pages');
        if (empty($ae_connect_pages))
            $ae_connect_pages = array();

        foreach ($ae_connect_pages as $pageID) {
            wp_delete_post($pageID, true);
        }
    }

}
