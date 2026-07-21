<?php
/**
 * Retention cleanup.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Removes expired plugin-owned records.
 */
class ALYNT_AG_Retention_Cleanup {

	/**
	 * Maximum rows deleted by each retention query.
	 */
	const BATCH_SIZE = 500;

	/**
	 * Follow-up hook for draining larger retention backlogs.
	 */
	const CONTINUATION_HOOK = 'alynt_ag_retention_cleanup_continue';

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'alynt_ag_retention_cleanup', array( $this, 'run' ) );
		add_action( self::CONTINUATION_HOOK, array( $this, 'run' ) );
	}

	/**
	 * Run cleanup.
	 *
	 * @return bool
	 */
	public function run() {
		global $wpdb;

		$settings = ALYNT_AG_Settings_Schema::get_settings();
		$tables   = ALYNT_AG_Database::tables();
		$now      = current_time( 'mysql', true );
		$limit    = self::BATCH_SIZE;

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Plugin-owned retention cleanup tables require dynamic table names.
		$results   = array();
		$results[] = $wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$tables['pending_registrations']} WHERE expires_at < %s LIMIT %d",
				$now,
				$limit
			)
		);

		$webhook_success_days = max( 1, absint( $settings['success_log_retention'] ) );
		$webhook_failed_days  = max( 1, absint( $settings['failed_log_retention'] ) );
		$verification_days    = max( 1, absint( $settings['verification_log_retention'] ) );
		$diagnostics_days     = max( 1, absint( $settings['diagnostics_retention'] ) );
		$consent_days         = max( 1, absint( $settings['consent_record_retention'] ) );
		$audit_days           = max( 1, absint( $settings['audit_log_retention'] ) );

		$results[] = $wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$tables['webhook_logs']} WHERE success = 1 AND created_at < DATE_SUB(%s, INTERVAL %d DAY) LIMIT %d",
				$now,
				$webhook_success_days,
				$limit
			)
		);

		$results[] = $wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$tables['webhook_logs']} WHERE success = 0 AND created_at < DATE_SUB(%s, INTERVAL %d DAY) LIMIT %d",
				$now,
				$webhook_failed_days,
				$limit
			)
		);

		$results[] = $wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$tables['verification_logs']} WHERE created_at < DATE_SUB(%s, INTERVAL %d DAY) LIMIT %d",
				$now,
				$verification_days,
				$limit
			)
		);

		$results[] = $wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$tables['diagnostics_logs']} WHERE created_at < DATE_SUB(%s, INTERVAL %d DAY) LIMIT %d",
				$now,
				$diagnostics_days,
				$limit
			)
		);

		$results[] = $wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$tables['consent_records']} WHERE created_at < DATE_SUB(%s, INTERVAL %d DAY) LIMIT %d",
				$now,
				$consent_days,
				$limit
			)
		);

		$results[] = $wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$tables['audit_logs']} WHERE created_at < DATE_SUB(%s, INTERVAL %d DAY) LIMIT %d",
				$now,
				$audit_days,
				$limit
			)
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		if ( in_array( false, $results, true ) ) {
			ALYNT_AG_Diagnostics_Logger::log_event(
				'error',
				'cron',
				'retention_cleanup_failed',
				__( 'One or more retention cleanup queries failed.', 'alynt-account-gateway' ),
				array( 'failed_queries' => count( array_keys( $results, false, true ) ) )
			);

			return false;
		}

		if ( in_array( self::BATCH_SIZE, $results, true ) && ! $this->schedule_continuation() ) {
			ALYNT_AG_Diagnostics_Logger::log_event(
				'error',
				'cron',
				'retention_cleanup_continuation_failed',
				__( 'A retention cleanup continuation could not be scheduled.', 'alynt-account-gateway' )
			);

			return false;
		}

		return true;
	}

	/**
	 * Schedule one near-term follow-up without duplicating an existing event.
	 *
	 * @return bool
	 */
	private function schedule_continuation() {
		if ( wp_next_scheduled( self::CONTINUATION_HOOK ) ) {
			return true;
		}

		return (bool) wp_schedule_single_event( time() + MINUTE_IN_SECONDS, self::CONTINUATION_HOOK );
	}
}
