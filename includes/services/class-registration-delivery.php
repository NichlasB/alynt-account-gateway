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
}
