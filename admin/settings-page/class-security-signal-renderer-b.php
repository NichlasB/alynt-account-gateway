<?php
/**
 * Settings page security-signal-renderer-b component.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Owns focused security-signal-renderer-b behavior for the settings page.
 */
class ALYNT_AG_Settings_Page_Security_Signal_Renderer_B extends ALYNT_AG_Settings_Page_Component {

	/**
	 * Render auth redirect summary from recent diagnostics logs.
	 *
	 * @param array<int,object> $diagnostic_events Recent diagnostics events.
	 * @return void
	 */
	public function render_security_auth_redirect_signals( $diagnostic_events ) {
		$items = $this->security_auth_redirect_signal_items( $diagnostic_events );
		?>
		<div class="alynt-ag-security-routing" aria-label="<?php esc_attr_e( 'Recent gateway routing signals', 'alynt-account-gateway' ); ?>">
			<h4><?php esc_html_e( 'Gateway Routing Signals', 'alynt-account-gateway' ); ?></h4>
			<div class="alynt-ag-security-status__grid">
				<?php $this->render_security_signal_cards( $items ); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render branded authentication summary from recent diagnostics logs.
	 *
	 * @param array<int,object> $diagnostic_events Recent diagnostics events.
	 * @return void
	 */
	public function render_security_branded_auth_signals( $diagnostic_events ) {
		$items = $this->security_branded_auth_signal_items( $diagnostic_events );
		?>
		<div class="alynt-ag-security-auth" aria-label="<?php esc_attr_e( 'Recent branded authentication signals', 'alynt-account-gateway' ); ?>">
			<h4><?php esc_html_e( 'Gateway Auth Signals', 'alynt-account-gateway' ); ?></h4>
			<div class="alynt-ag-security-status__grid">
				<?php $this->render_security_signal_cards( $items ); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render registration flow summary from recent verification logs.
	 *
	 * @param array<int,object> $logs Recent verification logs.
	 * @return void
	 */
	public function render_security_registration_flow_signals( $logs ) {
		$items = $this->security_registration_flow_signal_items( $logs );
		?>
		<div class="alynt-ag-security-flow" aria-label="<?php esc_attr_e( 'Recent registration flow signals', 'alynt-account-gateway' ); ?>">
			<h4><?php esc_html_e( 'Registration Flow Signals', 'alynt-account-gateway' ); ?></h4>
			<div class="alynt-ag-security-status__grid">
				<?php $this->render_security_signal_cards( $items ); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render account delivery summary from recent diagnostics and webhook logs.
	 *
	 * @param array<int,object> $external_events Recent external diagnostics events.
	 * @param array<int,object> $webhook_logs    Recent webhook logs.
	 * @return void
	 */
	public function render_security_delivery_signals( $external_events, $webhook_logs ) {
		$items = $this->security_delivery_signal_items( $external_events, $webhook_logs );
		?>
		<div class="alynt-ag-security-delivery" aria-label="<?php esc_attr_e( 'Recent account delivery signals', 'alynt-account-gateway' ); ?>">
			<h4><?php esc_html_e( 'Account Delivery Signals', 'alynt-account-gateway' ); ?></h4>
			<div class="alynt-ag-security-status__grid">
				<?php $this->render_security_signal_cards( $items ); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render provider health summary from recent verification logs.
	 *
	 * @param array<int,object> $logs Recent verification logs.
	 * @return void
	 */
	public function render_security_provider_health_signals( $logs ) {
		$items = $this->security_provider_health_signal_items( $logs );
		?>
		<div class="alynt-ag-security-signal" aria-label="<?php esc_attr_e( 'Recent provider health signals', 'alynt-account-gateway' ); ?>">
			<h4><?php esc_html_e( 'Provider Health Signals', 'alynt-account-gateway' ); ?></h4>
			<div class="alynt-ag-security-status__grid">
				<?php $this->render_security_signal_cards( $items ); ?>
			</div>
		</div>
		<?php
	}
}
