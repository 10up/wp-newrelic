<?php

/**
 * Class WP_NR
 *
 * Main plugin class
 */
class WP_NR {

	public function __construct() {

		if ( extension_loaded( 'newrelic' ) ) {
			$this->include_files();
			$this->init();
		} else {
			// enable a bypass for installations where you might want to install the plugin only on a specified set of servers
			if ( defined( 'WP_NR_DISABLE_INSTALL_NOTICE' ) && true === WP_NR_DISABLE_INSTALL_NOTICE ) {
				return;
			}

			if ( WP_NR_IS_NETWORK_ACTIVE ) {
				add_action( 'network_admin_notices', array( $this, 'wp_nr_not_installed_notice' ) );
			} else {
				add_action( 'admin_notices', array( $this, 'wp_nr_not_installed_notice' ) );
			}
		}

	}

	/**
	 * Include files
	 */
	public function include_files() {
		require_once( WP_NR_PATH . 'classes/class-wp-nr-helper.php' );
		require_once( WP_NR_PATH . 'classes/class-wp-nr-apm.php' );
		require_once( WP_NR_PATH . 'classes/class-wp-nr-dashboard.php' );
	}

	/**
	 * Init plugin functionalities
	 */
	public function init() {
		if ( is_admin() ) {
			new WP_NR_Dashboard();
		}
		new WP_NR_APM();
	}

	/**
	 * Admin notice if New Relic extension is not loaded
	 */
	public function wp_nr_not_installed_notice() {
		?>
		<div class="error"><p><strong>WP New Relic: </strong><?php esc_html_e( 'New Relic is not installed.', 'wp-newrelic' ) ?></p></div>
		<?php
	}
}
