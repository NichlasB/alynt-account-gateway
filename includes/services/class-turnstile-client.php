<?php
/**
 * Cloudflare Turnstile client placeholder.
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

		return true;
	}
}
