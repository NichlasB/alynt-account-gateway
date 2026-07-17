<?php
/**
 * Frontend dashboard screen service tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Test dashboard service.
 */
class ALYNT_AG_Test_Frontend_Dashboard_Service extends ALYNT_AG_Dashboard_Service {

	/**
	 * Links to return.
	 *
	 * @var array<int,array<string,mixed>>
	 */
	public $links = array();

	/**
	 * WooCommerce availability flag.
	 *
	 * @var bool
	 */
	public $available = false;

	/**
	 * Return configured test links.
	 *
	 * @param WP_User             $user     User object.
	 * @param array<string,mixed> $settings Settings.
	 * @return array<int,array<string,mixed>>
	 */
	public function links_for_user( $user, $settings ) {
		return $this->links;
	}

	/**
	 * Return configured WooCommerce availability.
	 *
	 * @return bool
	 */
	public function woocommerce_available() {
		return $this->available;
	}
}

/**
 * Test WooCommerce integration.
 */
class ALYNT_AG_Test_Frontend_Dashboard_WooCommerce extends ALYNT_AG_WooCommerce_Integration {

	/**
	 * Endpoint to return.
	 *
	 * @var array<string,mixed>
	 */
	public $endpoint = array(
		'endpoint' => 'dashboard',
		'value'    => '',
	);

	/**
	 * Whether endpoint rendering succeeds.
	 *
	 * @var bool
	 */
	public $rendered = true;

	/**
	 * Return configured endpoint.
	 *
	 * @param string              $path     Current relative path.
	 * @param array<string,mixed> $settings Settings.
	 * @return array<string,mixed>
	 */
	public function endpoint_from_path( $path, $settings ) {
		return $this->endpoint;
	}

	/**
	 * Return endpoint labels.
	 *
	 * @return array<string,string>
	 */
	public function endpoint_labels() {
		return array(
			'dashboard'           => 'Dashboard',
			'orders'              => 'Orders',
			'view-order'          => 'Order Details',
			'downloads'           => 'Downloads',
			'edit-address'        => 'Addresses',
			'edit-account'        => 'Account Details',
			'payment-methods'     => 'Payment Methods',
			'add-payment-method' => 'Add Payment Method',
			'loyalty-points'      => 'Loyalty Points',
		);
	}

	/**
	 * Render configured endpoint.
	 *
	 * @param string $endpoint Endpoint.
	 * @param string $value    Endpoint value.
	 * @return bool
	 */
	public function render_endpoint( $endpoint, $value = '' ) {
		if ( $this->rendered ) {
			echo '<div class="wc-endpoint-output">' . esc_html( $endpoint . ':' . $value ) . '</div>';
		}

		return $this->rendered;
	}
}

/**
 * Test frontend branding helper.
 */
class ALYNT_AG_Test_Frontend_Dashboard_Branding extends ALYNT_AG_Frontend_Branding {

	/**
	 * Return stable style output.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return string
	 */
	public function style_attribute( $settings ) {
		return '--test-color:#123;';
	}

	/**
	 * Render stable brand output.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	public function render_brand_block( $settings ) {
		echo '<div class="agw-brand"><div class="agw-brand__name">Test Store</div></div>';
	}
}

/**
 * Tests the frontend dashboard screen.
 */
class FrontendDashboardScreenTest extends TestCase {

	/**
	 * Test settings.
	 *
	 * @var array<string,mixed>
	 */
	private $settings;

	protected function setUp(): void {
		parent::setUp();

		$GLOBALS['alynt_ag_test_is_rtl'] = false;
		$this->settings = array(
			'after_login_redirect'  => '/my-account/',
			'login_path'            => '/login',
			'woocommerce_takeover'  => false,
			'dashboard_custom_links' => '[]',
			'dashboard_offcanvas_enabled' => false,
			'dashboard_offcanvas_menu_id' => 0,
			'dashboard_footer_menu_enabled' => false,
			'dashboard_footer_menu_id' => 0,
			'woocommerce_hidden_menu_items' => array(),
		);
	}

	protected function tearDown(): void {
		unset( $GLOBALS['alynt_ag_test_is_rtl'] );

		parent::tearDown();
	}

	public function test_render_dashboard_shell_outputs_brand_logout_hero_and_links() {
		$dashboard = new ALYNT_AG_Test_Frontend_Dashboard_Service();
		$dashboard->links = array(
			array(
				'label'  => 'Account Details',
				'url'    => 'https://example.test/my-account/edit-account/',
				'icon'   => 'user',
				'target' => '_self',
			),
			array(
				'label'  => 'Support',
				'url'    => 'https://example.test/support/',
				'icon'   => 'help',
				'target' => '_blank',
			),
		);
		$screen = new ALYNT_AG_Frontend_Dashboard_Screen(
			$dashboard,
			new ALYNT_AG_Test_Frontend_Dashboard_WooCommerce(),
			new ALYNT_AG_Test_Frontend_Dashboard_Branding()
		);

		ob_start();
		$screen->render_dashboard_shell( $this->settings, '/my-account/' );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'class="alynt-ag-gateway agw-dashboard"', $html );
		$this->assertStringContainsString( 'data-agw-screen="dashboard"', $html );
		$this->assertStringContainsString( 'dir="ltr"', $html );
		$this->assertStringContainsString( 'style="--test-color:#123;"', $html );
		$this->assertStringContainsString( 'Test Store', $html );
		$this->assertStringContainsString( 'class="agw-dashboard-actions"', $html );
		$this->assertStringContainsString( 'class="agw-dashboard-action agw-dashboard-action--home"', $html );
		$this->assertStringContainsString( 'class="agw-dashboard__logout agw-dashboard-action agw-dashboard-action--logout"', $html );
		$this->assertStringContainsString( 'aria-label="Go to homepage"', $html );
		$this->assertStringContainsString( 'aria-label="Log out"', $html );
		$this->assertStringNotContainsString( 'data-agw-offcanvas-open', $html );
		$this->assertStringNotContainsString( 'id="agw-dashboard-offcanvas"', $html );
		$this->assertStringNotContainsString( 'class="agw-dashboard-footer"', $html );
		$this->assertStringContainsString( 'redirect_to=https%3A%2F%2Fexample.test%2Flogin', $html );
		$this->assertStringContainsString( 'Account Dashboard', $html );
		$this->assertStringContainsString( 'Welcome, Damon', $html );
		$this->assertStringNotContainsString( 'Welcome, Damon Paulo', $html );
		$this->assertStringContainsString( 'damon@example.test', $html );
		$this->assertStringContainsString( 'Account Details', $html );
		$this->assertStringContainsString( 'Support', $html );
		$this->assertStringContainsString( 'target="_blank" rel="noopener noreferrer"', $html );
		$this->assertStringContainsString( 'opens in a new tab', $html );
	}

	public function test_render_dashboard_shell_outputs_offcanvas_menu_when_enabled() {
		$screen   = new ALYNT_AG_Frontend_Dashboard_Screen(
			new ALYNT_AG_Test_Frontend_Dashboard_Service(),
			new ALYNT_AG_Test_Frontend_Dashboard_WooCommerce(),
			new ALYNT_AG_Test_Frontend_Dashboard_Branding()
		);
		$settings = array_merge(
			$this->settings,
			array(
				'dashboard_offcanvas_enabled' => true,
				'dashboard_offcanvas_menu_id' => 123,
			)
		);

		ob_start();
		$screen->render_dashboard_shell( $settings, '/my-account/' );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'data-agw-offcanvas-open', $html );
		$this->assertStringContainsString( 'aria-controls="agw-dashboard-offcanvas"', $html );
		$this->assertStringContainsString( 'aria-expanded="false"', $html );
		$this->assertStringContainsString( 'id="agw-dashboard-offcanvas"', $html );
		$this->assertStringContainsString( 'aria-hidden="true"', $html );
		$this->assertStringContainsString( 'role="dialog" aria-modal="true"', $html );
		$this->assertStringContainsString( 'data-agw-offcanvas-close', $html );
		$this->assertStringContainsString( 'class="agw-offcanvas__menu"', $html );
		$this->assertStringContainsString( 'Shop', $html );
		$this->assertStringContainsString( 'Contact', $html );
	}

	public function test_render_dashboard_shell_outputs_independent_footer_menu_when_enabled() {
		$screen   = new ALYNT_AG_Frontend_Dashboard_Screen(
			new ALYNT_AG_Test_Frontend_Dashboard_Service(),
			new ALYNT_AG_Test_Frontend_Dashboard_WooCommerce(),
			new ALYNT_AG_Test_Frontend_Dashboard_Branding()
		);
		$settings = array_merge(
			$this->settings,
			array(
				'dashboard_footer_menu_enabled' => true,
				'dashboard_footer_menu_id'      => 456,
			)
		);

		ob_start();
		$screen->render_dashboard_shell( $settings, '/my-account/' );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'class="agw-dashboard-footer"', $html );
		$this->assertStringContainsString( 'aria-label="Dashboard footer navigation"', $html );
		$this->assertStringContainsString( 'class="agw-dashboard-footer__menu"', $html );
		$this->assertStringContainsString( 'Shop', $html );
		$this->assertStringContainsString( 'Contact', $html );
		$this->assertStringNotContainsString( 'data-agw-offcanvas-open', $html );
	}

	public function test_render_dashboard_shell_uses_rtl_direction_when_site_is_rtl() {
		$GLOBALS['alynt_ag_test_is_rtl'] = true;
		$screen                         = new ALYNT_AG_Frontend_Dashboard_Screen(
			new ALYNT_AG_Test_Frontend_Dashboard_Service(),
			new ALYNT_AG_Test_Frontend_Dashboard_WooCommerce(),
			new ALYNT_AG_Test_Frontend_Dashboard_Branding()
		);

		ob_start();
		$screen->render_dashboard_shell( $this->settings, '/my-account/' );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'data-agw-screen="dashboard"', $html );
		$this->assertStringContainsString( 'dir="rtl"', $html );
	}

	public function test_render_dashboard_shell_marks_current_account_link() {
		$dashboard = new ALYNT_AG_Test_Frontend_Dashboard_Service();
		$dashboard->links = array(
			array(
				'label'  => 'Account Details',
				'url'    => 'https://example.test/my-account/edit-account/',
				'icon'   => 'user',
				'target' => '_self',
			),
			array(
				'label'  => 'Support',
				'url'    => 'https://example.test/support/',
				'icon'   => 'help',
				'target' => '_self',
			),
		);
		$screen = new ALYNT_AG_Frontend_Dashboard_Screen(
			$dashboard,
			new ALYNT_AG_Test_Frontend_Dashboard_WooCommerce(),
			new ALYNT_AG_Test_Frontend_Dashboard_Branding()
		);

		ob_start();
		$screen->render_dashboard_shell( $this->settings, '/my-account/edit-account/' );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'href="https://example.test/my-account/edit-account/" target="_self" aria-current="page"', $html );
		$this->assertStringContainsString( 'href="https://example.test/support/" target="_self"', $html );
		$this->assertSame( 1, substr_count( $html, 'aria-current="page"' ) );
	}

	public function test_render_dashboard_screen_outputs_woocommerce_unavailable_warning() {
		$dashboard = new ALYNT_AG_Test_Frontend_Dashboard_Service();
		$screen    = new ALYNT_AG_Frontend_Dashboard_Screen(
			$dashboard,
			new ALYNT_AG_Test_Frontend_Dashboard_WooCommerce(),
			new ALYNT_AG_Test_Frontend_Dashboard_Branding()
		);
		$settings = array_merge(
			$this->settings,
			array(
				'woocommerce_takeover' => true,
			)
		);

		ob_start();
		$screen->render_dashboard_screen( $settings, '/my-account/' );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'WooCommerce account takeover is enabled, but WooCommerce is not active.', $html );
		$this->assertStringContainsString( 'role="alert" aria-live="assertive" aria-atomic="true"', $html );
	}

	public function test_render_dashboard_screen_outputs_woocommerce_endpoint_content() {
		$dashboard            = new ALYNT_AG_Test_Frontend_Dashboard_Service();
		$dashboard->available = true;
		$woocommerce          = new ALYNT_AG_Test_Frontend_Dashboard_WooCommerce();
		$woocommerce->endpoint = array(
			'endpoint' => 'orders',
			'value'    => '2',
		);
		$screen = new ALYNT_AG_Frontend_Dashboard_Screen(
			$dashboard,
			$woocommerce,
			new ALYNT_AG_Test_Frontend_Dashboard_Branding()
		);
		$settings = array_merge(
			$this->settings,
			array(
				'woocommerce_takeover' => true,
			)
		);

		ob_start();
		$screen->render_dashboard_screen( $settings, '/my-account/orders/2/' );
		$html = ob_get_clean();

		$this->assertStringContainsString( '<h2 id="agw-dashboard-content-title">', $html );
		$this->assertStringContainsString( 'Orders', $html );
		$this->assertStringContainsString( 'class="agw-dashboard-section-actions"', $html );
		$this->assertStringContainsString( 'href="/my-account/edit-address/"', $html );
		$this->assertStringContainsString( 'href="/my-account/edit-account/"', $html );
		$this->assertStringContainsString( 'class="agw-dashboard-guidance"', $html );
		$this->assertStringContainsString( 'Order History', $html );
		$this->assertStringContainsString( 'Track purchase status', $html );
		$this->assertStringContainsString( 'class="agw-dashboard-affordances"', $html );
		$this->assertStringContainsString( 'No orders yet?', $html );
		$this->assertStringContainsString( 'Once you place an order', $html );
		$this->assertStringContainsString( 'Manage addresses', $html );
		$this->assertStringContainsString( 'href="/my-account/edit-address/"', $html );
		$this->assertStringContainsString( '<div class="wc-endpoint-output">orders:2</div>', $html );
		$this->assertStringNotContainsString( 'This account section is not available.', $html );
	}

	public function test_render_dashboard_screen_outputs_payment_methods_guidance() {
		$dashboard            = new ALYNT_AG_Test_Frontend_Dashboard_Service();
		$dashboard->available = true;
		$woocommerce          = new ALYNT_AG_Test_Frontend_Dashboard_WooCommerce();
		$woocommerce->endpoint = array(
			'endpoint' => 'payment-methods',
			'value'    => '',
		);
		$screen = new ALYNT_AG_Frontend_Dashboard_Screen(
			$dashboard,
			$woocommerce,
			new ALYNT_AG_Test_Frontend_Dashboard_Branding()
		);
		$settings = array_merge(
			$this->settings,
			array(
				'woocommerce_takeover' => true,
			)
		);

		ob_start();
		$screen->render_dashboard_screen( $settings, '/my-account/payment-methods/' );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'Payment Methods', $html );
		$this->assertStringContainsString( 'class="agw-dashboard-section-actions"', $html );
		$this->assertStringContainsString( 'Saved Payment Methods', $html );
		$this->assertStringContainsString( 'Manage saved payment methods', $html );
		$this->assertStringContainsString( 'No saved payment methods?', $html );
		$this->assertStringContainsString( 'Add payment method', $html );
		$this->assertStringContainsString( 'href="/my-account/add-payment-method/"', $html );
		$this->assertStringContainsString( 'href="/my-account/edit-account/"', $html );
		$this->assertStringContainsString( '<div class="wc-endpoint-output">payment-methods:</div>', $html );
	}

	public function test_render_dashboard_screen_outputs_view_order_section_actions() {
		$dashboard            = new ALYNT_AG_Test_Frontend_Dashboard_Service();
		$dashboard->available = true;
		$woocommerce          = new ALYNT_AG_Test_Frontend_Dashboard_WooCommerce();
		$woocommerce->endpoint = array(
			'endpoint' => 'view-order',
			'value'    => '1001',
		);
		$screen = new ALYNT_AG_Frontend_Dashboard_Screen(
			$dashboard,
			$woocommerce,
			new ALYNT_AG_Test_Frontend_Dashboard_Branding()
		);
		$settings = array_merge(
			$this->settings,
			array(
				'woocommerce_takeover' => true,
			)
		);

		ob_start();
		$screen->render_dashboard_screen( $settings, '/my-account/view-order/1001/' );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'Order Details', $html );
		$this->assertStringContainsString( 'class="agw-dashboard-section-actions"', $html );
		$this->assertStringContainsString( 'Back to orders', $html );
		$this->assertStringContainsString( 'href="/my-account/orders/"', $html );
		$this->assertStringContainsString( 'Manage addresses', $html );
		$this->assertStringContainsString( 'href="/my-account/edit-address/"', $html );
		$this->assertStringContainsString( '<div class="wc-endpoint-output">view-order:1001</div>', $html );
	}

	public function test_render_dashboard_screen_outputs_downloads_edge_state_affordance() {
		$dashboard            = new ALYNT_AG_Test_Frontend_Dashboard_Service();
		$dashboard->available = true;
		$woocommerce          = new ALYNT_AG_Test_Frontend_Dashboard_WooCommerce();
		$woocommerce->endpoint = array(
			'endpoint' => 'downloads',
			'value'    => '',
		);
		$screen = new ALYNT_AG_Frontend_Dashboard_Screen(
			$dashboard,
			$woocommerce,
			new ALYNT_AG_Test_Frontend_Dashboard_Branding()
		);
		$settings = array_merge(
			$this->settings,
			array(
				'woocommerce_takeover' => true,
			)
		);

		ob_start();
		$screen->render_dashboard_screen( $settings, '/my-account/downloads/' );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'Downloads', $html );
		$this->assertStringContainsString( 'No downloads available?', $html );
		$this->assertStringContainsString( 'Downloadable files appear here', $html );
		$this->assertStringContainsString( 'View orders', $html );
		$this->assertStringContainsString( 'href="/my-account/orders/"', $html );
	}

	public function test_render_dashboard_screen_skips_guidance_for_custom_endpoint() {
		$dashboard            = new ALYNT_AG_Test_Frontend_Dashboard_Service();
		$dashboard->available = true;
		$woocommerce          = new ALYNT_AG_Test_Frontend_Dashboard_WooCommerce();
		$woocommerce->endpoint = array(
			'endpoint' => 'loyalty-points',
			'value'    => '',
		);
		$screen = new ALYNT_AG_Frontend_Dashboard_Screen(
			$dashboard,
			$woocommerce,
			new ALYNT_AG_Test_Frontend_Dashboard_Branding()
		);
		$settings = array_merge(
			$this->settings,
			array(
				'woocommerce_takeover' => true,
			)
		);

		ob_start();
		$screen->render_dashboard_screen( $settings, '/my-account/loyalty-points/' );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'Loyalty Points', $html );
		$this->assertStringNotContainsString( 'class="agw-dashboard-section-actions"', $html );
		$this->assertStringNotContainsString( 'class="agw-dashboard-guidance"', $html );
		$this->assertStringNotContainsString( 'class="agw-dashboard-affordances"', $html );
		$this->assertStringContainsString( '<div class="wc-endpoint-output">loyalty-points:</div>', $html );
	}

	public function test_render_dashboard_screen_outputs_woocommerce_overview_on_base_dashboard() {
		$dashboard            = new ALYNT_AG_Test_Frontend_Dashboard_Service();
		$dashboard->available = true;
		$woocommerce          = new ALYNT_AG_Test_Frontend_Dashboard_WooCommerce();
		$screen               = new ALYNT_AG_Frontend_Dashboard_Screen(
			$dashboard,
			$woocommerce,
			new ALYNT_AG_Test_Frontend_Dashboard_Branding()
		);
		$settings = array_merge(
			$this->settings,
			array(
				'woocommerce_takeover' => true,
			)
		);

		ob_start();
		$screen->render_dashboard_screen( $settings, '/my-account/' );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'class="agw-dashboard-overview"', $html );
		$this->assertStringContainsString( 'Customer Account', $html );
		$this->assertStringContainsString( 'Everything for your orders in one place', $html );
		$this->assertStringContainsString( 'href="/my-account/orders/"', $html );
		$this->assertStringContainsString( 'href="/my-account/edit-address/"', $html );
		$this->assertStringContainsString( 'href="/my-account/edit-account/"', $html );
		$this->assertStringNotContainsString( 'class="agw-dashboard-content"', $html );
	}

	public function test_woocommerce_overview_omits_hidden_shortcuts_without_empty_actions_markup() {
		$dashboard            = new ALYNT_AG_Test_Frontend_Dashboard_Service();
		$dashboard->available = true;
		$screen               = new ALYNT_AG_Frontend_Dashboard_Screen(
			$dashboard,
			new ALYNT_AG_Test_Frontend_Dashboard_WooCommerce(),
			new ALYNT_AG_Test_Frontend_Dashboard_Branding()
		);
		$settings = array_merge(
			$this->settings,
			array(
				'woocommerce_takeover'          => true,
				'woocommerce_hidden_menu_items' => array( 'orders', 'edit-address', 'edit-account' ),
			)
		);

		ob_start();
		$screen->render_dashboard_screen( $settings, '/my-account/' );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'agw-dashboard-overview--without-actions', $html );
		$this->assertStringNotContainsString( 'class="agw-dashboard-overview__actions"', $html );
		$this->assertStringNotContainsString( 'href="/my-account/orders/"', $html );
		$this->assertStringNotContainsString( 'href="/my-account/edit-address/"', $html );
		$this->assertStringNotContainsString( 'href="/my-account/edit-account/"', $html );
	}

	public function test_render_dashboard_screen_outputs_endpoint_fallback_when_render_fails() {
		$dashboard            = new ALYNT_AG_Test_Frontend_Dashboard_Service();
		$dashboard->available = true;
		$woocommerce          = new ALYNT_AG_Test_Frontend_Dashboard_WooCommerce();
		$woocommerce->endpoint = array(
			'endpoint' => 'orders',
			'value'    => '',
		);
		$woocommerce->rendered = false;
		$screen = new ALYNT_AG_Frontend_Dashboard_Screen(
			$dashboard,
			$woocommerce,
			new ALYNT_AG_Test_Frontend_Dashboard_Branding()
		);
		$settings = array_merge(
			$this->settings,
			array(
				'woocommerce_takeover' => true,
			)
		);

		ob_start();
		$screen->render_dashboard_screen( $settings, '/my-account/orders/' );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'class="agw-dashboard-empty"', $html );
		$this->assertStringContainsString( 'role="status" aria-live="polite" aria-atomic="true"', $html );
		$this->assertStringContainsString( 'Account section unavailable', $html );
		$this->assertStringContainsString( 'This area is not ready yet', $html );
		$this->assertStringContainsString( 'WooCommerce did not return content for Orders.', $html );
		$this->assertStringContainsString( 'Back to dashboard', $html );
		$this->assertStringContainsString( 'href="/my-account/"', $html );
		$this->assertStringContainsString( 'Manage account details', $html );
		$this->assertStringContainsString( 'href="/my-account/edit-account/"', $html );
		$this->assertStringNotContainsString( 'This account section is not available.', $html );
	}
}
