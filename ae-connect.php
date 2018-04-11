<?php

/**
 * The AE_Connect bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              avery.dev.appreciationengine.com
 * @since             1.0.0
 * @package           Ae_Connect
 *
 * @wordpress-plugin
 * Plugin Name:       AE Connect
 * Plugin URI:        https://get.theappreciationengine.com
 * Description:       Appreciation Engine's light weight social login tool for wordpress
 * Version:           1.0.0
 * Author:            Appreciation Engine
 * Author URI:        https://get.theappreciationengine.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ae-connect
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

define('PLUGIN_VERSION', '1.0.0');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ae-connect-activator.php
 */
function activate_ae_connect() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-ae-connect-activator.php';
    Ae_Connect_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ae-connect-deactivator.php
 */
function deactivate_ae_connect() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-ae-connect-deactivator.php';
    Ae_Connect_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_ae_connect');
register_deactivation_hook(__FILE__, 'deactivate_ae_connect');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-ae-connect.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ae_connect() {

    $plugin = new Ae_Connect();
    $plugin->run();
}

run_ae_connect();
