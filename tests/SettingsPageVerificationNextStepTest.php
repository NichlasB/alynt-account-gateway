<?php
/**
 * Focused structural test suite.
 *
 * @package Alynt_Account_Gateway
 */

require_once __DIR__ . '/support/class-settings-page-security-status-test-case.php';

/**
 * Tests generic verification next-step guidance.
 */
class SettingsPageVerificationNextStepTest extends SettingsPageSecurityStatusTestCase {

	public function test_security_verification_next_step_handles_generic_rows() {
		$settings_page = new ALYNT_AG_Settings_Page();

		$this->assertSame(
			'Review this blocked verification before changing policy.',
			$this->invoke_helper(
				$settings_page,
				'security_verification_next_step',
				array(
					(object) array(
						'provider' => 'custom_provider',
						'status'   => 'blocked_status',
						'blocked'  => 1,
					),
				)
			)
		);

		$this->assertSame(
			'No action needed unless this verification pattern changes.',
			$this->invoke_helper(
				$settings_page,
				'security_verification_next_step',
				array(
					(object) array(
						'provider' => 'custom_provider',
						'status'   => 'passed_status',
						'blocked'  => 0,
					),
				)
			)
		);
	}
}
