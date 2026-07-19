<?php
/**
 * Focused structural test suite.
 *
 * @package Alynt_Account_Gateway
 */

require_once __DIR__ . '/support/class-woocommerce-integration-test-case.php';

/**
 * Tests normalized order and download data.
 */
class WooCommerceOrderDownloadDataTest extends WooCommerceIntegrationTestCase {

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
}
