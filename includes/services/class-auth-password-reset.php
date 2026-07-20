<?php
/**
 * Validates and completes native password resets.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Validates and completes native password resets.
 */
class ALYNT_AG_Auth_Password_Reset extends ALYNT_AG_Service_Collaborator {

	/**
	 * Validate a native WordPress password reset key.
	 *
	 * @param string $key   Password reset key.
	 * @param string $login User login.
	 * @return WP_User|WP_Error
	 */
	public function run_validate_password_reset_key( $key, $login ) {
		$key   = sanitize_text_field( $key );
		$login = sanitize_user( $login );

		if ( '' === $key || '' === $login ) {
			return new WP_Error( 'invalid_or_expired_token', __( 'This reset link is invalid or has expired.', 'alynt-account-gateway' ) );
		}

		$user = check_password_reset_key( $key, $login );

		if ( is_wp_error( $user ) ) {
			return new WP_Error( 'invalid_or_expired_token', __( 'This reset link is invalid or has expired.', 'alynt-account-gateway' ) );
		}

		return $user;
	}

	/**
	 * Complete a native WordPress password reset.
	 *
	 * @param string $key              Password reset key.
	 * @param string $login            User login.
	 * @param string $password         Password.
	 * @param string $password_confirm Password confirmation.
	 * @return true|WP_Error
	 */
	public function run_complete_password_reset( $key, $login, $password, $password_confirm ) {
		$user = $this->validate_password_reset_key( $key, $login );
		if ( is_wp_error( $user ) ) {
			$this->log_auth_event(
				'warning',
				'branded_password_reset_failed',
				__( 'Rejected a branded password-reset completion attempt.', 'alynt-account-gateway' ),
				array(
					'reason'        => $user->get_error_code(),
					'key_present'   => '' !== (string) $key,
					'login_present' => '' !== (string) $login,
				)
			);
			return $user;
		}

		$registration = new ALYNT_AG_Registration_Service();
		$valid        = $registration->validate_password_pair( $password, $password_confirm );
		if ( is_wp_error( $valid ) ) {
			$this->log_auth_event(
				'warning',
				'branded_password_reset_failed',
				__( 'Rejected a branded password-reset completion attempt.', 'alynt-account-gateway' ),
				array(
					'reason'        => $valid->get_error_code(),
					'key_present'   => '' !== (string) $key,
					'login_present' => '' !== (string) $login,
				)
			);
			return $valid;
		}

		reset_password( $user, $password );

		$this->log_auth_event(
			'info',
			'branded_password_reset_completed',
			__( 'Completed a branded password-reset request.', 'alynt-account-gateway' ),
			array(
				'user_id' => isset( $user->ID ) ? absint( $user->ID ) : 0,
			)
		);

		return true;
	}
}
