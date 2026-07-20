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
	 * Maximum rows removed from each table per eraser invocation.
	 */
	const BATCH_SIZE = 100;

	/**
	 * Erase personal data stored by the plugin.
	 *
	 * @param string $email_address Email address.
	 * @param int    $page          Page number.
	 * @return array<string,mixed>
	 */
	public function erase_personal_data( $email_address, $page = 1 ) {
		global $wpdb;

		$page     = max( 1, absint( $page ) );
		$email    = sanitize_email( $email_address );
		$user     = function_exists( 'get_user_by' ) ? get_user_by( 'email', $email ) : false;
		$user_id  = $user && isset( $user->ID ) ? absint( $user->ID ) : 0;
		$tables   = ALYNT_AG_Database::tables();
		$removed  = false;
		$failed   = false;
		$has_more = false;
		$targets  = array(
			array( $tables['pending_registrations'], 'email', $email, '%s' ),
			array( $tables['verification_logs'], 'email', $email, '%s' ),
			array( $tables['consent_records'], 'email', $email, '%s' ),
		);

		if ( $user_id ) {
			$targets[] = array( $tables['consent_records'], 'user_id', $user_id, '%d' );
			$targets[] = array( $tables['webhook_logs'], 'user_id', $user_id, '%d' );
			$targets[] = array( $tables['audit_logs'], 'user_id', $user_id, '%d' );
		}

		foreach ( $targets as $target ) {
			$result = $this->erase_target_batch( $target );

			if ( is_wp_error( $result ) ) {
				$failed = true;
				continue;
			}

			$removed  = $result['removed'] || $removed;
			$has_more = $result['has_more'] || $has_more;
		}

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
			'done'           => ! $failed && ! $has_more,
		);
	}

	/**
	 * Erase one bounded target batch without offsetting disappearing rows.
	 *
	 * @param array{0:string,1:string,2:mixed,3:string} $target Target definition.
	 * @return array{removed:bool,has_more:bool}|WP_Error
	 */
	private function erase_target_batch( $target ) {
		global $wpdb;

		$limit = self::BATCH_SIZE + 1;

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Trusted table and column names target plugin-owned records.
		if ( '%d' === $target[3] ) {
			$query = $wpdb->prepare(
				"SELECT id FROM {$target[0]} WHERE {$target[1]} = %d ORDER BY id ASC LIMIT %d",
				$target[2],
				$limit
			);
		} else {
			$query = $wpdb->prepare(
				"SELECT id FROM {$target[0]} WHERE {$target[1]} = %s ORDER BY id ASC LIMIT %d",
				$target[2],
				$limit
			);
		}

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Query was prepared in the format-specific branch above.
		$rows = $wpdb->get_results( $query );

		if ( ! is_array( $rows ) ) {
			return new WP_Error( 'alynt_ag_privacy_erasure_read_failed' );
		}

		$has_more = count( $rows ) > self::BATCH_SIZE;
		$ids      = array();
		foreach ( array_slice( $rows, 0, self::BATCH_SIZE ) as $row ) {
			if ( isset( $row->id ) ) {
				$ids[] = absint( $row->id );
			}
		}

		if ( empty( $ids ) ) {
			return array(
				'removed'  => false,
				'has_more' => false,
			);
		}

		$placeholders = implode( ', ', array_fill( 0, count( $ids ), '%d' ) );
		$deleted      = $wpdb->query(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare -- Placeholder count matches the bounded integer ID list.
				"DELETE FROM {$target[0]} WHERE id IN ({$placeholders})",
				...$ids
			)
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		if ( false === $deleted ) {
			return new WP_Error( 'alynt_ag_privacy_erasure_delete_failed' );
		}

		return array(
			'removed'  => 0 < (int) $deleted,
			'has_more' => $has_more,
		);
	}
}
