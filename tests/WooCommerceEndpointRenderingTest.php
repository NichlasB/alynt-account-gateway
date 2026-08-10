<?php
/**
 * WooCommerce integration tests.
 *
 * @package Alynt_Account_Gateway
 */

require_once __DIR__ . '/support/class-woocommerce-integration-test-case.php';

/**
 * Tests WooCommerce endpoint rendering and form handling.
 */
class WooCommerceEndpointRenderingTest extends WooCommerceIntegrationTestCase {

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

	public function test_edit_account_endpoint_removes_public_display_name_field() {
		$integration = new class() extends ALYNT_AG_WooCommerce_Integration {
			public function detect() {
				return true;
			}
		};

		$GLOBALS['alynt_ag_test_action_output']['woocommerce_account_edit-account_endpoint'] = '<form><p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide"><label for="account_display_name">Display name</label><input id="account_display_name" name="account_display_name"><span><em>This will be shown publicly.</em></span></p><p class="form-row"><label for="account_email">Email address</label><input id="account_email" name="account_email"></p></form>';

		ob_start();
		$result = $integration->render_endpoint( 'edit-account', '' );
		$html   = ob_get_clean();

		$this->assertTrue( $result );
		$this->assertStringNotContainsString( 'account_display_name', $html );
		$this->assertStringContainsString( 'account_email', $html );
	}

	public function test_edit_account_post_primes_display_name_from_first_and_last_name() {
		$integration = new class() extends ALYNT_AG_WooCommerce_Integration {
			public function detect() {
				return true;
			}
		};

		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings'] = array(
			'dashboard_enabled'    => true,
			'woocommerce_takeover' => true,
			'after_login_redirect' => '/my-account/',
		);
		$GLOBALS['alynt_ag_test_current_user_id'] = 123;
		$GLOBALS['alynt_ag_test_user_logged_in']  = true;
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_SERVER['REQUEST_URI']    = '/my-account/edit-account/';
		$_POST                     = array(
			'save_account_details' => '1',
			'account_first_name'   => 'Anna',
			'account_last_name'    => 'Miller',
		);

		$integration->maybe_handle_account_form_post();

		$this->assertSame( 'Anna Miller', $_POST['account_display_name'] );
		$this->assertSame( 'Anna Miller', $GLOBALS['alynt_ag_test_wc_save_account_details_calls'][0]['account_display_name'] );
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
