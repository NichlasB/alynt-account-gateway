/**
 * Custom CSS editor setup.
 *
 * @package Alynt_Account_Gateway
 */

export function alyntAgInitCssEditor() {
	const fields         = document.querySelectorAll( '[data-alynt-ag-css-editor]' );
	const editorSettings = window.alyntAgAdmin && window.alyntAgAdmin.codeEditor;

	if ( ! fields.length || ! window.wp || ! window.wp.codeEditor || ! editorSettings ) {
		return;
	}

	fields.forEach(
		function ( field ) {
			if ( field.dataset.alyntAgCssEditorReady === '1' ) {
				return;
			}

			window.wp.codeEditor.initialize( field, editorSettings );
			field.dataset.alyntAgCssEditorReady = '1';
		}
	);
}
