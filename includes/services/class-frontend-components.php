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
				<div class="cf-turnstile" data-agw-turnstile-widget data-sitekey="<?php echo esc_attr( $settings['turnstile_site_key'] ); ?>"></div>
			</div>
			<?php
			return;
		}
		?>
		<div class="agw-verification-slot" role="status" aria-live="polite" aria-atomic="true"><?php esc_html_e( 'Verification will appear here when enabled.', 'alynt-account-gateway' ); ?></div>
		<?php
	}

	/**
	 * Render configurable screen instruction text.
	 *
	 * @param string $copy Notice copy.
	 * @param string $id   Optional notice ID.
	 * @return void
	 */
	public function render_notice( $copy, $id = '' ) {
		if ( ! $this->has_notice( $copy ) ) {
			return;
		}

		$id = sanitize_key( (string) $id );
		?>
		<div class="agw-notice"<?php echo $id ? ' id="' . esc_attr( $id ) . '"' : ''; ?>><?php echo wp_kses_post( wpautop( $copy ) ); ?></div>
		<?php
	}

	/**
	 * Return whether notice copy has visible text after tags are stripped.
	 *
	 * @param string $copy Notice copy.
	 * @return bool
	 */
	public function has_notice( $copy ) {
		return '' !== trim( wp_strip_all_tags( (string) $copy ) );
	}

	/**
	 * Return an aria-describedby attribute for one or more IDs.
	 *
	 * @param array<int,string> $ids Description IDs.
	 * @return string
	 */
	public function describedby_attribute( $ids ) {
		$ids = array_filter( array_map( 'sanitize_key', (array) $ids ) );

		return $ids ? ' aria-describedby="' . esc_attr( implode( ' ', $ids ) ) . '"' : '';
	}
}
