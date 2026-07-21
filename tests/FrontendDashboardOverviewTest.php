<?php
/**
 * Frontend dashboard screen tests.
 *
 * @package Alynt_Account_Gateway
 */

require_once __DIR__ . '/support/class-frontend-dashboard-screen-test-case.php';

/**
 * Tests WooCommerce dashboard overview modules.
 */
class FrontendDashboardOverviewTest extends FrontendDashboardScreenTestCase {

	public function test_woocommerce_dashboard_recent_orders_render_normalized_rows() {
		$dashboard            = new ALYNT_AG_Test_Frontend_Dashboard_Service();
		$dashboard->available = true;
		$woocommerce          = new ALYNT_AG_Test_Frontend_Dashboard_WooCommerce();
		$woocommerce->recent_orders = array(
			array(
				'id'     => 42,
				'number' => '1042',
				'status' => 'Processing',
				'date'   => 'January 1, 2024',
				'total'  => '£42.00',
			),
		);
		$screen = new ALYNT_AG_Frontend_Dashboard_Screen(
			$dashboard,
			$woocommerce,
			new ALYNT_AG_Test_Frontend_Dashboard_Branding()
		);
		$settings = array_merge(
			$this->settings,
			array( 'woocommerce_takeover' => true )
		);

		ob_start();
		$screen->render_dashboard_screen( $settings, '/my-account/' );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'Recent Orders', $html );
		$this->assertStringContainsString( 'View all orders', $html );
		$this->assertStringContainsString( 'href="/my-account/view-order/42/"', $html );
		$this->assertStringContainsString( 'Order #1042', $html );
		$this->assertStringContainsString( 'January 1, 2024', $html );
		$this->assertStringContainsString( 'Processing', $html );
		$this->assertStringContainsString( '£42.00', $html );
		$this->assertStringNotContainsString( 'Your recent orders will appear here', $html );
	}

	public function test_woocommerce_dashboard_available_downloads_render_normalized_rows() {
		$dashboard            = new ALYNT_AG_Test_Frontend_Dashboard_Service();
		$dashboard->available = true;
		$woocommerce          = new ALYNT_AG_Test_Frontend_Dashboard_WooCommerce();
		$woocommerce->available_downloads = array(
			array(
				'name'         => 'Digital Guide',
				'product_name' => 'Complete Course',
				'url'          => 'https://example.test/?download_file=42&key=private',
				'remaining'    => 2,
				'expires'      => 'August 1, 2026',
			),
			array(
				'name'         => 'Lifetime Reference',
				'product_name' => 'Lifetime Reference',
				'url'          => 'https://example.test/?download_file=43&key=lifetime',
				'remaining'    => null,
				'expires'      => '',
			),
			array(
				'name'         => 'Single Download',
				'product_name' => 'Single Download',
				'url'          => 'https://example.test/?download_file=44&key=single',
				'remaining'    => 1,
				'expires'      => '',
			),
		);
		$screen = new ALYNT_AG_Frontend_Dashboard_Screen(
			$dashboard,
			$woocommerce,
			new ALYNT_AG_Test_Frontend_Dashboard_Branding()
		);
		$settings = array_merge(
			$this->settings,
			array( 'woocommerce_takeover' => true )
		);

		ob_start();
		$screen->render_dashboard_screen( $settings, '/my-account/' );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'Available Downloads', $html );
		$this->assertStringContainsString( 'View all downloads', $html );
		$this->assertStringContainsString( 'href="/my-account/downloads/"', $html );
		$this->assertStringContainsString( 'Digital Guide', $html );
		$this->assertStringContainsString( 'Complete Course', $html );
		$this->assertStringContainsString( '2 downloads remaining', $html );
		$this->assertStringContainsString( '1 download remaining', $html );
		$this->assertStringContainsString( 'Expires: August 1, 2026', $html );
		$this->assertStringContainsString( 'Unlimited downloads', $html );
		$this->assertStringContainsString( 'No expiry', $html );
		$this->assertStringContainsString( 'href="https://example.test/?download_file=42&key=private"', $html );
		$this->assertStringContainsString( 'class="agw-dashboard-download__action"', $html );
		$this->assertStringContainsString( 'aria-label="Download Digital Guide"', $html );
		$this->assertStringContainsString( 'Download', $html );
		$this->assertStringNotContainsString( 'Your available files will appear here', $html );
	}

	public function test_woocommerce_dashboard_saved_addresses_render_normalized_lines_and_actions() {
		$dashboard            = new ALYNT_AG_Test_Frontend_Dashboard_Service();
		$dashboard->available = true;
		$woocommerce          = new ALYNT_AG_Test_Frontend_Dashboard_WooCommerce();
		$woocommerce->saved_addresses = array(
			'billing'  => array( 'Damon Example', '12 Main Street', 'Paris 75001' ),
			'shipping' => array(),
		);
		$screen = new ALYNT_AG_Frontend_Dashboard_Screen(
			$dashboard,
			$woocommerce,
			new ALYNT_AG_Test_Frontend_Dashboard_Branding()
		);
		$settings = array_merge(
			$this->settings,
			array( 'woocommerce_takeover' => true )
		);

		ob_start();
		$screen->render_dashboard_screen( $settings, '/my-account/' );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'Saved Addresses', $html );
		$this->assertStringContainsString( 'Manage all addresses', $html );
		$this->assertStringContainsString( 'Billing Address', $html );
		$this->assertStringContainsString( 'Damon Example', $html );
		$this->assertStringContainsString( '12 Main Street', $html );
		$this->assertStringContainsString( 'href="/my-account/edit-address/billing/"', $html );
		$this->assertStringContainsString( 'Edit billing address', $html );
		$this->assertStringContainsString( 'No shipping address is saved yet.', $html );
		$this->assertStringContainsString( 'href="/my-account/edit-address/shipping/"', $html );
		$this->assertStringContainsString( 'Add shipping address', $html );
	}

	public function test_woocommerce_dashboard_saved_payment_methods_render_normalized_rows() {
		$dashboard            = new ALYNT_AG_Test_Frontend_Dashboard_Service();
		$dashboard->available = true;
		$woocommerce          = new ALYNT_AG_Test_Frontend_Dashboard_WooCommerce();
		$woocommerce->saved_payment_methods = array(
			array(
				'display_name' => 'Visa ending in 4242 (expires 12/28)',
				'is_default'   => true,
			),
			array(
				'display_name' => 'eCheck ending in 6789',
				'is_default'   => false,
			),
		);
		$screen = new ALYNT_AG_Frontend_Dashboard_Screen(
			$dashboard,
			$woocommerce,
			new ALYNT_AG_Test_Frontend_Dashboard_Branding()
		);
		$settings = array_merge(
			$this->settings,
			array( 'woocommerce_takeover' => true )
		);

		ob_start();
		$screen->render_dashboard_screen( $settings, '/my-account/' );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'Saved Payment Methods', $html );
		$this->assertStringContainsString( 'Manage payment methods', $html );
		$this->assertStringContainsString( 'href="/my-account/payment-methods/"', $html );
		$this->assertStringContainsString( 'Visa ending in 4242 (expires 12/28)', $html );
		$this->assertStringContainsString( 'eCheck ending in 6789', $html );
		$this->assertSame( 1, substr_count( $html, 'agw-dashboard-payment-method__default' ) );
		$this->assertStringContainsString( '>Default</span>', $html );
		$this->assertStringNotContainsString( 'Saved payment methods will appear here', $html );
	}

	public function test_woocommerce_dashboard_account_details_render_complete_summary() {
		$dashboard            = new ALYNT_AG_Test_Frontend_Dashboard_Service();
		$dashboard->available = true;
		$woocommerce          = new ALYNT_AG_Test_Frontend_Dashboard_WooCommerce();
		$woocommerce->account_details = array(
			'name'         => 'Damon Paulo',
			'email'        => 'damon@example.test',
			'member_since' => 'July 3, 2026',
			'is_complete'  => true,
		);
		$screen = new ALYNT_AG_Frontend_Dashboard_Screen(
			$dashboard,
			$woocommerce,
			new ALYNT_AG_Test_Frontend_Dashboard_Branding()
		);
		$settings = array_merge(
			$this->settings,
			array( 'woocommerce_takeover' => true )
		);

		ob_start();
		$screen->render_dashboard_screen( $settings, '/my-account/' );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'class="agw-dashboard-section agw-dashboard-account-details"', $html );
		$this->assertStringContainsString( 'Edit account details', $html );
		$this->assertStringContainsString( 'href="/my-account/edit-account/"', $html );
		$this->assertStringContainsString( '<dt>Name</dt>', $html );
		$this->assertStringContainsString( 'Damon Paulo', $html );
		$this->assertStringContainsString( '<dt>Email address</dt>', $html );
		$this->assertStringContainsString( 'damon@example.test', $html );
		$this->assertStringContainsString( '<dt>Customer since</dt>', $html );
		$this->assertStringContainsString( 'July 3, 2026', $html );
		$this->assertStringContainsString( 'Details ready', $html );
		$this->assertStringContainsString( 'Your name and email are ready', $html );
		$this->assertStringNotContainsString( 'damon-account-username', $html );
	}

	public function test_woocommerce_dashboard_account_details_render_review_state_without_username_fallback() {
		$dashboard            = new ALYNT_AG_Test_Frontend_Dashboard_Service();
		$dashboard->available = true;
		$woocommerce          = new ALYNT_AG_Test_Frontend_Dashboard_WooCommerce();
		$woocommerce->account_details = array(
			'name'         => '',
			'email'        => 'customer@example.test',
			'member_since' => '',
			'is_complete'  => false,
		);
		$screen = new ALYNT_AG_Frontend_Dashboard_Screen(
			$dashboard,
			$woocommerce,
			new ALYNT_AG_Test_Frontend_Dashboard_Branding()
		);
		$settings = array_merge(
			$this->settings,
			array( 'woocommerce_takeover' => true )
		);

		ob_start();
		$screen->render_dashboard_screen( $settings, '/my-account/' );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'Needs review', $html );
		$this->assertStringContainsString( 'Not added yet', $html );
		$this->assertStringContainsString( 'Not available', $html );
		$this->assertStringContainsString( 'Add your first and last name', $html );
		$this->assertStringContainsString( 'customer@example.test', $html );
	}

	public function test_woocommerce_overview_omits_hidden_shortcuts_without_empty_actions_markup() {
		$dashboard            = new ALYNT_AG_Test_Frontend_Dashboard_Service();
		$dashboard->available = true;
		$screen               = new ALYNT_AG_Frontend_Dashboard_Screen(
			$dashboard,
			$woocommerce = new ALYNT_AG_Test_Frontend_Dashboard_WooCommerce(),
			new ALYNT_AG_Test_Frontend_Dashboard_Branding()
		);
		$woocommerce->recent_orders = array(
			array(
				'id' => 42,
				'number' => '1042',
				'status' => 'Processing',
				'date' => '',
				'total' => '',
			),
		);
		$settings = array_merge(
			$this->settings,
			array(
				'woocommerce_takeover'          => true,
				'woocommerce_hidden_menu_items' => array( 'orders', 'downloads', 'edit-address', 'edit-account', 'payment-methods' ),
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
		$this->assertStringNotContainsString( 'class="agw-dashboard-section agw-dashboard-recent-orders"', $html );
		$this->assertStringNotContainsString( 'class="agw-dashboard-section agw-dashboard-downloads"', $html );
		$this->assertStringNotContainsString( 'class="agw-dashboard-section agw-dashboard-addresses"', $html );
		$this->assertStringNotContainsString( 'class="agw-dashboard-section agw-dashboard-account-details"', $html );
		$this->assertStringNotContainsString( 'class="agw-dashboard-section agw-dashboard-payment-methods"', $html );
	}
}
