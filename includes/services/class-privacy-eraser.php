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
		$failed  = false;
		$targets = array(
			array( $tables['pending_registrations'], array( 'email' => $email ), array( '%s' ) ),
			array( $tables['verification_logs'], array( 'email' => $email ), array( '%s' ) ),
			array( $tables['consent_records'], array( 'email' => $email ), array( '%s' ) ),
		);

		if ( $user_id ) {
			$targets[] = array( $tables['consent_records'], array( 'user_id' => $user_id ), array( '%d' ) );
			$targets[] = array( $tables['webhook_logs'], array( 'user_id' => $user_id ), array( '%d' ) );
			$targets[] = array( $tables['audit_logs'], array( 'user_id' => $user_id ), array( '%d' ) );
		}

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Eraser deletes plugin-owned personal data.
		foreach ( $targets as $target ) {
			$result = $wpdb->delete( $target[0], $target[1], $target[2] );

			if ( false === $result ) {
				$failed = true;
				continue;
			}

			$removed = $result > 0 || $removed;
		}
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

		$messages = array();
		if ( $failed ) {
			$messages[] = __( 'Some Alynt Account Gateway records could not be erased. Please retry the request and check the site database if the problem continues.', 'alynt-account-gateway' );

			if ( class_exists( 'ALYNT_AG_Diagnostics_Logger' ) ) {
				ALYNT_AG_Diagnostics_Logger::log_event(
					'error',
					'database',
					'privacy_erasure_query_failed',
					__( 'A privacy erasure database query failed.', 'alynt-account-gateway' )
				);
			}
		}

		return array(
			'items_removed'  => $removed,
			'items_retained' => $failed,
			'messages'       => $messages,
			'done'           => true,
		);
	}
}
