<?php
/**
 * Admin JavaScript source tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests important admin JavaScript behavior markers.
 */
class AdminJsSourceTest extends TestCase {

	public function test_email_save_state_tracks_fields_and_tinymce_before_disabling_actions() {
		$js = file_get_contents( dirname( __DIR__ ) . '/assets/src/admin/index.js' );

		$this->assertIsString( $js );
		$this->assertStringContainsString( "document.querySelector( '[data-alynt-ag-email-settings]' )", $js );
		$this->assertStringContainsString( "event.target.matches( '.wp-editor-area' ) && ! event.isTrusted", $js );
		$this->assertStringContainsString( "settingsForm.addEventListener( 'input', handleSettingsChange )", $js );
		$this->assertStringContainsString( "settingsForm.addEventListener( 'change', handleSettingsChange )", $js );
		$this->assertStringContainsString( "editor.alyntAgEmailSaveStateTracked = 'pending'", $js );
		$this->assertStringContainsString( "editor.on( 'change input undo redo', setDirty )", $js );
		$this->assertStringContainsString( 'tinymce-editor-init.alyntAgEmailSaveState', $js );
		$this->assertStringContainsString( 'submit.disabled = true', $js );
		$this->assertStringContainsString( "submit.setAttribute( 'aria-disabled', 'true' )", $js );
		$this->assertStringContainsString( 'notice.hidden = false', $js );
		$this->assertStringContainsString( "window.addEventListener( 'beforeunload', handleBeforeUnload )", $js );
		$this->assertStringContainsString( 'event.preventDefault()', $js );
		$this->assertStringContainsString( "event.returnValue = ''", $js );
		$this->assertMatchesRegularExpression( "/settingsForm\\.addEventListener\\(\\s*'submit'.*isDirty = false;/s", $js );
	}
}
