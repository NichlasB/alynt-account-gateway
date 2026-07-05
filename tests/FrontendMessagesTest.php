<?php
/**
 * Frontend message catalog tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests frontend titles and public error messages.
 */
class FrontendMessagesTest extends TestCase {

	public function test_screen_title_returns_known_title_and_login_fallback() {
		$messages = new ALYNT_AG_Frontend_Messages();

		$this->assertSame( 'Create Account', $messages->screen_title( 'register' ) );
		$this->assertSame( 'Log In', $messages->screen_title( 'unknown' ) );
	}

	public function test_registration_error_returns_known_message_and_neutral_fallback() {
		$messages = new ALYNT_AG_Frontend_Messages();

		$this->assertSame( 'Please accept the terms and privacy policy to continue.', $messages->registration_error( 'terms_required' ) );
		$this->assertSame( 'The registration could not be started. Please try again.', $messages->registration_error( 'unknown' ) );
	}

	public function test_registration_error_returns_frontend_safe_provider_messages() {
		$messages = new ALYNT_AG_Frontend_Messages();

		$this->assertSame( 'This email address cannot be used for registration.', $messages->registration_error( 'alynt_ag_reoon_blocked' ) );
		$this->assertSame( 'Email verification is not available right now. Please try again later.', $messages->registration_error( 'alynt_ag_reoon_missing' ) );
		$this->assertSame( 'Email verification is temporarily unavailable. Please try again later.', $messages->registration_error( 'alynt_ag_reoon_request_failed' ) );
		$this->assertSame( 'Email verification is temporarily unavailable. Please try again later.', $messages->registration_error( 'alynt_ag_reoon_invalid_response' ) );
		$this->assertSame( 'Please complete the verification challenge and try again.', $messages->registration_error( 'alynt_ag_turnstile_failed' ) );
		$this->assertSame( 'Verification is not available right now. Please try again later.', $messages->registration_error( 'alynt_ag_turnstile_missing' ) );
		$this->assertSame( 'Verification is temporarily unavailable. Please try again later.', $messages->registration_error( 'alynt_ag_turnstile_request_failed' ) );
	}

	public function test_resend_error_returns_known_message_and_neutral_fallback() {
		$messages = new ALYNT_AG_Frontend_Messages();

		$this->assertSame( 'Too many confirmation email requests. Please wait a moment and try again.', $messages->resend_error( 'alynt_ag_rate_limited' ) );
		$this->assertSame( 'The confirmation email could not be sent. Please try again.', $messages->resend_error( 'unknown' ) );
	}

	public function test_password_error_returns_known_message_and_neutral_fallback() {
		$messages = new ALYNT_AG_Frontend_Messages();

		$this->assertSame( 'The passwords do not match.', $messages->password_error( 'password_mismatch' ) );
		$this->assertSame( 'Your account could not be created. Please try again.', $messages->password_error( 'unknown' ) );
	}
}
