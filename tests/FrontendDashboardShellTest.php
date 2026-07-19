<?php
/**
 * Frontend dashboard screen tests.
 *
 * @package Alynt_Account_Gateway
 */

require_once __DIR__ . '/support/class-frontend-dashboard-screen-test-case.php';

/**
 * Tests the dashboard shell and navigation.
 */
class FrontendDashboardShellTest extends FrontendDashboardScreenTestCase {

	public function test_render_dashboard_shell_outputs_brand_logout_hero_and_links() {
		$dashboard = new ALYNT_AG_Test_Frontend_Dashboard_Service();
		$dashboard->links = array(
			array(
				'label'  => 'Account Details',
				'url'    => 'https://example.test/my-account/edit-account/',
				'icon'   => 'user',
				'target' => '_self',
			),
			array(
				'label'  => 'Support',
				'url'    => 'https://example.test/support/',
				'icon'   => 'help',
				'target' => '_blank',
			),
		);
		$screen = new ALYNT_AG_Frontend_Dashboard_Screen(
			$dashboard,
			new ALYNT_AG_Test_Frontend_Dashboard_WooCommerce(),
			new ALYNT_AG_Test_Frontend_Dashboard_Branding()
		);

		ob_start();
		$screen->render_dashboard_shell( $this->settings, '/my-account/' );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'class="alynt-ag-gateway agw-dashboard"', $html );
		$this->assertStringContainsString( 'data-agw-screen="dashboard"', $html );
		$this->assertStringContainsString( 'dir="ltr"', $html );
		$this->assertStringContainsString( 'style="--test-color:#123;"', $html );
		$this->assertStringContainsString( 'Test Store', $html );
		$this->assertStringContainsString( 'class="agw-dashboard-actions"', $html );
		$this->assertStringContainsString( 'class="agw-dashboard-action agw-dashboard-action--home"', $html );
		$this->assertStringContainsString( 'class="agw-dashboard__logout agw-dashboard-action agw-dashboard-action--logout"', $html );
		$this->assertStringContainsString( 'aria-label="Go to homepage"', $html );
		$this->assertStringContainsString( 'aria-label="Log out"', $html );
		$this->assertStringNotContainsString( 'data-agw-offcanvas-open', $html );
		$this->assertStringNotContainsString( 'id="agw-dashboard-offcanvas"', $html );
		$this->assertStringNotContainsString( 'class="agw-dashboard-footer"', $html );
		$this->assertStringContainsString( 'redirect_to=https%3A%2F%2Fexample.test%2Flogin', $html );
		$this->assertStringContainsString( 'Account Dashboard', $html );
		$this->assertStringContainsString( 'Welcome, Damon', $html );
		$this->assertStringNotContainsString( 'Welcome, Damon Paulo', $html );
		$this->assertStringContainsString( 'damon@example.test', $html );
		$this->assertStringContainsString( 'Account Details', $html );
		$this->assertStringContainsString( 'Support', $html );
		$this->assertStringContainsString( 'target="_blank" rel="noopener noreferrer"', $html );
		$this->assertStringContainsString( 'opens in a new tab', $html );
	}

	public function test_render_dashboard_shell_outputs_offcanvas_menu_when_enabled() {
		$screen   = new ALYNT_AG_Frontend_Dashboard_Screen(
			new ALYNT_AG_Test_Frontend_Dashboard_Service(),
			new ALYNT_AG_Test_Frontend_Dashboard_WooCommerce(),
			new ALYNT_AG_Test_Frontend_Dashboard_Branding()
		);
		$settings = array_merge(
			$this->settings,
			array(
				'dashboard_offcanvas_enabled' => true,
				'dashboard_offcanvas_menu_id' => 123,
			)
		);

		ob_start();
		$screen->render_dashboard_shell( $settings, '/my-account/' );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'data-agw-offcanvas-open', $html );
		$this->assertStringContainsString( 'aria-controls="agw-dashboard-offcanvas"', $html );
		$this->assertStringContainsString( 'aria-expanded="false"', $html );
		$this->assertStringContainsString( 'id="agw-dashboard-offcanvas"', $html );
		$this->assertStringContainsString( 'aria-hidden="true"', $html );
		$this->assertStringContainsString( 'role="dialog" aria-modal="true"', $html );
		$this->assertStringContainsString( 'data-agw-offcanvas-close', $html );
		$this->assertStringContainsString( 'class="agw-offcanvas__menu"', $html );
		$this->assertStringContainsString( 'Shop', $html );
		$this->assertStringContainsString( 'Contact', $html );
	}

	public function test_render_dashboard_shell_outputs_independent_footer_menu_when_enabled() {
		$screen   = new ALYNT_AG_Frontend_Dashboard_Screen(
			new ALYNT_AG_Test_Frontend_Dashboard_Service(),
			new ALYNT_AG_Test_Frontend_Dashboard_WooCommerce(),
			new ALYNT_AG_Test_Frontend_Dashboard_Branding()
		);
		$settings = array_merge(
			$this->settings,
			array(
				'dashboard_footer_menu_enabled' => true,
				'dashboard_footer_menu_id'      => 456,
			)
		);

		ob_start();
		$screen->render_dashboard_shell( $settings, '/my-account/' );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'class="agw-dashboard-footer"', $html );
		$this->assertStringContainsString( 'aria-label="Dashboard footer navigation"', $html );
		$this->assertStringContainsString( 'class="agw-dashboard-footer__menu"', $html );
		$this->assertStringContainsString( 'Shop', $html );
		$this->assertStringContainsString( 'Contact', $html );
		$this->assertStringNotContainsString( 'data-agw-offcanvas-open', $html );
	}

	public function test_render_dashboard_shell_uses_rtl_direction_when_site_is_rtl() {
		$GLOBALS['alynt_ag_test_is_rtl'] = true;
		$screen                         = new ALYNT_AG_Frontend_Dashboard_Screen(
			new ALYNT_AG_Test_Frontend_Dashboard_Service(),
			new ALYNT_AG_Test_Frontend_Dashboard_WooCommerce(),
			new ALYNT_AG_Test_Frontend_Dashboard_Branding()
		);

		ob_start();
		$screen->render_dashboard_shell( $this->settings, '/my-account/' );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'data-agw-screen="dashboard"', $html );
		$this->assertStringContainsString( 'dir="rtl"', $html );
	}

	public function test_render_dashboard_shell_marks_current_account_link() {
		$dashboard = new ALYNT_AG_Test_Frontend_Dashboard_Service();
		$dashboard->links = array(
			array(
				'label'  => 'Account Details',
				'url'    => 'https://example.test/my-account/edit-account/',
				'icon'   => 'user',
				'target' => '_self',
			),
			array(
				'label'  => 'Support',
				'url'    => 'https://example.test/support/',
				'icon'   => 'help',
				'target' => '_self',
			),
		);
		$screen = new ALYNT_AG_Frontend_Dashboard_Screen(
			$dashboard,
			new ALYNT_AG_Test_Frontend_Dashboard_WooCommerce(),
			new ALYNT_AG_Test_Frontend_Dashboard_Branding()
		);

		ob_start();
		$screen->render_dashboard_shell( $this->settings, '/my-account/edit-account/' );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'href="https://example.test/my-account/edit-account/" target="_self" aria-current="page"', $html );
		$this->assertStringContainsString( 'href="https://example.test/support/" target="_self"', $html );
		$this->assertSame( 1, substr_count( $html, 'aria-current="page"' ) );
	}
}
