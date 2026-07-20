<?php
/**
 * Privacy eraser.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Erases personal data stored in plugin-owned tables.
 */
class ALYNT_AG_Privacy_Eraser {

	/**
	 * Erase personal data stored by the plugin.
	 *
	 * @param string $email_address Email address.
	 * @param int    $page          Page number.
	 * @return array<string,mixed>
	 */
	public function erase_personal_data( $email_address, $page = 1 ) {
		unset( $page );

		global $wpdb;

		$email   = sanitize_email( $email_address );
		$user    = function_exists( 'get_user_by' ) ? get_user_by( 'email', $email ) : false;
		$user_id = $user && isset( $user->ID ) ? absint( $user->ID ) : 0;
		$tables  = ALYNT_AG_Database::tables();
		$removed = false;

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Eraser deletes plugin-owned personal data.
		$removed = (bool) $wpdb->delete( $tables['pending_registrations'], array( 'email' => $email ), array( '%s' ) ) || $removed;
		$removed = (bool) $wpdb->delete( $tables['verification_logs'], array( 'email' => $email ), array( '%s' ) ) || $removed;
		$removed = (bool) $wpdb->delete( $tables['consent_records'], array( 'email' => $email ), array( '%s' ) ) || $removed;

		if ( $user_id ) {
			$removed = (bool) $wpdb->delete( $tables['consent_records'], array( 'user_id' => $user_id ), array( '%d' ) ) || $removed;
			$removed = (bool) $wpdb->delete( $tables['webhook_logs'], array( 'user_id' => $user_id ), array( '%d' ) ) || $removed;
			$removed = (bool) $wpdb->delete( $tables['audit_logs'], array( 'user_id' => $user_id ), array( '%d' ) ) || $removed;
		}
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

		return array(
			'items_removed'  => $removed,
			'items_retained' => false,
			'messages'       => array(),
			'done'           => true,
		);
	}
}
