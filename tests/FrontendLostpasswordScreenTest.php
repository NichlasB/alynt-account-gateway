<?php
/**
 * Frontend lost-password screen service tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests the frontend lost-password screen.
 */
class FrontendLostpasswordScreenTest extends TestCase {

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
			'account_action_base'       => '/account',
			'login_path'                => '/login',
			'lostpassword_intro_text'   => 'Enter your email address to receive a password reset link.',
		);
	}

	protected function tearDown(): void {
		$_GET = array();

		parent::tearDown();
	}

	public function test_render_lostpassword_screen_outputs_form_defaults() {
		$screen = new ALYNT_AG_Frontend_Lostpassword_Screen();

		ob_start();
		$screen->render_lostpassword_screen( $this->settings );
		$html = ob_get_clean();

		$this->assertStringContainsString( '<h1 id="agw-screen-title" class="agw-title">Reset Password</h1>', $html );
		$this->assertStringContainsString( '<div class="agw-notice" id="agw-lostpassword-instructions">', $html );
		$this->assertStringContainsString( 'Enter your email address to receive a password reset link.', $html );
		$this->assertStringContainsString( 'action="https://example.test/account?action=lostpassword"', $html );
		$this->assertStringContainsString( 'aria-describedby="agw-lostpassword-instructions"', $html );
		$this->assertStringContainsString( 'name="alynt_ag_action" value="lostpassword"', $html );
		$this->assertStringContainsString( 'name="alynt_ag_auth_nonce" value="test-nonce"', $html );
		$this->assertStringContainsString( 'id="agw-lost-email"', $html );
		$this->assertStringContainsString( 'name="user_login"', $html );
		$this->assertStringContainsString( 'type="email" autocomplete="email" dir="ltr"', $html );
		$this->assertStringContainsString( 'Back to Login', $html );
		$this->assertStringNotContainsString( 'agw-lostpassword-error', $html );
	}

	public function test_render_lostpassword_screen_outputs_error_state_from_request() {
		$screen = new ALYNT_AG_Frontend_Lostpassword_Screen();
		$_GET['reset_error'] = 'alynt_ag_rate_limited';

		ob_start();
		$screen->render_lostpassword_screen( $this->settings );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'id="agw-lostpassword-error"', $html );
		$this->assertStringContainsString( 'role="alert" aria-live="assertive" aria-atomic="true"', $html );
		$this->assertStringContainsString( 'Too many attempts. Please wait a moment and try again.', $html );
		$this->assertStringContainsString( 'aria-describedby="agw-lostpassword-instructions agw-lostpassword-error"', $html );
		$this->assertStringContainsString( 'aria-describedby="agw-lostpassword-error"', $html );
		$this->assertStringContainsString( 'aria-invalid="true"', $html );
	}

	public function test_render_lostpassword_screen_outputs_forced_error_state() {
		$screen = new ALYNT_AG_Frontend_Lostpassword_Screen();

		ob_start();
		$screen->render_lostpassword_screen( $this->settings, 'invalid_or_expired_token' );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'This reset link is invalid or has expired. Please request a new link.', $html );
		$this->assertStringContainsString( 'aria-describedby="agw-lostpassword-instructions agw-lostpassword-error"', $html );
		$this->assertStringContainsString( 'aria-describedby="agw-lostpassword-error"', $html );
	}

	public function test_render_lostpassword_screen_outputs_reset_sent_state() {
		$screen = new ALYNT_AG_Frontend_Lostpassword_Screen();
		$_GET['reset_sent'] = '1';

		ob_start();
		$screen->render_lostpassword_screen( $this->settings );
		$html = ob_get_clean();

		$this->assertStringContainsString( '<h1 id="agw-screen-title" class="agw-title">Check Your Email</h1>', $html );
		$this->assertStringContainsString( 'id="agw-lostpassword-sent" class="agw-status agw-status--success" role="status" aria-live="polite" aria-atomic="true"', $html );
		$this->assertStringContainsString( 'If an account can receive password reset instructions', $html );
		$this->assertStringContainsString( 'href="https://example.test/login"', $html );
		$this->assertStringNotContainsString( 'name="alynt_ag_action" value="lostpassword"', $html );
	}
}
