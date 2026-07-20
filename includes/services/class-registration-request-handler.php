<?php
/**
 * Routes branded registration requests.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Routes branded registration requests.
 */
class ALYNT_AG_Registration_Request_Handler extends ALYNT_AG_Service_Collaborator {

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
	 * Handle branded registration form submissions.
	 *
	 * @return void
	 */
	public function run_maybe_handle_registration_request() {
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

		$this->handle_start_registration_request();
	}

	/**
	 * Handle a start-registration form submission.
	 *
	 * @return void
	 */
	private function handle_start_registration_request() {
		$settings = ALYNT_AG_Settings_Schema::get_settings();
		$context  = $this->start_registration_context( $settings );

		if ( ! $this->request_nonce_is_valid( 'alynt_ag_start_registration', 'alynt_ag_registration_nonce' ) ) {
			wp_safe_redirect( add_query_arg( 'registration_error', 'session_expired', $context['base_url'] ) );
			exit;
		}

		$valid = $this->validate_start_registration_request( $context['email'], $settings );

		if ( is_wp_error( $valid ) ) {
			wp_safe_redirect( add_query_arg( 'registration_error', $valid->get_error_code(), $context['base_url'] ) );
			exit;
		}

		$result = $this->create_pending_registration_from_request( $settings, $context['return_path'] );
		if ( is_wp_error( $result ) ) {
			if ( 'email_unavailable' === $result->get_error_code() ) {
				wp_safe_redirect( add_query_arg( 'registration_sent', '1', $context['base_url'] ) );
				exit;
			}

			$this->log_registration_flow_result( $context['email'], $result->get_error_code() );
			wp_safe_redirect( add_query_arg( 'registration_error', $result->get_error_code(), $context['base_url'] ) );
			exit;
		}

		$email_sent = $this->send_confirmation_email( $result, $settings );
		if ( is_wp_error( $email_sent ) ) {
			$this->log_registration_flow_result( $context['email'], $email_sent->get_error_code() );
			wp_safe_redirect( add_query_arg( 'registration_error', $email_sent->get_error_code(), $context['base_url'] ) );
			exit;
		}

		wp_safe_redirect( add_query_arg( 'registration_sent', '1', $context['base_url'] ) );
		exit;
	}

	/**
	 * Build sanitized registration request context.
	 *
	 * @param array<string,mixed> $settings Plugin settings.
	 * @return array{email:string,base_url:string,return_path:string}
	 */
	private function start_registration_context( $settings ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Nonce is verified by the caller; destination is validated below.
		$submitted_redirect = isset( $_POST['redirect_to'] ) ? wp_unslash( $_POST['redirect_to'] ) : '';
		$redirect_to        = $this->destinations->absolute_url( $submitted_redirect, $settings );
		$return_path        = $this->destinations->relative_path( $redirect_to, $settings );
		$base_url           = add_query_arg( 'action', 'register', home_url( $settings['account_action_base'] ) );
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce is verified by the caller.
		$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

		if ( $redirect_to ) {
			$base_url = add_query_arg( 'redirect_to', rawurlencode( $redirect_to ), $base_url );
		}

		return array(
			'email'       => $email,
			'base_url'    => $base_url,
			'return_path' => $return_path,
		);
	}

	/**
	 * Validate registration availability and abuse protections.
	 *
	 * @param string              $email    Submitted email.
	 * @param array<string,mixed> $settings Plugin settings.
	 * @return true|WP_Error
	 */
	private function validate_start_registration_request( $email, $settings ) {
		if ( empty( $settings['registration_enabled'] ) ) {
			return new WP_Error( 'disabled' );
		}

		$rate_limit = $this->validate_rate_limit( 'registration', $email, $settings );
		if ( is_wp_error( $rate_limit ) ) {
			return $rate_limit;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Nonce is verified by the caller; token is sanitized by validate_registration_protection().
		$turnstile_token = isset( $_POST['cf-turnstile-response'] ) ? wp_unslash( $_POST['cf-turnstile-response'] ) : '';
		$protection      = $this->validate_registration_protection( $email, $turnstile_token, $settings );

		if ( is_wp_error( $protection ) ) {
			return $protection;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce is verified by the caller.
		$terms_value = isset( $_POST['terms'] ) ? sanitize_text_field( wp_unslash( $_POST['terms'] ) ) : '';
		$terms       = $this->validate_terms_acceptance( $terms_value );
		if ( is_wp_error( $terms ) ) {
			$this->log_registration_flow_result( $email, $terms->get_error_code() );
			return $terms;
		}

		return true;
	}

	/**
	 * Create a pending registration from nonce-verified request fields.
	 *
	 * @param array<string,mixed> $settings    Plugin settings.
	 * @param string              $return_path Validated return path.
	 * @return array<string,mixed>|WP_Error
	 */
	private function create_pending_registration_from_request( $settings, $return_path ) {
		// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Nonce is verified by the caller; values are sanitized in create_pending_registration().
		return $this->create_pending_registration(
			isset( $_POST['first_name'] ) ? wp_unslash( $_POST['first_name'] ) : '',
			isset( $_POST['last_name'] ) ? wp_unslash( $_POST['last_name'] ) : '',
			isset( $_POST['email'] ) ? wp_unslash( $_POST['email'] ) : '',
			$settings,
			$return_path
		);
		// phpcs:enable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	}

	/**
	 * Handle set-password form submission.
	 *
	 * @return void
	 */
	private function handle_complete_registration_request() {
		$settings = ALYNT_AG_Settings_Schema::get_settings();

		// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Values are sanitized/validated by complete_pending_registration().
		$token            = isset( $_POST['alynt_ag_token'] ) ? wp_unslash( $_POST['alynt_ag_token'] ) : '';
		$password         = isset( $_POST['password'] ) ? wp_unslash( $_POST['password'] ) : '';
		$password_confirm = isset( $_POST['password_confirm'] ) ? wp_unslash( $_POST['password_confirm'] ) : '';
		// phpcs:enable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		$base_url = add_query_arg(
			array(
				'action'         => 'setpassword',
				'alynt_ag_token' => rawurlencode( $token ),
			),
			home_url( $settings['account_action_base'] )
		);

		if ( ! $this->request_nonce_is_valid( 'alynt_ag_complete_registration', 'alynt_ag_registration_nonce' ) ) {
			wp_safe_redirect( add_query_arg( 'password_error', 'session_expired', $base_url ) );
			exit;
		}

		$result = $this->complete_pending_registration( $token, $password, $password_confirm, $settings );

		if ( is_wp_error( $result ) ) {
			wp_safe_redirect( add_query_arg( 'password_error', $result->get_error_code(), $base_url ) );
			exit;
		}

		wp_safe_redirect( $this->registration_complete_login_url( $settings ) );
		exit;
	}

	/**
	 * Handle resend-confirmation form submission.
	 *
	 * @return void
	 */
	private function handle_resend_confirmation_request() {
		$settings = ALYNT_AG_Settings_Schema::get_settings();
		$base_url = add_query_arg( 'action', 'invalidlink', home_url( $settings['account_action_base'] ) );
		$email    = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

		if ( ! $this->request_nonce_is_valid( 'alynt_ag_resend_confirmation', 'alynt_ag_registration_nonce' ) ) {
			wp_safe_redirect( add_query_arg( 'resend_error', 'session_expired', $base_url ) );
			exit;
		}

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

	// phpcs:enable WordPress.Security.NonceVerification.Missing
}
