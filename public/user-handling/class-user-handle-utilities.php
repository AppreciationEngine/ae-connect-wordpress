<?php

/**
 * User handling utilities for the plugin
 *
 * @link       avery.dev.appreciationengine.com
 * @since      1.0.0
 *
 * @package    Ae_Connect
 * @subpackage Ae_Connect/public
 */
class Ae_Connect_UserHandleUtilities {

    /**
     * @param array     $ae_user    raw ae_user
     *
     * @return true    If AE ID Exists:
     * @return true    If Service exists:
     * @return false   If CMS Email collision || no other case hit
     */
    public function ae_wp_user_validation($ae_user) {
        $ae_user_data = $ae_user['data'];
        // Check AE ID:
        if($this->match_ae_id($ae_user_data['ID'])) {
            return true;
        }

        // Check against services
        if($this->match_services($ae_user)) {
            return true;
        }

        $verified_email_wp_user = $this->get_user_with_verified_email($ae_user_data);

        // update user data and return true
        if (!empty($verified_email_wp_user)) {
            $v_wp_user = $verified_email_wp_user[0];
            $v_wp_user_id = $v_wp_user->ID;
            $this->update_ae_user_meta_data($ae_user, $v_wp_user_id);
            return true;
        }

        //check email collision:
        $wp_id_for_email = email_exists($ae_user_data['Email']);
        if (!empty($wp_id_for_email)) {
            // Is the user an AE user?
            $wp_ae_email = get_users(array('meta_key' => 'ae_connect_Email', 'meta_value' => $ae_user_data['Email']));
            if (empty($wp_ae_email)) { // Wp user has this email, but is not on record as an AE user (created before AE was installed)
                $this->update_ae_user_meta_data($ae_user, $wp_id_for_email);
                return true;
            } else { // Found an AE Email that matches the wordpress email, thus an AE user already exists and does not match the incoming user
                update_option('is_email_collision', true);
                return false;
            }
        }
        // No user record found and no email collision: good to go for a clean registration
        return true;
    }

    /**
     * determines if the ae_user signing up is a clean signon
     *
     * @param array     $post   $_POST
     * @return boolean          is a clean signon
     */
    public function is_clean_signon($post) {

        $ae_user;
        // Validate signon process if the flow is not logout
        if (isset($post['login'])) {
            $ae_user = $post['login'];
            $clean_signon = $this->ae_wp_user_validation($ae_user);
        } elseif (isset($post['registration'])) {
            $ae_user = $post['registration'];
            $clean_signon = $this->ae_wp_user_validation($ae_user);
        } else {
            $clean_signon = true;
        }

        return $clean_signon;
    }

    /**
     * creates a new wordpress user
     * @param  array    $user     an AE user array containing relevant user data needed for user creation
     * @return array              array of WP user data fields
     */
    public function create_new_ae_wp_user($user) {
        $user_data = $user['data'];

        // Compose variables to store core user data
        $user_name = $user_data['Username'];
        $email = $user_data['Email'];
        $pass = wp_generate_password();

        // Set an array which is used as the argument for wp_insert_user()
        $wp_user_data = array(
            'user_login' => $user_name,
            'user_pass' => $pass,
            'user_email' => $email
        );

        // Insert a new WP User into the database
        // and Store resulting User ID
        $user_id = wp_insert_user($wp_user_data);

        $new_user_data = array('user_data' => $user_data, 'user_id' => $user_id, 'wp_user_data' => $wp_user_data);
        return $new_user_data;
    }

    /**
     * @param  [type] $wp_user WP_User object containing relevant user data needed for sign-in
     * @return WP_User $wp_user
     */
    public function log_in_ae_wp_user($wp_user) {
        // Log the user in upon sign-in
        $wp_user = wp_set_current_user($wp_user->ID);
        wp_set_auth_cookie($wp_user->ID);

        do_action('wp_login', $wp_user->user_login, $wp_user);
        return $wp_user;
    }

    /**
     * returns the signon type from a $_POST signon request
     *
     * @param  array $post $_POST
     * @return string      signon type
     */
    public function get_signon_type_from_post($post) {

        if(isset($post['logout'])) {
            return 'logout';
        }

        elseif(isset($post['login'])) {
            return 'login';
        }

        elseif(isset($post['registration'])) {
            return 'registration';
        }

        else {
            return 'fatal_error';
        }
    }

    /**
     * searches for a WP user with $id
     *
     * @param  int          $id [description]
     * @return boolean      WP user with $id is found
     */
    private function match_ae_id($id) {

        $wp_users_byAeID = get_users(array('meta_key' => 'ae_connect_' . 'ID', 'meta_value' => $id));
        if (!empty($wp_users_byAeID) && count($wp_users_byAeID) == 1) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * searches for a WP user that has any matches against $services
     * @param  array    $services   ae services
     * @return boolean              WP user found that matches on services
     */
    public function match_services($ae_user) {

        $services = $ae_user['services'];
        $ae_user_services = $services;
        foreach ($ae_user_services as $service) {
            $social_platform = $service['Service'];

            $service_id = $service['ID'];

            $wp_users_byServiceID = get_users(array('meta_key' => 'ae_connect_' . $social_platform . '_service', 'meta_value' => $service_id));

            if (!empty($wp_users_byServiceID)) {
                $wp_user = $wp_users_byServiceID[0];
                $wp_user_id = $wp_user->ID;
                $this->update_ae_user_meta_data($ae_user, $wp_user_id);
                return true;
            }
        }

        return false; // no services found

    }

    /**
     * gets user's verified email;
     *
     * @param  array  $ae_user_data     raw ae user data
     * @return array                    user with verified email
     * @return empty var                user does not have a verified email
     */
    private function get_user_with_verified_email($ae_user_data) {

        // If the user has a verified email:
        if (array_key_exists('VerifiedEmail', $ae_user_data) && !empty($ae_user_data['VerifiedEmail'])) {
            $verified_email_wp_user = get_users(
                array(
                    'meta_key' => 'ae_connect_' . 'VerifiedEmail',
                    'meta_value' => $ae_user_data['VerifiedEmail'])
                );
            return $verified_email_wp_user;
        } else {
            return array();
        }

    }

    /**
     * gets $ae_id by $wp_id
     * @param  int $wp_id   WP_User ID
     *
     * @return int       AE Connect ID
     */
    public function get_ae_connect_id_by_wp_id($wp_id) {

        $ae_id = get_user_meta($wp_id, 'ae_connect_' . 'ID', true);
        return $ae_id;

    }

    /**
     * retrieves the AE Service ID of the first service in the AE Services array/structure
     * @param  int  $wp_id                WP_User ID
     * @return int  $ae_service_id        AE Connect Service ID
     */
    public function get_first_of_ae_services_id($wp_id) {

        $services = get_user_meta($wp_id, 'ae_connect_' . 'services');
        //  Get the first service in the services array
        $service = $services[0];
        $ae_service_id = reset($service);

        update_option('service_id', $ae_service_id);
        return $ae_service_id;

    }

    /**
     * retrieves the AE Service of the first service in the AE Services array/structure
     * @param  int  $wp_id                WP_User ID
     * @return array  $service       AE Connect Service
     */
    public function get_first_of_ae_services_service_type($wp_id) {

        $services = get_user_meta($wp_id, 'ae_connect_' . 'services');
        //  Get the first service in the services array
        $service = $services[0];
        $ae_service_id = reset($service);

        $ae_service_type = array_search($ae_service_id, $service);
        return $ae_service_type;

    }

    /**
     * Used to update a user's metafields.
     * @param  array    $ae_user         contains user meta field data array, optins array, and services array
     * @param  int      $wp_user_id      wordpress user's ID
     */
    public static function update_ae_user_meta_data($ae_user, $wp_user_id) {
        $user_data = $ae_user['data'];
        $user_services = $ae_user['services'];

        // Add user fields metafields
        // Add prefixes in order to name-space all fields:
        foreach ($user_data as $meta_field => $meta_value) {
            update_user_meta($wp_user_id, 'ae_connect_' . $meta_field, $meta_value);
        }
        // Ensure email in extra_fields is synced with email in WP_User:
        $meta_email = get_user_meta($wp_user_id, 'ae_connect_Email', true);
        if (!empty($meta_email)) {
            wp_update_user(array('user_email' => $meta_email));
        }

        // Add services metafields
        foreach ($user_services as $service) {
            // Add a new serviceID and Service
            $service_id = $service['ID'];
            update_user_meta($wp_user_id, 'ae_connect_' . $service['Service'] . '_service', $service_id);

            // Check to see if they have the services array field
            $old_services = get_user_meta($wp_user_id, 'ae_connect_' . 'services', true);
            $old_services[$service['Service']] = $service_id;

            update_user_meta($wp_user_id, 'ae_connect_' . 'services', $old_services);
        }
    }

    /**
     * [strip_sensitive_user_data description]
     * @param  json     $Obj    JSON ENCODE OBJECT
     * @return json             Sanitized output
     */
    public function strip_sensitive_user_data($Obj) {

        $sensitive_data = json_decode($Obj);
        if(is_string($sensitive_data)) {
            return json_encode($sensitive_data);
        }
        elseif(is_object($sensitive_data) && isset($sensitive_data->data)) {
            unset($sensitive_data->data->user_email);
            unset($sensitive_data->data->user_login);
            unset($sensitive_data->data->user_pass);
            unset($sensitive_data->data->user_activation_key);
            unset($sensitive_data->data->user_url);
            unset($sensitive_data->data->user_registered);
            return json_encode($sensitive_data);
        } else {

            return json_encode('unexpected_error_output');

        }


    }

}
