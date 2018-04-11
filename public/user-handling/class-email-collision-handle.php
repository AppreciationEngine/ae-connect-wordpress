<?php
require_once 'class-user-handle-utilities.php';
/**
 * User handling functionality for the plugin
 *
 * @link       avery.dev.appreciationengine.com
 * @since      1.0.0
 *
 * @package    Ae_Connect
 * @subpackage Ae_Connect/public
 */
class Ae_Connect_EmailCollisionHandle extends Ae_Connect_UserHandleUtilities {

    /**
     * Take user A's AE ID and Service ID,
     * and user B's AE ID
     * and save it in service_collision_handle option
     *
     * @param array     $ae_user    raw ae_user
     *
     * @return array    $service_collision_handle   collision data
     * @return string  'fatal_error'
     */
    public function handle_email_collision($ae_user) {
        $userA_wp_id = email_exists($ae_user['data']['Email']);
        if ($userA_wp_id) {
            $userA_ae_id = $this->get_ae_connect_id_by_wp_id($userA_wp_id);

            $userA_service_id = $this->get_first_of_ae_services_id($userA_wp_id);

            $userA_service_type = $this->get_first_of_ae_services_service_type($userA_wp_id);

            $userB_ae_id = $ae_user['data']['ID'];
            $userB_ae_AccessToken = $ae_user['data']['AccessToken'];

            $service_collision_handle = array(
                'code' => 1111,
                'error' => 'Email Collision',
                'userAAeID' => $userA_ae_id,
                'userAServiceID' => $userA_service_id,
                'userA_service_type' => $userA_service_type,
                'userBAeID' => $userB_ae_id,
                'userBAeAccessToken' => $userB_ae_AccessToken
            );

            update_option('email_collision_handle', $service_collision_handle);

            return array(
                'code' => $service_collision_handle['code'],
                'userA_service_type' => $service_collision_handle['userA_service_type'],
                'error' => 'Email Collision'
            );
        } else {
            return 'fatal_error';
        }

    }

    /**
     * Parses data returned from a user's collision resolution attempt
     * Makes member_merge request to AE's APIs
     *
     * This is a Callback (Action) executed/Added via the WP Boiler plate methodology
     */
    public function verify_email_colision_resolution() {
        if (isset($_POST['resolution'])) {
            $user_in_question = $_POST['resolution'];
            $service_collision_data = get_option('email_collision_handle');

            // Reset the collision data in the DB to avoid future conflicts
            update_option('email_collision_handle', array());

            if (!empty($service_collision_data)) {
                $merged_wp_user = array();
                $services_in_question = $user_in_question['services'];
                foreach ($services_in_question as $service) {
                    if ($service['ID'] == $service_collision_data['userAServiceID']) {
                        $res_data = array(
                            'success' => true,
                            'api_key' => get_option('ae_connect_api_key'),
                            'instance' => get_option('ae_connect_instance'),
                            'user_a_id' => $service_collision_data['userAAeID'],
                            'userBAeAccessToken' => $service_collision_data['userBAeAccessToken'],
                            'user_b_id' => $service_collision_data['userBAeID'],
                            'post_request_url' => '',
                            'post_response' => ''
                        );

                        $merge_request = $res_data['instance'] .
                            '/v1.1/member/' . $res_data['user_a_id'] .
                            '/merge?apiKey=' . $res_data['api_key'] . '&from=' . $res_data['user_b_id'] . '&accesstoken=' . $res_data['userBAeAccessToken'];

                        $response = wp_remote_post($merge_request, array('timeout' => 10));
                        $res_data['post_response'] = is_wp_error($response) ? array('error' => $response) : json_decode($response['body'], true);
                        if (array_key_exists('error', $res_data['post_response'])) {
                            $res_data['success'] = false;
                        } else { // successful merge
                            $merged_wp_user = $this->login_merged_user($res_data);
                        }

                        $res_data['post_request_url'] = $merge_request;

                        echo json_encode(
                            array('success' => $res_data['success'])
                        );
                        wp_die();
                    }
                }
                echo json_encode(array('success' => false));
                wp_die();
            }
            echo 'No collision data in the DB';
            wp_die();
        }
        echo "resolution POST wasn't set";
        wp_die();
    }

    /**
     * logs in a newly merged user
     * @return WP_User      if successful login
     * @return WP_Error     if unsuccessful login
     * @return NULL         if $res_data['success'] != true
     */
    public function login_merged_user($res_data) {

        $WP_User = NULL;
        if($res_data['success']) {
            require_once plugin_dir_path(dirname(__FILE__)) . 'user-handling/class-user-signon-handle.php';
            $UserSignonHandler = new Ae_Connect_UserSignonHandle();

            $merged_ae_user = $res_data['post_response'];
            $WP_User = $UserSignonHandler->wp_user_login($merged_ae_user);
        }
        return $WP_User;

    }

}
