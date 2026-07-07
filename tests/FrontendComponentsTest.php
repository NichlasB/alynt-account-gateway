<?php
/**
 * Frontend component service tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests shared frontend component rendering helpers.
 */
class FrontendComponentsTest extends TestCase {

	public function test_render_notice_skips_empty_copy_after_tags_are_stripped() {
		$components = new ALYNT_AG_Frontend_Components();

		ob_start();
		$components->render_notice( '<span>   </span>' );
		$html = ob_get_clean();

		$this->assertSame( '', $html );
	}

	public function test_render_notice_outputs_autop_safe_notice_markup() {
		$components = new ALYNT_AG_Frontend_Components();

		ob_start();
		$components->render_notice( "Welcome.\n\nReset your password.", 'agw-login-instructions' );
		$html = ob_get_clean();

		$this->assertStringContainsString( '<div class="agw-notice" id="agw-login-instructions">', $html );
		$this->assertStringContainsString( '<p>Welcome.</p>', $html );
		$this->assertStringContainsString( '<p>Reset your password.</p>', $html );
	}

	public function test_has_notice_ignores_markup_without_text() {
		$components = new ALYNT_AG_Frontend_Components();

		$this->assertFalse( $components->has_notice( '<strong> </strong>' ) );
		$this->assertTrue( $components->has_notice( '<strong>Welcome.</strong>' ) );
	}

	public function test_describedby_attribute_outputs_sanitized_id_list() {
		$components = new ALYNT_AG_Frontend_Components();

		$this->assertSame(
			' aria-describedby="agw-login-instructions agw-login-error"',
			$components->describedby_attribute( array( 'agw-login-instructions', 'agw-login-error' ) )
		);
		$this->assertSame( '', $components->describedby_attribute( array( '' ) ) );
	}

	public function test_render_verification_slot_outputs_placeholder_when_turnstile_is_not_configured() {
		$components = new ALYNT_AG_Frontend_Components();

		ob_start();
		$components->render_verification_slot( array( 'turnstile_site_key' => '' ) );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'class="agw-verification-slot"', $html );
		$this->assertStringContainsString( 'role="status"', $html );
		$this->assertStringContainsString( 'Verification will appear here when enabled.', $html );
		$this->assertStringNotContainsString( 'cf-turnstile', $html );
	}

	public function test_render_verification_slot_outputs_turnstile_widget_when_configured() {
		$components = new ALYNT_AG_Frontend_Components();

		ob_start();
		$components->render_verification_slot( array( 'turnstile_site_key' => 'site-key-123' ) );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'aria-label="Account verification"', $html );
		$this->assertStringContainsString( 'class="cf-turnstile"', $html );
		$this->assertStringContainsString( 'data-sitekey="site-key-123"', $html );
		$this->assertStringNotContainsString( 'Verification will appear here when enabled.', $html );
	}
}
