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
	 * Deliver a previously snapshotted event without re-reading mutable state.
	 *
	 * @param int                 $user_id     User ID retained for delivery logs.
	 * @param array<string,mixed> $envelope    Immutable delivery envelope.
	 * @param int                 $retry_count Retry attempt number.
	 * @return true|WP_Error
	 */
	public function dispatch_queued_envelope( $user_id, $envelope, $retry_count = 0 ) {
		$url      = isset( $envelope['url'] ) && is_string( $envelope['url'] ) ? esc_url_raw( $envelope['url'] ) : '';
		$payload  = isset( $envelope['payload'] ) && is_array( $envelope['payload'] ) ? $envelope['payload'] : array();
		$settings = isset( $envelope['settings'] ) && is_array( $envelope['settings'] ) ? $envelope['settings'] : array();
		$event_id = isset( $payload['id'] ) && is_string( $payload['id'] ) ? trim( $payload['id'] ) : '';
		$event    = isset( $payload['event'] ) && is_string( $payload['event'] ) ? $payload['event'] : '';

		if (
			! $url
			|| 'account.created' !== $event
			|| '' === $event_id
			|| empty( $payload['user'] )
			|| ! is_array( $payload['user'] )
			|| empty( $payload['site'] )
			|| ! is_array( $payload['site'] )
			|| ! $this->is_allowed_delivery_url( $url )
		) {
			return new WP_Error( 'alynt_ag_webhook_invalid_envelope', __( 'The queued webhook data is invalid.', 'alynt-account-gateway' ) );
		}

		return $this->dispatch_payload( 'account.created', $url, $user_id, $payload, $settings, $retry_count, true, $envelope );
	}

	/**
	 * Build account-created payload.
	 *
	 * @param WP_User $user User object.
	 * @return array<string,mixed>
	 */
	public function build_account_created_payload( $user ) {
		return array(
			'id'          => 'evt_' . wp_generate_password( 24, false, false ),
			'event'       => 'account.created',
			'occurred_at' => current_time( 'mysql', true ),
			'user'        => array(
				'id'           => absint( $user->ID ),
				'user_login'   => $user->user_login,
				'user_email'   => $user->user_email,
				'first_name'   => get_user_meta( $user->ID, 'first_name', true ),
				'last_name'    => get_user_meta( $user->ID, 'last_name', true ),
				'display_name' => isset( $user->display_name ) ? $user->display_name : '',
				'roles'        => isset( $user->roles ) && is_array( $user->roles ) ? array_values( $user->roles ) : array(),
				'registered'   => isset( $user->user_registered ) ? $user->user_registered : '',
			),
			'site'        => array(
				'name' => wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
				'url'  => home_url( '/' ),
			),
		);
	}

	/**
	 * Snapshot only settings required to complete one delivery.
	 *
	 * @param array<string,mixed> $settings Plugin settings.
	 * @return array<string,mixed>
	 */
	private function webhook_delivery_settings( $settings ) {
		return array(
			'webhook_signing_secret' => isset( $settings['webhook_signing_secret'] ) ? (string) $settings['webhook_signing_secret'] : '',
			'debug_payload_logging'  => ! empty( $settings['debug_payload_logging'] ),
		);
	}

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

		$user = get_userdata( absint( $user_id ) );
		if ( ! $user ) {
			return new WP_Error( 'alynt_ag_webhook_user_missing', __( 'The webhook user could not be found.', 'alynt-account-gateway' ) );
		}

		$envelope = array(
			'url'      => $url,
			'payload'  => $this->build_account_created_payload( $user ),
			'settings' => $this->webhook_delivery_settings( $settings ),
		);

		return ( new ALYNT_AG_Webhook_Retry_Scheduler() )->schedule_initial( self::DELIVERY_HOOK, $user_id, $envelope );
	}

	/**
	 * Deliver a queued account-created webhook.
	 *
	 * @param int                 $user_id User ID.
	 * @param array<string,mixed> $envelope Immutable delivery envelope.
	 * @return true|WP_Error
	 */
	public function deliver_account_created( $user_id, $envelope = array() ) {
		if ( ! empty( $envelope ) ) {
			return $this->dispatch_queued_envelope( absint( $user_id ), $envelope );
		}

		return $this->dispatch_account_created( absint( $user_id ), ALYNT_AG_Settings_Schema::get_settings() );
	}
}
