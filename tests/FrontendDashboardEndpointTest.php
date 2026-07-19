<?php
/**
 * Frontend dashboard screen tests.
 *
 * @package Alynt_Account_Gateway
 */

require_once __DIR__ . '/support/class-frontend-dashboard-screen-test-case.php';

/**
 * Tests dashboard endpoint rendering and fallbacks.
 */
class FrontendDashboardEndpointTest extends FrontendDashboardScreenTestCase {

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
		$dashboard->links     = array(
			array(
				'label'  => 'Customer Support',
				'url'    => 'https://support.example.test/',
				'icon'   => 'help',
				'target' => '_blank',
			),
		);
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
		$this->assertStringContainsString( 'class="agw-dashboard-section agw-dashboard-recent-orders"', $html );
		$this->assertStringContainsString( 'Your recent orders will appear here after your first purchase.', $html );
		$this->assertStringContainsString( 'class="agw-dashboard-section agw-dashboard-downloads"', $html );
		$this->assertStringContainsString( 'Your available files will appear here after a downloadable purchase.', $html );
		$this->assertStringContainsString( 'class="agw-dashboard-section agw-dashboard-addresses"', $html );
		$this->assertStringContainsString( 'No billing address is saved yet.', $html );
		$this->assertStringContainsString( 'No shipping address is saved yet.', $html );
		$this->assertStringContainsString( 'class="agw-dashboard-section agw-dashboard-account-details"', $html );
		$this->assertStringContainsString( 'Details ready', $html );
		$this->assertStringContainsString( 'Customer since', $html );
		$this->assertStringContainsString( 'class="agw-dashboard-section agw-dashboard-payment-methods"', $html );
		$this->assertStringContainsString( 'Saved payment methods will appear here when your payment provider supports secure account storage.', $html );
		$this->assertStringContainsString( 'class="agw-dashboard-grid"', $html );
		$this->assertStringContainsString( 'href="https://support.example.test/" target="_blank" rel="noopener noreferrer"', $html );
		$this->assertStringContainsString( 'agw-dashboard-link__icon--help', $html );
		$this->assertStringContainsString( 'Customer Support', $html );
		$this->assertStringContainsString( 'opens in a new tab', $html );
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
