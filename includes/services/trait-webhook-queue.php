<?php
/**
 * Initial webhook queue behavior.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Queues account-created delivery outside the registration request.
 */
trait ALYNT_AG_Webhook_Queue {

	/**
	 * Queue an account-created webhook without delaying registration completion.
	 *
	 * @param int                 $user_id  User ID.
	 * @param array<string,mixed> $settings Settings.
	 * @return true|WP_Error
	 */
	public function queue_account_created( $user_id, $settings ) {
		$url = ! empty( $settings['account_created_webhook'] ) ? esc_url_raw( $settings['account_created_webhook'] ) : '';
		if ( ! $url ) {
			return true;
		}

		if ( ! $this->is_allowed_delivery_url( $url ) ) {
			return new WP_Error( 'alynt_ag_webhook_insecure_url', __( 'Webhook URLs must use HTTPS unless they point to a local development host.', 'alynt-account-gateway' ) );
		}

		return ( new ALYNT_AG_Webhook_Retry_Scheduler() )->schedule_initial( self::DELIVERY_HOOK, $user_id );
	}

	/**
	 * Deliver a queued account-created webhook.
	 *
	 * @param int $user_id User ID.
	 * @return true|WP_Error
	 */
	public function deliver_account_created( $user_id ) {
		return $this->dispatch_account_created( absint( $user_id ), ALYNT_AG_Settings_Schema::get_settings() );
	}
}
