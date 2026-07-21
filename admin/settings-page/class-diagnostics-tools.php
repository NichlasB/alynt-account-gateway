<?php
/**
 * Settings page diagnostics-tools component.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Owns focused diagnostics-tools behavior for the settings page.
 */
class ALYNT_AG_Settings_Page_Diagnostics_Tools extends ALYNT_AG_Settings_Page_Component {

	/**
	 * Render diagnostics tools.
	 *
	 * @return void
	 */
	public function render_diagnostics_tools() {
		$health            = ALYNT_AG_Diagnostics_Logger::health_summary();
		$events            = ALYNT_AG_Diagnostics_Logger::recent_events( 100 );
		$verification_logs = $this->security_recent_verification_logs( 100 );
		$webhook_logs      = $this->recent_webhook_logs();
		$read_errors       = array_filter(
			array( $events, $verification_logs, $webhook_logs ),
			'is_wp_error'
		);
		$events            = is_wp_error( $events ) ? array() : $events;
		$verification_logs = is_wp_error( $verification_logs ) ? array() : $verification_logs;
		$webhook_logs      = is_wp_error( $webhook_logs ) ? array() : $webhook_logs;
		$recent_events     = array_slice( $events, 0, 20 );
		?>
		<?php $this->render_compatibility_warnings(); ?>

		<?php $this->render_gateway_preview_tools(); ?>

		<?php $this->render_settings_tools(); ?>

		<h2><?php esc_html_e( 'Diagnostics', 'alynt-account-gateway' ); ?></h2>
		<p class="description">
			<?php esc_html_e( 'Diagnostics are disabled by default. When enabled, structured events are stored in a plugin-owned table with sensitive fields redacted before persistence.', 'alynt-account-gateway' ); ?>
		</p>
		<?php $this->render_admin_data_read_errors( $read_errors ); ?>

		<table class="widefat striped" aria-label="<?php esc_attr_e( 'Diagnostics health summary', 'alynt-account-gateway' ); ?>">
			<tbody>
				<?php foreach ( $health as $label => $value ) : ?>
					<tr>
						<th scope="row"><?php echo esc_html( ucwords( str_replace( '_', ' ', $label ) ) ); ?></th>
						<td>
							<?php
							if ( is_bool( $value ) ) {
								echo esc_html( $value ? __( 'Yes', 'alynt-account-gateway' ) : __( 'No', 'alynt-account-gateway' ) );
							} else {
								echo esc_html( (string) $value );
							}
							?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<?php $this->render_diagnostics_operational_snapshot( $events, $verification_logs, $webhook_logs ); ?>

		<p>
			<a class="button" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=alynt_ag_export_diagnostics' ), 'alynt_ag_export_diagnostics' ) ); ?>">
				<?php esc_html_e( 'Export Diagnostics CSV', 'alynt-account-gateway' ); ?>
			</a>
		</p>

		<form
			method="post"
			action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
			data-alynt-ag-action-form
			data-alynt-ag-confirm="<?php esc_attr_e( 'Permanently clear all stored diagnostics events?', 'alynt-account-gateway' ); ?>"
		>
			<input type="hidden" name="action" value="alynt_ag_clear_diagnostics">
			<?php wp_nonce_field( 'alynt_ag_clear_diagnostics' ); ?>
			<?php submit_button( __( 'Clear Diagnostics Events', 'alynt-account-gateway' ), 'delete', 'submit', false ); ?>
		</form>

		<h3><?php esc_html_e( 'Recent Events', 'alynt-account-gateway' ); ?></h3>
		<?php if ( empty( $recent_events ) ) : ?>
			<p><?php esc_html_e( 'No diagnostics events have been recorded.', 'alynt-account-gateway' ); ?></p>
		<?php else : ?>
			<table class="widefat striped" aria-label="<?php esc_attr_e( 'Recent diagnostics events', 'alynt-account-gateway' ); ?>">
				<thead>
					<tr>
						<th scope="col"><?php esc_html_e( 'Time', 'alynt-account-gateway' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Level', 'alynt-account-gateway' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Category', 'alynt-account-gateway' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Event', 'alynt-account-gateway' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Message', 'alynt-account-gateway' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $recent_events as $event ) : ?>
						<tr>
							<td><?php echo esc_html( $event->created_at ); ?></td>
							<td><?php echo esc_html( $event->level ); ?></td>
							<td><?php echo esc_html( $event->category ); ?></td>
							<td><?php echo esc_html( $event->event_code ); ?></td>
							<td><?php echo esc_html( $event->message ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
		<?php
	}

	/**
	 * Render an operator-friendly diagnostics summary.
	 *
	 * @param array<int,object> $diagnostic_events Recent diagnostics events.
	 * @param array<int,object> $verification_logs Recent verification logs.
	 * @param array<int,object> $webhook_logs      Recent webhook logs.
	 * @return void
	 */
	public function render_diagnostics_operational_snapshot( $diagnostic_events, $verification_logs, $webhook_logs ) {
		$items = $this->diagnostics_operational_snapshot_items( $diagnostic_events, $verification_logs, $webhook_logs );
		?>
		<h3><?php esc_html_e( 'Operational Snapshot', 'alynt-account-gateway' ); ?></h3>
		<p class="description">
			<?php esc_html_e( 'Summarizes recent diagnostics, verification, and delivery evidence so support can spot account-gateway problems without reading raw rows first.', 'alynt-account-gateway' ); ?>
		</p>
		<div class="alynt-ag-security-status__grid alynt-ag-diagnostics-snapshot">
			<?php foreach ( $items as $item ) : ?>
				<section class="alynt-ag-security-card alynt-ag-security-card--<?php echo esc_attr( $item['status'] ); ?>">
					<span class="alynt-ag-security-card__badge"><?php echo esc_html( $this->readiness_status_label( $item['status'] ) ); ?></span>
					<h4><?php echo esc_html( $item['label'] ); ?></h4>
					<p>
						<strong><?php echo esc_html( (string) $item['count'] ); ?></strong>
						<?php echo esc_html( $item['message'] ); ?>
					</p>
				</section>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * Return diagnostics snapshot items for the Advanced Tools screen.
	 *
	 * @param array<int,object> $diagnostic_events Recent diagnostics events.
	 * @param array<int,object> $verification_logs Recent verification logs.
	 * @param array<int,object> $webhook_logs      Recent webhook logs.
	 * @return array<int,array{label:string,status:string,count:int,message:string}>
	 */
	public function diagnostics_operational_snapshot_items( $diagnostic_events, $verification_logs, $webhook_logs ) {
		$redirects_and_blocks = $this->count_diagnostics_events_by_codes(
			$diagnostic_events,
			array( 'native_login_redirected', 'wp_admin_access_blocked' )
		);
		$auth_outcomes        = $this->count_diagnostics_events_by_codes(
			$diagnostic_events,
			array(
				'branded_login_failed',
				'branded_login_rate_limited',
				'branded_login_succeeded',
				'branded_password_reset_requested',
				'branded_password_reset_failed',
				'branded_password_reset_email_failed',
				'branded_password_reset_rate_limited',
				'branded_password_reset_completed',
			)
		);
		$provider_failures    = $this->count_security_logs_by_provider_statuses(
			$verification_logs,
			'turnstile',
			array( 'alynt_ag_turnstile_missing', 'alynt_ag_turnstile_request_failed', 'alynt_ag_turnstile_failed' )
		) + $this->count_security_logs_by_provider_statuses(
			$verification_logs,
			'reoon',
			array( 'alynt_ag_reoon_missing', 'alynt_ag_reoon_request_failed', 'alynt_ag_reoon_invalid_response', 'alynt_ag_reoon_blocked' ),
			array( '_flagged_blocked' )
		);
		$registration_issues  = $this->count_security_logs_by_provider_statuses(
			$verification_logs,
			'registration_flow',
			array(
				'terms_required',
				'consent_record_failed',
				'pending_registration_failed',
				'confirmation_email_failed',
				'password_mismatch',
				'alynt_ag_password_length',
				'alynt_ag_password_complexity',
				'email_unavailable',
			)
		);
		$email_failures       = $this->count_diagnostics_events_by_codes(
			$diagnostic_events,
			array( 'account_created_welcome_failed', 'branded_password_reset_email_failed' )
		) + $this->count_security_logs_by_provider_statuses(
			$verification_logs,
			'registration_flow',
			array( 'confirmation_email_failed' )
		);
		$webhook_failures     = $this->count_diagnostics_events_by_code( $diagnostic_events, 'account_created_webhook_failed' ) + $this->count_failed_webhook_logs( $webhook_logs );

		return array(
			array(
				'label'   => __( 'Redirects and Admin Blocks', 'alynt-account-gateway' ),
				'status'  => $redirects_and_blocks > 0 ? 'warning' : 'ready',
				'count'   => $redirects_and_blocks,
				'message' => __( 'recent native login redirects or blocked wp-admin visits. Update stale links or review role access if this rises.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Branded Auth Outcomes', 'alynt-account-gateway' ),
				'status'  => 'ready',
				'count'   => $auth_outcomes,
				'message' => __( 'recent branded login and password-reset outcomes recorded by diagnostics.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Provider Verification Failures', 'alynt-account-gateway' ),
				'status'  => $provider_failures > 0 ? 'action' : 'ready',
				'count'   => $provider_failures,
				'message' => __( 'recent Turnstile or Reoon failures and blocks. Check provider configuration, outbound HTTP, and policy false positives.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Registration Flow Failures', 'alynt-account-gateway' ),
				'status'  => $registration_issues > 0 ? 'warning' : 'ready',
				'count'   => $registration_issues,
				'message' => __( 'recent registration blocks or system failures. Review consent copy, pending-registration storage, and password setup guidance.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Account Email Failures', 'alynt-account-gateway' ),
				'status'  => $email_failures > 0 ? 'action' : 'ready',
				'count'   => $email_failures,
				'message' => __( 'recent confirmation, reset, or welcome email failures. Check the mail path before inviting customers.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Webhook Delivery Failures', 'alynt-account-gateway' ),
				'status'  => $webhook_failures > 0 ? 'action' : 'ready',
				'count'   => $webhook_failures,
				'message' => __( 'recent account-created webhook failures. Review endpoint status, signing, and the Webhooks tab delivery rows.', 'alynt-account-gateway' ),
			),
		);
	}

	/**
	 * Export diagnostics events.
	 *
	 * @return void
	 */
	public function handle_export_diagnostics() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to export diagnostics.', 'alynt-account-gateway' ) );
		}

		check_admin_referer( 'alynt_ag_export_diagnostics' );
		$events = ALYNT_AG_Diagnostics_Logger::recent_events( 100 );

		if ( is_wp_error( $events ) ) {
			wp_die( esc_html( $events->get_error_message() ) );
		}

		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=alynt-account-gateway-diagnostics.csv' );

		ALYNT_AG_Diagnostics_Logger::export_csv( $events );
		exit;
	}

	/**
	 * Clear diagnostics events.
	 *
	 * @return void
	 */
	public function handle_clear_diagnostics() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to clear diagnostics.', 'alynt-account-gateway' ) );
		}

		check_admin_referer( 'alynt_ag_clear_diagnostics' );
		$cleared = ALYNT_AG_Diagnostics_Logger::clear_events();

		wp_safe_redirect(
			add_query_arg(
				array(
					'page'            => 'alynt-account-gateway',
					'tab'             => 'advanced_tools',
					'alynt_ag_notice' => $cleared ? 'diagnostics_cleared' : 'diagnostics_clear_failed',
				),
				admin_url( 'options-general.php' )
			)
		);
		exit;
	}
}
