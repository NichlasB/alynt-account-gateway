<?php
/**
 * Settings page field renderer tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

require_once ALYNT_AG_PLUGIN_DIR . 'admin/class-settings-page.php';

/**
 * Tests core settings field output after renderer decomposition.
 */
class SettingsPageFieldRendererTest extends TestCase {

	protected function setUp(): void {
		$GLOBALS['alynt_ag_test_editors'] = array();
	}

	/**
	 * Render one settings field.
	 *
	 * @param string              $key   Field key.
	 * @param array<string,mixed> $field Field schema.
	 * @param mixed               $value Current value.
	 * @return string
	 */
	private function render_field( $key, $field, $value ) {
		ob_start();
		alynt_ag_test_invoke_settings_page_method(
			new ALYNT_AG_Settings_Page(),
			'render_field',
			array( $key, $field, $value )
		);

		return (string) ob_get_clean();
	}

	public function test_boolean_field_preserves_hidden_and_checked_inputs() {
		$output = $this->render_field( 'enabled', array( 'type' => 'boolean' ), true );

		$this->assertStringContainsString( 'type="hidden"', $output );
		$this->assertStringContainsString( 'type="checkbox"', $output );
		$this->assertStringContainsString( 'checked=', $output );
	}

	public function test_scalar_fields_preserve_types_and_values() {
		$integer = $this->render_field( 'limit', array( 'type' => 'integer', 'min' => 1, 'max' => 100 ), 12 );
		$email   = $this->render_field( 'recipient', array( 'type' => 'email' ), 'test@example.com' );
		$text    = $this->render_field( 'message', array( 'type' => 'textarea' ), '<strong>Safe</strong>' );

		$this->assertStringContainsString( 'type="number"', $integer );
		$this->assertStringContainsString( 'value="12"', $integer );
		$this->assertStringContainsString( 'min="1"', $integer );
		$this->assertStringContainsString( 'max="100"', $integer );
		$this->assertStringContainsString( 'type="email"', $email );
		$this->assertStringContainsString( 'value="test@example.com"', $email );
		$this->assertStringContainsString( '&lt;strong&gt;Safe&lt;/strong&gt;', $text );
	}

	public function test_rich_text_field_uses_wordpress_editor_configuration() {
		$output = $this->render_field( 'email_body', array( 'type' => 'rich_text' ), '<p>Hello</p>' );

		$this->assertStringContainsString( 'class="wp-editor-wrap"', $output );
		$this->assertCount( 1, $GLOBALS['alynt_ag_test_editors'] );
		$this->assertSame( 'alynt_ag_settings[email_body]', $GLOBALS['alynt_ag_test_editors'][0]['settings']['textarea_name'] );
		$this->assertFalse( $GLOBALS['alynt_ag_test_editors'][0]['settings']['media_buttons'] );
	}

	public function test_select_field_preserves_options_and_selection() {
		$output = $this->render_field(
			'mode',
			array(
				'type'    => 'select',
				'options' => array(
					'one' => 'One',
					'two' => 'Two',
				),
			),
			'two'
		);

		$this->assertStringContainsString( '<select', $output );
		$this->assertStringContainsString( 'value="one"', $output );
		$this->assertStringContainsString( 'value="two"', $output );
		$this->assertStringContainsString( 'selected="selected"', $output );
	}

	public function test_secret_field_preserves_password_type_and_ltr_direction() {
		$output = $this->render_field( 'api_secret', array( 'type' => 'secret' ), 'secret-value' );

		$this->assertStringContainsString( 'type="password"', $output );
		$this->assertStringContainsString( 'value="secret-value"', $output );
		$this->assertStringContainsString( 'dir="ltr"', $output );
	}
}
