<?php
/**
 * Activation edge-case tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Locks lifecycle failure behavior without executing WordPress activation.
 */
class ActivationEdgeCaseTest extends TestCase {

	public function test_activation_rejects_network_wide_install_and_checks_database_result() {
		$source = file_get_contents( ALYNT_AG_PLUGIN_DIR . 'includes/class-activator.php' );

		$this->assertIsString( $source );
		$this->assertStringContainsString( 'public static function activate( $network_wide = false )', $source );
		$this->assertStringContainsString( 'if ( $network_wide )', $source );
		$this->assertStringContainsString( 'if ( ! ALYNT_AG_Database::install() )', $source );
		$this->assertStringContainsString( "delete_option( 'alynt_ag_settings' )", $source );
		$this->assertSame( 2, substr_count( $source, 'wp_die(' ) );
	}

	public function test_public_registration_serializes_pending_record_creation() {
		$source = file_get_contents( ALYNT_AG_PLUGIN_DIR . 'includes/services/class-registration-request-handler.php' );

		$this->assertIsString( $source );
		$this->assertStringContainsString( "ALYNT_AG_Operation_Lock::acquire( 'pending_registration'", $source );
		$this->assertStringContainsString( "ALYNT_AG_Operation_Lock::release( 'pending_registration'", $source );
	}
}
