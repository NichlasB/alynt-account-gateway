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
		$this->assertStringContainsString( 'new window.FormData( settingsForm ).entries()', $js );
		$this->assertStringContainsString( 'let initialState = readSettings()', $js );
		$this->assertStringContainsString( 'initialState = initialState.map(', $js );
		$this->assertStringContainsString( 'return [ entry[0], field.value ]', $js );
		$this->assertStringContainsString( 'setDirtyState( serializeSettings() !== serializeSettings( initialState ) )', $js );
		$this->assertStringContainsString( 'updateInitialField( textarea )', $js );
		$this->assertMatchesRegularExpression( "/window\\.setTimeout\\(.*editor\\.save\\(\\).*updateInitialField\\( textarea \\).*updateDirtyState\\(\\);/s", $js );
		$this->assertStringContainsString( "'change input undo redo'", $js );
		$this->assertStringContainsString( 'editor.save()', $js );
		$this->assertStringContainsString( 'tinymce-editor-init.alyntAgEmailSaveState', $js );
		$this->assertStringContainsString( 'submit.disabled = isDirty', $js );
		$this->assertStringContainsString( "submit.setAttribute( 'aria-disabled', 'true' )", $js );
		$this->assertStringContainsString( "submit.removeAttribute( 'aria-disabled' )", $js );
		$this->assertStringContainsString( 'notice.hidden = ! isDirty', $js );
		$this->assertStringContainsString( "settingsForm.classList.toggle( 'alynt-ag-email-settings--dirty', isDirty )", $js );
		$this->assertStringContainsString( "window.addEventListener( 'beforeunload', handleBeforeUnload )", $js );
		$this->assertStringContainsString( 'event.preventDefault()', $js );
		$this->assertStringContainsString( "event.returnValue = ''", $js );
		$this->assertMatchesRegularExpression( "/settingsForm\\.addEventListener\\(\\s*'submit'.*setDirtyState\\( false \\);/s", $js );
	}
}
