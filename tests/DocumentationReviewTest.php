<?php
/**
 * Documentation review regression tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Locks the administrator and integration references to current contracts.
 */
class DocumentationReviewTest extends TestCase {

	/**
	 * Every persisted schema field must have a settings-reference row.
	 */
	public function test_settings_reference_covers_every_schema_key() {
		$reference = file_get_contents( ALYNT_AG_PLUGIN_DIR . 'docs/SETTINGS.md' );

		$this->assertIsString( $reference );
		$this->assertStringContainsString( '## Sanitization Legend', $reference );

		foreach ( array_keys( ALYNT_AG_Settings_Schema::schema() ) as $key ) {
			$this->assertStringContainsString(
				'| `' . $key . '` |',
				$reference,
				'Every persisted settings key needs a settings-reference row.'
			);
		}
	}

	/**
	 * Public extension and scheduler contracts must remain described accurately.
	 */
	public function test_hooks_reference_distinguishes_extension_points_from_consumed_hooks() {
		$reference = file_get_contents( ALYNT_AG_PLUGIN_DIR . 'docs/HOOKS.md' );

		$this->assertIsString( $reference );
		$this->assertStringContainsString( 'alynt_ag_is_trusted_proxy', $reference );
		$this->assertStringContainsString( 'alynt_ag_trusted_proxy_headers', $reference );
		$this->assertStringContainsString( 'alynt_ag_retention_cleanup', $reference );
		$this->assertStringContainsString( 'alynt_ag_deliver_account_created_webhook', $reference );
		$this->assertStringContainsString( 'woocommerce_account_{endpoint}_endpoint', $reference );
		$this->assertStringContainsString( 'not an Alynt-owned hook', $reference );
	}

	/**
	 * Release-facing readmes and changelog must retain core owner guidance.
	 */
	public function test_release_documentation_has_current_owner_guidance() {
		$readme     = file_get_contents( ALYNT_AG_PLUGIN_DIR . 'README.md' );
		$wp_readme  = file_get_contents( ALYNT_AG_PLUGIN_DIR . 'readme.txt' );
		$changelog  = file_get_contents( ALYNT_AG_PLUGIN_DIR . 'CHANGELOG.md' );
		$plugin_php = file_get_contents( ALYNT_AG_PLUGIN_DIR . 'alynt-account-gateway.php' );

		$this->assertIsString( $readme );
		$this->assertIsString( $wp_readme );
		$this->assertIsString( $changelog );
		$this->assertIsString( $plugin_php );
		$this->assertStringContainsString( '## FAQ', $readme );
		$this->assertStringContainsString( 'Gateway Screen Preview', $readme );
		$this->assertStringContainsString( '== FAQ ==', $wp_readme );
		$this->assertStringContainsString( 'Stable tag: 1.1.25', $wp_readme );
		$this->assertStringContainsString( '## Unreleased', $changelog );
		$this->assertStringContainsString( 'Version:           1.1.25', $plugin_php );
		$this->assertStringContainsString( 'Text Domain:       alynt-account-gateway', $plugin_php );
	}
}
