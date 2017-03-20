<?php

/**
 * Class for helper functions library
 *
 * Class WP_NR_Helper
 */
class WP_NR_Helper {

	/**
	 * Check if capture url setting is enabled or not
	 *
	 * @return bool
	 */
	public static function is_capture_url() {

		return self::get_setting( 'wp_nr_capture_urls' );
	}

	/**
	 * Check if disable for AMP setting is enabled or not
	 *
	 * @return bool
	 */
	public static function is_disable_amp() {
		return self::get_setting( 'wp_nr_disable_amp' );
	}

	/**
	 * Get details of any registered dashboard widget embeddables
	 *
	 * @return array array of dashboard widgets: [{
	 *   @var string $title
	 *   @var string $embed_id    Required
	 *   @var string $description
	 * }]
	 */
    public static function dashboard_widgets() {
		$dashboard_widgets = get_option( 'wp_nr_dashboard_widgets', array() );

		return array_filter( $dashboard_widgets, function( $dashboard_widget ) {
			return ! empty( $dashboard_widget['embedID'] );
		} );
    }

	/**
	 * Get single setting
	 *
	 * @param $setting
	 *
	 * @return bool
	 */
	public static function get_setting( $setting ) {

		if ( WP_NR_IS_NETWORK_ACTIVE ) {
			$return = (bool) get_site_option( $setting, false );
		} else {
			$return = (bool) get_option( $setting, false );
		}

		return $return;
	}

}
