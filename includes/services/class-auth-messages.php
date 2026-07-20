<?php
/**
 * Provides neutral authentication messages.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Provides neutral authentication messages.
 */
class ALYNT_AG_Auth_Messages extends ALYNT_AG_Service_Collaborator {

	/**
	 * Get a public login error message.
	 *
	 * @param string $error_code Error code.
	 * @return string
	 */
	public function run_get_login_error_message( $error_code ) {
		if ( 'alynt_ag_rate_limited' === $error_code ) {
			return __( 'Too many attempts. Please wait a moment and try again.', 'alynt-account-gateway' );
		}

		return __( 'The email address or password is incorrect.', 'alynt-account-gateway' );
	}

	/**
	 * Get a public lost-password error message.
	 *
	 * @param string $error_code Error code.
	 * @return string
	 */
	public function run_get_lostpassword_error_message( $error_code ) {
		if ( 'alynt_ag_rate_limited' === $error_code ) {
			return __( 'Too many attempts. Please wait a moment and try again.', 'alynt-account-gateway' );
		}

		if ( 'invalid_or_expired_token' === $error_code ) {
			return __( 'This reset link is invalid or has expired. Please request a new link.', 'alynt-account-gateway' );
		}

		return __( 'The reset request could not be processed. Please try again.', 'alynt-account-gateway' );
	}

	/**
	 * Return the neutral reset-request status message.
	 *
	 * @return string
	 */
	public function run_get_lostpassword_sent_message() {
		return __( 'If an account can receive password reset instructions, an email has been sent. Please check your inbox and spam folder.', 'alynt-account-gateway' );
	}
}
