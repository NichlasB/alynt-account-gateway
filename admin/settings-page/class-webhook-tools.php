<?php
/**
 * Settings page webhook-tools component.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Owns focused webhook-tools behavior for the settings page.
 */
class ALYNT_AG_Settings_Page_Webhook_Tools extends ALYNT_AG_Settings_Page_Component {

	/**
	 * Render webhook test and recent delivery tools.
	 *
	 * @return void
	 */
	public function render_webhook_tools() {
		$settings   = ALYNT_AG_Settings_Schema::get_settings();
		$logs       = $this->recent_webhook_logs();
		$read_error = is_wp_error( $logs ) ? $logs : null;
		$logs       = $read_error ? array() : $logs;
		?>
		<h2><?php esc_html_e( 'Webhook Tools', 'alynt-account-gateway' ); ?></h2>
		<p class="description">
			<?php esc_html_e( 'Send a sample account-created test event to the saved webhook URL and review recent delivery metadata.', 'alynt-account-gateway' ); ?>
		</p>
		<?php $this->render_admin_data_read_errors( array( $read_error ) ); ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="alynt-ag-inline-tool" data-alynt-ag-action-form>
			<input type="hidden" name="action" value="alynt_ag_test_webhook">
			<?php wp_nonce_field( 'alynt_ag_test_webhook' ); ?>
			<?php
			submit_button(
				__( 'Send Test Webhook', 'alynt-account-gateway' ),
				'secondary',
				'submit',
				false,
				array( 'disabled' => empty( $settings['account_created_webhook'] ) )
			);
			?>
		</form>

		<?php if ( empty( $settings['account_created_webhook'] ) ) : ?>
			<p class="description"><?php esc_html_e( 'Save an account-created webhook URL to enable test sends.', 'alynt-account-gateway' ); ?></p>
		<?php endif; ?>

		<?php $this->render_webhook_delivery_summary( $logs, $settings ); ?>

		<h3><?php esc_html_e( 'Recent Webhook Deliveries', 'alynt-account-gateway' ); ?></h3>
		<?php if ( empty( $logs ) ) : ?>
			<p><?php esc_html_e( 'No webhook deliveries have been recorded.', 'alynt-account-gateway' ); ?></p>
		<?php else : ?>
			<table class="widefat striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Time', 'alynt-account-gateway' ); ?></th>
						<th><?php esc_html_e( 'Event', 'alynt-account-gateway' ); ?></th>
						<th><?php esc_html_e( 'Destination', 'alynt-account-gateway' ); ?></th>
						<th><?php esc_html_e( 'HTTP', 'alynt-account-gateway' ); ?></th>
						<th><?php esc_html_e( 'Result', 'alynt-account-gateway' ); ?></th>
						<th><?php esc_html_e( 'Error', 'alynt-account-gateway' ); ?></th>
						<th><?php esc_html_e( 'Details', 'alynt-account-gateway' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $logs as $log ) : ?>
						<tr>
							<td><?php echo esc_html( $this->format_webhook_log_time( $log->created_at ) ); ?></td>
							<td><?php echo esc_html( $log->event_name ); ?></td>
							<td><?php echo esc_html( $log->destination_host ); ?></td>
							<td><?php echo esc_html( (string) absint( $log->http_status ) ); ?></td>
							<td>
								<?php echo esc_html( $this->webhook_result_label( $log->success ) ); ?>
							</td>
							<td><?php echo esc_html( $log->error_message ); ?></td>
							<td><?php $this->render_webhook_log_details( $log ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
		<?php
	}

	/**
	 * Render a summary of webhook delivery status and signing setup.
	 *
	 * @param array<int,object>   $logs     Recent webhook logs.
	 * @param array<string,mixed> $settings Current settings.
	 * @return void
	 */
	public function render_webhook_delivery_summary( $logs, $settings ) {
		$latest          = ! empty( $logs ) ? $logs[0] : null;
		$signing_enabled = ! empty( $settings['webhook_signing_secret'] );
		?>
		<div class="notice notice-info inline">
			<p>
				<strong><?php esc_html_e( 'Delivery Status:', 'alynt-account-gateway' ); ?></strong>
				<?php if ( $latest ) : ?>
					<?php
					printf(
						/* translators: 1: result label, 2: event name, 3: destination host, 4: HTTP status, 5: created date. */
						esc_html__( '%1$s for %2$s to %3$s with HTTP %4$s at %5$s.', 'alynt-account-gateway' ),
						esc_html( $this->webhook_result_label( $latest->success ) ),
						esc_html( $latest->event_name ),
						esc_html( $latest->destination_host ),
						esc_html( (string) absint( $latest->http_status ) ),
						esc_html( $this->format_webhook_log_time( $latest->created_at ) )
					);
					?>
				<?php else : ?>
					<?php esc_html_e( 'No deliveries have been logged yet.', 'alynt-account-gateway' ); ?>
				<?php endif; ?>
			</p>
			<p>
				<strong><?php esc_html_e( 'Signing:', 'alynt-account-gateway' ); ?></strong>
				<?php
				echo $signing_enabled
					? esc_html__( 'Enabled. Outgoing webhooks include timestamped HMAC verification headers.', 'alynt-account-gateway' )
					: esc_html__( 'Disabled. Add a webhook signing secret to send verification headers.', 'alynt-account-gateway' );
				?>
			</p>
			<details>
				<summary><?php esc_html_e( 'Signature Verification Reference', 'alynt-account-gateway' ); ?></summary>
				<p><?php esc_html_e( 'Verify the X-Alynt-AG-Signature header by computing HMAC-SHA256 over the timestamp, event name, and exact JSON body.', 'alynt-account-gateway' ); ?></p>
				<p><code><?php echo esc_html( 'sha256=HMAC_SHA256({X-Alynt-AG-Time}.{X-Alynt-AG-Event}.{raw_json_body}, signing_secret)' ); ?></code></p>
				<p><?php esc_html_e( 'Reject requests with an unexpected event name, an old timestamp, or a signature that does not match.', 'alynt-account-gateway' ); ?></p>
			</details>
		</div>
		<?php
	}

	/**
	 * Render expanded webhook log metadata.
	 *
	 * @param object $log Webhook log row.
	 * @return void
	 */
	public function render_webhook_log_details( $log ) {
		?>
		<details>
			<summary><?php esc_html_e( 'View', 'alynt-account-gateway' ); ?></summary>
			<ul>
				<li><strong><?php esc_html_e( 'Event:', 'alynt-account-gateway' ); ?></strong> <?php echo esc_html( $log->event_name ); ?></li>
				<li><strong><?php esc_html_e( 'Destination:', 'alynt-account-gateway' ); ?></strong> <?php echo esc_html( $log->destination_host ); ?></li>
				<li><strong><?php esc_html_e( 'HTTP Status:', 'alynt-account-gateway' ); ?></strong> <?php echo esc_html( (string) absint( $log->http_status ) ); ?></li>
				<li><strong><?php esc_html_e( 'Result:', 'alynt-account-gateway' ); ?></strong> <?php echo esc_html( $this->webhook_result_label( $log->success ) ); ?></li>
				<li><strong><?php esc_html_e( 'Created:', 'alynt-account-gateway' ); ?></strong> <?php echo esc_html( $this->format_webhook_log_time( $log->created_at ) ); ?></li>
				<?php if ( ! empty( $log->error_message ) ) : ?>
					<li><strong><?php esc_html_e( 'Error:', 'alynt-account-gateway' ); ?></strong> <?php echo esc_html( $log->error_message ); ?></li>
				<?php endif; ?>
			</ul>
		</details>
		<?php
	}

	/**
	 * Return a translated webhook result label.
	 *
	 * @param mixed $success Success flag.
	 * @return string
	 */
	public function webhook_result_label( $success ) {
		return (int) $success ? __( 'Success', 'alynt-account-gateway' ) : __( 'Failed', 'alynt-account-gateway' );
	}

	/**
	 * Format a stored UTC webhook log timestamp for display.
	 *
	 * @param string $created_at Stored timestamp.
	 * @return string
	 */
	public function format_webhook_log_time( $created_at ) {
		$timestamp = strtotime( (string) $created_at );

		if ( ! $timestamp ) {
			return (string) $created_at;
		}

		$date_format = (string) get_option( 'date_format', 'Y-m-d' );
		$time_format = (string) get_option( 'time_format', 'H:i' );

		return date_i18n( $date_format . ' ' . $time_format, $timestamp, true );
	}

	/**
	 * Return recent webhook log rows.
	 *
	 * @return array<int,object>|WP_Error
	 */
	public function recent_webhook_logs() {
		global $wpdb;

		$tables = ALYNT_AG_Database::tables();

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Admin viewer reads plugin-owned webhook log table.
		$logs = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT id, event_name, destination_host, http_status, success, error_message, created_at FROM {$tables['webhook_logs']} ORDER BY created_at DESC, id DESC LIMIT %d",
				10
			)
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		return is_array( $logs )
			? $logs
			: new WP_Error(
				'alynt_ag_webhook_logs_read_failed',
				__( 'Recent webhook deliveries could not be loaded. Refresh the page and check the database connection if the problem continues.', 'alynt-account-gateway' )
			);
	}
}
