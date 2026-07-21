<?php
/**
 * Operation lock tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests lock ownership, contention, privacy, and recovery.
 */
class OperationLockTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		$GLOBALS['alynt_ag_test_options'] = array();
		$GLOBALS['alynt_ag_test_deleted_options'] = array();
	}

	public function test_lock_option_name_is_bounded_and_does_not_expose_identifier() {
		$token = ALYNT_AG_Operation_Lock::acquire( 'pending registration', 'Person@Example.test', 10 );

		$this->assertIsString( $token );
		$this->assertCount( 1, $GLOBALS['alynt_ag_test_options'] );
		$name = array_key_first( $GLOBALS['alynt_ag_test_options'] );
		$this->assertStringStartsWith( 'alynt_ag_lock_pendingregistration_', $name );
		$this->assertStringNotContainsString( 'person', strtolower( $name ) );
		$this->assertLessThanOrEqual( 191, strlen( $name ) );
	}

	public function test_active_lock_rejects_competing_owner() {
		$first  = ALYNT_AG_Operation_Lock::acquire( 'rate_limit', 'bucket', 10 );
		$second = ALYNT_AG_Operation_Lock::acquire( 'rate_limit', 'bucket', 10 );

		$this->assertIsString( $first );
		$this->assertInstanceOf( WP_Error::class, $second );
		$this->assertSame( 'alynt_ag_operation_locked', $second->get_error_code() );
		$this->assertCount( 1, $GLOBALS['alynt_ag_test_options'] );
	}

	public function test_expired_lock_is_reclaimed() {
		$first = ALYNT_AG_Operation_Lock::acquire( 'rate_limit', 'bucket', 10 );
		$name  = array_key_first( $GLOBALS['alynt_ag_test_options'] );
		$GLOBALS['alynt_ag_test_options'][ $name ]['expires_at'] = time() - 1;

		$second = ALYNT_AG_Operation_Lock::acquire( 'rate_limit', 'bucket', 10 );

		$this->assertIsString( $first );
		$this->assertIsString( $second );
		$this->assertContains( $name, $GLOBALS['alynt_ag_test_deleted_options'] );
		$this->assertCount( 1, $GLOBALS['alynt_ag_test_options'] );
	}

	public function test_malformed_lock_state_is_reclaimed() {
		$first = ALYNT_AG_Operation_Lock::acquire( 'rate_limit', 'bucket', 10 );
		$name  = array_key_first( $GLOBALS['alynt_ag_test_options'] );
		$GLOBALS['alynt_ag_test_options'][ $name ] = 'corrupt';

		$second = ALYNT_AG_Operation_Lock::acquire( 'rate_limit', 'bucket', 10 );

		$this->assertIsString( $first );
		$this->assertIsString( $second );
		$this->assertContains( $name, $GLOBALS['alynt_ag_test_deleted_options'] );
		$this->assertIsArray( $GLOBALS['alynt_ag_test_options'][ $name ] );
	}

	public function test_release_requires_current_owner_token() {
		$token = ALYNT_AG_Operation_Lock::acquire( 'rate_limit', 'bucket', 10 );

		$this->assertFalse( ALYNT_AG_Operation_Lock::release( 'rate_limit', 'bucket', 'wrong-owner' ) );
		$this->assertCount( 1, $GLOBALS['alynt_ag_test_options'] );
		$this->assertTrue( ALYNT_AG_Operation_Lock::release( 'rate_limit', 'bucket', $token ) );
		$this->assertCount( 0, $GLOBALS['alynt_ag_test_options'] );
	}

	public function test_non_positive_ttl_is_clamped_to_one_second() {
		$before = time();
		$token  = ALYNT_AG_Operation_Lock::acquire( 'rate_limit', 'bucket', 0 );
		$value  = reset( $GLOBALS['alynt_ag_test_options'] );

		$this->assertIsString( $token );
		$this->assertGreaterThanOrEqual( $before + 1, $value['expires_at'] );
		$this->assertLessThanOrEqual( time() + 1, $value['expires_at'] );
	}
}
