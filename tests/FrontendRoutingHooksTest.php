<?php
/**
 * Frontend routing tests.
 *
 * @package Alynt_Account_Gateway
 */

require_once __DIR__ . '/support/class-frontend-routing-test-case.php';

/**
 * Tests gateway hook order, URL filters, and preview assets.
 */
class FrontendRoutingHooksTest extends FrontendRoutingTestCase {

	public function test_gateway_render_hook_runs_before_canonical_redirects() {
		$GLOBALS['alynt_ag_test_actions'] = array();

		$frontend = new ALYNT_AG_Frontend();
		$frontend->register();

		$gateway_hooks = array_values(
			array_filter(
				$GLOBALS['alynt_ag_test_actions'],
				static function ( $hook ) {
					return 'template_redirect' === $hook['hook']
						&& is_array( $hook['callback'] )
						&& 'maybe_render_gateway' === $hook['callback'][1];
				}
			)
		);

		$this->assertCount( 1, $gateway_hooks );
		$this->assertSame( 1, $gateway_hooks[0]['priority'] );
	}

	public function test_auth_and_registration_post_handlers_run_before_gateway_render() {
		$GLOBALS['alynt_ag_test_actions'] = array();

		$auth         = new ALYNT_AG_Auth_Service();
		$registration = new ALYNT_AG_Registration_Service();
		$frontend     = new ALYNT_AG_Frontend();

		$auth->register();
		$registration->register();
		$frontend->register();

		$priorities = array();
		foreach ( $GLOBALS['alynt_ag_test_actions'] as $hook ) {
			if ( 'template_redirect' !== $hook['hook'] || ! is_array( $hook['callback'] ) ) {
				continue;
			}

			$priorities[ $hook['callback'][1] ] = $hook['priority'];
		}

		$this->assertSame( 0, $priorities['maybe_handle_auth_request'] );
		$this->assertSame( 0, $priorities['maybe_handle_registration_request'] );
		$this->assertSame( 1, $priorities['maybe_render_gateway'] );
		$this->assertLessThan( $priorities['maybe_render_gateway'], $priorities['maybe_handle_auth_request'] );
		$this->assertLessThan( $priorities['maybe_render_gateway'], $priorities['maybe_handle_registration_request'] );
	}

	public function test_url_filters_respect_frontend_master_switch() {
		$frontend = new ALYNT_AG_Frontend();

		$this->assertSame(
			'https://example.test/login?redirect_to=https%253A%252F%252Fexample.test%252Fmy-account%252F&reauth=1',
			$frontend->filter_login_url( 'https://example.test/wp-login.php', 'https://example.test/my-account/', true )
		);
		$this->assertSame(
			'https://example.test/account?action=lostpassword',
			$frontend->filter_lostpassword_url( 'https://example.test/wp-login.php?action=lostpassword', '' )
		);
		$this->assertSame(
			'https://example.test/account?action=register',
			$frontend->filter_register_url( 'https://example.test/wp-login.php?action=register' )
		);

		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings']['frontend_enabled'] = false;

		$this->assertSame(
			'https://example.test/wp-login.php',
			$frontend->filter_login_url( 'https://example.test/wp-login.php', '', false )
		);
		$this->assertSame(
			'https://example.test/wp-login.php?action=register',
			$frontend->filter_register_url( 'https://example.test/wp-login.php?action=register' )
		);
	}

	public function test_preview_assets_bypass_disabled_output_without_changing_public_enqueue() {
		$frontend = new ALYNT_AG_Frontend();
		$settings = $GLOBALS['alynt_ag_test_options']['alynt_ag_settings'];

		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings']['frontend_enabled'] = false;
		$GLOBALS['alynt_ag_test_enqueued_styles']                                 = array();
		$GLOBALS['alynt_ag_test_enqueued_scripts']                                = array();
		$_SERVER['REQUEST_URI']                                                    = '/login';

		$frontend->enqueue_assets();

		$this->assertSame( array(), $GLOBALS['alynt_ag_test_enqueued_styles'] );
		$this->assertSame( array(), $GLOBALS['alynt_ag_test_enqueued_scripts'] );

		$settings['frontend_enabled'] = false;
		$frontend->enqueue_preview_assets( $settings, 'login' );

		$this->assertSame(
			array( 'alynt-ag-frontend' ),
			array_column( $GLOBALS['alynt_ag_test_enqueued_styles'], 'handle' )
		);
		$this->assertSame(
			array( 'alynt-ag-frontend' ),
			array_column( $GLOBALS['alynt_ag_test_enqueued_scripts'], 'handle' )
		);
	}
}
