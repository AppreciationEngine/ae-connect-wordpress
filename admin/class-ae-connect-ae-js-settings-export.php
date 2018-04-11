<?php

/**
 * Class For Functions Which Support the exporting of WP Backend plugin settings
 * to the AEJS framework via AJAX Call
 *
 * @link       avery.dev.appreciationengine.com
 * @since      1.0.0
 *
 * @package    Ae_Connect
 * @subpackage Ae_Connect/public
 */
class Ae_JS_Settings_Export {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @     string    $plugin_name    The ID of this plugin.
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

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     *
     * Ajax call back which returns a json_encoded array of ae_js_settings
     *
     * @return [type] [description]
     */
    function export_ae_js_settings() {
        $ae_js_settings = array();

        $ae_js_settings['services'] = '';
        $ae_js_settings['auth_window'] = get_option('ae_connect_auth_window') == 'on' ? true : false;
        $ae_js_settings['flow_css'] = get_option('ae_connect_flow_css');
        $ae_js_settings['disable_local_wp_user'] = get_option('ae_connect_disable_local_wp_user') == 'on' ? true : false;

        $ae_js_settings['extra_fields'] = $this->render_extra_fields_settings();
        $ae_js_settings['verify_email'] = get_option('ae_connect_verify_email') == 'on' ? true : false;
        $ae_js_settings['verify_email_for_login'] = get_option('ae_connect_verify_email_for_login') == 'on' ? true : false;
        $ae_js_settings['services'] = $this->render_services_string();
        $ae_js_settings['email_format'] = $this->render_email_format_settings();

        $ae_js_settings['hide_email_form'] = get_option('ae_connect_hide_email_form') == 'on' ? true : false;
        $ae_js_settings['global_top_title'] = get_option('ae_connect_global_top_title');
        $ae_js_settings['global_bottom_title'] = get_option('ae_connect_global_bottom_title');
        $ae_js_settings['flow_text'] = $this->render_flow_text_settings();
        $ae_js_settings['flow_css'] = $this->get_selected_flow_css_url();
        $ae_js_settings['language'] = get_option('ae_connect_language');

        $ae_js_settings['optins'] = get_option('ae-connect-general-opt-ins');

        return json_encode($ae_js_settings);
    }

    /**
     * Sets up the extra_fields structure by checking the WP DB to see if the extra field is enabled
     * and then if it is populating the extra fields structure to be exported with the value in the WP DB
     * set by the user in the WP Admin backend
     *
     * @return array extra_fields structure to be exported to the AEJS structure
     */
    public function render_extra_fields_settings() {
        $extra_fields = array();
        // A list of extra_fields:
        $extra_fields_assoc_array = get_option('ae_connect_extra_fields_assoc_array');
        $plugin_name = 'ae_connect';

        if (!empty($extra_fields_assoc_array)) {
            // Loop through this array and renders the JSON
            foreach ($extra_fields_assoc_array as $field) {
                $enabled = get_option($plugin_name . '_' . $field) == 'on' ? true : false;
                $label = get_option($plugin_name . '_label_' . $field);
                $required = get_option($plugin_name . '_required_' . $field) == 'on' ? true : false;

                if ($enabled) {
                    $extra_fields[$field] = array(
                        'label' => $label,
                        'required' => $required,
                    );
                }
            }
        }

        return $extra_fields;
    }

    /**
     * Sets up aeJS email_formats structure by populating the extra fields structure to be exported
     * with the value in the WP DB set by the user in the WP Admin backend
     *
     * @return array extra_fields structure to be exported to the AEJS structure
     */
    public function render_email_format_settings() {
        $email_formats = array();
        // A list of extra_infos:
        $email_formats_array = get_option('ae_connect_email_formats_struct');
        $plugin_name = 'ae_connect';

        if (!empty($email_formats_array)) {
            // Loop through this array and renders the JSON
            foreach ($email_formats_array as $format) {
                $customization = get_option($plugin_name . '_' . $format['field']);

                if (!empty($customization)) {
                    $email_formats[$format['field']] = $customization;
                }
            }
        }

        return $email_formats;
    }

    /**
     * Sets up aeJS flow_text to be exported to the AEJS Structure by populating the values
     * with the value in the WP DB set by the user in the WP Admin backend
     *
     * @return array extra_fields structure to be exported to the AEJS structure
     */
    public function render_flow_text_settings() {
        $flow_text = array();
        // A list of extra_infos:
        $flow_text_assoc_array = get_option('flow_text_assoc_array');
        $plugin_name = 'ae_connect';

        if (!empty($flow_text_assoc_array)) {
            // Loop through this array and renders the JSON
            foreach ($flow_text_assoc_array as $text_field) {
                $label = get_option($plugin_name . '_' . $text_field);

                if (!empty($label)) {
                    $flow_text[$text_field] = $label;
                }
            }
        }

        return $flow_text;
    }

    /**
     * Pulls the AE Application's available social logins from the WP DB
     * checks to see if they are enabled in the WP Options table
     * adds them to an array that will be imploded into a comma separated list
     * string
     *
     * Returns impoloded services string
     * @return String impoloded services string
     */
    public function render_services_string() {
        $services_array = get_option('ae_connect_social_logins') ? get_option('ae_connect_social_logins') : array();
        $aeJsServices = array();

        $count = 0;
        foreach ($services_array as $service) {
            if (get_option('ae_connect_' . $service) == 'on') {
                $aeJsServices[$count] = lcfirst($service);
                $count++;
            }
        }

        return implode(",", $aeJsServices);
    }

    /**
     * If the client has defined a custom flow_css URL, it will be loaded into flow_css
     * If the client has not defined a custom flow_css URL and also selected a css theme, the theme will be
     * loaded into flow_css
     *
     * @return string  flow_css URL
     */
    public function get_selected_flow_css_url() {
        $flow_css_url = get_option('ae_connect_flow_css');
        $theme_css_option = get_option('ae_connect_flow_css_themes');
        if( empty($flow_css_url) && ( $theme_css_option != 0 || empty($theme_css_option) ) ) {

            return $this->get_theme_stylesheet($theme_css_option);

        }
        return $flow_css_url;
    }

    /**
     * Excepts an option, returns the corresponding theme based upon the option
     * @param  int $option      integer representing which stylesheet is to be returned
     * @return string stylesheet url (empty string if case:0 or invalid option)
     */
    public function get_theme_stylesheet($option) {
        $plugin_dir = plugin_dir_url(__DIR__);
        $theme1 = $plugin_dir . 'public/css/ae-signup-theme-colourful.css';
        $theme2 = $plugin_dir . 'public/css/ae-signup-theme-light.css';
        $theme3 = $plugin_dir . 'public/css/ae-signup-theme-dark.css';
        switch($option) {
            case 0:
                return '';
            case 1:
                return $theme1;
            case 2:
                return $theme2;
            case 3:
                return $theme3;
            default:
                return '';
        }

    }

}


?>
