<?php
/**
 * User handling functionality for the plugin
 *
 * @link       avery.dev.appreciationengine.com
 * @since      1.0.0
 *
 * @package    Ae_Connect
 * @subpackage Ae_Connect/public
 */
class Ae_Connect_User_Flow {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {
        require_once plugin_dir_path(dirname(__FILE__)) . 'user-handling/class-user-signon-handle.php';
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->UserHandle = new Ae_Connect_UserSignonHandle();
    }

    /**
     * Handle user login flow data POSTED From the public JS file
     * Uses a series of POST data checks to determine which login
     * flow the AE user is executing
     *
     * This is a Callback (Action) executed/Added via the WP Boiler plate methodology
     */
    public function ae_login_flow() {
        $clean_signon = $this->UserHandle->is_clean_signon($_POST); // pass $_POST for testability/modularity
        $result = $this->UserHandle->process_ae_data($clean_signon, $_POST); // pass $_POST for testability/modularity

        if($clean_signon) {
            echo $this->UserHandle->strip_sensitive_user_data($result);
        } else {
            echo $result;
        }

        wp_die();
    }

    /**
     * This is a Callback (Action) executed/Added via the WP Boiler plate methodology
     *
     * Handle onUser update state in aeJS
     *
     * Gets the current logged in WP user by filtering out an array that only contains the
     * user who has the ae ID of the user logging in
     *
     * Grabs the user from the returned filtered users array
     * Updates that user's Meta data as to sync the WP AE user with the AE User
     *
     * Echoes WP_User if the lookup is successful
     * Echoes 'wp_user_dne' if the lookup is unsuccessful
     * Echoes 'fatal_id_collision' if the lookup returns more than 1 WP_User
     *
     * @return null
     */
    public function ae_update_wp() {
        if (isset($_POST['update']) && get_option('ae_connect_disable_local_wp_user') != 'on') {
            // Get raw array of user data
            $ae_user = $_POST['update'];
            $ae_user_data = $ae_user['data'];
            $ae_id = $ae_user_data['ID'];

            $wp_users = get_users(array('meta_key' => 'ae_connect_' . 'ID', 'meta_value' => $ae_id));

            // catch failed lookups (AKA ae user exists but wordpress user doesn't)
            if (empty($wp_users)) {
                echo 'wp_user_dne';
            }

            // Check to make sure there is only one user with this ID in the WP system
            elseif (count($wp_users) == 1) {
                // Pull the single user out of the array
                $wp_user = $wp_users[0];

                // Update the user's meta data to ensure that it is in synch with AE
                $this->UserHandle->update_ae_user_meta_data($ae_user, $wp_user->ID);

                echo json_encode($wp_user);
            } else {
                echo 'fatal_id_collision';
            }

            wp_die();
        }
    }

    /**
     *
     * @todo should move this to a different location/class
     *
     * Redirects user to our login page if they try to sign in
     */
    public function redirect_custom_login() {   // Code snippits taken from
        //  https://code.tutsplus.com/tutorials/build-a-custom-wordpress-user-flow-part-1-replace-the-login-page--cms-23627
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            // logout ae user if WP user is logging out
            if (array_key_exists('loggedout', $_REQUEST)) {
                $logout_link = get_option('ae_connect_logout_url');
                if ($logout_link) {
                    $return_url = '?auth_method=direct&return_url=' . home_url();
                    wp_redirect($logout_link . $return_url);
                    exit;
                }
            }
            update_option('signon_request', $_REQUEST);
            $redirect_to = isset($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : null;
            $URI = $_SERVER['REQUEST_URI'];
            update_option('ae_admin_login_url', $URI);
            if (is_user_logged_in()) {
                $this->redirect_logged_in_user($redirect_to);
                exit;
            }

            // If the user is visiting wp-admin, continue regular redirection
            // Otherwise redirect them to the AE login form
            if (strpos($redirect_to, 'wp-admin') == false || strpos($URI, 'wp-admin') == false) {
                $login_url = home_url('ae-connect-login-signup');
                if (!empty($redirect_to)) {
                    $login_url = add_query_arg('redirect_to', $redirect_to, $login_url);
                }
                update_option('zzz_newgaliboogali_redirect', $login_url);
                wp_redirect($login_url);
                exit;
            }
        }
    }

    /**
     *
     * @todo should move this to a different location/class
     *
     * Redirects user to the appropriate page based on their privilages
     *
     * Code snippits taken from
     * https://code.tutsplus.com/tutorials/build-a-custom-wordpress-user-flow-part-1-replace-the-login-page--cms-23627
     */
    private function redirect_logged_in_user($redirect_to = null) {
        $user = wp_get_current_user();
        if (user_can($user, 'manage_options')) {
            if ($redirect_to) {
                wp_safe_redirect($redirect_to);
            } else {
                wp_redirect(admin_url());
            }
        } else {
            wp_redirect(home_url());
        }
    }

}
