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

	/**
	 * Reads the complete admin JavaScript source tree.
	 *
	 * @return string
	 */
	private function get_admin_js() {
		$source_dir = dirname( __DIR__ ) . '/assets/src/admin';
		$files      = glob( $source_dir . '/modules/*.js' );

		$this->assertIsArray( $files );
		sort( $files );
		array_unshift( $files, $source_dir . '/index.js' );

		$js = '';
		foreach ( $files as $file ) {
			$module = file_get_contents( $file );

			$this->assertIsString( $module );
			$js .= "\n" . $module;
		}

		return $js;
	}

	public function test_admin_javascript_uses_focused_modules() {
		$source_dir = dirname( __DIR__ ) . '/assets/src/admin';
		$entry      = file_get_contents( $source_dir . '/index.js' );
		$modules    = glob( $source_dir . '/modules/*.js' );

		$this->assertIsString( $entry );
		$this->assertIsArray( $modules );
		$this->assertCount( 6, $modules );

		foreach ( $modules as $module ) {
			$relative_path = './modules/' . basename( $module );
			$lines         = file( $module );

			$this->assertStringContainsString( $relative_path, $entry );
			$this->assertIsArray( $lines );
			$this->assertLessThanOrEqual( 250, count( $lines ), $relative_path );
		}
	}

	public function test_email_save_state_tracks_fields_and_tinymce_before_disabling_actions() {
		$js = $this->get_admin_js();

		$this->assertIsString( $js );
		$this->assertStringContainsString( "document.querySelector( '[data-alynt-ag-email-settings]' )", $js );
		$this->assertStringContainsString( "event.target.matches( '.wp-editor-area' ) && ! event.isTrusted", $js );
		$this->assertStringContainsString( "state.settingsForm.addEventListener( 'input', handleSettingsChange )", $js );
		$this->assertStringContainsString( "state.settingsForm.addEventListener( 'change', handleSettingsChange )", $js );
		$this->assertStringContainsString( "editor.alyntAgEmailSaveStateTracked = 'pending'", $js );
		$this->assertStringContainsString( 'new window.FormData( state.settingsForm ).entries()', $js );
		$this->assertStringContainsString( 'state.settingsForm.elements.namedItem( entry[0] )', $js );
		$this->assertStringContainsString( "field.matches( '.wp-editor-area' )", $js );
		$this->assertStringContainsString( 'window.tinymce.get( field.id )', $js );
		$this->assertStringContainsString( 'alyntAgNormalizeEditorContent(', $js );
		$this->assertStringContainsString( "typeof window.switchEditors.wpautop !== 'function'", $js );
		$this->assertStringContainsString( 'new window.tinymce.html.DomParser( {}, editor.schema )', $js );
		$this->assertStringContainsString( 'new window.tinymce.html.Serializer( {}, editor.schema )', $js );
		$this->assertStringContainsString( 'window.switchEditors.wpautop( content )', $js );
		$this->assertStringContainsString( 'serializer.serialize( parser.parse( html ) )', $js );
		$this->assertStringContainsString( 'editor.isHidden()', $js );
		$this->assertStringContainsString( 'editor.isHidden() ? field.value : editor.getContent()', $js );
		$this->assertStringContainsString( 'state.initialState = alyntAgReadEmailSettings( state )', $js );
		$this->assertStringContainsString( 'state.initialState = state.initialState.map(', $js );
		$this->assertStringContainsString( 'return [ entry[0], alyntAgReadEmailFieldValue( field ) ]', $js );
		$this->assertStringContainsString( 'alyntAgSetEmailDirtyState( state, current !== initial )', $js );
		$this->assertStringContainsString( 'alyntAgUpdateInitialEmailField( state, textarea )', $js );
		$this->assertMatchesRegularExpression( "/window\\.setTimeout\\(.*editor\\.save\\(\\).*alyntAgUpdateInitialEmailField\\( state, textarea \\).*alyntAgUpdateEmailDirtyState\\( state \\);/s", $js );
		$this->assertStringContainsString( "'change input undo redo'", $js );
		$this->assertStringContainsString( 'editor.save()', $js );
		$this->assertStringContainsString( 'tinymce-editor-init.alyntAgEmailSaveState', $js );
		$this->assertStringContainsString( 'submit.disabled = state.isDirty', $js );
		$this->assertStringContainsString( "submit.setAttribute( 'aria-disabled', 'true' )", $js );
		$this->assertStringContainsString( "submit.removeAttribute( 'aria-disabled' )", $js );
		$this->assertStringContainsString( 'state.notice.hidden = ! state.isDirty', $js );
		$this->assertStringContainsString( "state.settingsForm.classList.toggle( 'alynt-ag-email-settings--dirty', state.isDirty )", $js );
		$this->assertStringContainsString( "window.addEventListener( 'beforeunload', handleBeforeUnload )", $js );
		$this->assertStringContainsString( 'event.preventDefault()', $js );
		$this->assertStringContainsString( "event.returnValue = ''", $js );
		$this->assertMatchesRegularExpression( "/state\\.settingsForm\\.addEventListener\\(\\s*'submit'.*alyntAgSetEmailDirtyState\\( state, false \\);/s", $js );
	}

	public function test_typography_presets_update_existing_stack_fields_and_preserve_custom_edits() {
		$js = $this->get_admin_js();

		$this->assertIsString( $js );
		$this->assertStringContainsString( "document.querySelector( '[data-alynt-ag-typography-presets]' )", $js );
		$this->assertStringContainsString( "document.querySelector( '#alynt-ag-heading_font_family' )", $js );
		$this->assertStringContainsString( "document.querySelector( '#alynt-ag-body_font_family' )", $js );
		$this->assertStringContainsString( "if ( option.value !== 'custom' )", $js );
		$this->assertStringContainsString( 'state.headingInput.value = option.dataset.heading', $js );
		$this->assertStringContainsString( 'state.bodyInput.value    = option.dataset.body', $js );
		$this->assertStringContainsString( "state.selector.value = 'custom'", $js );
		$this->assertStringContainsString( "state.headingInput.addEventListener( 'input', markCustom )", $js );
		$this->assertStringContainsString( "state.bodyInput.addEventListener( 'input', markCustom )", $js );
		$this->assertStringContainsString( "state.previewHeading.style.fontFamily = state.headingInput.value || 'inherit'", $js );
		$this->assertStringContainsString( "state.previewBody.style.fontFamily    = state.bodyInput.value || 'inherit'", $js );
	}

	public function test_admin_forms_confirm_consequential_actions_and_report_submission_progress() {
		$js = $this->get_admin_js();

		$this->assertStringContainsString( "document.querySelectorAll( '[data-alynt-ag-action-form]' )", $js );
		$this->assertStringContainsString( 'window.confirm( confirmation )', $js );
		$this->assertStringContainsString( "form.setAttribute( 'aria-busy', 'true' )", $js );
		$this->assertStringContainsString( "submit.setAttribute( 'aria-disabled', 'true' )", $js );
		$this->assertStringContainsString( "form.getAttribute( 'aria-busy' ) === 'true'", $js );
	}

	public function test_non_email_settings_forms_warn_before_discarding_unsaved_changes() {
		$js = $this->get_admin_js();

		$this->assertStringContainsString(
			"'[data-alynt-ag-settings-form]:not([data-alynt-ag-email-settings])'",
			$js
		);
		$this->assertStringContainsString( 'alyntAgSerializeSettingsForm( form ) !== state.initial', $js );
		$this->assertStringContainsString( "window.addEventListener( 'beforeunload', handleBeforeUnload )", $js );
	}

	public function test_color_controls_synchronize_picker_and_hex_text_in_both_directions() {
		$js = $this->get_admin_js();

		$this->assertIsString( $js );
		$this->assertStringContainsString( "document.querySelectorAll( '[data-alynt-ag-color-control]' )", $js );
		$this->assertStringContainsString( "control.querySelector( '[data-alynt-ag-color-picker]' )", $js );
		$this->assertStringContainsString( "control.querySelector( '[data-alynt-ag-color-text]' )", $js );
		$this->assertStringContainsString( 'const hex    = /^#[0-9a-f]{6}$/i', $js );
		$this->assertStringContainsString( 'picker.value = value', $js );
		$this->assertStringContainsString( 'text.value = picker.value.toUpperCase()', $js );
		$this->assertStringContainsString( "picker.addEventListener( 'input', updateText )", $js );
		$this->assertStringContainsString( "text.addEventListener( 'input', updatePicker )", $js );
		$this->assertStringContainsString( "text.addEventListener( 'change', normalizeText )", $js );
		$this->assertStringContainsString( "text.setAttribute( 'aria-invalid', 'true' )", $js );
		$this->assertStringContainsString( "control.classList.add( 'alynt-ag-color-control--invalid' )", $js );
	}
}
