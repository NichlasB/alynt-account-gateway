<?php
/**
 * Settings page admin notice tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

require_once ALYNT_AG_PLUGIN_DIR . 'admin/class-settings-page.php';

/**
 * Tests settings-page notice routing and rendering.
 */
class SettingsPageAdminNoticesTest extends TestCase {

	/**
	 * Render one notice.
	 *
	 * @param string $notice        Notice key.
	 * @param int    $ignored_count Ignored import key count.
	 * @return string
	 */
	private function render_notice( $notice, $ignored_count = 0 ) {
		$_GET['alynt_ag_notice']         = $notice;
		$_GET['alynt_ag_import_ignored'] = (string) $ignored_count;

		ob_start();
		alynt_ag_test_invoke_settings_page_method(
			new ALYNT_AG_Settings_Page(),
			'render_admin_notice'
		);
		$output = (string) ob_get_clean();

		unset( $_GET['alynt_ag_notice'], $_GET['alynt_ag_import_ignored'] );

		return $output;
	}

	public function test_standard_notice_uses_definition_type_and_message() {
		$output = $this->render_notice( 'email_test_sent' );

		$this->assertStringContainsString( 'notice-success', $output );
		$this->assertStringContainsString( 'Test email sent.', $output );
	}

	public function test_provider_notice_uses_provider_definition() {
		$output = $this->render_notice( 'reoon_check_missing' );

		$this->assertStringContainsString( 'notice-warning', $output );
		$this->assertStringContainsString( 'Save a Reoon API key', $output );
	}

	public function test_ignored_import_notice_includes_sanitized_count() {
		$output = $this->render_notice( 'settings_imported_with_ignored_keys', 3 );

		$this->assertStringContainsString( 'notice-warning', $output );
		$this->assertStringContainsString( 'Unrecognized setting keys ignored: 3.', $output );
	}

	public function test_ignored_import_notice_uses_singular_key_copy() {
		$output = $this->render_notice( 'settings_imported_with_ignored_keys', 1 );

		$this->assertStringContainsString( 'Unrecognized setting key ignored: 1.', $output );
	}

	public function test_unknown_notice_renders_nothing() {
		$this->assertSame( '', $this->render_notice( 'unknown' ) );
	}
}
