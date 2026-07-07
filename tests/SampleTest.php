<?php
/**
 * Sample tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Basic scaffold test.
 */
class SampleTest extends TestCase {

	public function test_version_constant_is_declared_in_main_plugin_file() {
		$main_file = file_get_contents( dirname( __DIR__ ) . '/alynt-account-gateway.php' );

		$this->assertStringContainsString( "define( 'ALYNT_AG_VERSION', '0.1.84' );", $main_file );
		$this->assertStringContainsString( 'GitHub Plugin URI: NichlasB/alynt-account-gateway', $main_file );
	}
}
