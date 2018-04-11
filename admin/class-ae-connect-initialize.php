<?php

/**
 * The Class which is instantiated inorder to initialize The
 * AE JS Framework and set appropriate local variables in the plugin.
 *
 * @link       avery.dev.appreciationengine.com
 * @since      1.0.0
 *
 * @package    Ae_Connect
 * @subpackage Ae_Connect/admin
 */
class Initialize {

    public function __construct() {
        include_once 'ae_notices.php';
    }

    private $ae_domains_list = array(
        'default' => 'https://theappreciationengine.com'
    );

    private $request_status_msgs = array(
        'success' => 'Your framework has been successfully validated',
        'error' => "There was an error initializing your framework. Please ensure that you have properly entered your
        API key and Instance.",
        'whitelist_error' => 'Error whitelisting. Please Ensure that your instance is entered in correctly',
        'whitelist_success' => 'Your domain has been successfully whitelisted.',
        'remote_whitelisting_disabled' =>  'Your AE Plan does not permit for whitelist API Calls. Please sign into your Dashboard and manually whitelist your domain.'
                    . '<strong> If you have already whitelisted your domain, you can ignore this message</strong>'
    );

    public function ae_connect_init_with_api_key($api_key) {
        // Assume initialization failed until proven otherwise
        update_option('ae_connect_initialization', 'error');

        // get the AE instance
        $ae_domain = get_option('ae_connect_instance');

        // If the instance is not set, the default is used
        if (empty($ae_domain)) {
            $ae_domain = $this->ae_domains_list['default'];
        }

        $ae_domain = $this->ae_sanitize_url($ae_domain);
        update_option('ae_connect_instance', $ae_domain);

        $AE_Notices = new AE_Notices();

        $info_data = $this->get_ae_data($api_key, $ae_domain);

        if ($info_data[1]) { // API Calls were successful
            $this->save_ae_data_to_wp_db($info_data[0]);
            $AE_Notices->set_notice($this->request_status_msgs['success'], 'updated');
        } else { // API Calls were unsuccessful
            $AE_Notices->set_notice($info_data[0], 'error');
            return 'api_error';
        }

        update_option('ae_connect_initialization', 'OK');

        $wl_result = $this->whitelist_domain($api_key, $ae_domain);
        $AE_Notices->set_notice($wl_result[0], $wl_result[1]);

    }

    /**
     * @author Avery K.
     *
     *
     * @param  [type] $ae_domain [description]
     * @param  [type] $api_key  [description]
     *
     * @return array    $result_array       a valid configuration and boolean
     *                                      true value if the request succeeded.
     *
     * @return array    $result_array       an error message, and boolean false
     *                                      value if the request threw a RequestException
     */
    private function get_ae_data($api_key, $ae_domain) {
        // Prepare the GET request URL in order to obtain the client's APP info
        $info_request = $ae_domain . '/v1.1/app/info?apiKey=' . $api_key . '&turnoffdebug=1';

        $info_response = wp_remote_get($info_request);

        if (is_wp_error($info_response)) { // error making request
            $result_array = array($this->request_status_msgs['error'], false);
            return $result_array;
        }

        $info_data = json_decode($info_response['body']);

        if($this->ae_response_is_valid($info_data)) { // successful framework get
            $result_array = array($info_data, true);
        } else { // unsuccessful framework get
            $result_array = isset($info_data->error) && isset($info_data->error->message) ?
                array($info_data->error->message, false) :
                array($this->request_status_msgs['error'], false);
        }

        return $result_array;
    }

    /**
     * [whitelist_domain description]
     * @param  [type] $api_key [description]
     * @param  [type] $ae_domain  [description]
     *
     * @return array  $result_array   array($status_msg, $status)
     *                                $status_msg is the status of the whitelist, code corresponds notice status of the whitelist
     */
    private function whitelist_domain($api_key, $ae_domain) {
        // Prepare the POST request URL in order to whitelist the client's domain
        $white_list_request = $ae_domain . '/v1.1/app/whitelist?apiKey=' . $api_key
            . '&turnoffdebug=1' . '&domain=' . get_site_url();

        $white_list_response = wp_remote_post($white_list_request);

        if (is_wp_error($white_list_response)) {
            $result_array = array($this->request_status_msgs['whitelist_error'], 'error');
            return $result_array;
        }

        $wl_data = json_decode($white_list_response['body']);

        if($this->ae_response_is_valid($wl_data)) { // successful remote whitelist

            $result_array = array($this->request_status_msgs['whitelist_success'], 'updated');

        } else { // Unsuccessful remote whitelist

            if( isset($wl_data->error) && isset($wl_data->error->code) ) {

                if($wl_data->error->code == 12 || $wl_data->error->code == 9) { // Client's plan doesn't allow remote whitelisting

                    $result_array = array($this->request_status_msgs['remote_whitelisting_disabled'], 'update-nag');

                } else {

                    $result_array = array($this->request_status_msgs['error'], 'error'); // use AE's error message

                }

            } else { // fallback error messaging (if AE's error & message arrays aren't present in isset checks)

                $result_array = array($this->request_status_msgs['whitelist_error'], 'error');

            }
        }

        return $result_array;
    }

    /**
     * @author Avery K.
     * [ae_response_is_valid description]
     * @param  array $response_body ae framework configuration/AE error response body
     * @return Boolean         true if valid response returned from AE, false if error
     */
    public function ae_response_is_valid($response_body) {

        if(isset($response_body->error)) {

            return false;

        } else {

            return true;

        }

    }

    private function save_ae_data_to_wp_db($info_data) {
        // Save Framework URL in the DB
        update_option('ae-js-framework-url', $info_data->Widget->URL);
        // Save logout_url in the DB
        update_option('ae_connect_logout_url', $info_data->LogoutURL);

        // Enable social service associated with the App
        $social_login_options = array();
        foreach ($info_data->Urls as $social_login) {
            $social_login_options[$social_login->Name] = $social_login->Name;
        }

        // Strip email from the social logins, as it is treated as a seperate entity
        unset($social_login_options['Email']);

        // Save the social options in the DB
        update_option('ae_connect_social_logins', $social_login_options);
    }

    /**
     * Parses a url and returns a sanitized url
     * @param  [type] $url [description]
     * @return [type]      [description]
     */
    private function ae_sanitize_url($url) {
        $components = parse_url($url);
        $scheme = array_key_exists('scheme', $components) ? $components['scheme'] . '://' : 'http://';
        $host = array_key_exists('host', $components) ? $components['host'] : '';
        // if host is empty, it might be set to path
        $host = empty($host) && array_key_exists('path', $components) ? $components['path'] : $host;

        $clean_url = $scheme . $host;
        return $clean_url;
    }

}
