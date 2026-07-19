<?php
/**
 * Return destination tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests same-site return destination handling.
 */
class ReturnDestinationTest extends TestCase {

	/**
	 * Settings.
	 *
	 * @var array<string,mixed>
	 */
	private $settings;

	protected function setUp(): void {
		parent::setUp();

		$this->settings = array(
			'login_path'          => '/login',
			'account_action_base' => '/account',
		);
	}

	public function test_accepts_same_site_absolute_url_and_keeps_query() {
		$service = new ALYNT_AG_Return_Destination();

		$this->assertSame(
			'/checkout/?coupon=welcome',
			$service->relative_path( 'https://example.test/checkout/?coupon=welcome#summary', $this->settings )
		);
	}

	public function test_accepts_relative_path_and_builds_absolute_url() {
		$service = new ALYNT_AG_Return_Destination();

		$this->assertSame(
			'https://example.test/my-account/orders/',
			$service->absolute_url( '/my-account/orders/', $this->settings )
		);
	}

	public function test_rejects_external_and_protocol_relative_urls() {
		$service = new ALYNT_AG_Return_Destination();

		$this->assertSame( '', $service->relative_path( 'https://evil.example/checkout/', $this->settings ) );
		$this->assertSame( '', $service->relative_path( '//evil.example/checkout/', $this->settings ) );
		$this->assertSame( '', $service->relative_path( '//example.test/checkout/', $this->settings ) );
		$this->assertSame( '', $service->relative_path( 'ftp://example.test/checkout/', $this->settings ) );
		$this->assertSame( '', $service->relative_path( 'https://user:pass@example.test/checkout/', $this->settings ) );
	}

	public function test_rejects_authentication_surfaces() {
		$service = new ALYNT_AG_Return_Destination();

		$this->assertSame( '', $service->relative_path( 'https://example.test/login?again=1', $this->settings ) );
		$this->assertSame( '', $service->relative_path( '/account?action=register', $this->settings ) );
		$this->assertSame( '', $service->relative_path( '/wp-login.php', $this->settings ) );
	}
}
