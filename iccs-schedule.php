<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://michaeldfoley.com
 * @since             1.0.0
 * @package           Iccs_Schedule
 *
 * @wordpress-plugin
 * Plugin Name:       ICCS Schedule
 * Plugin URI:        https://iccs.fordham.edu
 * Description:       A plugin to add a conference schedule to the ICCS website.
 * Version:           1.0.0
 * Author:            Michael Foley
 * Author URI:        https://michaeldfoley.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       iccs-schedule
 * Domain Path:       /languages
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Helper function for prettying up errors
 * @since 1.0.0
 *
 * @param string    $message        The error message
 * @param string    $subtitle       They category of the error
 * @param string    $title          Default title override
 * @param bool      $return         Whether to return the message
 *
 * @return string
 */
function iccs_schedule_error($message, $subtitle = '', $title = '', $return = false) {
    $title = $title ?: __('ICCS Schedule &rsaquo; Error', 'iccs-schedule');
    if ($return) {
        return sprintf('<div class="error"><p><strong>%s</strong><br>%s</p><p>%s</p></div>', $title, $subtitle, $message);
    }
    wp_die(sprintf('<h1>%s<br><small>%s</small></h1><p>%s</p>', $title, $subtitle, $message));

};

/**
 * Dependency error message
 * @since 1.0.0
 *
 * @return string
 */
function iccs_schedule_dependency_error() {
    echo iccs_schedule_error(__('Please reinstall the plugin.', 'iccs-schedule'), __('Dependencies not found.', 'iccs-schedule'), '', true);
}

/**
 * Activation hook
 * @since 1.0.0
 */
function iccs_schedule_activate() {
    $plugin_basename = plugin_basename( __FILE__ );
    /**
     * Maybe flush rewrite rules
     */
    if ( ! get_option( 'iccs_flush_rewrite_rules' ) ) {
        add_option( 'iccs_flush_rewrite_rules', true );
    }
    /**
     * Ensure compatible version of PHP is used
     */
    if (version_compare('7.1', phpversion(), '>=')) {
        deactivate_plugins($plugin_basename);
        if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );
        iccs_schedule_error(__('You must be using PHP 7.1 or greater.', 'fu-iccs'), __('Invalid PHP version', 'iccs-schedule'));
    }

    /**
     * Ensure compatible version of WordPress is used
     */
    if (version_compare('4.7.0', get_bloginfo('version'), '>=')) {
        deactivate_plugins($plugin_basename);
        if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );
        iccs_schedule_error(__('You must be using WordPress 4.7.0 or greater.', 'fu-iccs'), __('Invalid WordPress version', 'iccs-schedule'));
    }
}
register_activation_hook(__FILE__, 'iccs_schedule_activate');

/**
 * Instantiate class
 * @since 1.0.0
 */
function iccs_schedule_events_load() {
    $plugin_path = plugin_dir_path( __FILE__ );
    require_once $plugin_path . 'src/Main.php';

    /**
     * Ensure dependencies are loaded
     */
    if (!class_exists('Iccs__Schedule__Main')) {
        add_action( 'admin_notices', 'iccs_schedule_dependency_error' );
        return;
    }
    Iccs__Schedule__Main::register(__FILE__);
}
add_action( 'plugins_loaded', 'iccs_schedule_events_load', 2 );
