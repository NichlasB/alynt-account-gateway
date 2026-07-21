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

	public function test_recent_events_returns_error_when_database_read_fails() {
		$tables = ALYNT_AG_Database::tables();
		$GLOBALS['alynt_ag_test_db_results'][ $tables['diagnostics_logs'] ] = false;

		$result = ALYNT_AG_Diagnostics_Logger::recent_events();

		$this->assertTrue( is_wp_error( $result ) );
		$this->assertSame( 'alynt_ag_diagnostics_read_failed', $result->get_error_code() );
		unset( $GLOBALS['alynt_ag_test_db_results'][ $tables['diagnostics_logs'] ] );
	}
}
