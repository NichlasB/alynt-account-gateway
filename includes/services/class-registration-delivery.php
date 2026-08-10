<?php
/**
 * Delivers account-created email and webhook events.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Delivers account-created email and webhook events.
 */
class ALYNT_AG_Registration_Delivery extends ALYNT_AG_Service_Collaborator {

	/**
	 * Deliver non-blocking account-created integrations.
	 *
	 * @param object              $pending  Pending registration.
	 * @param int                 $user_id  WordPress user ID.
	 * @param array<string,mixed> $settings Plugin settings.
	 * @return void
	 */
	public function run_deliver_registration_integrations( $pending, $user_id, $settings ) {
		$this->track_integration_result(
			$this->sync_funnelkit_registration_contact( $pending, $user_id ),
			'funnelkit_contact_sync_failed',
			__( 'The FunnelKit contact could not be synchronized after account creation.', 'alynt-account-gateway' ),
			$pending,
			$user_id
		);

		$this->track_integration_result(
			$this->send_account_created_welcome_email( $pending, $user_id, $settings ),
			'account_created_welcome_failed',
			__( 'The account-created welcome email could not be sent.', 'alynt-account-gateway' ),
			$pending,
			$user_id
		);

		$this->track_integration_result(
			$this->dispatch_account_created_webhook( $user_id, $settings ),
			'account_created_webhook_failed',
			__( 'The account-created webhook could not be queued.', 'alynt-account-gateway' ),
			$pending,
			$user_id
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
	public function run_send_account_created_welcome_email( $pending, $user_id, $settings ) {
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
	 * Queue the account-created webhook.
	 *
	 * @param int                 $user_id  Created user ID.
	 * @param array<string,mixed> $settings Settings.
	 * @return true|WP_Error
	 */
	public function run_dispatch_account_created_webhook( $user_id, $settings ) {
		$dispatcher = new ALYNT_AG_Webhook_Dispatcher();

		return $dispatcher->queue_account_created( $user_id, $settings );
	}

	/**
	 * Record a non-blocking integration failure.
	 *
	 * @param true|WP_Error $result  Integration result.
	 * @param string        $event   Diagnostics event.
	 * @param string        $message Safe diagnostics message.
	 * @param object        $pending Pending registration.
	 * @param int           $user_id WordPress user ID.
	 * @return void
	 */
	private function track_integration_result( $result, $event, $message, $pending, $user_id ) {
		if ( ! is_wp_error( $result ) ) {
			return;
		}

		ALYNT_AG_Diagnostics_Logger::log_event(
			'warning',
			'external_api',
			$event,
			$message,
			array(
				'user_id' => $user_id,
				'email'   => $pending->email,
				'error'   => $result->get_error_code(),
			)
		);
	}
}
