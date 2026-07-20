<?php
/**
 * Handles authentication rate limits and diagnostics.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles authentication rate limits and diagnostics.
 */
class ALYNT_AG_Auth_Activity extends ALYNT_AG_Service_Collaborator {

	/**
	 * Validate a login or lost-password rate limit.
	 *
	 * @param string              $bucket     Bucket name.
	 * @param string              $identifier Submitted identifier.
	 * @param array<string,mixed> $settings   Settings.
	 * @return true|WP_Error
	 */
	public function run_validate_rate_limit( $bucket, $identifier, $settings ) {
		$limiter = new ALYNT_AG_Rate_Limiter();

		if ( 'lostpassword' === $bucket ) {
			$result = $limiter->check_and_increment(
				'lostpassword',
				$identifier,
				$settings['lostpassword_rate_limit_count'],
				$settings['lostpassword_rate_limit_window']
			);

			if ( is_wp_error( $result ) ) {
				$this->log_rate_limit_result( $identifier, 'lostpassword_rate_limited' );
			}

			return $result;
		}

		$result = $limiter->check_and_increment(
			'login',
			$identifier,
			$settings['login_rate_limit_count'],
			$settings['login_rate_limit_window']
		);

		if ( is_wp_error( $result ) ) {
			$this->log_rate_limit_result( $identifier, 'login_rate_limited' );
		}

		return $result;
	}

	/**
	 * Log an auth-side rate-limit block to the shared verification activity table.
	 *
	 * @param string $identifier Submitted email identifier.
	 * @param string $status     Compact status key.
	 * @return bool
	 */
	public function run_log_rate_limit_result( $identifier, $status ) {
		global $wpdb;

		$email  = sanitize_email( $identifier );
		$status = sanitize_key( $status );

		if ( ! $email || ! $status ) {
			return false;
		}

		$tables = ALYNT_AG_Database::tables();

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Plugin-owned verification log table.
		return (bool) $wpdb->insert(
			$tables['verification_logs'],
			array(
				'email'      => $email,
				'provider'   => 'rate_limit',
				'status'     => $status,
				'blocked'    => 1,
				'created_at' => current_time( 'mysql', true ),
			),
			array( '%s', '%s', '%s', '%d', '%s' )
		);
	}

	/**
	 * Log a privacy-conscious branded authentication diagnostics event.
	 *
	 * @param string              $level      Severity level.
	 * @param string              $event_code Event code.
	 * @param string              $message    Event message.
	 * @param array<string,mixed> $context    Event context.
	 * @return bool
	 */
	public function run_log_auth_event( $level, $event_code, $message, $context = array() ) {
		return ALYNT_AG_Diagnostics_Logger::log_event(
			$level,
			'security',
			$event_code,
			$message,
			$context
		);
	}
}
