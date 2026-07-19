<?php
/**
 * WooCommerce checkout gate tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests checkout authentication decisions.
 */
class WooCommerceCheckoutGateTest extends TestCase {

	/**
	 * Settings.
	 *
	 * @var array<string,mixed>
	 */
	private $settings;

	protected function setUp(): void {
		parent::setUp();

		$this->settings = array_merge(
			ALYNT_AG_Settings_Schema::defaults(),
			array(
				'frontend_enabled'                    => true,
				'woocommerce_require_login_checkout'  => true,
				'woocommerce_require_login_order_pay' => false,
			)
		);

		$GLOBALS['alynt_ag_test_is_checkout'] = true;
		$GLOBALS['alynt_ag_test_user_logged_in'] = false;
		$GLOBALS['alynt_ag_test_doing_ajax'] = false;
		$GLOBALS['alynt_ag_test_wc_endpoint'] = '';
		$GLOBALS['alynt_ag_test_checkout_url'] = 'https://example.test/checkout/';
		$GLOBALS['alynt_ag_test_redirects'] = array();
		$GLOBALS['alynt_ag_test_throw_on_redirect'] = false;
		$_SERVER['REQUEST_URI'] = '/checkout/';
	}

	protected function tearDown(): void {
		unset(
			$GLOBALS['alynt_ag_test_is_checkout'],
			$GLOBALS['alynt_ag_test_user_logged_in'],
			$GLOBALS['alynt_ag_test_doing_ajax'],
			$GLOBALS['alynt_ag_test_wc_endpoint'],
			$GLOBALS['alynt_ag_test_checkout_url'],
			$GLOBALS['alynt_ag_test_throw_on_redirect']
		);

		parent::tearDown();
	}

	public function test_gate_is_disabled_by_default() {
		$service = new ALYNT_AG_WooCommerce_Checkout_Gate();

		$this->assertFalse( $service->should_redirect_current_request( ALYNT_AG_Settings_Schema::defaults() ) );
	}

	public function test_gate_redirects_only_anonymous_non_ajax_checkout() {
		$service = new ALYNT_AG_WooCommerce_Checkout_Gate();

		$this->assertTrue( $service->should_redirect_current_request( $this->settings ) );

		$GLOBALS['alynt_ag_test_user_logged_in'] = true;
		$this->assertFalse( $service->should_redirect_current_request( $this->settings ) );

		$GLOBALS['alynt_ag_test_user_logged_in'] = false;
		$GLOBALS['alynt_ag_test_doing_ajax'] = true;
		$this->assertFalse( $service->should_redirect_current_request( $this->settings ) );
	}

	public function test_order_received_is_never_gated_and_order_pay_is_opt_in() {
		$service = new ALYNT_AG_WooCommerce_Checkout_Gate();

		$GLOBALS['alynt_ag_test_wc_endpoint'] = 'order-received';
		$this->assertFalse( $service->should_redirect_current_request( $this->settings ) );

		$GLOBALS['alynt_ag_test_wc_endpoint'] = 'order-pay';
		$this->assertFalse( $service->should_redirect_current_request( $this->settings ) );

		$this->settings['woocommerce_require_login_order_pay'] = true;
		$this->assertTrue( $service->should_redirect_current_request( $this->settings ) );
	}

	public function test_checkout_destination_recognizes_main_checkout_and_order_pay_policy() {
		$service = new ALYNT_AG_WooCommerce_Checkout_Gate();

		$this->assertTrue( $service->is_checkout_destination( 'https://example.test/checkout/?coupon=welcome', $this->settings ) );
		$this->assertFalse( $service->is_checkout_destination( 'https://example.test/checkout/order-received/42/', $this->settings ) );
		$this->assertFalse( $service->is_checkout_destination( 'https://example.test/checkout/order-pay/42/?key=test', $this->settings ) );

		$this->settings['woocommerce_require_login_order_pay'] = true;
		$this->assertTrue( $service->is_checkout_destination( 'https://example.test/checkout/order-pay/42/?key=test', $this->settings ) );
		$this->assertFalse( $service->is_checkout_destination( 'https://evil.example/checkout/', $this->settings ) );
	}

	public function test_redirect_uses_branded_login_and_checkout_return_destination() {
		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings'] = $this->settings;
		$GLOBALS['alynt_ag_test_throw_on_redirect'] = true;
		$service = new ALYNT_AG_WooCommerce_Checkout_Gate();

		try {
			$service->maybe_redirect_checkout();
			$this->fail( 'Expected redirect exception.' );
		} catch ( RuntimeException $exception ) {
			$this->assertStringContainsString( 'redirect:https://example.test/login?redirect_to=', $exception->getMessage() );
			$this->assertStringContainsString( 'https%253A%252F%252Fexample.test%252Fcheckout%252F', $exception->getMessage() );
		}
	}
}
