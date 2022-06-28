<?php

/**
 * Plugin Name:       New Relic Reporting for WordPress
 * Plugin URI:        https://wordpress.org/plugins/wp-newrelic
 * Description:       New Relic APM reports for WordPress
 * Version:           1.3.2
 * Requires at least: 4.0
 * Requires PHP:      7.3.11
 * Author:            10up
 * Author URI:        https://10up.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wp-newrelic
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'WP_NR_URL' ) ) {
	define( 'WP_NR_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'WP_NR_PATH' ) ) {
	define( 'WP_NR_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'WP_NR_BASENAME' ) ) {
	define( 'WP_NR_BASENAME', plugin_basename( __FILE__ ) );
}

/**
 * Check if plugin is network active.
 *
 * @return bool
 */
function wp_nr_is_network_active() {
	$plugins = get_site_option( 'active_sitewide_plugins' );

	if ( is_multisite() && isset( $plugins[ WP_NR_BASENAME ] ) ) {
		return true;
	}

	return false;
}

if ( ! defined( 'WP_NR_IS_NETWORK_ACTIVE' ) ) {
	define( 'WP_NR_IS_NETWORK_ACTIVE', wp_nr_is_network_active() );
}

require_once( WP_NR_PATH . 'classes/class-wp-nr.php' );

new WP_NR();
