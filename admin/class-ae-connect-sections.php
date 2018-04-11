<?php

class AE_Connect_Sections {

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   public
     * @var      string    $sections    Plugin's admin sections
     */
    public $sections;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    Ae_Connect_Admin    $admin_obj       Instantiating class' object
     */
    public function __construct($admin_obj) {

        $this->sections = array(
            'main_page_sections' => array(
                'installation' => array(
                    'id' => $admin_obj->option_name . '_installation',
                    'title' => __('Installation', 'ae-connect'),
                    'callback' => array($admin_obj, $admin_obj->option_name . '_display_generic_section'),
                    'page' => $admin_obj->plugin_name
                ),
            ),
            'general_settings_sections' => array(
                'options' => array(
                    'id' => $admin_obj->option_name . '_options',
                    'title' => __('Options', 'ae-connect'),
                    'callback' => array($admin_obj, $admin_obj->option_name . '_display_generic_section'),
                    'page' => $admin_obj->plugin_name . '-general-settings'
                ),
            ),
            'general_social_sections' => array(
                'networks' => array(
                    'id' => $admin_obj->option_name . '_networks',
                    'title' => __('Social Networks', 'ae-connect'),
                    'callback' => array($admin_obj, $admin_obj->option_name . '_display_socials_section'),
                    'page' => $admin_obj->plugin_name . '-general-social'
                ),
            ),
            'general_user_fields_sections' => array(
                'additional_data' => array(
                    'id' => $admin_obj->option_name . '_user_fields',
                    'title' => __('Additional Data', 'ae-connect'),
                    'callback' => array($admin_obj, $admin_obj->option_name . '_display_generic_section'),
                    'page' => $admin_obj->plugin_name . '-general-user-fields'
                ),
            ),
            'general_email_sections' => array(
                'options' => array(
                    'id' => $admin_obj->option_name . '_email_options',
                    'title' => __('Options', 'ae-connect'),
                    'callback' => array($admin_obj, $admin_obj->option_name . '_display_email_options_section'),
                    'page' => $admin_obj->plugin_name . '-general-email'
                ),
                'format' => array(
                    'id' => $admin_obj->option_name . '_email_format',
                    'title' => __('Format', 'ae-connect'),
                    'callback' => array($admin_obj, $admin_obj->option_name . '_display_generic_section'),
                    'page' => $admin_obj->plugin_name . '-general-email'
                ),
                'header' => array(
                    'id' => $admin_obj->option_name . '_email_header',
                    'title' => __('Header', 'ae-connect'),
                    'callback' => array($admin_obj, $admin_obj->option_name . '_display_generic_section'),
                    'page' => $admin_obj->plugin_name . '-general-email'
                ),
                'footer' => array(
                    'id' => $admin_obj->option_name . '_email_footer',
                    'title' => __('Footer', 'ae-connect'),
                    'callback' => array($admin_obj, $admin_obj->option_name . '_display_generic_section'),
                    'page' => $admin_obj->plugin_name . '-general-email'
                ),
                'verify' => array(
                    'id' => $admin_obj->option_name . '_email_verify',
                    'title' => __('Verify Email Settings', 'ae-connect'),
                    'callback' => array($admin_obj, $admin_obj->option_name . '_display_generic_section'),
                    'page' => $admin_obj->plugin_name . '-general-email'
                ),
                'reset_pword' => array(
                    'id' => $admin_obj->option_name . '_email_reset_pword',
                    'title' => __('Reset Password Settings', 'ae-connect'),
                    'callback' => array($admin_obj, $admin_obj->option_name . '_display_generic_section'),
                    'page' => $admin_obj->plugin_name . '-general-email'
                ),
                'add_opt_ins' => array(
                    'id' => $admin_obj->option_name . '_add_opt_ins',
                    'title' => __('Add Opt-ins', 'ae-connect'),
                    'callback' => array($admin_obj, $admin_obj->option_name . '_display_generic_section'),
                    'page' => $admin_obj->plugin_name . '-general-opt-ins'
                )
            ),
            'popup_settings_sections' => array(
                'options' => array(
                    'id' => $admin_obj->option_name . '_popup_options',
                    'title' => __('Options', 'ae-connect'),
                    'callback' => array($admin_obj, $admin_obj->option_name . '_display_generic_section'),
                    'page' => $admin_obj->plugin_name . '-popup-settings'
                ),
            ),
            'popup_text_sections' => array(
                'header' => array(
                    'id' => $admin_obj->option_name . '_popup_text_header',
                    'title' => __('Header (text or html)', 'ae-connect'),
                    'callback' => array($admin_obj, $admin_obj->option_name . '_display_generic_section'),
                    'page' => $admin_obj->plugin_name . '-popup-text'
                ),
                'footer' => array(
                    'id' => $admin_obj->option_name . '_popup_text_footer',
                    'title' => __('Footer (text or html)', 'ae-connect'),
                    'callback' => array($admin_obj, $admin_obj->option_name . '_display_generic_section'),
                    'page' => $admin_obj->plugin_name . '-popup-text'
                ),
                'labels_messages' => array(
                    'id' => $admin_obj->option_name . '_labels_messages',
                    'title' => __('Labels & Messages', 'ae-connect'),
                    'callback' => array($admin_obj, $admin_obj->option_name . '_display_generic_section'),
                    'page' => $admin_obj->plugin_name . '-popup-text'
                ),
            ),
            'performance' => array(
                'options' => array(
                    'id' => $admin_obj->option_name . '_performance_options',
                    'title' => __('Options', 'ae-connect'),
                    'callback' => array($admin_obj, $admin_obj->option_name . '_display_generic_section'),
                    'page' => $admin_obj->plugin_name . '-performance'
                )
            )
        );
    }

}
