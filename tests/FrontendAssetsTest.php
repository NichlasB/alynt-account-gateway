<?php
/**
 * Frontend asset service tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests frontend asset enqueue decisions.
 */
class FrontendAssetsTest extends TestCase {

	/**
	 * Test settings.
	 *
	 * @var array<string,mixed>
	 */
	private $settings;

	protected function setUp(): void {
		parent::setUp();

		$this->settings = array(
			'frontend_enabled'  => true,
			'turnstile_site_key' => '',
		);

		$GLOBALS['alynt_ag_test_enqueued_styles']    = array();
		$GLOBALS['alynt_ag_test_enqueued_scripts']   = array();
		$GLOBALS['alynt_ag_test_localized_scripts']  = array();
	}

	public function test_enqueue_skips_when_frontend_disabled_or_screen_missing() {
		$assets = new ALYNT_AG_Frontend_Assets();

		$this->settings['frontend_enabled'] = false;
		$assets->enqueue( $this->settings, 'login' );
		$this->assertSame( array(), $GLOBALS['alynt_ag_test_enqueued_styles'] );
		$this->assertSame( array(), $GLOBALS['alynt_ag_test_enqueued_scripts'] );

		$this->settings['frontend_enabled'] = true;
		$assets->enqueue( $this->settings, '' );
		$this->assertSame( array(), $GLOBALS['alynt_ag_test_enqueued_styles'] );
		$this->assertSame( array(), $GLOBALS['alynt_ag_test_enqueued_scripts'] );
	}

	public function test_enqueue_loads_frontend_style_script_and_labels() {
		$assets = new ALYNT_AG_Frontend_Assets();

		$assets->enqueue( $this->settings, 'login' );

		$this->assertCount( 1, $GLOBALS['alynt_ag_test_enqueued_styles'] );
		$this->assertSame( 'alynt-ag-frontend', $GLOBALS['alynt_ag_test_enqueued_styles'][0]['handle'] );
		$this->assertSame( ALYNT_AG_PLUGIN_URL . 'assets/dist/frontend/index.css', $GLOBALS['alynt_ag_test_enqueued_styles'][0]['src'] );

		$this->assertCount( 1, $GLOBALS['alynt_ag_test_enqueued_scripts'] );
		$this->assertSame( 'alynt-ag-frontend', $GLOBALS['alynt_ag_test_enqueued_scripts'][0]['handle'] );
		$this->assertSame( ALYNT_AG_PLUGIN_URL . 'assets/dist/frontend/index.js', $GLOBALS['alynt_ag_test_enqueued_scripts'][0]['src'] );
		$this->assertTrue( $GLOBALS['alynt_ag_test_enqueued_scripts'][0]['in_footer'] );

		$this->assertCount( 1, $GLOBALS['alynt_ag_test_localized_scripts'] );
		$this->assertSame( 'alyntAgFrontend', $GLOBALS['alynt_ag_test_localized_scripts'][0]['object_name'] );
		$this->assertSame( 'Show password', $GLOBALS['alynt_ag_test_localized_scripts'][0]['l10n']['labels']['showPassword'] );
		$this->assertSame( 'Password is visible.', $GLOBALS['alynt_ag_test_localized_scripts'][0]['l10n']['labels']['passwordVisible'] );
		$this->assertSame( 'Password is hidden.', $GLOBALS['alynt_ag_test_localized_scripts'][0]['l10n']['labels']['passwordHidden'] );
		$this->assertSame( 'Met', $GLOBALS['alynt_ag_test_localized_scripts'][0]['l10n']['labels']['requirementMet'] );
		$this->assertSame( 'Not met', $GLOBALS['alynt_ag_test_localized_scripts'][0]['l10n']['labels']['requirementNotMet'] );
		$this->assertSame( '%1$d of %2$d requirements met.', $GLOBALS['alynt_ag_test_localized_scripts'][0]['l10n']['labels']['requirementsMet'] );
	}

	public function test_preview_enqueue_loads_assets_while_frontend_output_is_disabled() {
		$assets = new ALYNT_AG_Frontend_Assets();

		$this->settings['frontend_enabled'] = false;
		$assets->enqueue_preview( $this->settings, 'login' );

		$this->assertSame(
			array( 'alynt-ag-frontend' ),
			array_column( $GLOBALS['alynt_ag_test_enqueued_styles'], 'handle' )
		);
		$this->assertSame(
			array( 'alynt-ag-frontend' ),
			array_column( $GLOBALS['alynt_ag_test_enqueued_scripts'], 'handle' )
		);

		$GLOBALS['alynt_ag_test_enqueued_styles']    = array();
		$GLOBALS['alynt_ag_test_enqueued_scripts']   = array();
		$GLOBALS['alynt_ag_test_localized_scripts']  = array();

		$assets->enqueue_preview( $this->settings, '' );

		$this->assertSame( array(), $GLOBALS['alynt_ag_test_enqueued_styles'] );
		$this->assertSame( array(), $GLOBALS['alynt_ag_test_enqueued_scripts'] );
	}

	public function test_enqueue_turnstile_only_on_registration_screen_when_configured() {
		$assets = new ALYNT_AG_Frontend_Assets();
		$this->settings['turnstile_site_key'] = 'site-key';

		$assets->enqueue( $this->settings, 'login' );
		$this->assertSame(
			array( 'alynt-ag-frontend' ),
			array_column( $GLOBALS['alynt_ag_test_enqueued_scripts'], 'handle' )
		);

		$GLOBALS['alynt_ag_test_enqueued_scripts']  = array();
		$GLOBALS['alynt_ag_test_localized_scripts'] = array();

		$assets->enqueue( $this->settings, 'register' );
		$this->assertSame(
			array( 'alynt-ag-frontend', 'alynt-ag-turnstile' ),
			array_column( $GLOBALS['alynt_ag_test_enqueued_scripts'], 'handle' )
		);
		$this->assertSame( 'https://challenges.cloudflare.com/turnstile/v0/api.js', $GLOBALS['alynt_ag_test_enqueued_scripts'][1]['src'] );
		$this->assertNull( $GLOBALS['alynt_ag_test_enqueued_scripts'][1]['ver'] );
		$this->assertTrue( $GLOBALS['alynt_ag_test_enqueued_scripts'][1]['in_footer'] );
	}
}
