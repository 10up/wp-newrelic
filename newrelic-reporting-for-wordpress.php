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
 * Get the minimum version of PHP required by this plugin.
 *
 * @return string Minimum version required.
 */
function wp_nr_minimum_php_requirement() {
	return '7.3.11';
}

/**
 * Checks whether PHP installation meets the minimum requirements
 *
 * @return bool True if meets minimum requirements, false otherwise.
 */
function wp_nr_site_meets_php_requirements() {

	return version_compare( phpversion(), wp_nr_minimum_php_requirement(), '>=' );
}

if ( ! wp_nr_site_meets_php_requirements() ) {
	add_action(
		'admin_notices',
		function() {
			?>
			<div class="notice notice-error">
				<p>
					<?php
					echo wp_kses_post(
						sprintf(
							/* translators: %s: Minimum required PHP version */
							__( 'New Relic Reporting for WordPress requires PHP version %s or later. Please upgrade PHP or disable the plugin.', 'wp-newrelic' ),
							esc_html( wp_nr_minimum_php_requirement() )
						)
					);
					?>
				</p>
			</div>
			<?php
		}
	);
	return;
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
