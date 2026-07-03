<?php
/**
 * Registration service placeholder.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles pending registration flow.
 */
class ALYNT_AG_Registration_Service {

	/**
	 * Minimum password length.
	 */
	const MIN_PASSWORD_LENGTH = 12;

	/**
	 * Validate password policy.
	 *
	 * @param string $password Password.
	 * @return true|WP_Error
	 */
	public function validate_password( $password ) {
		if ( strlen( $password ) < self::MIN_PASSWORD_LENGTH ) {
			return new WP_Error( 'alynt_ag_password_length', __( 'Password must be at least 12 characters.', 'alynt-account-gateway' ) );
		}

		if ( ! preg_match( '/[A-Z]/', $password ) || ! preg_match( '/[a-z]/', $password ) || ! preg_match( '/[0-9]/', $password ) || ! preg_match( '/[^A-Za-z0-9]/', $password ) ) {
			return new WP_Error( 'alynt_ag_password_complexity', __( 'Password must include uppercase, lowercase, number, and symbol characters.', 'alynt-account-gateway' ) );
		}

		return true;
	}
}
