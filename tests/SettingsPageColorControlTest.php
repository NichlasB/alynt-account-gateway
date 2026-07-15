<?php
/**
 * Settings page color control tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

require_once ALYNT_AG_PLUGIN_DIR . 'admin/class-settings-page.php';

/**
 * Tests the synchronized color picker and hex input renderer.
 */
class SettingsPageColorControlTest extends TestCase {

	/**
	 * Render a private settings field helper.
	 *
	 * @param string $value Current color value.
	 * @return string
	 */
	private function render_color_field( $value ) {
		$settings_page = new ALYNT_AG_Settings_Page();
		$reflection    = new ReflectionMethod( $settings_page, 'render_field' );

		ob_start();
		$reflection->invokeArgs(
			$settings_page,
			array(
				'primary_color',
				array(
					'type'    => 'color',
					'default' => '#3B5249',
					'label'   => 'Primary Color',
				),
				$value,
			)
		);

		return (string) ob_get_clean();
	}

	public function test_color_field_renders_picker_and_named_hex_input() {
		$output = $this->render_color_field( '#3B5249' );

		$this->assertStringContainsString( 'class="alynt-ag-color-control"', $output );
		$this->assertStringContainsString( 'data-alynt-ag-color-control', $output );
		$this->assertStringContainsString( 'type="color"', $output );
		$this->assertStringContainsString( 'value="#3B5249"', $output );
		$this->assertStringContainsString( 'aria-label="Choose Primary Color"', $output );
		$this->assertStringContainsString( 'data-alynt-ag-color-picker', $output );
		$this->assertStringContainsString( 'id="alynt-ag-primary_color"', $output );
		$this->assertStringContainsString( 'name="alynt_ag_settings[primary_color]"', $output );
		$this->assertStringContainsString( 'pattern="^#[a-fA-F0-9]{6}$"', $output );
		$this->assertStringContainsString( 'data-alynt-ag-color-text', $output );
		$this->assertSame( 1, substr_count( $output, 'name="alynt_ag_settings[primary_color]"' ) );
	}

	public function test_picker_uses_schema_default_when_saved_text_is_empty() {
		$output = $this->render_color_field( '' );

		$this->assertStringContainsString( 'type="color"', $output );
		$this->assertStringContainsString( 'value="#3B5249"', $output );
		$this->assertStringContainsString( 'name="alynt_ag_settings[primary_color]"', $output );
		$this->assertStringContainsString( 'value=""', $output );
	}

	public function test_primary_color_help_explains_picker_and_hex_entry() {
		$settings_page = new ALYNT_AG_Settings_Page();
		$reflection    = new ReflectionMethod( $settings_page, 'settings_field_help_text' );
		$help          = $reflection->invokeArgs( $settings_page, array( 'primary_color' ) );

		$this->assertStringContainsString( 'color swatch to open the picker', $help );
		$this->assertStringContainsString( 'six-digit hex value such as #3B5249', $help );
	}
}
