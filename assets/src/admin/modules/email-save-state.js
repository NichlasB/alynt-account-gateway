/**
 * Rich email editor save-state tracking.
 *
 * @package Alynt_Account_Gateway
 */

export function alyntAgInitEmailSaveState() {
	const settingsForm = document.querySelector( '[data-alynt-ag-email-settings]' );
	const notice       = document.querySelector( '[data-alynt-ag-email-save-state]' );
	const actionForms  = document.querySelectorAll( '[data-alynt-ag-requires-saved-email-settings]' );

	if ( ! settingsForm || ! notice || ! actionForms.length ) {
		return;
	}

	let isDirty      = false;
	let initialState = readSettings();

	function normalizeEditorContent( editor, content ) {
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

	function readFieldValue( field ) {
		if ( ! field.matches( '.wp-editor-area' ) || ! window.tinymce ) {
			return field.value;
		}

		const editor = window.tinymce.get( field.id );

		if ( ! editor ) {
			return field.value;
		}

		return normalizeEditorContent(
			editor,
			editor.isHidden() ? field.value : editor.getContent()
		);
	}

	function readSettings() {
		return Array.from( new window.FormData( settingsForm ).entries() ).map(
			function ( entry ) {
				const field = settingsForm.elements.namedItem( entry[0] );

				if ( ! field || typeof field.matches !== 'function' ) {
					return entry;
				}

				return [ entry[0], readFieldValue( field ) ];
			}
		);
	}

	function serializeSettings( entries ) {
		return JSON.stringify( entries || readSettings() );
	}

	function updateInitialField( field ) {
		initialState = initialState.map(
			function ( entry ) {
				if ( entry[0] !== field.name ) {
					return entry;
				}

				return [ entry[0], readFieldValue( field ) ];
			}
		);
	}

	function setDirtyState( nextIsDirty ) {
		if ( isDirty === nextIsDirty ) {
			return;
		}

		isDirty       = nextIsDirty;
		notice.hidden = ! isDirty;
		settingsForm.classList.toggle( 'alynt-ag-email-settings--dirty', isDirty );

		actionForms.forEach(
			function ( form ) {
				const submit = form.querySelector( 'button[type="submit"], input[type="submit"]' );

				if ( submit ) {
					submit.disabled = isDirty;

					if ( isDirty ) {
						submit.setAttribute( 'aria-disabled', 'true' );
					} else {
						submit.removeAttribute( 'aria-disabled' );
					}
				}
			}
		);
	}

	function updateDirtyState() {
		setDirtyState( serializeSettings() !== serializeSettings( initialState ) );
	}

	function trackTinyMceEditor( editor ) {
		if ( ! editor || editor.alyntAgEmailSaveStateTracked ) {
			return;
		}

		const textarea = editor.getElement();

		if ( ! textarea || ! settingsForm.contains( textarea ) ) {
			return;
		}

		editor.alyntAgEmailSaveStateTracked = 'pending';
		window.setTimeout(
			function () {
				editor.save();
				updateInitialField( textarea );
				editor.alyntAgEmailSaveStateTracked = true;
				editor.on(
					'change input undo redo',
					function () {
						editor.save();
						updateDirtyState();
					}
				);
				updateDirtyState();
			},
			0
		);
	}

	function handleSettingsChange( event ) {
		if ( event.target.matches( '.wp-editor-area' ) && ! event.isTrusted ) {
			return;
		}

		updateDirtyState();
	}

	function handleBeforeUnload( event ) {
		if ( ! isDirty ) {
			return;
		}

		event.preventDefault();
		event.returnValue = '';
	}

	settingsForm.addEventListener( 'input', handleSettingsChange );
	settingsForm.addEventListener( 'change', handleSettingsChange );
	settingsForm.addEventListener(
		'submit',
		function () {
			setDirtyState( false );
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
				trackTinyMceEditor( editor );
			}
		);
	}
}
