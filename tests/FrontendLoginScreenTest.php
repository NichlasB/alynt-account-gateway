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
			'registration_enabled' => true,
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
		$this->assertStringContainsString( '<div class="agw-notice" id="agw-login-instructions">', $html );
		$this->assertStringContainsString( 'Welcome back to your account.', $html );
		$this->assertStringContainsString( 'action="https://example.test/login"', $html );
		$this->assertStringContainsString( '<form class="agw-form" method="post" action="https://example.test/login" data-agw-retain-fields aria-describedby="agw-login-instructions">', $html );
		$this->assertStringContainsString( 'name="alynt_ag_action" value="login"', $html );
		$this->assertStringContainsString( 'name="alynt_ag_auth_nonce" value="test-nonce"', $html );
		$this->assertStringContainsString( 'id="agw-login-email"', $html );
		$this->assertStringContainsString( 'name="email"', $html );
		$this->assertStringContainsString( 'required data-agw-retain', $html );
		$this->assertStringContainsString( 'type="email" autocomplete="email" dir="ltr"', $html );
		$this->assertStringContainsString( 'id="agw-login-password"', $html );
		$this->assertStringContainsString( 'name="pwd"', $html );
		$this->assertStringContainsString( 'type="password" autocomplete="current-password" dir="ltr"', $html );
		$this->assertStringContainsString( 'data-agw-password-toggle', $html );
		$this->assertStringContainsString( 'aria-controls="agw-login-password"', $html );
		$this->assertStringContainsString( 'aria-label="Show password"', $html );
		$this->assertStringContainsString( 'data-agw-password-visibility-status role="status" aria-live="polite" aria-atomic="true"', $html );
		$this->assertStringContainsString( 'name="rememberme"', $html );
		$this->assertStringContainsString( 'href="https://example.test/account?action=register"', $html );
		$this->assertStringContainsString( 'href="https://example.test/account?action=lostpassword"', $html );
		$this->assertStringNotContainsString( 'agw-login-error', $html );
		$this->assertStringNotContainsString( 'name="redirect_to"', $html );
	}

	public function test_render_login_screen_hides_registration_link_when_registration_is_disabled() {
		$screen                                  = new ALYNT_AG_Frontend_Login_Screen();
		$this->settings['registration_enabled'] = false;

		ob_start();
		$screen->render_login_screen( $this->settings );
		$html = ob_get_clean();

		$this->assertStringNotContainsString( 'action=register', $html );
		$this->assertStringNotContainsString( 'Create Account', $html );
		$this->assertStringContainsString( 'href="https://example.test/account?action=lostpassword"', $html );
		$this->assertStringContainsString( 'Forgot Password?', $html );
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
		$this->assertStringContainsString( 'id="agw-registration-complete" class="agw-status agw-status--success" role="status" aria-live="polite" aria-atomic="true"', $html );
		$this->assertStringContainsString( 'id="agw-password-reset" class="agw-status agw-status--success" role="status" aria-live="polite" aria-atomic="true"', $html );
		$this->assertStringContainsString( 'aria-describedby="agw-login-instructions agw-registration-complete agw-password-reset"', $html );
		$this->assertStringContainsString( 'name="redirect_to" value="https://example.test/my-account/"', $html );
	}

	public function test_render_login_screen_outputs_error_state() {
		$screen = new ALYNT_AG_Frontend_Login_Screen();
		$_GET['login_error'] = 'alynt_ag_rate_limited';

		ob_start();
		$screen->render_login_screen( $this->settings );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'id="agw-login-error"', $html );
		$this->assertStringContainsString( 'role="alert" aria-live="assertive" aria-atomic="true"', $html );
		$this->assertStringContainsString( 'Too many attempts. Please wait a moment and try again.', $html );
		$this->assertStringContainsString( '<form class="agw-form" method="post" action="https://example.test/login" data-agw-retain-fields aria-describedby="agw-login-instructions agw-login-error">', $html );
		$this->assertStringContainsString( 'aria-describedby="agw-login-error"', $html );
		$this->assertStringContainsString( 'aria-invalid="true"', $html );
	}

	public function test_checkout_login_notice_links_to_registration_and_preserves_return_destination() {
		$screen = new ALYNT_AG_Frontend_Login_Screen();
		$this->settings['woocommerce_require_login_checkout'] = true;
		$_GET['redirect_to'] = 'https://example.test/checkout/';

		ob_start();
		$screen->render_login_screen( $this->settings );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'id="agw-checkout-login"', $html );
		$this->assertStringContainsString( 'Log in to complete your order', $html );
		$this->assertStringContainsString( 'create an account', $html );
		$this->assertStringContainsString( 'action=register&redirect_to=https%253A%252F%252Fexample.test%252Fcheckout%252F', $html );
		$this->assertStringContainsString( 'aria-describedby="agw-login-instructions agw-checkout-login"', $html );
		$this->assertStringContainsString( 'name="redirect_to" value="https://example.test/checkout/"', $html );
	}

	public function test_checkout_login_notice_omits_registration_link_when_registration_is_disabled() {
		$screen = new ALYNT_AG_Frontend_Login_Screen();
		$this->settings['woocommerce_require_login_checkout'] = true;
		$this->settings['registration_enabled'] = false;
		$_GET['redirect_to'] = 'https://example.test/checkout/';

		ob_start();
		$screen->render_login_screen( $this->settings );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'New account registration is currently unavailable.', $html );
		$this->assertStringNotContainsString( 'action=register', $html );
	}
}
