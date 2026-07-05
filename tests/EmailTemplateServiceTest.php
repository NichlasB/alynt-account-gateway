<?php
/**
 * Email template service tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests branded email rendering.
 */
class EmailTemplateServiceTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		$GLOBALS['alynt_ag_test_mail'] = array();
		$GLOBALS['alynt_ag_test_options'] = array();
		$GLOBALS['alynt_ag_test_deleted_user_meta'] = array();
		$_POST = array();
	}

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

	public function test_send_uses_html_mail_headers() {
		$service = new ALYNT_AG_Email_Template_Service();
		$result  = $service->send(
			'password_reset',
			'customer@example.test',
			array(
				'first_name' => 'Damon',
				'reset_url'  => 'https://example.test/account?action=setpassword&key=abc',
			),
			ALYNT_AG_Settings_Schema::defaults()
		);

		$this->assertTrue( $result );
		$this->assertCount( 1, $GLOBALS['alynt_ag_test_mail'] );
		$this->assertSame( 'customer@example.test', $GLOBALS['alynt_ag_test_mail'][0]['to'] );
		$this->assertContains( 'Content-Type: text/html; charset=UTF-8', $GLOBALS['alynt_ag_test_mail'][0]['headers'] );
	}

	public function test_send_rejects_invalid_recipient_without_queuing_mail() {
		$service = new ALYNT_AG_Email_Template_Service();
		$result  = $service->send(
			'password_reset',
			'not-an-email',
			$service->preview_tokens(),
			ALYNT_AG_Settings_Schema::defaults()
		);

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'alynt_ag_invalid_email_recipient', $result->get_error_code() );
		$this->assertCount( 0, $GLOBALS['alynt_ag_test_mail'] );
	}

	public function test_send_rejects_unknown_template_without_queuing_mail() {
		$service = new ALYNT_AG_Email_Template_Service();
		$result  = $service->send(
			'missing_template',
			'customer@example.test',
			$service->preview_tokens(),
			ALYNT_AG_Settings_Schema::defaults()
		);

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'alynt_ag_unknown_email_template', $result->get_error_code() );
		$this->assertCount( 0, $GLOBALS['alynt_ag_test_mail'] );
	}

	public function test_password_reset_notification_filter_returns_branded_email_array() {
		$service = new ALYNT_AG_Email_Template_Service();
		$user    = new WP_User( 'customer@example.test' );

		$email = $service->filter_retrieve_password_notification_email(
			array(
				'to'      => 'customer@example.test',
				'subject' => 'Core subject',
				'message' => 'Core message',
				'headers' => array(),
			),
			'reset-key',
			'customer@example.test',
			$user
		);

		$this->assertStringContainsString( 'Reset your password', $email['subject'] );
		$this->assertStringContainsString( 'Damon', $email['message'] );
		$this->assertStringContainsString( 'key=reset-key', $email['message'] );
		$this->assertContains( 'Content-Type: text/html; charset=UTF-8', $email['headers'] );
	}

	public function test_password_changed_email_can_be_disabled() {
		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings'] = array(
			'email_password_changed_disabled' => true,
		);

		$service = new ALYNT_AG_Email_Template_Service();

		$this->assertFalse( $service->filter_send_password_change_email( true, new WP_User(), array() ) );
	}

	public function test_password_changed_email_filter_returns_branded_email_array() {
		$service = new ALYNT_AG_Email_Template_Service();
		$email   = $service->filter_password_change_email(
			array(
				'to'      => 'customer@example.test',
				'subject' => 'Core subject',
				'message' => 'Core message',
				'headers' => array(),
			),
			new WP_User( 'customer@example.test' ),
			array()
		);

		$this->assertStringContainsString( 'password was changed', $email['subject'] );
		$this->assertStringContainsString( 'Damon', $email['message'] );
		$this->assertContains( 'Content-Type: text/html; charset=UTF-8', $email['headers'] );
	}

	public function test_email_change_email_can_be_disabled() {
		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings'] = array(
			'email_change_confirmation_disabled' => true,
		);

		$service = new ALYNT_AG_Email_Template_Service();

		$this->assertFalse( $service->filter_send_email_change_email( true, new WP_User(), array() ) );
	}

	public function test_email_change_email_filter_returns_branded_email_array() {
		$service = new ALYNT_AG_Email_Template_Service();
		$email   = $service->filter_email_change_email(
			array(
				'to'      => 'customer@example.test',
				'subject' => 'Core subject',
				'message' => 'Core message',
				'headers' => array(),
			),
			new WP_User( 'customer@example.test' ),
			array()
		);

		$this->assertStringContainsString( 'Confirm your email address', $email['subject'] );
		$this->assertStringContainsString( 'Damon', $email['message'] );
		$this->assertContains( 'Content-Type: text/html; charset=UTF-8', $email['headers'] );
	}

	public function test_new_user_email_content_filter_returns_plain_branded_body_with_core_placeholder() {
		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings'] = array(
			'email_change_confirmation_body' => 'Hi {{first_name}}, confirm {{user_email}} here:',
		);

		$service = new ALYNT_AG_Email_Template_Service();
		$content = $service->filter_new_user_email_content(
			'Core message',
			array(
				'hash'     => 'abc123',
				'newemail' => 'new@example.test',
			)
		);

		$this->assertStringContainsString( 'Damon', $content );
		$this->assertStringContainsString( 'new@example.test', $content );
		$this->assertStringContainsString( '###ADMIN_URL###', $content );
		$this->assertStringNotContainsString( '<html', $content );
	}

	public function test_pending_profile_email_change_request_is_suppressed_when_disabled() {
		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings'] = array(
			'email_change_confirmation_disabled' => true,
		);
		$_POST['user_id'] = 123;
		$_POST['email']   = 'new@example.test';

		$service = new ALYNT_AG_Email_Template_Service();
		$content = $service->filter_new_user_email_content(
			'Core message',
			array(
				'hash'     => 'abc123',
				'newemail' => 'new@example.test',
			)
		);

		$this->assertSame( 'Core message', $content );
		$this->assertFalse(
			$service->filter_pre_wp_mail_for_profile_email_change(
				null,
				array(
					'to'      => 'new@example.test',
					'subject' => '[Example Store] Email Change Request',
					'message' => 'Core message',
				)
			)
		);
		$this->assertSame(
			array(
				array(
					'user_id'  => 123,
					'meta_key' => '_new_email',
				),
			),
			$GLOBALS['alynt_ag_test_deleted_user_meta']
		);
	}

	public function test_pending_profile_email_change_suppression_is_single_use() {
		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings'] = array(
			'email_change_confirmation_disabled' => true,
		);
		$_POST['user_id'] = 123;
		$_POST['email']   = 'new@example.test';

		$service = new ALYNT_AG_Email_Template_Service();
		$service->filter_new_user_email_content(
			'Core message',
			array(
				'hash'     => 'abc123',
				'newemail' => 'new@example.test',
			)
		);

		$this->assertFalse(
			$service->filter_pre_wp_mail_for_profile_email_change(
				null,
				array( 'to' => 'new@example.test' )
			)
		);
		$this->assertNull(
			$service->filter_pre_wp_mail_for_profile_email_change(
				null,
				array( 'to' => 'new@example.test' )
			)
		);
	}
}
