<?php
/**
 * Email template service placeholder.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders branded account emails.
 */
class ALYNT_AG_Email_Template_Service {

	/**
	 * Return supported template keys.
	 *
	 * @return array<string,string>
	 */
	public function templates() {
		return array(
			'password_reset'            => __( 'Password Reset', 'alynt-account-gateway' ),
			'password_changed'          => __( 'Password Changed', 'alynt-account-gateway' ),
			'registration_confirmation' => __( 'Registration Confirmation', 'alynt-account-gateway' ),
			'email_change_confirmation' => __( 'Email Change Confirmation', 'alynt-account-gateway' ),
		);
	}
}
