<?php
/**
 * Provider connection check tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests privacy-safe Turnstile and Reoon connection checks.
 */
class ProviderConnectionCheckTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		$GLOBALS['alynt_ag_test_remote_posts'] = array();
		$GLOBALS['alynt_ag_test_remote_gets']  = array();
		unset(
			$GLOBALS['alynt_ag_test_remote_post_response'],
			$GLOBALS['alynt_ag_test_remote_get_response']
		);
	}

	protected function tearDown(): void {
		$GLOBALS['alynt_ag_test_remote_posts'] = array();
		$GLOBALS['alynt_ag_test_remote_gets']  = array();
		unset(
			$GLOBALS['alynt_ag_test_remote_post_response'],
			$GLOBALS['alynt_ag_test_remote_get_response']
		);

		parent::tearDown();
	}

	public function test_turnstile_check_accepts_expected_invalid_token_response() {
		$GLOBALS['alynt_ag_test_remote_post_response'] = array(
			'body' => '{"success":false,"error-codes":["invalid-input-response"]}',
		);

		$result = ( new ALYNT_AG_Turnstile_Client() )->check_configuration( 'saved-secret' );

		$this->assertTrue( $result );
		$this->assertSame( ALYNT_AG_Turnstile_Client::VERIFY_URL, $GLOBALS['alynt_ag_test_remote_posts'][0]['url'] );
		$this->assertSame( 'saved-secret', $GLOBALS['alynt_ag_test_remote_posts'][0]['args']['body']['secret'] );
		$this->assertSame( 'alynt-ag-configuration-check', $GLOBALS['alynt_ag_test_remote_posts'][0]['args']['body']['response'] );
	}

	public function test_turnstile_check_rejects_invalid_secret() {
		$GLOBALS['alynt_ag_test_remote_post_response'] = array(
			'body' => '{"success":false,"error-codes":["invalid-input-secret"]}',
		);

		$result = ( new ALYNT_AG_Turnstile_Client() )->check_configuration( 'rejected-secret' );

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'alynt_ag_turnstile_invalid_secret', $result->get_error_code() );
	}

	public function test_turnstile_check_handles_request_and_response_failures() {
		$GLOBALS['alynt_ag_test_remote_post_response'] = new WP_Error( 'http_request_failed', 'Timed out.' );
		$request_result = ( new ALYNT_AG_Turnstile_Client() )->check_configuration( 'saved-secret' );

		$this->assertSame( 'alynt_ag_turnstile_request_failed', $request_result->get_error_code() );

		$GLOBALS['alynt_ag_test_remote_post_response'] = array( 'body' => '{"success":true}' );
		$response_result = ( new ALYNT_AG_Turnstile_Client() )->check_configuration( 'saved-secret' );

		$this->assertSame( 'alynt_ag_turnstile_invalid_response', $response_result->get_error_code() );
	}

	public function test_reoon_check_returns_only_sanitized_account_status() {
		$GLOBALS['alynt_ag_test_remote_get_response'] = array(
			'body' => wp_json_encode(
				array(
					'status'                    => 'success',
					'api_status'                => 'active',
					'remaining_daily_credits'   => '120',
					'remaining_instant_credits' => '45',
					'account_email'             => 'provider-owner@example.test',
				)
			),
		);

		$result = ( new ALYNT_AG_Reoon_Client() )->check_account( 'saved key' );

		$this->assertSame(
			array(
				'status'                    => 'success',
				'api_status'                => 'active',
				'remaining_daily_credits'   => 120,
				'remaining_instant_credits' => 45,
			),
			$result
		);
		$this->assertStringStartsWith( ALYNT_AG_Reoon_Client::BALANCE_URL, $GLOBALS['alynt_ag_test_remote_gets'][0]['url'] );
		$this->assertStringContainsString( 'key=saved+key', $GLOBALS['alynt_ag_test_remote_gets'][0]['url'] );
		$this->assertStringNotContainsString( 'email=', $GLOBALS['alynt_ag_test_remote_gets'][0]['url'] );
		$this->assertArrayNotHasKey( 'account_email', $result );
	}

	public function test_reoon_check_rejects_inactive_account() {
		$GLOBALS['alynt_ag_test_remote_get_response'] = array(
			'body' => '{"status":"success","api_status":"inactive"}',
		);

		$result = ( new ALYNT_AG_Reoon_Client() )->check_account( 'saved-key' );

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'alynt_ag_reoon_account_inactive', $result->get_error_code() );
	}

	public function test_reoon_check_handles_request_and_response_failures() {
		$GLOBALS['alynt_ag_test_remote_get_response'] = new WP_Error( 'http_request_failed', 'Timed out.' );
		$request_result = ( new ALYNT_AG_Reoon_Client() )->check_account( 'saved-key' );

		$this->assertSame( 'alynt_ag_reoon_request_failed', $request_result->get_error_code() );

		$GLOBALS['alynt_ag_test_remote_get_response'] = array( 'body' => 'not-json' );
		$response_result = ( new ALYNT_AG_Reoon_Client() )->check_account( 'saved-key' );

		$this->assertSame( 'alynt_ag_reoon_invalid_response', $response_result->get_error_code() );
	}
}
