<?php

/**
 * class of callbacks for registering AE Widgets
 *
 * @link       avery.dev.appreciationengine.com
 * @since      1.0.0
 *
 * @package    Ae_Connect
 * @subpackage Ae_Connect/public
 */
class AE_Register_Widgets {

    public function __construct() {
        $this->load_dependencies();
    }

    private function load_dependencies() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'widgets/ae-on-page-form-widget.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'widgets/ae-link-widget.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'widgets/ae-window-widget.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'widgets/ae-logout-widget.php';
    }

    public function register_AE_On_Page_Form_Widget() {
        register_widget('AE_On_Page_Form_Widget');
    }

    public function register_AE_Link_Widget() {
        register_widget('AE_Link_Widget');
    }

    public function register_AE_Window_Widget() {
        register_widget('AE_Window_Widget');
    }

    public function register_AE_Logout_Widget() {
        register_widget('AE_Logout_Widget');
    }

}
