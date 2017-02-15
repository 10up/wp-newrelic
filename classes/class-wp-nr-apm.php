<?php

/**
 * Class WP_NR_APM
 *
 * Handles setting all the relevant info for current transaction
 */
class WP_NR_APM {

	public $config = false;

	protected static $error_log_count = 0;

	protected $async_tasks = false;

	public function __construct() {

		add_action( 'plugins_loaded', array( $this, 'setup_config' ), 9999 );
		add_action( 'init', array( $this, 'set_custom_variables' ) );
		add_filter( 'template_include', array( $this, 'set_template' ), 9999 );
		add_action( 'parse_query', array( $this, 'set_transaction' ), 10 );
		add_action( 'wp', array( $this, 'set_post_id' ), 10 );

		add_action( 'wp_async_task_before_job', array( $this, 'async_before_job_track_time' ), 9999, 1 );
		add_action( 'wp_async_task_after_job', array( $this, 'async_after_job_set_attribute' ), 9999, 1 );

		if ( WP_NR_Helper::is_disable_amp() ) {
			add_action( 'pre_amp_render_post', array( $this, 'disable_nr_autorum' ), 9999, 1 );
		}
	}

	/**
	 * Setup New Relic config
	 */
	public function setup_config() {
		$this->config = apply_filters( 'wp_nr_config', array(
			'newrelic.appname' => $this->get_appname(),
			'newrelic.capture_params' => WP_NR_Helper::is_capture_url(),
		) );

		if ( is_array( $this->config ) ) {
			ini_set( 'newrelic.framework', 'wordpress' );
			if ( isset( $this->config['newrelic.appname'] ) && function_exists( 'newrelic_set_appname' ) ) {
				newrelic_set_appname( $this->config['newrelic.appname'] );
			}
			if ( isset( $this->config['newrelic.capture_params'] ) && function_exists( 'newrelic_capture_params' ) ) {
				newrelic_capture_params( $this->config['newrelic.capture_params'] );
			}
		}

		do_action( 'wp_nr_setup_config', $this->config );
	}

	/**
	 * Set template custom parameter in current transaction
	 *
	 * @param $template
	 *
	 * @return mixed
	 */
	public function set_template( $template ) {
		if ( function_exists( 'newrelic_add_custom_parameter' ) ) {
			newrelic_add_custom_parameter( 'template', $template );
		}

		return $template;
	}

	/**
	 * Set all the custom variables
	 *
	 * - Current User
	 * - Request type (ajax/cli/web)
	 */
	public function set_custom_variables() {

		// Set User
		if ( function_exists( 'newrelic_set_user_attributes' ) ) {
			if ( is_user_logged_in() ) {
				$user = wp_get_current_user();
				newrelic_set_user_attributes( $user->ID, '', array_shift( $user->roles ) );
			} else {
				newrelic_set_user_attributes( 'not-logged-in', '', 'no-role' );
			}
		}

		if ( function_exists( 'newrelic_add_custom_parameter' ) ) {

			// Set theme
			$theme = wp_get_theme();
			newrelic_add_custom_parameter( 'theme', $theme->get( 'Name' ) );

			// Set Ajax/CLI/CRON/Gearman/Web
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				$req_type = 'ajax';
			} elseif ( defined( 'DOING_CRON' ) && DOING_CRON ) {
				$req_type = 'cron';
			} elseif ( defined( 'WP_CLI' ) && WP_CLI ) {
				$req_type = 'cli';
			} elseif ( defined( 'WP_GEARS' ) && WP_GEARS ) {
				$req_type = 'gearman';
			} else {
				$req_type = 'web';
			}
			newrelic_add_custom_parameter( 'request_type', apply_filters( 'wp_nr_request_type', $req_type ) );
		}

	}

	/**
	 * Set current transaction name as per the main WP_Query
	 *
	 * @param $query
	 */
	public function set_transaction( $query ) {

		if ( ! function_exists( 'newrelic_name_transaction' ) ) {
			return;
		}
		// set transaction
		$transaction = false;
		if ( $query->is_main_query() ) {
			if ( is_front_page() && is_home() ) {
				$transaction = 'Default Home Page';
			} elseif ( is_front_page() ) {
				$transaction = 'Front Page';
			} elseif ( is_home() ) {
				$transaction = 'Blog Page';
			} elseif ( is_network_admin() ) {
				$transaction = 'Network Dashboard';
			} elseif ( is_admin() ) {
				$transaction = 'Dashboard';
			} elseif ( is_single() ) {
				$post_type = ( ! empty( $query->query['post_type'] ) ) ? $query->query['post_type'] : 'Post';
				$transaction = "Single - {$post_type}";
			} elseif ( is_page() ) {
                if ( isset( $query->query['pagename'] ) ) {
                    $this->add_custom_parameter( $query->query['pagename'] );
                }
                $transaction = "Page";
			} elseif ( is_date() ) {
				$transaction = 'Date Archive';
			} elseif ( is_search() ) {
				if ( isset( $query->query['s'] ) ) {
					$this->add_custom_parameter( 'search', $query->query['s'] );
				}
				$transaction = 'Search Page';
			} elseif ( is_feed() ) {
				$transaction = 'Feed';
			} elseif ( is_post_type_archive() ) {
				$post_type = post_type_archive_title( '', false );
				$transaction = "Archive - {$post_type}";
			} elseif ( is_category() ) {
                if ( isset( $query->query['category_name'] ) ) {
                    $this->add_custom_parameter( 'cat_slug', $query->query['category_name'] );
                }
				$transaction = "Category";
			} elseif ( is_tag() ) {
				if ( isset( $query->query['tag'] ) ) {
					$this->add_custom_parameter( 'tag_slug', $query->query['tag'] );
				}
				$transaction = "Tag";
			} elseif ( is_tax() ) {
				$tax    = key( $query->tax_query->queried_terms );
				$term   = implode( ' | ', $query->tax_query->queried_terms[ $tax ]['terms'] );
                $this->add_custom_parameter( 'term_slug', $term );
				$transaction = "Tax - {$tax}";
			}

			if ( ! empty( $transaction ) ) {
				newrelic_name_transaction( apply_filters( 'wp_nr_transaction_name', $transaction ) );
			}
		}
	}

	/**
	 * Set post_id custom parameter if it's single post
	 *
	 * @param $wp
	 */
	public function set_post_id( $wp ) {
		if ( is_single() && function_exists( 'newrelic_add_custom_parameter' ) ) {
			newrelic_add_custom_parameter( 'post_id', apply_filters( 'wp_nr_post_id', get_the_ID() ) );
		}
	}

    /**
     * Adds a custom parameter through `newrelic_add_custom_parameter`
     * Prefixes the $key with 'wpnr_' to avoid collisions with NRQL reserved words
     *
     * @see https://docs.newrelic.com/docs/agents/php-agent/configuration/php-agent-api#api-custom-param
     *
     * @param $key      string  Custom parameter key
     * @param $value    string  Custom parameter value
     * @return bool
     */
	public function add_custom_parameter( $key, $value ) {
        if ( function_exists( 'newrelic_add_custom_parameter' ) ) {
            //prefixing with wpnr_ to avoid collisions with reserved works in NRQL
            $key = 'wpnr_' . $key;
            return newrelic_add_custom_parameter( $key, apply_filters( 'wp_nr_add_custom_parameter', $value, $key ) );
        }

        return false;
    }

	/**
	 * Custom error logging
	 *
	 * Note: As described here: https://docs.newrelic.com/docs/agents/php-agent/configuration/php-agent-api#api-notice-error
	 * Only a single error per transaction is captured by newrelic_notice_error and reported to New Relic.
	 * If you are using multiple newrelic_notice_error calls in a single transaction,
	 * only the last error captured by the call will be reported.
	 *
	 * @param $message
	 * @param Exception|null $exception
	 *
	 * @return bool
	 */
	public static function log_errors( $message, Exception $exception = null ) {
		if ( self::$error_log_count > 0 ) {
			trigger_error( esc_html__( 'New Relic error logging can only be used once per transaction.', 'wp-newrelic' ), E_USER_WARNING );
			return false;
		}
		if ( function_exists( 'newrelic_notice_error' ) ) {
			newrelic_notice_error( $message, $exception );
			self::$error_log_count++;

			return true;
		} else {
			return false;
		}
	}

	/**
	 * Track time before starting async job
	 *
	 * @param $hook
	 */
	public function async_before_job_track_time( $hook ) {
		if ( false === $this->async_tasks ) {
			$this->async_tasks = array();
		}

		$this->async_tasks[ $hook ] = array(
			'start_time' => time(),
		);
	}

	/**
	 * Set time taken for async task into custom parameter
	 *
	 * @param $hook
	 */
	public function async_after_job_set_attribute( $hook ) {
		if ( is_array( $this->async_tasks ) && ! empty( $this->async_tasks[ $hook ] ) ) {
			$this->async_tasks[ $hook ]['end_time'] = time();

			$time_diff = $this->async_tasks[ $hook ]['start_time'] - $this->async_tasks[ $hook ]['end_time'];

			if ( function_exists( 'newrelic_add_custom_parameter' ) ) {
				newrelic_add_custom_parameter( 'wp_async_task-' . $hook, $time_diff );
			}
		}
	}

	/**
	 * Get New Relic app name as per the home url
	 *
	 * @return string
	 */
	public function get_appname() {
		$home_url = parse_url( home_url() );
		$app_name = $home_url['host'] . ( isset( $home_url['path'] ) ? $home_url['path'] : '' );

		return apply_filters( 'wp_nr_app_name', $app_name );
	}

	/**
	 * Disable New Relic autorum
	 *
	 * @param $post_id
	 */
	public function disable_nr_autorum( $post_id ) {
		if ( ! function_exists( 'newrelic_disable_autorum' ) ) {
			return;
		}
		if ( apply_filters( 'disable_post_autorum', true, $post_id ) ) {
			newrelic_disable_autorum();
		}
	}
}

/**
 * Function for custom error logging.
 *
 * @param $message
 * @param $exception
 *
 * @return bool
 */
function wp_nr_log_errors( $message, $exception ) {
	return WP_NR_APM::log_errors( $message, $exception );
}
