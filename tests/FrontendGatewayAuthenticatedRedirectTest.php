<?php
/**
 * Frontend gateway authenticated redirect tests.
 *
 * @package Alynt_Account_Gateway
 */

require_once __DIR__ . '/support/class-frontend-routing-test-case.php';

/**
 * Records gateway rendering attempts.
 */
class ALYNT_AG_Test_Gateway_Renderer_Throws {

	/**
	 * Rendered documents.
	 *
	 * @var array<int,array<string,mixed>>
	 */
	public $documents = array();

	/**
	 * Render a gateway document.
	 *
	 * @param string              $screen       Screen key.
	 * @param array<string,mixed> $settings     Settings.
	 * @param string              $current_path Current path.
	 * @return void
	 */
	public function render_gateway_document( $screen, $settings, $current_path ) {
		$this->documents[] = compact( 'screen', 'settings', 'current_path' );

		throw new RuntimeException( 'rendered:' . $screen );
	}
}

/**
 * Tests auth-state routing at login-equivalent gateway URLs.
 */
class FrontendGatewayAuthenticatedRedirectTest extends FrontendRoutingTestCase {

	/**
	 * Renderer spy.
	 *
	 * @var ALYNT_AG_Test_Gateway_Renderer_Throws
	 */
	private $renderer;

	protected function setUp(): void {
		parent::setUp();

		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings']['administrator_after_login_redirect'] = '/wp-admin/';
		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings']['shop_manager_after_login_redirect']  = '/staff-dashboard/';
		$GLOBALS['alynt_ag_test_throw_on_redirect'] = true;
		$this->renderer = new ALYNT_AG_Test_Gateway_Renderer_Throws();
	}

	protected function tearDown(): void {
		unset( $GLOBALS['alynt_ag_test_current_user_roles'] );

		parent::tearDown();
	}

	public function test_logged_in_customer_is_redirected_from_configured_login_path() {
		$GLOBALS['alynt_ag_test_user_logged_in']    = true;
		$GLOBALS['alynt_ag_test_current_user_roles'] = array( 'customer' );
		$_SERVER['REQUEST_URI'] = '/login/';

		$this->assertGatewayRedirectsTo( 'https://example.test/my-account/' );
	}

	public function test_logged_in_admin_is_redirected_from_configured_account_action_base() {
		$GLOBALS['alynt_ag_test_user_logged_in']    = true;
		$GLOBALS['alynt_ag_test_current_user_roles'] = array( 'administrator' );
		$_SERVER['REQUEST_URI'] = '/account';

		$this->assertGatewayRedirectsTo( 'https://example.test/wp-admin/' );
	}

	public function test_logged_in_shop_manager_is_redirected_from_configured_account_action_base() {
		$GLOBALS['alynt_ag_test_user_logged_in']    = true;
		$GLOBALS['alynt_ag_test_current_user_roles'] = array( 'shop_manager' );
		$_SERVER['REQUEST_URI'] = '/account';

		$this->assertGatewayRedirectsTo( 'https://example.test/staff-dashboard/' );
	}

	public function test_logged_out_bare_account_action_base_redirects_to_configured_login_path() {
		$GLOBALS['alynt_ag_test_user_logged_in'] = false;
		$_SERVER['REQUEST_URI'] = '/account';

		$this->assertGatewayRedirectsTo( 'https://example.test/login' );
	}

	public function test_reauth_request_can_render_login_screen_for_logged_in_user() {
		$GLOBALS['alynt_ag_test_user_logged_in']    = true;
		$GLOBALS['alynt_ag_test_current_user_roles'] = array( 'administrator' );
		$_SERVER['REQUEST_URI'] = '/login/?reauth=1';
		$_GET['reauth'] = '1';

		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage( 'rendered:login' );

		$this->controller()->maybe_render_gateway();
	}

	public function test_explicit_action_screens_still_render_for_logged_in_user() {
		$GLOBALS['alynt_ag_test_user_logged_in']    = true;
		$GLOBALS['alynt_ag_test_current_user_roles'] = array( 'customer' );
		$_SERVER['REQUEST_URI'] = '/account?action=lostpassword';
		$_GET['action'] = 'lostpassword';

		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage( 'rendered:lostpassword' );

		$this->controller()->maybe_render_gateway();
	}

	/**
	 * Assert the gateway redirects to an expected URL.
	 *
	 * @param string $expected Expected URL.
	 * @return void
	 */
	private function assertGatewayRedirectsTo( $expected ) {
		try {
			$this->controller()->maybe_render_gateway();
		} catch ( RuntimeException $exception ) {
			$this->assertSame( 'redirect:' . $expected, $exception->getMessage() );
		}

		$this->assertSame( $expected, $GLOBALS['alynt_ag_test_redirects'][0]['location'] );
		$this->assertSame( array(), $this->renderer->documents );
	}

	/**
	 * Build the gateway controller under test.
	 *
	 * @return ALYNT_AG_Frontend_Gateway_Controller
	 */
	private function controller() {
		return new ALYNT_AG_Frontend_Gateway_Controller(
			new ALYNT_AG_Frontend_Routes(),
			new ALYNT_AG_Frontend_Assets(),
			$this->renderer
		);
	}
}
