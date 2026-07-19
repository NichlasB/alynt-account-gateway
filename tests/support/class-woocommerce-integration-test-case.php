<?php
/**
 * Shared woocommerce integration tests. support.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests WooCommerce account endpoint routing.
 */

/**
 * Shared setup for woocommerce integration tests..
 */
abstract class WooCommerceIntegrationTestCase extends TestCase {

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
		$GLOBALS['alynt_ag_test_wc_payment_tokens'] = array();
		$GLOBALS['alynt_ag_test_wc_payment_token_calls'] = array();
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
			$GLOBALS['alynt_ag_test_wc_payment_tokens'],
			$GLOBALS['alynt_ag_test_wc_payment_token_calls'],
			$GLOBALS['alynt_ag_test_wc_formatted_addresses'],
			$GLOBALS['alynt_ag_test_wc_formatted_address_calls']
		);
		parent::tearDown();
	}
}
