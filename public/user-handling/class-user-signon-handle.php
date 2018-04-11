<?php
require_once plugin_dir_path(dirname(__FILE__)) . 'user-handling/class-email-collision-handle.php';
require_once plugin_dir_path(dirname(__FILE__)) . 'user-handling/class-user-handle-utilities.php';
/**
 * User Signon handling functionality for the plugin
 *
 * @link       avery.dev.appreciationengine.com
 * @since      1.0.0
 *
 * @package    Ae_Connect
 * @subpackage Ae_Connect/public
 */
class Ae_Connect_UserSignonHandle extends Ae_Connect_UserHandleUtilities {

    /**
     * Processes the data passed through the ajax call
     * @param  Boolean  $clean_signon   valid user
     * @return JSON     {WP_User} || {'logged out'} || {'email_collision'} || {'no_process'}
     */
    public function process_ae_data($clean_signon, $post) {

        $result = 'no_process';
        $singon_type = $this->get_signon_type_from_post($post);
        if($singon_type == 'fatal_error')
            return json_encode($result);

        // If local WP Users are not disabled:
        // handle user action
        if (get_option('ae_connect_disable_local_wp_user') != 'on') {
            $ae_user = $post[$singon_type];
            // If ae user is logging out
            if (isset($post['logout'])) {
                $this->wp_user_logout();
                $result = 'logged out';
            }
            // If existing user is logging in
            elseif ($clean_signon && isset($post['login'])) {

                $logged_in_wp_user = $this->wp_user_login($ae_user);
                if ($logged_in_wp_user === "wp_user_dne") { // user does not exist, but an AE user existed previously
                    $logged_in_wp_user = $this->wp_user_register($ae_user);
                }

                $result = $logged_in_wp_user;
            }
            // If new ae user is Registering/logging-in
            elseif ($clean_signon && isset($post['registration'])) {
                $result = $this->wp_user_register($ae_user);
            }
            elseif (!$clean_signon) { // Email collision occured
                $EmailCollisionHandle = new Ae_Connect_EmailCollisionHandle();
                $result = $EmailCollisionHandle->handle_email_collision($ae_user);
            }
        }

        return json_encode($result);

    }

    /**
     * Handle logout call data POSTED From the public JS file
     */
    public function wp_user_logout() {
        // Logout the wordpress user
        wp_logout();
        wp_set_current_user(0);
    }

    /**
     * @param  array $ae_user  AE User data structure
     * @return string    'wp_user_dne'               if the user does not exist in the wordpress DB
     * @return WP_User    $wp_user                   if user is successful logged in
     * @return WP_Error   $wp_user                   if an error occured during the log in process
     * @return WP_Error  'fatal_id_collision'        fatal_id_collision (String) if the ID of the ae_user already exists in the system
     */
    public function wp_user_login($ae_user) {
        $ae_user_data = $ae_user['data'];
        $ae_id = $ae_user_data['ID'];
        // filter out an array that only contains the user who has the ae ID of the
        // user logging in
        $wp_users = get_users(array('meta_key' => 'ae_connect_' . 'ID', 'meta_value' => $ae_id));

        // catch failed lookups (AKA ae user exists but wordpress user doesn't)
        if (empty($wp_users)) {
            return 'wp_user_dne';
        }

        // Check to make sure there is only one user with this ID in the WP system
        // grab that user from the users array
        elseif (count($wp_users) == 1) {
            // Pull the single user out of the array
            $wp_user = $wp_users[0];
            // Log that user in if user sessions are enabled.
            if (get_option('ae_connect_disable_user_log_in_session') != 'on') {
                $wp_user = $this->log_in_ae_wp_user($wp_user);
            }

            // Update the user's meta data to ensure that it is in synch with AE
            $this->update_ae_user_meta_data($ae_user, $wp_user->ID);

            return $wp_user;
        } else {
            return 'fatal_id_collision';
        }
    }

    /**
     * handles a new AE Wordpress user registering
     * @param  array    $ae_user    contains raw ae user data sub arrays
     * @return WP_User              newly created and registered WP_User
     */
    public function wp_user_register($ae_user) {
        $ae_user_data = $ae_user['data'];
        $ae_id = $ae_user_data['ID'];

        // check for existing WP User, if already exists, don't create another one
        $wp_email_exists = email_exists($ae_user_data['Email']);
        if (!empty($wp_email_exists)) {
            $wp_user_id = $wp_email_exists; // email_exists() returns WP user's ID
        } else {
            $new_wp_user_array = $this->create_new_ae_wp_user($ae_user);
            $wp_user_id = $new_wp_user_array['user_id'];
        }

        // If the wp User was successfully created
        // Update the user's meta data from ae
        if (!is_wp_error($wp_user_id)) {
            $Wp_User = get_user_by('ID', $wp_user_id);
            // If the client/admin has enabled local WP sessions
            // Log the user in
            if (get_option('ae_connect_disable_user_log_in_session') != 'on') {
                $Wp_User = $this->log_in_ae_wp_user($Wp_User);
            }
            //Update user's metadata fields in the DB:
            $this->update_ae_user_meta_data($ae_user, $wp_user_id);
        }

        return $Wp_User;
    }

}
