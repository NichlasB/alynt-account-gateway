<?php
/**
 * Frontend state screen service tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests low-interaction frontend auth state screens.
 */
class FrontendStateScreensTest extends TestCase {

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
			'login_path'                 => '/login',
			'account_action_base'        => '/account',
			'registration_disabled_text' => 'Registration is currently closed.',
			'invalid_link_text'          => 'This link has expired. Request a new one.',
			'resend_confirmation_rate_limit_window' => 45,
		);
	}

	protected function tearDown(): void {
		$_GET = array();

		parent::tearDown();
	}

	public function test_render_registration_disabled_screen_outputs_notice_and_login_link() {
		$screens = new ALYNT_AG_Frontend_State_Screens();

		ob_start();
		$screens->render_registration_disabled_screen( $this->settings );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'Registration Unavailable', $html );
		$this->assertStringContainsString( '<div class="agw-notice">', $html );
		$this->assertStringContainsString( 'Registration is currently closed.', $html );
		$this->assertStringContainsString( 'href="https://example.test/login"', $html );
		$this->assertStringContainsString( 'Back to Login', $html );
	}

	public function test_render_invalid_link_screen_outputs_resend_form_defaults() {
		$screens = new ALYNT_AG_Frontend_State_Screens();

		ob_start();
		$screens->render_invalid_link_screen( $this->settings );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'Link Expired', $html );
		$this->assertStringContainsString( 'This link has expired. Request a new one.', $html );
		$this->assertStringContainsString( 'action="https://example.test/account?action=invalidlink"', $html );
		$this->assertStringContainsString( 'name="alynt_ag_action" value="resend_confirmation"', $html );
		$this->assertStringContainsString( 'name="alynt_ag_registration_nonce" value="test-nonce"', $html );
		$this->assertStringContainsString( 'id="agw-invalid-email"', $html );
		$this->assertStringContainsString( 'type="email" autocomplete="email" dir="ltr"', $html );
		$this->assertStringContainsString( 'Send New Link', $html );
		$this->assertStringNotContainsString( 'agw-status--success', $html );
		$this->assertStringNotContainsString( 'agw-resend-error', $html );
	}

	public function test_render_invalid_link_screen_outputs_success_and_error_states() {
		$screens = new ALYNT_AG_Frontend_State_Screens();
		$_GET['confirmation_resent'] = '1';
		$_GET['resend_error'] = 'alynt_ag_rate_limited';

		ob_start();
		$screens->render_invalid_link_screen( $this->settings );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'agw-status agw-status--success', $html );
		$this->assertStringContainsString( 'If a pending registration can be found, a new confirmation email has been sent.', $html );
		$this->assertStringContainsString( 'id="agw-resend-error"', $html );
		$this->assertStringContainsString( 'Too many confirmation email requests. Please wait for the resend window before trying again.', $html );
		$this->assertStringContainsString( 'id="agw-resend-guidance"', $html );
		$this->assertStringContainsString( 'Before requesting another link', $html );
		$this->assertStringContainsString( 'Wait 45 minutes before requesting another confirmation email.', $html );
		$this->assertStringContainsString( 'Use the newest confirmation email only.', $html );
		$this->assertStringContainsString( 'aria-describedby="agw-resend-error agw-resend-guidance"', $html );
		$this->assertStringContainsString( 'aria-invalid="true"', $html );
	}

	public function test_render_invalid_link_screen_omits_throttle_guidance_for_other_resend_errors() {
		$screens = new ALYNT_AG_Frontend_State_Screens();
		$_GET['resend_error'] = 'confirmation_email_failed';

		ob_start();
		$screens->render_invalid_link_screen( $this->settings );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'The confirmation email could not be sent. Please try again.', $html );
		$this->assertStringNotContainsString( 'Before requesting another link', $html );
		$this->assertStringNotContainsString( 'Wait 45 minutes before requesting another confirmation email.', $html );
	}
}
