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
		add_action( 'template_redirect', array( $this, 'maybe_handle_auth_request' ), 0 );
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
			$result = $limiter->check_and_increment(
				'lostpassword',
				$identifier,
				$settings['lostpassword_rate_limit_count'],
				$settings['lostpassword_rate_limit_window']
			);

			if ( is_wp_error( $result ) ) {
				$this->log_rate_limit_result( $identifier, 'lostpassword_rate_limited' );
			}

			return $result;
		}

		$result = $limiter->check_and_increment(
			'login',
			$identifier,
			$settings['login_rate_limit_count'],
			$settings['login_rate_limit_window']
		);

		if ( is_wp_error( $result ) ) {
			$this->log_rate_limit_result( $identifier, 'login_rate_limited' );
		}

		return $result;
	}

	/**
	 * Log an auth-side rate-limit block to the shared verification activity table.
	 *
	 * @param string $identifier Submitted email identifier.
	 * @param string $status     Compact status key.
	 * @return bool
	 */
	public function log_rate_limit_result( $identifier, $status ) {
		global $wpdb;

		$email  = sanitize_email( $identifier );
		$status = sanitize_key( $status );

		if ( ! $email || ! $status ) {
			return false;
		}

		$tables = ALYNT_AG_Database::tables();

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Plugin-owned verification log table.
		return (bool) $wpdb->insert(
			$tables['verification_logs'],
			array(
				'email'      => $email,
				'provider'   => 'rate_limit',
				'status'     => $status,
				'blocked'    => 1,
				'created_at' => current_time( 'mysql', true ),
			),
			array( '%s', '%s', '%s', '%d', '%s' )
		);
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
		return ALYNT_AG_Diagnostics_Logger::log_event(
			$level,
			'security',
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
			$this->log_auth_event(
				'warning',
				'branded_login_rate_limited',
				__( 'Blocked a branded login attempt by rate limit.', 'alynt-account-gateway' ),
				array(
					'has_email' => '' !== $email,
				)
			);
			wp_safe_redirect( add_query_arg( 'login_error', $rate_limit->get_error_code(), $base_url ) );
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
			$this->log_auth_event(
				'warning',
				'branded_login_failed',
				__( 'WordPress rejected a branded login attempt.', 'alynt-account-gateway' ),
				array(
					'reason'     => 'wp_signon_failed',
					'error_code' => $user->get_error_code(),
				)
			);
			wp_safe_redirect( add_query_arg( 'login_error', 'failed', $base_url ) );
			exit;
		}

		$redirect_to = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : '';
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
	 * Return a safe login redirect URL.
	 *
	 * @param string              $redirect_to Submitted redirect URL.
	 * @param array<string,mixed> $settings    Settings.
	 * @param WP_User|null        $user        Authenticated user, when available.
	 * @return string
	 */
	public function get_login_redirect_url( $redirect_to, $settings, $user = null ) {
		$default = home_url( $this->get_default_login_redirect_path( $settings, $user ) );

		if ( '' === (string) $redirect_to ) {
			return $default;
		}

		$destination = wp_validate_redirect( $redirect_to, $default );

		if ( $this->is_auth_surface_redirect_destination( $destination, $settings ) ) {
			return $default;
		}

		return $destination;
	}

	/**
	 * Return the configured role-aware default login redirect path.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @param WP_User|null        $user     Authenticated user, when available.
	 * @return string
	 */
	private function get_default_login_redirect_path( $settings, $user = null ) {
		$roles = $user instanceof WP_User && is_array( $user->roles ) ? $user->roles : array();

		if ( in_array( 'administrator', $roles, true ) ) {
			return $settings['administrator_after_login_redirect'] ?? '/wp-admin/';
		}

		if ( in_array( 'shop_manager', $roles, true ) ) {
			return $settings['shop_manager_after_login_redirect'] ?? '/wp-admin/';
		}

		return $settings['after_login_redirect'] ?? '/my-account/';
	}

	/**
	 * Determine whether a post-login destination points back to an auth surface.
	 *
	 * @param string              $destination Validated redirect destination.
	 * @param array<string,mixed> $settings    Settings.
	 * @return bool
	 */
	private function is_auth_surface_redirect_destination( $destination, $settings ) {
		$path = wp_parse_url( $destination, PHP_URL_PATH );

		if ( ! is_string( $path ) || '' === $path ) {
			return false;
		}

		$path          = $this->normalize_redirect_path( $path );
		$login_path    = $this->normalize_redirect_path( isset( $settings['login_path'] ) ? (string) $settings['login_path'] : '/login/' );
		$account_base  = $this->normalize_redirect_path( isset( $settings['account_action_base'] ) ? (string) $settings['account_action_base'] : '/account' );
		$wp_login_path = $this->normalize_redirect_path( '/wp-login.php' );

		return in_array( $path, array( $login_path, $account_base, $wp_login_path ), true );
	}

	/**
	 * Normalize a URL path for redirect-surface comparison.
	 *
	 * @param string $path URL path.
	 * @return string
	 */
	private function normalize_redirect_path( $path ) {
		$path = '/' . ltrim( $path, '/' );
		$path = untrailingslashit( $path );

		return '' === $path ? '/' : $path;
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
}
