<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://neuropassenger.ru
 * @since             1.0.0
 * @package           Bs_Cf7m
 *
 * @wordpress-plugin
 * Plugin Name:       Contact Form 7 Monitor
 * Plugin URI:        https://github.com/Neuropassenger/bs-cf7m
 * Description:       Tracking Contact Form 7.
 * Version:           1.3.5
 * Author:            Oleg Sokolov
 * Author URI:        https://neuropassenger.ru
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bs-cf7m
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
define( 'BS_CF7M_VERSION', '1.3.5' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-bs-cf7m-activator.php
 */
function activate_bs_cf7m() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bs-cf7m-activator.php';
	Bs_Cf7m_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-bs-cf7m-deactivator.php
 */
function deactivate_bs_cf7m() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bs-cf7m-deactivator.php';
	Bs_Cf7m_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_bs_cf7m' );
register_deactivation_hook( __FILE__, 'deactivate_bs_cf7m' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-bs-cf7m.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_bs_cf7m() {

	$plugin = new Bs_Cf7m();
	$plugin->run();

}
run_bs_cf7m();
