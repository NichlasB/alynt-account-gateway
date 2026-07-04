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
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'template_redirect', array( $this, 'maybe_handle_registration_request' ), 5 );
	}

	/**
	 * Handle branded registration form submissions.
	 *
	 * @return void
	 */
	public function maybe_handle_registration_request() {
		$request_method = isset( $_SERVER['REQUEST_METHOD'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) : '';
		if ( 'POST' !== strtoupper( $request_method ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Action check only; nonce is verified below before processing.
		$action = isset( $_POST['alynt_ag_action'] ) ? sanitize_key( wp_unslash( $_POST['alynt_ag_action'] ) ) : '';
		if ( 'complete_registration' === $action ) {
			$this->handle_complete_registration_request();
			return;
		}

		if ( 'resend_confirmation' === $action ) {
			$this->handle_resend_confirmation_request();
			return;
		}

		if ( 'start_registration' !== $action ) {
			return;
		}

		check_admin_referer( 'alynt_ag_start_registration', 'alynt_ag_registration_nonce' );

		$settings = ALYNT_AG_Settings_Schema::get_settings();
		$base_url = add_query_arg( 'action', 'register', home_url( $settings['account_action_base'] ) );

		if ( empty( $settings['registration_enabled'] ) ) {
			wp_safe_redirect( add_query_arg( 'registration_error', 'disabled', $base_url ) );
			exit;
		}

		$email      = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$rate_limit = $this->validate_rate_limit( 'registration', $email, $settings );
		if ( is_wp_error( $rate_limit ) ) {
			wp_safe_redirect( add_query_arg( 'registration_error', $rate_limit->get_error_code(), $base_url ) );
			exit;
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Turnstile token is sanitized by validate_registration_protection().
		$turnstile_token = isset( $_POST['cf-turnstile-response'] ) ? wp_unslash( $_POST['cf-turnstile-response'] ) : '';
		$protection      = $this->validate_registration_protection( $email, $turnstile_token, $settings );

		if ( is_wp_error( $protection ) ) {
			wp_safe_redirect( add_query_arg( 'registration_error', $protection->get_error_code(), $base_url ) );
			exit;
		}

		$terms_value = isset( $_POST['terms'] ) ? sanitize_text_field( wp_unslash( $_POST['terms'] ) ) : '';
		$terms       = $this->validate_terms_acceptance( $terms_value );
		if ( is_wp_error( $terms ) ) {
			wp_safe_redirect( add_query_arg( 'registration_error', $terms->get_error_code(), $base_url ) );
			exit;
		}

		// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Values are sanitized in create_pending_registration().
		$result = $this->create_pending_registration(
			isset( $_POST['first_name'] ) ? wp_unslash( $_POST['first_name'] ) : '',
			isset( $_POST['last_name'] ) ? wp_unslash( $_POST['last_name'] ) : '',
			isset( $_POST['email'] ) ? wp_unslash( $_POST['email'] ) : '',
			$settings
		);
		// phpcs:enable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		if ( is_wp_error( $result ) ) {
			if ( 'email_unavailable' === $result->get_error_code() ) {
				wp_safe_redirect( add_query_arg( 'registration_sent', '1', $base_url ) );
				exit;
			}

			wp_safe_redirect( add_query_arg( 'registration_error', $result->get_error_code(), $base_url ) );
			exit;
		}

		$email_sent = $this->send_confirmation_email( $result, $settings );
		if ( is_wp_error( $email_sent ) ) {
			wp_safe_redirect( add_query_arg( 'registration_error', $email_sent->get_error_code(), $base_url ) );
			exit;
		}

		wp_safe_redirect( add_query_arg( 'registration_sent', '1', $base_url ) );
		exit;
	}

	/**
	 * Create or replace a pending registration.
	 *
	 * @param string              $first_name First name.
	 * @param string              $last_name  Last name.
	 * @param string              $email      Email address.
	 * @param array<string,mixed> $settings   Settings.
	 * @return array<string,mixed>|WP_Error
	 */
	public function create_pending_registration( $first_name, $last_name, $email, $settings ) {
		global $wpdb;

		$first_name = sanitize_text_field( $first_name );
		$last_name  = sanitize_text_field( $last_name );
		$email      = sanitize_email( $email );

		if ( ! $first_name || ! $last_name || ! $email ) {
			return new WP_Error( 'missing_required_fields', __( 'Please complete all required fields.', 'alynt-account-gateway' ) );
		}

		if ( ! is_email( $email ) ) {
			return new WP_Error( 'invalid_email', __( 'Please enter a valid email address.', 'alynt-account-gateway' ) );
		}

		if ( email_exists( $email ) ) {
			return new WP_Error( 'email_unavailable', __( 'If this email address can be used, a confirmation email will be sent.', 'alynt-account-gateway' ) );
		}

		$token      = $this->generate_confirmation_token();
		$token_hash = $this->hash_token( $token );
		$now        = current_time( 'mysql', true );
		$expires_at = gmdate( 'Y-m-d H:i:s', time() + ( max( 1, absint( $settings['registration_token_hours'] ) ) * HOUR_IN_SECONDS ) );
		$tables     = ALYNT_AG_Database::tables();

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Plugin-owned pending registration table.
		$wpdb->delete(
			$tables['pending_registrations'],
			array(
				'email'  => $email,
				'status' => 'pending',
			),
			array( '%s', '%s' )
		);

		$inserted = $wpdb->insert(
			$tables['pending_registrations'],
			array(
				'email'        => $email,
				'first_name'   => $first_name,
				'last_name'    => $last_name,
				'user_id'      => 0,
				'token_hash'   => $token_hash,
				'status'       => 'pending',
				'expires_at'   => $expires_at,
				'created_at'   => $now,
				'confirmed_at' => null,
			),
			array( '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', null )
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		if ( ! $inserted ) {
			return new WP_Error( 'pending_registration_failed', __( 'The registration could not be started. Please try again.', 'alynt-account-gateway' ) );
		}

		$privacy = new ALYNT_AG_Privacy_Service();
		if ( ! $privacy->record_registration_consent( $email, $settings ) ) {
			return new WP_Error( 'consent_record_failed', __( 'The registration consent record could not be stored. Please try again.', 'alynt-account-gateway' ) );
		}

		return array(
			'id'               => (int) $wpdb->insert_id,
			'email'            => $email,
			'first_name'       => $first_name,
			'last_name'        => $last_name,
			'token'            => $token,
			'token_hash'       => $token_hash,
			'expires_at'       => $expires_at,
			'confirmation_url' => $this->build_confirmation_url( $token, $settings ),
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
		$checks = array();

		if ( ! empty( $settings['turnstile_secret_key'] ) && ! empty( $settings['turnstile_site_key'] ) ) {
			$turnstile = new ALYNT_AG_Turnstile_Client();
			$checks[]  = $turnstile->verify( sanitize_text_field( $turnstile_token ), $settings['turnstile_secret_key'] );
		}

		if ( ! empty( $settings['reoon_api_key'] ) && is_email( $email ) ) {
			$reoon    = new ALYNT_AG_Reoon_Client();
			$checks[] = $reoon->verify( sanitize_email( $email ), $settings['reoon_api_key'], $settings['reoon_mode'] ?? 'quick' );
		}

		if ( empty( $checks ) ) {
			return true;
		}

		$requires_all = ! empty( $settings['protection_mode'] ) && 'turnstile_and_reoon' === $settings['protection_mode'];
		$has_success  = false;
		$last_error   = null;

		foreach ( $checks as $check ) {
			if ( is_wp_error( $check ) ) {
				$last_error = $check;
				if ( $requires_all ) {
					return $check;
				}
				continue;
			}

			$has_success = true;
		}

		if ( $has_success ) {
			return true;
		}

		return $last_error ? $last_error : new WP_Error( 'alynt_ag_registration_protection_failed', __( 'Registration verification failed. Please try again.', 'alynt-account-gateway' ) );
	}

	/**
	 * Validate the public registration terms acceptance checkbox.
	 *
	 * @param mixed $accepted Submitted checkbox value.
	 * @return true|WP_Error
	 */
	public function validate_terms_acceptance( $accepted ) {
		if ( empty( $accepted ) ) {
			return new WP_Error( 'terms_required', __( 'Please accept the terms and privacy policy to continue.', 'alynt-account-gateway' ) );
		}

		return true;
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
		$limiter = new ALYNT_AG_Rate_Limiter();

		if ( 'resend_confirmation' === $bucket ) {
			return $limiter->check_and_increment(
				'resend_confirmation',
				$identifier,
				$settings['resend_confirmation_rate_limit_count'],
				$settings['resend_confirmation_rate_limit_window']
			);
		}

		return $limiter->check_and_increment(
			'registration',
			$identifier,
			$settings['registration_rate_limit_count'],
			$settings['registration_rate_limit_window']
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
		$expiry_hours = max( 1, absint( $settings['registration_token_hours'] ) );
		$email        = new ALYNT_AG_Email_Template_Service();
		$sent         = $email->send(
			'registration_confirmation',
			$pending['email'],
			array(
				'first_name'       => $pending['first_name'],
				'last_name'        => $pending['last_name'],
				'user_email'       => $pending['email'],
				'confirmation_url' => $pending['confirmation_url'],
				'expiry_hours'     => (string) $expiry_hours,
			),
			$settings
		);

		if ( is_wp_error( $sent ) ) {
			return new WP_Error( 'confirmation_email_failed', __( 'The confirmation email could not be sent. Please try again.', 'alynt-account-gateway' ) );
		}

		return true;
	}

	/**
	 * Find a pending registration by raw token.
	 *
	 * @param string $token Raw token.
	 * @return object|null
	 */
	public function find_pending_by_token( $token ) {
		global $wpdb;

		$token_hash = $this->hash_token( $token );
		$tables     = ALYNT_AG_Database::tables();
		$now        = current_time( 'mysql', true );

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Plugin-owned pending registration table.
		return $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$tables['pending_registrations']} WHERE token_hash = %s AND status IN ('pending', 'email_confirmed') AND expires_at >= %s LIMIT 1",
				$token_hash,
				$now
			)
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	/**
	 * Find the latest registration that can receive a fresh confirmation token.
	 *
	 * @param string $email Email address.
	 * @return object|null
	 */
	public function find_resendable_pending_by_email( $email ) {
		global $wpdb;

		$email  = sanitize_email( $email );
		$tables = ALYNT_AG_Database::tables();

		if ( ! is_email( $email ) ) {
			return null;
		}

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Plugin-owned pending registration table.
		return $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$tables['pending_registrations']} WHERE email = %s AND status IN ('pending', 'email_confirmed') ORDER BY id DESC LIMIT 1",
				$email
			)
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	/**
	 * Renew a pending registration with a fresh confirmation token.
	 *
	 * @param object              $pending  Pending registration row.
	 * @param array<string,mixed> $settings Settings.
	 * @return array<string,mixed>|WP_Error
	 */
	public function renew_pending_confirmation( $pending, $settings ) {
		global $wpdb;

		$token      = $this->generate_confirmation_token();
		$token_hash = $this->hash_token( $token );
		$now        = current_time( 'mysql', true );
		$expires_at = gmdate( 'Y-m-d H:i:s', time() + ( max( 1, absint( $settings['registration_token_hours'] ) ) * HOUR_IN_SECONDS ) );
		$tables     = ALYNT_AG_Database::tables();

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Plugin-owned pending registration table.
		$updated = $wpdb->update(
			$tables['pending_registrations'],
			array(
				'token_hash'   => $token_hash,
				'status'       => 'pending',
				'expires_at'   => $expires_at,
				'created_at'   => $now,
				'confirmed_at' => null,
			),
			array( 'id' => (int) $pending->id ),
			array( '%s', '%s', '%s', '%s', null ),
			array( '%d' )
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		if ( false === $updated ) {
			return new WP_Error( 'pending_registration_failed', __( 'The confirmation link could not be renewed. Please try again.', 'alynt-account-gateway' ) );
		}

		return array(
			'id'               => (int) $pending->id,
			'email'            => $pending->email,
			'first_name'       => $pending->first_name,
			'last_name'        => $pending->last_name,
			'token'            => $token,
			'token_hash'       => $token_hash,
			'expires_at'       => $expires_at,
			'confirmation_url' => $this->build_confirmation_url( $token, $settings ),
		);
	}

	/**
	 * Resend a confirmation link without exposing whether a pending registration exists.
	 *
	 * @param string              $email    Email address.
	 * @param array<string,mixed> $settings Settings.
	 * @return true|WP_Error
	 */
	public function resend_confirmation( $email, $settings ) {
		$email = sanitize_email( $email );

		if ( ! is_email( $email ) ) {
			return new WP_Error( 'invalid_email', __( 'Please enter a valid email address.', 'alynt-account-gateway' ) );
		}

		if ( email_exists( $email ) ) {
			return true;
		}

		$pending = $this->find_resendable_pending_by_email( $email );
		if ( ! $pending ) {
			return true;
		}

		$renewed = $this->renew_pending_confirmation( $pending, $settings );
		if ( is_wp_error( $renewed ) ) {
			return $renewed;
		}

		return $this->send_confirmation_email( $renewed, $settings );
	}

	/**
	 * Mark a pending registration as email-confirmed without creating a user.
	 *
	 * @param string $token Raw token.
	 * @return object|WP_Error
	 */
	public function confirm_pending_token( $token ) {
		global $wpdb;

		$pending = $this->find_pending_by_token( $token );
		if ( ! $pending ) {
			return new WP_Error( 'invalid_or_expired_token', __( 'This confirmation link is invalid or has expired.', 'alynt-account-gateway' ) );
		}

		if ( 'email_confirmed' === $pending->status ) {
			return $pending;
		}

		$tables = ALYNT_AG_Database::tables();
		$now    = current_time( 'mysql', true );

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Plugin-owned pending registration table.
		$wpdb->update(
			$tables['pending_registrations'],
			array(
				'status'       => 'email_confirmed',
				'confirmed_at' => $now,
			),
			array( 'id' => (int) $pending->id ),
			array( '%s', '%s' ),
			array( '%d' )
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		$pending->status       = 'email_confirmed';
		$pending->confirmed_at = $now;

		return $pending;
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
		global $wpdb;

		$pending = $this->confirm_pending_token( $token );
		if ( is_wp_error( $pending ) ) {
			return $pending;
		}

		$password_valid = $this->validate_password_pair( $password, $password_confirm );
		if ( is_wp_error( $password_valid ) ) {
			return $password_valid;
		}

		if ( email_exists( $pending->email ) ) {
			return new WP_Error( 'email_unavailable', __( 'This email address can no longer be used.', 'alynt-account-gateway' ) );
		}

		$username = $this->generate_username( $pending->first_name, $pending->last_name, $settings );
		$user_id  = wp_create_user( $username, $password, $pending->email );

		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}

		wp_update_user(
			array(
				'ID'           => (int) $user_id,
				'first_name'   => $pending->first_name,
				'last_name'    => $pending->last_name,
				'display_name' => trim( $pending->first_name . ' ' . $pending->last_name ),
			)
		);

		$tables = ALYNT_AG_Database::tables();

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Plugin-owned pending registration table.
		$wpdb->update(
			$tables['pending_registrations'],
			array(
				'status'  => 'account_created',
				'user_id' => (int) $user_id,
			),
			array( 'id' => (int) $pending->id ),
			array( '%s', '%d' ),
			array( '%d' )
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		$privacy = new ALYNT_AG_Privacy_Service();
		$privacy->attach_registration_consent_to_user( $pending->email, (int) $user_id );

		$welcome_sent = $this->send_account_created_welcome_email( $pending, (int) $user_id, $settings );
		if ( is_wp_error( $welcome_sent ) ) {
			ALYNT_AG_Diagnostics_Logger::log_event(
				'warning',
				'external_api',
				'account_created_welcome_failed',
				__( 'The account-created welcome email could not be sent.', 'alynt-account-gateway' ),
				array(
					'user_id' => (int) $user_id,
					'email'   => $pending->email,
					'error'   => $welcome_sent->get_error_code(),
				)
			);
		}

		$webhook_sent = $this->dispatch_account_created_webhook( (int) $user_id, $settings );
		if ( is_wp_error( $webhook_sent ) ) {
			ALYNT_AG_Diagnostics_Logger::log_event(
				'warning',
				'external_api',
				'account_created_webhook_failed',
				__( 'The account-created webhook could not be sent.', 'alynt-account-gateway' ),
				array(
					'user_id' => (int) $user_id,
					'email'   => $pending->email,
					'error'   => $webhook_sent->get_error_code(),
				)
			);
		}

		return (int) $user_id;
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
		if ( ! empty( $settings['email_new_user_welcome_disabled'] ) ) {
			return true;
		}

		$email = new ALYNT_AG_Email_Template_Service();
		$sent  = $email->send(
			'new_user_welcome',
			$pending->email,
			array(
				'first_name'    => $pending->first_name,
				'last_name'     => $pending->last_name,
				'user_email'    => $pending->email,
				'user_id'       => (string) absint( $user_id ),
				'dashboard_url' => home_url( $settings['after_login_redirect'] ?? '/my-account/' ),
			),
			$settings
		);

		if ( is_wp_error( $sent ) ) {
			return new WP_Error( 'welcome_email_failed', __( 'The welcome email could not be sent.', 'alynt-account-gateway' ) );
		}

		return true;
	}

	/**
	 * Dispatch the account-created webhook.
	 *
	 * @param int                 $user_id  Created user ID.
	 * @param array<string,mixed> $settings Settings.
	 * @return true|WP_Error
	 */
	public function dispatch_account_created_webhook( $user_id, $settings ) {
		$dispatcher = new ALYNT_AG_Webhook_Dispatcher();

		return $dispatcher->dispatch_account_created( $user_id, $settings );
	}

	/**
	 * Validate password and confirmation together.
	 *
	 * @param string $password         Password.
	 * @param string $password_confirm Password confirmation.
	 * @return true|WP_Error
	 */
	public function validate_password_pair( $password, $password_confirm ) {
		if ( $password !== $password_confirm ) {
			return new WP_Error( 'password_mismatch', __( 'The passwords do not match.', 'alynt-account-gateway' ) );
		}

		return $this->validate_password( $password );
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
		$format = ! empty( $settings['username_format'] ) ? (string) $settings['username_format'] : '@User_{first_name}_{last_name}';
		$base   = strtr(
			$format,
			array(
				'{first_name}' => $first_name,
				'{last_name}'  => $last_name,
				'{first}'      => $first_name,
				'{last}'       => $last_name,
			)
		);

		$base = preg_replace( '/\s+/', '_', $base );
		$base = sanitize_user( $base, true );
		$base = $base ? $base : 'user';

		$username = $base;
		$suffix   = 2;

		while ( username_exists( $username ) ) {
			$username = $base . '_' . $suffix;
			++$suffix;
		}

		return $username;
	}

	/**
	 * Handle set-password form submission.
	 *
	 * @return void
	 */
	private function handle_complete_registration_request() {
		check_admin_referer( 'alynt_ag_complete_registration', 'alynt_ag_registration_nonce' );

		$settings = ALYNT_AG_Settings_Schema::get_settings();

		// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Values are sanitized/validated by complete_pending_registration().
		$token            = isset( $_POST['alynt_ag_token'] ) ? wp_unslash( $_POST['alynt_ag_token'] ) : '';
		$password         = isset( $_POST['password'] ) ? wp_unslash( $_POST['password'] ) : '';
		$password_confirm = isset( $_POST['password_confirm'] ) ? wp_unslash( $_POST['password_confirm'] ) : '';
		// phpcs:enable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		$result = $this->complete_pending_registration( $token, $password, $password_confirm, $settings );

		$base_url = add_query_arg(
			array(
				'action'         => 'setpassword',
				'alynt_ag_token' => rawurlencode( $token ),
			),
			home_url( $settings['account_action_base'] )
		);

		if ( is_wp_error( $result ) ) {
			wp_safe_redirect( add_query_arg( 'password_error', $result->get_error_code(), $base_url ) );
			exit;
		}

		wp_safe_redirect( add_query_arg( 'registration_complete', '1', home_url( $settings['login_path'] ) ) );
		exit;
	}

	/**
	 * Handle resend-confirmation form submission.
	 *
	 * @return void
	 */
	private function handle_resend_confirmation_request() {
		check_admin_referer( 'alynt_ag_resend_confirmation', 'alynt_ag_registration_nonce' );

		$settings = ALYNT_AG_Settings_Schema::get_settings();
		$base_url = add_query_arg( 'action', 'invalidlink', home_url( $settings['account_action_base'] ) );
		$email    = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

		$rate_limit = $this->validate_rate_limit( 'resend_confirmation', $email, $settings );
		if ( is_wp_error( $rate_limit ) ) {
			wp_safe_redirect( add_query_arg( 'resend_error', $rate_limit->get_error_code(), $base_url ) );
			exit;
		}

		$result = $this->resend_confirmation( $email, $settings );
		if ( is_wp_error( $result ) ) {
			wp_safe_redirect( add_query_arg( 'resend_error', $result->get_error_code(), $base_url ) );
			exit;
		}

		wp_safe_redirect( add_query_arg( 'confirmation_resent', '1', $base_url ) );
		exit;
	}

	/**
	 * Generate a raw confirmation token.
	 *
	 * @return string
	 */
	public function generate_confirmation_token() {
		return wp_generate_password( 32, false, false );
	}

	/**
	 * Hash a confirmation token for storage.
	 *
	 * @param string $token Raw token.
	 * @return string
	 */
	public function hash_token( $token ) {
		return hash_hmac( 'sha256', (string) $token, wp_salt( 'auth' ) );
	}

	/**
	 * Verify a raw token against a stored hash.
	 *
	 * @param string $token Raw token.
	 * @param string $hash  Stored token hash.
	 * @return bool
	 */
	public function token_matches_hash( $token, $hash ) {
		return hash_equals( (string) $hash, $this->hash_token( $token ) );
	}

	/**
	 * Build the confirmation URL for a raw token.
	 *
	 * @param string              $token    Raw token.
	 * @param array<string,mixed> $settings Settings.
	 * @return string
	 */
	public function build_confirmation_url( $token, $settings ) {
		return add_query_arg(
			array(
				'action'         => 'setpassword',
				'alynt_ag_token' => rawurlencode( $token ),
			),
			home_url( $settings['account_action_base'] )
		);
	}

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
