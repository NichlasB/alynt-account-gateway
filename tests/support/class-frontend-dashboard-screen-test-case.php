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
	 * Recent orders to return.
	 *
	 * @var array<int,array<string,mixed>>
	 */
	public $recent_orders = array();

	/**
	 * Available downloads to return.
	 *
	 * @var array<int,array<string,mixed>>
	 */
	public $available_downloads = array();

	/**
	 * Saved addresses to return.
	 *
	 * @var array<string,array<int,string>>
	 */
	public $saved_addresses = array(
		'billing'  => array(),
		'shipping' => array(),
	);

	/**
	 * Account details to return.
	 *
	 * @var array<string,mixed>
	 */
	public $account_details = array(
		'name'         => 'Damon Paulo',
		'email'        => 'damon@example.test',
		'member_since' => 'July 3, 2026',
		'is_complete'  => true,
	);

	/**
	 * Saved payment methods to return.
	 *
	 * @var array<int,array<string,mixed>>
	 */
	public $saved_payment_methods = array();

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

	/**
	 * Return configured recent orders.
	 *
	 * @param int $user_id User ID.
	 * @param int $limit   Maximum orders.
	 * @return array<int,array<string,mixed>>
	 */
	public function recent_orders( $user_id, $limit = 3 ) {
		return $this->recent_orders;
	}

	/**
	 * Return configured available downloads.
	 *
	 * @param int $user_id User ID.
	 * @param int $limit   Maximum downloads.
	 * @return array<int,array<string,mixed>>
	 */
	public function available_downloads( $user_id, $limit = 3 ) {
		return $this->available_downloads;
	}

	/**
	 * Return configured saved addresses.
	 *
	 * @param int $user_id User ID.
	 * @return array<string,array<int,string>>
	 */
	public function saved_addresses( $user_id ) {
		return $this->saved_addresses;
	}

	/**
	 * Return configured account details.
	 *
	 * @param int $user_id User ID.
	 * @return array<string,mixed>
	 */
	public function account_details( $user_id ) {
		return $this->account_details;
	}

	/**
	 * Return configured saved payment methods.
	 *
	 * @param int $user_id User ID.
	 * @param int $limit   Maximum payment methods.
	 * @return array<int,array<string,mixed>>
	 */
	public function saved_payment_methods( $user_id, $limit = 3 ) {
		return $this->saved_payment_methods;
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

/**
 * Shared setup for frontend dashboard screen tests..
 */
abstract class FrontendDashboardScreenTestCase extends TestCase {

	/**
	 * Dashboard settings fixture.
	 *
	 * @var array<string,mixed>
	 */
	protected $settings;

	protected function setUp(): void {
		parent::setUp();

		$GLOBALS['alynt_ag_test_is_rtl'] = false;
		$GLOBALS['alynt_ag_test_user_meta'] = array(
			'first_name' => 'Damon',
			'last_name'  => 'Paulo',
		);
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
		unset( $GLOBALS['alynt_ag_test_is_rtl'], $GLOBALS['alynt_ag_test_user_meta'] );

		parent::tearDown();
	}
}
