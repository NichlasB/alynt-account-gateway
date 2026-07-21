<?php
/**
 * Performance optimization regression tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Locks request-bounding and lazy-loading behavior.
 */
class PerformanceOptimizationTest extends TestCase {

	public function test_woocommerce_integration_defers_focused_collaborators() {
		$integration = new ALYNT_AG_WooCommerce_Integration();
		$reflection  = new ReflectionClass( $integration );
		$property    = $reflection->getProperty( 'collaborators' );
		if ( PHP_VERSION_ID < 80100 ) {
			$property->setAccessible( true );
		}

		$this->assertSame(
			array(
				'navigation' => null,
				'routing'    => null,
				'renderer'   => null,
				'data'       => null,
			),
			$property->getValue( $integration )
		);
	}

	public function test_admin_work_has_explicit_memory_bounds() {
		$this->assertSame( 1048576, ALYNT_AG_Settings_Page_Settings_Transfer::MAX_IMPORT_BYTES );
		$this->assertSame( 1000, ALYNT_AG_Settings_Page_Security_Rate_Limits::MAX_ACTIVE_BUCKETS );

		$source = file_get_contents( ALYNT_AG_PLUGIN_DIR . 'admin/settings-page/class-security-rate-limits.php' );
		$this->assertStringContainsString( 'LIMIT %d', $source );
		$this->assertStringContainsString( 'self::MAX_ACTIVE_BUCKETS + 1', $source );
	}
}
