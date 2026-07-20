<?php
/**
 * Webhook retry scheduling.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Schedules bounded account-created webhook retries.
 */
class ALYNT_AG_Webhook_Retry_Scheduler {

	/**
	 * Queue an initial account-created delivery outside the customer request.
	 *
	 * @param string              $hook    Delivery hook.
	 * @param int                 $user_id User ID.
	 * @param array<string,mixed> $envelope Immutable delivery envelope.
	 * @return true|WP_Error
	 */
	public function schedule_initial( $hook, $user_id, $envelope = array() ) {
		$args = array( absint( $user_id ), $envelope );
		if ( wp_next_scheduled( $hook, $args ) ) {
			return true;
		}

		$scheduled = wp_schedule_single_event( time() + 1, $hook, $args, true );

		return is_wp_error( $scheduled ) || ! $scheduled
			? new WP_Error( 'alynt_ag_webhook_schedule_failed', __( 'The account-created webhook could not be queued.', 'alynt-account-gateway' ) )
			: true;
	}

	/**
	 * Queue a bounded retry after a transport or HTTP failure.
	 *
	 * @param string              $hook          Retry hook.
	 * @param int                 $maximum       Maximum retry count.
	 * @param int                 $user_id       User ID.
	 * @param int                 $retry_count   Current retry count.
	 * @param bool                $allow_retries Whether retries are enabled for this event.
	 * @param array<string,mixed> $envelope Immutable delivery envelope.
	 * @return bool
	 */
	public function schedule( $hook, $maximum, $user_id, $retry_count, $allow_retries, $envelope = array() ) {
		if ( ! $allow_retries || $retry_count >= $maximum ) {
			return false;
		}

		$next_retry = $retry_count + 1;
		$args       = array( absint( $user_id ), $next_retry, $envelope );
		if ( wp_next_scheduled( $hook, $args ) ) {
			return true;
		}

		$scheduled = wp_schedule_single_event( time() + ( MINUTE_IN_SECONDS * ( 2 ** $retry_count ) ), $hook, $args, true );
		if ( is_wp_error( $scheduled ) || ! $scheduled ) {
			ALYNT_AG_Diagnostics_Logger::log_event(
				'error',
				'cron',
				'webhook_retry_schedule_failed',
				__( 'A failed webhook retry could not be scheduled.', 'alynt-account-gateway' ),
				array(
					'user_id'     => absint( $user_id ),
					'retry_count' => $next_retry,
				)
			);

			return false;
		}

		return true;
	}
}
