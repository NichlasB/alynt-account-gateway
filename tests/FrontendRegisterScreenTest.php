<?php
/**
 * Frontend registration screen service tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests the frontend registration screen.
 */
class FrontendRegisterScreenTest extends TestCase {

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
			'register_intro_text' => 'Create your customer account.',
			'terms_path'          => '/terms/',
			'privacy_path'        => '/legal/privacy/',
			'turnstile_site_key'  => '',
		);
	}

	protected function tearDown(): void {
		$_GET = array();

		parent::tearDown();
	}

	public function test_render_register_screen_outputs_form_defaults() {
		$screen = new ALYNT_AG_Frontend_Register_Screen();

		ob_start();
		$screen->render_register_screen( $this->settings );
		$html = ob_get_clean();

		$this->assertStringContainsString( '<h1 id="agw-screen-title" class="agw-title">Create Account</h1>', $html );
		$this->assertStringContainsString( '<div class="agw-notice" id="agw-register-instructions">', $html );
		$this->assertStringContainsString( 'Create your customer account.', $html );
		$this->assertStringContainsString( 'action="https://example.test/account"', $html );
		$this->assertStringContainsString( 'data-agw-registration-form data-agw-retain-fields aria-describedby="agw-register-instructions"', $html );
		$this->assertStringContainsString( 'name="alynt_ag_action" value="start_registration"', $html );
		$this->assertStringContainsString( 'name="alynt_ag_registration_nonce" value="test-nonce"', $html );
		$this->assertStringContainsString( 'id="agw-register-first"', $html );
		$this->assertStringContainsString( 'name="first_name"', $html );
		$this->assertStringContainsString( 'id="agw-register-last"', $html );
		$this->assertStringContainsString( 'name="last_name"', $html );
		$this->assertStringContainsString( 'id="agw-register-email"', $html );
		$this->assertStringContainsString( 'name="email"', $html );
		$this->assertStringContainsString( 'type="email" autocomplete="email" dir="ltr"', $html );
		$this->assertStringContainsString( 'id="agw-register-terms"', $html );
		$this->assertStringContainsString( 'href="https://example.test/terms/"', $html );
		$this->assertStringContainsString( 'href="https://example.test/legal/privacy/"', $html );
		$this->assertStringContainsString( 'role="status" aria-live="polite" aria-atomic="true"', $html );
		$this->assertStringContainsString( 'Verification will appear here when enabled.', $html );
		$this->assertStringContainsString( 'data-agw-registration-submit aria-disabled="false"', $html );
		$this->assertStringNotContainsString( 'data-agw-registration-submit disabled', $html );
		$this->assertStringContainsString( 'href="https://example.test/login"', $html );
		$this->assertStringNotContainsString( 'agw-register-error', $html );
	}

	public function test_render_register_screen_outputs_sent_state() {
		$screen = new ALYNT_AG_Frontend_Register_Screen();
		$_GET['registration_sent'] = '1';

		ob_start();
		$screen->render_register_screen( $this->settings );
		$html = ob_get_clean();

		$this->assertStringContainsString( '<h1 id="agw-screen-title" class="agw-title">Check Your Email</h1>', $html );
		$this->assertStringContainsString( 'If the details can be used, a confirmation email has been sent.', $html );
		$this->assertStringContainsString( 'id="agw-registration-sent" class="agw-status agw-status--success" role="status" aria-live="polite" aria-atomic="true"', $html );
		$this->assertStringContainsString( 'href="https://example.test/login"', $html );
		$this->assertStringNotContainsString( 'data-agw-registration-form', $html );
	}

	public function test_render_register_screen_outputs_error_state() {
		$screen = new ALYNT_AG_Frontend_Register_Screen();
		$_GET['registration_error'] = 'terms_required';

		ob_start();
		$screen->render_register_screen( $this->settings );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'id="agw-register-error"', $html );
		$this->assertStringContainsString( 'role="alert" aria-live="assertive" aria-atomic="true"', $html );
		$this->assertStringContainsString( 'Please accept the terms and privacy policy to continue.', $html );
		$this->assertStringContainsString( 'data-agw-registration-form data-agw-retain-fields aria-describedby="agw-register-instructions agw-register-error"', $html );
		$this->assertStringContainsString( 'aria-describedby="agw-register-error"', $html );
		$this->assertStringContainsString( 'id="agw-register-terms" name="terms" type="checkbox" required data-agw-registration-terms data-agw-retain aria-invalid="true"', $html );
	}

	public function test_render_register_screen_outputs_turnstile_slot_when_configured() {
		$screen = new ALYNT_AG_Frontend_Register_Screen();
		$this->settings['turnstile_site_key'] = 'site-key-123';

		ob_start();
		$screen->render_register_screen( $this->settings );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'aria-label="Account verification"', $html );
		$this->assertStringContainsString( 'class="cf-turnstile"', $html );
		$this->assertStringContainsString( 'data-agw-turnstile-widget', $html );
		$this->assertStringContainsString( 'data-sitekey="site-key-123"', $html );
		$this->assertStringNotContainsString( 'Verification will appear here when enabled.', $html );
	}

	public function test_registration_screen_preserves_valid_checkout_return_destination() {
		$screen = new ALYNT_AG_Frontend_Register_Screen();
		$_GET['redirect_to'] = 'https://example.test/checkout/';

		ob_start();
		$screen->render_register_screen( $this->settings );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'name="redirect_to" value="https://example.test/checkout/"', $html );
		$this->assertStringContainsString( 'href="https://example.test/login?redirect_to=https%253A%252F%252Fexample.test%252Fcheckout%252F"', $html );
	}

	public function test_registration_screen_rejects_external_return_destination() {
		$screen = new ALYNT_AG_Frontend_Register_Screen();
		$_GET['redirect_to'] = 'https://evil.example/checkout/';

		ob_start();
		$screen->render_register_screen( $this->settings );
		$html = ob_get_clean();

		$this->assertStringNotContainsString( 'name="redirect_to"', $html );
		$this->assertStringContainsString( 'href="https://example.test/login"', $html );
	}
}
