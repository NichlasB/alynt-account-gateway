<?php
/**
 * Email template service tests.
 *
 * @package Alynt_Account_Gateway
 */

require_once __DIR__ . '/support/class-email-template-service-test-case.php';

/**
 * Tests template sending validation.
 */
class EmailTemplateSendingTest extends EmailTemplateServiceTestCase {

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
}
