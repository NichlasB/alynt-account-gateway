<?php
/**
 * Branded authentication service.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Public facade for branded authentication flows.
 */
class ALYNT_AG_Auth_Service {

	/**
	 * Return destination helper.
	 *
	 * @var ALYNT_AG_Return_Destination
	 */
	private $destinations;

	/**
	 * Focused authentication collaborators.
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
			'request'        => new ALYNT_AG_Auth_Request_Handler( $this, $this->destinations ),
			'activity'       => new ALYNT_AG_Auth_Activity( $this ),
			'messages'       => new ALYNT_AG_Auth_Messages( $this ),
			'password_reset' => new ALYNT_AG_Auth_Password_Reset( $this ),
			'redirects'      => new ALYNT_AG_Auth_Redirects( $this, $this->destinations ),
		);
		$this->collaborators = array_merge( $defaults, is_array( $collaborators ) ? $collaborators : array() );
	}

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'template_redirect', array( $this, 'maybe_handle_auth_request' ), 0 );
	}

	/**
	 * Handle branded auth form submissions.
	 *
	 * @return void
	 */
	public function maybe_handle_auth_request() {
		$this->collaborators['request']->run_maybe_handle_auth_request();
	}

	/**
	 * Validate a login or lost-password rate limit.
	 *
	 * @param string              $bucket     Bucket name.
	 * @param string              $identifier Submitted identifier.
	 * @param array<string,mixed> $settings   Settings.
	 * @return true|WP_Error
	 */
	public function validate_rate_limit( $bucket, $identifier, $settings ) {
		return $this->collaborators['activity']->run_validate_rate_limit(
			$bucket,
			$identifier,
			$settings
		);
	}

	/**
	 * Log an auth-side rate-limit block to the shared verification activity table.
	 *
	 * @param string $identifier Submitted email identifier.
	 * @param string $status     Compact status key.
	 * @return bool
	 */
	public function log_rate_limit_result( $identifier, $status ) {
		return $this->collaborators['activity']->run_log_rate_limit_result( $identifier, $status );
	}

	/**
	 * Log a privacy-conscious branded authentication diagnostics event.
	 *
	 * @param string              $level      Severity level.
	 * @param string              $event_code Event code.
	 * @param string              $message    Event message.
	 * @param array<string,mixed> $context    Event context.
	 * @return bool
	 */
	public function log_auth_event( $level, $event_code, $message, $context = array() ) {
		return $this->collaborators['activity']->run_log_auth_event(
			$level,
			$event_code,
			$message,
			$context
		);
	}

	/**
	 * Get a public login error message.
	 *
	 * @param string $error_code Error code.
	 * @return string
	 */
	public function get_login_error_message( $error_code ) {
		return $this->collaborators['messages']->run_get_login_error_message( $error_code );
	}

	/**
	 * Get a public lost-password error message.
	 *
	 * @param string $error_code Error code.
	 * @return string
	 */
	public function get_lostpassword_error_message( $error_code ) {
		return $this->collaborators['messages']->run_get_lostpassword_error_message( $error_code );
	}

	/**
	 * Return the neutral reset-request status message.
	 *
	 * @return string
	 */
	public function get_lostpassword_sent_message() {
		return $this->collaborators['messages']->run_get_lostpassword_sent_message();
	}

	/**
	 * Validate a native WordPress password reset key.
	 *
	 * @param string $key   Password reset key.
	 * @param string $login User login.
	 * @return WP_User|WP_Error
	 */
	public function validate_password_reset_key( $key, $login ) {
		return $this->collaborators['password_reset']->run_validate_password_reset_key( $key, $login );
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
	public function complete_password_reset( $key, $login, $password, $password_confirm ) {
		return $this->collaborators['password_reset']->run_complete_password_reset(
			$key,
			$login,
			$password,
			$password_confirm
		);
	}

	/**
	 * Return a safe login redirect URL.
	 *
	 * @param string              $redirect_to Submitted redirect URL.
	 * @param array<string,mixed> $settings    Settings.
	 * @param WP_User|null        $user        Authenticated user, when available.
	 * @return string
	 */
	public function get_login_redirect_url( $redirect_to, $settings, $user = null ) {
		return $this->collaborators['redirects']->run_get_login_redirect_url(
			$redirect_to,
			$settings,
			$user
		);
	}
}
