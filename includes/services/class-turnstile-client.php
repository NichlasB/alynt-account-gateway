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
						'remoteip' => $this->get_remote_ip(),
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

	/**
	 * Return best-effort visitor IP for provider verification.
	 *
	 * @return string
	 */
	private function get_remote_ip() {
		foreach ( array( 'HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' ) as $key ) {
			if ( ! empty( $_SERVER[ $key ] ) ) {
				$value = sanitize_text_field( wp_unslash( $_SERVER[ $key ] ) );
				$parts = explode( ',', $value );
				return trim( $parts[0] );
			}
		}

		return '';
	}
}
