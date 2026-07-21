<?php
/**
 * Routes branded authentication requests.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Routes branded authentication requests.
 */
class ALYNT_AG_Auth_Request_Handler extends ALYNT_AG_Service_Collaborator {

	/**
	 * Return destination helper.
	 *
	 * @var ALYNT_AG_Return_Destination
	 */
	private $destinations;

	/**
	 * Constructor.
	 *
	 * @param object                      $service      Public service facade.
	 * @param ALYNT_AG_Return_Destination $destinations Return destination helper.
	 */
	public function __construct( $service, $destinations ) {
		parent::__construct( $service );
		$this->destinations = $destinations;
	}

	// phpcs:disable WordPress.Security.NonceVerification.Missing -- Each handler verifies its branded frontend nonce explicitly before processing the request.

	/**
	 * Handle branded auth form submissions.
	 *
	 * @return void
	 */
	public function run_maybe_handle_auth_request() {
		$request_method = isset( $_SERVER['REQUEST_METHOD'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) : '';
		if ( 'POST' !== strtoupper( $request_method ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Action check only; nonce is verified by each handler.
		$action = isset( $_POST['alynt_ag_action'] ) ? sanitize_key( wp_unslash( $_POST['alynt_ag_action'] ) ) : '';

		if ( 'login' === $action ) {
			$this->handle_login_request();
			return;
		}

		if ( 'lostpassword' === $action ) {
			$this->handle_lostpassword_request();
			return;
		}

		if ( 'reset_password' === $action ) {
			$this->handle_reset_password_request();
		}
	}

	/**
	 * Handle the branded login form.
	 *
	 * @return void
	 */
	private function handle_login_request() {
		$settings = ALYNT_AG_Settings_Schema::get_settings();
		$base_url = home_url( $settings['login_path'] );
		$email    = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Validated as a same-site destination below.
		$submitted_redirect = isset( $_POST['redirect_to'] ) ? wp_unslash( $_POST['redirect_to'] ) : '';
		$redirect_to        = $this->destinations->absolute_url( $submitted_redirect, $settings );

		if ( ! $this->request_nonce_is_valid( 'alynt_ag_login', 'alynt_ag_auth_nonce' ) ) {
			wp_safe_redirect( $this->login_error_url( 'session_expired', $base_url, $redirect_to ) );
			exit;
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Password is passed to wp_signon() and must not be altered.
		$password = isset( $_POST['pwd'] ) ? wp_unslash( $_POST['pwd'] ) : '';

		$rate_limit = $this->validate_rate_limit( 'login', $email, $settings );
		if ( is_wp_error( $rate_limit ) ) {
			$this->log_auth_event(
				'warning',
				'branded_login_rate_limited',
				__( 'Blocked a branded login attempt by rate limit.', 'alynt-account-gateway' ),
				array(
					'has_email' => '' !== $email,
				)
			);
			wp_safe_redirect( $this->login_error_url( $rate_limit->get_error_code(), $base_url, $redirect_to ) );
			exit;
		}

		if ( ! is_email( $email ) || '' === (string) $password ) {
			$this->log_auth_event(
				'warning',
				'branded_login_failed',
				__( 'Rejected a branded login attempt before WordPress authentication.', 'alynt-account-gateway' ),
				array(
					'reason'       => 'invalid_request',
					'has_email'    => '' !== $email,
					'has_password' => '' !== (string) $password,
				)
			);
			wp_safe_redirect( $this->login_error_url( 'failed', $base_url, $redirect_to ) );
			exit;
		}

		$user = wp_signon(
			array(
				'user_login'    => $email,
				'user_password' => $password,
				'remember'      => ! empty( $_POST['rememberme'] ),
			),
			is_ssl()
		);

		if ( is_wp_error( $user ) ) {
			$this->log_auth_event(
				'warning',
				'branded_login_failed',
				__( 'WordPress rejected a branded login attempt.', 'alynt-account-gateway' ),
				array(
					'reason'     => 'wp_signon_failed',
					'error_code' => $user->get_error_code(),
				)
			);
			wp_safe_redirect( $this->login_error_url( 'failed', $base_url, $redirect_to ) );
			exit;
		}

		$destination = $this->get_login_redirect_url( $redirect_to, $settings, $user );

		$this->log_auth_event(
			'info',
			'branded_login_succeeded',
			__( 'Completed a branded login request.', 'alynt-account-gateway' ),
			array(
				'destination_path'     => $this->path_from_url( $destination ),
				'redirect_to_present'  => '' !== (string) $redirect_to,
				'redirect_to_accepted' => '' !== (string) $redirect_to && $destination === $redirect_to,
			)
		);

		wp_safe_redirect( $destination );
		exit;
	}

	/**
	 * Build a failed-login URL that retains a validated return destination.
	 *
	 * @param string $error_code  Public error code.
	 * @param string $base_url    Branded login URL.
	 * @param string $redirect_to Validated return destination.
	 * @return string
	 */
	private function login_error_url( $error_code, $base_url, $redirect_to = '' ) {
		$args = array( 'login_error' => sanitize_key( $error_code ) );

		if ( $redirect_to ) {
			$args['redirect_to'] = rawurlencode( $redirect_to );
		}

		return add_query_arg( $args, $base_url );
	}

	/**
	 * Handle the branded lost-password request form.
	 *
	 * @return void
	 */
	private function handle_lostpassword_request() {
		$settings = ALYNT_AG_Settings_Schema::get_settings();
		$base_url = add_query_arg( 'action', 'lostpassword', home_url( $settings['account_action_base'] ) );
		$email    = isset( $_POST['user_login'] ) ? sanitize_email( wp_unslash( $_POST['user_login'] ) ) : '';

		if ( ! $this->request_nonce_is_valid( 'alynt_ag_lostpassword', 'alynt_ag_auth_nonce' ) ) {
			wp_safe_redirect( add_query_arg( 'reset_error', 'session_expired', $base_url ) );
			exit;
		}

		$rate_limit = $this->validate_rate_limit( 'lostpassword', $email, $settings );
		if ( is_wp_error( $rate_limit ) ) {
			$this->log_auth_event(
				'warning',
				'branded_password_reset_rate_limited',
				__( 'Blocked a branded password-reset request by rate limit.', 'alynt-account-gateway' ),
				array(
					'has_email' => '' !== $email,
				)
			);
			wp_safe_redirect( add_query_arg( 'reset_error', $rate_limit->get_error_code(), $base_url ) );
			exit;
		}

		$matched_account = is_email( $email ) && email_exists( $email );
		$email_result    = null;

		if ( $matched_account ) {
			$email_result = retrieve_password( $email );
		}

		if ( is_wp_error( $email_result ) ) {
			$this->log_auth_event(
				'error',
				'branded_password_reset_email_failed',
				__( 'A branded password-reset email could not be sent.', 'alynt-account-gateway' ),
				array(
					'error_code' => $email_result->get_error_code(),
				)
			);
		}

		$this->log_auth_event(
			'info',
			'branded_password_reset_requested',
			__( 'Processed a branded password-reset request with a neutral public response.', 'alynt-account-gateway' ),
			array(
				'has_valid_email'    => is_email( $email ),
				'delivery_attempted' => $matched_account,
			)
		);

		wp_safe_redirect( add_query_arg( 'reset_sent', '1', $base_url ) );
		exit;
	}

	/**
	 * Handle native WordPress reset-key password updates through the branded form.
	 *
	 * @return void
	 */
	private function handle_reset_password_request() {
		$settings = ALYNT_AG_Settings_Schema::get_settings();
		$key      = isset( $_POST['key'] ) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : '';
		$login    = isset( $_POST['login'] ) ? sanitize_user( wp_unslash( $_POST['login'] ) ) : '';

		// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Passwords are validated by complete_password_reset() and must not be altered.
		$password         = isset( $_POST['password'] ) ? wp_unslash( $_POST['password'] ) : '';
		$password_confirm = isset( $_POST['password_confirm'] ) ? wp_unslash( $_POST['password_confirm'] ) : '';
		// phpcs:enable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		$base_url = add_query_arg(
			array(
				'action' => 'setpassword',
				'key'    => rawurlencode( $key ),
				'login'  => rawurlencode( $login ),
			),
			home_url( $settings['account_action_base'] )
		);

		if ( ! $this->request_nonce_is_valid( 'alynt_ag_reset_password', 'alynt_ag_auth_nonce' ) ) {
			wp_safe_redirect( add_query_arg( 'password_error', 'session_expired', $base_url ) );
			exit;
		}

		$result = $this->complete_password_reset( $key, $login, $password, $password_confirm );
		if ( is_wp_error( $result ) ) {
			wp_safe_redirect( add_query_arg( 'password_error', $result->get_error_code(), $base_url ) );
			exit;
		}

		wp_safe_redirect( add_query_arg( 'password_reset', '1', home_url( $settings['login_path'] ) ) );
		exit;
	}

	/**
	 * Return only the path portion of a URL for diagnostics.
	 *
	 * @param string $url URL.
	 * @return string
	 */
	private function path_from_url( $url ) {
		$path = wp_parse_url( $url, PHP_URL_PATH );

		return $path ? sanitize_text_field( $path ) : '';
	}

	// phpcs:enable WordPress.Security.NonceVerification.Missing
}
