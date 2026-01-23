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

			// Skip notice in WordPress Playground if explicitly disabled
			if ( self::is_playground() && defined( 'WP_NR_DISABLE_PLAYGROUND_NOTICE' ) && true === WP_NR_DISABLE_PLAYGROUND_NOTICE ) {
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
	 * Check if running in WordPress Playground environment
	 *
	 * @return bool True if running in WordPress Playground, false otherwise.
	 */
	public static function is_playground() {
		// Check for WordPress Playground environment indicators
		if ( defined( 'WP_PLAYGROUND' ) && WP_PLAYGROUND ) {
			return true;
		}

		// Check for Playground-specific constants or environment variables
		if ( defined( 'PLAYGROUND_ENVIRONMENT' ) && PLAYGROUND_ENVIRONMENT ) {
			return true;
		}

		// Check for Playground user agent or server variables
		if ( isset( $_SERVER['HTTP_USER_AGENT'] ) && strpos( $_SERVER['HTTP_USER_AGENT'], 'WordPress-Playground' ) !== false ) {
			return true;
		}

		// Check if running in browser-based PHP environment (Playground uses WebAssembly)
		if ( isset( $_SERVER['HTTP_HOST'] ) && strpos( $_SERVER['HTTP_HOST'], 'playground.wordpress.net' ) !== false ) {
			return true;
		}

		return false;
	}

	/**
	 * Admin notice if New Relic extension is not loaded
	 */
	public function wp_nr_not_installed_notice() {
		$is_playground = self::is_playground();

		if ( $is_playground ) {
			?>
			<div class="notice notice-info">
				<p>
					<strong><?php esc_html_e( 'WP New Relic: ', 'wp-newrelic' ); ?></strong>
					<?php
					echo wp_kses_post(
						sprintf(
							/* translators: %s: WordPress Playground documentation link */
							__( 'You are running WordPress in Playground. The New Relic PHP extension is not available in this browser-based environment. This plugin is designed for production environments with the New Relic PHP agent installed. <a href="%s" target="_blank">Learn more about WordPress Playground</a>.', 'wp-newrelic' ),
							esc_url( 'https://wordpress.github.io/wordpress-playground/' )
						)
					);
					?>
				</p>
			</div>
			<?php
		} else {
			?>
			<div class="error"><p><strong>WP New Relic: </strong><?php esc_html_e( 'New Relic is not installed.', 'wp-newrelic' ) ?></p></div>
			<?php
		}
	}
}
