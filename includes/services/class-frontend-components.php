<?php
/**
 * Frontend shared component helpers.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders shared frontend gateway components.
 */
class ALYNT_AG_Frontend_Components {

	/**
	 * Render configured registration verification area.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	public function render_verification_slot( $settings ) {
		if ( ! empty( $settings['turnstile_site_key'] ) ) {
			?>
			<div class="agw-verification-slot" aria-label="<?php esc_attr_e( 'Account verification', 'alynt-account-gateway' ); ?>">
				<div class="cf-turnstile" data-sitekey="<?php echo esc_attr( $settings['turnstile_site_key'] ); ?>"></div>
			</div>
			<?php
			return;
		}
		?>
		<div class="agw-verification-slot" role="status"><?php esc_html_e( 'Verification will appear here when enabled.', 'alynt-account-gateway' ); ?></div>
		<?php
	}

	/**
	 * Render configurable screen instruction text.
	 *
	 * @param string $copy Notice copy.
	 * @return void
	 */
	public function render_notice( $copy ) {
		if ( '' === trim( wp_strip_all_tags( (string) $copy ) ) ) {
			return;
		}
		?>
		<div class="agw-notice"><?php echo wp_kses_post( wpautop( $copy ) ); ?></div>
		<?php
	}
}
