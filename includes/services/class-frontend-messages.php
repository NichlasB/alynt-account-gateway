<?php
/**
 * Frontend gateway message catalog.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Provides user-facing frontend gateway titles and error messages.
 */
class ALYNT_AG_Frontend_Messages {

	/**
	 * Get title for the document title tag.
	 *
	 * @param string $screen Screen key.
	 * @return string
	 */
	public function screen_title( $screen ) {
		$titles = array(
			'dashboard'             => __( 'Account Dashboard', 'alynt-account-gateway' ),
			'login'                 => __( 'Log In', 'alynt-account-gateway' ),
			'register'              => __( 'Create Account', 'alynt-account-gateway' ),
			'lostpassword'          => __( 'Reset Password', 'alynt-account-gateway' ),
			'setpassword'           => __( 'Set New Password', 'alynt-account-gateway' ),
			'logout'                => __( 'Log Out', 'alynt-account-gateway' ),
			'registration_disabled' => __( 'Registration Unavailable', 'alynt-account-gateway' ),
			'invalidlink'           => __( 'Link Expired', 'alynt-account-gateway' ),
		);

		return isset( $titles[ $screen ] ) ? $titles[ $screen ] : $titles['login'];
	}

	/**
	 * Get public registration error message.
	 *
	 * @param string $error_code Error code.
	 * @return string
	 */
	public function registration_error( $error_code ) {
		$messages = array(
			'disabled'                    => __( 'Registration is currently unavailable.', 'alynt-account-gateway' ),
			'missing_required_fields'     => __( 'Please complete all required fields.', 'alynt-account-gateway' ),
			'invalid_email'               => __( 'Please enter a valid email address.', 'alynt-account-gateway' ),
			'terms_required'              => __( 'Please accept the terms and privacy policy to continue.', 'alynt-account-gateway' ),
			'email_unavailable'           => __( 'If this email address can be used, a confirmation email will be sent.', 'alynt-account-gateway' ),
			'pending_registration_failed' => __( 'The registration could not be started. Please try again.', 'alynt-account-gateway' ),
			'consent_record_failed'       => __( 'Your consent record could not be saved. Please try again.', 'alynt-account-gateway' ),
			'confirmation_email_failed'   => __( 'The confirmation email could not be sent. Please try again.', 'alynt-account-gateway' ),
		);

		return isset( $messages[ $error_code ] ) ? $messages[ $error_code ] : __( 'The registration could not be started. Please try again.', 'alynt-account-gateway' );
	}

	/**
	 * Get public resend-confirmation error message.
	 *
	 * @param string $error_code Error code.
	 * @return string
	 */
	public function resend_error( $error_code ) {
		$messages = array(
			'invalid_email'               => __( 'Please enter a valid email address.', 'alynt-account-gateway' ),
			'alynt_ag_rate_limited'       => __( 'Too many attempts. Please wait a moment and try again.', 'alynt-account-gateway' ),
			'pending_registration_failed' => __( 'The confirmation link could not be renewed. Please try again.', 'alynt-account-gateway' ),
			'confirmation_email_failed'   => __( 'The confirmation email could not be sent. Please try again.', 'alynt-account-gateway' ),
		);

		return isset( $messages[ $error_code ] ) ? $messages[ $error_code ] : __( 'The confirmation email could not be sent. Please try again.', 'alynt-account-gateway' );
	}

	/**
	 * Get public set-password error message.
	 *
	 * @param string $error_code Error code.
	 * @return string
	 */
	public function password_error( $error_code ) {
		$messages = array(
			'invalid_or_expired_token'     => __( 'This link is invalid or has expired.', 'alynt-account-gateway' ),
			'password_mismatch'            => __( 'The passwords do not match.', 'alynt-account-gateway' ),
			'alynt_ag_password_length'     => __( 'Password must be at least 12 characters.', 'alynt-account-gateway' ),
			'alynt_ag_password_complexity' => __( 'Password must include uppercase, lowercase, number, and symbol characters.', 'alynt-account-gateway' ),
			'email_unavailable'            => __( 'This email address can no longer be used.', 'alynt-account-gateway' ),
		);

		return isset( $messages[ $error_code ] ) ? $messages[ $error_code ] : __( 'Your account could not be created. Please try again.', 'alynt-account-gateway' );
	}
}
