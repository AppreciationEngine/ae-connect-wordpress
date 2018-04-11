<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Public scripts are registered
 *
 * Defines and registers shortcodes
 *
 * @link       avery.dev.appreciationengine.com
 * @since      1.0.0
 *
 * @package    Ae_Connect
 * @subpackage Ae_Connect/public
 */
class Ae_Connect_Public {

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
        require_once plugin_dir_path(__DIR__) . 'utilities/class-ae-utilities.php';

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->Utilities = new Ae_Utilities();
    }

    /**
     * returns a json encoded array structure of aeJS settings
     * to be exported to the public JS Script
     * @return json_encoded_array aeJs settings
     */
    public function load_aeJs_settings() {
        require_once plugin_dir_path(__DIR__) . 'admin/class-ae-connect-ae-js-settings-export.php';

        $Ae_JS_Export = new Ae_JS_Settings_Export($this->plugin_name, $this->version);
        return $Ae_JS_Export->export_ae_js_settings();
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/ae-connect-public.css', array(), $this->version, 'all');
        wp_enqueue_style('bootstrap.css', '//netdna.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.css', array(), null, 'all');
        wp_enqueue_style('jQueryUI.css', '//ajax.aspnetcdn.com/ajax/jquery.ui/1.12.1/themes/ui-darkness/jquery-ui.css', array(), null, 'all');
        wp_enqueue_style('alert-messages', plugin_dir_url(__FILE__) . 'css/ae-connect-collision-messages.css', array(), null, 'all');

        if(get_option('ae_connect_social_icons') == 'on') {
            wp_enqueue_style('ae-signup-customicons.css', plugin_dir_url(__FILE__) . 'css/ae-signup-customicons.css', array(), $this->version, 'all');
        }

        $this->Utilities->load_ae_form_style(get_option('ae_connect_css_themes'), $this->version);
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     * @todo remove dynamic versioning by date for production
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        // Custom dynamic version codes. This prevents wordpress/web browser from
        // Caching the javascript file and resulting in my ftp up changes not registering
        $dynamic_version = date("ymd-Gis", filemtime(plugin_dir_path(__FILE__) . 'js/ae-ready.js'));

        // Parameters:
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script('bootstrap.js', '//netdna.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.js', array('jquery'), null, false);
        wp_enqueue_script('jquery-ui.min.js', '//code.jquery.com/ui/1.12.1/jquery-ui.min.js', array('jquery'), null, false);
        $segmentid = $this->set_segmentid_param(); // will be an empty string if no segmentid has been set for the current page.
        // Include aeJS framework script set by the installation in the admin settings
        $aeJS_frame_work_url = get_option('ae-js-framework-url');
        $ae_flow_css_url = $this->Utilities->get_selected_flow_css_url();

        $aeJS_frame_work_url = $this->Utilities->change_aejs_scheme_based_on_flow_css($ae_flow_css_url, $aeJS_frame_work_url);
        if (!empty($aeJS_frame_work_url)) {

            if (!empty($segmentid)) {
                $aeJS_frame_work_url .= '?segment=' . $segmentid; // append segmentid query parameter to the AEJS framework
            }

            wp_enqueue_script('aejs', $aeJS_frame_work_url, array(), null, false);

            wp_enqueue_script('ae-email-verification.js', plugin_dir_url(__FILE__) . 'js/email-verification.js', array('jquery'), $dynamic_version, false);
            wp_enqueue_script('ae-onpage-extra-fields.js', plugin_dir_url(__FILE__) . 'js/onpage-extra-fields.js', array('jquery'), $dynamic_version, false);
            wp_enqueue_script('set-aejs-settings.js', plugin_dir_url(__FILE__) . 'js/set-aejs-settings.js', array('jquery'), $dynamic_version, false);
            wp_enqueue_script('ae-user-handler.js', plugin_dir_url(__FILE__) . 'js/user-handler.js', array('jquery'), $dynamic_version, false);
            wp_enqueue_script('ae-state.js', plugin_dir_url(__FILE__) . 'js/ae-state.js', array('jquery'), $dynamic_version, false);
            wp_enqueue_script('ae-utilities.js', plugin_dir_url(__FILE__) . 'js/ae-utilities.js', array('jquery'), $dynamic_version, false);

            wp_enqueue_script( // main js file
                'ae-ready.js', plugin_dir_url(__FILE__) . 'js/ae-ready.js',
                array('jquery'),
                $dynamic_version, false
            );
            //  'ae-state.js', 'ae-user-handler', 'set-aejs-settings.js', 'ae-onpage-extra-fields.js', 'ae-email-verification.js'
            $aeJS_settings = $this->load_aeJs_settings();
            wp_localize_script('ae-ready.js', 'aeJS_WP_settings', $aeJS_settings);

            // in JavaScript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
            wp_localize_script('ae-user-handler.js', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php'), 'ajaxnonce' => wp_create_nonce('ajax_post_validation')));
        }
    }

    /**
     * Callback function to Display AE Connect On-page Signon Form via shortcode
     * Registered in the loader class
     *
     * shortcode: [ae-form]
     * @var atts
     */
    public function registration_form_shortcode($atts) {
        // Only load login form if a user is not currently logged in
        if (!is_user_logged_in()) {
            require_once 'partials/ae-connect-registration-form-display.php';
        } else {
            return '<a href="#" data-ae-logout-link="true" '
                . 'data-ae-return="' . wp_logout_url(home_url()) . '">Logout</a>';
        }
    }

    /*
     *
     * Renders Shortcode for ae-data-<login || register>-link tags
     * Shortcode in the form of:
     * [ae-link type="login || register" service="facebook || spotify || etc." return="URL"]
     *
     */

    public function ae_link_shortcode($atts, $content = null) {
        $site_url = get_site_url();
        $secure = parse_url($site_url, PHP_URL_SCHEME) == 'https' ? true : false;
        // Set up shortcode arguments
        $args = array(
            'type' => 'register',
            'service' => 'email',
            'return' => $site_url,
            'show_after_login' => 0
        );

        // Register shortcode arguments
        $ae_link_args = shortcode_atts($args, $atts);
        $ae_link_args['return'] = $this->Utilities->append_scheme_to_url($ae_link_args['return'], $secure);

        // Only render link if all users are logged out
        if (!is_user_logged_in() || $ae_link_args['show_after_login'] == 1) {
            // Renders HTML Output
            return
                '<div id="ae-email-sent" style="display: none;">
            Thank you for signing up, a Verification email is on its way
            </div>
            <a class="ae-flow-step1 ' . $ae_link_args['service'] . '" href="#" data-ae-' . esc_attr($ae_link_args['type']) . '-link="' . esc_attr($ae_link_args['service'])
                . '" data-ae-return="' . esc_attr($ae_link_args['return']) . '">' . esc_attr($content) . '</a>';
        }
    }

    /*
     *
     * Renders Shortcode for ae-data-<login || register>-window tags
     * Shortcode in the form of:
     * [ae-window type="login || register" return="URL"]
     *
     */
    public function ae_window_shortcode($atts, $content = null) {
        $site_url = get_site_url();
        $secure = parse_url($site_url, PHP_URL_SCHEME) == 'https' ? true : false;
        // Set up shortcode arguments
        $args = array(
            'type' => 'register',
            'return' => $site_url,
            'show_after_login' => 0
        );

        // Register shortcode arguments
        $ae_window_args = shortcode_atts($args, $atts);
        $ae_window_args['return'] = $this->Utilities->append_scheme_to_url($ae_window_args['return'], $secure);

        $current_user = wp_get_current_user();

        // Only render link if all users are logged out
        if (!is_user_logged_in() || $ae_window_args['show_after_login'] == 1) {
            // Renders HTML Output
            return
                '<div id="ae-email-sent" style="display: none;">
            Thank you for signing up, a Verification email is on its way
            </div>
            <a class="ae-flow-step1" href="#" data-ae-' . esc_attr($ae_window_args['type']) . '-window="true" data-ae-return="'
                . esc_attr($ae_window_args['return']) . '">' . esc_attr($content) . '</a>';
        }
    }

    /*
     *
     * Renders Shortcode for ae-data-logout-link tags
     * Shortcode in the form of:
     * [ae-logout return="URL"]
     *
     */

    public function ae_logout_shortcode($atts, $content = null) {
        $site_url = get_site_url();
        $secure = parse_url($site_url, PHP_URL_SCHEME) == 'https' ? true : false;
        // Get logout URL from the DB
        $lu = get_option('ae_connect_logout_url');
        $logout_url = !empty($lu) ? $lu : '';

        // Set up shortcode arguments
        $args = array('return' => home_url(), 'logout_url' => $logout_url);

        // Register shortcode arguments
        $ae_logout_args = shortcode_atts($args, $atts);
        $ae_logout_args['return'] = $this->Utilities->append_scheme_to_url($ae_logout_args['return'], $secure);

        // only render logout links if a user is logged in
        if (is_user_logged_in()) {
            // Renders HTML Output
            return '<a class="ae-logout" href="#" data-ae-logout-link="true" '
                . 'data-ae-return="' . wp_logout_url(esc_attr($ae_logout_args['return'])) . '">' . esc_attr($content) . '</a>';
        }
    }

    /**
     * Code Format: [ae-forgot-password]
     * A link that triggers ae password recovery
     */
    public function ae_forgot_password_shortcode($atts) {
        return '<button class="button button-primary" onclick="globalAEJS.trigger.verify_reset_password(window.location.origin);">Forgot password?</button>';
    }

    /**
     * [set_segmentid_param description]
     *
     * @return segmentID BUT, if no segment ID has been set for the current page returns an empty string
     */
    public function set_segmentid_param() {
        global $post;
        $currentpageid = $post->ID;
        // update_option('zzzPOSTID', $currentpageid);
        $segmentid = get_post_meta($currentpageid, 'ae_segmentid_metabox_value', true);
        return $segmentid;
    }

}
