<?php
/**
 * Settings page configuration-summary component.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Owns non-secret configuration summary behavior for the settings page.
 */
class ALYNT_AG_Settings_Page_Configuration_Summary extends ALYNT_AG_Settings_Page_Component {

	/**
	 * Render the current configuration summary.
	 *
	 * @param array<string,mixed> $settings Current settings.
	 * @return void
	 */
	public function render_configuration_summary_panel( $settings ) {
		$items = $this->configuration_summary_items( $settings );
		?>
		<section class="alynt-ag-readiness" aria-labelledby="alynt-ag-configuration-summary-title">
			<div class="alynt-ag-readiness__header">
				<div>
					<h2 id="alynt-ag-configuration-summary-title"><?php esc_html_e( 'Current Configuration Summary', 'alynt-account-gateway' ); ?></h2>
					<p><?php esc_html_e( 'Review the non-secret setup state before launch, support, or site handoff.', 'alynt-account-gateway' ); ?></p>
				</div>
			</div>
			<table class="widefat striped" aria-label="<?php esc_attr_e( 'Current non-secret Account Gateway configuration', 'alynt-account-gateway' ); ?>">
				<thead>
					<tr>
						<th scope="col"><?php esc_html_e( 'Area', 'alynt-account-gateway' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Current State', 'alynt-account-gateway' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Review', 'alynt-account-gateway' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $items as $item ) : ?>
						<tr>
							<td><strong><?php echo esc_html( $item['label'] ); ?></strong></td>
							<td><?php echo esc_html( $item['value'] ); ?></td>
							<td><a href="<?php echo esc_url( $this->settings_tab_url( $item['tab'] ) ); ?>"><?php esc_html_e( 'Open Setting', 'alynt-account-gateway' ); ?></a></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</section>
		<?php
	}

	/**
	 * Return non-secret configuration summary items.
	 *
	 * @param array<string,mixed> $settings Current settings.
	 * @return array<int,array{label:string,value:string,tab:string}>
	 */
	public function configuration_summary_items( $settings ) {
		return array(
			$this->summary_item( __( 'Frontend Output', 'alynt-account-gateway' ), $this->enabled_label( ! empty( $settings['frontend_enabled'] ) ), 'general' ),
			$this->summary_item( __( 'Login URL Path', 'alynt-account-gateway' ), $this->string_or_missing( $settings['login_path'] ?? '' ), 'urls' ),
			$this->summary_item( __( 'Account Action Base', 'alynt-account-gateway' ), $this->string_or_missing( $settings['account_action_base'] ?? '' ), 'urls' ),
			$this->summary_item( __( 'After Login Redirect', 'alynt-account-gateway' ), $this->string_or_missing( $settings['after_login_redirect'] ?? '' ), 'urls' ),
			$this->summary_item( __( 'Administrator Redirect', 'alynt-account-gateway' ), $this->string_or_missing( $settings['administrator_after_login_redirect'] ?? '' ), 'urls' ),
			$this->summary_item( __( 'Shop Manager Redirect', 'alynt-account-gateway' ), $this->string_or_missing( $settings['shop_manager_after_login_redirect'] ?? '' ), 'urls' ),
			$this->summary_item( __( 'Public Registration', 'alynt-account-gateway' ), $this->enabled_label( ! empty( $settings['registration_enabled'] ) ), 'registration' ),
			$this->summary_item( __( 'Terms And Privacy Paths', 'alynt-account-gateway' ), $this->configured_label( ! empty( $settings['terms_path'] ) && ! empty( $settings['privacy_path'] ) ), 'registration' ),
			$this->summary_item( __( 'Custom Dashboard', 'alynt-account-gateway' ), $this->enabled_label( ! empty( $settings['dashboard_enabled'] ) ), 'dashboard' ),
			$this->summary_item( __( 'WooCommerce Takeover', 'alynt-account-gateway' ), $this->enabled_label( ! empty( $settings['woocommerce_takeover'] ) ), 'woocommerce' ),
			$this->summary_item( __( 'Checkout Login Gate', 'alynt-account-gateway' ), $this->enabled_label( ! empty( $settings['woocommerce_require_login_checkout'] ) ), 'woocommerce' ),
			$this->summary_item( __( 'Order-Pay Login Gate', 'alynt-account-gateway' ), $this->enabled_label( ! empty( $settings['woocommerce_require_login_order_pay'] ) ), 'woocommerce' ),
			$this->summary_item( __( 'Turnstile', 'alynt-account-gateway' ), $this->configured_label( ! empty( $settings['turnstile_site_key'] ) && ! empty( $settings['turnstile_secret_key'] ) ), 'security' ),
			$this->summary_item( __( 'Reoon', 'alynt-account-gateway' ), $this->configured_label( ! empty( $settings['reoon_api_key'] ) ), 'security' ),
			$this->summary_item( __( 'Account-Created Webhook', 'alynt-account-gateway' ), $this->configured_label( ! empty( $settings['account_created_webhook'] ) ), 'webhooks' ),
			$this->summary_item( __( 'Webhook Signing', 'alynt-account-gateway' ), $this->configured_label( ! empty( $settings['webhook_signing_secret'] ) ), 'webhooks' ),
			$this->summary_item( __( 'Email Test Recipient', 'alynt-account-gateway' ), $this->configured_label( ! empty( $settings['email_test_recipient'] ) ), 'emails' ),
			$this->summary_item( __( 'Brand Logo', 'alynt-account-gateway' ), $this->configured_label( ! empty( $settings['brand_logo_id'] ) ), 'branding' ),
			$this->summary_item( __( 'Gateway Background Image', 'alynt-account-gateway' ), $this->configured_label( ! empty( $settings['background_image_id'] ) ), 'branding' ),
			$this->summary_item( __( 'Emergency Bypass Key', 'alynt-account-gateway' ), $this->configured_label( ! empty( $settings['emergency_bypass_key'] ) ), 'advanced_tools' ),
		);
	}

	/**
	 * Create one summary item.
	 *
	 * @param string $label Item label.
	 * @param string $value Item value.
	 * @param string $tab   Review tab.
	 * @return array{label:string,value:string,tab:string}
	 */
	private function summary_item( $label, $value, $tab ) {
		return array(
			'label' => $label,
			'value' => $value,
			'tab'   => $tab,
		);
	}

	/**
	 * Return an enabled or disabled label.
	 *
	 * @param bool $enabled Whether enabled.
	 * @return string
	 */
	private function enabled_label( $enabled ) {
		return $enabled ? __( 'Enabled', 'alynt-account-gateway' ) : __( 'Disabled', 'alynt-account-gateway' );
	}

	/**
	 * Return a configured or not configured label.
	 *
	 * @param bool $configured Whether configured.
	 * @return string
	 */
	private function configured_label( $configured ) {
		return $configured ? __( 'Configured', 'alynt-account-gateway' ) : __( 'Not configured', 'alynt-account-gateway' );
	}

	/**
	 * Return a non-empty scalar value or a missing label.
	 *
	 * @param mixed $value Candidate value.
	 * @return string
	 */
	private function string_or_missing( $value ) {
		$value = is_scalar( $value ) ? trim( (string) $value ) : '';

		return '' !== $value ? $value : __( 'Not configured', 'alynt-account-gateway' );
	}
}
