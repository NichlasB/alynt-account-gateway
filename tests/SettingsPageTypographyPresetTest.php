<?php
/**
 * Settings page typography preset tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

require_once ALYNT_AG_PLUGIN_DIR . 'admin/class-settings-page.php';

/**
 * Tests the brand-agnostic typography preset helper.
 */
class SettingsPageTypographyPresetTest extends TestCase {

	/**
	 * Invoke a private settings page helper.
	 *
	 * @param ALYNT_AG_Settings_Page $settings_page Settings page instance.
	 * @param string                 $method        Method name.
	 * @param array<int,mixed>       $args          Method arguments.
	 * @return mixed
	 */
	private function invoke_helper( $settings_page, $method, $args = array() ) {
		$reflection = new ReflectionMethod( $settings_page, $method );

		return $reflection->invokeArgs( $settings_page, $args );
	}

	public function test_presets_use_local_system_stacks_and_include_current_defaults() {
		$settings_page = new ALYNT_AG_Settings_Page();
		$presets       = $this->invoke_helper( $settings_page, 'typography_presets' );

		$this->assertSame( array( 'classic', 'modern', 'editorial', 'humanist' ), array_keys( $presets ) );
		$this->assertSame( 'Georgia, serif', $presets['classic']['heading'] );
		$this->assertSame( '-apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif', $presets['classic']['body'] );

		foreach ( $presets as $preset ) {
			$this->assertStringNotContainsString( 'http', $preset['heading'] . $preset['body'] );
			$this->assertStringNotContainsString( '@font-face', $preset['heading'] . $preset['body'] );
		}
	}

	public function test_current_stacks_match_known_preset_or_custom_without_mutation() {
		$settings_page = new ALYNT_AG_Settings_Page();

		$this->assertSame(
			'classic',
			$this->invoke_helper(
				$settings_page,
				'selected_typography_preset',
				array(
					array(
						'heading_font_family' => 'Georgia, serif',
						'body_font_family'    => '-apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif',
					),
				)
			)
		);
		$this->assertSame(
			'custom',
			$this->invoke_helper(
				$settings_page,
				'selected_typography_preset',
				array(
					array(
						'heading_font_family' => 'My Local Heading, serif',
						'body_font_family'    => 'My Local Body, sans-serif',
					),
				)
			)
		);
	}

	public function test_control_renders_presets_custom_state_preview_and_no_saved_field() {
		$settings_page = new ALYNT_AG_Settings_Page();

		ob_start();
		$this->invoke_helper(
			$settings_page,
			'render_typography_preset_control',
			array(
				array(
					'heading_font_family' => 'My Local Heading, serif',
					'body_font_family'    => 'My Local Body, sans-serif',
				),
			)
		);
		$output = ob_get_clean();

		$this->assertStringContainsString( 'data-alynt-ag-typography-presets', $output );
		$this->assertStringContainsString( 'data-heading="Georgia, serif"', $output );
		$this->assertStringContainsString( 'value="custom" selected', $output );
		$this->assertStringContainsString( 'Current pairing: Custom', $output );
		$this->assertStringContainsString( 'aria-live="polite"', $output );
		$this->assertStringContainsString( 'No remote fonts are loaded.', $output );
		$this->assertStringNotContainsString( 'name="alynt_ag_settings[typography_preset]"', $output );
	}
}
