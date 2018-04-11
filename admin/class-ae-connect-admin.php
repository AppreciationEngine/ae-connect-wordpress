<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * This class is used to render out the backend settings, displays for settings
 * AE_Connect_Fields, and AE_Connect_Sections classes are used in order to preprocess
 * a great number of sections and fields associated with the plugin
 *
 * An instance of this class will check for the API and Instance settings and Initialize
 * the aeJS Framework based upon those values. This is done via the Initialize class
 *
 * Lastly the action that exports all backend settings to front facing javascript where
 * they are used to control the aeJS signup flow is hooked into wordpress via this class
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ae_Connect
 * @subpackage Ae_Connect/admin
 * @author     Appreciation Engine <avery@appreciationengine.com>
 */
class Ae_Connect_Admin {

    /**
     * The options name to be used in this plugin
     *
     * @since  	1.0.0
     * @access 	public
     * @var  	string 		$option_name 	Option name of this plugin
     */
    public $option_name = 'ae_connect';

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   public
     * @var      string    $plugin_name    The ID of this plugin.
     */
    public $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $sections    Plugin's admin sections
     */
    private $sections;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $sections    Plugin's admin fields
     */
    private $fields;

    private $AE_Notices;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {
        include_once 'class-ae-connect-fields.php';
        include_once 'class-ae-connect-sections.php';
        include_once 'class-ae-connect-initialize.php';
        include_once 'ae_notices.php';

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        $AE_Connect_Sections = new AE_Connect_Sections($this);
        $AE_Connect_Fields = new AE_Connect_Fields($this);

        $this->sections = $AE_Connect_Sections->sections;
        $this->fields = $AE_Connect_Fields->get_fields();

        $this->AE_Notices = new AE_Notices();
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        /**
         *
         * An instance of this class should be passed to the run() function
         * defined in Ae_Connect_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Ae_Connect_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/ae-connect-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
         *
         * An instance of this class should be passed to the run() function
         * defined in Ae_Connect_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Ae_Connect_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/ae-connect-admin.js', array('jquery'), $this->version, false);
    }

    /**
     * render the ae_connect plugin menu
     *
     */
    public function ae_connect_plugin_menu() {
        // Add the main plugin menu Link
        $page_title = __('AE Connect Settings', 'ae-connect');
        $menu_title = __('AE Connect Settings', 'ae-connect');
        $capability = 'manage_options';
        $menu_slug = $this->plugin_name;
        $function = array($this, 'display_options_page');
        $icon_url = '';
        $position = 15;

        add_menu_page(
            $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position
        );

        // If the AE Framework has been properly initialized then load the subpages
        if (get_option('ae_connect_initialization') === 'OK') {
            // add Social Registration submenu page:
            add_submenu_page(
                $this->plugin_name, __('General', 'ae-connect'), __('General', 'ae-connect'), 'manage_options', $this->plugin_name . '-general-settings', array($this, 'display_general_page')
            );

            add_submenu_page(
                $this->plugin_name, __('Popup Styling', 'ae-connect'), __('Popup Styling', 'ae-connect'), 'manage_options', $this->plugin_name . '-popup-settings', array($this, 'display_popup_page')
            );

            add_submenu_page(
                $this->plugin_name, __('Performance', 'ae-connect'), __('Performance', 'ae-connect'), 'manage_options', $this->plugin_name . '-performance', array($this, 'display_performance_page')
            );
        }
    }

    public function display_options_page() {
        include_once 'partials/ae-connect-admin-display.php';
    }

    public function display_general_page() {
        include_once 'partials/ae-connect-admin-general-display.php';
    }

    public function display_popup_page() {
        include_once 'partials/ae-connect-admin-popup-display.php';
    }

    public function display_performance_page() {
        include_once 'partials/ae-connect-admin-performance-display.php';
    }

    /**
     * Render all of the settings for the AE Connect Admin
     * Action is loaded in the class-ae-connect.php file
     * @return null
     */
    public function ae_connect_register_all_settings() {
        // Register sections
        foreach ($this->sections as $sub_sections) {
            $this->render_sections($sub_sections);
        }

        // Register fields
        foreach ($this->fields as $sub_fields) {
            $this->render_fields($sub_fields);
        }
    }

    /**
     * A function which renders a list of sections
     * @param  array $sections  a list of sections
     * @return null
     */
    public function render_sections($sections) {
        foreach ($sections as $section) {
            $id = $section['id'];
            $title = $section['title'];
            $callback = $section['callback'];
            $page = $section['page'];

            add_settings_section($id, $title, $callback, $page);
        }
    }

    /**
     * A function which renders a list of fields
     * @param  array $fields  a list of fields
     * @return null
     */
    public function render_fields($fields) {
        foreach ($fields as $field) {
            $id = $field['id'];
            $title = $field['title'];
            $callback = $field['callback'];
            $page = $field['page']; //happens to be the same as the $options_group
            $section = $field['section'];
            $args = $field['args'];

            add_settings_field($id, $title, $callback, $page, $section, $args);
            register_setting($page, $this->option_name . $args['label'], array($this, $args['sanitize']));
        }

        register_setting('ae-connect-general-opt-ins', 'ae-connect-general-opt-ins', array($this, 'sanitize_opt_ins'));
    }

    /**
     * A generic callback to display section content
     * @return null
     */
    public function ae_connect_display_generic_section() {
        echo '';
    }

    public function ae_connect_display_socials_section() {
        echo
            "<p class='client-notice'>
                <i>Note: If you just finished creating a social application visit the <a href='/wp-admin/admin.php?page=ae-connect'>AE Connect Settings page</a> and press 'Save Changes'. Presto: Your new social app will appear on this page.</i>
            </p>";
    }

    public function ae_connect_display_email_options_section() {
        echo
            "<p class='client-notice'>
                <i>Note: If you check \"Site Requires Verified Email Before Login\" then you must also check \"Send Email Verification During Registration\". Otherwise, it is expected
                that you have the developer skills to handle sending verification emails manually, through aeJS or other methods.</i>
            </p>";
    }

    public function ae_connect_cb($args) {
        $label = $args['label'];
        $cb_checked = get_option($this->option_name . $label);
        $input_tag = $input_tag = '<input type="checkbox" name="' . $this->option_name . $label . '" '
            . 'id="' . $this->option_name . $label . '"' . ' > ';

        // Check for default value
        // A field will only have a default value if it is TRUE by default
        // or FALSE and aeJS is True by default
        if (!$cb_checked && array_key_exists('default', $args)) {
            $default = $args['default'];
            if ($default == "on") {
                $input_tag = '<input type="checkbox" name="' . $this->option_name . $label . '" '
                    . 'id="' . $this->option_name . $label . '"' . "checked" . ' > ';
            }
            // Set default value in the DB
            update_option($this->option_name . $label, $default);
        }

        if ($cb_checked == "on") {
            $input_tag = '<input type="checkbox" name="' . $this->option_name . $label . '" '
                . 'id="' . $this->option_name . $label . '"' . "checked" . ' > ';
            update_option($this->option_name . $label, $cb_checked);
        } elseif ($cb_checked === '') {
            $input_tag = '<input type="checkbox" name="' . $this->option_name . $label . '" '
                . 'id="' . $this->option_name . $label . '"' . ' > ';
            update_option($this->option_name . $label, $cb_checked);
        }

        echo $input_tag;
    }

    // Render generic sections' text field
    public function ae_connect_text_field($args) {
        $label = $args['label'];
        $size = $args['size'];
        $txt = get_option($this->option_name . $label) ? get_option($this->option_name . $label) : '';

        $default_value = '';
        $placeholder = '';
        // if the user input is blank, fill with the default if there exists one.
        if (array_key_exists('placeholder_txt', $args) && empty($txt)) {
            $placeholder = $args['placeholder_txt'];
        }
        if (array_key_exists('default', $args) && empty($txt)) {
            $default_value = $args['default'];
            $txt = $default_value;
        }

        echo '<input type="text" placeholder="' . $placeholder . '" size="' . $size . '" name="' . $this->option_name . $label . '" ' . 'id="' . $this->option_name . $label . '"'
        . ' value="' . htmlentities($txt, ENT_QUOTES, 'UTF-8') . '"' . ' > ';

        // if this field has a label for the input <label>
        if (array_key_exists('for_label_element', $args)) {
            echo '<label for="' . $this->option_name . $label . '">' . $args['for_label_element'] . '</label>';
        }
    }

    // Render Generic Dropdown menu
    public function ae_connect_drop_down($args) {
        $label = $args['label'];
        // Options is an index in $args which contains a key value pair list of
        // option name => option value
        $options = $args['options'];

        $selection = get_option($this->option_name . $label) ? get_option($this->option_name . $label) : '';

        echo '<select name="' . $this->option_name . $label . '">';
        foreach ($options as $name => $value) {
            if ($selection == $value) {
                echo '<option value="' . $value . '" selected="selected" >' . $name . '</option>';
            } else {
                echo '<option value="' . $value . '">' . $name . '</option>';
            }
        }
        echo '</select>';
    }

    // Call back for displaying API field
    public function ae_connect_api_field($args) {
        $label = $args['label'];
        $size = $args['size'];
        $txt = get_option($this->option_name . $label) ? get_option($this->option_name . $label) : '';
        // echo '<br> This is the key that is getting saved to the DB: ' . $this->option_name . $label . '<br>';

        echo '<input type="text" size="' . $size . '" name="' . $this->option_name . $label . '" ' . 'id="' . $this->option_name . $label . '"'
        . ' value="' . htmlentities($txt, ENT_QUOTES, 'UTF-8') . '"' . ' > ';

        // Make API calls and initialize AE data if the API key is set
        if ($txt != '') {
            $Init = new Initialize();
            $Init->ae_connect_init_with_api_key($txt);
            $status = get_option('ae_connect_initialization');
            if($status == 'OK') {
                echo '<p style="display: none;" id="init_status" >' . $status . '</p>';
            }
        }
    }

    public function ae_connect_text_area_field($args) {
        $label = $args['label'];
        $txt = get_option($this->option_name . $label) ? get_option($this->option_name . $label) : '';

        echo '<textarea rows="4" cols="50" name="' . $this->option_name . $label .
        '">' . htmlentities($txt, ENT_QUOTES, 'UTF-8') . '</textarea>';
    }

    /**
    * Render ask for CB, Require CB, and Label Txt Field
    * @return [type] [description]
    */
   public function ae_connect_extra_field_options($args) {
       // Echo out the name of the extra field (AKA Additional data)
       $label = $args['label'];

       $cb_checked = get_option($this->option_name . $label);

       // $this->AE_Notices->set_notice('User fields successfully changed.', 'updated');
       if ($label == '_state') {
           if ($cb_checked == 'on') {
               update_option($this->option_name . '_country', 'on');
           }
       }
       // echo '<br> This is the key that is getting saved to the DB: ' . $this->option_name . $label . '<br>';
       // If the current CB being processed is for the required field and the associated enable field is
       // not checked then set the value of cb_checked to a blank string and disable the required field
       // in the DB
       if ($args['type'] == 'required') {
           $dependency = $args['dependency']; // The suffix name of the setting's enable field
           $enabled = get_option($this->option_name . $dependency);
           if ($enabled != 'on') {
               $cb_checked = '';
               update_option($this->option_name . $label, '');
           }
       }
       // Check for each different sub extra field setting:
       // If the current register_setting call is for the required field or main field
       if ($args['type'] == 'required' || $args['type'] == 'enable') {

           echo '<div id="' . $label . '" class="' . $args['type'] . '">';

           // Label for the CB
           echo '<label for="' . $this->option_name . $label . '">' . $args['type'] . ' </label>';

           if ($cb_checked == "on") {
               echo '<input type="checkbox" name="' . $this->option_name . $label . '" '
               . 'class="' . $args['type'] . ' ae-extra-fields"' . "checked" . ' > ';
           } else {
               echo '<input type="checkbox" name="' . $this->option_name . $label . '" '
               . 'class="' . $args['type'] . ' ae-extra-fields"' . ' > ';
           }

           echo '</div>';
       }
       // If the current register_setting call is for the label field
       elseif ($args['type'] == 'label') {
           $size = isset($args['size']) ? $args['size'] : '';
           $txt = get_option($this->option_name . $label) ? get_option($this->option_name . $label) : '';
           // echo '<br> This is the key that is getting saved to the DB: ' . $this->option_name . $label . '<br>';
           echo '<label for="' . $this->option_name . $label . '">Label </label>';
           echo '<input type="text" size="' . $size . '" name="' . $this->option_name . $label . '" ' . 'id="' . $this->option_name . $label . '"'
           . ' value="' . htmlentities($txt, ENT_QUOTES, 'UTF-8') . '"' . ' > ';
       }
   }

    public function ae_connect_opt_ins($args) {
        $size = 50;
        $label = $args['label']; // _opt_in_fields[0]
        $raw_label = $args['raw_label']; // _opt_in_fields
        $opt_ins_array = get_option('ae_connect' . $label, array('label' => '', 'segmentid' => '', 'checked' => ''));

        $opt_in_label = $opt_ins_array['label'];
        $opt_in_segmentid = $opt_ins_array['segmentid'];
        $opt_in_checked = $opt_ins_array['checked'];

        echo '<input type="text" size="' . $size . '"
        name="' . $this->option_name . $label . '[\'label\']" ' . 'id="' . $this->option_name . $raw_label . '_label"'
        . ' value="' . htmlentities($opt_in_label, ENT_QUOTES, 'UTF-8') . '"' . ' > ';


        echo '<input type="text" size="' . $size . '"
        name="' . $this->option_name . $label . '[\'segmentid\']" ' . 'id="' . $this->option_name . $raw_label . '_segmentid"'
        . ' value="' . htmlentities($opt_in_segmentid, ENT_QUOTES, 'UTF-8') . '"' . ' > ';

        echo '<input type="checkbox" size="' . $size . '"
        name="' . $this->option_name . $label . '[\'checked\']" ' . 'id="' . $this->option_name . $raw_label . '_checked"'
        . ' value="' . htmlentities($opt_in_checked, ENT_QUOTES, 'UTF-8') . '"' . ' > ';
    }

    // A callback for Sanitizing text inputs
    public function ae_connect_sanitize_text_input($raw_text) {
        $sanitary_text = sanitize_text_field($raw_text);

        return $sanitary_text;
    }

    // A callback for Sanitizing text areas
    public function ae_connect_sanitize_text_area($raw_text) {
        $sanitary_text = wp_kses_post($raw_text);

        return $sanitary_text;
    }

    // A callback for sanitizing Checkbox fields
    public function sanitize_cb_field($raw_input) {
        if (!empty($raw_input)) {
            return "on";
        }
    }

    // A callback for sanitizing extra fields
    public function sanitize_extra_field($raw_input) {
        $sanitary_text = sanitize_text_field($raw_input);

        return $sanitary_text;
    }

    // A callback for sanitizing extra fields
    public function sanitize_opt_ins($raw_input_array) {
        $sanitized_input = $raw_input_array;
        // $sanitary_text = sanitize_text_field($raw_input);
        $i = 0;
        foreach ($raw_input_array as $raw_opt_in) {
            // If the checkbox has not been checked, set it.
            if (!array_key_exists('checked', $raw_opt_in)) {
                $raw_opt_in['checked'] = false;
            }
            foreach ($raw_opt_in as $field_name => $raw_field) {

                $sanitized_input[$i][$field_name] = sanitize_text_field($raw_field);
                // convert checked fields to booleans
                if ($field_name == 'checked' && $raw_field == 'true') {
                    $sanitized_input[$i][$field_name] = true;
                } elseif ($raw_field == 'false' || $raw_field == false) {
                    $sanitized_input[$i][$field_name] = false;
                }
            }
            $i++;
        }
        return $sanitized_input;
    }

    /**
     * [ae_segmentid_metabox description]
     * @return [type] [description]
     */
    public function ae_segmentid_metabox() {
        // add_meta_box( string $id, string $title, callable $callback, string|array|WP_Screen $screen = null, string $context = 'advanced', string $priority = 'default', array $callback_args = null )
        add_meta_box(
            'ae_segmentid_metabox', 'Segment ID', array($this, 'ae_segmentid_metabox_html')
        );
    }

    /**
     * [ae_segmentid_metabox_html description]
     * @param  [type] $post [description]
     * @return [type]       [description]
     */
    public function ae_segmentid_metabox_html($post) {
        $value = get_post_meta($post->ID, 'ae_segmentid_metabox_value', true);
        ?>
        <h4>Enter an AE Segment ID in order to capture user visit data</h4>
        <label for="ae_segmentid">Segment ID</label>
        <?php if (empty($value)): ?>
            <input type="number" name="ae_segmentid" id="ae_segmentid" class="postbox" />
        <?php else: ?>
            <input type="number" name="ae_segmentid" id="ae_segmentid" class="postbox" value="<?php echo $value ?>"/>
        <?php endif; ?>
        <?php
    }

    function ae_segmentid_metabox_save($post_id) {
        if (array_key_exists('ae_segmentid', $_POST)) {
            update_post_meta(
                $post_id, 'ae_segmentid_metabox_value', $_POST['ae_segmentid']
            );
        }
    }

}
