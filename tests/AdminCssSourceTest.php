<?php
/**
 * Admin CSS source tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests important admin CSS selectors.
 */
class AdminCssSourceTest extends TestCase {

	/**
	 * Reads the admin source CSS.
	 *
	 * @return string
	 */
	private function get_admin_css() {
		$css = file_get_contents( dirname( __DIR__ ) . '/assets/src/admin/style.css' );

		$this->assertIsString( $css );

		return $css;
	}

	public function test_admin_css_uses_logical_inline_start_for_rtl_panels() {
		$css = $this->get_admin_css();

		$this->assertStringContainsString( 'border-inline-start: 4px solid #3858e9;', $css );
		$this->assertStringContainsString( 'padding-inline-start: 18px;', $css );
		$this->assertStringContainsString( 'inset-inline-start: 0;', $css );
		$this->assertStringContainsString( 'border-inline-start-width: 4px;', $css );
		$this->assertStringContainsString( 'border-inline-start-color: #008a20;', $css );
		$this->assertStringContainsString( 'border-inline-start-color: #dba617;', $css );
		$this->assertStringContainsString( 'border-inline-start-color: #d63638;', $css );
		$this->assertStringContainsString( 'border-inline-start: 4px solid #2271b1;', $css );
	}

	public function test_admin_css_avoids_left_specific_panel_accents() {
		$css = $this->get_admin_css();

		$this->assertStringNotContainsString( 'border-left: 4px solid', $css );
		$this->assertStringNotContainsString( 'border-left-width: 4px;', $css );
		$this->assertStringNotContainsString( 'border-left-color:', $css );
		$this->assertStringNotContainsString( 'padding-left: 18px;', $css );
		$this->assertStringNotContainsString( 'left: 0;', $css );
	}

	public function test_settings_tabs_wrap_as_independent_controls() {
		$css = $this->get_admin_css();

		$this->assertMatchesRegularExpression( '/\.alynt-ag-admin \.nav-tab-wrapper\s*\{[^}]*display:\s*flex;/s', $css );
		$this->assertMatchesRegularExpression( '/\.alynt-ag-admin \.nav-tab-wrapper\s*\{[^}]*flex-wrap:\s*wrap;/s', $css );
		$this->assertMatchesRegularExpression( '/\.alynt-ag-admin \.nav-tab-wrapper\s*\{[^}]*gap:\s*8px;/s', $css );
		$this->assertMatchesRegularExpression( '/\.alynt-ag-admin \.nav-tab\s*\{[^}]*float:\s*none;/s', $css );
		$this->assertMatchesRegularExpression( '/\.alynt-ag-admin \.nav-tab\s*\{[^}]*white-space:\s*nowrap;/s', $css );
		$this->assertMatchesRegularExpression( '/\.alynt-ag-admin \.nav-tab-active\s*\{[^}]*box-shadow:\s*inset 0 3px #2271b1;/s', $css );
	}

	public function test_email_save_state_notice_respects_hidden_attribute() {
		$css = $this->get_admin_css();

		$this->assertMatchesRegularExpression( '/\.alynt-ag-email-save-state\s*\{[^}]*max-width:\s*78rem;/s', $css );
		$this->assertMatchesRegularExpression( '/\.alynt-ag-email-save-state\[hidden\]\s*\{[^}]*display:\s*none;/s', $css );
	}

	public function test_typography_preview_is_stable_and_readable() {
		$css = $this->get_admin_css();

		$this->assertMatchesRegularExpression( '/\.alynt-ag-typography-control\s*\{[^}]*max-width:\s*46rem;/s', $css );
		$this->assertMatchesRegularExpression( '/\.alynt-ag-typography-preview\s*\{[^}]*border-radius:\s*4px;/s', $css );
		$this->assertMatchesRegularExpression( '/\.alynt-ag-typography-preview__heading\s*\{[^}]*font-size:\s*24px;/s', $css );
		$this->assertMatchesRegularExpression( '/\.alynt-ag-typography-preview__body\s*\{[^}]*font-size:\s*16px;/s', $css );
	}

	public function test_color_control_has_stable_picker_and_accessible_focus_styles() {
		$css = $this->get_admin_css();

		$this->assertMatchesRegularExpression( '/\.alynt-ag-color-control\s*\{[^}]*display:\s*flex;/s', $css );
		$this->assertMatchesRegularExpression( '/\.alynt-ag-color-control\s*\{[^}]*max-width:\s*36rem;/s', $css );
		$this->assertMatchesRegularExpression( '/\.alynt-ag-color-control__picker\s*\{[^}]*width:\s*64px;/s', $css );
		$this->assertMatchesRegularExpression( '/\.alynt-ag-color-control__picker\s*\{[^}]*min-height:\s*40px;/s', $css );
		$this->assertMatchesRegularExpression( '/\.alynt-ag-color-control__picker:focus\s*\{[^}]*box-shadow:\s*0 0 0 1px #2271b1;/s', $css );
		$this->assertMatchesRegularExpression( '/\.alynt-ag-color-control--invalid \.alynt-ag-color-control__text\s*\{[^}]*border-color:\s*#d63638;/s', $css );
	}
}
