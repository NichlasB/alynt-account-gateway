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
		$this->assertSame( 'account.created', $GLOBALS['alynt_ag_test_remote_posts'][0]['args']['headers']['X-Alynt-AG-Event'] );
		$this->assertArrayNotHasKey( 'X-Alynt-AG-Signature', $GLOBALS['alynt_ag_test_remote_posts'][0]['args']['headers'] );
		$this->assertStringContainsString( '"event":"account.created"', $GLOBALS['alynt_ag_test_remote_posts'][0]['args']['body'] );
		$this->assertCount( 1, $GLOBALS['alynt_ag_test_db_inserts'] );
	}

	public function test_dispatch_account_created_returns_error_and_logs_non_2xx_response() {
		$GLOBALS['alynt_ag_test_remote_post_response'] = array(
			'body'     => '{"error":"temporarily unavailable"}',
			'response' => array(
				'code'    => 503,
				'message' => 'Service Unavailable',
			),
		);

		$dispatcher = new ALYNT_AG_Webhook_Dispatcher();
		$result     = $dispatcher->dispatch_account_created(
			321,
			array(
				'account_created_webhook' => 'https://hooks.example.test/account-created',
				'debug_payload_logging'   => false,
			)
		);

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'alynt_ag_webhook_http_error', $result->get_error_code() );
		$this->assertCount( 1, $GLOBALS['alynt_ag_test_db_inserts'] );

		$row = $GLOBALS['alynt_ag_test_db_inserts'][0]['data'];
		$this->assertSame( 503, $row['http_status'] );
		$this->assertSame( 0, $row['success'] );
		$this->assertSame( 'Service Unavailable', $row['error_message'] );
		$this->assertNull( $row['payload'] );
	}

	public function test_dispatch_account_created_returns_and_logs_transport_error() {
		$GLOBALS['alynt_ag_test_remote_post_response'] = new WP_Error( 'http_request_failed', 'Connection timed out.' );

		$dispatcher = new ALYNT_AG_Webhook_Dispatcher();
		$result     = $dispatcher->dispatch_account_created(
			321,
			array(
				'account_created_webhook' => 'https://hooks.example.test/account-created',
				'debug_payload_logging'   => false,
			)
		);

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'http_request_failed', $result->get_error_code() );
		$this->assertCount( 1, $GLOBALS['alynt_ag_test_db_inserts'] );

		$row = $GLOBALS['alynt_ag_test_db_inserts'][0]['data'];
		$this->assertSame( 0, $row['http_status'] );
		$this->assertSame( 0, $row['success'] );
		$this->assertSame( 'Connection timed out.', $row['error_message'] );
		$this->assertNull( $row['payload'] );
	}

	public function test_dispatch_account_created_signs_json_body_when_secret_is_configured() {
		$dispatcher = new ALYNT_AG_Webhook_Dispatcher();
		$result     = $dispatcher->dispatch_account_created(
			321,
			array(
				'account_created_webhook' => 'https://hooks.example.test/account-created',
				'webhook_signing_secret'  => 'shared-secret',
				'debug_payload_logging'   => false,
			)
		);

		$this->assertTrue( $result );

		$post      = $GLOBALS['alynt_ag_test_remote_posts'][0];
		$headers   = $post['args']['headers'];
		$timestamp = $headers['X-Alynt-AG-Time'];
		$expected  = hash_hmac( 'sha256', $timestamp . '.account.created.' . $post['args']['body'], 'shared-secret' );

		$this->assertSame( 'account.created', $headers['X-Alynt-AG-Event'] );
		$this->assertSame( '1', $headers['X-Alynt-AG-Version'] );
		$this->assertSame( 'sha256=' . $expected, $headers['X-Alynt-AG-Signature'] );
	}

	public function test_dispatch_account_created_test_posts_test_event_and_logs_response() {
		$dispatcher = new ALYNT_AG_Webhook_Dispatcher();
		$result     = $dispatcher->dispatch_account_created_test(
			321,
			array(
				'account_created_webhook' => 'https://hooks.example.test/account-created',
				'debug_payload_logging'   => false,
			)
		);

		$this->assertTrue( $result );
		$this->assertCount( 1, $GLOBALS['alynt_ag_test_remote_posts'] );
		$this->assertStringContainsString( '"event":"account.created.test"', $GLOBALS['alynt_ag_test_remote_posts'][0]['args']['body'] );
		$this->assertStringContainsString( '"test":true', $GLOBALS['alynt_ag_test_remote_posts'][0]['args']['body'] );
		$this->assertCount( 1, $GLOBALS['alynt_ag_test_db_inserts'] );
		$this->assertSame( 'account.created.test', $GLOBALS['alynt_ag_test_db_inserts'][0]['data']['event_name'] );
	}

	public function test_dispatch_account_created_rejects_public_http_webhook_url() {
		$dispatcher = new ALYNT_AG_Webhook_Dispatcher();
		$result     = $dispatcher->dispatch_account_created(
			321,
			array(
				'account_created_webhook' => 'http://hooks.example.test/account-created',
				'debug_payload_logging'   => false,
			)
		);

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'alynt_ag_webhook_insecure_url', $result->get_error_code() );
		$this->assertCount( 0, $GLOBALS['alynt_ag_test_remote_posts'] );
	}

	public function test_local_http_webhook_urls_are_allowed_for_development() {
		$dispatcher = new ALYNT_AG_Webhook_Dispatcher();

		$this->assertTrue( $dispatcher->is_allowed_delivery_url( 'http://localhost:8080/account-created' ) );
		$this->assertTrue( $dispatcher->is_allowed_delivery_url( 'http://127.0.0.1:8080/account-created' ) );
		$this->assertTrue( $dispatcher->is_allowed_delivery_url( 'http://plugin-tester.local/account-created' ) );
		$this->assertTrue( $dispatcher->is_allowed_delivery_url( 'https://hooks.example.test/account-created' ) );
		$this->assertFalse( $dispatcher->is_allowed_delivery_url( 'http://hooks.example.test/account-created' ) );
	}
}
