<?php
/**
 * Password-change notification suppression tests.
 *
 * @package Alynt_Account_Gateway
 */

require_once __DIR__ . '/support/class-email-template-service-test-case.php';

/**
 * Tests suppression of native WordPress password-change notifications.
 */
class EmailPasswordChangeNotificationsTest extends EmailTemplateServiceTestCase {

	protected function setUp(): void {
		parent::setUp();
		$GLOBALS['alynt_ag_test_removed_actions'] = array();
	}

	public function test_disabled_setting_removes_native_admin_password_notification() {
		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings'] = array(
			'email_password_changed_disabled' => true,
		);

		$service = new ALYNT_AG_Email_Password_Change_Notifications();
		$service->maybe_suppress_admin_notification();

		$this->assertSame(
			array(
				array(
					'hook'     => 'after_password_reset',
					'callback' => 'wp_password_change_notification',
					'priority' => 10,
				),
			),
			$GLOBALS['alynt_ag_test_removed_actions']
		);
	}

	public function test_enabled_setting_keeps_native_admin_password_notification() {
		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings'] = array(
			'email_password_changed_disabled' => false,
		);

		$service = new ALYNT_AG_Email_Password_Change_Notifications();
		$service->maybe_suppress_admin_notification();

		$this->assertSame( array(), $GLOBALS['alynt_ag_test_removed_actions'] );
	}

	public function test_disabled_setting_suppresses_native_admin_password_mail() {
		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings'] = array(
			'email_password_changed_disabled' => true,
		);

		$service = new ALYNT_AG_Email_Password_Change_Notifications();

		$this->assertFalse(
			$service->filter_pre_wp_mail(
				null,
				array(
					'to'      => 'admin@example.test',
					'subject' => '[Example Site] Password Changed',
					'message' => "Password changed for user: customer\n",
				)
			)
		);
	}

	public function test_disabled_setting_does_not_suppress_unrelated_mail() {
		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings'] = array(
			'email_password_changed_disabled' => true,
		);

		$service = new ALYNT_AG_Email_Password_Change_Notifications();

		$this->assertNull(
			$service->filter_pre_wp_mail(
				null,
				array(
					'to'      => 'admin@example.test',
					'subject' => '[Example Site] Other Notice',
					'message' => "Password changed for user: customer\n",
				)
			)
		);
	}
}
