<?php
/**
 * Registration completion collaborator.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Completes confirmed registrations and builds their return login URL.
 */
class ALYNT_AG_Registration_Completion extends ALYNT_AG_Service_Collaborator {

	/**
	 * Return destination helper.
	 *
	 * @var ALYNT_AG_Return_Destination
	 */
	private $destinations;

	/**
	 * Constructor.
	 *
	 * @param object                      $service      Public registration facade.
	 * @param ALYNT_AG_Return_Destination $destinations Return destination helper.
	 */
	public function __construct( $service, $destinations ) {
		parent::__construct( $service );
		$this->destinations = $destinations;
	}

	/**
	 * Complete registration after email confirmation and password validation.
	 *
	 * @param string              $token                      Raw token.
	 * @param string              $password                   Password.
	 * @param string              $password_confirm           Password confirmation.
	 * @param array<string,mixed> $settings                   Settings.
	 * @param string              $last_completed_return_path Return path state.
	 * @return int|WP_Error
	 */
	public function run_complete_pending_registration( $token, $password, $password_confirm, $settings, &$last_completed_return_path ) {
		global $wpdb;

		$pending = $this->confirm_pending_token( $token );
		if ( is_wp_error( $pending ) ) {
			return $pending;
		}

		$last_completed_return_path = isset( $pending->return_path )
			? $this->destinations->relative_path( $pending->return_path, $settings )
			: '';

		$password_valid = $this->validate_password_pair( $password, $password_confirm );
		if ( is_wp_error( $password_valid ) ) {
			$this->log_registration_flow_result( $pending->email, $password_valid->get_error_code() );
			return $password_valid;
		}

		if ( email_exists( $pending->email ) ) {
			$this->log_registration_flow_result( $pending->email, 'email_unavailable' );
			return new WP_Error( 'email_unavailable', __( 'This email address can no longer be used.', 'alynt-account-gateway' ) );
		}

		$username = $this->generate_username( $pending->first_name, $pending->last_name, $settings );
		$user_id  = wp_create_user( $username, $password, $pending->email );

		if ( is_wp_error( $user_id ) ) {
			$this->log_registration_flow_result( $pending->email, $user_id->get_error_code() );
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
	 * Build the login URL used after a registration is completed.
	 *
	 * @param array<string,mixed> $settings                   Settings.
	 * @param string              $last_completed_return_path Return path state.
	 * @return string
	 */
	public function run_registration_complete_login_url( $settings, $last_completed_return_path ) {
		$login_url  = add_query_arg( 'registration_complete', '1', home_url( $settings['login_path'] ) );
		$return_url = $this->destinations->from_stored_path( $last_completed_return_path, $settings );

		if ( $return_url ) {
			$login_url = add_query_arg( 'redirect_to', rawurlencode( $return_url ), $login_url );
		}

		return $login_url;
	}
}
