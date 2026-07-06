<?php
/**
 * Frontend login screen service tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests the frontend login screen.
 */
class FrontendLoginScreenTest extends TestCase {

	/**
	 * Test settings.
	 *
	 * @var array<string,mixed>
	 */
	private $settings;

	protected function setUp(): void {
		parent::setUp();

		$_GET = array();

		$this->settings = array(
			'account_action_base' => '/account',
			'login_path'          => '/login',
			'login_intro_text'    => 'Welcome back to your account.',
		);
	}

	protected function tearDown(): void {
		$_GET = array();

		parent::tearDown();
	}

	public function test_render_login_screen_outputs_form_defaults() {
		$screen = new ALYNT_AG_Frontend_Login_Screen();

		ob_start();
		$screen->render_login_screen( $this->settings );
		$html = ob_get_clean();

		$this->assertStringContainsString( '<h1 id="agw-screen-title" class="agw-title">Log In</h1>', $html );
		$this->assertStringContainsString( '<div class="agw-notice">', $html );
		$this->assertStringContainsString( 'Welcome back to your account.', $html );
		$this->assertStringContainsString( 'action="https://example.test/login"', $html );
		$this->assertStringContainsString( 'name="alynt_ag_action" value="login"', $html );
		$this->assertStringContainsString( 'name="alynt_ag_auth_nonce" value="test-nonce"', $html );
		$this->assertStringContainsString( 'id="agw-login-email"', $html );
		$this->assertStringContainsString( 'name="email"', $html );
		$this->assertStringContainsString( 'id="agw-login-password"', $html );
		$this->assertStringContainsString( 'name="pwd"', $html );
		$this->assertStringContainsString( 'data-agw-password-toggle', $html );
		$this->assertStringContainsString( 'aria-controls="agw-login-password"', $html );
		$this->assertStringContainsString( 'aria-label="Show password"', $html );
		$this->assertStringContainsString( 'name="rememberme"', $html );
		$this->assertStringContainsString( 'href="https://example.test/account?action=register"', $html );
		$this->assertStringContainsString( 'href="https://example.test/account?action=lostpassword"', $html );
		$this->assertStringNotContainsString( 'agw-login-error', $html );
		$this->assertStringNotContainsString( 'name="redirect_to"', $html );
	}

	public function test_render_login_screen_outputs_success_states_and_redirect() {
		$screen = new ALYNT_AG_Frontend_Login_Screen();
		$_GET['registration_complete'] = '1';
		$_GET['password_reset'] = '1';
		$_GET['redirect_to'] = 'https://example.test/my-account/';

		ob_start();
		$screen->render_login_screen( $this->settings );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'Your account has been created. You can log in now.', $html );
		$this->assertStringContainsString( 'Your password has been updated. You can log in now.', $html );
		$this->assertStringContainsString( 'name="redirect_to" value="https://example.test/my-account/"', $html );
	}

	public function test_render_login_screen_outputs_error_state() {
		$screen = new ALYNT_AG_Frontend_Login_Screen();
		$_GET['login_error'] = 'alynt_ag_rate_limited';

		ob_start();
		$screen->render_login_screen( $this->settings );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'id="agw-login-error"', $html );
		$this->assertStringContainsString( 'Too many attempts. Please wait a moment and try again.', $html );
		$this->assertStringContainsString( 'aria-describedby="agw-login-error"', $html );
		$this->assertStringContainsString( 'aria-invalid="true"', $html );
	}
}
