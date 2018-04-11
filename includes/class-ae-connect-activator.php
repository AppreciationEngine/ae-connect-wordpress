<?php

/**
 * Fired during plugin activation
 *
 * @link       avery.dev.appreciationengine.com
 * @since      1.0.0
 *
 * @package    Ae_Connect
 * @subpackage Ae_Connect/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Ae_Connect
 * @subpackage Ae_Connect/includes
 * @author     Appreciation Engine <avery@appreciationengine.com>
 */
class Ae_Connect_Activator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate() {
        Ae_Connect_Activator::render_default_login_page();
        Ae_Connect_Activator::render_error_page();
    }

    public static function render_default_login_page() {
        // Code snippits used from http://jafty.com/blog/tag/programmatically-adding-pages-to-wordpress/
        $new_page_title = 'Login/Signup';

        $new_page_content = ' [ae-form]';

        $page = get_page_by_title($new_page_title);

        if (empty($page)) {
            $new_page = array(
                'post_type' => 'page',
                'post_title' => $new_page_title,
                'post_content' => $new_page_content,
                'post_status' => 'publish',
                'post_author' => 1,
                'post_name' => 'ae-connect-' . $new_page_title
            );
            $postID = wp_insert_post($new_page);
        } elseif (strtolower($page->post_status) == 'trash') {
            $page->post_status = 'publish';
            $postID = wp_update_post($page);
        } else {
            $page->post_status = 'publish';
            $postID = wp_update_post($page);
        }

        $ae_connect_pages = get_option('ae_connect_custom_pages');
        $ae_connect_pages = !empty($ae_connect_pages) ? $ae_connect_pages : array();

        array_push($ae_connect_pages, $postID);
        update_option('ae_connect_custom_pages', $ae_connect_pages);

        // store page ID by the name of the page
        update_option('ae-connect-' . $new_page_title, $postID);
    }

    /**
     * Renders display page for the AE Connect Error Page
     * @return Null
     */
    public static function render_error_page() {
        // Code snippits used from http://jafty.com/blog/tag/programmatically-adding-pages-to-wordpress/
        $new_page_title = 'Sorry, there was a problem';

        $new_page_content = '<p id="error-message" style="color:red;"></p>
    <div style="display:none;" id="ae-signup-error">
    </div>
    <div style="display:none" id="ae-email-collision"></div>';

        $page = get_page_by_title($new_page_title);

        if (empty($page)) {
            $new_page = array(
                'post_type' => 'page',
                'post_title' => $new_page_title,
                'post_content' => $new_page_content,
                'post_status' => 'publish',
                'post_author' => 1,
                'post_name' => 'ae-connect-' . $new_page_title
            );
            $postID = wp_insert_post($new_page);
        } elseif (strtolower($page->post_status) == 'trash') {
            $page->post_status = 'publish';
            $postID = wp_update_post($page);
        } else {
            $page->post_status = 'publish';
            $postID = wp_update_post($page);
        }

        $ae_connect_pages = get_option('ae_connect_custom_pages');
        $ae_connect_pages = !empty($ae_connect_pages) ? $ae_connect_pages : array();

        array_push($ae_connect_pages, $postID);
        update_option('ae_connect_custom_pages', $ae_connect_pages);
    }

    /**
     * Renders display page for the AE Connect Collision Resolution Thank You Page
     * @return Null
     */
    public static function render_thank_you_page() {
        // Code snippits used from http://jafty.com/blog/tag/programmatically-adding-pages-to-wordpress/
        $new_page_title = 'AE Connect Thank You Page';

        $new_page_content = '<p id="thankyou-message"></p>';

        $page = get_page_by_title($new_page_title);

        if (empty($page)) {
            $new_page = array(
                'post_type' => 'page',
                'post_title' => $new_page_title,
                'post_content' => $new_page_content,
                'post_status' => 'publish',
                'post_author' => 1,
            );
            $postID = wp_insert_post($new_page);
        } elseif (strtolower($page->post_status) == 'trash') {
            $page->post_status = 'publish';
            $postID = wp_update_post($page);
        } else {
            $page->post_status = 'publish';
            $postID = wp_update_post($page);
        }

        $ae_connect_pages = get_option('ae_connect_custom_pages');
        $ae_connect_pages = !empty($ae_connect_pages) ? $ae_connect_pages : array();

        array_push($ae_connect_pages, $postID);
        update_option('ae_connect_custom_pages', $ae_connect_pages);
    }

}
