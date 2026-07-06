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

		$this->assertStringContainsString( '@media (max-width: 799px)', $css );
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

		$this->assertStringContainsString( ".agw-field input {\n\tappearance: none;", $css );
		$this->assertStringContainsString( "\tmax-width: 100%;\n\tmin-height: 48px;", $css );
		$this->assertStringContainsString( ".agw-password__toggle {\n\tappearance: none;", $css );
		$this->assertStringContainsString( ".agw-button {\n\tappearance: none;", $css );
	}

	public function test_frontend_css_normalizes_dashboard_form_controls_against_theme_styles() {
		$css = $this->get_frontend_css();

		$this->assertStringContainsString( ".agw-dashboard-content input,\n.agw-dashboard-content textarea {\n\tappearance: none;", $css );
		$this->assertStringContainsString( ".agw-dashboard-content input[type=\"checkbox\"],\n.agw-dashboard-content input[type=\"radio\"] {\n\tappearance: auto;", $css );
		$this->assertStringContainsString( ".agw-dashboard-content .button,\n.agw-dashboard-content button,\n.agw-dashboard-content input[type=\"submit\"] {\n\tappearance: none;", $css );
		$this->assertStringContainsString( "\tmax-width: 100%;\n\tmin-height: 42px;", $css );
	}
}
