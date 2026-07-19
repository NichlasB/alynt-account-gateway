<?php
/**
 * Email template service tests.
 *
 * @package Alynt_Account_Gateway
 */

require_once __DIR__ . '/support/class-email-template-service-test-case.php';

/**
 * Tests template rendering, tokens, and responsive output.
 */
class EmailTemplateRenderingTest extends EmailTemplateServiceTestCase {

	public function test_supported_templates_include_required_account_emails() {
		$service = new ALYNT_AG_Email_Template_Service();
		$templates = $service->templates();

		$this->assertArrayHasKey( 'registration_confirmation', $templates );
		$this->assertArrayHasKey( 'password_reset', $templates );
		$this->assertArrayHasKey( 'password_changed', $templates );
		$this->assertArrayHasKey( 'new_user_welcome', $templates );
		$this->assertArrayHasKey( 'email_change_confirmation', $templates );
	}

	public function test_token_reference_documents_preview_tokens() {
		$service        = new ALYNT_AG_Email_Template_Service();
		$reference      = $service->token_reference();
		$preview_tokens = $service->preview_tokens();

		$this->assertSame( array_keys( $preview_tokens ), array_keys( $reference ) );
		$this->assertArrayHasKey( 'confirmation_url', $reference );
		$this->assertArrayHasKey( 'reset_url', $reference );
		$this->assertArrayHasKey( 'change_email_url', $reference );
		$this->assertArrayHasKey( 'dashboard_url', $reference );

		foreach ( $reference as $token => $item ) {
			$this->assertNotEmpty( $token );
			$this->assertNotEmpty( $item['label'] );
			$this->assertNotEmpty( $item['description'] );
		}
	}

	public function test_render_replaces_tokens_and_includes_branded_button() {
		$service  = new ALYNT_AG_Email_Template_Service();
		$settings = ALYNT_AG_Settings_Schema::defaults();
		$rendered = $service->render(
			'registration_confirmation',
			array(
				'first_name'       => 'Damon',
				'confirmation_url' => 'https://example.test/account?action=setpassword&token=abc',
				'expiry_hours'     => '24',
			),
			$settings
		);

		$this->assertIsArray( $rendered );
		$this->assertStringContainsString( 'Example Store', $rendered['subject'] );
		$this->assertStringContainsString( 'Damon', $rendered['html'] );
		$this->assertStringContainsString( 'Confirm Account', $rendered['html'] );
		$this->assertStringContainsString( 'https://example.test/account?action=setpassword&token=abc', $rendered['plain'] );
	}

	public function test_render_preserves_safe_rich_text_and_converts_it_to_plain_text() {
		$service  = new ALYNT_AG_Email_Template_Service();
		$settings = array_merge(
			ALYNT_AG_Settings_Schema::defaults(),
			array(
				'email_password_reset_body' => '<h2>Hello {{first_name}}</h2><p><strong>Important</strong> <a href="https://example.test/help">Read more</a></p><ul><li>First step</li></ul>',
			)
		);
		$rendered = $service->render(
			'password_reset',
			array(
				'first_name' => 'Damon',
				'reset_url'  => 'https://example.test/reset',
			),
			$settings
		);

		$this->assertStringContainsString( '<h2>Hello Damon</h2>', $rendered['html'] );
		$this->assertStringContainsString( '<strong>Important</strong>', $rendered['html'] );
		$this->assertStringContainsString( '<a href="https://example.test/help">Read more</a>', $rendered['html'] );
		$this->assertStringContainsString( '<ul><li style="font-size:20px;line-height:1.6;margin:0 0 8px;">First step</li></ul>', $rendered['html'] );
		$this->assertStringNotContainsString( '<h2>', $rendered['plain'] );
		$this->assertStringContainsString( 'Hello Damon', $rendered['plain'] );
		$this->assertStringContainsString( 'https://example.test/reset', $rendered['plain'] );
	}

	public function test_email_logo_uses_explicit_constrained_dimensions() {
		$GLOBALS['alynt_ag_test_attachment_urls']['123:full'] = 'https://example.test/logo.png';

		$service  = new ALYNT_AG_Email_Template_Service();
		$settings = array_merge(
			ALYNT_AG_Settings_Schema::defaults(),
			array(
				'brand_logo_id'        => 123,
				'brand_logo_max_width' => 150,
			)
		);
		$rendered = $service->render( 'password_changed', $service->preview_tokens(), $settings );

		$this->assertStringContainsString( 'src="https://example.test/logo.png"', $rendered['html'] );
		$this->assertStringContainsString( 'width="150"', $rendered['html'] );
		$this->assertStringContainsString( 'width:150px;max-width:100%;height:auto;', $rendered['html'] );
		$this->assertStringContainsString( 'display:block;margin:0 auto;', $rendered['html'] );
	}

	public function test_email_body_uses_responsive_reading_sizes() {
		$service  = new ALYNT_AG_Email_Template_Service();
		$settings = array_merge(
			ALYNT_AG_Settings_Schema::defaults(),
			array(
				'email_password_changed_body' => "Hi {{first_name}},\n\nThis confirms your password was changed.",
			)
		);
		$rendered = $service->render( 'password_changed', $service->preview_tokens(), $settings );

		$this->assertStringContainsString( 'class="agw-email-body"', $rendered['html'] );
		$this->assertStringContainsString( 'class="agw-email-body" style="font-size:20px;line-height:1.6;', $rendered['html'] );
		$this->assertStringContainsString( '<p style="font-size:20px;line-height:1.6;margin:0 0 16px;">Hi Damon,</p>', $rendered['html'] );
		$this->assertStringContainsString( '@media screen and (max-width: 599px)', $rendered['html'] );
		$this->assertStringContainsString( 'font-size: 16px !important;', $rendered['html'] );
		$this->assertStringContainsString( '@media screen and (min-width: 600px) and (max-width: 959px)', $rendered['html'] );
		$this->assertStringContainsString( 'font-size: 18px !important;', $rendered['html'] );
	}

	public function test_email_fallback_action_url_wraps_on_narrow_screens() {
		$service  = new ALYNT_AG_Email_Template_Service();
		$rendered = $service->render(
			'registration_confirmation',
			array(
				'first_name'       => 'Damon',
				'confirmation_url' => 'https://example.test/account?action=setpassword&alynt_ag_token=sample-token',
				'expiry_hours'     => '24',
			),
			ALYNT_AG_Settings_Schema::defaults()
		);

		$this->assertStringContainsString(
			'opacity:.78;word-break:break-all;overflow-wrap:anywhere;',
			$rendered['html']
		);
	}

	public function test_render_strips_unsafe_markup_and_escapes_token_markup() {
		$service  = new ALYNT_AG_Email_Template_Service();
		$settings = array_merge(
			ALYNT_AG_Settings_Schema::defaults(),
			array(
				'email_password_reset_body' => '<p onclick="bad()">Hello {{first_name}}</p><script>alert(1)</script><a href="javascript:alert(2)">Bad link</a>',
			)
		);
		$rendered = $service->render(
			'password_reset',
			array(
				'first_name' => '<img src=x onerror=bad()>Damon',
				'reset_url'  => 'https://example.test/reset',
			),
			$settings
		);

		$this->assertStringNotContainsString( '<script', $rendered['html'] );
		$this->assertStringNotContainsString( 'onclick=', $rendered['html'] );
		$this->assertStringNotContainsString( 'javascript:', $rendered['html'] );
		$this->assertStringNotContainsString( '<img', $rendered['html'] );
		$this->assertStringContainsString( '&lt;img src=x onerror=bad()&gt;Damon', $rendered['html'] );
	}

	public function test_preview_tokens_render_every_supported_template() {
		$service  = new ALYNT_AG_Email_Template_Service();
		$settings = ALYNT_AG_Settings_Schema::defaults();

		foreach ( array_keys( $service->templates() ) as $template ) {
			$rendered = $service->render( $template, $service->preview_tokens(), $settings );

			$this->assertIsArray( $rendered );
			$this->assertStringContainsString( 'Example Store', $rendered['subject'] );
			$this->assertStringContainsString( '<!doctype html>', $rendered['html'] );
			$this->assertStringContainsString( 'Damon', $rendered['html'] );
			$this->assertStringContainsString( $template, $rendered['html'] );
			$this->assertStringNotContainsString( '{{', $rendered['subject'] );
			$this->assertStringNotContainsString( '{{', $rendered['html'] );
			$this->assertStringNotContainsString( '{{', $rendered['plain'] );
		}
	}
}
