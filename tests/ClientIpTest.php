<?php
/**
 * Client IP resolver tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests trusted client IP resolution.
 */
class ClientIpTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		$GLOBALS['alynt_ag_test_filters']      = array();
		$GLOBALS['alynt_ag_test_remote_posts'] = array();
		unset( $GLOBALS['alynt_ag_test_remote_post_response'] );

		$_SERVER['REMOTE_ADDR'] = '203.0.113.10';
		unset( $_SERVER['HTTP_CF_CONNECTING_IP'], $_SERVER['HTTP_X_FORWARDED_FOR'] );
	}

	protected function tearDown(): void {
		$GLOBALS['alynt_ag_test_filters'] = array();
		unset(
			$_SERVER['HTTP_CF_CONNECTING_IP'],
			$_SERVER['HTTP_X_FORWARDED_FOR'],
			$_SERVER['HTTP_X_UNTRUSTED_CLIENT_IP']
		);

		parent::tearDown();
	}

	public function test_resolver_ignores_forwarded_headers_by_default() {
		$_SERVER['HTTP_CF_CONNECTING_IP'] = '198.51.100.21';
		$_SERVER['HTTP_X_FORWARDED_FOR'] = '198.51.100.22';

		$this->assertSame( '203.0.113.10', ALYNT_AG_Client_IP::resolve() );
	}

	public function test_resolver_accepts_valid_forwarded_ip_from_explicitly_trusted_proxy() {
		$_SERVER['HTTP_CF_CONNECTING_IP'] = '198.51.100.21';

		add_filter(
			'alynt_ag_is_trusted_proxy',
			static function ( $is_trusted, $remote_addr ) {
				return '203.0.113.10' === $remote_addr;
			},
			10,
			2
		);

		$this->assertSame( '198.51.100.21', ALYNT_AG_Client_IP::resolve() );
	}

	public function test_resolver_falls_back_when_forwarded_ip_is_invalid() {
		$_SERVER['HTTP_CF_CONNECTING_IP'] = 'not-an-ip';
		$_SERVER['HTTP_X_FORWARDED_FOR'] = 'also-invalid';

		add_filter(
			'alynt_ag_is_trusted_proxy',
			static function () {
				return true;
			}
		);

		$this->assertSame( '203.0.113.10', ALYNT_AG_Client_IP::resolve() );
	}

	public function test_resolver_ignores_unsupported_proxy_header_names() {
		$_SERVER['HTTP_X_UNTRUSTED_CLIENT_IP'] = '198.51.100.44';

		add_filter(
			'alynt_ag_is_trusted_proxy',
			static function () {
				return true;
			}
		);
		add_filter(
			'alynt_ag_trusted_proxy_headers',
			static function () {
				return array( 'HTTP_X_UNTRUSTED_CLIENT_IP' );
			}
		);

		$this->assertSame( '203.0.113.10', ALYNT_AG_Client_IP::resolve() );
	}

	public function test_resolver_returns_empty_string_for_invalid_immediate_peer() {
		$_SERVER['REMOTE_ADDR'] = 'not-an-ip';

		add_filter(
			'alynt_ag_is_trusted_proxy',
			static function () {
				return true;
			}
		);

		$this->assertSame( '', ALYNT_AG_Client_IP::resolve() );
	}

	public function test_turnstile_request_uses_hardened_client_ip() {
		$_SERVER['HTTP_CF_CONNECTING_IP'] = '198.51.100.21';
		$_SERVER['HTTP_X_FORWARDED_FOR'] = '198.51.100.22';

		$client = new ALYNT_AG_Turnstile_Client();

		$this->assertTrue( $client->verify( 'test-token', 'test-secret' ) );
		$this->assertSame(
			'203.0.113.10',
			$GLOBALS['alynt_ag_test_remote_posts'][0]['args']['body']['remoteip']
		);
	}
}
