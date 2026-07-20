<?php
/**
 * Settings page tab guidance tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

require_once ALYNT_AG_PLUGIN_DIR . 'admin/class-settings-page.php';

/**
 * Tests tab-level setup guidance on the settings page.
 */
class SettingsPageTabGuidanceTest extends TestCase {

	/**
	 * Invoke a private settings page helper.
	 *
	 * @param ALYNT_AG_Settings_Page $settings_page Settings page instance.
	 * @param string                 $method        Method name.
	 * @param array<int,mixed>       $args          Method arguments.
	 * @return mixed
	 */
	private function invoke_helper( $settings_page, $method, $args = array() ) {
		return alynt_ag_test_invoke_settings_page_method( $settings_page, $method, $args );
	}

	public function test_guidance_exists_for_every_settings_tab() {
		$settings_page = new ALYNT_AG_Settings_Page();
		$guidance      = $this->invoke_helper( $settings_page, 'settings_tab_guidance' );

		$this->assertSame(
			array_keys( ALYNT_AG_Settings_Schema::tabs() ),
			array_keys( $guidance )
		);

		foreach ( $guidance as $tab => $item ) {
			$this->assertNotEmpty( $tab );
			$this->assertNotEmpty( $item['title'] );
			$this->assertNotEmpty( $item['description'] );
			$this->assertCount( 3, $item['steps'] );
		}
	}

	public function test_registration_guidance_renders_next_security_step() {
		$settings_page = new ALYNT_AG_Settings_Page();

		ob_start();
		$this->invoke_helper( $settings_page, 'render_tab_guidance', array( 'registration' ) );
		$output = ob_get_clean();

		$this->assertStringContainsString( 'Keep account creation intentional.', $output );
		$this->assertStringContainsString( 'Confirm the Terms and Privacy paths point to real public pages.', $output );
		$this->assertStringContainsString( 'Review Security', $output );
		$this->assertStringContainsString( 'tab=security', $output );
	}

	public function test_invalid_tab_guidance_falls_back_to_general() {
		$settings_page = new ALYNT_AG_Settings_Page();

		ob_start();
		$this->invoke_helper( $settings_page, 'render_tab_guidance', array( 'missing_tab' ) );
		$output = ob_get_clean();

		$this->assertStringContainsString( 'Start safely before changing public account screens.', $output );
		$this->assertStringContainsString( 'Review URLs', $output );
		$this->assertStringContainsString( 'tab=urls', $output );
	}
}
