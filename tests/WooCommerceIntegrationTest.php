<?php
/**
 * WooCommerce integration tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests WooCommerce account endpoint routing.
 */
class WooCommerceIntegrationTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		$GLOBALS['alynt_ag_test_actions'] = array();
	}

	public function test_endpoint_from_path_detects_base_dashboard() {
		$integration = new ALYNT_AG_WooCommerce_Integration();
		$endpoint    = $integration->endpoint_from_path(
			'/my-account/',
			array( 'after_login_redirect' => '/my-account/' )
		);

		$this->assertSame( 'dashboard', $endpoint['endpoint'] );
		$this->assertSame( '', $endpoint['value'] );
	}

	public function test_endpoint_from_path_detects_standard_endpoint_and_value() {
		$integration = new ALYNT_AG_WooCommerce_Integration();
		$endpoint    = $integration->endpoint_from_path(
			'/my-account/view-order/1234/',
			array( 'after_login_redirect' => '/my-account/' )
		);

		$this->assertSame( 'view-order', $endpoint['endpoint'] );
		$this->assertSame( '1234', $endpoint['value'] );
	}

	public function test_endpoint_from_path_rejects_unknown_endpoint() {
		$integration = new ALYNT_AG_WooCommerce_Integration();
		$endpoint    = $integration->endpoint_from_path(
			'/my-account/not-real/',
			array( 'after_login_redirect' => '/my-account/' )
		);

		$this->assertSame( '', $endpoint['endpoint'] );
	}

	public function test_endpoint_from_path_accepts_plugin_added_menu_endpoint() {
		$integration = new class() extends ALYNT_AG_WooCommerce_Integration {
			public function account_menu_items() {
				return array(
					'dashboard'      => 'Dashboard',
					'loyalty-points' => 'Loyalty Points',
				);
			}
		};
		$endpoint = $integration->endpoint_from_path(
			'/my-account/loyalty-points/',
			array( 'after_login_redirect' => '/my-account/' )
		);

		$this->assertSame( 'loyalty-points', $endpoint['endpoint'] );
	}

	public function test_account_menu_links_include_plugin_added_endpoint() {
		$integration = new class() extends ALYNT_AG_WooCommerce_Integration {
			public function account_menu_items() {
				return array(
					'dashboard'      => 'Dashboard',
					'orders'         => 'Orders',
					'loyalty-points' => 'Loyalty Points',
					'customer-logout' => 'Log Out',
				);
			}
		};
		$links = $integration->account_menu_links(
			array(
				'after_login_redirect' => '/my-account/',
				'login_path'           => '/login',
			)
		);
		$labels = array_column( $links, 'label' );

		$this->assertContains( 'Loyalty Points', $labels );
		$this->assertSame( '/my-account/loyalty-points/', $links[2]['url'] );
		$this->assertSame( 'link', $links[2]['icon'] );
	}

	public function test_render_endpoint_delegates_to_woocommerce_action_when_available() {
		$integration = new class() extends ALYNT_AG_WooCommerce_Integration {
			public function detect() {
				return true;
			}
		};

		$this->assertTrue( $integration->render_endpoint( 'orders', '2' ) );
		$this->assertSame( 'woocommerce_account_orders_endpoint', $GLOBALS['alynt_ag_test_actions'][0]['hook'] );
		$this->assertSame( array( '2' ), $GLOBALS['alynt_ag_test_actions'][0]['args'] );
	}
}
