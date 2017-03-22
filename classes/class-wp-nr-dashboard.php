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

            // TODO: Add network dashboard meta boxes if required
            // add_action( 'wp_network_dashboard_setup', array( $this, 'action_wp_network_dashboard_setup' ) );
		} else {
			add_action( 'admin_menu', array( $this, 'action_admin_menu' ) );
		}

		// save settings
		add_action( 'admin_init', array( $this, 'save_settings' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

        // Add dashboard meta boxes if required.
        add_action( 'wp_dashboard_setup', array( $this, 'action_wp_dashboard_setup') );
	}

	public function admin_enqueue_scripts() {
		wp_enqueue_script( 'nr-dashboard-widget-view', WP_NR_URL . 'js/src/dashboard-widget-view.js',
			array( 'jquery', 'underscore', 'backbone' )
		);

		wp_localize_script( 'nr-dashboard-widget-view', 'WP_NewRelic',
			array(
				'dashboardWidgets' => WP_NR_Helper::dashboard_widgets(),
				'strings' => array(
					'delete'             => esc_html__( 'Delete', 'wp-newrelic' ),
					'visualizationTitle' => esc_html__( 'Untitled Visualization', 'wp-newrelic' ),
				),
			)
		);
	}

	/**
	 * Save settings
	 */
	public function save_settings() {
		$nonce = filter_input( INPUT_POST, 'wp_nr_settings', FILTER_SANITIZE_STRING );

		if ( wp_verify_nonce( $nonce, 'wp_nr_settings' ) ) {

			$account_id = filter_input( INPUT_POST, 'wp_nr_account_id' );
			$capture_url = filter_input( INPUT_POST, 'wp_nr_capture_urls' );
			$disable_amp = filter_input( INPUT_POST, 'wp_nr_disable_amp' );

			if ( absint( $account_id ) <= 1 ) {
				$account_id = false;
			}

			if ( ! empty( $capture_url ) ) {
				$capture_url = true;
			} else {
				$capture_url = false;
			}

			if ( ! empty( $disable_amp ) ) {
				$disable_amp = true;
			} else {
				$disable_amp = false;
			}

			if ( WP_NR_IS_NETWORK_ACTIVE ) {
				update_site_option( 'wp_nr_account_id', $account_id );
				update_site_option( 'wp_nr_capture_urls', $capture_url );
				update_site_option( 'wp_nr_disable_amp', $disable_amp );
			} else {
				update_option( 'wp_nr_account_id', $account_id );
				update_option( 'wp_nr_capture_urls', $capture_url );
				update_option( 'wp_nr_disable_amp', $disable_amp );
			}

			$dashboard_widgets = ! empty( $_POST['wp_nr_dashboard_widgets'] ) ?
				(array) $_POST['wp_nr_dashboard_widgets'] : array();
			$add_dashboard_widget = stripslashes_deep( $_POST['wp_nr_add_dashboard_widget'] );

			// Check if input posted for "additional dashboard widget" was valid
			if ( ! empty( $add_dashboard_widget['embed_html'] ) &&
				preg_match(
					'#<iframe[^>]* src="https://insights-embed.newrelic.com/embedded_widget/([^/"]*)"#',
					$add_dashboard_widget['embed_html'],
					$embed_html_matches ) ) {

				$dashboard_widgets[] = array(
					'title'       => sanitize_text_field( $add_dashboard_widget['title'] ),
					'embedID'     => $embed_html_matches[1],
					'description' => sanitize_text_field( $add_dashboard_widget['description'] )
				);
			}

			update_option( 'wp_nr_dashboard_widgets', $dashboard_widgets );
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
	 * Add dashboard widgets for any embeddable views defined
	 *
	 * @return void
	 */
	public function action_wp_dashboard_setup() {
		$dashboard_widgets = WP_NR_Helper::dashboard_widgets();

		foreach ( $dashboard_widgets as $i => $dashboard_widget ) {

			if ( $embed_id = $dashboard_widget['embedID'] ) {
				add_meta_box(
					sanitize_title( 'new-relic-insights-' . $embed_id ),
					esc_html__( 'New Relic Insights', 'wp-newrelic' ),
					function() use ( $dashboard_widget ) {
						$this->render_dashboard_widget( $dashboard_widget );
					},
					'dashboard', 'side', 'high'
				);
			}
		}

	}

	/**
	 * Render a dashboard widget with an embedded New Relic visualization
	 *
	 * @param array $dashboard_widget Settings for widget: {
	 *   @var string $title       Optional ttitle field
	 *   @ver string $embedID     New Relic visualization ID
	 *   @var string $description (optional) paragraph text, displayed below embed.
	 * }
	 * @return void
	 */
	public function render_dashboard_widget( $dashboard_widget ) {
		if ( ! empty( $dashboard_widget['title'] ) ) {
			echo '<h4>' . esc_html( $dashboard_widget['title'] ) . '</h4>';
		}
			?>
		<div style="position: relative; width: 100%; height: 0; padding-top: 56.25%;">
			<iframe src="<?php echo esc_url( "https://insights-embed.newrelic.com/embedded_widget/{$dashboard_widget['embedID']}" ); ?>"
				style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
				frameborder="0"></iframe>
		</div>
			<?php
		if ( ! empty( $dashboard_widget['description'] ) ) {
			echo '<p class="description">' . wp_kses_post( $dashboard_widget['description' ]) . '</p>';
		}
	}

	/**
	 * Option page
	 */
	public function dashboard_page() {
		$nr_account_id = WP_NR_Helper::nr_account_id();
		$is_capture = WP_NR_Helper::is_capture_url();
		$is_disable_amp = WP_NR_Helper::is_disable_amp();
		$dashboard_widgets = WP_NR_Helper::dashboard_widgets();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'New Relic for WordPress', 'wp-newrelic' ) ?></h1>
			<form method="post" action="">
				<?php
				wp_nonce_field( 'wp_nr_settings', 'wp_nr_settings' );
				?>
				<table class="form-table">
					<tr>
						<th scope="row"><label for="wp_nr_account_id"><?php esc_html_e( 'New Relic Account ID', 'wp-newrelic' ); ?></label></th>
						<td>
						<input type="text" name="wp_nr_account_id" value="<?php echo esc_attr( absint( $nr_account_id ) ); ?>">
							<p class="description"><?php esc_html_e( 'Entering your account ID here helps us provide links to documentation and reports in your New Relic dashboard.', 'wp-newrelic' ) ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="wp_nr_capture_urls"><?php esc_html_e( 'Capture URL Parameters', 'wp-newrelic' ); ?></label></th>
						<td>
							<input type="checkbox" name="wp_nr_capture_urls" <?php checked( true, $is_capture ) ?>>
							<p class="description"><?php esc_html_e( 'Enable this to record parameter passed to PHP script via the URL (everything after the "?" in the URL).', 'wp-newrelic' ) ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="wp_nr_disable_amp"><?php esc_html_e( 'Disable for AMP', 'wp-newrelic' ); ?></label></th>
						<td>
							<input type="checkbox" name="wp_nr_disable_amp" <?php checked( true, $is_disable_amp ) ?>>
							<p class="description"><?php esc_html_e( 'Enable this to disable New Relic for AMP.', 'wp-newrelic' ) ?></p>
						</td>
					</tr>
				</table>
				<h2 class="title"><?php esc_html_e( 'Embeddable reports in dashboard', 'wp-newrelic' ); ?></h2>
				<p><?php echo esc_html__(
						'You may register any number of embeddable New Relic visualizations to be shown as dashboard widgets on this site.',
						'wp-newrelic' );
					if ( $account_id = WP_NR_Helper::nr_account_id() ) {
						echo ' ' . sprintf(
							__( '<a href="%s" target="_blank">See your existing visualizations</a> or <a href="%s" target="_blank">build new reports</a> in the New relic dashboard.', 'wp-newrelic' ),
							esc_url( "https://insights.newrelic.com/accounts/{$account_id}/manage/embeddables" ),
							esc_url( "https://insights.newrelic.com/accounts/{$account_id}/query" )
						);
					}
					?>
				</p>
				<div id="wp-nr-widget-settings-form" ></div>
			<?php submit_button( esc_html__( 'Save Changes', 'wp-newrelic' ), 'submit primary' ); ?>
			</form>
		</div>
		<?php

		require_once( WP_NR_PATH . 'templates/view-dashboard-widget.html' );
		require_once( WP_NR_PATH . 'templates/add-new-dashboard-widget.html' );

	}
}
