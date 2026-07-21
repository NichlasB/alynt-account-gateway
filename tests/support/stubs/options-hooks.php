<?php
/**
 * Options, hooks, scheduling, and error test stubs.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! function_exists( 'get_option' ) ) {
	function get_option( $name, $default = false ) {
		if ( isset( $GLOBALS['alynt_ag_test_options'][ $name ] ) ) {
			return $GLOBALS['alynt_ag_test_options'][ $name ];
		}

		return $default;
	}
}

if ( ! function_exists( 'update_option' ) ) {
	function update_option( $name, $value, $autoload = null ) {
		if ( isset( $GLOBALS['alynt_ag_test_update_option_result'] ) && false === $GLOBALS['alynt_ag_test_update_option_result'] ) {
			return false;
		}

		$GLOBALS['alynt_ag_test_options'][ $name ] = $value;

		return true;
	}
}

if ( ! function_exists( 'add_option' ) ) {
	function add_option( $name, $value, $deprecated = '', $autoload = 'yes' ) {
		unset( $deprecated, $autoload );
		if ( array_key_exists( $name, $GLOBALS['alynt_ag_test_options'] ) ) {
			return false;
		}

		$GLOBALS['alynt_ag_test_options'][ $name ] = $value;
		return true;
	}
}

if ( ! function_exists( 'delete_option' ) ) {
	function delete_option( $name ) {
		$GLOBALS['alynt_ag_test_deleted_options'][] = $name;
		unset( $GLOBALS['alynt_ag_test_options'][ $name ] );

		return true;
	}
}

if ( ! function_exists( 'wp_next_scheduled' ) ) {
	function wp_next_scheduled( $hook, $args = array() ) {
		if ( isset( $GLOBALS['alynt_ag_test_scheduled_hooks'][ $hook ] ) ) {
			return $GLOBALS['alynt_ag_test_scheduled_hooks'][ $hook ];
		}

		foreach ( $GLOBALS['alynt_ag_test_single_events'] ?? array() as $event ) {
			if ( $hook === $event['hook'] && $args === $event['args'] ) {
				return $event['timestamp'];
			}
		}

		return false;
	}
}

if ( ! function_exists( 'wp_schedule_single_event' ) ) {
	function wp_schedule_single_event( $timestamp, $hook, $args = array(), $wp_error = false ) {
		unset( $wp_error );
		if ( array_key_exists( 'alynt_ag_test_schedule_single_event_result', $GLOBALS ) ) {
			$result = $GLOBALS['alynt_ag_test_schedule_single_event_result'];
			if ( is_wp_error( $result ) || ! $result ) {
				return $result;
			}
		}

		$GLOBALS['alynt_ag_test_single_events'][] = array(
			'timestamp' => $timestamp,
			'hook'      => $hook,
			'args'      => $args,
		);

		return true;
	}
}

if ( ! function_exists( 'wp_schedule_event' ) ) {
	function wp_schedule_event( $timestamp, $recurrence, $hook, $args = array(), $wp_error = false ) {
		unset( $wp_error );
		$GLOBALS['alynt_ag_test_scheduled_hooks'][ $hook ] = $timestamp;
		$GLOBALS['alynt_ag_test_recurring_events'][] = array(
			'timestamp'  => $timestamp,
			'recurrence' => $recurrence,
			'hook'       => $hook,
			'args'       => $args,
		);

		return true;
	}
}

if ( ! function_exists( 'wp_unschedule_event' ) ) {
	function wp_unschedule_event( $timestamp, $hook ) {
		$GLOBALS['alynt_ag_test_unscheduled_events'][] = array(
			'timestamp' => $timestamp,
			'hook'      => $hook,
		);

		return true;
	}
}

if ( ! function_exists( 'wp_clear_scheduled_hook' ) ) {
	function wp_clear_scheduled_hook( $hook ) {
		$GLOBALS['alynt_ag_test_cleared_hooks'][] = $hook;

		return 1;
	}
}

if ( ! function_exists( 'flush_rewrite_rules' ) ) {
	function flush_rewrite_rules() {
		$GLOBALS['alynt_ag_test_rewrite_rules_flushed'] = true;
	}
}

if ( ! function_exists( 'get_site_option' ) ) {
	function get_site_option( $name, $default = false ) {
		if ( isset( $GLOBALS['alynt_ag_test_site_options'][ $name ] ) ) {
			return $GLOBALS['alynt_ag_test_site_options'][ $name ];
		}

		return $default;
	}
}

if ( ! function_exists( 'is_multisite' ) ) {
	function is_multisite() {
		return ! empty( $GLOBALS['alynt_ag_test_is_multisite'] );
	}
}

if ( ! function_exists( 'get_sites' ) ) {
	function get_sites( $args = array() ) {
		$site_ids = $GLOBALS['alynt_ag_test_site_ids'] ?? array();
		$offset   = isset( $args['offset'] ) ? absint( $args['offset'] ) : 0;
		$number   = isset( $args['number'] ) ? absint( $args['number'] ) : count( $site_ids );

		return array_slice( $site_ids, $offset, $number );
	}
}

if ( ! function_exists( 'switch_to_blog' ) ) {
	function switch_to_blog( $blog_id ) {
		global $wpdb;

		$GLOBALS['alynt_ag_test_blog_stack'][] = array(
			'prefix'  => $wpdb->prefix,
			'options' => $wpdb->options,
		);
		$GLOBALS['alynt_ag_test_switched_blogs'][] = absint( $blog_id );
		$wpdb->prefix  = 'wp_' . absint( $blog_id ) . '_';
		$wpdb->options = $wpdb->prefix . 'options';

		return true;
	}
}

if ( ! function_exists( 'restore_current_blog' ) ) {
	function restore_current_blog() {
		global $wpdb;

		$previous = array_pop( $GLOBALS['alynt_ag_test_blog_stack'] );
		if ( ! $previous ) {
			return false;
		}

		$wpdb->prefix  = $previous['prefix'];
		$wpdb->options = $previous['options'];
		$GLOBALS['alynt_ag_test_restored_blogs']++;

		return true;
	}
}

if ( ! function_exists( 'is_admin' ) ) {
	function is_admin() {
		return ! empty( $GLOBALS['alynt_ag_test_is_admin'] );
	}
}

if ( ! function_exists( 'do_action' ) ) {
	function do_action( $hook_name, ...$args ) {
		$GLOBALS['alynt_ag_test_actions'][] = array(
			'hook' => $hook_name,
			'args' => $args,
		);

		if ( isset( $GLOBALS['alynt_ag_test_action_output'][ $hook_name ] ) ) {
			echo $GLOBALS['alynt_ag_test_action_output'][ $hook_name ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Test fixture output.
		}
	}
}

if ( ! function_exists( 'add_action' ) ) {
	function add_action( $hook_name, $callback, $priority = 10, $accepted_args = 1 ) {
		$GLOBALS['alynt_ag_test_actions'][] = array(
			'hook'          => $hook_name,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);

		return true;
	}
}

if ( ! function_exists( 'add_filter' ) ) {
	function add_filter( $hook_name, $callback, $priority = 10, $accepted_args = 1 ) {
		$GLOBALS['alynt_ag_test_filters'][] = array(
			'hook'          => $hook_name,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);

		return true;
	}
}

if ( ! function_exists( 'remove_filter' ) ) {
	function remove_filter( $hook_name, $callback, $priority = 10 ) {
		$GLOBALS['alynt_ag_test_filters'] = array_values(
			array_filter(
				$GLOBALS['alynt_ag_test_filters'],
				static function ( $filter ) use ( $hook_name, $callback, $priority ) {
					return ! (
						$hook_name === $filter['hook']
						&& $callback === $filter['callback']
						&& $priority === $filter['priority']
					);
				}
			)
		);

		return true;
	}
}

if ( ! function_exists( 'apply_filters' ) ) {
	function apply_filters( $hook_name, $value, ...$args ) {
		$filters = array_filter(
			$GLOBALS['alynt_ag_test_filters'],
			static function ( $filter ) use ( $hook_name ) {
				return $hook_name === $filter['hook'];
			}
		);

		usort(
			$filters,
			static function ( $left, $right ) {
				return $left['priority'] <=> $right['priority'];
			}
		);

		foreach ( $filters as $filter ) {
			$accepted_args = max( 1, (int) $filter['accepted_args'] );
			$filter_args   = array_slice( array_merge( array( $value ), $args ), 0, $accepted_args );
			$value         = call_user_func_array( $filter['callback'], $filter_args );
		}

		return $value;
	}
}

if ( ! function_exists( 'get_user_by' ) ) {
	function get_user_by( $field, $value ) {
		if ( 'email' !== $field || ! is_email( $value ) ) {
			return false;
		}

		if ( ! empty( $GLOBALS['alynt_ag_test_missing_user_emails'] ) && in_array( $value, $GLOBALS['alynt_ag_test_missing_user_emails'], true ) ) {
			return false;
		}

		$user = new WP_User( $value );
		$user->ID = 123;

		return $user;
	}
}

if ( ! function_exists( 'get_transient' ) ) {
	function get_transient( $name ) {
		return isset( $GLOBALS['alynt_ag_test_transients'][ $name ] ) ? $GLOBALS['alynt_ag_test_transients'][ $name ]['value'] : false;
	}
}

if ( ! function_exists( 'set_transient' ) ) {
	function set_transient( $name, $value, $expiration = 0 ) {
		if ( isset( $GLOBALS['alynt_ag_test_set_transient_result'] ) && false === $GLOBALS['alynt_ag_test_set_transient_result'] ) {
			return false;
		}

		$GLOBALS['alynt_ag_test_transients'][ $name ] = array(
			'value'      => $value,
			'expiration' => $expiration,
		);

		return true;
	}
}

if ( ! class_exists( 'WP_Error' ) ) {
	class WP_Error {
		public $code;
		public $message;
		public $data;

		public function __construct( $code = '', $message = '', $data = '' ) {
			$this->code    = $code;
			$this->message = $message;
			$this->data    = $data;
		}

		public function get_error_code() {
			return $this->code;
		}

		public function get_error_message() {
			return $this->message;
		}

		public function get_error_data() {
			return $this->data;
		}
	}
}
