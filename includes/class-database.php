<?php
/**
 * Database table management.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Installs and names plugin tables.
 */
class ALYNT_AG_Database {

	const DB_VERSION = '0.1.4';

	/**
	 * Install database tables.
	 *
	 * @return void
	 */
	public static function install() {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();
		$tables          = self::tables();

		$sql = array(
			"CREATE TABLE {$tables['pending_registrations']} (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				email varchar(190) NOT NULL,
				first_name varchar(100) NOT NULL DEFAULT '',
				last_name varchar(100) NOT NULL DEFAULT '',
				user_id bigint(20) unsigned NOT NULL DEFAULT 0,
				token_hash varchar(255) NOT NULL,
				status varchar(40) NOT NULL DEFAULT 'pending',
				expires_at datetime NOT NULL,
				created_at datetime NOT NULL,
				confirmed_at datetime NULL,
				PRIMARY KEY  (id),
				KEY user_id (user_id),
				KEY email (email),
				KEY status (status),
				KEY expires_at (expires_at)
			) {$charset_collate};",
			"CREATE TABLE {$tables['webhook_logs']} (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				event_name varchar(100) NOT NULL,
				user_id bigint(20) unsigned NOT NULL DEFAULT 0,
				destination_host varchar(190) NOT NULL DEFAULT '',
				http_status int(11) NOT NULL DEFAULT 0,
				success tinyint(1) NOT NULL DEFAULT 0,
				retry_count int(11) NOT NULL DEFAULT 0,
				payload longtext NULL,
				error_message text NULL,
				created_at datetime NOT NULL,
				PRIMARY KEY  (id),
				KEY event_name (event_name),
				KEY user_id (user_id),
				KEY success (success),
				KEY created_at (created_at)
			) {$charset_collate};",
			"CREATE TABLE {$tables['verification_logs']} (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				email varchar(190) NOT NULL,
				provider varchar(40) NOT NULL,
				status varchar(80) NOT NULL,
				blocked tinyint(1) NOT NULL DEFAULT 0,
				review_decision varchar(40) NOT NULL DEFAULT '',
				reviewed_by bigint(20) unsigned NOT NULL DEFAULT 0,
				reviewed_at datetime NULL,
				created_at datetime NOT NULL,
				PRIMARY KEY  (id),
				KEY email (email),
				KEY provider (provider),
				KEY status (status),
				KEY review_decision (review_decision),
				KEY reviewed_at (reviewed_at),
				KEY created_at (created_at)
			) {$charset_collate};",
			"CREATE TABLE {$tables['consent_records']} (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				user_id bigint(20) unsigned NOT NULL DEFAULT 0,
				email varchar(190) NOT NULL DEFAULT '',
				terms_path varchar(255) NOT NULL DEFAULT '',
				privacy_path varchar(255) NOT NULL DEFAULT '',
				context varchar(80) NOT NULL DEFAULT 'registration',
				consent_version varchar(40) NOT NULL DEFAULT '',
				settings_hash varchar(64) NOT NULL DEFAULT '',
				created_at datetime NOT NULL,
				PRIMARY KEY  (id),
				KEY user_id (user_id),
				KEY email (email),
				KEY created_at (created_at)
			) {$charset_collate};",
			"CREATE TABLE {$tables['audit_logs']} (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				user_id bigint(20) unsigned NOT NULL DEFAULT 0,
				action varchar(100) NOT NULL,
				context text NULL,
				created_at datetime NOT NULL,
				PRIMARY KEY  (id),
				KEY user_id (user_id),
				KEY action (action),
				KEY created_at (created_at)
			) {$charset_collate};",
			"CREATE TABLE {$tables['diagnostics_logs']} (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				level varchar(20) NOT NULL,
				category varchar(80) NOT NULL,
				event_code varchar(120) NOT NULL,
				message text NOT NULL,
				context longtext NULL,
				correlation_id varchar(80) NOT NULL DEFAULT '',
				created_at datetime NOT NULL,
				PRIMARY KEY  (id),
				KEY level (level),
				KEY category (category),
				KEY event_code (event_code),
				KEY created_at (created_at)
			) {$charset_collate};",
		);

		foreach ( $sql as $statement ) {
			dbDelta( $statement );
		}

		update_option( 'alynt_ag_db_version', self::DB_VERSION );
	}

	/**
	 * Install updates when the stored schema version is outdated.
	 *
	 * @return void
	 */
	public static function maybe_upgrade() {
		if ( get_option( 'alynt_ag_db_version' ) === self::DB_VERSION ) {
			return;
		}

		self::install();
	}

	/**
	 * Return table names.
	 *
	 * @return array<string,string>
	 */
	public static function tables() {
		global $wpdb;

		return array(
			'pending_registrations' => $wpdb->prefix . 'alynt_ag_pending_registrations',
			'webhook_logs'          => $wpdb->prefix . 'alynt_ag_webhook_logs',
			'verification_logs'     => $wpdb->prefix . 'alynt_ag_verification_logs',
			'consent_records'       => $wpdb->prefix . 'alynt_ag_consent_records',
			'audit_logs'            => $wpdb->prefix . 'alynt_ag_audit_logs',
			'diagnostics_logs'      => $wpdb->prefix . 'alynt_ag_diagnostics_logs',
		);
	}
}
