<?php

/**
 * Class WP_NewRelic_Dashboard
 *
 * Class to handle options page and saving options
 */
class WP_NR_Dashboard {

	public function __construct() {

		if ( WP_NR_IS_NETWORK_ACTIVE ) {
			// Network setting
			add_action( 'network_admin_menu', array( $this, 'action_admin_menu' ) );
		} else {
			add_action( 'admin_menu', array( $this, 'action_admin_menu' ) );
		}

		// save settings
		add_action( 'admin_init', array( $this, 'save_settings' ) );
	}

	/**
	 * Save settings
	 */
	public function save_settings() {
		$nonce = filter_input( INPUT_POST, 'wp_nr_settings', FILTER_SANITIZE_STRING );

		if ( wp_verify_nonce( $nonce, 'wp_nr_settings' ) ) {
			$capture_url = filter_input( INPUT_POST, 'wp_nr_capture_urls' );

			if ( ! empty( $capture_url ) ) {
				$capture_url = true;
			} else {
				$capture_url = false;
			}

			if ( WP_NR_IS_NETWORK_ACTIVE ) {
				update_site_option( 'wp_nr_capture_urls', $capture_url );
			} else {
				update_option( 'wp_nr_capture_urls', $capture_url );
			}
		}
	}

	/**
	 * Add menu page
	 */
	public function action_admin_menu() {
		if ( WP_NR_IS_NETWORK_ACTIVE ) {
			add_menu_page(
				'New Relic',
				'New Relic',
				'manage_network',
				'wp-nr-settings',
				array( $this, 'dashboard_page' ),
				'',
				20
			);
		} else {
			add_management_page(
				'New Relic',
				'New Relic',
				'manage_options',
				'wp-nr-settings',
				array( $this, 'dashboard_page' )
			);
		}
	}

	/**
	 * Option page
	 */
	public function dashboard_page() {
		$is_capture = WP_NR_Helper::is_capture_url();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'New Relic for WordPress', 'wp-newrelic' ) ?></h1>
			<form method="post" action="">
				<?php
				wp_nonce_field( 'wp_nr_settings', 'wp_nr_settings' );
				?>
				<table class="form-table">
					<tr>
						<th scope="row"><label for="wp_nr_capture_urls"><?php esc_html_e( 'Capture URL Parameters', 'wp-newrelic' ); ?></label></th>
						<td>
							<input type="checkbox" name="wp_nr_capture_urls" <?php checked( true, $is_capture ) ?>>
							<p class="description"><?php esc_html_e( 'Enable this to record parameter passed to PHP script via the URL (everything after the "?" in the URL).', 'wp-newrelic' ) ?></p>
						</td>
					</tr>
				</table>
				<?php
				submit_button( esc_html__( 'Save Changes', 'wp-newrelic' ), 'submit primary' );
				?>
			</form>
		</div>
		<?php
	}
}
