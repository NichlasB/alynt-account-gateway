<?php
/**
 * Settings page security-log-metrics component.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Owns focused security-log-metrics behavior for the settings page.
 */
class ALYNT_AG_Settings_Page_Security_Log_Metrics extends ALYNT_AG_Settings_Page_Component {

	/**
	 * Count matching security log rows.
	 *
	 * @param array<int,object> $logs            Recent verification logs.
	 * @param string            $provider        Provider key.
	 * @param array<int,string> $statuses        Exact status keys.
	 * @param array<int,string> $status_suffixes Status suffixes.
	 * @return int
	 */
	public function count_security_logs_by_provider_statuses( $logs, $provider, $statuses, $status_suffixes = array() ) {
		$count           = 0;
		$provider        = sanitize_key( $provider );
		$statuses        = array_map( 'sanitize_key', $statuses );
		$status_suffixes = array_map( 'sanitize_key', $status_suffixes );

		foreach ( $logs as $log ) {
			$log_provider = isset( $log->provider ) ? sanitize_key( $log->provider ) : '';
			$status       = isset( $log->status ) ? sanitize_key( $log->status ) : '';

			if ( $provider !== $log_provider || '' === $status ) {
				continue;
			}

			if ( in_array( $status, $statuses, true ) ) {
				++$count;
				continue;
			}

			foreach ( $status_suffixes as $suffix ) {
				if ( $this->status_has_suffix( $status, $suffix ) ) {
					++$count;
					break;
				}
			}
		}

		return $count;
	}

	/**
	 * Count matching diagnostics event rows.
	 *
	 * @param array<int,object> $events     Recent diagnostics events.
	 * @param string            $event_code Event code.
	 * @return int
	 */
	public function count_diagnostics_events_by_code( $events, $event_code ) {
		$count      = 0;
		$event_code = sanitize_key( $event_code );

		foreach ( $events as $event ) {
			$code = isset( $event->event_code ) ? sanitize_key( $event->event_code ) : '';

			if ( $event_code === $code ) {
				++$count;
			}
		}

		return $count;
	}

	/**
	 * Count matching diagnostics event rows across multiple event codes.
	 *
	 * @param array<int,object> $events      Recent diagnostics events.
	 * @param array<int,string> $event_codes Event codes.
	 * @return int
	 */
	public function count_diagnostics_events_by_codes( $events, $event_codes ) {
		$count       = 0;
		$event_codes = array_values( array_filter( array_map( 'sanitize_key', $event_codes ) ) );

		foreach ( $events as $event ) {
			$code = isset( $event->event_code ) ? sanitize_key( $event->event_code ) : '';

			if ( in_array( $code, $event_codes, true ) ) {
				++$count;
			}
		}

		return $count;
	}

	/**
	 * Count failed webhook log rows.
	 *
	 * @param array<int,object> $logs Recent webhook logs.
	 * @return int
	 */
	public function count_failed_webhook_logs( $logs ) {
		$count = 0;

		foreach ( $logs as $log ) {
			if ( empty( $log->success ) ) {
				++$count;
			}
		}

		return $count;
	}

	/**
	 * Count native login redirects by preserved query keys.
	 *
	 * @param array<int,object> $events        Recent diagnostics events.
	 * @param array<int,string> $required_keys Required preserved query keys.
	 * @return int
	 */
	public function count_native_login_redirects_with_preserved_keys( $events, $required_keys = array() ) {
		$count         = 0;
		$required_keys = array_values( array_filter( array_map( 'sanitize_key', $required_keys ) ) );

		foreach ( $events as $event ) {
			$code = isset( $event->event_code ) ? sanitize_key( $event->event_code ) : '';

			if ( 'native_login_redirected' !== $code ) {
				continue;
			}

			if ( empty( $required_keys ) || $this->diagnostics_event_has_preserved_query_keys( $event, $required_keys ) ) {
				++$count;
			}
		}

		return $count;
	}

	/**
	 * Determine whether a diagnostics event preserved all requested query keys.
	 *
	 * @param object            $event         Diagnostics event row.
	 * @param array<int,string> $required_keys Required preserved query keys.
	 * @return bool
	 */
	public function diagnostics_event_has_preserved_query_keys( $event, $required_keys ) {
		$context        = $this->diagnostics_event_context( $event );
		$preserved_keys = array();

		if ( isset( $context['preserved_query_keys'] ) && is_array( $context['preserved_query_keys'] ) ) {
			foreach ( $context['preserved_query_keys'] as $key ) {
				if ( is_scalar( $key ) ) {
					$preserved_keys[] = sanitize_key( (string) $key );
				}
			}
		}

		foreach ( $required_keys as $required_key ) {
			if ( ! in_array( sanitize_key( $required_key ), $preserved_keys, true ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Return a decoded diagnostics event context.
	 *
	 * @param object $event Diagnostics event row.
	 * @return array<string,mixed>
	 */
	public function diagnostics_event_context( $event ) {
		if ( ! isset( $event->context ) ) {
			return array();
		}

		if ( is_array( $event->context ) ) {
			return $event->context;
		}

		if ( ! is_string( $event->context ) || '' === $event->context ) {
			return array();
		}

		$context = json_decode( $event->context, true );

		return is_array( $context ) ? $context : array();
	}

	/**
	 * Return sanitized query keys from diagnostics context.
	 *
	 * @param array<string,mixed> $context Diagnostics context.
	 * @return array<int,string>
	 */
	public function diagnostics_context_query_keys( $context ) {
		$keys = array();

		if ( ! isset( $context['request_query_keys'] ) || ! is_array( $context['request_query_keys'] ) ) {
			return $keys;
		}

		foreach ( $context['request_query_keys'] as $key ) {
			if ( is_scalar( $key ) ) {
				$keys[] = sanitize_key( (string) $key );
			}
		}

		return array_values( array_filter( array_unique( $keys ) ) );
	}
}
