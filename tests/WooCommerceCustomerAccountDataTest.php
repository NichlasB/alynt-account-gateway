<?php
/**
 * Focused structural test suite.
 *
 * @package Alynt_Account_Gateway
 */

require_once __DIR__ . '/support/class-woocommerce-integration-test-case.php';

/**
 * Tests normalized payment, profile, and address data.
 */
class WooCommerceCustomerAccountDataTest extends WooCommerceIntegrationTestCase {

	public function test_saved_payment_methods_returns_masked_customer_display_data() {
		$GLOBALS['alynt_ag_test_wc_payment_tokens'] = array(
			new class() {
				public function get_display_name() {
					return '<strong>Visa ending in 4242</strong> (expires 12/28)';
				}

				public function is_default() {
					return true;
				}

				public function get_token() {
					return 'tok_private';
				}
			},
			new class() {
				public function get_display_name() {
					return 'eCheck ending in 6789';
				}

				public function is_default() {
					return false;
				}
			},
			new stdClass(),
		);

		$integration = new ALYNT_AG_WooCommerce_Integration();
		$methods     = $integration->saved_payment_methods( 9, 3 );

		$this->assertSame(
			array(
				array(
					'display_name' => 'Visa ending in 4242 (expires 12/28)',
					'is_default'   => true,
				),
				array(
					'display_name' => 'eCheck ending in 6789',
					'is_default'   => false,
				),
			),
			$methods
		);
		$this->assertSame( array( 9 ), $GLOBALS['alynt_ag_test_wc_payment_token_calls'] );
		$this->assertArrayNotHasKey( 'token', $methods[0] );
		$this->assertArrayNotHasKey( 'gateway_id', $methods[0] );
	}

	public function test_account_details_returns_only_normalized_customer_summary_data() {
		$GLOBALS['alynt_ag_test_options']['date_format'] = 'F j, Y';

		$integration = new ALYNT_AG_WooCommerce_Integration();
		$details     = $integration->account_details( 9 );

		$this->assertSame(
			array(
				'name'         => 'Damon Paulo',
				'email'        => 'customer@example.test',
				'member_since' => 'July 3, 2026',
				'is_complete'  => true,
			),
			$details
		);
		$this->assertArrayNotHasKey( 'user_login', $details );
		$this->assertArrayNotHasKey( 'display_name', $details );
		$this->assertArrayNotHasKey( 'roles', $details );
		$this->assertSame( array(), $integration->account_details( 0 ) );
	}

	public function test_saved_payment_methods_caps_results_and_rejects_invalid_user() {
		$GLOBALS['alynt_ag_test_wc_payment_tokens'] = array(
			new class() {
				public function get_display_name() {
					return 'Method one';
				}
			},
			new class() {
				public function get_display_name() {
					return 'Method two';
				}
			},
			new class() {
				public function get_display_name() {
					return 'Method three';
				}
			},
		);

		$integration = new ALYNT_AG_WooCommerce_Integration();

		$this->assertCount( 2, $integration->saved_payment_methods( 9, 2 ) );
		$this->assertSame( array(), $integration->saved_payment_methods( 0, 3 ) );
		$this->assertSame( array( 9 ), $GLOBALS['alynt_ag_test_wc_payment_token_calls'] );
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
}
