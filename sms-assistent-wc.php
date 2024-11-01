<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://sms-assistent.by
 * @since             1.0.0
 * @package           Sms_Assistent_Wc
 *
 * @wordpress-plugin
 * Plugin Name:       SMS-assistent for WooCommerce
 * Plugin URI:        https://dev.sms-assistent.by/integraciya-obzor#rec206067682
 * Description:       Module will make you to add SMS features to your WooCommerce store for free. Once configured customers will be notified with an SMS when an Account is registered, Order is placed, Order status is updated etc.
 * Version:           1.0.5
 * Author:            SMS-assistent
 * Author URI:        http://sms-assistent.by
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sms-assistent-wc
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'SMS_ASSISTENT_WC_VERSION', '1.0.5' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-sms-assistent-wc-activator.php
 */
function activate_sms_assistent_wc() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sms-assistent-wc-activator.php';
    Sms_Assistent_Wc_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-sms-assistent-wc-deactivator.php
 */
function deactivate_sms_assistent_wc() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sms-assistent-wc-deactivator.php';
    Sms_Assistent_Wc_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_sms_assistent_wc' );
register_deactivation_hook( __FILE__, 'deactivate_sms_assistent_wc' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-sms-assistent-wc.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_sms_assistent_wc() {

	$plugin = new Sms_Assistent_Wc();
	$plugin->run();

}

run_sms_assistent_wc();
