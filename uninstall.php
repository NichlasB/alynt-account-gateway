<?php
/**
 * Uninstall cleanup.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$alynt_ag_database_file = __DIR__ . '/includes/class-database.php';
if ( ! class_exists( 'ALYNT_AG_Database' ) && file_exists( $alynt_ag_database_file ) ) {
	require_once $alynt_ag_database_file;
}

/**
 * Remove all plugin-owned data for the current site.
 */
$alynt_ag_cleanup_site = static function () {
	global $wpdb;

	delete_option( 'alynt_ag_settings' );
	delete_option( 'alynt_ag_db_version' );

	$scheduled_hooks = array(
		'alynt_ag_retention_cleanup',
		'alynt_ag_retention_cleanup_continue',
		'alynt_ag_deliver_account_created_webhook',
		'alynt_ag_retry_account_created_webhook',
	);

	foreach ( $scheduled_hooks as $hook ) {
		wp_clear_scheduled_hook( $hook );
	}

	if ( class_exists( 'ALYNT_AG_Database' ) ) {
		$tables = array_values( ALYNT_AG_Database::tables() );
	} else {
		// Keep uninstall self-contained if the database registry file is unavailable.
		$tables = array(
			$wpdb->prefix . 'alynt_ag_pending_registrations',
			$wpdb->prefix . 'alynt_ag_webhook_logs',
			$wpdb->prefix . 'alynt_ag_verification_logs',
			$wpdb->prefix . 'alynt_ag_consent_records',
			$wpdb->prefix . 'alynt_ag_audit_logs',
			$wpdb->prefix . 'alynt_ag_diagnostics_logs',
		);
	}

	foreach ( $tables as $table ) {
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Uninstall removes plugin-owned tables.
		$wpdb->query( "DROP TABLE IF EXISTS {$table}" );
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Uninstall removes plugin-owned transient and lock option rows.
	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s OR option_name LIKE %s OR option_name LIKE %s OR option_name LIKE %s",
			$wpdb->esc_like( '_transient_alynt_ag_rl_' ) . '%',
			$wpdb->esc_like( '_transient_timeout_alynt_ag_rl_' ) . '%',
			$wpdb->esc_like( '_transient_alynt_ag_rl_meta_' ) . '%',
			$wpdb->esc_like( '_transient_timeout_alynt_ag_rl_meta_' ) . '%',
			$wpdb->esc_like( 'alynt_ag_lock_' ) . '%'
		)
	);
	// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
};

if ( is_multisite() ) {
	$offset = 0;
	$limit  = 100;

	do {
		$site_ids = get_sites(
			array(
				'fields' => 'ids',
				'number' => $limit,
				'offset' => $offset,
			)
		);

		foreach ( $site_ids as $site_id ) {
			switch_to_blog( $site_id );
			$alynt_ag_cleanup_site();
			restore_current_blog();
		}

		$count   = count( $site_ids );
		$offset += $count;
	} while ( $count === $limit );
} else {
	$alynt_ag_cleanup_site();
}

unset( $alynt_ag_cleanup_site, $alynt_ag_database_file );
