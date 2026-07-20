<?php
/**
 * Settings page guidance component.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Owns focused guidance behavior for the settings page.
 */
class ALYNT_AG_Settings_Page_Guidance extends ALYNT_AG_Settings_Page_Component {

	/**
	 * Render tab-level setup guidance.
	 *
	 * @param string $active_tab Active settings tab.
	 * @return void
	 */
	public function render_tab_guidance( $active_tab ) {
		$guidance = $this->settings_tab_guidance();
		$tab      = isset( $guidance[ $active_tab ] ) ? $active_tab : 'general';
		$item     = $guidance[ $tab ];
		?>
		<section class="alynt-ag-tab-guidance" aria-labelledby="alynt-ag-tab-guidance-title">
			<div class="alynt-ag-tab-guidance__copy">
				<p class="alynt-ag-tab-guidance__eyebrow"><?php esc_html_e( 'Tab Guidance', 'alynt-account-gateway' ); ?></p>
				<h2 id="alynt-ag-tab-guidance-title"><?php echo esc_html( $item['title'] ); ?></h2>
				<p><?php echo esc_html( $item['description'] ); ?></p>
			</div>
			<ul class="alynt-ag-tab-guidance__steps">
				<?php foreach ( $item['steps'] as $step ) : ?>
					<li><?php echo esc_html( $step ); ?></li>
				<?php endforeach; ?>
			</ul>
			<?php if ( ! empty( $item['related_tab'] ) && ! empty( $item['related_label'] ) ) : ?>
				<a class="button button-secondary" href="<?php echo esc_url( $this->settings_tab_url( $item['related_tab'] ) ); ?>">
					<?php echo esc_html( $item['related_label'] ); ?>
				</a>
			<?php endif; ?>
		</section>
		<?php
	}

	/**
	 * Return tab-level setup guidance.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	public function settings_tab_guidance() {
		return array(
			'general'        => array(
				'title'         => __( 'Start safely before changing public account screens.', 'alynt-account-gateway' ),
				'description'   => __( 'Keep frontend output disabled while you configure and preview the gateway. Use readiness checks here as the launch gate.', 'alynt-account-gateway' ),
				'steps'         => array(
					__( 'Confirm URLs, branding, registration, email, dashboard, and privacy settings before enabling public output.', 'alynt-account-gateway' ),
					__( 'Use previews and test sends before sending real users to the gateway.', 'alynt-account-gateway' ),
					__( 'Save the emergency bypass key somewhere private before replacing public login screens.', 'alynt-account-gateway' ),
				),
				'related_tab'   => 'urls',
				'related_label' => __( 'Review URLs', 'alynt-account-gateway' ),
			),
			'urls'           => array(
				'title'         => __( 'Set the public account paths first.', 'alynt-account-gateway' ),
				'description'   => __( 'These paths decide where login, account actions, and post-login redirects send users.', 'alynt-account-gateway' ),
				'steps'         => array(
					__( 'Keep the login path separate from the account action base when you want clean login URLs.', 'alynt-account-gateway' ),
					__( 'Use relative paths so settings can move cleanly between staging and production domains.', 'alynt-account-gateway' ),
					__( 'Confirm the after-login redirect matches the dashboard or WooCommerce account destination.', 'alynt-account-gateway' ),
				),
				'related_tab'   => 'dashboard',
				'related_label' => __( 'Review Dashboard', 'alynt-account-gateway' ),
			),
			'branding'       => array(
				'title'         => __( 'Make the default gateway feel site-owned.', 'alynt-account-gateway' ),
				'description'   => __( 'Logo, color, layout, and font settings control the front-facing gateway shell and account dashboard.', 'alynt-account-gateway' ),
				'steps'         => array(
					__( 'Upload a brand logo and set a max width that works on mobile and desktop.', 'alynt-account-gateway' ),
					__( 'Check button background and text colors together for readable contrast.', 'alynt-account-gateway' ),
					__( 'Use one global background image that scales well in a two-column layout.', 'alynt-account-gateway' ),
				),
				'related_tab'   => 'copy',
				'related_label' => __( 'Review Screen Copy', 'alynt-account-gateway' ),
			),
			'copy'           => array(
				'title'         => __( 'Tune the words users see at sensitive account moments.', 'alynt-account-gateway' ),
				'description'   => __( 'Screen copy appears above login, registration, password, logout, disabled-registration, and invalid-link states.', 'alynt-account-gateway' ),
				'steps'         => array(
					__( 'Keep instructions short and reassuring so forms stay easy to scan.', 'alynt-account-gateway' ),
					__( 'Mention spam-folder checks on registration and password reset flows where appropriate.', 'alynt-account-gateway' ),
					__( 'Avoid brand-specific claims in reusable defaults; save those for site-specific configuration.', 'alynt-account-gateway' ),
				),
				'related_tab'   => 'branding',
				'related_label' => __( 'Review Branding', 'alynt-account-gateway' ),
			),
			'registration'   => array(
				'title'         => __( 'Keep account creation intentional.', 'alynt-account-gateway' ),
				'description'   => __( 'Registration stays disabled by default. Enable it only after terms, privacy, confirmation, and protection settings are ready.', 'alynt-account-gateway' ),
				'steps'         => array(
					__( 'Confirm the Terms and Privacy paths point to real public pages.', 'alynt-account-gateway' ),
					__( 'Keep the 24-hour pending registration window unless the site has a reason to shorten it.', 'alynt-account-gateway' ),
					__( 'Review username generation before inviting customers so generated usernames stay consistent.', 'alynt-account-gateway' ),
				),
				'related_tab'   => 'security',
				'related_label' => __( 'Review Security', 'alynt-account-gateway' ),
			),
			'security'       => array(
				'title'         => __( 'Layer protection before opening registration.', 'alynt-account-gateway' ),
				'description'   => __( 'Turnstile, Reoon, and rate limits reduce spam signups, repeated login attempts, and password-reset abuse.', 'alynt-account-gateway' ),
				'steps'         => array(
					__( 'Configure at least one provider before enabling public registration.', 'alynt-account-gateway' ),
					__( 'Use server-side Turnstile verification keys, not the client widget alone.', 'alynt-account-gateway' ),
					__( 'Keep rate limits conservative until real traffic patterns are known.', 'alynt-account-gateway' ),
				),
				'related_tab'   => 'registration',
				'related_label' => __( 'Review Registration', 'alynt-account-gateway' ),
			),
			'emails'         => array(
				'title'         => __( 'Preview account emails before users rely on them.', 'alynt-account-gateway' ),
				'description'   => __( 'Email settings control confirmation, password reset, password changed, welcome, and email-change confirmation messages.', 'alynt-account-gateway' ),
				'steps'         => array(
					__( 'Set a test recipient before using preview and test-send tools.', 'alynt-account-gateway' ),
					__( 'Keep required confirmation and reset tokens in the message body.', 'alynt-account-gateway' ),
					__( 'Only disable account emails when another system reliably covers the same notification.', 'alynt-account-gateway' ),
				),
				'related_tab'   => 'registration',
				'related_label' => __( 'Review Registration', 'alynt-account-gateway' ),
			),
			'dashboard'      => array(
				'title'         => __( 'Decide where logged-in users land.', 'alynt-account-gateway' ),
				'description'   => __( 'Dashboard settings control the branded account dashboard and custom links users can access after login.', 'alynt-account-gateway' ),
				'steps'         => array(
					__( 'Enable the custom dashboard before enabling WooCommerce takeover.', 'alynt-account-gateway' ),
					__( 'Add custom links only when they are useful to the roles that can see them.', 'alynt-account-gateway' ),
					__( 'Use ordering and icons to keep repeated account tasks easy to scan.', 'alynt-account-gateway' ),
				),
				'related_tab'   => 'woocommerce',
				'related_label' => __( 'Review WooCommerce', 'alynt-account-gateway' ),
			),
			'woocommerce'    => array(
				'title'         => __( 'Take over WooCommerce account screens carefully.', 'alynt-account-gateway' ),
				'description'   => __( 'WooCommerce takeover wraps native account endpoints in the branded dashboard while WooCommerce keeps handling account actions.', 'alynt-account-gateway' ),
				'steps'         => array(
					__( 'Keep the custom dashboard enabled before switching on WooCommerce takeover.', 'alynt-account-gateway' ),
					__( 'Smoke orders, addresses, account details, downloads, and payment methods after changes.', 'alynt-account-gateway' ),
					__( 'Leave sensitive form handling delegated to WooCommerce.', 'alynt-account-gateway' ),
				),
				'related_tab'   => 'dashboard',
				'related_label' => __( 'Review Dashboard', 'alynt-account-gateway' ),
			),
			'webhooks'       => array(
				'title'         => __( 'Send account-created data only where it belongs.', 'alynt-account-gateway' ),
				'description'   => __( 'Webhook settings dispatch account-created events to external tools and can include signing headers for receiver verification.', 'alynt-account-gateway' ),
				'steps'         => array(
					__( 'Add a signing secret when the receiver can verify HMAC headers.', 'alynt-account-gateway' ),
					__( 'Use the test webhook tool before relying on automation downstream.', 'alynt-account-gateway' ),
					__( 'Enable debug payload logging only while diagnosing webhook payloads.', 'alynt-account-gateway' ),
				),
				'related_tab'   => 'privacy',
				'related_label' => __( 'Review Retention', 'alynt-account-gateway' ),
			),
			'privacy'        => array(
				'title'         => __( 'Set retention before collecting account evidence.', 'alynt-account-gateway' ),
				'description'   => __( 'Privacy settings control how long plugin-owned verification, webhook, consent, and audit records are kept.', 'alynt-account-gateway' ),
				'steps'         => array(
					__( 'Keep successful webhook logs shorter than failed logs unless the site needs longer audit evidence.', 'alynt-account-gateway' ),
					__( 'Retain consent and audit records long enough for operational review.', 'alynt-account-gateway' ),
					__( 'Confirm exporter and eraser behavior during privacy QA.', 'alynt-account-gateway' ),
				),
				'related_tab'   => 'advanced_tools',
				'related_label' => __( 'Review Tools', 'alynt-account-gateway' ),
			),
			'advanced_tools' => array(
				'title'       => __( 'Use advanced tools for recovery and diagnostics.', 'alynt-account-gateway' ),
				'description' => __( 'Advanced tools include emergency access, import/export, diagnostics, and cleanup controls for setup and support.', 'alynt-account-gateway' ),
				'steps'       => array(
					__( 'Store the emergency bypass key securely before replacing public login routes.', 'alynt-account-gateway' ),
					__( 'Export settings before larger configuration changes.', 'alynt-account-gateway' ),
					__( 'Enable diagnostics only when setup or support needs extra evidence.', 'alynt-account-gateway' ),
				),
			),
		);
	}
}
