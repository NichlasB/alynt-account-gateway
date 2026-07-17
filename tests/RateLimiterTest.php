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
		$GLOBALS['alynt_ag_test_filters']    = array();
		$_SERVER['REMOTE_ADDR'] = '203.0.113.10';
		unset( $_SERVER['HTTP_CF_CONNECTING_IP'], $_SERVER['HTTP_X_FORWARDED_FOR'] );
	}

	public function test_bucket_key_does_not_expose_identifier_or_ip() {
		$limiter = new ALYNT_AG_Rate_Limiter();
		$key     = $limiter->get_bucket_key( 'registration', 'damon@example.test' );

		$this->assertStringStartsWith( 'alynt_ag_rl_', $key );
		$this->assertStringNotContainsString( 'damon', $key );
		$this->assertStringNotContainsString( '203.0.113.10', $key );
	}

	public function test_bucket_key_normalizes_identifier_case() {
		$limiter = new ALYNT_AG_Rate_Limiter();

		$this->assertSame(
			$limiter->get_bucket_key( 'registration', 'Damon@Example.Test' ),
			$limiter->get_bucket_key( 'registration', 'damon@example.test' )
		);
	}

	public function test_spoofed_forwarded_headers_do_not_change_bucket_by_default() {
		$limiter = new ALYNT_AG_Rate_Limiter();
		$key     = $limiter->get_bucket_key( 'login', 'damon@example.test' );

		$_SERVER['HTTP_CF_CONNECTING_IP'] = '198.51.100.21';
		$_SERVER['HTTP_X_FORWARDED_FOR'] = '198.51.100.22';

		$this->assertSame( $key, $limiter->get_bucket_key( 'login', 'damon@example.test' ) );

		$_SERVER['HTTP_CF_CONNECTING_IP'] = '192.0.2.31';
		$_SERVER['HTTP_X_FORWARDED_FOR'] = '192.0.2.32';

		$this->assertSame( $key, $limiter->get_bucket_key( 'login', 'damon@example.test' ) );
	}

	public function test_check_and_increment_blocks_after_limit() {
		$limiter = new ALYNT_AG_Rate_Limiter();

		$this->assertTrue( $limiter->check_and_increment( 'registration', 'damon@example.test', 2, 60 ) );
		$this->assertTrue( $limiter->check_and_increment( 'registration', 'damon@example.test', 2, 60 ) );

		$result = $limiter->check_and_increment( 'registration', 'damon@example.test', 2, 60 );

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'alynt_ag_rate_limited', $result->get_error_code() );
	}

	public function test_check_and_increment_stores_privacy_preserving_bucket_metadata() {
		$limiter = new ALYNT_AG_Rate_Limiter();

		$this->assertTrue( $limiter->check_and_increment( 'login', 'damon@example.test', 1, 15 ) );
		$result = $limiter->check_and_increment( 'login', 'damon@example.test', 1, 15 );

		$this->assertInstanceOf( WP_Error::class, $result );

		$meta_transients = array_filter(
			$GLOBALS['alynt_ag_test_transients'],
			static function ( $value, $key ) {
				return 0 === strpos( (string) $key, 'alynt_ag_rl_meta_' );
			},
			ARRAY_FILTER_USE_BOTH
		);

		$this->assertCount( 1, $meta_transients );

		$meta = reset( $meta_transients );
		$this->assertSame( 'login', $meta['value']['action'] );
		$this->assertSame( 1, $meta['value']['count'] );
		$this->assertSame( 1, $meta['value']['limit'] );
		$this->assertTrue( $meta['value']['locked'] );
		$this->assertArrayHasKey( 'expires_at', $meta['value'] );
		$this->assertStringNotContainsString( 'damon', wp_json_encode( $meta['value'] ) );
		$this->assertStringNotContainsString( '203.0.113.10', wp_json_encode( $meta['value'] ) );
	}
}
