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
}