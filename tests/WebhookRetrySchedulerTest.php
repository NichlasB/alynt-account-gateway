<?php
/**
 * Webhook retry scheduler tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests bounded and identity-aware webhook scheduling.
 */
class WebhookRetrySchedulerTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		$GLOBALS['alynt_ag_test_single_events'] = array();
		$GLOBALS['alynt_ag_test_scheduled_hooks'] = array();
		$GLOBALS['alynt_ag_test_db_inserts'] = array();
		$GLOBALS['alynt_ag_test_options'] = array();
		unset( $GLOBALS['alynt_ag_test_schedule_single_event_result'] );
	}

	protected function tearDown(): void {
		unset( $GLOBALS['alynt_ag_test_schedule_single_event_result'] );
		parent::tearDown();
	}

	public function test_initial_schedule_failure_is_reported_without_recording_event() {
		$GLOBALS['alynt_ag_test_schedule_single_event_result'] = false;
		$scheduler = new ALYNT_AG_Webhook_Retry_Scheduler();
		$result    = $scheduler->schedule_initial(
			ALYNT_AG_Webhook_Dispatcher::DELIVERY_HOOK,
			321,
			$this->envelope( 'evt_failure' )
		);

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'alynt_ag_webhook_schedule_failed', $result->get_error_code() );
		$this->assertCount( 0, $GLOBALS['alynt_ag_test_single_events'] );
	}

	public function test_initial_schedule_deduplicates_exact_event_identity_only() {
		$scheduler = new ALYNT_AG_Webhook_Retry_Scheduler();
		$first     = $this->envelope( 'evt_first' );
		$second    = $this->envelope( 'evt_second' );

		$this->assertTrue( $scheduler->schedule_initial( ALYNT_AG_Webhook_Dispatcher::DELIVERY_HOOK, 321, $first ) );
		$this->assertTrue( $scheduler->schedule_initial( ALYNT_AG_Webhook_Dispatcher::DELIVERY_HOOK, 321, $first ) );
		$this->assertTrue( $scheduler->schedule_initial( ALYNT_AG_Webhook_Dispatcher::DELIVERY_HOOK, 321, $second ) );
		$this->assertCount( 2, $GLOBALS['alynt_ag_test_single_events'] );
		$this->assertSame( 'evt_first', $GLOBALS['alynt_ag_test_single_events'][0]['args'][1]['payload']['id'] );
		$this->assertSame( 'evt_second', $GLOBALS['alynt_ag_test_single_events'][1]['args'][1]['payload']['id'] );
	}

	public function test_retry_preserves_envelope_and_stops_at_limit() {
		$scheduler = new ALYNT_AG_Webhook_Retry_Scheduler();
		$envelope  = $this->envelope( 'evt_retry' );

		$this->assertTrue( $scheduler->schedule( ALYNT_AG_Webhook_Dispatcher::RETRY_HOOK, 2, 321, 0, true, $envelope ) );
		$this->assertSame( 1, $GLOBALS['alynt_ag_test_single_events'][0]['args'][1] );
		$this->assertSame( $envelope, $GLOBALS['alynt_ag_test_single_events'][0]['args'][2] );
		$this->assertFalse( $scheduler->schedule( ALYNT_AG_Webhook_Dispatcher::RETRY_HOOK, 2, 321, 2, true, $envelope ) );
		$this->assertCount( 1, $GLOBALS['alynt_ag_test_single_events'] );
	}

	public function test_retry_schedule_failure_is_bounded_and_logged() {
		$GLOBALS['alynt_ag_test_schedule_single_event_result'] = false;
		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings'] = array(
			'diagnostics_enabled'   => true,
			'diagnostics_min_level' => 'debug',
		);
		$scheduler = new ALYNT_AG_Webhook_Retry_Scheduler();

		$this->assertFalse(
			$scheduler->schedule(
				ALYNT_AG_Webhook_Dispatcher::RETRY_HOOK,
				2,
				321,
				0,
				true,
				$this->envelope( 'evt_failure' )
			)
		);
		$this->assertCount( 0, $GLOBALS['alynt_ag_test_single_events'] );
		$this->assertCount( 1, $GLOBALS['alynt_ag_test_db_inserts'] );
		$this->assertSame( 'webhook_retry_schedule_failed', $GLOBALS['alynt_ag_test_db_inserts'][0]['data']['event_code'] );
	}

	private function envelope( $event_id ) {
		return array(
			'url'      => 'https://hooks.example.test/account-created',
			'payload'  => array(
				'id'    => $event_id,
				'event' => 'account.created',
				'user'  => array( 'id' => 321 ),
				'site'  => array( 'url' => 'https://example.test/' ),
			),
			'settings' => array(),
		);
	}
}
