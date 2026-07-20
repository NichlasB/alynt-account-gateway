/**
 * Rich email editor save-state tracking.
 *
 * @package Alynt_Account_Gateway
 */

/**
 * Normalize TinyMCE content before comparison.
 *
 * @param {Object} editor  TinyMCE editor.
 * @param {string} content Editor content.
 *
 * @return {string} Normalized content.
 */
function alyntAgNormalizeEditorContent( editor, content ) {
	if (
		! window.switchEditors ||
		typeof window.switchEditors.wpautop !== 'function' ||
		! window.tinymce.html
	) {
		return content;
	}

	const parser     = new window.tinymce.html.DomParser( {}, editor.schema );
	const serializer = new window.tinymce.html.Serializer( {}, editor.schema );
	const html       = window.switchEditors.wpautop( content );

	return serializer.serialize( parser.parse( html ) );
}

function alyntAgReadEmailFieldValue( field ) {
	if ( ! field.matches( '.wp-editor-area' ) || ! window.tinymce ) {
		return field.value;
	}

	const editor = window.tinymce.get( field.id );

	if ( ! editor ) {
		return field.value;
	}

	return alyntAgNormalizeEditorContent(
		editor,
		editor.isHidden() ? field.value : editor.getContent()
	);
}

function alyntAgReadEmailSettings( state ) {
	return Array.from( new window.FormData( state.settingsForm ).entries() ).map(
		function ( entry ) {
			const field = state.settingsForm.elements.namedItem( entry[0] );

			if ( ! field || typeof field.matches !== 'function' ) {
				return entry;
			}

			return [ entry[0], alyntAgReadEmailFieldValue( field ) ];
		}
	);
}

function alyntAgSerializeEmailSettings( state, entries ) {
	return JSON.stringify( entries || alyntAgReadEmailSettings( state ) );
}

function alyntAgUpdateInitialEmailField( state, field ) {
	state.initialState = state.initialState.map(
		function ( entry ) {
			if ( entry[0] !== field.name ) {
				return entry;
			}

			return [ entry[0], alyntAgReadEmailFieldValue( field ) ];
		}
	);
}

function alyntAgSetEmailDirtyState( state, nextIsDirty ) {
	if ( state.isDirty === nextIsDirty ) {
		return;
	}

	state.isDirty       = nextIsDirty;
	state.notice.hidden = ! state.isDirty;
	state.settingsForm.classList.toggle( 'alynt-ag-email-settings--dirty', state.isDirty );

	state.actionForms.forEach(
		function ( form ) {
			const submit = form.querySelector( 'button[type="submit"], input[type="submit"]' );

			if ( ! submit ) {
				return;
			}

			submit.disabled = state.isDirty;
			if ( state.isDirty ) {
				submit.setAttribute( 'aria-disabled', 'true' );
			} else {
				submit.removeAttribute( 'aria-disabled' );
			}
		}
	);
}

function alyntAgUpdateEmailDirtyState( state ) {
	const current = alyntAgSerializeEmailSettings( state );
	const initial = alyntAgSerializeEmailSettings( state, state.initialState );

	alyntAgSetEmailDirtyState( state, current !== initial );
}

function alyntAgTrackEmailEditor( state, editor ) {
	if ( ! editor || editor.alyntAgEmailSaveStateTracked ) {
		return;
	}

	const textarea = editor.getElement();
	if ( ! textarea || ! state.settingsForm.contains( textarea ) ) {
		return;
	}

	editor.alyntAgEmailSaveStateTracked = 'pending';
	window.setTimeout(
		function () {
			editor.save();
			alyntAgUpdateInitialEmailField( state, textarea );
			editor.alyntAgEmailSaveStateTracked = true;
			editor.on(
				'change input undo redo',
				function () {
					editor.save();
					alyntAgUpdateEmailDirtyState( state );
				}
			);
			alyntAgUpdateEmailDirtyState( state );
		},
		0
	);
}

function alyntAgBindEmailSaveState( state ) {
	const handleSettingsChange = function ( event ) {
		if ( event.target.matches( '.wp-editor-area' ) && ! event.isTrusted ) {
			return;
		}

		alyntAgUpdateEmailDirtyState( state );
	};
	const handleBeforeUnload   = function ( event ) {
		if ( ! state.isDirty ) {
			return;
		}

		event.preventDefault();
		event.returnValue = '';
	};
	const trackTinyMceEditor   = ( editor ) => alyntAgTrackEmailEditor( state, editor );

	state.settingsForm.addEventListener( 'input', handleSettingsChange );
	state.settingsForm.addEventListener( 'change', handleSettingsChange );
	state.settingsForm.addEventListener(
		'submit',
		function () {
			alyntAgSetEmailDirtyState( state, false );
		}
	);
	window.addEventListener( 'beforeunload', handleBeforeUnload );

	if ( window.tinymce && Array.isArray( window.tinymce.editors ) ) {
		window.tinymce.editors.forEach( trackTinyMceEditor );
	}

	if ( window.jQuery ) {
		window.jQuery( document ).on(
			'tinymce-editor-init.alyntAgEmailSaveState',
			function ( event, editor ) {
				alyntAgTrackEmailEditor( state, editor );
			}
		);
	}
}

export function alyntAgInitEmailSaveState() {
	const state = {
		settingsForm: document.querySelector( '[data-alynt-ag-email-settings]' ),
		notice: document.querySelector( '[data-alynt-ag-email-save-state]' ),
		actionForms: document.querySelectorAll( '[data-alynt-ag-requires-saved-email-settings]' ),
		isDirty: false,
		initialState: [],
	};

	if ( ! state.settingsForm || ! state.notice || ! state.actionForms.length ) {
		return;
	}

	state.initialState = alyntAgReadEmailSettings( state );
	alyntAgBindEmailSaveState( state );
}
