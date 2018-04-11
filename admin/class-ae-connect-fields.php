<?php

class AE_Connect_Fields {

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   public
     * @var      string    $sections    Plugin's admin fields
     */
    private $fields;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      Ae_Connect_Admin    $admin_obj    Instantiating class' object
     */
    private $admin_obj;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      array    $country_codes    A KV list of human_readable_language => language_code
     */
    private $language_codes = array(
        'English' => 'en_US',
        'Albanian  ' => 'sq_AL',
        'Arabic ' => 'ar_MA',
        'Basque ' => 'eu_ES',
        'Belgium (French) ' => 'be_FR',
        'Belgium (Netherland) ' => 'be_NL',
        'Bulgarian ' => 'bg_BG',
        'Catalan ' => 'ca_ES',
        'Chilean Spanish ' => 'es_CL',
        'Chinese ' => 'zh_CN',
        'Czech ' => 'cs_CZ',
        'Danish ' => 'da_DK',
        'Dutch (Netherland) ' => 'nl_NL',
        'Finnish ' => 'fi_FI',
        'French ' => 'fr_FR',
        'German ' => 'de_DE',
        'Greek ' => 'gr_EL',
        'Hebrew ' => 'he_IL',
        'Hindi ' => 'hi_IN',
        'Hungarian ' => 'hu_HU',
        'Indonesian ' => 'id_ID',
        'Italian ' => 'it_IT',
        'Japanese ' => 'ja_JP',
        'Norwegian ' => 'no_NO',
        'Persian (Farsi) ' => 'fa_IR',
        'Polish ' => 'pl_PL',
        'Portuguese (Brazil) ' => 'pt_BR',
        'Portuguese (Portugal) ' => 'pt_PT',
        'Romanian ' => 'ro_RO',
        'Russian ' => 'ru_RU',
        'Serbian ' => 'sr_RS',
        'Slovak ' => 'sk_SK',
        'Spanish ' => 'es_ES',
        'Swedish ' => 'sv_SE',
        'Taiwanese ' => 'zh_TW',
        'Thai ' => 'th_TH',
        'Turkish ' => 'tr_TR',
        'Ukrainian ' => 'ua_UA',
        'Vietnamese ' => 'vi_VN'
    );

    private $css_themes = array(
        'Colourful theme' => 1,
        'Light theme' => 2,
        'Dark theme' => 3,
        'No theme' => 0,
    );

    private $flow_text_defaults = array(
        'error_header' => 'Sorry, there seems to be a glitch.',
        'login_header' => 'Sign In',
        'login_button'=> 'Sign In',
        'login_with_button'=> 'Sign In With',
        'register_header' => 'Sign Up',
        'register_button'=> 'Sign Up',
        'register_with_button'=> 'Sign Up With',
        'add_info_header' => 'Extra Info',
        'add_info_button' => 'Submit',
        'reset_pw_header' => 'Reset Password',
        'reset_pw_sent' => 'A verification email will be sent to',
        'reset_pw_instructions' => 'Please click the link in the email to confirm your address and reset your password.',
        'reset_pw_button' => 'Verify Email',
        'reset_pw_confirm_header' => 'Let\'s Confirm',
        'reset_pw_confirm_button' => 'Confirm',
        'reset_pw_confirm_instructions' => 'Please enter a new password',
        'reset_pw_done_header' => 'Bravo',
        'reset_pw_done_message' => 'Magic! Your password has been reset.',
        'reset_pw_done_button' => 'Okay',
        'verify_email_header' => 'Verify Email',
        'verify_email_sent' => 'A verification email will be sent to',
        'verify_email_instructions' => 'Please click the link in the email to confirm your address and continue.',
        'verify_email_retry_button' => 'Please Retry',
        'verify_email_success_header' => 'Bravo',
        'verify_email_success_message' => 'Magic! Your email is verified. Go and do great things!',
        'verify_email_success_button' => 'OK',
        'verify_email_error_header' => 'Things that make you go humm....',
        'verify_email_error_message' => 'Sorry that link might\'ve been cut off or it expired. Please double check the link in your email, or start a new verification email.',
        'forgot_password_link' => 'Forgot your Password?',
        'recover_password_link' => 'Recover your Password',
        'optins_title' => 'Subscribe to these Mailing Lists',
        'have_account_link' => 'Have an Account?',
        'need_help_link' => 'Need Help?',
        'create_account_link' => 'Create an Account'
    );

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    PHP object    $admin_obj       Instantiating class' object
     */
    public function __construct($admin_obj) {
        $this->fields = $this->render_fields($admin_obj);
    }

    /**
     * Getter for the private variable fields
     * @return array $fields
     */
    public function get_fields() {
        return $this->fields;
    }

    /**
     * Renders and returns a list of preprocessed fields
     * @return array fields
     */
    private function render_fields($admin_obj) {
        $fields = array(
            'main_page_fields' => array(
                'instance' => array(
                    'id' => $admin_obj->option_name . '_instance',
                    'title' => __('Instance'),
                    'callback' => array($admin_obj, $admin_obj->option_name . '_text_field'),
                    'page' => $admin_obj->plugin_name,
                    'section' => $admin_obj->option_name . '_installation',
                    'args' => array('label' => '_instance', 'size' => 50, 'sanitize' => 'ae_connect_sanitize_text_input', 'for_label_element' => 'Leave blank for the default: https://theappreciationengine.com', 'placeholder_txt' => 'https://theappreciationengine.com')
                ),
                'api_key' => array(
                    'id' => $admin_obj->option_name . '_api_key',
                    'title' => __('API Key'),
                    'callback' => array($admin_obj, $admin_obj->option_name . '_api_field'),
                    'page' => $admin_obj->plugin_name,
                    'section' => $admin_obj->option_name . '_installation',
                    'args' => array('label' => '_api_key', 'size' => '50', 'sanitize' => 'ae_connect_sanitize_text_input')
                ),
            ),
            'general_settings_options' => array(
                'auth_window' => array(
                    'id' => $admin_obj->option_name . '_auth_window',
                    'title' => __('Show Authentication as Popup'),
                    'callback' => array($admin_obj, $admin_obj->option_name . '_cb'),
                    'page' => $admin_obj->plugin_name . '-general-settings',
                    'section' => $admin_obj->option_name . '_options',
                    'args' => array('label' => '_auth_window', 'sanitize' => 'sanitize_cb_field')
                ),
                'mobile_detect' => array(
                    'id' => $admin_obj->option_name . '_mobile_detect',
                    'title' => __('Device Auto-detect'),
                    'callback' => array($admin_obj, $admin_obj->option_name . '_cb'),
                    'page' => $admin_obj->plugin_name . '-general-settings',
                    'section' => $admin_obj->option_name . '_options',
                    'args' => array('label' => '_mobile_detect', 'sanitize' => 'sanitize_cb_field', 'default' => 'on')
                ),
                'display_error_message' => array(
                    'id' => $admin_obj->option_name . '_display_error_message',
                    'title' => __('Display Default Error Message'),
                    'callback' => array($admin_obj, $admin_obj->option_name . '_cb'),
                    'page' => $admin_obj->plugin_name . '-general-settings',
                    'section' => $admin_obj->option_name . '_options',
                    'args' => array('label' => '_display_error_message', 'sanitize' => 'sanitize_cb_field')
                ),
                'sso' => array(
                    'id' => $admin_obj->option_name . '_sso',
                    'title' => __('Enable Multi-site Login'),
                    'callback' => array($admin_obj, $admin_obj->option_name . '_cb'),
                    'page' => $admin_obj->plugin_name . '-general-settings',
                    'section' => $admin_obj->option_name . '_options',
                    'args' => array('label' => '_sso', 'sanitize' => 'sanitize_cb_field', 'default' => '')
                ),
                'no_email' => array(
                    'id' => $admin_obj->option_name . '_no_email',
                    'title' => __('Social Login Only'),
                    'callback' => array($admin_obj, $admin_obj->option_name . '_cb'),
                    'page' => $admin_obj->plugin_name . '-general-settings',
                    'section' => $admin_obj->option_name . '_options',
                    'args' => array('label' => '_no_email', 'sanitize' => 'sanitize_cb_field')
                ),
                'css_themes' => array(
                    'id' => $admin_obj->option_name . '_css_themes',
                    'title' => __('Choose an AE Form Style?'),
                    'callback' => array($admin_obj, $admin_obj->option_name . '_drop_down'),
                    'page' => $admin_obj->plugin_name . '-general-settings',
                    'section' => $admin_obj->option_name . '_options',
                    'args' => array(
                        'label' => '_css_themes', 'sanitize' => 'ae_connect_sanitize_text_input',
                        'options' => $this->css_themes
                    )
                ),
                'social_icons' => array(
                    'id' => $admin_obj->option_name . '_social_icons',
                    'title' => __('Do you want to use social icons for available services?'),
                    'callback' => array($admin_obj, $admin_obj->option_name . '_cb'),
                    'page' => $admin_obj->plugin_name . '-general-settings',
                    'section' => $admin_obj->option_name . '_options',
                    'args' => array('label' => '_social_icons', 'sanitize' => 'sanitize_cb_field')
                ),
            ),
            'general_email_options' => array(
                'verify_email' => array(
                    'id' => $admin_obj->option_name . '_verify_email',
                    'title' => __('Send Email Verification During Registration'),
                    'callback' => array($admin_obj, $admin_obj->option_name . '_cb'),
                    'page' => $admin_obj->plugin_name . '-general-email',
                    'section' => $admin_obj->option_name . '_email_options',
                    'args' => array('label' => '_verify_email', 'sanitize' => 'sanitize_cb_field')
                ),
                'verify_email_for_login' => array(
                    'id' => $admin_obj->option_name . '_verify_email_for_login',
                    'title' => __('Site Requires Verified Email Before Login'),
                    'callback' => array($admin_obj, $admin_obj->option_name . '_cb'),
                    'page' => $admin_obj->plugin_name . '-general-email',
                    'section' => $admin_obj->option_name . '_email_options',
                    'args' => array('label' => '_verify_email_for_login', 'sanitize' => 'sanitize_cb_field')
                ),
            ),
            'general_social_networks' => $this->render_social_fields($admin_obj),
            'general_user_fields' => $this->render_extra_fields($admin_obj),
            'general_email_format' => $this->render_email_format_fields($admin_obj),
            'popup_settings' => array(
                'hide_email_form' => array(
                    'id' => $admin_obj->option_name . '_hide_email_form',
                    'title' => __('Show Email/Password as Button Option'),
                    'callback' => array($admin_obj, $admin_obj->option_name . '_cb'),
                    'page' => $admin_obj->plugin_name . '-popup-settings',
                    'section' => $admin_obj->option_name . '_popup_options',
                    'args' => array('label' => '_hide_email_form', 'sanitize' => 'sanitize_cb_field', 'default' => 'on')
                ),
                'css_themes' => array(
                    'id' => $admin_obj->option_name . '_flow_css_themes',
                    'title' => __('Choose a Popup Style?'),
                    'callback' => array($admin_obj, $admin_obj->option_name . '_drop_down'),
                    'page' => $admin_obj->plugin_name . '-popup-settings',
                    'section' => $admin_obj->option_name . '_popup_options',
                    'args' => array(
                        'label' => '_flow_css_themes', 'sanitize' => 'ae_connect_sanitize_text_input',
                        'options' => $this->css_themes
                    )
                ),
                'flow_css' => array(
                    'id' => $admin_obj->option_name . '_flow_css',
                    'title' => __('Stylesheet URL (Overrides styles from the above dropdown)'),
                    'callback' => array($admin_obj, $admin_obj->option_name . '_text_field'),
                    'page' => $admin_obj->plugin_name . '-popup-settings',
                    'section' => $admin_obj->option_name . '_popup_options',
                    'args' => array('label' => '_flow_css', 'size' => 100, 'sanitize' => 'ae_connect_sanitize_text_input')
                ),
                'language' => array(
                    'id' => $admin_obj->option_name . '_language',
                    'title' => __('Form Validation Language'),
                    'callback' => array($admin_obj, $admin_obj->option_name . '_drop_down'),
                    'page' => $admin_obj->plugin_name . '-popup-settings',
                    'section' => $admin_obj->option_name . '_popup_options',
                    'args' => array(
                        'label' => '_language', 'sanitize' => 'ae_connect_sanitize_text_input',
                        'options' => $this->language_codes
                    )
                )
            ),
            'popup_text' => array(
                'global_top_title' => array(
                    'id' => $admin_obj->option_name . '_global_top_title',
                    'title' => __(''),
                    'callback' => array($admin_obj, $admin_obj->option_name . '_text_area_field'),
                    'page' => $admin_obj->plugin_name . '-popup-text',
                    'section' => $admin_obj->option_name . '_popup_text_header',
                    'args' => array('label' => '_global_top_title', 'sanitize' => 'ae_connect_sanitize_text_area')
                ),
                'global_bottom_title' => array(
                    'id' => $admin_obj->option_name . '_global_bottom_title',
                    'title' => __(''),
                    'callback' => array($admin_obj, $admin_obj->option_name . '_text_area_field'),
                    'page' => $admin_obj->plugin_name . '-popup-text',
                    'section' => $admin_obj->option_name . '_popup_text_footer',
                    'args' => array('label' => '_global_bottom_title', 'sanitize' => 'ae_connect_sanitize_text_area')
                )
            ),
            'popup_flow_text' => $this->render_flow_text_fields($admin_obj),
            'performance' => array(
                '_disable_local_wp_user' => array(
                    'id' => $admin_obj->option_name . '_disable_local_wp_user',
                    'title' => __('Do not create local user (Checking this also disables local user sessions)'),
                    'callback' => array($admin_obj, $admin_obj->option_name . '_cb'),
                    'page' => $admin_obj->plugin_name . '-performance',
                    'section' => $admin_obj->option_name . '_performance_options',
                    'args' => array('label' => '_disable_local_wp_user', 'sanitize' => 'sanitize_cb_field'),
                ),
                '_disable_user_log_in_session' => array(
                    'id' => $admin_obj->option_name . '_disable_user_log_in_session',
                    'title' => __('Do not sign in local user'),
                    'callback' => array($admin_obj, $admin_obj->option_name . '_cb'),
                    'page' => $admin_obj->plugin_name . '-performance',
                    'section' => $admin_obj->option_name . '_performance_options',
                    'args' => array('label' => '_disable_user_log_in_session', 'sanitize' => 'sanitize_cb_field'), 'dependency' => $admin_obj->option_name . '_disable_local_wp_user'
                )
            )
        );
        return $fields;
    }

    /**
     * Renders social fields and returns a list of preprocessed social fields
     * to be added to the global $fields variable
     * @param  PHP object $admin_obj Instantiating class' object
     * @return array  $social_fields
     */
    private function render_social_fields($admin_obj) {
        // Preprocess social networks data:
        $social_logins = get_option('ae_connect_social_logins');

        // If the get_option returned false, set $social logins to an empty array:
        if (empty($social_logins)) {
            $social_logins = array();
        }

        $social_fields = array();

        foreach ($social_logins as $login) {
            $social_fields[$login] = array(
                'id' => $admin_obj->option_name . '_' . $login,
                'title' => ucwords($login),
                'callback' => array($admin_obj, $admin_obj->option_name . '_cb'),
                'page' => $admin_obj->plugin_name . '-general-social',
                'section' => $admin_obj->option_name . '_networks',
                'args' => array('label' => '_' . $login, 'sanitize' => 'sanitize_cb_field')
            );
        }
        return $social_fields;
    }

    /**
     * Renders social fields and returns a list of preprocessed social fields
     * to be added to the global $fields variable
     * @param  PHP object $admin_obj Instantiating class' object
     * @return array  $extra_fields
     */
    private function render_extra_fields($admin_obj) {
        // Preprocess social networks data:
        $e_fields = array(
            'First Name' => 'firstname',
            'Last Name' => 'surname',
            'Email' => 'email',
            'Address' => 'address',
            'Address/line2' => 'addressline2',
            'City' => 'city',
            'State/Province' => 'state',
            'Country' => 'country',
            'Postal Code' => 'postcode',
            'Home Phone' => 'homephone',
            'Mobile Phone' => 'mobilephone',
            'Username' => 'username',
            'Website' => 'website',
            'Tell Us About Yourself' => 'bio',
            'Gender' => 'gender',
            'Birthdate' => 'birthdate'
        );
        // Save these extra fields to the DB to be used to populate aeJS settings
        update_option('ae_connect_extra_fields_assoc_array', $e_fields);

        $extra_fields = array();

        foreach ($e_fields as $readable_name => $field) {
            // Setup enable disable CB fields
            $extra_fields[$field] = array(
                'id' => $admin_obj->option_name . '_' . $field,
                'title' => ucwords($readable_name),
                'callback' => array($admin_obj, $admin_obj->option_name . '_extra_field_options'),
                'page' => $admin_obj->plugin_name . '-general-user-fields',
                'section' => $admin_obj->option_name . '_user_fields',
                'args' => array('label' => '_' . $field, 'type' => 'enable', 'sanitize' => 'sanitize_cb_field')
            );
            // Setup require CB fields
            // Of the form ae_connect_required_auth_window
            $extra_fields['required_' . $field] = array(
                'id' => $admin_obj->option_name . '_required_' . $field,
                'title' => '',
                'callback' => array($admin_obj, $admin_obj->option_name . '_extra_field_options'),
                'page' => $admin_obj->plugin_name . '-general-user-fields',
                'section' => $admin_obj->option_name . '_user_fields',
                'args' => array('label' => '_required_' . $field, 'type' => 'required', 'sanitize' => 'sanitize_cb_field', 'dependency' => '_' . $field)
            );
            // Setup label text fields
            // of the form: ae_connect_label_auth_window
            $extra_fields['label_' . $field] = array(
                'id' => $admin_obj->option_name . '_label_' . $field,
                'title' => '',
                'callback' => array($admin_obj, $admin_obj->option_name . '_extra_field_options'),
                'page' => $admin_obj->plugin_name . '-general-user-fields',
                'section' => $admin_obj->option_name . '_user_fields',
                'args' => array('label' => '_label_' . $field, 'size' => 50, 'type' => 'label', 'sanitize' => 'ae_connect_sanitize_text_input')
            );
        }

        return $extra_fields;
    }

    private function render_flow_text_fields($admin_obj) {
        $flow_text_fields = array();
        $f_txt = array(
            "Error Screen Title" => "error_header",
            "Login Screen Title" => "login_header",
            "Email Login Button Text" => "login_button",
            "Social Login Button Text" => "login_with_button",
            "Register Screen Title" => "register_header",
            "Email Register Button Text" => "register_button",
            "Social Register Button Text" => "register_with_button",
            "Additional Data Screen Header" => "add_info_header",
            "Additional Data Submit Button" => "add_info_button",
            "Reset Password Send Screen Header" => "reset_pw_header",
            "Reset Password Send Screen Message" => "reset_pw_sent",
            "Reset Password Send Screen Instructions" => "reset_pw_instructions",
            "Reset Password Send Button Text" => "reset_pw_button",
            "Reset Password Confirm Screen Hader" => "reset_pw_confirm_header",
            "Reset Password Confirm Button Text" => "reset_pw_confirm_button",
            "Reset Password Confirm Screen Instructions" => "reset_pw_confirm_instructions",
            "Reset Password Success Screen Header" => "reset_pw_done_header",
            "Reset Password Success Screen Message" => "reset_pw_done_message",
            "Reset Password Success OK Button" => "reset_pw_done_button",
            "Verify Email Screen Header" => "verify_email_header",
            "Verify Email Screen Message" => "verify_email_sent",
            "Verify Email Screen Instructions" => "verify_email_instructions",
            "Verify Email Screen Retry Button" => "verify_email_retry_button",
            "Verify Email Success Screen Header" => "verify_email_success_header",
            "Verify Email Success Screen Message" => "verify_email_success_message",
            "Verify Email Success OK Button" => "verify_email_success_button",
            "Verify Email Error Screen Header" => "verify_email_error_header",
            "Verify Email Error Screen Message" => "verify_email_error_message",
            "Forgot Password Link Text" => "forgot_password_link",
            "Opt-in Title Text" => "optins_title",
            "Existing Account Link Text" => "have_account_link",
            "Help Link Text" => "need_help_link",
            "Create Account Link Text" => "create_account_link",
        );
        // Save these extra info to the DB to be used to populate aeJS settings
        update_option('flow_text_assoc_array', $f_txt);

        foreach ($f_txt as $readable_name => $field) {
            $default_text = $this->flow_text_defaults[$field];
            $flow_text_fields[$field] = array(
                'id' => $admin_obj->option_name . '_' . $field,
                'title' => ucwords($readable_name),
                'callback' => array($admin_obj, $admin_obj->option_name . '_text_field'),
                'page' => $admin_obj->plugin_name . '-popup-text',
                'section' => $admin_obj->option_name . '_labels_messages',
                'args' => array('label' => '_' . $field, 'size' => 50, 'sanitize' => 'ae_connect_sanitize_text_input', 'default' => $default_text)
            );
        }

        return $flow_text_fields;
    }

    private function render_email_format_fields($admin_obj) {
        $email_format_fields = array();
        $email_formats = array(
            array(
                "field" => "background_color",
                "human_readable" => "Background Color",
                "section" => "email_format"),
            array(
                "field" => "font_size",
                "human_readable" => "Font Size",
                "section" => "email_format"),
            array(
                "field" => "font_family",
                "human_readable" => "Font Family",
                "section" => "email_format"),
            array(
                "field" => "font_color",
                "human_readable" => "Font Color",
                "section" => "email_format"),
            array(
                "field" => "show_header",
                "human_readable" => "Show Header",
                "section" => "email_header"),
            array(
                "field" => "header_background_color",
                "human_readable" => "Header Background Color",
                "section" => "email_header"),
            array(
                "field" => "header_font_color",
                "human_readable" => "Header Font Color",
                "section" => "email_header"),
            array(
                "field" => "image_url",
                "human_readable" => "Header Image URL",
                "section" => "email_header"),
            array(
                "field" => "show_footer",
                "human_readable" => "Show Footer",
                "section" => "email_footer"),
            array(
                "field" => "footer_background_color",
                "human_readable" => "Footer Background Color",
                "section" => "email_footer"),
            array(
                "field" => "footer_font_color",
                "human_readable" => "Footer Font Color",
                "section" => "email_footer"),
            array(
                "field" => "logo_link",
                "human_readable" => "Footer Logo Destination URL",
                "section" => "email_footer"),
            array(
                "field" => "logo_img_url",
                "human_readable" => "Footer Logo Image URL",
                "section" => "email_footer"),
            array(
                "field" => "copyright",
                "human_readable" => "Copyright Text",
                "section" => "email_footer"),
            array(
                "field" => "verify_email_subject",
                "human_readable" => "Verify Email Subject",
                "section" => "email_verify"),
            array(
                "field" => "verify_email_message",
                "human_readable" => "Verify Email Text",
                "section" => "email_verify"),
            array(
                "field" => "verify_email_link",
                "human_readable" => "Verify Email Link Text",
                "section" => "email_verify"),
            array(
                "field" => "reset_pw_email_subject",
                "human_readable" => "Reset Password Subject",
                "section" => "email_reset_pword"),
            array(
                "field" => "reset_pw_email_message",
                "human_readable" => "Reset Password Text",
                "section" => "email_reset_pword"),
            array(
                "field" => "reset_pw_email_link",
                "human_readable" => "Reset Email Link Text",
                "section" => "email_reset_pword")
        );

        update_option('ae_connect_email_formats_struct', $email_formats);

        foreach ($email_formats as $field) {
            // Setup enable disable CB fields
            $email_format_fields[$field['field']] = array(
                'id' => $admin_obj->option_name . '_' . $field['field'],
                'title' => ucwords($field['human_readable']),
                'callback' => array($admin_obj, $admin_obj->option_name . '_text_field'),
                'page' => $admin_obj->plugin_name . '-general-email',
                'section' => $admin_obj->option_name . '_' . $field['section'],
                'args' => array('label' => '_' . $field['field'], 'size' => 50, 'sanitize' => 'ae_connect_sanitize_text_input')
            );
        }

        return $email_format_fields;
    }

}
