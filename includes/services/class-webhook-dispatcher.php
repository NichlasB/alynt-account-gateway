<?php
/**
 * Webhook dispatcher placeholder.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Dispatches account gateway webhooks.
 */
class ALYNT_AG_Webhook_Dispatcher {

	/**
	 * Build account-created payload.
	 *
	 * @param WP_User $user User object.
	 * @return array<string,mixed>
	 */
	public function build_account_created_payload( $user ) {
		return array(
			'event'      => 'account.created',
			'user_id'    => $user->ID,
			'user_email' => $user->user_email,
			'user_login' => $user->user_login,
			'first_name' => get_user_meta( $user->ID, 'first_name', true ),
			'last_name'  => get_user_meta( $user->ID, 'last_name', true ),
			'created_at' => current_time( 'mysql', true ),
		);
	}
}
