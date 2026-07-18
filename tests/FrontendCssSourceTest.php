<?php
/**
 * Frontend CSS source tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests important frontend CSS selectors.
 */
class FrontendCssSourceTest extends TestCase {

	/**
	 * Reads the frontend source CSS.
	 *
	 * @return string
	 */
	private function get_frontend_css() {
		$css = file_get_contents( dirname( __DIR__ ) . '/assets/src/frontend/style.css' );

		$this->assertIsString( $css );

		return $css;
	}

	public function test_woocommerce_dashboard_form_polish_is_scoped_to_dashboard_content() {
		$css = $this->get_frontend_css();

		$selectors = array(
			'.agw-dashboard-content .woocommerce-notices-wrapper',
			'.agw-dashboard-content .woocommerce-message',
			'.agw-dashboard-content .woocommerce-error',
			'.agw-dashboard-content .form-row',
			'.agw-dashboard-content .woocommerce-form-row',
			'.agw-dashboard-content .woocommerce-address-fields__field-wrapper',
			'.agw-dashboard-content .woocommerce-EditAccountForm',
			'.agw-dashboard-content fieldset',
			'.agw-dashboard-content input[type="submit"]',
			'.agw-dashboard-content .woocommerce-PaymentMethods',
		);

		foreach ( $selectors as $selector ) {
			$this->assertStringContainsString( $selector, $css );
		}
	}

	public function test_woocommerce_dashboard_form_polish_has_mobile_single_column_fallback() {
		$css = $this->get_frontend_css();

		$this->assertStringContainsString( '@media (max-width: 800px)', $css );
		$this->assertStringContainsString( '.agw-dashboard-content .woocommerce-address-fields__field-wrapper', $css );
		$this->assertStringContainsString( '.agw-dashboard-content .woocommerce-EditAccountForm', $css );
		$this->assertStringContainsString( 'grid-template-columns: 1fr;', $css );
	}

	public function test_frontend_css_includes_focus_visible_guardrails() {
		$css = $this->get_frontend_css();

		$this->assertStringContainsString( '.agw-field input:focus-visible', $css );
		$this->assertStringContainsString( '.agw-password__toggle:focus-visible', $css );
		$this->assertStringContainsString( '.agw-dashboard-link:focus-visible', $css );
		$this->assertStringContainsString( '.agw-dashboard-content input[type="submit"]:focus-visible', $css );
		$this->assertStringContainsString( 'outline: 3px solid var(--agw-color-primary);', $css );
		$this->assertStringContainsString( 'outline-offset: 3px;', $css );
	}

	public function test_frontend_css_includes_forced_colors_support() {
		$css = $this->get_frontend_css();

		$this->assertStringContainsString( '@media (forced-colors: active)', $css );
		$this->assertStringContainsString( '--agw-color-text: CanvasText;', $css );
		$this->assertStringContainsString( '--agw-color-background: Canvas;', $css );
		$this->assertStringContainsString( '--agw-button-background: ButtonFace;', $css );
		$this->assertStringContainsString( 'forced-color-adjust: auto;', $css );
		$this->assertStringContainsString( 'border: 1px solid CanvasText;', $css );
		$this->assertStringContainsString( 'background: Field;', $css );
		$this->assertStringContainsString( 'color: LinkText;', $css );
		$this->assertStringContainsString( 'outline: 3px solid Highlight;', $css );
	}

	public function test_frontend_css_uses_logical_resend_guidance_indentation() {
		$css = $this->get_frontend_css();

		$this->assertStringContainsString( '.agw-resend-guidance ul', $css );
		$this->assertStringContainsString( 'padding-inline-start: 20px;', $css );
		$this->assertStringNotContainsString( 'padding-left: 20px;', $css );
	}

	public function test_frontend_css_normalizes_gateway_form_controls_against_theme_styles() {
		$css = $this->get_frontend_css();

		$this->assertStringContainsString( ".alynt-ag-gateway,\n.alynt-ag-gateway *,\n.alynt-ag-gateway *::before,\n.alynt-ag-gateway *::after {\n\tbox-sizing: border-box;", $css );
		$this->assertStringContainsString( ".agw-form p,\n.agw-form fieldset {\n\tmargin: 0;", $css );
		$this->assertStringContainsString( ".agw-field input {\n\tappearance: none;", $css );
		$this->assertStringContainsString( "\tmax-width: 100%;\n\tmin-height: 48px;\n\tmargin: 0;", $css );
		$this->assertStringContainsString( "\tbox-shadow: none;\n\tcolor: var(--agw-color-text);", $css );
		$this->assertStringContainsString( ".agw-password__toggle {\n\tappearance: none;", $css );
		$this->assertStringContainsString( "\tline-height: 1;\n\ttext-transform: none;", $css );
		$this->assertStringContainsString( ".agw-button {\n\tappearance: none;", $css );
		$this->assertStringContainsString( "\ttext-decoration: none;\n\ttext-transform: none;\n\tletter-spacing: 0;", $css );
		$this->assertStringContainsString( ".agw-links a,\n.agw-back-link,\n.agw-checkbox a {\n\tcolor: var(--agw-color-primary);", $css );
		$this->assertStringContainsString( "\toverflow-wrap: anywhere;\n\ttext-decoration: underline;\n\ttext-transform: none;", $css );
		$this->assertStringContainsString( ".alynt-ag-gateway .agw-form p,\n.alynt-ag-gateway .agw-form fieldset {\n\tmin-width: 0;\n\tmargin: 0;", $css );
		$this->assertStringContainsString( ".alynt-ag-gateway .agw-form .agw-field input {\n\tmin-width: 0;\n\tmargin: 0;\n\tbox-shadow: none;", $css );
		$this->assertStringContainsString( '.alynt-ag-gateway .agw-form .agw-password__toggle', $css );
		$this->assertStringContainsString( ".alynt-ag-gateway .agw-form .agw-button,\n.alynt-ag-gateway .agw-actions .agw-button {\n\twidth: auto;\n\tmax-width: 100%;\n\tmin-width: 0;", $css );
		$this->assertStringContainsString( ".alynt-ag-gateway .agw-links a,\n.alynt-ag-gateway .agw-back-link,\n.alynt-ag-gateway .agw-checkbox a {\n\tmax-width: 100%;\n\tmargin: 0;", $css );
	}

	public function test_frontend_css_normalizes_dashboard_form_controls_against_theme_styles() {
		$css = $this->get_frontend_css();

		$this->assertStringContainsString( ".agw-dashboard-content input,\n.agw-dashboard-content textarea {\n\tappearance: none;", $css );
		$this->assertStringContainsString( "\tmax-width: 100%;\n\tmin-width: 0;\n\tmin-height: 42px;\n\tmargin: 0;", $css );
		$this->assertStringContainsString( "\tbox-shadow: none;\n\tcolor: var(--agw-color-text);", $css );
		$this->assertStringContainsString( ".agw-dashboard-content input[type=\"checkbox\"],\n.agw-dashboard-content input[type=\"radio\"] {\n\tappearance: auto;", $css );
		$this->assertStringContainsString( ".agw-dashboard-content .button,\n.agw-dashboard-content button,\n.agw-dashboard-content input[type=\"submit\"] {\n\tappearance: none;", $css );
		$this->assertStringContainsString( "\tmax-width: 100%;\n\tmin-height: 42px;\n\tmargin: 0;", $css );
		$this->assertStringContainsString( "\ttext-decoration: none;\n\ttext-transform: none;\n\tletter-spacing: 0;", $css );
	}

	public function test_frontend_text_sizes_are_at_least_sixteen_pixels() {
		$css = $this->get_frontend_css();

		preg_match_all( '/font-size:\s*([0-9]+)px;/', $css, $matches );

		$this->assertNotEmpty( $matches[1] );

		foreach ( $matches[1] as $font_size ) {
			$this->assertGreaterThanOrEqual( 16, (int) $font_size );
		}
	}

	public function test_requested_gateway_controls_use_eighteen_pixel_text() {
		$css = $this->get_frontend_css();

		$this->assertMatchesRegularExpression( '/\.agw-notice\s*\{[^}]*font-size:\s*18px;/s', $css );
		$this->assertMatchesRegularExpression( '/\.agw-field input\s*\{[^}]*font-size:\s*18px;/s', $css );
		$this->assertMatchesRegularExpression( '/\.agw-checkbox\s*\{[^}]*font-size:\s*18px;/s', $css );
		$this->assertMatchesRegularExpression( '/\.agw-button\s*\{[^}]*font-size:\s*18px;/s', $css );
		$this->assertMatchesRegularExpression( '/\.agw-links\s*\{[^}]*font-size:\s*18px;/s', $css );
		$this->assertMatchesRegularExpression( '/\.agw-back-link\s*\{[^}]*font-size:\s*18px;/s', $css );
		$this->assertMatchesRegularExpression( '/\.agw-dashboard-content input,[^}]*font-size:\s*18px;/s', $css );
	}

	public function test_recent_orders_module_has_responsive_accessible_styles() {
		$css = $this->get_frontend_css();

		$this->assertStringContainsString( '.agw-dashboard-recent-orders__list', $css );
		$this->assertStringContainsString( '.agw-dashboard-recent-order:focus-visible', $css );
		$this->assertStringContainsString( '.agw-dashboard-recent-order__summary', $css );
		$this->assertMatchesRegularExpression( '/\.agw-dashboard-hero__meta\s*\{[^}]*overflow-wrap:\s*anywhere;/s', $css );
		$this->assertStringContainsString( '@media (max-width: 800px)', $css );
		$this->assertStringContainsString( '.agw-dashboard-recent-order {', $css );
		$this->assertStringContainsString( 'grid-template-columns: 1fr;', $css );
		$this->assertStringContainsString( '@media (forced-colors: active)', $css );
	}

	public function test_available_downloads_module_has_responsive_accessible_styles() {
		$css = $this->get_frontend_css();

		$this->assertStringContainsString( '.agw-dashboard-downloads__list', $css );
		$this->assertStringContainsString( '.agw-dashboard-download__action:focus-visible', $css );
		$this->assertStringContainsString( '.agw-dashboard-download__meta', $css );
		$this->assertMatchesRegularExpression( '/\.agw-dashboard-downloads__header a,[^{]*\.agw-dashboard-download__action\s*\{[^}]*min-height:\s*44px;/s', $css );
		$this->assertMatchesRegularExpression( '/\.agw-dashboard-download__identity strong\s*\{[^}]*overflow-wrap:\s*anywhere;/s', $css );
		$this->assertMatchesRegularExpression( '/@media \(max-width: 800px\)[\s\S]*\.agw-dashboard-download\s*\{[^}]*grid-template-columns:\s*1fr;/s', $css );
		$this->assertMatchesRegularExpression( '/@media \(forced-colors: active\)[\s\S]*\.agw-dashboard-download,[\s\S]*\.agw-dashboard-download__action,/s', $css );
	}

	public function test_saved_addresses_module_has_responsive_accessible_styles() {
		$css = $this->get_frontend_css();

		$this->assertStringContainsString( '.agw-dashboard-addresses__grid', $css );
		$this->assertStringContainsString( 'grid-template-columns: repeat(2, minmax(0, 1fr));', $css );
		$this->assertStringContainsString( '.agw-dashboard-address__action:focus-visible', $css );
		$this->assertStringContainsString( '.agw-dashboard-address__details', $css );
		$this->assertStringContainsString( 'overflow-wrap: anywhere;', $css );
		$this->assertMatchesRegularExpression( '/\.agw-dashboard-addresses__header a,[^{]*\.agw-dashboard-address__action\s*\{[^}]*min-height:\s*44px;/s', $css );
		$this->assertMatchesRegularExpression( '/@media \(max-width: 800px\)[\s\S]*\.agw-dashboard-addresses__header[^{]*\{[^}]*flex-direction:\s*column;/s', $css );
		$this->assertStringContainsString( '.agw-dashboard-address {', $css );
	}

	public function test_saved_payment_methods_module_has_responsive_accessible_styles() {
		$css = $this->get_frontend_css();

		$this->assertStringContainsString( '.agw-dashboard-payment-methods__list', $css );
		$this->assertStringContainsString( '.agw-dashboard-payment-methods__header a:focus-visible', $css );
		$this->assertStringContainsString( '.agw-dashboard-payment-method__default', $css );
		$this->assertMatchesRegularExpression( '/\.agw-dashboard-payment-methods__header a\s*\{[^}]*min-height:\s*44px;/s', $css );
		$this->assertMatchesRegularExpression( '/\.agw-dashboard-payment-method__name\s*\{[^}]*font-size:\s*16px;[^}]*overflow-wrap:\s*anywhere;/s', $css );
		$this->assertMatchesRegularExpression( '/@media \(max-width: 800px\)[\s\S]*\.agw-dashboard-payment-methods__list\s*\{[^}]*grid-template-columns:\s*1fr;/s', $css );
		$this->assertMatchesRegularExpression( '/@media \(forced-colors: active\)[\s\S]*\.agw-dashboard-payment-method,[\s\S]*\.agw-dashboard-payment-method__default,/s', $css );
	}

	public function test_account_details_module_has_responsive_accessible_styles() {
		$css = $this->get_frontend_css();

		$this->assertStringContainsString( '.agw-dashboard-account-details__grid', $css );
		$this->assertStringContainsString( '.agw-dashboard-account-details__header a:focus-visible', $css );
		$this->assertStringContainsString( '.agw-dashboard-account-details__status--ready', $css );
		$this->assertMatchesRegularExpression( '/\.agw-dashboard-account-details__header a\s*\{[^}]*min-height:\s*44px;/s', $css );
		$this->assertMatchesRegularExpression( '/\.agw-dashboard-account-detail dd\s*\{[^}]*font-size:\s*16px;[^}]*overflow-wrap:\s*anywhere;/s', $css );
		$this->assertMatchesRegularExpression( '/@media \(max-width: 800px\)[\s\S]*\.agw-dashboard-account-details__grid\s*\{[^}]*grid-template-columns:\s*1fr;/s', $css );
		$this->assertMatchesRegularExpression( '/@media \(forced-colors: active\)[\s\S]*\.agw-dashboard-account-detail,/s', $css );
		$this->assertMatchesRegularExpression( '/@media \(forced-colors: active\)[\s\S]*\.agw-dashboard-account-details__status\s*\{[^}]*color:\s*CanvasText;/s', $css );
	}

	public function test_gateway_card_spacing_matches_post_v1_visual_tweak() {
		$css = $this->get_frontend_css();

		$this->assertMatchesRegularExpression( '/\.agw-panel\s*\{[^}]*padding:\s*24px;/s', $css );
		$this->assertMatchesRegularExpression( '/\.agw-card\s*\{[^}]*max-width:\s*540px;[^}]*padding:\s*40px 24px;/s', $css );
		$this->assertMatchesRegularExpression( '/\.agw-notice p:last-child\s*\{[^}]*margin:\s*0;/s', $css );
	}
}
