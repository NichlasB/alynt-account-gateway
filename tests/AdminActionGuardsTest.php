<?php
/**
 * Admin action guard tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests capability and nonce boundaries before admin side effects.
 */
class AdminActionGuardsTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		$GLOBALS['alynt_ag_test_user_caps'] = array();
		$GLOBALS['alynt_ag_test_admin_referer_checks'] = array();
		$GLOBALS['alynt_ag_test_db_inserts'] = array();
		$GLOBALS['alynt_ag_test_db_updates'] = array();
		$GLOBALS['alynt_ag_test_mail'] = array();
		$GLOBALS['alynt_ag_test_remote_posts'] = array();
		$GLOBALS['alynt_ag_test_redirects'] = array();
		$GLOBALS['alynt_ag_test_options'] = array();
		$_GET = array();
		$_POST = array();
		$_FILES = array();
		unset( $GLOBALS['alynt_ag_test_admin_nonce_valid'] );
	}

	protected function tearDown(): void {
		unset( $GLOBALS['alynt_ag_test_admin_nonce_valid'] );
		parent::tearDown();
	}

	/**
	 * @dataProvider guarded_action_provider
	 */
	public function test_admin_actions_reject_unauthorized_callers_before_nonce_and_side_effects( $class_name, $method, $request ) {
		$this->set_request( $request );

		try {
			( new $class_name( $this->components() ) )->$method();
			$this->fail( 'Expected unauthorized admin action to stop.' );
		} catch ( RuntimeException $exception ) {
			$this->assertStringContainsString( 'permission', strtolower( $exception->getMessage() ) );
		}

		$this->assertSame( array(), $GLOBALS['alynt_ag_test_admin_referer_checks'] );
		$this->assert_no_side_effects();
	}

	/**
	 * @dataProvider guarded_action_provider
	 */
	public function test_admin_actions_reject_invalid_nonce_before_side_effects( $class_name, $method, $request ) {
		$GLOBALS['alynt_ag_test_user_caps'] = array( 'manage_options' );
		$GLOBALS['alynt_ag_test_admin_nonce_valid'] = false;
		$this->set_request( $request );

		try {
			( new $class_name( $this->components() ) )->$method();
			$this->fail( 'Expected invalid admin nonce to stop.' );
		} catch ( RuntimeException $exception ) {
			$this->assertStringContainsString( 'invalid admin nonce', strtolower( $exception->getMessage() ) );
		}

		$this->assertCount( 1, $GLOBALS['alynt_ag_test_admin_referer_checks'] );
		$this->assert_no_side_effects();
	}

	public function guarded_action_provider() {
		return array(
			'email preview' => array( ALYNT_AG_Settings_Page_Messaging_Actions::class, 'handle_preview_email', array( 'get' => array( 'template' => 'registration_confirmation' ) ) ),
			'test email' => array( ALYNT_AG_Settings_Page_Messaging_Actions::class, 'handle_test_email', array( 'post' => array( 'template' => 'registration_confirmation', 'recipient' => 'admin@example.test' ) ) ),
			'test webhook' => array( ALYNT_AG_Settings_Page_Messaging_Actions::class, 'handle_test_webhook', array() ),
			'settings export' => array( ALYNT_AG_Settings_Page_Settings_Transfer::class, 'handle_export_settings', array() ),
			'settings import' => array( ALYNT_AG_Settings_Page_Settings_Transfer::class, 'handle_import_settings', array( 'files' => array( 'settings_file' => array() ) ) ),
			'restore defaults' => array( ALYNT_AG_Settings_Page_Settings_Transfer::class, 'handle_restore_tab_defaults', array( 'post' => array( 'tab' => 'general' ) ) ),
			'diagnostics export' => array( ALYNT_AG_Settings_Page_Diagnostics_Tools::class, 'handle_export_diagnostics', array() ),
			'diagnostics clear' => array( ALYNT_AG_Settings_Page_Diagnostics_Tools::class, 'handle_clear_diagnostics', array() ),
			'verification review' => array( ALYNT_AG_Settings_Page_Security_Actions::class, 'handle_review_verification', array( 'post' => array( 'log_id' => '7', 'decision' => 'monitor' ) ) ),
			'provider test' => array( ALYNT_AG_Settings_Page_Security_Actions::class, 'handle_test_security_provider', array( 'post' => array( 'provider' => 'turnstile' ) ) ),
		);
	}

	private function set_request( $request ) {
		$_GET   = isset( $request['get'] ) ? $request['get'] : array();
		$_POST  = isset( $request['post'] ) ? $request['post'] : array();
		$_FILES = isset( $request['files'] ) ? $request['files'] : array();
	}

	private function components() {
		return new class() {
			public function call( $method, $arguments ) {
				unset( $method, $arguments );
				throw new RuntimeException( 'Unexpected component delegation.' );
			}
		};
	}

	private function assert_no_side_effects() {
		$this->assertSame( array(), $GLOBALS['alynt_ag_test_db_inserts'] );
		$this->assertSame( array(), $GLOBALS['alynt_ag_test_db_updates'] );
		$this->assertSame( array(), $GLOBALS['alynt_ag_test_mail'] );
		$this->assertSame( array(), $GLOBALS['alynt_ag_test_remote_posts'] );
		$this->assertSame( array(), $GLOBALS['alynt_ag_test_redirects'] );
		$this->assertSame( array(), $GLOBALS['alynt_ag_test_options'] );
	}
}
