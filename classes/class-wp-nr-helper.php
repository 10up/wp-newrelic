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
