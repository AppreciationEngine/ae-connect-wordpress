<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       avery.dev.appreciationengine.com
 * @since      1.0.0
 *
 * @package    Ae_Connect
 * @subpackage Ae_Connect/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Ae_Connect
 * @subpackage Ae_Connect/includes
 * @author     Appreciation Engine <avery@appreciationengine.com>
 */
class Ae_Connect {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Ae_Connect_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {
        if (defined('PLUGIN_VERSION')) {
            $this->version = PLUGIN_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'ae-connect';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Ae_Connect_Loader. Orchestrates the hooks of the plugin.
     * - Ae_Connect_i18n. Defines internationalization functionality.
     * - Ae_Connect_Admin. Defines all hooks for the admin area.
     * - Ae_Connect_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ae-connect-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ae-connect-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-ae-connect-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-ae-connect-public.php';

        /**
         * The class responsible for handling all user flow that occurs in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/user-handling/class-ae-connect-user-flow.php';

        /**
         * The class responsible for handling email collisions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/user-handling/class-email-collision-handle.php';

        /**
         * The class responsible for handling widget registration
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/widgets/register-widgets.php';

        $this->loader = new Ae_Connect_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Ae_Connect_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new Ae_Connect_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

        $plugin_admin = new Ae_Connect_Admin($this->get_plugin_name(), $this->get_version());

        remove_action('admin_enqueue_scripts', 'wp_auth_check_load');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_menu', $plugin_admin, 'ae_connect_plugin_menu');
        $this->loader->add_action('admin_init', $plugin_admin, 'ae_connect_register_all_settings');

        // Add metabox for segmentID
        $this->loader->add_action('add_meta_boxes', $plugin_admin, 'ae_segmentid_metabox');
        $this->loader->add_action('save_post', $plugin_admin, 'ae_segmentid_metabox_save');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {
        $plugin_public = new Ae_Connect_Public($this->get_plugin_name(), $this->get_version());
        $plugin_user_flow = new Ae_Connect_User_Flow($this->get_plugin_name(), $this->get_version());
        $plugin_email_collision_handle = new Ae_Connect_EmailCollisionHandle();
        $plugin_register_widgets = new AE_Register_Widgets();

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

        $this->loader->add_action('wp_ajax_nopriv_ae_login_flow', $plugin_user_flow, 'ae_login_flow');
        $this->loader->add_action('wp_ajax_ae_login_flow', $plugin_user_flow, 'ae_login_flow');

        $this->loader->add_action('wp_ajax_nopriv_ae_update_wp', $plugin_user_flow, 'ae_update_wp');
        $this->loader->add_action('wp_ajax_ae_update_wp', $plugin_user_flow, 'ae_update_wp');

        $this->loader->add_action('login_form_login', $plugin_user_flow, 'redirect_custom_login');

        $this->loader->add_action('wp_ajax_nopriv_verify_email_colision_resolution', $plugin_email_collision_handle, 'verify_email_colision_resolution');
        $this->loader->add_action('wp_ajax_verify_email_colision_resolution', $plugin_email_collision_handle, 'verify_email_colision_resolution');

        $this->loader->add_shortcode('ae-form', $plugin_public, 'registration_form_shortcode');
        $this->loader->add_shortcode('ae-link', $plugin_public, 'ae_link_shortcode');
        $this->loader->add_shortcode('ae-logout', $plugin_public, 'ae_logout_shortcode');
        $this->loader->add_shortcode('ae-window', $plugin_public, 'ae_window_shortcode');
        $this->loader->add_shortcode('ae-forgot-password', $plugin_public, 'ae_forgot_password_shortcode');

        // add widgets
        $this->loader->add_action('widgets_init', $plugin_register_widgets, 'register_AE_On_Page_Form_Widget');
        $this->loader->add_action('widgets_init', $plugin_register_widgets, 'register_AE_Link_Widget');
        $this->loader->add_action('widgets_init', $plugin_register_widgets, 'register_AE_Window_Widget');
        $this->loader->add_action('widgets_init', $plugin_register_widgets, 'register_AE_Logout_Widget');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Ae_Connect_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

}
