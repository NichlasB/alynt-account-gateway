<?php
/**
 * Diagnostics logger.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Privacy-conscious audit and diagnostics logger.
 */
class ALYNT_AG_Diagnostics_Logger {

	/**
	 * Supported severity levels in ascending order.
	 *
	 * @return array<string,int>
	 */
	public static function levels() {
		return array(
			'debug'    => 100,
			'info'     => 200,
			'warning'  => 300,
			'error'    => 400,
			'critical' => 500,
		);
	}

	/**
	 * Supported event categories.
	 *
	 * @return array<string,string>
	 */
	public static function categories() {
		return array(
			'database'     => __( 'Database', 'alynt-account-gateway' ),
			'filesystem'   => __( 'Filesystem', 'alynt-account-gateway' ),
			'external_api' => __( 'External API', 'alynt-account-gateway' ),
			'ajax'         => __( 'AJAX', 'alynt-account-gateway' ),
			'rest'         => __( 'REST', 'alynt-account-gateway' ),
			'cron'         => __( 'Cron', 'alynt-account-gateway' ),
			'admin_action' => __( 'Admin Action', 'alynt-account-gateway' ),
			'migration'    => __( 'Migration', 'alynt-account-gateway' ),
			'security'     => __( 'Security', 'alynt-account-gateway' ),
		);
	}

	/**
	 * Log an admin or system audit event.
	 *
	 * @param string              $action  Event action.
	 * @param array<string,mixed> $context Event context.
	 * @param int                 $user_id User ID.
	 * @return void
	 */
	public static function log( $action, $context = array(), $user_id = 0 ) {
		global $wpdb;

		$tables = ALYNT_AG_Database::tables();

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Plugin-owned audit table.
		$wpdb->insert(
			$tables['audit_logs'],
			array(
				'user_id'    => absint( $user_id ),
				'action'     => sanitize_key( $action ),
				'context'    => wp_json_encode( self::redact_context( $context ) ),
				'created_at' => current_time( 'mysql', true ),
			),
			array( '%d', '%s', '%s', '%s' )
		);
	}

	/**
	 * Log a structured diagnostics event.
	 *
	 * @param string              $level          Severity level.
	 * @param string              $category       Event category.
	 * @param string              $event_code     Short event code.
	 * @param string              $message        Summary message.
	 * @param array<string,mixed> $context        Event context.
	 * @param string              $correlation_id Optional correlation ID.
	 * @return bool
	 */
	public static function log_event( $level, $category, $event_code, $message, $context = array(), $correlation_id = '' ) {
		global $wpdb;

		$settings = ALYNT_AG_Settings_Schema::get_settings();

		if ( empty( $settings['diagnostics_enabled'] ) || ! self::level_is_allowed( $level, $settings['diagnostics_min_level'] ) ) {
			return false;
		}

		$tables = ALYNT_AG_Database::tables();

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Plugin-owned diagnostics table.
		return (bool) $wpdb->insert(
			$tables['diagnostics_logs'],
			array(
				'level'          => sanitize_key( $level ),
				'category'       => sanitize_key( $category ),
				'event_code'     => sanitize_key( $event_code ),
				'message'        => sanitize_text_field( $message ),
				'context'        => wp_json_encode( self::redact_context( $context ) ),
				'correlation_id' => sanitize_text_field( $correlation_id ),
				'created_at'     => current_time( 'mysql', true ),
			),
			array( '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
		);
	}

	/**
	 * Return recent diagnostics events.
	 *
	 * @param int $limit Number of records.
	 * @return array<int,object>
	 */
	public static function recent_events( $limit = 20 ) {
		global $wpdb;

		$tables = ALYNT_AG_Database::tables();
		$limit  = min( 100, max( 1, absint( $limit ) ) );

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Admin diagnostics viewer reads plugin-owned table.
		$events = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$tables['diagnostics_logs']} ORDER BY created_at DESC, id DESC LIMIT %d",
				$limit
			)
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		return is_array( $events ) ? $events : array();
	}

	/**
	 * Return diagnostics health summary.
	 *
	 * @return array<string,mixed>
	 */
	public static function health_summary() {
		global $wpdb, $wp_version;

		$tables = ALYNT_AG_Database::tables();

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Admin health panel reads plugin-owned table.
		$total      = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$tables['diagnostics_logs']}" );
		$last_event = $wpdb->get_var( "SELECT created_at FROM {$tables['diagnostics_logs']} ORDER BY created_at DESC, id DESC LIMIT 1" );
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		$settings = ALYNT_AG_Settings_Schema::get_settings();

		return array(
			'plugin_version'      => ALYNT_AG_VERSION,
			'wordpress_version'   => $wp_version,
			'php_version'         => PHP_VERSION,
			'diagnostics_enabled' => ! empty( $settings['diagnostics_enabled'] ),
			'minimum_level'       => $settings['diagnostics_min_level'],
			'retention_days'      => absint( $settings['diagnostics_retention'] ),
			'storage'             => 'custom_table',
			'total_events'        => $total,
			'last_event'          => $last_event ? $last_event : '',
		);
	}

	/**
	 * Clear diagnostics events.
	 *
	 * @return void
	 */
	public static function clear_events() {
		global $wpdb;

		$tables = ALYNT_AG_Database::tables();

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Admin purge clears plugin-owned diagnostics table.
		$wpdb->query( "TRUNCATE TABLE {$tables['diagnostics_logs']}" );
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	/**
	 * Export recent diagnostics rows as CSV.
	 *
	 * @return void
	 */
	public static function export_csv() {
		$events = self::recent_events( 100 );
		$output = fopen( 'php://output', 'w' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen -- Streaming admin CSV export.

		fputcsv( $output, array( 'created_at', 'level', 'category', 'event_code', 'message', 'context', 'correlation_id' ) );

		foreach ( $events as $event ) {
			fputcsv(
				$output,
				array(
					$event->created_at,
					$event->level,
					$event->category,
					$event->event_code,
					$event->message,
					$event->context,
					$event->correlation_id,
				)
			);
		}

		fclose( $output ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- Streaming admin CSV export.
	}

	/**
	 * Redact known sensitive fields.
	 *
	 * @param array<string,mixed> $context Raw context.
	 * @return array<string,mixed>
	 */
	public static function redact_context( $context ) {
		$redacted       = $context;
		$sensitive_keys = array( 'secret', 'secret_key', 'api_key', 'token', 'password', 'webhook_secret', 'authorization', 'cookie', 'nonce', 'raw_body' );

		foreach ( $redacted as $key => $value ) {
			if ( in_array( strtolower( (string) $key ), $sensitive_keys, true ) ) {
				$redacted[ $key ] = '[redacted]';
				continue;
			}

			if ( is_array( $value ) ) {
				$redacted[ $key ] = self::redact_context( $value );
				continue;
			}

			if ( is_string( $value ) && strlen( $value ) > 500 ) {
				$redacted[ $key ] = substr( $value, 0, 500 ) . '... [truncated]';
			}
		}

		return $redacted;
	}

	/**
	 * Determine whether a level passes the configured threshold.
	 *
	 * @param string $level     Candidate level.
	 * @param string $threshold Minimum level.
	 * @return bool
	 */
	private static function level_is_allowed( $level, $threshold ) {
		$levels    = self::levels();
		$level     = sanitize_key( $level );
		$threshold = sanitize_key( $threshold );

		if ( ! isset( $levels[ $level ], $levels[ $threshold ] ) ) {
			return false;
		}

		return $levels[ $level ] >= $levels[ $threshold ];
	}
}
