<?php
/**
 * Settings page security-signal-renderer-a component.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Owns focused security-signal-renderer-a behavior for the settings page.
 */
class ALYNT_AG_Settings_Page_Security_Signal_Renderer_A extends ALYNT_AG_Settings_Page_Component {

	/**
	 * Render recent registration verification activity.
	 *
	 * @return void
	 */
	public function render_security_verification_activity() {
		$logs              = $this->security_recent_verification_logs( 10 );
		$diagnostic_events = $this->security_recent_diagnostics_events( 25 );
		$external_events   = $this->security_recent_external_diagnostics_events( 25 );
		$webhook_logs      = $this->recent_webhook_logs();
		$settings          = ALYNT_AG_Settings_Schema::get_settings();
		$read_errors       = array_filter(
			array( $logs, $diagnostic_events, $external_events, $webhook_logs ),
			'is_wp_error'
		);
		$logs              = is_wp_error( $logs ) ? array() : $logs;
		$diagnostic_events = is_wp_error( $diagnostic_events ) ? array() : $diagnostic_events;
		$external_events   = is_wp_error( $external_events ) ? array() : $external_events;
		$webhook_logs      = is_wp_error( $webhook_logs ) ? array() : $webhook_logs;
		?>
		<div class="alynt-ag-security-activity">
			<h3><?php esc_html_e( 'Recent Registration Verification Activity', 'alynt-account-gateway' ); ?></h3>
			<p class="description">
				<?php esc_html_e( 'Shows recent Turnstile and Reoon outcomes stored in the plugin verification log. Email addresses are masked in this admin view.', 'alynt-account-gateway' ); ?>
			</p>
			<?php $this->render_admin_data_read_errors( $read_errors ); ?>

			<?php $this->render_security_provider_health_signals( $logs ); ?>
			<?php $this->render_security_manual_review_queue( $logs ); ?>
			<?php $this->render_security_provider_failure_triage( $logs ); ?>
			<?php $this->render_security_rate_limit_pressure( $logs ); ?>
			<?php $this->render_security_registration_abuse_signals( $logs ); ?>
			<?php $this->render_security_diagnostics_dependency_notice( $settings ); ?>
			<?php $this->render_security_access_control_signals( $logs, $diagnostic_events ); ?>
			<?php $this->render_security_auth_redirect_signals( $diagnostic_events ); ?>
			<?php $this->render_security_branded_auth_signals( $diagnostic_events ); ?>
			<?php $this->render_security_registration_flow_signals( $logs ); ?>
			<?php $this->render_security_delivery_signals( $external_events, $webhook_logs ); ?>

			<?php if ( empty( $logs ) ) : ?>
				<p class="alynt-ag-security-status__notice">
					<?php esc_html_e( 'No verification activity has been logged yet.', 'alynt-account-gateway' ); ?>
				</p>
			<?php else : ?>
				<table class="widefat striped alynt-ag-security-activity__table">
					<thead>
						<tr>
							<th scope="col"><?php esc_html_e( 'Email', 'alynt-account-gateway' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Provider', 'alynt-account-gateway' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Outcome', 'alynt-account-gateway' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Decision', 'alynt-account-gateway' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Guidance', 'alynt-account-gateway' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Next Step', 'alynt-account-gateway' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Review', 'alynt-account-gateway' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Logged At', 'alynt-account-gateway' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $logs as $log ) : ?>
							<tr>
								<td><?php echo esc_html( $this->mask_email_for_display( $log->email ?? '' ) ); ?></td>
								<td><?php echo esc_html( $this->security_provider_label( $log->provider ?? '' ) ); ?></td>
								<td><code><?php echo esc_html( $log->status ?? '' ); ?></code></td>
								<td>
									<span class="alynt-ag-security-decision alynt-ag-security-decision--<?php echo ! empty( $log->blocked ) ? 'blocked' : 'passed'; ?>">
										<?php echo ! empty( $log->blocked ) ? esc_html__( 'Blocked', 'alynt-account-gateway' ) : esc_html__( 'Passed', 'alynt-account-gateway' ); ?>
									</span>
								</td>
								<td class="alynt-ag-security-guidance"><?php echo esc_html( $this->security_verification_guidance( $log ) ); ?></td>
								<td class="alynt-ag-security-next-step"><?php echo esc_html( $this->security_verification_next_step( $log ) ); ?></td>
								<td class="alynt-ag-security-review"><?php $this->render_security_review_action( $log ); ?></td>
								<td><?php echo esc_html( $log->created_at ?? '' ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Render notice for security signals that depend on diagnostics logs.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	public function render_security_diagnostics_dependency_notice( $settings ) {
		if ( ! empty( $settings['diagnostics_enabled'] ) ) {
			return;
		}

		?>
		<div class="alynt-ag-security-diagnostics-note" role="note">
			<strong><?php esc_html_e( 'Diagnostics are disabled.', 'alynt-account-gateway' ); ?></strong>
			<?php esc_html_e( 'Access control, gateway routing, welcome-email failure, and webhook-dispatch signals only show complete evidence while diagnostics are enabled in Advanced Tools.', 'alynt-account-gateway' ); ?>
		</div>
		<?php
	}

	/**
	 * Render registration abuse summary from recent verification logs.
	 *
	 * @param array<int,object> $logs Recent verification logs.
	 * @return void
	 */
	public function render_security_registration_abuse_signals( $logs ) {
		$items = $this->security_registration_abuse_signal_items( $logs );
		?>
		<div class="alynt-ag-security-abuse" aria-label="<?php esc_attr_e( 'Recent registration abuse signals', 'alynt-account-gateway' ); ?>">
			<h4><?php esc_html_e( 'Registration Abuse Signals', 'alynt-account-gateway' ); ?></h4>
			<div class="alynt-ag-security-status__grid">
				<?php foreach ( $items as $item ) : ?>
					<section class="alynt-ag-security-card alynt-ag-security-card--<?php echo esc_attr( $item['status'] ); ?>">
						<span class="alynt-ag-security-card__badge"><?php echo esc_html( $this->readiness_status_label( $item['status'] ) ); ?></span>
						<h5><?php echo esc_html( $item['label'] ); ?></h5>
						<p>
							<strong><?php echo esc_html( (string) $item['count'] ); ?></strong>
							<?php echo esc_html( $item['message'] ); ?>
						</p>
					</section>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render access-control summary from recent verification and diagnostics logs.
	 *
	 * @param array<int,object> $logs              Recent verification logs.
	 * @param array<int,object> $diagnostic_events Recent diagnostics events.
	 * @return void
	 */
	public function render_security_access_control_signals( $logs, $diagnostic_events ) {
		$items = $this->security_access_control_signal_items( $logs, $diagnostic_events );
		?>
		<div class="alynt-ag-security-access" aria-label="<?php esc_attr_e( 'Recent access control signals', 'alynt-account-gateway' ); ?>">
			<h4><?php esc_html_e( 'Access Control Signals', 'alynt-account-gateway' ); ?></h4>
			<div class="alynt-ag-security-status__grid">
				<?php foreach ( $items as $item ) : ?>
					<section class="alynt-ag-security-card alynt-ag-security-card--<?php echo esc_attr( $item['status'] ); ?>">
						<span class="alynt-ag-security-card__badge"><?php echo esc_html( $this->readiness_status_label( $item['status'] ) ); ?></span>
						<h5><?php echo esc_html( $item['label'] ); ?></h5>
						<p>
							<strong><?php echo esc_html( (string) $item['count'] ); ?></strong>
							<?php echo esc_html( $item['message'] ); ?>
						</p>
					</section>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}
