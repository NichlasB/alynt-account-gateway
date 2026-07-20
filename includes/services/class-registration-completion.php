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

		$user_id = $this->create_registration_user( $pending, $password, $settings );
		if ( is_wp_error( $user_id ) ) {
			$this->log_registration_flow_result( $pending->email, $user_id->get_error_code() );
			return $user_id;
		}

		if ( ! $this->mark_pending_registration_created( $pending, $user_id ) ) {
			$this->rollback_registration_user( $user_id );
			$this->log_registration_flow_result( $pending->email, 'pending_registration_update_failed' );

			return new WP_Error( 'pending_registration_update_failed', __( 'The account could not be finalized. Please try again.', 'alynt-account-gateway' ) );
		}

		if ( ! $this->attach_registration_consent( $pending, $user_id ) ) {
			$this->restore_pending_registration( $pending );
			$this->rollback_registration_user( $user_id );
			$this->log_registration_flow_result( $pending->email, 'consent_attachment_failed' );

			return new WP_Error( 'consent_attachment_failed', __( 'The account consent record could not be finalized. Please try again.', 'alynt-account-gateway' ) );
		}

		$this->deliver_registration_integrations( $pending, $user_id, $settings );

		return $user_id;
	}

	/**
	 * Create and populate the confirmed WordPress user.
	 *
	 * @param object              $pending  Pending registration.
	 * @param string              $password Chosen password.
	 * @param array<string,mixed> $settings Plugin settings.
	 * @return int|WP_Error
	 */
	private function create_registration_user( $pending, $password, $settings ) {
		$username = $this->generate_username( $pending->first_name, $pending->last_name, $settings );
		$user_id  = wp_create_user( $username, $password, $pending->email );

		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}

		$updated = wp_update_user(
			array(
				'ID'           => (int) $user_id,
				'first_name'   => $pending->first_name,
				'last_name'    => $pending->last_name,
				'display_name' => trim( $pending->first_name . ' ' . $pending->last_name ),
			)
		);

		if ( is_wp_error( $updated ) || ! $updated ) {
			$this->rollback_registration_user( $user_id );

			return new WP_Error( 'user_profile_update_failed', __( 'The account profile could not be saved. Please try again.', 'alynt-account-gateway' ) );
		}

		return (int) $user_id;
	}

	/**
	 * Mark a pending registration as converted.
	 *
	 * @param object $pending Pending registration.
	 * @param int    $user_id WordPress user ID.
	 * @return bool
	 */
	private function mark_pending_registration_created( $pending, $user_id ) {
		global $wpdb;

		$tables = ALYNT_AG_Database::tables();

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Plugin-owned pending registration table.
		$updated = $wpdb->update(
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

		return 1 === (int) $updated;
	}

	/**
	 * Attach the pending registration consent to the new user.
	 *
	 * @param object $pending Pending registration.
	 * @param int    $user_id WordPress user ID.
	 * @return bool
	 */
	private function attach_registration_consent( $pending, $user_id ) {
		$privacy = new ALYNT_AG_Privacy_Service();

		return $privacy->attach_registration_consent_to_user( $pending->email, $user_id );
	}

	/**
	 * Restore a converted pending registration after a later persistence failure.
	 *
	 * @param object $pending Pending registration.
	 * @return bool
	 */
	private function restore_pending_registration( $pending ) {
		global $wpdb;

		$tables = ALYNT_AG_Database::tables();

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Compensating update restores plugin-owned registration state.
		$restored = $wpdb->update(
			$tables['pending_registrations'],
			array(
				'status'  => 'email_confirmed',
				'user_id' => 0,
			),
			array( 'id' => (int) $pending->id ),
			array( '%s', '%d' ),
			array( '%d' )
		);

		if ( 1 !== (int) $restored ) {
			ALYNT_AG_Diagnostics_Logger::log_event(
				'error',
				'database',
				'registration_rollback_failed',
				__( 'A pending registration could not be restored after account finalization failed.', 'alynt-account-gateway' )
			);
		}

		return 1 === (int) $restored;
	}

	/**
	 * Remove a newly created user after account finalization fails.
	 *
	 * @param int $user_id WordPress user ID.
	 * @return bool
	 */
	private function rollback_registration_user( $user_id ) {
		if ( ! function_exists( 'wp_delete_user' ) ) {
			require_once ABSPATH . 'wp-admin/includes/user.php';
		}

		$deleted = wp_delete_user( (int) $user_id );
		if ( ! $deleted ) {
			ALYNT_AG_Diagnostics_Logger::log_event(
				'critical',
				'database',
				'registration_user_rollback_failed',
				__( 'A newly created user could not be removed after account finalization failed.', 'alynt-account-gateway' ),
				array( 'user_id' => (int) $user_id )
			);
		}

		return (bool) $deleted;
	}

	/**
	 * Deliver non-blocking account-created integrations.
	 *
	 * @param object              $pending  Pending registration.
	 * @param int                 $user_id  WordPress user ID.
	 * @param array<string,mixed> $settings Plugin settings.
	 * @return void
	 */
	private function deliver_registration_integrations( $pending, $user_id, $settings ) {
		$welcome_sent = $this->send_account_created_welcome_email( $pending, $user_id, $settings );
		if ( is_wp_error( $welcome_sent ) ) {
			$this->log_integration_failure(
				'account_created_welcome_failed',
				__( 'The account-created welcome email could not be sent.', 'alynt-account-gateway' ),
				array(
					'user_id' => $user_id,
					'email'   => $pending->email,
					'error'   => $welcome_sent->get_error_code(),
				)
			);
		}

		$webhook_sent = $this->dispatch_account_created_webhook( $user_id, $settings );
		if ( is_wp_error( $webhook_sent ) ) {
			$this->log_integration_failure(
				'account_created_webhook_failed',
				__( 'The account-created webhook could not be sent.', 'alynt-account-gateway' ),
				array(
					'user_id' => $user_id,
					'email'   => $pending->email,
					'error'   => $webhook_sent->get_error_code(),
				)
			);
		}
	}

	/**
	 * Record a non-blocking account-created integration failure.
	 *
	 * @param string              $event   Diagnostics event.
	 * @param string              $message Safe diagnostics message.
	 * @param array<string,mixed> $context Failure context.
	 * @return void
	 */
	private function log_integration_failure( $event, $message, $context ) {
		ALYNT_AG_Diagnostics_Logger::log_event(
			'warning',
			'external_api',
			$event,
			$message,
			$context
		);
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
