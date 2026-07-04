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
 * Handles branded login and password-reset request submissions.
 */
class ALYNT_AG_Auth_Service {

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'template_redirect', array( $this, 'maybe_handle_auth_request' ), 4 );
	}

	/**
	 * Handle branded auth form submissions.
	 *
	 * @return void
	 */
	public function maybe_handle_auth_request() {
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
	 * Validate a login or lost-password rate limit.
	 *
	 * @param string              $bucket     Bucket name.
	 * @param string              $identifier Submitted identifier.
	 * @param array<string,mixed> $settings   Settings.
	 * @return true|WP_Error
	 */
	public function validate_rate_limit( $bucket, $identifier, $settings ) {
		$limiter = new ALYNT_AG_Rate_Limiter();

		if ( 'lostpassword' === $bucket ) {
			return $limiter->check_and_increment(
				'lostpassword',
				$identifier,
				$settings['lostpassword_rate_limit_count'],
				$settings['lostpassword_rate_limit_window']
			);
		}

		return $limiter->check_and_increment(
			'login',
			$identifier,
			$settings['login_rate_limit_count'],
			$settings['login_rate_limit_window']
		);
	}

	/**
	 * Get a public login error message.
	 *
	 * @param string $error_code Error code.
	 * @return string
	 */
	public function get_login_error_message( $error_code ) {
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
	public function get_lostpassword_error_message( $error_code ) {
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
	public function get_lostpassword_sent_message() {
		return __( 'If an account can receive password reset instructions, an email has been sent. Please check your inbox and spam folder.', 'alynt-account-gateway' );
	}

	/**
	 * Validate a native WordPress password reset key.
	 *
	 * @param string $key   Password reset key.
	 * @param string $login User login.
	 * @return WP_User|WP_Error
	 */
	public function validate_password_reset_key( $key, $login ) {
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
	public function complete_password_reset( $key, $login, $password, $password_confirm ) {
		$user = $this->validate_password_reset_key( $key, $login );
		if ( is_wp_error( $user ) ) {
			return $user;
		}

		$registration = new ALYNT_AG_Registration_Service();
		$valid        = $registration->validate_password_pair( $password, $password_confirm );
		if ( is_wp_error( $valid ) ) {
			return $valid;
		}

		reset_password( $user, $password );

		return true;
	}

	/**
	 * Handle the branded login form.
	 *
	 * @return void
	 */
	private function handle_login_request() {
		check_admin_referer( 'alynt_ag_login', 'alynt_ag_auth_nonce' );

		$settings = ALYNT_AG_Settings_Schema::get_settings();
		$base_url = home_url( $settings['login_path'] );
		$email    = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Password is passed to wp_signon() and must not be altered.
		$password = isset( $_POST['pwd'] ) ? wp_unslash( $_POST['pwd'] ) : '';

		$rate_limit = $this->validate_rate_limit( 'login', $email, $settings );
		if ( is_wp_error( $rate_limit ) ) {
			wp_safe_redirect( add_query_arg( 'login_error', $rate_limit->get_error_code(), $base_url ) );
			exit;
		}

		if ( ! is_email( $email ) || '' === (string) $password ) {
			wp_safe_redirect( add_query_arg( 'login_error', 'failed', $base_url ) );
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
			wp_safe_redirect( add_query_arg( 'login_error', 'failed', $base_url ) );
			exit;
		}

		$redirect_to = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : '';

		wp_safe_redirect( $this->get_login_redirect_url( $redirect_to, $settings ) );
		exit;
	}

	/**
	 * Return a safe login redirect URL.
	 *
	 * @param string              $redirect_to Submitted redirect URL.
	 * @param array<string,mixed> $settings    Settings.
	 * @return string
	 */
	public function get_login_redirect_url( $redirect_to, $settings ) {
		$default = home_url( $settings['after_login_redirect'] );

		if ( '' === (string) $redirect_to ) {
			return $default;
		}

		return wp_validate_redirect( $redirect_to, $default );
	}

	/**
	 * Handle the branded lost-password request form.
	 *
	 * @return void
	 */
	private function handle_lostpassword_request() {
		check_admin_referer( 'alynt_ag_lostpassword', 'alynt_ag_auth_nonce' );

		$settings = ALYNT_AG_Settings_Schema::get_settings();
		$base_url = add_query_arg( 'action', 'lostpassword', home_url( $settings['account_action_base'] ) );
		$email    = isset( $_POST['user_login'] ) ? sanitize_email( wp_unslash( $_POST['user_login'] ) ) : '';

		$rate_limit = $this->validate_rate_limit( 'lostpassword', $email, $settings );
		if ( is_wp_error( $rate_limit ) ) {
			wp_safe_redirect( add_query_arg( 'reset_error', $rate_limit->get_error_code(), $base_url ) );
			exit;
		}

		if ( is_email( $email ) && email_exists( $email ) ) {
			retrieve_password( $email );
		}

		wp_safe_redirect( add_query_arg( 'reset_sent', '1', $base_url ) );
		exit;
	}

	/**
	 * Handle native WordPress reset-key password updates through the branded form.
	 *
	 * @return void
	 */
	private function handle_reset_password_request() {
		check_admin_referer( 'alynt_ag_reset_password', 'alynt_ag_auth_nonce' );

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

		$result = $this->complete_password_reset( $key, $login, $password, $password_confirm );
		if ( is_wp_error( $result ) ) {
			wp_safe_redirect( add_query_arg( 'password_error', $result->get_error_code(), $base_url ) );
			exit;
		}

		wp_safe_redirect( add_query_arg( 'password_reset', '1', home_url( $settings['login_path'] ) ) );
		exit;
	}
}
