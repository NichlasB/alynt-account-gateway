<?php
/**
 * Rate limiter tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests transient-backed rate limiting.
 */
class RateLimiterTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		$GLOBALS['alynt_ag_test_transients'] = array();
		$_SERVER['REMOTE_ADDR'] = '203.0.113.10';
	}

	public function test_bucket_key_does_not_expose_identifier_or_ip() {
		$limiter = new ALYNT_AG_Rate_Limiter();
		$key     = $limiter->get_bucket_key( 'registration', 'damon@example.test' );

		$this->assertStringStartsWith( 'alynt_ag_rl_', $key );
		$this->assertStringNotContainsString( 'damon', $key );
		$this->assertStringNotContainsString( '203.0.113.10', $key );
	}

	public function test_check_and_increment_blocks_after_limit() {
		$limiter = new ALYNT_AG_Rate_Limiter();

		$this->assertTrue( $limiter->check_and_increment( 'registration', 'damon@example.test', 2, 60 ) );
		$this->assertTrue( $limiter->check_and_increment( 'registration', 'damon@example.test', 2, 60 ) );

		$result = $limiter->check_and_increment( 'registration', 'damon@example.test', 2, 60 );

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'alynt_ag_rate_limited', $result->get_error_code() );
	}
}
