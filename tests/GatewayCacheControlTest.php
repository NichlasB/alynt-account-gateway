<?php
/**
 * Gateway cache-control tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests request-local cache bypass signals for branded gateway routes.
 */
class GatewayCacheControlTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();

		unset( $GLOBALS['alynt_ag_test_nocache_headers'] );
	}

	public function test_cache_bypass_constants_include_common_page_cache_markers() {
		$this->assertSame(
			array(
				'DONOTCACHEPAGE',
				'DONOTCACHEOBJECT',
				'DONOTCACHEDB',
			),
			ALYNT_AG_Gateway_Cache_Control::cache_bypass_constants()
		);
	}

	public function test_header_lines_include_gridpane_and_browser_cache_bypass_headers() {
		$headers = ALYNT_AG_Gateway_Cache_Control::header_lines();

		$this->assertContains( 'do-not-cache: true', $headers );
		$this->assertContains( 'Cache-Control: private, no-store, no-cache, must-revalidate, max-age=0', $headers );
		$this->assertContains( 'Pragma: no-cache', $headers );
		$this->assertContains( 'Expires: Wed, 11 Jan 1984 05:00:00 GMT', $headers );
	}

	public function test_prevent_caching_sets_wordpress_nocache_and_constants() {
		ALYNT_AG_Gateway_Cache_Control::prevent_caching();

		$this->assertTrue( $GLOBALS['alynt_ag_test_nocache_headers'] );
		$this->assertTrue( defined( 'DONOTCACHEPAGE' ) && DONOTCACHEPAGE );
		$this->assertTrue( defined( 'DONOTCACHEOBJECT' ) && DONOTCACHEOBJECT );
		$this->assertTrue( defined( 'DONOTCACHEDB' ) && DONOTCACHEDB );
	}
}
