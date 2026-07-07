<?php
/**
 * Frontend gateway shell service tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

class ALYNT_AG_Test_Gateway_Shell_Branding extends ALYNT_AG_Frontend_Branding {
	public function style_attribute( $settings ) {
		return '--agw-color-primary:#123456;';
	}

	public function render_media_panel( $settings ) {
		echo '<div class="test-media"></div>';
	}

	public function render_brand_block( $settings ) {
		echo '<div class="test-brand">Brand</div>';
	}
}

class ALYNT_AG_Test_Gateway_Shell_Login_Screen extends ALYNT_AG_Frontend_Login_Screen {
	public function render_login_screen( $settings ) {
		echo '<div class="test-screen">login</div>';
	}
}

class ALYNT_AG_Test_Gateway_Shell_Register_Screen extends ALYNT_AG_Frontend_Register_Screen {
	public function render_register_screen( $settings ) {
		echo '<div class="test-screen">register</div>';
	}
}

class ALYNT_AG_Test_Gateway_Shell_Lostpassword_Screen extends ALYNT_AG_Frontend_Lostpassword_Screen {
	public function render_lostpassword_screen( $settings, $forced_error_code = '' ) {
		echo '<div class="test-screen">lostpassword</div>';
	}
}

class ALYNT_AG_Test_Gateway_Shell_Setpassword_Screen extends ALYNT_AG_Frontend_Setpassword_Screen {
	public function render_setpassword_screen( $settings ) {
		echo '<div class="test-screen">setpassword</div>';
	}

	public function render_password_form( $settings, $action_url, $action, $nonce_action, $nonce_name, $hidden_fields, $error_code ) {
		echo '<div class="test-password-preview" data-action="' . esc_attr( $action_url ) . '"></div>';
	}
}

class ALYNT_AG_Test_Gateway_Shell_Logout_Screen extends ALYNT_AG_Frontend_Logout_Screen {
	public function render_logout_screen( $settings ) {
		echo '<div class="test-screen">logout</div>';
	}
}

class ALYNT_AG_Test_Gateway_Shell_State_Screens extends ALYNT_AG_Frontend_State_Screens {
	public function render_registration_disabled_screen( $settings ) {
		echo '<div class="test-screen">registration_disabled</div>';
	}

	public function render_invalid_link_screen( $settings ) {
		echo '<div class="test-screen">invalidlink</div>';
	}
}

/**
 * Tests the frontend gateway shell renderer.
 */
class FrontendGatewayShellTest extends TestCase {

	/**
	 * Test settings.
	 *
	 * @var array<string,mixed>
	 */
	private $settings;

	protected function setUp(): void {
		parent::setUp();

		$GLOBALS['alynt_ag_test_is_rtl'] = false;
		$this->settings = array(
			'account_action_base' => '/account',
		);
	}

	protected function tearDown(): void {
		unset( $GLOBALS['alynt_ag_test_is_rtl'] );

		parent::tearDown();
	}

	public function test_render_gateway_shell_outputs_shell_branding_and_selected_screen() {
		$shell = $this->make_shell();

		ob_start();
		$shell->render_gateway_shell( 'register', $this->settings );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'class="alynt-ag-gateway"', $html );
		$this->assertStringContainsString( 'data-agw-screen="register"', $html );
		$this->assertStringContainsString( 'dir="ltr"', $html );
		$this->assertStringContainsString( 'style="--agw-color-primary:#123456;"', $html );
		$this->assertStringContainsString( '<div class="test-media"></div>', $html );
		$this->assertStringContainsString( '<div class="test-brand">Brand</div>', $html );
		$this->assertStringContainsString( '<div class="test-screen">register</div>', $html );
		$this->assertStringNotContainsString( '<div class="test-screen">login</div>', $html );
	}

	/**
	 * @dataProvider screen_provider
	 *
	 * @param string $screen        Screen key.
	 * @param string $expected_text Expected rendered text.
	 * @return void
	 */
	public function test_render_gateway_shell_dispatches_auth_screens( $screen, $expected_text ) {
		$shell = $this->make_shell();

		ob_start();
		$shell->render_gateway_shell( $screen, $this->settings );
		$html = ob_get_clean();

		$this->assertStringContainsString( '<div class="test-screen">' . $expected_text . '</div>', $html );
	}

	public function test_render_gateway_shell_with_password_preview_outputs_preview_form() {
		$shell = $this->make_shell();

		ob_start();
		$shell->render_gateway_shell_with_password_preview( $this->settings );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'data-agw-screen="setpassword"', $html );
		$this->assertStringContainsString( 'dir="ltr"', $html );
		$this->assertStringContainsString( '<div class="test-brand">Brand</div>', $html );
		$this->assertStringContainsString( 'class="test-password-preview"', $html );
		$this->assertStringContainsString( 'data-action="https://example.test/account"', $html );
	}

	public function test_render_gateway_shell_uses_rtl_direction_when_site_is_rtl() {
		$GLOBALS['alynt_ag_test_is_rtl'] = true;
		$shell                          = $this->make_shell();

		ob_start();
		$shell->render_gateway_shell( 'login', $this->settings );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'data-agw-screen="login"', $html );
		$this->assertStringContainsString( 'dir="rtl"', $html );

		ob_start();
		$shell->render_gateway_shell_with_password_preview( $this->settings );
		$preview_html = ob_get_clean();

		$this->assertStringContainsString( 'data-agw-screen="setpassword"', $preview_html );
		$this->assertStringContainsString( 'dir="rtl"', $preview_html );
	}

	public function screen_provider() {
		return array(
			'login'                 => array( 'login', 'login' ),
			'lost password'         => array( 'lostpassword', 'lostpassword' ),
			'set password'          => array( 'setpassword', 'setpassword' ),
			'logout'                => array( 'logout', 'logout' ),
			'registration disabled' => array( 'registration_disabled', 'registration_disabled' ),
			'invalid link'          => array( 'invalidlink', 'invalidlink' ),
			'unknown falls back'    => array( 'not-a-screen', 'login' ),
		);
	}

	/**
	 * Build a gateway shell with deterministic helper doubles.
	 *
	 * @return ALYNT_AG_Frontend_Gateway_Shell
	 */
	private function make_shell() {
		return new ALYNT_AG_Frontend_Gateway_Shell(
			new ALYNT_AG_Test_Gateway_Shell_Branding(),
			new ALYNT_AG_Test_Gateway_Shell_Login_Screen(),
			new ALYNT_AG_Test_Gateway_Shell_Register_Screen(),
			new ALYNT_AG_Test_Gateway_Shell_Lostpassword_Screen(),
			new ALYNT_AG_Test_Gateway_Shell_Setpassword_Screen(),
			new ALYNT_AG_Test_Gateway_Shell_Logout_Screen(),
			new ALYNT_AG_Test_Gateway_Shell_State_Screens()
		);
	}
}
