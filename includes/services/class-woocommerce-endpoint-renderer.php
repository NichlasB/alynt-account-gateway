<?php
/**
 * WooCommerce endpoint renderer.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders native WooCommerce endpoint content in the branded dashboard.
 */
class ALYNT_AG_WooCommerce_Endpoint_Renderer extends ALYNT_AG_Service_Collaborator {

	/**
	 * Render WooCommerce account endpoint content.
	 *
	 * @param string $endpoint Endpoint key.
	 * @param string $value    Endpoint value.
	 * @return bool
	 */
	public function render_endpoint( $endpoint, $value = '' ) {
		if ( ! $this->service->detect() || ! $endpoint || 'dashboard' === $endpoint ) {
			return false;
		}

		ob_start();

		if ( function_exists( 'woocommerce_output_all_notices' ) ) {
			woocommerce_output_all_notices();
		}

		do_action( 'woocommerce_account_' . sanitize_key( $endpoint ) . '_endpoint', $value );

		$output        = ob_get_clean();
		$output        = 'edit-account' === $endpoint && is_string( $output )
			? $this->remove_display_name_field( $output )
			: $output;
		$content_check = is_string( $output ) ? trim( $output ) : '';
		$content_check = preg_replace( '#<div\s+class=(["\'])woocommerce-notices-wrapper\1>\s*</div>#i', '', $content_check );

		if ( ! is_string( $output ) || '' === trim( (string) $content_check ) ) {
			return false;
		}

		echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WooCommerce endpoint handlers render trusted account template output.
		return true;
	}

	/**
	 * Remove WooCommerce's public display-name row from the branded account form.
	 *
	 * @param string $output WooCommerce endpoint output.
	 * @return string
	 */
	private function remove_display_name_field( $output ) {
		$pattern = '#<p\b(?=[^>]*\bwoocommerce-form-row\b)[^>]*>\s*<label\b[^>]*\bfor=(["\'])account_display_name\1.*?</p>#is';
		$output  = preg_replace( $pattern, '', $output, 1 );

		return is_string( $output ) ? $output : '';
	}
}
