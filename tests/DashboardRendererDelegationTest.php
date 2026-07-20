<?php
/**
 * Dashboard renderer delegation tests.
 *
 * @package Alynt_Account_Gateway
 */

require_once __DIR__ . '/support/class-frontend-dashboard-screen-test-case.php';

/**
 * Records navigation renderer calls.
 */
class ALYNT_AG_Test_Dashboard_Navigation_Renderer {

	/**
	 * Calls in render order.
	 *
	 * @var array<int,string>
	 */
	public $calls = array();

	public function render_actions( $settings ) {
		unset( $settings );
		$this->calls[] = 'actions';
	}

	public function render_offcanvas_menu( $settings ) {
		unset( $settings );
		$this->calls[] = 'offcanvas';
	}

	public function render_footer_menu( $settings ) {
		unset( $settings );
		$this->calls[] = 'footer';
	}
}

/**
 * Records endpoint renderer calls.
 */
class ALYNT_AG_Test_Dashboard_Endpoint_Renderer {

	/**
	 * Calls.
	 *
	 * @var array<int,array<string,mixed>>
	 */
	public $calls = array();

	public function render( $endpoint, $settings, $current_path ) {
		$this->calls[] = compact( 'endpoint', 'settings', 'current_path' );
	}
}

/**
 * Records dashboard module renderer calls.
 */
class ALYNT_AG_Test_Dashboard_Module_Renderer {

	/**
	 * Calls.
	 *
	 * @var array<int,array<string,mixed>>
	 */
	public $calls = array();

	public function render( $user_id, $settings ) {
		$this->calls[] = compact( 'user_id', 'settings' );
	}
}

/**
 * Tests the stable dashboard facade delegates to injected collaborators.
 */
class DashboardRendererDelegationTest extends FrontendDashboardScreenTestCase {

	public function test_dashboard_facade_and_renderers_stay_below_structure_threshold() {
		$files = array(
			'class-frontend-dashboard-screen.php',
			'class-dashboard-navigation-renderer.php',
			'class-dashboard-endpoint-metadata.php',
			'class-dashboard-endpoint-renderer.php',
			'class-dashboard-commerce-renderer.php',
			'class-dashboard-account-renderer.php',
		);

		foreach ( $files as $file ) {
			$contents = file_get_contents( ALYNT_AG_PLUGIN_DIR . 'includes/services/' . $file );
			$this->assertIsString( $contents );
			$this->assertLessThanOrEqual(
				300,
				substr_count( $contents, "\n" ) + 1,
				$file . ' exceeds the 300-line structural threshold.'
			);
		}
	}

	public function test_dashboard_shell_delegates_navigation_regions_in_order() {
		$navigation = new ALYNT_AG_Test_Dashboard_Navigation_Renderer();
		$screen     = new ALYNT_AG_Frontend_Dashboard_Screen(
			new ALYNT_AG_Test_Frontend_Dashboard_Service(),
			new ALYNT_AG_Test_Frontend_Dashboard_WooCommerce(),
			new ALYNT_AG_Test_Frontend_Dashboard_Branding(),
			$navigation
		);

		ob_start();
		$screen->render_dashboard_shell( $this->settings, '/my-account/' );
		ob_end_clean();

		$this->assertSame( array( 'actions', 'offcanvas', 'footer' ), $navigation->calls );
	}

	public function test_base_dashboard_delegates_to_both_overview_module_renderers() {
		$dashboard            = new ALYNT_AG_Test_Frontend_Dashboard_Service();
		$dashboard->available = true;
		$woocommerce          = new ALYNT_AG_Test_Frontend_Dashboard_WooCommerce();
		$endpoint             = new ALYNT_AG_Test_Dashboard_Endpoint_Renderer();
		$commerce             = new ALYNT_AG_Test_Dashboard_Module_Renderer();
		$account              = new ALYNT_AG_Test_Dashboard_Module_Renderer();
		$settings             = array_merge( $this->settings, array( 'woocommerce_takeover' => true ) );
		$screen               = new ALYNT_AG_Frontend_Dashboard_Screen(
			$dashboard,
			$woocommerce,
			new ALYNT_AG_Test_Frontend_Dashboard_Branding(),
			null,
			$endpoint,
			$commerce,
			$account
		);

		ob_start();
		$screen->render_dashboard_screen( $settings, '/my-account/' );
		ob_end_clean();

		$user_id = wp_get_current_user()->ID;
		$this->assertSame( array( compact( 'user_id', 'settings' ) ), $commerce->calls );
		$this->assertSame( array( compact( 'user_id', 'settings' ) ), $account->calls );
		$this->assertSame( array(), $endpoint->calls );
	}

	public function test_non_dashboard_endpoint_delegates_exact_context_only_to_endpoint_renderer() {
		$dashboard              = new ALYNT_AG_Test_Frontend_Dashboard_Service();
		$dashboard->available   = true;
		$woocommerce            = new ALYNT_AG_Test_Frontend_Dashboard_WooCommerce();
		$woocommerce->endpoint = array(
			'endpoint' => 'orders',
			'value'    => '2',
		);
		$endpoint               = new ALYNT_AG_Test_Dashboard_Endpoint_Renderer();
		$commerce               = new ALYNT_AG_Test_Dashboard_Module_Renderer();
		$account                = new ALYNT_AG_Test_Dashboard_Module_Renderer();
		$settings               = array_merge( $this->settings, array( 'woocommerce_takeover' => true ) );
		$current_path           = '/my-account/orders/2/';
		$screen                 = new ALYNT_AG_Frontend_Dashboard_Screen(
			$dashboard,
			$woocommerce,
			new ALYNT_AG_Test_Frontend_Dashboard_Branding(),
			null,
			$endpoint,
			$commerce,
			$account
		);

		ob_start();
		$screen->render_dashboard_screen( $settings, $current_path );
		ob_end_clean();

		$this->assertSame(
			array(
				array(
					'endpoint'     => $woocommerce->endpoint,
					'settings'     => $settings,
					'current_path' => $current_path,
				),
			),
			$endpoint->calls
		);
		$this->assertSame( array(), $commerce->calls );
		$this->assertSame( array(), $account->calls );
	}
}
