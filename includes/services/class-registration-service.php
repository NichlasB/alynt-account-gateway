<?php
/**
 * Registration service.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Public facade for pending registration flows.
 */
class ALYNT_AG_Registration_Service {

	/**
	 * Minimum password length.
	 */
	const MIN_PASSWORD_LENGTH = 12;

	/**
	 * Return destination helper.
	 *
	 * @var ALYNT_AG_Return_Destination
	 */
	private $destinations;

	/**
	 * Return path associated with the most recently completed registration.
	 *
	 * @var string
	 */
	private $last_completed_return_path = '';

	/**
	 * Focused registration collaborators.
	 *
	 * @var array<string,object>
	 */
	private $collaborators;

	/**
	 * Constructor.
	 *
	 * @param ALYNT_AG_Return_Destination|null $destinations Return destination helper.
	 * @param array<string,object>             $collaborators Optional collaborator overrides.
	 */
	public function __construct( $destinations = null, $collaborators = array() ) {
		$this->destinations  = $destinations ? $destinations : new ALYNT_AG_Return_Destination();
		$defaults            = array(
			'request'      => new ALYNT_AG_Registration_Request_Handler( $this, $this->destinations ),
			'protection'   => new ALYNT_AG_Registration_Protection( $this ),
			'activity'     => new ALYNT_AG_Registration_Activity( $this ),
			'pending'      => new ALYNT_AG_Registration_Pending_Store( $this, $this->destinations ),
			'confirmation' => new ALYNT_AG_Registration_Confirmation( $this ),
			'completion'   => new ALYNT_AG_Registration_Completion( $this, $this->destinations ),
			'delivery'     => new ALYNT_AG_Registration_Delivery( $this ),
			'credentials'  => new ALYNT_AG_Registration_Credentials( $this ),
		);
		$this->collaborators = array_merge( $defaults, is_array( $collaborators ) ? $collaborators : array() );
	}

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'template_redirect', array( $this, 'maybe_handle_registration_request' ), 0 );
	}

	/**
	 * Handle branded registration form submissions.
	 *
	 * @return void
	 */
	public function maybe_handle_registration_request() {
		$this->collaborators['request']->run_maybe_handle_registration_request();
	}

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
	 * Validate configured registration protection providers.
	 *
	 * @param string              $email           Email address.
	 * @param string              $turnstile_token Turnstile response token.
	 * @param array<string,mixed> $settings        Settings.
	 * @return true|WP_Error
	 */
	public function validate_registration_protection( $email, $turnstile_token, $settings ) {
		return $this->collaborators['protection']->run_validate_registration_protection(
			$email,
			$turnstile_token,
			$settings
		);
	}

	/**
	 * Log a provider verification result.
	 *
	 * @param string                            $email    Submitted email.
	 * @param string                            $provider Provider key.
	 * @param true|array<string,mixed>|WP_Error $result   Verification result.
	 * @return bool
	 */
	public function log_verification_result( $email, $provider, $result ) {
		return $this->collaborators['activity']->run_log_verification_result(
			$email,
			$provider,
			$result
		);
	}

	/**
	 * Log a registration-flow outcome to the security activity stream.
	 *
	 * @param string $email   Submitted email.
	 * @param string $status  Compact status code.
	 * @param bool   $blocked Whether the flow was blocked.
	 * @return bool
	 */
	public function log_registration_flow_result( $email, $status, $blocked = true ) {
		return $this->collaborators['activity']->run_log_registration_flow_result(
			$email,
			$status,
			$blocked
		);
	}

	/**
	 * Apply the configured policy for Reoon flagged statuses.
	 *
	 * @param true|array<string,mixed>|WP_Error $result   Reoon verification result.
	 * @param array<string,mixed>               $settings Settings.
	 * @return true|array<string,mixed>|WP_Error
	 */
	public function apply_reoon_flagged_policy( $result, $settings ) {
		return $this->collaborators['protection']->run_apply_reoon_flagged_policy( $result, $settings );
	}

	/**
	 * Validate the public registration terms acceptance checkbox.
	 *
	 * @param mixed $accepted Submitted checkbox value.
	 * @return true|WP_Error
	 */
	public function validate_terms_acceptance( $accepted ) {
		return $this->collaborators['protection']->run_validate_terms_acceptance( $accepted );
	}

	/**
	 * Validate a registration-related rate limit.
	 *
	 * @param string              $bucket     Registration bucket.
	 * @param string              $identifier Identifier such as email.
	 * @param array<string,mixed> $settings   Settings.
	 * @return true|WP_Error
	 */
	public function validate_rate_limit( $bucket, $identifier, $settings ) {
		return $this->collaborators['protection']->run_validate_rate_limit(
			$bucket,
			$identifier,
			$settings
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
	 * Validate password and confirmation together.
	 *
	 * @param string $password         Password.
	 * @param string $password_confirm Password confirmation.
	 * @return true|WP_Error
	 */
	public function validate_password_pair( $password, $password_confirm ) {
		return $this->collaborators['credentials']->run_validate_password_pair( $password, $password_confirm );
	}

	/**
	 * Generate a unique WordPress username from settings.
	 *
	 * @param string              $first_name First name.
	 * @param string              $last_name  Last name.
	 * @param array<string,mixed> $settings   Settings.
	 * @return string
	 */
	public function generate_username( $first_name, $last_name, $settings ) {
		return $this->collaborators['credentials']->run_generate_username(
			$first_name,
			$last_name,
			$settings
		);
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

	/**
	 * Generate a raw confirmation token.
	 *
	 * @return string
	 */
	public function generate_confirmation_token() {
		return $this->collaborators['credentials']->run_generate_confirmation_token();
	}

	/**
	 * Hash a confirmation token for storage.
	 *
	 * @param string $token Raw token.
	 * @return string
	 */
	public function hash_token( $token ) {
		return $this->collaborators['credentials']->run_hash_token( $token );
	}

	/**
	 * Verify a raw token against a stored hash.
	 *
	 * @param string $token Raw token.
	 * @param string $hash  Stored token hash.
	 * @return bool
	 */
	public function token_matches_hash( $token, $hash ) {
		return $this->collaborators['credentials']->run_token_matches_hash( $token, $hash );
	}

	/**
	 * Build the confirmation URL for a raw token.
	 *
	 * @param string              $token    Raw token.
	 * @param array<string,mixed> $settings Settings.
	 * @return string
	 */
	public function build_confirmation_url( $token, $settings ) {
		return $this->collaborators['credentials']->run_build_confirmation_url( $token, $settings );
	}

	/**
	 * Validate password policy.
	 *
	 * @param string $password Password.
	 * @return true|WP_Error
	 */
	public function validate_password( $password ) {
		return $this->collaborators['credentials']->run_validate_password( $password );
	}
}
