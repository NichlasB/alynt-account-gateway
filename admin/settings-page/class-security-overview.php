<?php
/**
 * Settings page security-overview component.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Owns focused security-overview behavior for the settings page.
 */
class ALYNT_AG_Settings_Page_Security_Overview extends ALYNT_AG_Settings_Page_Component {

	/**
	 * Render security provider and rate-limit status guidance.
	 *
	 * @param array<string,mixed> $settings Current settings.
	 * @return void
	 */
	public function render_security_status_panel( $settings ) {
		$providers       = $this->security_provider_status_items( $settings );
		$launch_items    = $this->security_launch_decision_items( $settings );
		$rate_limits     = $this->security_rate_limit_items( $settings );
		$provider_counts = $this->setup_readiness_counts( $providers );
		?>
		<section class="alynt-ag-security-status" aria-labelledby="alynt-ag-security-status-title">
			<div class="alynt-ag-security-status__header">
				<div>
					<h2 id="alynt-ag-security-status-title"><?php esc_html_e( 'Security And Spam Status', 'alynt-account-gateway' ); ?></h2>
					<p><?php esc_html_e( 'Review configured anti-spam providers, Reoon policy handling, and rate limits before enabling public registration.', 'alynt-account-gateway' ); ?></p>
				</div>
				<div class="alynt-ag-readiness__summary" aria-label="<?php esc_attr_e( 'Security provider summary', 'alynt-account-gateway' ); ?>">
					<span><strong><?php echo esc_html( (string) $provider_counts['action'] ); ?></strong> <?php esc_html_e( 'Action Needed', 'alynt-account-gateway' ); ?></span>
					<span><strong><?php echo esc_html( (string) $provider_counts['warning'] ); ?></strong> <?php esc_html_e( 'Review', 'alynt-account-gateway' ); ?></span>
					<span><strong><?php echo esc_html( (string) $provider_counts['ready'] ); ?></strong> <?php esc_html_e( 'Ready', 'alynt-account-gateway' ); ?></span>
				</div>
			</div>

			<?php if ( 0 === $this->security_configured_provider_count( $settings ) ) : ?>
				<p class="alynt-ag-security-status__notice">
					<?php esc_html_e( 'No anti-spam provider is fully configured. Keep registration disabled or configure Turnstile or Reoon before going public.', 'alynt-account-gateway' ); ?>
				</p>
			<?php endif; ?>

			<div class="alynt-ag-security-launch" aria-label="<?php esc_attr_e( 'Security launch decision summary', 'alynt-account-gateway' ); ?>">
				<h3><?php esc_html_e( 'Launch Decision Summary', 'alynt-account-gateway' ); ?></h3>
				<p class="description"><?php esc_html_e( 'Use this quick checklist before making public registration available. It summarizes configuration choices that affect spam resistance, customer support, and launch evidence.', 'alynt-account-gateway' ); ?></p>
				<div class="alynt-ag-security-status__grid">
					<?php foreach ( $launch_items as $item ) : ?>
						<section class="alynt-ag-security-card alynt-ag-security-card--<?php echo esc_attr( $item['status'] ); ?>">
							<span class="alynt-ag-security-card__badge"><?php echo esc_html( $this->readiness_status_label( $item['status'] ) ); ?></span>
							<h4><?php echo esc_html( $item['label'] ); ?></h4>
							<p><?php echo esc_html( $item['message'] ); ?></p>
						</section>
					<?php endforeach; ?>
				</div>
			</div>

			<h3><?php esc_html_e( 'Provider Readiness', 'alynt-account-gateway' ); ?></h3>
			<div class="alynt-ag-security-status__grid">
				<?php foreach ( $providers as $item ) : ?>
					<section class="alynt-ag-security-card alynt-ag-security-card--<?php echo esc_attr( $item['status'] ); ?>">
						<span class="alynt-ag-security-card__badge"><?php echo esc_html( $this->readiness_status_label( $item['status'] ) ); ?></span>
						<h4><?php echo esc_html( $item['label'] ); ?></h4>
						<p><?php echo esc_html( $item['message'] ); ?></p>
					</section>
				<?php endforeach; ?>
			</div>

			<?php $this->render_security_provider_checks( $settings ); ?>
			<?php $this->render_security_reoon_policy_guide( $settings ); ?>

			<h3><?php esc_html_e( 'Rate Limit Posture', 'alynt-account-gateway' ); ?></h3>
			<div class="alynt-ag-security-status__grid">
				<?php foreach ( $rate_limits as $item ) : ?>
					<section class="alynt-ag-security-card alynt-ag-security-card--<?php echo esc_attr( $item['status'] ); ?>">
						<span class="alynt-ag-security-card__badge"><?php echo esc_html( $this->readiness_status_label( $item['status'] ) ); ?></span>
						<h4><?php echo esc_html( $item['label'] ); ?></h4>
						<p><?php echo esc_html( $item['message'] ); ?></p>
					</section>
				<?php endforeach; ?>
			</div>

			<?php $this->render_security_verification_activity(); ?>
			<?php $this->render_security_pending_registrations(); ?>
		</section>
		<?php
	}

	/**
	 * Return security launch decision items.
	 *
	 * @param array<string,mixed> $settings Current settings.
	 * @return array<int,array{label:string,status:string,message:string}>
	 */
	public function security_launch_decision_items( $settings ) {
		$registration_enabled = ! empty( $settings['registration_enabled'] );
		$provider_count       = $this->security_configured_provider_count( $settings );
		$has_terms            = ! empty( $settings['terms_path'] );
		$has_privacy          = ! empty( $settings['privacy_path'] );
		$has_reoon            = ! empty( $settings['reoon_api_key'] );
		$flagged_policy       = ! empty( $settings['reoon_flagged_policy'] ) ? sanitize_key( $settings['reoon_flagged_policy'] ) : 'allow';
		$diagnostics_enabled  = ! empty( $settings['diagnostics_enabled'] );

		return array(
			array(
				'label'   => __( 'Public Registration', 'alynt-account-gateway' ),
				'status'  => $registration_enabled ? 'ready' : 'action',
				'message' => $registration_enabled
					? __( 'Public account creation is enabled. Confirm the remaining checks before sending customers to registration.', 'alynt-account-gateway' )
					: __( 'Public account creation is disabled. Keep it disabled while configuring the gateway, then enable it only after provider and email checks are ready.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Anti-Spam Coverage', 'alynt-account-gateway' ),
				'status'  => $provider_count > 0 ? 'ready' : 'action',
				'message' => $provider_count > 0
					? __( 'At least one anti-spam provider is fully configured for registration verification.', 'alynt-account-gateway' )
					: __( 'No fully configured anti-spam provider is available. Configure Turnstile or Reoon before public registration receives traffic.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Consent Links', 'alynt-account-gateway' ),
				'status'  => $has_terms && $has_privacy ? 'ready' : 'action',
				'message' => $has_terms && $has_privacy
					? __( 'Terms and privacy links are configured for the registration consent checkbox.', 'alynt-account-gateway' )
					: __( 'Configure both Terms and Privacy relative URL paths before public registration.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Flagged Email Policy', 'alynt-account-gateway' ),
				'status'  => $has_reoon && 'block' === $flagged_policy ? 'ready' : 'warning',
				'message' => $has_reoon
					? $this->security_reoon_flagged_policy_message( $flagged_policy )
					: __( 'Reoon is not configured, so flagged email policy decisions are inactive. Use Turnstile alone or add Reoon before relying on email-quality review.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Launch Evidence', 'alynt-account-gateway' ),
				'status'  => $diagnostics_enabled ? 'ready' : 'warning',
				'message' => $diagnostics_enabled
					? __( 'Diagnostics are enabled, so launch and support signals can be collected during registration rollout.', 'alynt-account-gateway' )
					: __( 'Diagnostics are disabled. Enable them temporarily during launch or support windows if you need fuller evidence for redirects, emails, and webhook outcomes.', 'alynt-account-gateway' ),
			),
		);
	}

	/**
	 * Return security provider status items.
	 *
	 * @param array<string,mixed> $settings Current settings.
	 * @return array<int,array{label:string,status:string,message:string}>
	 */
	public function security_provider_status_items( $settings ) {
		$has_turnstile_site   = ! empty( $settings['turnstile_site_key'] );
		$has_turnstile_secret = ! empty( $settings['turnstile_secret_key'] );
		$has_turnstile        = $has_turnstile_site && $has_turnstile_secret;
		$has_reoon            = ! empty( $settings['reoon_api_key'] );

		$turnstile_status  = $has_turnstile ? 'ready' : 'action';
		$turnstile_message = __( 'Turnstile is not configured. Add both keys or use Reoon before enabling public registration.', 'alynt-account-gateway' );

		if ( $has_turnstile ) {
			$turnstile_message = __( 'Server-side verification can run when the registration form sends a Turnstile token.', 'alynt-account-gateway' );
		} elseif ( $has_turnstile_site || $has_turnstile_secret ) {
			$turnstile_status  = 'warning';
			$turnstile_message = __( 'Turnstile is partially configured. Add both the site key and secret key before relying on it.', 'alynt-account-gateway' );
		}

		$mode                 = ! empty( $settings['protection_mode'] ) ? sanitize_key( $settings['protection_mode'] ) : 'turnstile_or_reoon';
		$reoon_flagged_policy = ! empty( $settings['reoon_flagged_policy'] ) ? sanitize_key( $settings['reoon_flagged_policy'] ) : 'allow';

		return array(
			array(
				'label'   => __( 'Protection Mode', 'alynt-account-gateway' ),
				'status'  => $this->security_configured_provider_count( $settings ) > 0 ? 'ready' : 'warning',
				'message' => $this->security_protection_mode_message( $mode ),
			),
			array(
				'label'   => __( 'Turnstile', 'alynt-account-gateway' ),
				'status'  => $turnstile_status,
				'message' => $turnstile_message,
			),
			array(
				'label'   => __( 'Reoon Email Verifier', 'alynt-account-gateway' ),
				'status'  => $has_reoon ? 'ready' : 'action',
				'message' => $has_reoon
					? __( 'Email quality verification can run using the configured Reoon API key.', 'alynt-account-gateway' )
					: __( 'Reoon is not configured. Add an API key or use Turnstile before enabling public registration.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Reoon Blocked Statuses', 'alynt-account-gateway' ),
				'status'  => 'ready',
				'message' => __( 'Always blocks invalid, disabled, disposable, and spamtrap statuses.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Reoon Flagged Statuses', 'alynt-account-gateway' ),
				'status'  => 'block' === $reoon_flagged_policy ? 'ready' : 'warning',
				'message' => $this->security_reoon_flagged_policy_message( $reoon_flagged_policy ),
			),
		);
	}

	/**
	 * Render safe provider connection checks using saved settings.
	 *
	 * @param array<string,mixed> $settings Current settings.
	 * @return void
	 */
	public function render_security_provider_checks( $settings ) {
		$turnstile_ready = ! empty( $settings['turnstile_site_key'] ) && ! empty( $settings['turnstile_secret_key'] );
		$reoon_ready     = ! empty( $settings['reoon_api_key'] );
		?>
		<div class="alynt-ag-provider-checks" aria-labelledby="alynt-ag-provider-checks-title">
			<div>
				<h3 id="alynt-ag-provider-checks-title"><?php esc_html_e( 'Provider Connection Checks', 'alynt-account-gateway' ); ?></h3>
				<p class="description"><?php esc_html_e( 'These checks use saved credentials and return only a fixed readiness result. Credentials, provider payloads, and customer data are not displayed or stored.', 'alynt-account-gateway' ); ?></p>
			</div>
			<div class="alynt-ag-provider-checks__grid">
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="alynt-ag-provider-check">
					<input type="hidden" name="action" value="alynt_ag_test_security_provider">
					<input type="hidden" name="provider" value="turnstile">
					<?php wp_nonce_field( 'alynt_ag_test_security_provider_turnstile' ); ?>
					<h4><?php esc_html_e( 'Cloudflare Turnstile', 'alynt-account-gateway' ); ?></h4>
					<p id="alynt-ag-turnstile-check-help"><?php esc_html_e( 'Checks outbound Siteverify connectivity and whether Cloudflare accepts the saved secret far enough to reject a fixed invalid token. A real registration challenge is still required to prove the complete widget and hostname flow.', 'alynt-account-gateway' ); ?></p>
					<button type="submit" class="button button-secondary" aria-describedby="alynt-ag-turnstile-check-help" <?php disabled( $turnstile_ready, false ); ?>>
						<?php esc_html_e( 'Check Turnstile Connection', 'alynt-account-gateway' ); ?>
					</button>
				</form>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="alynt-ag-provider-check">
					<input type="hidden" name="action" value="alynt_ag_test_security_provider">
					<input type="hidden" name="provider" value="reoon">
					<?php wp_nonce_field( 'alynt_ag_test_security_provider_reoon' ); ?>
					<h4><?php esc_html_e( 'Reoon Email Verifier', 'alynt-account-gateway' ); ?></h4>
					<p id="alynt-ag-reoon-check-help"><?php esc_html_e( 'Checks the saved API key against Reoon account status. It does not submit an email address or run a customer verification.', 'alynt-account-gateway' ); ?></p>
					<button type="submit" class="button button-secondary" aria-describedby="alynt-ag-reoon-check-help" <?php disabled( $reoon_ready, false ); ?>>
						<?php esc_html_e( 'Check Reoon Account', 'alynt-account-gateway' ); ?>
					</button>
				</form>
			</div>
		</div>
		<?php
	}

	/**
	 * Map a provider check result to a fixed, non-sensitive notice key.
	 *
	 * @param string              $provider Provider key.
	 * @param true|array|WP_Error $result   Provider check result.
	 * @return string
	 */
	public function security_provider_check_notice_key( $provider, $result ) {
		$provider = sanitize_key( $provider );
		if ( ! is_wp_error( $result ) ) {
			return $provider . '_check_ready';
		}

		$error_code = sanitize_key( $result->get_error_code() );
		$maps       = array(
			'turnstile' => array(
				'alynt_ag_turnstile_missing'          => 'turnstile_check_missing',
				'alynt_ag_turnstile_invalid_secret'   => 'turnstile_check_invalid_secret',
				'alynt_ag_turnstile_request_failed'   => 'turnstile_check_request_failed',
				'alynt_ag_turnstile_invalid_response' => 'turnstile_check_invalid_response',
			),
			'reoon'     => array(
				'alynt_ag_reoon_missing'          => 'reoon_check_missing',
				'alynt_ag_reoon_account_inactive' => 'reoon_check_inactive',
				'alynt_ag_reoon_request_failed'   => 'reoon_check_request_failed',
				'alynt_ag_reoon_invalid_response' => 'reoon_check_invalid_response',
			),
		);

		return isset( $maps[ $provider ][ $error_code ] )
			? $maps[ $provider ][ $error_code ]
			: $provider . '_check_invalid_response';
	}
}
