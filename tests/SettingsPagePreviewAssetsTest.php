<?php
/**
 * Settings page gateway preview asset tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

require_once ALYNT_AG_PLUGIN_DIR . 'admin/class-settings-page.php';

/**
 * Tests standalone gateway preview asset localization.
 */
class SettingsPagePreviewAssetsTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();

		$GLOBALS['alynt_ag_test_actions']           = array();
		$GLOBALS['alynt_ag_test_enqueued_styles']   = array();
		$GLOBALS['alynt_ag_test_enqueued_scripts']  = array();
		$GLOBALS['alynt_ag_test_localized_scripts'] = array();
	}

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

	public function test_gateway_preview_localizes_password_visibility_status_labels() {
		$settings_page = new ALYNT_AG_Settings_Page();

		$this->invoke_helper( $settings_page, 'enqueue_gateway_preview_assets', array( 'setpassword', array() ) );

		$this->assertCount( 1, $GLOBALS['alynt_ag_test_localized_scripts'] );
		$this->assertSame( 'alynt-ag-frontend', $GLOBALS['alynt_ag_test_localized_scripts'][0]['handle'] );
		$this->assertSame( 'alyntAgFrontend', $GLOBALS['alynt_ag_test_localized_scripts'][0]['object_name'] );
		$this->assertSame( 'Show password', $GLOBALS['alynt_ag_test_localized_scripts'][0]['l10n']['labels']['showPassword'] );
		$this->assertSame( 'Hide password', $GLOBALS['alynt_ag_test_localized_scripts'][0]['l10n']['labels']['hidePassword'] );
		$this->assertSame( 'Password is visible.', $GLOBALS['alynt_ag_test_localized_scripts'][0]['l10n']['labels']['passwordVisible'] );
		$this->assertSame( 'Met', $GLOBALS['alynt_ag_test_localized_scripts'][0]['l10n']['labels']['requirementMet'] );
		$this->assertSame( 'Not met', $GLOBALS['alynt_ag_test_localized_scripts'][0]['l10n']['labels']['requirementNotMet'] );
		$this->assertSame( '%1$d of %2$d requirement met.', $GLOBALS['alynt_ag_test_localized_scripts'][0]['l10n']['labels']['requirementMetSummary'] );
		$this->assertSame( '%1$d of %2$d requirements met.', $GLOBALS['alynt_ag_test_localized_scripts'][0]['l10n']['labels']['requirementsMetSummary'] );
		$this->assertSame( 'Password is hidden.', $GLOBALS['alynt_ag_test_localized_scripts'][0]['l10n']['labels']['passwordHidden'] );
	}

	public function test_register_adds_ajax_preview_route_and_compatibility_routes() {
		$settings_page = new ALYNT_AG_Settings_Page();

		$settings_page->register();

		$admin_init_preview = array_values(
			array_filter(
				$GLOBALS['alynt_ag_test_actions'],
				static function ( $action ) use ( $settings_page ) {
					return 'admin_init' === $action['hook']
						&& array( $settings_page, 'maybe_handle_preview_gateway_request' ) === $action['callback'];
				}
			)
		);

		$admin_post_preview = array_values(
			array_filter(
				$GLOBALS['alynt_ag_test_actions'],
				static function ( $action ) use ( $settings_page ) {
					return 'admin_post_alynt_ag_preview_gateway' === $action['hook']
						&& array( $settings_page, 'handle_preview_gateway' ) === $action['callback'];
				}
			)
		);

		$ajax_preview = array_values(
			array_filter(
				$GLOBALS['alynt_ag_test_actions'],
				static function ( $action ) use ( $settings_page ) {
					return 'wp_ajax_alynt_ag_preview_gateway' === $action['hook']
						&& array( $settings_page, 'handle_preview_gateway' ) === $action['callback'];
				}
			)
		);

		$this->assertCount( 1, $admin_init_preview );
		$this->assertSame( 1, $admin_init_preview[0]['priority'] );
		$this->assertCount( 1, $admin_post_preview );
		$this->assertCount( 1, $ajax_preview );
	}

	public function test_unrelated_admin_request_does_not_build_component_registry() {
		$original_get = $_GET;
		$_GET        = array( 'page' => 'unrelated-settings-page' );

		$settings_page = new ALYNT_AG_Settings_Page();
		$settings_page->maybe_handle_preview_gateway_request();

		$reflection = new ReflectionClass( $settings_page );
		$components = $reflection->getProperty( 'components' );
		if ( PHP_VERSION_ID < 80100 ) {
			$components->setAccessible( true );
		}

		$this->assertNull( $components->getValue( $settings_page ) );

		$_GET = $original_get;
	}

	public function test_gateway_preview_links_use_frontend_preview_route() {
		$settings_page = new ALYNT_AG_Settings_Page();

		ob_start();
		$this->invoke_helper( $settings_page, 'render_gateway_preview_tools' );
		$output = ob_get_clean();

		$this->assertStringContainsString( 'https://example.test/?alynt_ag_preview_gateway=1', $output );
		$this->assertStringContainsString( 'alynt_ag_preview_screen=l', $output );
		$this->assertStringNotContainsString( 'alynt_ag_preview_gateway=login', $output );
		$this->assertStringContainsString( 'alynt_ag_preview_nonce=test-nonce', $output );
		$this->assertStringNotContainsString( 'admin-ajax.php?action=alynt_ag_preview_gateway', $output );
		$this->assertStringNotContainsString( 'options-general.php?page=alynt-account-gateway', $output );
		$this->assertStringNotContainsString( 'admin-post.php?action=alynt_ag_preview_gateway', $output );
	}
}
