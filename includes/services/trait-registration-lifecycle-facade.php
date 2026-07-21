<?php
/**
 * Registration lifecycle facade methods.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Delegates pending-registration lifecycle and delivery operations.
 */
trait ALYNT_AG_Registration_Lifecycle_Facade {

	/**
	 * Create or replace a pending registration.
	 *
	 * @param string              $first_name First name.
	 * @param string              $last_name  Last name.
	 * @param string              $email      Email address.
	 * @param array<string,mixed> $settings   Settings.
	 * @param string              $return_path Validated same-site return path.
	 * @return array<string,mixed>|WP_Error
	 */
	public function create_pending_registration( $first_name, $last_name, $email, $settings, $return_path = '' ) {
		return $this->collaborators['pending']->run_create_pending_registration(
			$first_name,
			$last_name,
			$email,
			$settings,
			$return_path
		);
	}

	/**
	 * Send registration confirmation email.
	 *
	 * @param array<string,mixed> $pending  Pending registration data.
	 * @param array<string,mixed> $settings Settings.
	 * @return true|WP_Error
	 */
	public function send_confirmation_email( $pending, $settings ) {
		return $this->collaborators['confirmation']->run_send_confirmation_email( $pending, $settings );
	}

	/**
	 * Find a pending registration by raw token.
	 *
	 * @param string $token Raw token.
	 * @return object|null
	 */
	public function find_pending_by_token( $token ) {
		return $this->collaborators['pending']->run_find_pending_by_token( $token );
	}

	/**
	 * Find the latest registration that can receive a fresh confirmation token.
	 *
	 * @param string $email Email address.
	 * @return object|null
	 */
	public function find_resendable_pending_by_email( $email ) {
		return $this->collaborators['pending']->run_find_resendable_pending_by_email( $email );
	}

	/**
	 * Renew a pending registration with a fresh confirmation token.
	 *
	 * @param object              $pending  Pending registration row.
	 * @param array<string,mixed> $settings Settings.
	 * @return array<string,mixed>|WP_Error
	 */
	public function renew_pending_confirmation( $pending, $settings ) {
		return $this->collaborators['confirmation']->run_renew_pending_confirmation( $pending, $settings );
	}

	/**
	 * Resend a confirmation link without exposing whether a pending registration exists.
	 *
	 * @param string              $email    Email address.
	 * @param array<string,mixed> $settings Settings.
	 * @return true|WP_Error
	 */
	public function resend_confirmation( $email, $settings ) {
		return $this->collaborators['confirmation']->run_resend_confirmation( $email, $settings );
	}

	/**
	 * Mark a pending registration as email-confirmed without creating a user.
	 *
	 * @param string $token Raw token.
	 * @return object|WP_Error
	 */
	public function confirm_pending_token( $token ) {
		return $this->collaborators['pending']->run_confirm_pending_token( $token );
	}

	/**
	 * Complete registration after email confirmation and password validation.
	 *
	 * @param string              $token            Raw token.
	 * @param string              $password         Password.
	 * @param string              $password_confirm Password confirmation.
	 * @param array<string,mixed> $settings         Settings.
	 * @return int|WP_Error
	 */
	public function complete_pending_registration( $token, $password, $password_confirm, $settings ) {
		return $this->collaborators['completion']->run_complete_pending_registration(
			$token,
			$password,
			$password_confirm,
			$settings,
			$this->last_completed_return_path
		);
	}

	/**
	 * Send the account-created welcome email unless disabled.
	 *
	 * @param object              $pending  Pending registration row.
	 * @param int                 $user_id  Created user ID.
	 * @param array<string,mixed> $settings Settings.
	 * @return true|WP_Error
	 */
	public function send_account_created_welcome_email( $pending, $user_id, $settings ) {
		return $this->collaborators['delivery']->run_send_account_created_welcome_email(
			$pending,
			$user_id,
			$settings
		);
	}

	/**
	 * Dispatch the account-created webhook.
	 *
	 * @param int                 $user_id  Created user ID.
	 * @param array<string,mixed> $settings Settings.
	 * @return true|WP_Error
	 */
	public function dispatch_account_created_webhook( $user_id, $settings ) {
		return $this->collaborators['delivery']->run_dispatch_account_created_webhook( $user_id, $settings );
	}

	/**
	 * Build the login URL used after a registration is completed.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return string
	 */
	public function registration_complete_login_url( $settings ) {
		return $this->collaborators['completion']->run_registration_complete_login_url(
			$settings,
			$this->last_completed_return_path
		);
	}
}
