<?php
/**
 * Settings page security-pending component.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Owns focused security-pending behavior for the settings page.
 */
class ALYNT_AG_Settings_Page_Security_Pending extends ALYNT_AG_Settings_Page_Component {

	/**
	 * Render recent pending registration activity.
	 *
	 * @return void
	 */
	public function render_security_pending_registrations() {
		$registrations = $this->security_recent_pending_registrations( 10 );
		$read_error    = is_wp_error( $registrations ) ? $registrations : null;
		$registrations = $read_error ? array() : $registrations;
		?>
		<div class="alynt-ag-security-activity">
			<h3><?php esc_html_e( 'Recent Pending Registrations', 'alynt-account-gateway' ); ?></h3>
			<p class="description">
				<?php esc_html_e( 'Shows recent email-confirmation registration records stored by the plugin. Email addresses are masked in this admin view.', 'alynt-account-gateway' ); ?>
			</p>
			<?php $this->render_admin_data_read_errors( array( $read_error ) ); ?>

			<?php $this->render_security_pending_registration_lifecycle_signals( $registrations ); ?>

			<?php if ( empty( $registrations ) ) : ?>
				<p class="alynt-ag-security-status__notice">
					<?php esc_html_e( 'No pending registration records have been created yet.', 'alynt-account-gateway' ); ?>
				</p>
			<?php else : ?>
				<table class="widefat striped alynt-ag-security-activity__table" aria-label="<?php esc_attr_e( 'Recent pending registrations', 'alynt-account-gateway' ); ?>">
					<thead>
						<tr>
							<th scope="col"><?php esc_html_e( 'Email', 'alynt-account-gateway' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Status', 'alynt-account-gateway' ); ?></th>
							<th scope="col"><?php esc_html_e( 'User ID', 'alynt-account-gateway' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Created At', 'alynt-account-gateway' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Confirmed At', 'alynt-account-gateway' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Expires At', 'alynt-account-gateway' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Next Step', 'alynt-account-gateway' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $registrations as $registration ) : ?>
							<?php $status = $this->security_pending_registration_status( $registration ); ?>
							<tr>
								<td><?php echo esc_html( $this->mask_email_for_display( $registration->email ?? '' ) ); ?></td>
								<td>
									<span class="alynt-ag-security-registration-status alynt-ag-security-registration-status--<?php echo esc_attr( $status['key'] ); ?>">
										<?php echo esc_html( $status['label'] ); ?>
									</span>
								</td>
								<td><?php echo ! empty( $registration->user_id ) ? esc_html( (string) absint( $registration->user_id ) ) : esc_html__( 'None', 'alynt-account-gateway' ); ?></td>
								<td><?php echo esc_html( $registration->created_at ?? '' ); ?></td>
								<td><?php echo esc_html( $registration->confirmed_at ?? '' ); ?></td>
								<td><?php echo esc_html( $registration->expires_at ?? '' ); ?></td>
								<td class="alynt-ag-security-guidance"><?php echo esc_html( $this->security_pending_registration_guidance( $status['key'] ) ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Render pending registration lifecycle summary.
	 *
	 * @param array<int,object> $registrations Recent pending registration rows.
	 * @return void
	 */
	public function render_security_pending_registration_lifecycle_signals( $registrations ) {
		$items = $this->security_pending_registration_lifecycle_signal_items( $registrations );
		?>
		<div class="alynt-ag-security-lifecycle" aria-label="<?php esc_attr_e( 'Recent pending registration lifecycle signals', 'alynt-account-gateway' ); ?>">
			<h4><?php esc_html_e( 'Pending Registration Lifecycle Signals', 'alynt-account-gateway' ); ?></h4>
			<div class="alynt-ag-security-status__grid">
				<?php $this->render_security_signal_cards( $items ); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Return pending registration lifecycle signal items.
	 *
	 * @param array<int,object> $registrations Recent pending registration rows.
	 * @return array<int,array{label:string,status:string,count:int,message:string}>
	 */
	public function security_pending_registration_lifecycle_signal_items( $registrations ) {
		$counts = array(
			'pending'         => 0,
			'email-confirmed' => 0,
			'expired'         => 0,
			'completed'       => 0,
		);

		foreach ( $registrations as $registration ) {
			$status = $this->security_pending_registration_status( $registration );
			$key    = isset( $status['key'] ) ? sanitize_key( $status['key'] ) : 'pending';

			if ( isset( $counts[ $key ] ) ) {
				++$counts[ $key ];
			}
		}

		return array(
			array(
				'label'   => __( 'Waiting For Confirmation', 'alynt-account-gateway' ),
				'status'  => $counts['pending'] > 0 ? 'warning' : 'ready',
				'count'   => $counts['pending'],
				'message' => __( 'recent pending records still waiting for email confirmation. Watch resend activity and inbox-delivery support requests.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Confirmed, Not Completed', 'alynt-account-gateway' ),
				'status'  => $counts['email-confirmed'] > 0 ? 'warning' : 'ready',
				'count'   => $counts['email-confirmed'],
				'message' => __( 'recent records where email is confirmed but password setup is unfinished. Customers may need clearer next-step copy.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Expired Pending Records', 'alynt-account-gateway' ),
				'status'  => $counts['expired'] > 0 ? 'action' : 'ready',
				'count'   => $counts['expired'],
				'message' => __( 'recent pending records past their confirmation window. High counts can indicate missed emails or confusing confirmation instructions.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Completed Pending Records', 'alynt-account-gateway' ),
				'status'  => 'ready',
				'count'   => $counts['completed'],
				'message' => __( 'recent pending records that reached account creation. This helps compare completed registrations against stalled ones.', 'alynt-account-gateway' ),
			),
		);
	}

	/**
	 * Return recent registration verification logs.
	 *
	 * @param int $limit Maximum records.
	 * @return array<int,object>|WP_Error
	 */
	public function security_recent_verification_logs( $limit = 10 ) {
		global $wpdb;

		$tables = ALYNT_AG_Database::tables();
		$limit  = min( 25, max( 1, absint( $limit ) ) );

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Admin security viewer reads plugin-owned verification log table.
		$logs = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT id, email, provider, status, blocked, review_decision, reviewed_by, reviewed_at, created_at FROM {$tables['verification_logs']} ORDER BY created_at DESC, id DESC LIMIT %d",
				$limit
			)
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		return is_array( $logs )
			? $logs
			: new WP_Error(
				'alynt_ag_verification_logs_read_failed',
				__( 'Recent verification activity could not be loaded. Refresh the page and check the database connection if the problem continues.', 'alynt-account-gateway' )
			);
	}

	/**
	 * Return recent security diagnostics events.
	 *
	 * @param int $limit Maximum records.
	 * @return array<int,object>|WP_Error
	 */
	public function security_recent_diagnostics_events( $limit = 25 ) {
		global $wpdb;

		$tables = ALYNT_AG_Database::tables();
		$limit  = min( 50, max( 1, absint( $limit ) ) );

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Admin security viewer reads plugin-owned diagnostics log table.
		$events = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT event_code, context, created_at FROM {$tables['diagnostics_logs']} WHERE category = %s ORDER BY created_at DESC, id DESC LIMIT %d",
				'security',
				$limit
			)
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		return is_array( $events )
			? $events
			: new WP_Error(
				'alynt_ag_security_diagnostics_read_failed',
				__( 'Recent security diagnostics could not be loaded. Refresh the page and check the database connection if the problem continues.', 'alynt-account-gateway' )
			);
	}

	/**
	 * Return recent external diagnostics events.
	 *
	 * @param int $limit Maximum records.
	 * @return array<int,object>|WP_Error
	 */
	public function security_recent_external_diagnostics_events( $limit = 25 ) {
		global $wpdb;

		$tables = ALYNT_AG_Database::tables();
		$limit  = min( 50, max( 1, absint( $limit ) ) );

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Admin security viewer reads plugin-owned diagnostics log table.
		$events = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT event_code, context, created_at FROM {$tables['diagnostics_logs']} WHERE category = %s ORDER BY created_at DESC, id DESC LIMIT %d",
				'external_api',
				$limit
			)
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		return is_array( $events )
			? $events
			: new WP_Error(
				'alynt_ag_external_diagnostics_read_failed',
				__( 'Recent delivery diagnostics could not be loaded. Refresh the page and check the database connection if the problem continues.', 'alynt-account-gateway' )
			);
	}
}
