<?php
/**
 * Settings page handoff-tools component.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Owns non-secret site handoff guidance and export behavior.
 */
class ALYNT_AG_Settings_Page_Handoff_Tools extends ALYNT_AG_Settings_Page_Component {

	/**
	 * Render handoff tools.
	 *
	 * @param array<string,mixed> $settings Current settings.
	 * @return void
	 */
	public function render_handoff_tools( $settings ) {
		?>
		<h2><?php esc_html_e( 'Site Handoff', 'alynt-account-gateway' ); ?></h2>
		<p class="description">
			<?php esc_html_e( 'Use this non-secret checklist and summary when finishing setup or handing the gateway to a site owner.', 'alynt-account-gateway' ); ?>
		</p>
		<div class="notice notice-info inline">
			<p><strong><?php esc_html_e( 'Recommended setup order', 'alynt-account-gateway' ); ?></strong></p>
			<ol>
				<?php foreach ( $this->handoff_setup_steps() as $step ) : ?>
					<li><?php echo esc_html( $step ); ?></li>
				<?php endforeach; ?>
			</ol>
		</div>
		<table class="widefat striped" aria-label="<?php esc_attr_e( 'Account Gateway handoff checklist', 'alynt-account-gateway' ); ?>">
			<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'Final Check', 'alynt-account-gateway' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Recommended Evidence', 'alynt-account-gateway' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $this->handoff_checklist_items( $settings ) as $item ) : ?>
					<tr>
						<td><strong><?php echo esc_html( $item['label'] ); ?></strong></td>
						<td><?php echo esc_html( $item['evidence'] ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<p>
			<a class="button" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=alynt_ag_export_handoff_summary' ), 'alynt_ag_export_handoff_summary' ) ); ?>">
				<?php esc_html_e( 'Download Handoff Summary', 'alynt-account-gateway' ); ?>
			</a>
		</p>
		<?php
	}

	/**
	 * Export a non-secret handoff summary.
	 *
	 * @return void
	 */
	public function handle_export_handoff_summary() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to export the handoff summary.', 'alynt-account-gateway' ) );
		}

		check_admin_referer( 'alynt_ag_export_handoff_summary' );

		$summary = $this->build_handoff_summary( ALYNT_AG_Settings_Schema::get_settings() );

		nocache_headers();
		header( 'Content-Type: text/plain; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=alynt-account-gateway-handoff-summary.txt' );
		echo $summary; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Plain-text download is composed from labels and non-secret status values.
		exit;
	}

	/**
	 * Build a non-secret handoff summary.
	 *
	 * @param array<string,mixed> $settings Current settings.
	 * @return string
	 */
	public function build_handoff_summary( $settings ) {
		$lines = array(
			'Alynt Account Gateway Handoff Summary',
			'',
			'Site: ' . wp_strip_all_tags( get_bloginfo( 'name' ) ),
			'Home URL: ' . esc_url( home_url( '/' ) ),
			'Plugin Version: ' . ( defined( 'ALYNT_AG_VERSION' ) ? ALYNT_AG_VERSION : '' ),
			'Generated: ' . gmdate( 'c' ),
			'',
			'Current Configuration',
		);

		foreach ( $this->configuration_summary_items( $settings ) as $item ) {
			$lines[] = '- ' . wp_strip_all_tags( $item['label'] ) . ': ' . wp_strip_all_tags( $item['value'] );
		}

		$lines[] = '';
		$lines[] = 'Recommended Final Checks';

		foreach ( $this->handoff_checklist_items( $settings ) as $item ) {
			$lines[] = '- ' . wp_strip_all_tags( $item['label'] ) . ': ' . wp_strip_all_tags( $item['evidence'] );
		}

		$lines[] = '';
		$lines[] = 'Security Note: This summary intentionally excludes API keys, bypass keys, webhook signing secrets, cookies, credentials, and raw setting payloads.';

		return implode( "\n", $lines ) . "\n";
	}

	/**
	 * Return recommended setup steps.
	 *
	 * @return array<int,string>
	 */
	public function handoff_setup_steps() {
		return array(
			__( 'Configure URLs and redirects.', 'alynt-account-gateway' ),
			__( 'Configure branding, screen copy, and previews.', 'alynt-account-gateway' ),
			__( 'Configure registration, security providers, and rate limits.', 'alynt-account-gateway' ),
			__( 'Configure and test emails.', 'alynt-account-gateway' ),
			__( 'Configure dashboard, WooCommerce, webhooks, and privacy retention.', 'alynt-account-gateway' ),
			__( 'Review setup readiness, export settings, store emergency access privately, then enable frontend output.', 'alynt-account-gateway' ),
		);
	}

	/**
	 * Return handoff checklist items.
	 *
	 * @param array<string,mixed> $settings Current settings.
	 * @return array<int,array{label:string,evidence:string}>
	 */
	public function handoff_checklist_items( $settings ) {
		$items = array(
			$this->checklist_item( __( 'Gateway previews reviewed', 'alynt-account-gateway' ), __( 'Open each preview screen from Advanced / Tools and confirm layout, logo, copy, and colors.', 'alynt-account-gateway' ) ),
			$this->checklist_item( __( 'Test email received', 'alynt-account-gateway' ), __( 'Send representative email previews to the configured test recipient and confirm logo sizing, copy, and links.', 'alynt-account-gateway' ) ),
			$this->checklist_item( __( 'Password reset tested', 'alynt-account-gateway' ), __( 'Request a reset, follow the link, set a new password, and confirm branded login works.', 'alynt-account-gateway' ) ),
			$this->checklist_item( __( 'Admin redirects tested', 'alynt-account-gateway' ), __( 'Confirm administrator and shop manager accounts land on their configured redirects.', 'alynt-account-gateway' ) ),
			$this->checklist_item( __( 'Emergency access stored privately', 'alynt-account-gateway' ), __( 'Store the native-login bypass key outside WordPress before enabling frontend output.', 'alynt-account-gateway' ) ),
			$this->checklist_item( __( 'Settings export saved', 'alynt-account-gateway' ), __( 'Download a settings JSON after approval so the final configuration can be restored or compared later.', 'alynt-account-gateway' ) ),
		);

		if ( ! empty( $settings['registration_enabled'] ) ) {
			$items[] = $this->checklist_item( __( 'Registration flow tested', 'alynt-account-gateway' ), __( 'Complete pending registration, confirmation email, set-password, and first login with a disposable user.', 'alynt-account-gateway' ) );
		}

		if ( ! empty( $settings['dashboard_enabled'] ) ) {
			$items[] = $this->checklist_item( __( 'Dashboard tested', 'alynt-account-gateway' ), __( 'Log in as a disposable customer and confirm dashboard navigation, custom links, and logout behavior.', 'alynt-account-gateway' ) );
		}

		if ( ! empty( $settings['woocommerce_takeover'] ) || ! empty( $settings['woocommerce_require_login_checkout'] ) || ! empty( $settings['woocommerce_require_login_order_pay'] ) ) {
			$items[] = $this->checklist_item( __( 'WooCommerce account and checkout paths tested', 'alynt-account-gateway' ), __( 'Smoke orders, account details, addresses, payment methods, checkout login, and order-pay login behavior.', 'alynt-account-gateway' ) );
		}

		if ( ! empty( $settings['account_created_webhook'] ) ) {
			$items[] = $this->checklist_item( __( 'Webhook receiver tested', 'alynt-account-gateway' ), __( 'Use the test webhook tool and confirm the receiver validates any configured signing headers.', 'alynt-account-gateway' ) );
		}

		return $items;
	}

	/**
	 * Create one checklist item.
	 *
	 * @param string $label    Item label.
	 * @param string $evidence Recommended evidence.
	 * @return array{label:string,evidence:string}
	 */
	private function checklist_item( $label, $evidence ) {
		return array(
			'label'    => $label,
			'evidence' => $evidence,
		);
	}
}
