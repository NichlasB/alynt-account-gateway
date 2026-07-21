<?php
/**
 * Frontend routing tests.
 *
 * @package Alynt_Account_Gateway
 */

require_once __DIR__ . '/support/class-frontend-routing-test-case.php';

/**
 * Tests toolbar and wp-admin access policy.
 */
class FrontendAccessPolicyTest extends FrontendRoutingTestCase {

	public function test_toolbar_is_limited_to_admins_and_shop_managers() {
		$frontend = new ALYNT_AG_Frontend();
		$GLOBALS['alynt_ag_test_user_logged_in'] = true;

		$this->assertFalse( $frontend->filter_admin_bar( true ) );

		$GLOBALS['alynt_ag_test_user_caps'] = array( 'manage_woocommerce' );
		$this->assertTrue( $frontend->filter_admin_bar( false ) );

		$GLOBALS['alynt_ag_test_user_caps'] = array( 'manage_options' );
		$this->assertTrue( $frontend->filter_admin_bar( false ) );
	}

	public function test_toolbar_filter_respects_frontend_master_switch() {
		$frontend = new ALYNT_AG_Frontend();
		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings']['frontend_enabled'] = false;
		$GLOBALS['alynt_ag_test_user_logged_in'] = true;

		$this->assertTrue( $frontend->filter_admin_bar( true ) );
		$this->assertFalse( $frontend->filter_admin_bar( false ) );
	}

	public function test_wp_admin_block_redirects_non_privileged_users() {
		$frontend = new ALYNT_AG_Frontend();
		$GLOBALS['alynt_ag_test_user_logged_in'] = true;
		$GLOBALS['alynt_ag_test_throw_on_redirect'] = true;

		try {
			$frontend->maybe_block_wp_admin();
			$this->fail( 'Expected redirect exception.' );
		} catch ( RuntimeException $exception ) {
			$this->assertSame( 'redirect:https://example.test/my-account/', $exception->getMessage() );
		}

		$this->assertSame( 'https://example.test/my-account/', $GLOBALS['alynt_ag_test_redirects'][0]['location'] );
	}

	public function test_wp_admin_block_respects_frontend_master_switch() {
		$frontend = new ALYNT_AG_Frontend();
		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings']['frontend_enabled'] = false;
		$GLOBALS['alynt_ag_test_user_logged_in'] = true;
		$GLOBALS['alynt_ag_test_throw_on_redirect'] = true;

		$frontend->maybe_block_wp_admin();

		$this->assertSame( array(), $GLOBALS['alynt_ag_test_redirects'] );
	}

	public function test_wp_admin_block_allows_authenticated_admin_post_dispatchers() {
		$frontend        = new ALYNT_AG_Frontend();
		$previous_pagenow = $GLOBALS['pagenow'] ?? null;

		$GLOBALS['alynt_ag_test_user_logged_in'] = true;
		$GLOBALS['alynt_ag_test_throw_on_redirect'] = true;
		$GLOBALS['pagenow'] = 'admin-post.php';
		$_GET['action'] = 'awcom_complete_customer_payment_switch';
		$_SERVER['REQUEST_URI'] = '/wp-admin/admin-post.php?action=awcom_complete_customer_payment_switch';

		try {
			$frontend->maybe_block_wp_admin();
		} finally {
			if ( null === $previous_pagenow ) {
				unset( $GLOBALS['pagenow'] );
			} else {
				$GLOBALS['pagenow'] = $previous_pagenow;
			}
		}

		$this->assertSame( array(), $GLOBALS['alynt_ag_test_redirects'] );
		$this->assertSame( array(), $GLOBALS['alynt_ag_test_db_inserts'] );
	}

	public function test_wp_admin_block_logs_diagnostics_when_enabled() {
		$frontend = new ALYNT_AG_Frontend();
		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings']['diagnostics_enabled'] = true;
		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings']['diagnostics_min_level'] = 'debug';
		$GLOBALS['alynt_ag_test_user_logged_in'] = true;
		$GLOBALS['alynt_ag_test_throw_on_redirect'] = true;
		$_GET = array(
			'page'        => 'orders',
			'redirect_to' => 'https://evil.example/path',
		);
		$_SERVER['REQUEST_URI'] = '/wp-admin/admin.php?page=orders&redirect_to=https%3A%2F%2Fevil.example%2Fpath';

		try {
			$frontend->maybe_block_wp_admin();
			$this->fail( 'Expected redirect exception.' );
		} catch ( RuntimeException $exception ) {
			$this->assertSame( 'redirect:https://example.test/my-account/', $exception->getMessage() );
		}

		$tables = ALYNT_AG_Database::tables();
		$this->assertCount( 1, $GLOBALS['alynt_ag_test_db_inserts'] );
		$this->assertSame( $tables['diagnostics_logs'], $GLOBALS['alynt_ag_test_db_inserts'][0]['table'] );

		$row     = $GLOBALS['alynt_ag_test_db_inserts'][0]['data'];
		$context = json_decode( $row['context'], true );

		$this->assertSame( 'warning', $row['level'] );
		$this->assertSame( 'security', $row['category'] );
		$this->assertSame( 'wp_admin_access_blocked', $row['event_code'] );
		$this->assertSame( '/my-account/', $context['destination_path'] );
		$this->assertSame( 0, $context['user_id'] );
		$this->assertSame( '/wp-admin/admin.php', $context['request_path'] );
		$this->assertSame( 'GET', $context['request_method'] );
		$this->assertSame( array( 'page', 'redirect_to' ), $context['request_query_keys'] );
		$this->assertStringNotContainsString( 'orders', $row['context'] );
		$this->assertStringNotContainsString( 'evil.example', $row['context'] );
	}
}
