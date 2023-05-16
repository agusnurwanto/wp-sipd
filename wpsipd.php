<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/agusnurwanto
 * @since             1.0.0
 * @package           Wpsipd
 *
 * @wordpress-plugin
 * Plugin Name:       WP SIPD
 * Plugin URI:        https://github.com/agusnurwanto/wp-sipd
 * Description:       Aplikasi Open Source untuk backup data SIPD lokal
 * Version:           1.0.25
 * Author:            Agus Nurwanto
 * Author URI:        https://github.com/agusnurwanto
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wpsipd
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WPSIPD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WPSIPD_PLUGIN_PATH', plugin_dir_path(__FILE__) );
define( 'WPSIPD_API_KEY', '_crb_api_key_extension' );

// ============== https://carbonfields.net/ ================
if(!defined('Carbon_Fields_Plugin\PLUGIN_FILE')){
	define( 'Carbon_Fields_Plugin\PLUGIN_FILE', __FILE__ );
	define( 'Carbon_Fields_Plugin\RELATIVE_PLUGIN_FILE', basename( dirname( \Carbon_Fields_Plugin\PLUGIN_FILE ) ) . '/' . basename( \Carbon_Fields_Plugin\PLUGIN_FILE ) );
}

add_action( 'after_setup_theme', 'carbon_fields_boot_plugin' );
if(!function_exists('carbon_fields_boot_plugin')){
	function carbon_fields_boot_plugin() {
		if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
			require( __DIR__ . '/vendor/autoload.php' );
		}
		\Carbon_Fields\Carbon_Fields::boot();
		if ( is_admin() ) {
			\Carbon_Fields_Plugin\Libraries\Plugin_Update_Warning\Plugin_Update_Warning::boot();
		}
	}
}
// ============== https://carbonfields.net/ ================

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WPSIPD_VERSION', '1.0.25' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wpsipd-activator.php
 */
function activate_wpsipd() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpsipd-activator.php';
	Wpsipd_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wpsipd-deactivator.php
 */
function deactivate_wpsipd() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpsipd-deactivator.php';
	Wpsipd_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wpsipd' );
register_deactivation_hook( __FILE__, 'deactivate_wpsipd' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wpsipd.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wpsipd() {

	$plugin = new Wpsipd();
	$plugin->run();

}
run_wpsipd();
