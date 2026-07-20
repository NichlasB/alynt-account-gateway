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

		check_admin_referer( 'alynt_ag_start_registration', 'alynt_ag_registration_nonce' );

		$settings = ALYNT_AG_Settings_Schema::get_settings();
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce is verified above before this optional return destination is read.
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Validated as a same-site destination below.
		$submitted_redirect = isset( $_POST['redirect_to'] ) ? wp_unslash( $_POST['redirect_to'] ) : '';
		$redirect_to        = $this->destinations->absolute_url( $submitted_redirect, $settings );
		$return_path        = $this->destinations->relative_path( $redirect_to, $settings );
		$base_url           = add_query_arg( 'action', 'register', home_url( $settings['account_action_base'] ) );

		if ( $redirect_to ) {
			$base_url = add_query_arg( 'redirect_to', rawurlencode( $redirect_to ), $base_url );
		}

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
			$this->log_registration_flow_result( $email, $terms->get_error_code() );
			wp_safe_redirect( add_query_arg( 'registration_error', $terms->get_error_code(), $base_url ) );
			exit;
		}

		// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Values are sanitized in create_pending_registration().
		$result = $this->create_pending_registration(
			isset( $_POST['first_name'] ) ? wp_unslash( $_POST['first_name'] ) : '',
			isset( $_POST['last_name'] ) ? wp_unslash( $_POST['last_name'] ) : '',
			isset( $_POST['email'] ) ? wp_unslash( $_POST['email'] ) : '',
			$settings,
			$return_path
		);
		// phpcs:enable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		if ( is_wp_error( $result ) ) {
			if ( 'email_unavailable' === $result->get_error_code() ) {
				wp_safe_redirect( add_query_arg( 'registration_sent', '1', $base_url ) );
				exit;
			}

			$this->log_registration_flow_result( $email, $result->get_error_code() );
			wp_safe_redirect( add_query_arg( 'registration_error', $result->get_error_code(), $base_url ) );
			exit;
		}

		$email_sent = $this->send_confirmation_email( $result, $settings );
		if ( is_wp_error( $email_sent ) ) {
			$this->log_registration_flow_result( $email, $email_sent->get_error_code() );
			wp_safe_redirect( add_query_arg( 'registration_error', $email_sent->get_error_code(), $base_url ) );
			exit;
		}

		wp_safe_redirect( add_query_arg( 'registration_sent', '1', $base_url ) );
		exit;
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

		wp_safe_redirect( $this->registration_complete_login_url( $settings ) );
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
}
