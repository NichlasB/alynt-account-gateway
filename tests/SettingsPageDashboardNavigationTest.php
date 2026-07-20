<?php
/**
 * Dashboard navigation settings tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests dashboard navigation settings controls.
 */
class SettingsPageDashboardNavigationTest extends TestCase {

	/**
	 * Render a private settings field helper.
	 *
	 * @param string              $key   Settings key.
	 * @param array<string,mixed> $field Field schema.
	 * @param mixed               $value Current value.
	 * @return string
	 */
	private function render_field( $key, $field, $value ) {
		$settings_page = new ALYNT_AG_Settings_Page();

		ob_start();
		alynt_ag_test_invoke_settings_page_method(
			$settings_page,
			'render_field',
			array( $key, $field, $value )
		);

		return ob_get_clean();
	}

	public function test_woocommerce_navigation_field_lists_detected_items_as_checked_by_default() {
		$schema = ALYNT_AG_Settings_Schema::schema();
		$html   = $this->render_field(
			'woocommerce_hidden_menu_items',
			$schema['woocommerce_hidden_menu_items'],
			array()
		);

		$this->assertStringContainsString( 'class="alynt-ag-checkbox-list"', $html );
		$this->assertStringContainsString( 'Show Dashboard', $html );
		$this->assertStringContainsString( 'Show Orders', $html );
		$this->assertStringContainsString( 'Show Payment Methods', $html );
		$this->assertSame( 2, substr_count( $html, 'alynt_ag_settings[woocommerce_hidden_menu_items][orders]' ) );
		$this->assertGreaterThanOrEqual( 7, substr_count( $html, 'checked=' ) );
	}

	public function test_woocommerce_navigation_field_leaves_hidden_item_unchecked() {
		$schema = ALYNT_AG_Settings_Schema::schema();
		$html   = $this->render_field(
			'woocommerce_hidden_menu_items',
			$schema['woocommerce_hidden_menu_items'],
			array( 'orders' )
		);

		$this->assertSame( 6, substr_count( $html, 'checked=' ) );
		$this->assertStringContainsString( 'Show Orders', $html );
	}
}
