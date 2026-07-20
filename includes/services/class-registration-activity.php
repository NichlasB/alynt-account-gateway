<?php
/**
 * Records registration verification and flow activity.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Records registration verification and flow activity.
 */
class ALYNT_AG_Registration_Activity extends ALYNT_AG_Service_Collaborator {

	/**
	 * Log a provider verification result.
	 *
	 * @param string                            $email    Submitted email.
	 * @param string                            $provider Provider key.
	 * @param true|array<string,mixed>|WP_Error $result   Verification result.
	 * @return bool
	 */
	public function run_log_verification_result( $email, $provider, $result ) {
		global $wpdb;

		$email    = sanitize_email( $email );
		$provider = sanitize_key( $provider );

		if ( ! $email || ! $provider ) {
			return false;
		}

		$status  = $this->verification_result_status( $result );
		$blocked = is_wp_error( $result );
		$tables  = ALYNT_AG_Database::tables();

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Plugin-owned verification log table.
		return (bool) $wpdb->insert(
			$tables['verification_logs'],
			array(
				'email'      => $email,
				'provider'   => $provider,
				'status'     => $status,
				'blocked'    => $blocked ? 1 : 0,
				'created_at' => current_time( 'mysql', true ),
			),
			array( '%s', '%s', '%s', '%d', '%s' )
		);
	}

	/**
	 * Log a registration-flow outcome to the security activity stream.
	 *
	 * @param string $email   Submitted email.
	 * @param string $status  Compact status code.
	 * @param bool   $blocked Whether the flow was blocked.
	 * @return bool
	 */
	public function run_log_registration_flow_result( $email, $status, $blocked = true ) {
		global $wpdb;

		$email  = sanitize_email( $email );
		$status = sanitize_key( $status );

		if ( ! is_email( $email ) || ! $status ) {
			return false;
		}

		$tables = ALYNT_AG_Database::tables();

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Plugin-owned verification log table.
		return (bool) $wpdb->insert(
			$tables['verification_logs'],
			array(
				'email'      => $email,
				'provider'   => 'registration_flow',
				'status'     => $status,
				'blocked'    => $blocked ? 1 : 0,
				'created_at' => current_time( 'mysql', true ),
			),
			array( '%s', '%s', '%s', '%d', '%s' )
		);
	}

	/**
	 * Return a compact verification result status.
	 *
	 * @param true|array<string,mixed>|WP_Error $result Verification result.
	 * @return string
	 */
	private function verification_result_status( $result ) {
		if ( is_wp_error( $result ) ) {
			if ( 'alynt_ag_reoon_flagged_blocked' === $result->get_error_code() ) {
				$data = $result->get_error_data();
				if ( is_array( $data ) && ! empty( $data['status'] ) ) {
					return sanitize_key( $data['status'] . '_flagged_blocked' );
				}
			}

			return sanitize_key( $result->get_error_code() );
		}

		if ( is_array( $result ) && ! empty( $result['status'] ) ) {
			if ( ! empty( $result['flagged'] ) ) {
				return sanitize_key( $result['status'] . '_flagged' );
			}

			return sanitize_key( $result['status'] );
		}

		return 'passed';
	}
}
