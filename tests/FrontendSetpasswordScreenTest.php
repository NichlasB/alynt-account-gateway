<?php
/**
 * Frontend set-password screen service tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

class ALYNT_AG_Test_Setpassword_Auth_Service extends ALYNT_AG_Auth_Service {
	public $valid = true;

	public function validate_password_reset_key( $key, $login ) {
		if ( ! $this->valid ) {
			return new WP_Error( 'invalid_or_expired_token', 'Invalid reset key.' );
		}

		return (object) array(
			'ID'         => 123,
			'user_login' => $login,
		);
	}
}

class ALYNT_AG_Test_Setpassword_Registration_Service extends ALYNT_AG_Registration_Service {
	public $valid = true;

	public function confirm_pending_token( $token ) {
		if ( ! $this->valid ) {
			return new WP_Error( 'invalid_or_expired_token', 'Invalid registration token.' );
		}

		return (object) array(
			'id'     => 456,
			'status' => 'email_confirmed',
		);
	}
}

/**
 * Tests the frontend set-password screen.
 */
class FrontendSetpasswordScreenTest extends TestCase {

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
			'account_action_base'     => '/account',
			'login_path'              => '/login',
			'setpassword_intro_text'  => 'Choose a strong password.',
			'lostpassword_intro_text' => 'Reset your password.',
			'invalid_link_text'       => 'This link is no longer valid.',
		);
	}

	protected function tearDown(): void {
		$_GET = array();

		parent::tearDown();
	}

	public function test_render_password_form_outputs_defaults_and_requirements() {
		$screen = new ALYNT_AG_Frontend_Setpassword_Screen();

		ob_start();
		$screen->render_password_form(
			$this->settings,
			'https://example.test/account?action=setpassword',
			'reset_password',
			'alynt_ag_reset_password',
			'alynt_ag_auth_nonce',
			array(
				'key'   => 'reset-key',
				'login' => 'customer@example.test',
			),
			''
		);
		$html = ob_get_clean();

		$this->assertStringContainsString( '<h1 id="agw-screen-title" class="agw-title">Set New Password</h1>', $html );
		$this->assertStringContainsString( '<div class="agw-notice" id="agw-setpassword-instructions">', $html );
		$this->assertStringContainsString( 'Choose a strong password.', $html );
		$this->assertStringContainsString( 'action="https://example.test/account?action=setpassword"', $html );
		$this->assertStringContainsString( 'data-agw-password-form aria-describedby="agw-setpassword-instructions"', $html );
		$this->assertStringContainsString( 'name="alynt_ag_action" value="reset_password"', $html );
		$this->assertStringContainsString( 'name="alynt_ag_auth_nonce" value="test-nonce"', $html );
		$this->assertStringContainsString( 'name="key" value="reset-key"', $html );
		$this->assertStringContainsString( 'name="login" value="customer@example.test"', $html );
		$this->assertStringContainsString( 'id="agw-set-password"', $html );
		$this->assertStringContainsString( 'name="password"', $html );
		$this->assertStringContainsString( 'name="password" type="password" autocomplete="new-password" dir="ltr"', $html );
		$this->assertStringContainsString( 'id="agw-set-confirm"', $html );
		$this->assertStringContainsString( 'name="password_confirm"', $html );
		$this->assertStringContainsString( 'name="password_confirm" type="password" autocomplete="new-password" dir="ltr"', $html );
		$this->assertStringContainsString( 'aria-controls="agw-set-password"', $html );
		$this->assertStringContainsString( 'aria-controls="agw-set-confirm"', $html );
		$this->assertSame( 2, substr_count( $html, 'data-agw-password-toggle' ) );
		$this->assertSame( 2, substr_count( $html, 'aria-label="Show password"' ) );
		$this->assertSame( 2, substr_count( $html, 'data-agw-password-visibility-status role="status" aria-live="polite" aria-atomic="true"' ) );
		$this->assertStringContainsString( 'data-agw-strength', $html );
		$this->assertStringContainsString( 'role="status" aria-live="polite" aria-atomic="true"', $html );
		$this->assertStringContainsString( 'data-agw-password-requirements', $html );
		$this->assertSame( 6, substr_count( $html, 'role="checkbox" aria-checked="false" aria-disabled="true"' ) );
		$this->assertStringNotContainsString( 'aria-current="true"', $html );
		$this->assertStringNotContainsString( 'aria-current="false"', $html );
		$this->assertStringContainsString( 'At least 12 characters', $html );
		$this->assertStringContainsString( 'At least one uppercase letter', $html );
		$this->assertStringContainsString( 'At least one lowercase letter', $html );
		$this->assertStringContainsString( 'At least one number', $html );
		$this->assertStringContainsString( 'At least one special symbol', $html );
		$this->assertStringContainsString( 'Passwords match', $html );
		$this->assertStringContainsString( 'data-agw-password-submit disabled aria-disabled="true"', $html );
		$this->assertStringNotContainsString( 'agw-password-error', $html );
	}

	public function test_render_password_form_outputs_error_state() {
		$screen = new ALYNT_AG_Frontend_Setpassword_Screen();

		ob_start();
		$screen->render_password_form(
			$this->settings,
			'https://example.test/account?action=setpassword',
			'complete_registration',
			'alynt_ag_complete_registration',
			'alynt_ag_registration_nonce',
			array(
				'alynt_ag_token' => 'registration-token',
			),
			'password_mismatch'
		);
		$html = ob_get_clean();

		$this->assertStringContainsString( 'id="agw-password-error"', $html );
		$this->assertStringContainsString( 'role="alert" aria-live="assertive" aria-atomic="true"', $html );
		$this->assertStringContainsString( 'The passwords do not match.', $html );
		$this->assertStringContainsString( 'data-agw-password-form aria-describedby="agw-setpassword-instructions agw-password-error"', $html );
		$this->assertStringContainsString( 'aria-describedby="agw-password-error agw-password-status agw-password-requirements"', $html );
		$this->assertStringContainsString( 'aria-invalid="true"', $html );
		$this->assertStringContainsString( 'name="alynt_ag_registration_nonce" value="test-nonce"', $html );
		$this->assertStringContainsString( 'name="alynt_ag_token" value="registration-token"', $html );
	}

	public function test_render_setpassword_screen_outputs_registration_token_form() {
		$screen = new ALYNT_AG_Frontend_Setpassword_Screen(
			new ALYNT_AG_Test_Setpassword_Auth_Service(),
			new ALYNT_AG_Test_Setpassword_Registration_Service()
		);
		$_GET['alynt_ag_token'] = 'registration-token';
		$_GET['password_error'] = 'alynt_ag_password_length';

		ob_start();
		$screen->render_setpassword_screen( $this->settings );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'action="https://example.test/account?action=setpassword&alynt_ag_token=registration-token"', $html );
		$this->assertStringContainsString( 'name="alynt_ag_action" value="complete_registration"', $html );
		$this->assertStringContainsString( 'name="alynt_ag_registration_nonce" value="test-nonce"', $html );
		$this->assertStringContainsString( 'name="alynt_ag_token" value="registration-token"', $html );
		$this->assertStringContainsString( 'Password must be at least 12 characters.', $html );
	}

	public function test_render_setpassword_screen_outputs_native_reset_form() {
		$screen = new ALYNT_AG_Frontend_Setpassword_Screen(
			new ALYNT_AG_Test_Setpassword_Auth_Service(),
			new ALYNT_AG_Test_Setpassword_Registration_Service()
		);
		$_GET['key'] = 'reset-key';
		$_GET['login'] = 'customer@example.test';

		ob_start();
		$screen->render_setpassword_screen( $this->settings );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'action="https://example.test/account?action=setpassword&key=reset-key&login=customer%2540example.test"', $html );
		$this->assertStringContainsString( 'name="alynt_ag_action" value="reset_password"', $html );
		$this->assertStringContainsString( 'name="alynt_ag_auth_nonce" value="test-nonce"', $html );
		$this->assertStringContainsString( 'name="key" value="reset-key"', $html );
		$this->assertStringContainsString( 'name="login" value="customer@example.test"', $html );
	}

	public function test_render_setpassword_screen_outputs_invalid_link_when_registration_token_fails() {
		$registration = new ALYNT_AG_Test_Setpassword_Registration_Service();
		$registration->valid = false;
		$screen = new ALYNT_AG_Frontend_Setpassword_Screen(
			new ALYNT_AG_Test_Setpassword_Auth_Service(),
			$registration
		);
		$_GET['alynt_ag_token'] = 'bad-token';

		ob_start();
		$screen->render_setpassword_screen( $this->settings );
		$html = ob_get_clean();

		$this->assertStringContainsString( '<h1 id="agw-screen-title" class="agw-title">Link Expired</h1>', $html );
		$this->assertStringContainsString( 'This link is no longer valid.', $html );
		$this->assertStringContainsString( 'name="alynt_ag_action" value="resend_confirmation"', $html );
		$this->assertStringNotContainsString( 'data-agw-password-form', $html );
	}

	public function test_render_setpassword_screen_outputs_lostpassword_error_when_native_key_fails() {
		$auth = new ALYNT_AG_Test_Setpassword_Auth_Service();
		$auth->valid = false;
		$screen = new ALYNT_AG_Frontend_Setpassword_Screen(
			$auth,
			new ALYNT_AG_Test_Setpassword_Registration_Service()
		);
		$_GET['key'] = 'bad-key';
		$_GET['login'] = 'customer@example.test';

		ob_start();
		$screen->render_setpassword_screen( $this->settings );
		$html = ob_get_clean();

		$this->assertStringContainsString( '<h1 id="agw-screen-title" class="agw-title">Reset Password</h1>', $html );
		$this->assertStringContainsString( 'This reset link is invalid or has expired. Please request a new link.', $html );
		$this->assertStringContainsString( 'name="alynt_ag_action" value="lostpassword"', $html );
		$this->assertStringNotContainsString( 'data-agw-password-form', $html );
	}
}
