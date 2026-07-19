<?php
/**
 * Email template service tests.
 *
 * @package Alynt_Account_Gateway
 */

require_once __DIR__ . '/support/class-email-template-service-test-case.php';

/**
 * Tests WordPress account-email filters and suppression.
 */
class EmailTemplateWordPressFiltersTest extends EmailTemplateServiceTestCase {

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
