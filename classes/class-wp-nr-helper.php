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
		
		if( WP_NR_IS_NETWORK_ACTIVE ) {
			$return = (bool) get_site_option( 'wp_nr_capture_urls', false );
		} else {
			$return = (bool) get_option( 'wp_nr_capture_urls', false );
		}
		
		return $return;
	}
	
}