<?php
/**
 * Settings page portability tools tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

require_once ALYNT_AG_PLUGIN_DIR . 'admin/class-settings-page.php';

if ( ! function_exists( 'submit_button' ) ) {
	function submit_button( $text = null, $type = 'primary', $name = 'submit', $wrap = true, $other_attributes = null ) {
		unset( $type, $wrap, $other_attributes );

		echo '<input type="submit" name="' . esc_attr( $name ) . '" value="' . esc_attr( (string) $text ) . '">';
	}
}

/**
 * Tests settings import/export guidance on the settings page.
 */
class SettingsPageSettingsToolsTest extends TestCase {

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

	public function test_settings_tools_render_portability_guidance() {
		$settings_page = new ALYNT_AG_Settings_Page();

		ob_start();
		$this->invoke_helper( $settings_page, 'render_settings_tools' );
		$output = ob_get_clean();

		$this->assertStringContainsString( 'Configuration portability notes', $output );
		$this->assertStringContainsString( 'Media-library files, pending registrations, diagnostics, webhook delivery logs, and WordPress users are not included.', $output );
		$this->assertStringContainsString( 'Imports validate JSON before saving', $output );
		$this->assertStringContainsString( 'Use the restore button at the bottom of each tab', $output );
		$this->assertStringContainsString( 'Export Settings JSON', $output );
		$this->assertStringContainsString( 'Import Settings', $output );
	}
}
