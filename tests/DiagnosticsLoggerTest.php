<?php
/**
 * Diagnostics logger tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests diagnostics redaction behavior.
 */
class DiagnosticsLoggerTest extends TestCase {

	public function test_redaction_masks_sensitive_values() {
		$redacted = ALYNT_AG_Diagnostics_Logger::redact_context(
			array(
				'api_key' => 'secret-value',
				'nested'  => array(
					'token'      => 'token-value',
					'user_email' => 'customer@example.test',
				),
				'email'         => 'customer@example.test',
				'email_address' => 'customer@example.test',
				'safe'          => 'visible',
			)
		);

		$this->assertSame( '[redacted]', $redacted['api_key'] );
		$this->assertSame( '[redacted]', $redacted['nested']['token'] );
		$this->assertSame( '[redacted]', $redacted['nested']['user_email'] );
		$this->assertSame( '[redacted]', $redacted['email'] );
		$this->assertSame( '[redacted]', $redacted['email_address'] );
		$this->assertSame( 'visible', $redacted['safe'] );
	}

	public function test_redaction_truncates_long_strings() {
		$redacted = ALYNT_AG_Diagnostics_Logger::redact_context(
			array(
				'message' => str_repeat( 'a', 600 ),
			)
		);

		$this->assertStringEndsWith( '... [truncated]', $redacted['message'] );
		$this->assertLessThan( 530, strlen( $redacted['message'] ) );
	}
}
