<?php
/**
 * Settings page email tools tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

require_once ALYNT_AG_PLUGIN_DIR . 'admin/class-settings-page.php';

if ( ! function_exists( 'submit_button' ) ) {
	function submit_button( $text = null, $type = 'primary', $name = 'submit', $wrap = true, $other_attributes = null ) {
		unset( $type, $wrap, $other_attributes );

		echo '<input type="submit" name="' . esc_attr( $name ) . '" value="' . esc_attr( (string) $text ) . '">';
	}
}

/**
 * Tests email template editor guidance on the settings page.
 */
class SettingsPageEmailToolsTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		$GLOBALS['alynt_ag_test_editors'] = array();
	}

	/**
	 * Invoke a private settings page helper.
	 *
	 * @param ALYNT_AG_Settings_Page $settings_page Settings page instance.
	 * @param string                 $method        Method name.
	 * @param array<int,mixed>       $args          Method arguments.
	 * @return mixed
	 */
	private function invoke_helper( $settings_page, $method, $args = array() ) {
		$reflection = new ReflectionMethod( $settings_page, $method );

		return $reflection->invokeArgs( $settings_page, $args );
	}

	public function test_email_template_reference_documents_required_action_tokens() {
		$settings_page = new ALYNT_AG_Settings_Page();
		$reference     = $this->invoke_helper( $settings_page, 'email_template_reference' );

		$this->assertSame(
			array(
				'registration_confirmation',
				'password_reset',
				'password_changed',
				'new_user_welcome',
				'email_change_confirmation',
			),
			array_keys( $reference )
		);
		$this->assertSame( array( 'confirmation_url', 'expiry_hours' ), $reference['registration_confirmation']['tokens'] );
		$this->assertSame( array( 'reset_url' ), $reference['password_reset']['tokens'] );
		$this->assertSame( array(), $reference['password_changed']['tokens'] );
		$this->assertSame( array( 'dashboard_url' ), $reference['new_user_welcome']['tokens'] );
		$this->assertSame( array( 'change_email_url' ), $reference['email_change_confirmation']['tokens'] );
	}

	public function test_email_tools_render_template_reference_tokens_and_accessible_form_help() {
		$settings_page = new ALYNT_AG_Settings_Page();

		ob_start();
		$this->invoke_helper( $settings_page, 'render_email_tools' );
		$output = ob_get_clean();

		$this->assertStringContainsString( 'Template Reference', $output );
		$this->assertStringContainsString( 'Available Template Tokens', $output );
		$this->assertStringContainsString( '{{confirmation_url}}', $output );
		$this->assertStringContainsString( '{{reset_url}}', $output );
		$this->assertStringContainsString( '{{change_email_url}}', $output );
		$this->assertStringContainsString( 'Core profile email-change requests may use a plain-text body', $output );
		$this->assertStringContainsString( 'aria-describedby="alynt-ag-email-preview-help"', $output );
		$this->assertStringContainsString( 'aria-describedby="alynt-ag-email-test-help"', $output );
		$this->assertStringContainsString( 'Sends one real test email to this recipient.', $output );
		$this->assertStringContainsString( 'data-alynt-ag-email-save-state', $output );
		$this->assertStringContainsString( 'role="status"', $output );
		$this->assertStringContainsString( 'aria-live="polite"', $output );
		$this->assertStringContainsString( 'You have unsaved email changes.', $output );
		$this->assertStringContainsString( 'Save Settings before previewing, sending a test email, or leaving this page.', $output );
		$this->assertSame( 2, substr_count( $output, 'data-alynt-ag-requires-saved-email-settings' ) );
	}

	public function test_rich_text_field_uses_wordpress_visual_and_text_editor_without_media_uploads() {
		$settings_page = new ALYNT_AG_Settings_Page();

		ob_start();
		$this->invoke_helper(
			$settings_page,
			'render_field',
			array(
				'email_password_reset_body',
				array( 'type' => 'rich_text' ),
				'<h2>Reset {{first_name}}</h2>',
			)
		);
		$output = ob_get_clean();

		$this->assertCount( 1, $GLOBALS['alynt_ag_test_editors'] );
		$editor = $GLOBALS['alynt_ag_test_editors'][0];
		$this->assertSame( 'alynt-ag-email_password_reset_body', $editor['editor_id'] );
		$this->assertSame( 'alynt_ag_settings[email_password_reset_body]', $editor['settings']['textarea_name'] );
		$this->assertFalse( $editor['settings']['media_buttons'] );
		$this->assertFalse( $editor['settings']['drag_drop_upload'] );
		$this->assertIsArray( $editor['settings']['tinymce'] );
		$this->assertIsArray( $editor['settings']['quicktags'] );
		$this->assertStringContainsString( 'formatselect,bold,italic', $editor['settings']['tinymce']['toolbar1'] );
		$this->assertStringContainsString( 'wp-editor-wrap', $output );
	}
}
