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
