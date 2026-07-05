<?php
/**
 * Settings page field help tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

require_once ALYNT_AG_PLUGIN_DIR . 'admin/class-settings-page.php';

if ( ! function_exists( 'checked' ) ) {
	/**
	 * Test stub for WordPress checked().
	 *
	 * @param mixed $checked Current value.
	 * @param mixed $current Expected value.
	 * @param bool  $echo    Whether to echo.
	 * @return string
	 */
	function checked( $checked, $current = true, $echo = true ) {
		$result = $checked === $current ? ' checked="checked"' : '';

		if ( $echo ) {
			echo $result; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static test stub output.
		}

		return $result;
	}
}

/**
 * Tests field-level help text on the settings page.
 */
class SettingsPageFieldHelpTest extends TestCase {

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

	public function test_field_help_map_covers_high_impact_settings() {
		$settings_page = new ALYNT_AG_Settings_Page();

		$this->assertStringContainsString(
			'Leave disabled until URLs',
			$this->invoke_helper( $settings_page, 'settings_field_help_text', array( 'frontend_enabled' ) )
		);
		$this->assertStringContainsString(
			'Use a relative path such as /login',
			$this->invoke_helper( $settings_page, 'settings_field_help_text', array( 'login_path' ) )
		);
		$this->assertStringContainsString(
			'WordPress still needs a generated username',
			$this->invoke_helper( $settings_page, 'settings_field_help_text', array( 'username_format' ) )
		);
		$this->assertStringContainsString(
			'Requires the custom dashboard',
			$this->invoke_helper( $settings_page, 'settings_field_help_text', array( 'woocommerce_takeover' ) )
		);
		$this->assertSame(
			'',
			$this->invoke_helper( $settings_page, 'settings_field_help_text', array( 'missing_setting' ) )
		);
	}

	public function test_text_field_renders_aria_describedby_and_help() {
		$settings_page = new ALYNT_AG_Settings_Page();
		$field         = array(
			'type'    => 'relative_path',
			'default' => '/login',
		);

		ob_start();
		$this->invoke_helper( $settings_page, 'render_field', array( 'login_path', $field, '/login' ) );
		$this->invoke_helper( $settings_page, 'render_field_help', array( 'login_path' ) );
		$output = ob_get_clean();

		$this->assertStringContainsString( 'aria-describedby="alynt-ag-login_path-help"', $output );
		$this->assertStringContainsString( 'id="alynt-ag-login_path-help"', $output );
		$this->assertStringContainsString( 'Use a relative path such as /login.', $output );
	}

	public function test_boolean_field_renders_aria_describedby_and_help() {
		$settings_page = new ALYNT_AG_Settings_Page();
		$field         = array(
			'type'    => 'boolean',
			'default' => false,
		);

		ob_start();
		$this->invoke_helper( $settings_page, 'render_field', array( 'frontend_enabled', $field, false ) );
		$this->invoke_helper( $settings_page, 'render_field_help', array( 'frontend_enabled' ) );
		$output = ob_get_clean();

		$this->assertStringContainsString( 'type="checkbox"', $output );
		$this->assertStringContainsString( 'aria-describedby="alynt-ag-frontend_enabled-help"', $output );
		$this->assertStringContainsString( 'Leave disabled until URLs', $output );
	}

	public function test_field_without_help_does_not_render_description() {
		$settings_page = new ALYNT_AG_Settings_Page();

		ob_start();
		$this->invoke_helper( $settings_page, 'render_field_help', array( 'missing_setting' ) );
		$output = ob_get_clean();

		$this->assertSame( '', $output );
	}
}
