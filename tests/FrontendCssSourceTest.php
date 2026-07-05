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
}
