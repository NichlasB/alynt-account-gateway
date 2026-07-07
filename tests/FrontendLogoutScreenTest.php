<?php
/**
 * Frontend logout screen service tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests the frontend logout confirmation screen.
 */
class FrontendLogoutScreenTest extends TestCase {

	/**
	 * Test settings.
	 *
	 * @var array<string,mixed>
	 */
	private $settings;

	protected function setUp(): void {
		parent::setUp();

		$this->settings = array(
			'account_action_base' => '/account',
			'after_login_redirect' => '/my-account/',
			'logout_intro_text'    => 'You are about to log out.',
		);
	}

	public function test_render_logout_screen_outputs_notice_and_actions() {
		$screen = new ALYNT_AG_Frontend_Logout_Screen();

		ob_start();
		$screen->render_logout_screen( $this->settings );
		$html = ob_get_clean();

		$this->assertStringContainsString( '<h1 id="agw-screen-title" class="agw-title">Log Out</h1>', $html );
		$this->assertStringContainsString( '<div class="agw-notice" id="agw-logout-instructions">', $html );
		$this->assertStringContainsString( 'You are about to log out.', $html );
		$this->assertStringContainsString( 'href="https://example.test/account?action=logout&confirm=1&_wpnonce=test-nonce"', $html );
		$this->assertStringContainsString( 'class="agw-button agw-button--primary"', $html );
		$this->assertStringContainsString( 'href="https://example.test/my-account/"', $html );
		$this->assertStringContainsString( 'class="agw-button agw-button--secondary"', $html );
		$this->assertStringContainsString( 'Cancel', $html );
	}

	public function test_render_logout_screen_suppresses_empty_notice() {
		$screen = new ALYNT_AG_Frontend_Logout_Screen();
		$this->settings['logout_intro_text'] = '   ';

		ob_start();
		$screen->render_logout_screen( $this->settings );
		$html = ob_get_clean();

		$this->assertStringNotContainsString( 'agw-notice', $html );
		$this->assertStringContainsString( 'href="https://example.test/account?action=logout&confirm=1&_wpnonce=test-nonce"', $html );
		$this->assertStringContainsString( 'href="https://example.test/my-account/"', $html );
	}
}
