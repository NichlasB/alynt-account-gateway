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

global $wpdb;

$tables = array(
	$wpdb->prefix . 'alynt_ag_pending_registrations',
	$wpdb->prefix . 'alynt_ag_webhook_logs',
	$wpdb->prefix . 'alynt_ag_verification_logs',
	$wpdb->prefix . 'alynt_ag_consent_records',
	$wpdb->prefix . 'alynt_ag_audit_logs',
	$wpdb->prefix . 'alynt_ag_diagnostics_logs',
);

foreach ( $tables as $table ) {
	// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Uninstall removes plugin-owned tables.
	$wpdb->query( "DROP TABLE IF EXISTS {$table}" );
	// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
}

// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Uninstall removes plugin-owned transient option rows.
$wpdb->query(
	$wpdb->prepare(
		"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
		$wpdb->esc_like( '_transient_alynt_ag_rl_' ) . '%',
		$wpdb->esc_like( '_transient_timeout_alynt_ag_rl_' ) . '%'
	)
);
// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
