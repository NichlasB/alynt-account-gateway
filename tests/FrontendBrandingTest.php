<?php
/**
 * Frontend branding service tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests frontend branding rendering helpers.
 */
class FrontendBrandingTest extends TestCase {

	/**
	 * Test settings.
	 *
	 * @var array<string,mixed>
	 */
	private $settings;

	protected function setUp(): void {
		parent::setUp();

		$this->settings = array(
			'text_color'              => '#1A1A1A',
			'primary_color'           => '#335C4A',
			'page_background_color'   => '#F6F2EA',
			'accent_color'            => '#FFF9D8',
			'error_color'             => '#B42318',
			'surface_color'           => '#FFFFFF',
			'button_background_color' => '#335C4A',
			'button_text_color'       => '#FFFFFF',
			'heading_font_family'     => 'Georgia, serif',
			'body_font_family'        => 'Arial, sans-serif',
			'background_image_id'     => 0,
			'brand_logo_id'           => 0,
			'brand_logo_max_width'    => 180,
		);

		$GLOBALS['alynt_ag_test_attachment_urls'] = array();
	}

	public function test_style_attribute_includes_configured_design_tokens_and_skips_empty_values() {
		$branding = new ALYNT_AG_Frontend_Branding();
		$this->settings['accent_color'] = '';

		$style = $branding->style_attribute( $this->settings );

		$this->assertStringContainsString( '--agw-color-text:#1A1A1A;', $style );
		$this->assertStringContainsString( '--agw-color-primary:#335C4A;', $style );
		$this->assertStringContainsString( '--agw-button-background:#335C4A;', $style );
		$this->assertStringContainsString( '--agw-font-heading:Georgia, serif;', $style );
		$this->assertStringNotContainsString( '--agw-color-notice:', $style );
	}

	public function test_render_media_panel_outputs_pattern_when_no_background_image_exists() {
		$branding = new ALYNT_AG_Frontend_Branding();

		ob_start();
		$branding->render_media_panel( $this->settings );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'class="agw-media__pattern"', $html );
		$this->assertStringContainsString( 'agw-leaf agw-leaf--one', $html );
		$this->assertStringNotContainsString( 'agw-media__image', $html );
	}

	public function test_render_media_panel_outputs_background_image_when_configured() {
		$branding = new ALYNT_AG_Frontend_Branding();
		$this->settings['background_image_id'] = 42;
		$GLOBALS['alynt_ag_test_attachment_urls']['42:full'] = 'https://example.test/background.jpg';

		ob_start();
		$branding->render_media_panel( $this->settings );
		$html = ob_get_clean();

		$this->assertSame(
			'<div class="agw-media__image" style="background-image:url(https://example.test/background.jpg);"></div>',
			$html
		);
	}

	public function test_render_brand_block_outputs_store_name_without_logo() {
		$branding = new ALYNT_AG_Frontend_Branding();

		ob_start();
		$branding->render_brand_block( $this->settings );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'class="agw-brand__name">Example Store</div>', $html );
		$this->assertStringNotContainsString( 'agw-brand__logo', $html );
	}

	public function test_render_brand_block_outputs_logo_with_clamped_width() {
		$branding = new ALYNT_AG_Frontend_Branding();
		$this->settings['brand_logo_id'] = 15;
		$this->settings['brand_logo_max_width'] = 999;
		$GLOBALS['alynt_ag_test_attachment_urls']['15:full'] = 'https://example.test/logo.png';

		ob_start();
		$branding->render_brand_block( $this->settings );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'class="agw-brand__logo"', $html );
		$this->assertStringContainsString( 'src="https://example.test/logo.png"', $html );
		$this->assertStringContainsString( 'alt="Example Store"', $html );
		$this->assertStringContainsString( 'style="max-width:520px;"', $html );
	}
}
