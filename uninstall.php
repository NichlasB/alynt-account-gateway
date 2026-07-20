<?php
/**
 * Uninstall cleanup.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'alynt_ag_settings' );
delete_option( 'alynt_ag_db_version' );

wp_clear_scheduled_hook( 'alynt_ag_retention_cleanup' );
wp_clear_scheduled_hook( 'alynt_ag_retention_cleanup_continue' );

global $wpdb;

$database_file = __DIR__ . '/includes/class-database.php';
if ( ! class_exists( 'ALYNT_AG_Database' ) && file_exists( $database_file ) ) {
	require_once $database_file;
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

// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Uninstall removes plugin-owned transient option rows.
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
