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
		$GLOBALS['alynt_ag_test_actions']       = array();
		$GLOBALS['alynt_ag_test_action_output'] = array();
		$GLOBALS['alynt_ag_test_wc_get_orders_args'] = array();
		$GLOBALS['alynt_ag_test_wc_orders']           = array();
		$GLOBALS['alynt_ag_test_wc_order_statuses']   = array();
		$GLOBALS['alynt_ag_test_wc_format_datetime_calls'] = array();
		$GLOBALS['alynt_ag_test_wc_available_downloads'] = array();
		$GLOBALS['alynt_ag_test_wc_available_download_calls'] = array();
		$GLOBALS['alynt_ag_test_wc_formatted_addresses'] = array();
		$GLOBALS['alynt_ag_test_wc_formatted_address_calls'] = array();
	}

	protected function tearDown(): void {
		unset(
			$GLOBALS['alynt_ag_test_wc_get_orders_args'],
			$GLOBALS['alynt_ag_test_wc_orders'],
			$GLOBALS['alynt_ag_test_wc_order_statuses'],
			$GLOBALS['alynt_ag_test_wc_format_datetime_calls'],
			$GLOBALS['alynt_ag_test_wc_available_downloads'],
			$GLOBALS['alynt_ag_test_wc_available_download_calls'],
			$GLOBALS['alynt_ag_test_wc_formatted_addresses'],
			$GLOBALS['alynt_ag_test_wc_formatted_address_calls']
		);
		parent::tearDown();
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
					'dashboard'       => 'Dashboard',
					'orders'          => 'Orders',
					'loyalty-points'  => 'Loyalty Points',
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

	public function test_account_menu_links_exclude_explicitly_hidden_items() {
		$integration = new class() extends ALYNT_AG_WooCommerce_Integration {
			public function account_menu_items() {
				return array(
					'dashboard'       => 'Dashboard',
					'orders'          => 'Orders',
					'loyalty-points'  => 'Loyalty Points',
					'customer-logout' => 'Log Out',
				);
			}
		};
		$links = $integration->account_menu_links(
			array(
				'after_login_redirect'          => '/my-account/',
				'login_path'                    => '/login',
				'woocommerce_hidden_menu_items' => array( 'orders', 'customer-logout' ),
			)
		);
		$labels = array_column( $links, 'label' );

		$this->assertSame( array( 'Dashboard', 'Loyalty Points' ), $labels );
		$this->assertTrue(
			$integration->is_account_menu_item_visible(
				'loyalty-points',
				array( 'woocommerce_hidden_menu_items' => array( 'orders' ) )
			)
		);
	}

	public function test_hidden_navigation_item_does_not_disable_direct_endpoint_routing() {
		$integration = new ALYNT_AG_WooCommerce_Integration();
		$settings    = array(
			'after_login_redirect'          => '/my-account/',
			'woocommerce_hidden_menu_items' => array( 'orders', 'downloads' ),
		);
		$order_endpoint    = $integration->endpoint_from_path( '/my-account/orders/', $settings );
		$download_endpoint = $integration->endpoint_from_path( '/my-account/downloads/', $settings );

		$this->assertFalse( $integration->is_account_menu_item_visible( 'orders', $settings ) );
		$this->assertSame( 'orders', $order_endpoint['endpoint'] );
		$this->assertFalse( $integration->is_account_menu_item_visible( 'downloads', $settings ) );
		$this->assertSame( 'downloads', $download_endpoint['endpoint'] );
	}

	public function test_account_menu_items_restore_required_standard_items_when_wc_omits_them() {
		$integration = new ALYNT_AG_WooCommerce_Integration();
		$method      = new ReflectionMethod( $integration, 'merge_standard_account_menu_items' );
		$items = $method->invoke(
			$integration,
			array(
				'dashboard'       => 'Dashboard',
				'orders'          => 'Orders',
				'downloads'       => 'Downloads',
				'edit-address'    => 'Addresses',
				'edit-account'    => 'Account details',
				'loyalty-points'  => 'Loyalty Points',
				'customer-logout' => 'Log out',
			),
			$integration->standard_account_menu_items()
		);

		$this->assertArrayHasKey( 'payment-methods', $items );
		$this->assertSame( 'Payment Methods', $items['payment-methods'] );
		$this->assertArrayHasKey( 'loyalty-points', $items );
		$this->assertSame( 'Log out', $items['customer-logout'] );
	}

	public function test_default_account_menu_items_include_payment_methods() {
		$integration = new ALYNT_AG_WooCommerce_Integration();
		$items       = $integration->standard_account_menu_items();

		$this->assertSame( 'dashboard', array_key_first( $items ) );
		$this->assertArrayHasKey( 'payment-methods', $items );
	}

	public function test_endpoint_url_uses_configured_account_base() {
		$integration = new ALYNT_AG_WooCommerce_Integration();
		$settings    = array(
			'after_login_redirect' => '/customer-area/',
			'login_path'           => '/login',
		);

		$this->assertSame( '/customer-area/orders/', $integration->endpoint_url( 'orders', $settings ) );
		$this->assertSame( '/customer-area/', $integration->endpoint_url( 'dashboard', $settings ) );
	}

	public function test_recent_orders_returns_normalized_customer_order_data() {
		$GLOBALS['alynt_ag_test_options']['date_format']       = 'F j, Y';
		$GLOBALS['alynt_ag_test_wc_order_statuses']['processing'] = 'Processing';
		$GLOBALS['alynt_ag_test_wc_orders'] = array(
			new class() {
				public function get_id() {
					return 42;
				}

				public function get_order_number() {
					return '1042';
				}

				public function get_status() {
					return 'processing';
				}

				public function get_date_created() {
					return new class() {
						public function getTimestamp() {
							return 1704067200;
						}
					};
				}

				public function get_formatted_order_total() {
					return '<span class="amount">&pound;42.00</span>';
				}
			},
		);

		$integration = new ALYNT_AG_WooCommerce_Integration();
		$orders      = $integration->recent_orders( 9, 3 );

		$this->assertSame(
			array(
				array(
					'id'     => 42,
					'number' => '1042',
					'status' => 'Processing',
					'date'   => 'January 1, 2024',
					'total'  => '£42.00',
				),
			),
			$orders
		);
		$this->assertSame(
			array(
				'customer_id' => 9,
				'limit'       => 3,
				'orderby'     => 'date',
				'order'       => 'DESC',
				'return'      => 'objects',
			),
			$GLOBALS['alynt_ag_test_wc_get_orders_args'][0]
		);
		$this->assertSame( 'F j, Y', $GLOBALS['alynt_ag_test_wc_format_datetime_calls'][0]['format'] );
	}

	public function test_available_downloads_returns_normalized_customer_download_data() {
		$GLOBALS['alynt_ag_test_options']['date_format'] = 'F j, Y';
		$GLOBALS['alynt_ag_test_wc_available_downloads'] = array(
			array(
				'download_url'        => 'https://example.test/?download_file=42&key=private',
				'download_name'       => 'Digital Guide',
				'product_name'        => 'Complete Course',
				'downloads_remaining' => '2',
				'access_expires'      => '2026-08-01 12:00:00',
				'file'                => array( 'file' => 'private-file.pdf' ),
				'order_key'           => 'wc_order_private',
			),
			array(
				'download_url'        => 'https://example.test/?download_file=43&key=lifetime',
				'download_name'       => '',
				'product_name'        => 'Lifetime Reference',
				'downloads_remaining' => '',
				'access_expires'      => '',
			),
			array(
				'download_url'  => '',
				'download_name' => 'Invalid file',
			),
		);

		$integration = new ALYNT_AG_WooCommerce_Integration();
		$downloads   = $integration->available_downloads( 9, 3 );

		$this->assertSame(
			array(
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
			),
			$downloads
		);
		$this->assertSame( array( 9 ), $GLOBALS['alynt_ag_test_wc_available_download_calls'] );
		$this->assertArrayNotHasKey( 'file', $downloads[0] );
		$this->assertArrayNotHasKey( 'order_key', $downloads[0] );
	}

	public function test_available_downloads_caps_results_and_rejects_invalid_user() {
		$GLOBALS['alynt_ag_test_wc_available_downloads'] = array(
			array(
				'download_url'  => 'https://example.test/one',
				'download_name' => 'One',
			),
			array(
				'download_url'  => 'https://example.test/two',
				'download_name' => 'Two',
			),
			array(
				'download_url'  => 'https://example.test/three',
				'download_name' => 'Three',
			),
		);

		$integration = new ALYNT_AG_WooCommerce_Integration();

		$this->assertCount( 2, $integration->available_downloads( 9, 2 ) );
		$this->assertSame( array(), $integration->available_downloads( 0, 3 ) );
		$this->assertSame( array( 9 ), $GLOBALS['alynt_ag_test_wc_available_download_calls'] );
	}

	public function test_recent_orders_skips_invalid_results_and_caps_query_limit() {
		$GLOBALS['alynt_ag_test_wc_orders'] = array( null, new stdClass() );

		$integration = new ALYNT_AG_WooCommerce_Integration();

		$this->assertSame( array(), $integration->recent_orders( 9, 99 ) );
		$this->assertSame( 5, $GLOBALS['alynt_ag_test_wc_get_orders_args'][0]['limit'] );
		$this->assertSame( array(), $integration->recent_orders( 0, 3 ) );
		$this->assertCount( 1, $GLOBALS['alynt_ag_test_wc_get_orders_args'] );
	}

	public function test_order_url_uses_configured_account_base_and_order_id() {
		$integration = new ALYNT_AG_WooCommerce_Integration();
		$url         = $integration->order_url(
			42,
			array( 'after_login_redirect' => '/customer-area/' )
		);

		$this->assertSame( '/customer-area/view-order/42/', $url );
	}

	public function test_saved_addresses_returns_sanitized_text_lines() {
		$GLOBALS['alynt_ag_test_wc_formatted_addresses'] = array(
			'billing:9'  => '<strong>Damon Example</strong><br>12 Main Street<br>Paris&nbsp;75001',
			'shipping:9' => '',
		);

		$integration = new ALYNT_AG_WooCommerce_Integration();
		$addresses   = $integration->saved_addresses( 9 );

		$this->assertSame(
			array(
				'billing'  => array( 'Damon Example', '12 Main Street', 'Paris 75001' ),
				'shipping' => array(),
			),
			$addresses
		);
		$this->assertSame(
			array(
				array(
					'type'    => 'billing',
					'user_id' => 9,
				),
				array(
					'type'    => 'shipping',
					'user_id' => 9,
				),
			),
			$GLOBALS['alynt_ag_test_wc_formatted_address_calls']
		);
		$this->assertSame(
			array(
				'billing'  => array(),
				'shipping' => array(),
			),
			$integration->saved_addresses( 0 )
		);
	}

	public function test_address_url_uses_configured_account_base_and_allowed_type() {
		$integration = new ALYNT_AG_WooCommerce_Integration();
		$settings    = array( 'after_login_redirect' => '/customer-area/' );

		$this->assertSame( '/customer-area/edit-address/shipping/', $integration->address_url( 'shipping', $settings ) );
		$this->assertSame( '/customer-area/edit-address/billing/', $integration->address_url( 'not-valid', $settings ) );
	}

	public function test_render_endpoint_returns_false_when_woocommerce_action_outputs_nothing() {
		$integration = new class() extends ALYNT_AG_WooCommerce_Integration {
			public function detect() {
				return true;
			}
		};

		$this->assertFalse( $integration->render_endpoint( 'orders', '2' ) );
		$this->assertSame( 'woocommerce_account_orders_endpoint', $GLOBALS['alynt_ag_test_actions'][0]['hook'] );
		$this->assertSame( array( '2' ), $GLOBALS['alynt_ag_test_actions'][0]['args'] );
	}

	public function test_render_endpoint_returns_false_when_only_empty_notice_wrapper_outputs() {
		$integration = new class() extends ALYNT_AG_WooCommerce_Integration {
			public function detect() {
				return true;
			}
		};

		$GLOBALS['alynt_ag_test_action_output']['woocommerce_account_orders_endpoint'] = '<div class="woocommerce-notices-wrapper"></div>';

		ob_start();
		$result = $integration->render_endpoint( 'orders', '2' );
		$html   = ob_get_clean();

		$this->assertFalse( $result );
		$this->assertSame( '', $html );
	}

	public function test_render_endpoint_outputs_woocommerce_action_content_when_available() {
		$integration = new class() extends ALYNT_AG_WooCommerce_Integration {
			public function detect() {
				return true;
			}
		};

		$GLOBALS['alynt_ag_test_action_output']['woocommerce_account_orders_endpoint'] = '<div class="wc-output">Orders output</div>';

		ob_start();
		$result = $integration->render_endpoint( 'orders', '2' );
		$html   = ob_get_clean();

		$this->assertTrue( $result );
		$this->assertSame( '<div class="wc-output">Orders output</div>', $html );
		$this->assertSame( 'woocommerce_account_orders_endpoint', $GLOBALS['alynt_ag_test_actions'][0]['hook'] );
		$this->assertSame( array( '2' ), $GLOBALS['alynt_ag_test_actions'][0]['args'] );
	}

	public function test_account_form_post_handler_registers_before_gateway_render() {
		$integration = new ALYNT_AG_WooCommerce_Integration();
		$integration->register();

		$hooks = array_values(
			array_filter(
				$GLOBALS['alynt_ag_test_actions'],
				static function ( $hook ) {
					return 'template_redirect' === $hook['hook']
						&& is_array( $hook['callback'] )
						&& 'maybe_handle_account_form_post' === $hook['callback'][1];
				}
			)
		);

		$this->assertCount( 1, $hooks );
		$this->assertSame( 0, $hooks[0]['priority'] );
	}
}
