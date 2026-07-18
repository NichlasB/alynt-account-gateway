<?php
/**
 * Cloudflare Turnstile client.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Verifies Turnstile tokens.
 */
class ALYNT_AG_Turnstile_Client {

	const VERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

	/**
	 * Check Siteverify connectivity and secret acceptance without a customer token.
	 *
	 * A fixed invalid response is expected to be rejected. Receiving an
	 * invalid-response error proves the endpoint was reached and the secret was
	 * accepted far enough to evaluate the response, but does not validate the
	 * public site key, hostname, widget, or a real challenge.
	 *
	 * @param string $secret_key Secret key.
	 * @return true|WP_Error
	 */
	public function check_configuration( $secret_key ) {
		if ( empty( $secret_key ) ) {
			return new WP_Error( 'alynt_ag_turnstile_missing', __( 'Turnstile verification is not configured.', 'alynt-account-gateway' ) );
		}

		$response = wp_remote_post(
			self::VERIFY_URL,
			array(
				'timeout' => 10,
				'body'    => array(
					'secret'   => $secret_key,
					'response' => 'alynt-ag-configuration-check',
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return new WP_Error( 'alynt_ag_turnstile_request_failed', __( 'Turnstile verification failed. Please try again.', 'alynt-account-gateway' ) );
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( ! is_array( $body ) || empty( $body['error-codes'] ) || ! is_array( $body['error-codes'] ) ) {
			return new WP_Error( 'alynt_ag_turnstile_invalid_response', __( 'Turnstile returned an unexpected response.', 'alynt-account-gateway' ) );
		}

		$error_codes = array_map( 'sanitize_key', $body['error-codes'] );
		if ( in_array( 'invalid-input-secret', $error_codes, true ) || in_array( 'missing-input-secret', $error_codes, true ) ) {
			return new WP_Error( 'alynt_ag_turnstile_invalid_secret', __( 'Turnstile rejected the saved secret key.', 'alynt-account-gateway' ) );
		}

		if ( in_array( 'invalid-input-response', $error_codes, true ) || in_array( 'missing-input-response', $error_codes, true ) ) {
			return true;
		}

		return new WP_Error( 'alynt_ag_turnstile_invalid_response', __( 'Turnstile returned an unexpected response.', 'alynt-account-gateway' ) );
	}

	/**
	 * Verify a token server-side.
	 *
	 * @param string $token      Turnstile response token.
	 * @param string $secret_key Secret key.
	 * @return true|WP_Error
	 */
	public function verify( $token, $secret_key ) {
		if ( empty( $token ) || empty( $secret_key ) ) {
			return new WP_Error( 'alynt_ag_turnstile_missing', __( 'Turnstile verification is not configured.', 'alynt-account-gateway' ) );
		}

		$response = wp_remote_post(
			self::VERIFY_URL,
			array(
				'timeout' => 10,
				'body'    => array_filter(
					array(
						'secret'   => $secret_key,
						'response' => $token,
						'remoteip' => ALYNT_AG_Client_IP::resolve(),
					)
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return new WP_Error( 'alynt_ag_turnstile_request_failed', __( 'Turnstile verification failed. Please try again.', 'alynt-account-gateway' ) );
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		return $this->interpret_response( is_array( $body ) ? $body : array() );
	}

	/**
	 * Interpret Siteverify response body.
	 *
	 * @param array<string,mixed> $body Response body.
	 * @return true|WP_Error
	 */
	public function interpret_response( $body ) {
		if ( ! empty( $body['success'] ) ) {
			return true;
		}

		return new WP_Error(
			'alynt_ag_turnstile_failed',
			__( 'Turnstile verification failed. Please try again.', 'alynt-account-gateway' ),
			array(
				'error_codes' => isset( $body['error-codes'] ) && is_array( $body['error-codes'] ) ? $body['error-codes'] : array(),
			)
		);
	}
}
