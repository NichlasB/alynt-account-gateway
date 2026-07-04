<?php
/**
 * Frontend route service tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests branded route URL and screen resolution helpers.
 */
class FrontendRoutesTest extends TestCase {

	/**
	 * Test settings.
	 *
	 * @var array<string,mixed>
	 */
	private $settings;

	protected function setUp(): void {
		parent::setUp();

		$this->settings = array(
			'login_path'           => '/login',
			'account_action_base'  => '/account',
			'after_login_redirect' => '/my-account/',
			'registration_enabled' => true,
			'dashboard_enabled'    => true,
			'woocommerce_takeover' => false,
		);

		$_GET    = array();
		$_SERVER = array();
	}

	protected function tearDown(): void {
		$_GET    = array();
		$_SERVER = array();

		parent::tearDown();
	}

	public function test_action_url_maps_known_actions_and_falls_back_to_login() {
		$routes = new ALYNT_AG_Frontend_Routes();

		$this->assertSame( 'https://example.test/login', $routes->action_url( 'login', $this->settings ) );
		$this->assertSame( 'https://example.test/account?action=register', $routes->action_url( 'register', $this->settings ) );
		$this->assertSame( 'https://example.test/account?action=lostpassword', $routes->action_url( 'lostpassword', $this->settings ) );
		$this->assertSame( 'https://example.test/account?action=resetpass', $routes->action_url( 'resetpass', $this->settings ) );
		$this->assertSame( 'https://example.test/login', $routes->action_url( 'unexpected', $this->settings ) );
	}

	public function test_filter_url_helpers_preserve_redirect_and_nonce_behavior() {
		$routes = new ALYNT_AG_Frontend_Routes();

		$this->assertSame(
			'https://example.test/login?redirect_to=https%253A%252F%252Fexample.test%252Fmy-account%252F&reauth=1',
			$routes->login_url( $this->settings, 'https://example.test/my-account/', true )
		);
		$this->assertSame(
			'https://example.test/account?action=lostpassword&redirect_to=https%253A%252F%252Fexample.test%252Fmy-account%252F',
			$routes->lostpassword_url( $this->settings, 'https://example.test/my-account/' )
		);
		$this->assertSame(
			'https://example.test/account?action=logout&_wpnonce=test-nonce&redirect_to=https%253A%252F%252Fexample.test%252Flogin',
			$routes->logout_url( $this->settings, 'https://example.test/login' )
		);
	}

	public function test_screen_resolves_login_action_screens_and_disabled_registration() {
		$routes = new ALYNT_AG_Frontend_Routes();

		$_SERVER['REQUEST_URI'] = '/account?action=register';
		$_GET['action']         = 'register';
		$this->assertSame( 'register', $routes->screen( $this->settings ) );

		$this->settings['registration_enabled'] = false;
		$this->assertSame( 'registration_disabled', $routes->screen( $this->settings ) );

		$_GET['action'] = 'rp';
		$this->assertSame( 'setpassword', $routes->screen( $this->settings ) );

		$_GET['action'] = 'unknown';
		$this->assertSame( 'login', $routes->screen( $this->settings ) );
	}

	public function test_screen_resolves_login_dashboard_and_non_gateway_paths() {
		$routes = new ALYNT_AG_Frontend_Routes();

		$_SERVER['REQUEST_URI'] = '/login';
		$this->assertSame( 'login', $routes->screen( $this->settings ) );

		$_SERVER['REQUEST_URI'] = '/my-account/';
		$this->assertSame( 'dashboard', $routes->screen( $this->settings ) );

		$_SERVER['REQUEST_URI'] = '/not-the-gateway/';
		$this->assertSame( '', $routes->screen( $this->settings ) );
	}

	public function test_woocommerce_takeover_endpoint_resolves_to_dashboard() {
		$woocommerce = new class() {
			public function takeover_enabled( $settings ) {
				return ! empty( $settings['woocommerce_takeover'] );
			}

			public function endpoint_from_path( $path, $settings ) {
				return '/my-account/orders' === untrailingslashit( $path )
					? array(
						'endpoint' => 'orders',
						'value'    => '',
					)
					: array(
						'endpoint' => '',
						'value'    => '',
					);
			}
		};
		$routes      = new ALYNT_AG_Frontend_Routes( $woocommerce );

		$this->settings['woocommerce_takeover'] = true;
		$_SERVER['REQUEST_URI']                 = '/my-account/orders/';

		$this->assertSame( 'dashboard', $routes->screen( $this->settings ) );
	}

	public function test_path_matching_ignores_trailing_slashes() {
		$routes = new ALYNT_AG_Frontend_Routes();

		$this->assertTrue( $routes->paths_match( 'login/', '/login' ) );
		$this->assertFalse( $routes->paths_match( '/login', '/account' ) );
	}
}
