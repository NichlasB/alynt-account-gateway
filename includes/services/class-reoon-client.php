<?php
/**
 * Reoon Email Verifier client.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Maps Reoon verification statuses.
 */
class ALYNT_AG_Reoon_Client {

	const VERIFY_URL  = 'https://emailverifier.reoon.com/api/v1/verify';
	const BALANCE_URL = 'https://emailverifier.reoon.com/api/v1/check-account-balance/';

	/**
	 * Check Reoon account connectivity without submitting an email address.
	 *
	 * @param string $api_key API key.
	 * @return array<string,mixed>|WP_Error
	 */
	public function check_account( $api_key ) {
		if ( empty( $api_key ) ) {
			return new WP_Error( 'alynt_ag_reoon_missing', __( 'Reoon verification is not configured.', 'alynt-account-gateway' ) );
		}

		$response = wp_remote_get(
			add_query_arg( 'key', $api_key, self::BALANCE_URL ),
			array( 'timeout' => 10 )
		);

		if ( is_wp_error( $response ) ) {
			return new WP_Error( 'alynt_ag_reoon_request_failed', __( 'Email verification failed. Please try again.', 'alynt-account-gateway' ) );
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( ! is_array( $body ) ) {
			return new WP_Error( 'alynt_ag_reoon_invalid_response', __( 'Email verification failed. Please try again.', 'alynt-account-gateway' ) );
		}

		$status     = isset( $body['status'] ) ? sanitize_key( $body['status'] ) : '';
		$api_status = isset( $body['api_status'] ) ? sanitize_key( $body['api_status'] ) : '';

		if ( 'success' !== $status || 'active' !== $api_status ) {
			return new WP_Error( 'alynt_ag_reoon_account_inactive', __( 'The Reoon API account is not active.', 'alynt-account-gateway' ) );
		}

		return array(
			'status'                    => $status,
			'api_status'                => $api_status,
			'remaining_daily_credits'   => isset( $body['remaining_daily_credits'] ) ? absint( $body['remaining_daily_credits'] ) : 0,
			'remaining_instant_credits' => isset( $body['remaining_instant_credits'] ) ? absint( $body['remaining_instant_credits'] ) : 0,
		);
	}

	/**
	 * Verify an email address with Reoon.
	 *
	 * @param string $email   Email address.
	 * @param string $api_key API key.
	 * @param string $mode    Verification mode.
	 * @return array<string,mixed>|WP_Error
	 */
	public function verify( $email, $api_key, $mode = 'quick' ) {
		if ( empty( $email ) || empty( $api_key ) ) {
			return new WP_Error( 'alynt_ag_reoon_missing', __( 'Reoon verification is not configured.', 'alynt-account-gateway' ) );
		}

		$mode = 'power' === $mode ? 'power' : 'quick';
		$url  = add_query_arg(
			array(
				'email' => rawurlencode( $email ),
				'key'   => rawurlencode( $api_key ),
				'mode'  => $mode,
			),
			self::VERIFY_URL
		);

		$response = wp_remote_get(
			$url,
			array(
				'timeout' => 'power' === $mode ? 30 : 10,
			)
		);

		if ( is_wp_error( $response ) ) {
			return new WP_Error( 'alynt_ag_reoon_request_failed', __( 'Email verification failed. Please try again.', 'alynt-account-gateway' ) );
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! is_array( $body ) ) {
			return new WP_Error( 'alynt_ag_reoon_invalid_response', __( 'Email verification failed. Please try again.', 'alynt-account-gateway' ) );
		}

		return $this->interpret_response( $body );
	}

	/**
	 * Interpret a Reoon response.
	 *
	 * @param array<string,mixed> $body Response body.
	 * @return array<string,mixed>|WP_Error
	 */
	public function interpret_response( $body ) {
		$status = isset( $body['status'] ) ? sanitize_key( $body['status'] ) : 'unknown';

		if ( $this->is_blocked_status( $status ) ) {
			return new WP_Error(
				'alynt_ag_reoon_blocked',
				__( 'This email address cannot be used.', 'alynt-account-gateway' ),
				array( 'status' => $status )
			);
		}

		return array(
			'status'  => $status,
			'blocked' => false,
			'flagged' => $this->is_flagged_status( $status ),
			'body'    => $body,
		);
	}

	/**
	 * Return whether a Reoon status should be blocked by default.
	 *
	 * @param string $status Reoon status.
	 * @return bool
	 */
	public function is_blocked_status( $status ) {
		return in_array( sanitize_key( $status ), array( 'invalid', 'disabled', 'disposable', 'spamtrap' ), true );
	}

	/**
	 * Return whether a Reoon status should be allowed but flagged.
	 *
	 * @param string $status Reoon status.
	 * @return bool
	 */
	public function is_flagged_status( $status ) {
		return in_array( sanitize_key( $status ), array( 'catch_all', 'role_account', 'unknown', 'inbox_full' ), true );
	}
}
