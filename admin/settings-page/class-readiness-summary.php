<?php
/**
 * Settings page readiness-summary component.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Owns focused readiness-summary behavior for the settings page.
 */
class ALYNT_AG_Settings_Page_Readiness_Summary extends ALYNT_AG_Settings_Page_Component {

	/**
	 * Render setup readiness checks.
	 *
	 * @param array<string,mixed> $settings Current settings.
	 * @return void
	 */
	public function render_setup_readiness_panel( $settings ) {
		$checks = $this->setup_readiness_checks( $settings );
		$counts = $this->setup_readiness_counts( $checks );
		?>
		<section class="alynt-ag-readiness" aria-labelledby="alynt-ag-readiness-title">
			<div class="alynt-ag-readiness__header">
				<div>
					<h2 id="alynt-ag-readiness-title"><?php esc_html_e( 'Setup Readiness', 'alynt-account-gateway' ); ?></h2>
					<p><?php esc_html_e( 'Review these checks before enabling public account gateway output.', 'alynt-account-gateway' ); ?></p>
				</div>
				<div class="alynt-ag-readiness__summary" aria-label="<?php esc_attr_e( 'Setup readiness summary', 'alynt-account-gateway' ); ?>">
					<span><strong><?php echo esc_html( (string) $counts['action'] ); ?></strong> <?php esc_html_e( 'Action Needed', 'alynt-account-gateway' ); ?></span>
					<span><strong><?php echo esc_html( (string) $counts['warning'] ); ?></strong> <?php esc_html_e( 'Review', 'alynt-account-gateway' ); ?></span>
					<span><strong><?php echo esc_html( (string) $counts['ready'] ); ?></strong> <?php esc_html_e( 'Ready', 'alynt-account-gateway' ); ?></span>
				</div>
			</div>
			<ul class="alynt-ag-readiness__list">
				<?php foreach ( $checks as $check ) : ?>
					<li class="alynt-ag-readiness__item alynt-ag-readiness__item--<?php echo esc_attr( $check['status'] ); ?>">
						<span class="alynt-ag-readiness__badge"><?php echo esc_html( $this->readiness_status_label( $check['status'] ) ); ?></span>
						<div>
							<strong><?php echo esc_html( $check['label'] ); ?></strong>
							<p><?php echo esc_html( $check['message'] ); ?></p>
							<?php if ( ! empty( $check['tab'] ) ) : ?>
								<a href="<?php echo esc_url( $this->settings_tab_url( $check['tab'] ) ); ?>"><?php esc_html_e( 'Open Setting', 'alynt-account-gateway' ); ?></a>
							<?php endif; ?>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		</section>
		<?php
	}

	/**
	 * Return setup readiness checks.
	 *
	 * @param array<string,mixed> $settings Current settings.
	 * @return array<int,array{label:string,status:string,message:string,tab:string}>
	 */
	public function setup_readiness_checks( $settings ) {
		$has_login_path       = ! empty( $settings['login_path'] );
		$has_action_base      = ! empty( $settings['account_action_base'] );
		$has_after_login      = ! empty( $settings['after_login_redirect'] );
		$registration_enabled = ! empty( $settings['registration_enabled'] );
		$has_turnstile        = ! empty( $settings['turnstile_site_key'] ) && ! empty( $settings['turnstile_secret_key'] );
		$has_reoon            = ! empty( $settings['reoon_api_key'] );
		$dashboard_enabled    = ! empty( $settings['dashboard_enabled'] );
		$woocommerce_takeover = ! empty( $settings['woocommerce_takeover'] );
		$webhook_enabled      = ! empty( $settings['account_created_webhook'] );

		$checks = array();

		$checks[] = array(
			'label'   => __( 'Frontend Output', 'alynt-account-gateway' ),
			'status'  => ! empty( $settings['frontend_enabled'] ) ? 'ready' : 'warning',
			'message' => ! empty( $settings['frontend_enabled'] )
				? __( 'Frontend output is enabled. Keep the remaining checks ready before sending users to the gateway.', 'alynt-account-gateway' )
				: __( 'Frontend output is disabled, which is safest while setup is in progress.', 'alynt-account-gateway' ),
			'tab'     => 'general',
		);

		$checks[] = array(
			'label'   => __( 'Gateway URLs', 'alynt-account-gateway' ),
			'status'  => $has_login_path && $has_action_base && $has_after_login ? 'ready' : 'action',
			'message' => $has_login_path && $has_action_base && $has_after_login
				? __( 'Login, account action, and after-login paths are configured.', 'alynt-account-gateway' )
				: __( 'Set the login path, account action base, and after-login redirect before enabling frontend output.', 'alynt-account-gateway' ),
			'tab'     => 'urls',
		);

		$checks[] = array(
			'label'   => __( 'Emergency Access', 'alynt-account-gateway' ),
			'status'  => ! empty( $settings['emergency_bypass_key'] ) ? 'ready' : 'action',
			'message' => ! empty( $settings['emergency_bypass_key'] )
				? __( 'An emergency bypass key exists for restoring access to the native login screen.', 'alynt-account-gateway' )
				: __( 'Generate and save an emergency bypass key before replacing public login screens.', 'alynt-account-gateway' ),
			'tab'     => 'advanced_tools',
		);

		$checks[] = array(
			'label'   => __( 'Branding', 'alynt-account-gateway' ),
			'status'  => ! empty( $settings['brand_logo_id'] ) ? 'ready' : 'warning',
			'message' => ! empty( $settings['brand_logo_id'] )
				? __( 'A brand logo is configured for gateway screens and the dashboard.', 'alynt-account-gateway' )
				: __( 'No brand logo is configured yet. The gateway can still run, but branded output will feel less complete.', 'alynt-account-gateway' ),
			'tab'     => 'branding',
		);

		$checks[] = $this->registration_readiness_check( $registration_enabled, $has_turnstile, $has_reoon, $settings );

		$checks[] = array(
			'label'   => __( 'Email Testing', 'alynt-account-gateway' ),
			'status'  => ! empty( $settings['email_test_recipient'] ) ? 'ready' : 'warning',
			'message' => ! empty( $settings['email_test_recipient'] )
				? __( 'A test recipient is configured for email preview and test-send checks.', 'alynt-account-gateway' )
				: __( 'Add a test recipient and send representative account emails before inviting users.', 'alynt-account-gateway' ),
			'tab'     => 'emails',
		);

		$checks[] = array(
			'label'   => __( 'Dashboard', 'alynt-account-gateway' ),
			'status'  => $dashboard_enabled ? 'ready' : 'warning',
			'message' => $dashboard_enabled
				? __( 'The branded account dashboard is enabled.', 'alynt-account-gateway' )
				: __( 'The branded account dashboard is disabled. Users may be redirected to the configured account destination without the custom dashboard.', 'alynt-account-gateway' ),
			'tab'     => 'dashboard',
		);

		$checks[] = $this->woocommerce_readiness_check( $dashboard_enabled, $woocommerce_takeover );

		$checks[] = array(
			'label'   => __( 'Webhook Signing', 'alynt-account-gateway' ),
			'status'  => ! $webhook_enabled || ! empty( $settings['webhook_signing_secret'] ) ? 'ready' : 'warning',
			'message' => $this->webhook_signing_readiness_message( $webhook_enabled, ! empty( $settings['webhook_signing_secret'] ) ),
			'tab'     => 'webhooks',
		);

		$checks[] = array(
			'label'   => __( 'Privacy Retention', 'alynt-account-gateway' ),
			'status'  => $this->privacy_retention_ready( $settings ) ? 'ready' : 'action',
			'message' => $this->privacy_retention_ready( $settings )
				? __( 'Plugin-owned privacy, verification, webhook, consent, and audit retention windows are configured.', 'alynt-account-gateway' )
				: __( 'Set retention windows above zero so plugin-owned logs and records can be cleaned up predictably.', 'alynt-account-gateway' ),
			'tab'     => 'privacy',
		);

		return $checks;
	}

	/**
	 * Count readiness check statuses.
	 *
	 * @param array<int,array{status:string}> $checks Readiness checks.
	 * @return array{action:int,warning:int,ready:int}
	 */
	public function setup_readiness_counts( $checks ) {
		$counts = array(
			'action'  => 0,
			'warning' => 0,
			'ready'   => 0,
		);

		foreach ( $checks as $check ) {
			if ( isset( $counts[ $check['status'] ] ) ) {
				++$counts[ $check['status'] ];
			}
		}

		return $counts;
	}

	/**
	 * Return a readiness status label.
	 *
	 * @param string $status Status key.
	 * @return string
	 */
	public function readiness_status_label( $status ) {
		if ( 'action' === $status ) {
			return __( 'Action Needed', 'alynt-account-gateway' );
		}

		if ( 'warning' === $status ) {
			return __( 'Review', 'alynt-account-gateway' );
		}

		return __( 'Ready', 'alynt-account-gateway' );
	}

	/**
	 * Build an admin URL to a settings tab.
	 *
	 * @param string $tab Tab key.
	 * @return string
	 */
	public function settings_tab_url( $tab ) {
		return add_query_arg(
			array(
				'page' => 'alynt-account-gateway',
				'tab'  => sanitize_key( $tab ),
			),
			admin_url( 'options-general.php' )
		);
	}
}
