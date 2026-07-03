<?php
/**
 * Webhook dispatcher tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests account-created webhook dispatch.
 */
class WebhookDispatcherTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		$GLOBALS['alynt_ag_test_remote_posts'] = array();
		$GLOBALS['alynt_ag_test_db_inserts'] = array();
		unset( $GLOBALS['alynt_ag_test_remote_post_response'] );
	}

	public function test_account_created_payload_contains_full_user_fields() {
		$dispatcher = new ALYNT_AG_Webhook_Dispatcher();
		$user       = new WP_User( 'customer@example.test' );
		$user->ID   = 321;

		$payload = $dispatcher->build_account_created_payload( $user );

		$this->assertSame( 'account.created', $payload['event'] );
		$this->assertSame( 321, $payload['user']['id'] );
		$this->assertSame( 'customer@example.test', $payload['user']['user_email'] );
		$this->assertSame( 'Damon', $payload['user']['first_name'] );
		$this->assertSame( 'Paulo', $payload['user']['last_name'] );
		$this->assertSame( 'Damon Paulo', $payload['user']['display_name'] );
		$this->assertSame( array( 'customer' ), $payload['user']['roles'] );
		$this->assertSame( 'Example Store', $payload['site']['name'] );
	}

	public function test_log_dispatch_stores_metadata_without_payload_by_default() {
		$dispatcher = new ALYNT_AG_Webhook_Dispatcher();
		$result     = $dispatcher->log_dispatch(
			'account.created',
			'https://hooks.example.test/account-created',
			321,
			array( 'sample' => 'payload' ),
			array( 'response' => array( 'code' => 204, 'message' => 'No Content' ) ),
			array( 'debug_payload_logging' => false )
		);

		$this->assertTrue( $result );
		$this->assertCount( 1, $GLOBALS['alynt_ag_test_db_inserts'] );
		$row = $GLOBALS['alynt_ag_test_db_inserts'][0]['data'];
		$this->assertSame( 'account.created', $row['event_name'] );
		$this->assertSame( 'hooks.example.test', $row['destination_host'] );
		$this->assertSame( 204, $row['http_status'] );
		$this->assertSame( 1, $row['success'] );
		$this->assertNull( $row['payload'] );
	}

	public function test_log_dispatch_stores_payload_when_debug_enabled() {
		$dispatcher = new ALYNT_AG_Webhook_Dispatcher();
		$dispatcher->log_dispatch(
			'account.created',
			'https://hooks.example.test/account-created',
			321,
			array( 'sample' => 'payload' ),
			array( 'response' => array( 'code' => 500, 'message' => 'Server Error' ) ),
			array( 'debug_payload_logging' => true )
		);

		$row = $GLOBALS['alynt_ag_test_db_inserts'][0]['data'];
		$this->assertSame( 0, $row['success'] );
		$this->assertSame( 'Server Error', $row['error_message'] );
		$this->assertStringContainsString( '"sample":"payload"', $row['payload'] );
	}

	public function test_dispatch_account_created_posts_json_and_logs_response() {
		$dispatcher = new ALYNT_AG_Webhook_Dispatcher();
		$result     = $dispatcher->dispatch_account_created(
			321,
			array(
				'account_created_webhook' => 'https://hooks.example.test/account-created',
				'debug_payload_logging'   => false,
			)
		);

		$this->assertTrue( $result );
		$this->assertCount( 1, $GLOBALS['alynt_ag_test_remote_posts'] );
		$this->assertSame( 'https://hooks.example.test/account-created', $GLOBALS['alynt_ag_test_remote_posts'][0]['url'] );
		$this->assertSame( 'application/json', $GLOBALS['alynt_ag_test_remote_posts'][0]['args']['headers']['Content-Type'] );
		$this->assertStringContainsString( '"event":"account.created"', $GLOBALS['alynt_ag_test_remote_posts'][0]['args']['body'] );
		$this->assertCount( 1, $GLOBALS['alynt_ag_test_db_inserts'] );
	}
}
