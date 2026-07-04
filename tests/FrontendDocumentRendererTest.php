<?php
/**
 * Frontend document renderer tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

class ALYNT_AG_Test_Document_Renderer_Gateway_Shell extends ALYNT_AG_Frontend_Gateway_Shell {
	public function render_gateway_shell( $screen, $settings ) {
		echo '<main class="test-gateway-shell" data-screen="' . esc_attr( $screen ) . '"></main>';
	}

	public function render_gateway_shell_with_password_preview( $settings ) {
		echo '<main class="test-password-preview"></main>';
	}
}

class ALYNT_AG_Test_Document_Renderer_Dashboard_Screen extends ALYNT_AG_Frontend_Dashboard_Screen {
	public function render_dashboard_shell( $settings, $current_path = '' ) {
		echo '<main class="test-dashboard-shell" data-path="' . esc_attr( $current_path ) . '"></main>';
	}
}

class ALYNT_AG_Test_Document_Renderer_Messages extends ALYNT_AG_Frontend_Messages {
	public function screen_title( $screen ) {
		return 'Title: ' . $screen;
	}
}

/**
 * Tests the frontend document renderer.
 */
class FrontendDocumentRendererTest extends TestCase {

	/**
	 * Test settings.
	 *
	 * @var array<string,mixed>
	 */
	private $settings;

	protected function setUp(): void {
		parent::setUp();

		$this->settings = array(
			'account_action_base' => '/account',
		);

		unset( $GLOBALS['alynt_ag_test_status_header'], $GLOBALS['alynt_ag_test_nocache_headers'] );
	}

	public function test_render_gateway_document_outputs_full_document_and_auth_shell() {
		$renderer = $this->make_renderer();

		ob_start();
		$renderer->render_gateway_document( 'login', $this->settings, '/login' );
		$html = ob_get_clean();

		$this->assertSame( 200, $GLOBALS['alynt_ag_test_status_header'] );
		$this->assertTrue( $GLOBALS['alynt_ag_test_nocache_headers'] );
		$this->assertStringContainsString( '<!doctype html><html lang="en-US">', $html );
		$this->assertStringContainsString( '<meta charset="UTF-8">', $html );
		$this->assertStringContainsString( '<title>Title: login</title>', $html );
		$this->assertStringContainsString( '<body class="alynt-ag-body">', $html );
		$this->assertStringContainsString( '<!-- wp_head -->', $html );
		$this->assertStringContainsString( '<main class="test-gateway-shell" data-screen="login"></main>', $html );
		$this->assertStringContainsString( '<!-- wp_footer -->', $html );
	}

	public function test_render_gateway_document_outputs_dashboard_shell_with_current_path() {
		$renderer = $this->make_renderer();

		ob_start();
		$renderer->render_gateway_document( 'dashboard', $this->settings, '/my-account/orders/' );
		$html = ob_get_clean();

		$this->assertStringContainsString( '<title>Title: dashboard</title>', $html );
		$this->assertStringContainsString( '<main class="test-dashboard-shell" data-path="/my-account/orders/"></main>', $html );
		$this->assertStringNotContainsString( 'test-gateway-shell', $html );
	}

	public function test_render_preview_normalizes_unknown_screen_to_login() {
		$renderer = $this->make_renderer();

		ob_start();
		$renderer->render_preview( 'unknown-screen', $this->settings, '/preview' );
		$html = ob_get_clean();

		$this->assertSame( '<main class="test-gateway-shell" data-screen="login"></main>', $html );
	}

	public function test_render_preview_outputs_password_preview_shell() {
		$renderer = $this->make_renderer();

		ob_start();
		$renderer->render_preview( 'setpassword', $this->settings, '/preview' );
		$html = ob_get_clean();

		$this->assertSame( '<main class="test-password-preview"></main>', $html );
	}

	public function test_get_screen_title_delegates_to_messages() {
		$renderer = $this->make_renderer();

		$this->assertSame( 'Title: lostpassword', $renderer->get_screen_title( 'lostpassword' ) );
	}

	public function test_frontend_title_wrapper_remains_available_for_admin_preview() {
		$frontend = new ALYNT_AG_Frontend();

		$this->assertSame( 'Create Account', $frontend->get_screen_title( 'register' ) );
	}

	/**
	 * Build a document renderer with deterministic helper doubles.
	 *
	 * @return ALYNT_AG_Frontend_Document_Renderer
	 */
	private function make_renderer() {
		return new ALYNT_AG_Frontend_Document_Renderer(
			new ALYNT_AG_Test_Document_Renderer_Gateway_Shell(),
			new ALYNT_AG_Test_Document_Renderer_Dashboard_Screen(),
			new ALYNT_AG_Test_Document_Renderer_Messages()
		);
	}
}
