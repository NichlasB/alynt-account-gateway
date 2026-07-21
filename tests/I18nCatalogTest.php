<?php
/**
 * Internationalization catalog tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests translation metadata and catalog-generation support.
 */
class I18nCatalogTest extends TestCase {

	public function test_plugin_declares_and_loads_the_expected_text_domain() {
		$plugin = file_get_contents( ALYNT_AG_PLUGIN_DIR . 'alynt-account-gateway.php' );
		$i18n   = file_get_contents( ALYNT_AG_PLUGIN_DIR . 'includes/class-i18n.php' );

		$this->assertIsString( $plugin );
		$this->assertIsString( $i18n );
		$this->assertStringContainsString( 'Text Domain:       alynt-account-gateway', $plugin );
		$this->assertStringContainsString( 'Domain Path:       /languages', $plugin );
		$this->assertStringContainsString( "define( 'ALYNT_AG_TEXT_DOMAIN', 'alynt-account-gateway' );", $plugin );
		$this->assertStringContainsString( "add_action( 'plugins_loaded', array( \$this, 'load_textdomain' ) );", $i18n );
		$this->assertStringContainsString( "dirname( ALYNT_AG_PLUGIN_BASENAME ) . '/languages/'", $i18n );
	}

	public function test_pot_generator_supports_plural_context_and_translator_metadata() {
		$generator = file_get_contents( ALYNT_AG_PLUGIN_DIR . 'scripts/make-pot.mjs' );

		$this->assertIsString( $generator );
		$this->assertStringContainsString( 'function translatorComment', $generator );
		$this->assertStringContainsString( 'const contextual = new RegExp', $generator );
		$this->assertStringContainsString( 'const plural = new RegExp', $generator );
		$this->assertStringContainsString( 'const contextualPlural = new RegExp', $generator );
		$this->assertStringContainsString( "formatPotString(entry.plural, 'msgid_plural')", $generator );
		$this->assertStringContainsString( "formatPotString(entry.context, 'msgctxt')", $generator );
	}

	public function test_generated_catalog_contains_plural_and_translator_entries() {
		$catalog = file_get_contents( ALYNT_AG_PLUGIN_DIR . 'languages/alynt-account-gateway.pot' );

		$this->assertIsString( $catalog );
		$this->assertStringContainsString( '#. translators: %d: configured attempt count.', $catalog );
		$this->assertStringContainsString( 'msgid "%d attempt"', $catalog );
		$this->assertStringContainsString( 'msgid_plural "%d attempts"', $catalog );
		$this->assertStringContainsString( 'msgstr[0] ""', $catalog );
		$this->assertStringContainsString( 'msgstr[1] ""', $catalog );
	}
}
