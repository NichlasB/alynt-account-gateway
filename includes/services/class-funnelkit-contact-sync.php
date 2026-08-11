<?php
/**
 * Optional FunnelKit contact synchronization.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Keeps FunnelKit/Autonami contacts aligned after AAG creates users.
 */
class ALYNT_AG_FunnelKit_Contact_Sync {

	/**
	 * Register optional integration hooks.
	 *
	 * @return void
	 */
	public function register() {
		if ( defined( 'WP_CLI' ) && WP_CLI && class_exists( 'WP_CLI' ) ) {
			WP_CLI::add_command( 'alynt-ag funnelkit-backfill', array( $this, 'cli_backfill' ) );
		}
	}

	/**
	 * Sync one newly created account into a matching FunnelKit contact row.
	 *
	 * @param object $pending Pending registration row.
	 * @param int    $user_id Created WordPress user ID.
	 * @return true|WP_Error
	 */
	public function sync_registration_contact( $pending, $user_id ) {
		global $wpdb;

		$table = $this->contact_table();
		if ( ! $this->contact_table_exists( $table ) ) {
			return true;
		}

		$user_id    = absint( $user_id );
		$email      = isset( $pending->email ) ? sanitize_email( $pending->email ) : '';
		$first_name = isset( $pending->first_name ) ? sanitize_text_field( $pending->first_name ) : '';
		$last_name  = isset( $pending->last_name ) ? sanitize_text_field( $pending->last_name ) : '';

		if ( ! $user_id ) {
			return new WP_Error( 'funnelkit_sync_missing_user', __( 'FunnelKit contact sync could not identify the created user.', 'alynt-account-gateway' ) );
		}

		$sets   = array( 'wpid = %d' );
		$values = array( $user_id );

		if ( '' !== $first_name ) {
			$sets[]   = 'f_name = %s';
			$values[] = $first_name;
		}

		if ( '' !== $last_name ) {
			$sets[]   = 'l_name = %s';
			$values[] = $last_name;
		}

		$where = 'wpid = %d';
		if ( '' !== $email ) {
			$where    = 'email = %s OR wpid = %d';
			$values[] = $email;
		}
		$values[] = $user_id;

		$sql = "UPDATE {$table} SET " . implode( ', ', $sets ) . " WHERE {$where}";
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Optional third-party contact table sync after account creation.
		$result = $wpdb->query(
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- SQL fragments are fixed column assignments selected above.
			$wpdb->prepare( $sql, ...$values )
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		if ( false === $result ) {
			return new WP_Error( 'funnelkit_sync_failed', $wpdb->last_error ? $wpdb->last_error : __( 'FunnelKit contact sync failed.', 'alynt-account-gateway' ) );
		}

		return true;
	}

	/**
	 * Backfill blank FunnelKit names from linked WordPress users.
	 *
	 * @param int $limit Maximum contacts to inspect.
	 * @return array<string,int|bool>
	 */
	public function backfill_linked_contacts( $limit = 500 ) {
		global $wpdb;

		$table = $this->contact_table();
		if ( ! $this->contact_table_exists( $table ) ) {
			return array(
				'table_exists' => false,
				'inspected'    => 0,
				'updated'      => 0,
				'skipped'      => 0,
				'failed'       => 0,
			);
		}

		$limit = max( 1, min( 1000, absint( $limit ) ) );

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- One-time optional third-party contact-table backfill.
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT id, wpid, f_name, l_name FROM {$table} WHERE wpid <> 0 AND (f_name IS NULL OR f_name = '' OR l_name IS NULL OR l_name = '') LIMIT %d",
				$limit
			)
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		$stats = array(
			'table_exists' => true,
			'inspected'    => is_array( $rows ) ? count( $rows ) : 0,
			'updated'      => 0,
			'skipped'      => 0,
			'failed'       => 0,
		);

		if ( ! is_array( $rows ) ) {
			return $stats;
		}

		foreach ( $rows as $row ) {
			$data = $this->backfill_row_data( $row );
			if ( empty( $data ) ) {
				++$stats['skipped'];
				continue;
			}

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- One-time optional third-party contact-table backfill.
			$updated = $wpdb->update(
				$table,
				$data,
				array( 'id' => absint( $row->id ) ),
				array_fill( 0, count( $data ), '%s' ),
				array( '%d' )
			);

			if ( false === $updated ) {
				++$stats['failed'];
				continue;
			}

			$stats['updated'] += (int) $updated;
		}

		return $stats;
	}

	/**
	 * WP-CLI command callback for one-time FunnelKit contact-name backfill.
	 *
	 * @param array<int,string>   $args       Positional args.
	 * @param array<string,mixed> $assoc_args Associative args.
	 * @return void
	 */
	public function cli_backfill( $args, $assoc_args ) {
		unset( $args );

		$limit = isset( $assoc_args['limit'] ) ? absint( $assoc_args['limit'] ) : 500;
		$stats = $this->backfill_linked_contacts( $limit );

		if ( empty( $stats['table_exists'] ) ) {
			WP_CLI::warning( 'FunnelKit contact table was not found; nothing to backfill.' );
			return;
		}

		WP_CLI::success(
			sprintf(
				'FunnelKit backfill complete. Inspected: %d. Updated: %d. Skipped: %d. Failed: %d.',
				(int) $stats['inspected'],
				(int) $stats['updated'],
				(int) $stats['skipped'],
				(int) $stats['failed']
			)
		);
	}

	/**
	 * Build backfill update data for one contact row.
	 *
	 * @param object $row FunnelKit contact row.
	 * @return array<string,string>
	 */
	private function backfill_row_data( $row ) {
		$user_id    = isset( $row->wpid ) ? absint( $row->wpid ) : 0;
		$first_name = $user_id ? sanitize_text_field( get_user_meta( $user_id, 'first_name', true ) ) : '';
		$last_name  = $user_id ? sanitize_text_field( get_user_meta( $user_id, 'last_name', true ) ) : '';
		$data       = array();

		if ( $this->is_blank_contact_name( $row->f_name ?? null ) && '' !== $first_name ) {
			$data['f_name'] = $first_name;
		}

		if ( $this->is_blank_contact_name( $row->l_name ?? null ) && '' !== $last_name ) {
			$data['l_name'] = $last_name;
		}

		return $data;
	}

	/**
	 * Check whether a FunnelKit contact name value is blank.
	 *
	 * @param mixed $value Contact name value.
	 * @return bool
	 */
	private function is_blank_contact_name( $value ) {
		return null === $value || '' === (string) $value;
	}

	/**
	 * Return the current site's FunnelKit contact table name.
	 *
	 * @return string
	 */
	private function contact_table() {
		global $wpdb;

		return $wpdb->prefix . 'bwf_contact';
	}

	/**
	 * Check whether the FunnelKit contact table exists.
	 *
	 * @param string $table Table name.
	 * @return bool
	 */
	private function contact_table_exists( $table ) {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Cheap optional third-party table existence check.
		return $table === $wpdb->get_var(
			$wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table ) )
		);
	}
}
