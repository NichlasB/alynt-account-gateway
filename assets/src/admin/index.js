/**
 * Admin entry point.
 *
 * @package Alynt_Account_Gateway
 */

import './style.css';

const root = document.querySelector( '.alynt-ag-admin' );

if ( root ) {
	root.classList.add( 'alynt-ag-admin--ready' );
}

function alyntAgInitEmailSaveState() {
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

alyntAgInitEmailSaveState();

function alyntAgInitTypographyPresets() {
	const control      = document.querySelector( '[data-alynt-ag-typography-presets]' );
	const headingInput = document.querySelector( '#alynt-ag-heading_font_family' );
	const bodyInput    = document.querySelector( '#alynt-ag-body_font_family' );

	if ( ! control || ! headingInput || ! bodyInput ) {
		return;
	}

	const selector       = control.querySelector( '[data-alynt-ag-typography-select]' );
	const previewHeading = control.querySelector( '[data-alynt-ag-typography-heading]' );
	const previewBody    = control.querySelector( '[data-alynt-ag-typography-body]' );
	const status         = control.querySelector( '[data-alynt-ag-typography-status]' );
	const statusPrefix   = control.dataset.statusPrefix || 'Current pairing:';

	function selectedOption() {
		return selector.options[ selector.selectedIndex ];
	}

	function updateStatus() {
		status.textContent = `${ statusPrefix } ${ selectedOption().textContent.trim() }`;
	}

	function updatePreview() {
		previewHeading.style.fontFamily = headingInput.value || 'inherit';
		previewBody.style.fontFamily    = bodyInput.value || 'inherit';
	}

	function applyPreset() {
		const option = selectedOption();

		if ( option.value !== 'custom' ) {
			headingInput.value = option.dataset.heading;
			bodyInput.value    = option.dataset.body;
		}

		updatePreview();
		updateStatus();
	}

	function markCustom() {
		selector.value = 'custom';
		updatePreview();
		updateStatus();
	}

	selector.addEventListener( 'change', applyPreset );
	headingInput.addEventListener( 'input', markCustom );
	bodyInput.addEventListener( 'input', markCustom );
	updatePreview();
}

alyntAgInitTypographyPresets();

function alyntAgOpenMediaFrame( field ) {
	const input        = field.querySelector( '[data-alynt-ag-media-input]' );
	const preview      = field.querySelector( '[data-alynt-ag-media-preview]' );
	const removeButton = field.querySelector( '[data-alynt-ag-media-remove]' );
	const labels       = window.alyntAgAdmin || {};
	const frame        = window.wp.media(
		{
			title: labels.selectImage || 'Select Image',
			button: {
				text: labels.useImage || 'Use Image',
			},
			multiple: false,
		}
	);

	frame.on(
		'select',
		function () {
			const attachment = frame.state().get( 'selection' ).first().toJSON();
			const medium     = attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium : null;
			const imageUrl   = medium ? medium.url : attachment.url;
			const image      = document.createElement( 'img' );

			image.src = imageUrl;
			image.alt = '';

			input.value = attachment.id;
			preview.replaceChildren( image );
			removeButton.disabled = false;
		}
	);

	frame.open();
}

function alyntAgHandleMediaClick( event ) {
	const selectButton = event.target.closest( '[data-alynt-ag-media-select]' );
	const removeButton = event.target.closest( '[data-alynt-ag-media-remove]' );

	if ( selectButton ) {
		alyntAgOpenMediaFrame( selectButton.closest( '[data-alynt-ag-media-field]' ) );
	}

	if ( removeButton ) {
		const field   = removeButton.closest( '[data-alynt-ag-media-field]' );
		const input   = field.querySelector( '[data-alynt-ag-media-input]' );
		const preview = field.querySelector( '[data-alynt-ag-media-preview]' );

		input.value = '0';
		preview.replaceChildren();
		removeButton.disabled = true;
	}
}

document.addEventListener( 'click', alyntAgHandleMediaClick );

function alyntAgRenumberDashboardLinkRow( row, index ) {
	row.querySelectorAll( '[id]' ).forEach(
		function ( field ) {
			field.id = field.id.replace( '__index__', index );
		}
	);

	row.querySelectorAll( 'label[for]' ).forEach(
		function ( label ) {
			label.setAttribute( 'for', label.getAttribute( 'for' ).replace( '__index__', index ) );
		}
	);
}

function alyntAgDashboardLinkRows( editor ) {
	return Array.from( editor.querySelectorAll( '[data-alynt-ag-dashboard-link-row]' ) ).filter(
		function ( row ) {
			return ! row.closest( 'template' );
		}
	);
}

function alyntAgSerializeDashboardLinks( editor ) {
	const textarea = editor.querySelector( '[data-alynt-ag-dashboard-link-json]' );
	const links    = alyntAgDashboardLinkRows( editor ).map(
		function ( row ) {
			const label  = row.querySelector( '[data-alynt-ag-dashboard-link-label]' ).value.trim();
			const url    = row.querySelector( '[data-alynt-ag-dashboard-link-url]' ).value.trim();
			const icon   = row.querySelector( '[data-alynt-ag-dashboard-link-icon]' ).value;
			const order  = parseInt( row.querySelector( '[data-alynt-ag-dashboard-link-order]' ).value, 10 );
			const target = row.querySelector( '[data-alynt-ag-dashboard-link-new-tab]' ).checked ? '_blank' : '_self';
			const roles  = Array.from( row.querySelectorAll( '[data-alynt-ag-dashboard-link-role]:checked' ) ).map(
				function ( checkbox ) {
					return checkbox.value;
				}
			);

			if ( ! label || ! url ) {
				return null;
			}

			return {
				label,
				url,
				icon: icon || 'link',
				order: Number.isNaN( order ) ? 100 : Math.max( 0, order ),
				target,
				roles,
			};
		}
	).filter( Boolean );

	textarea.value = JSON.stringify( links, null, 2 );
}

function alyntAgHandleVisualDashboardLinkChange( editor ) {
	const textarea = editor.querySelector( '[data-alynt-ag-dashboard-link-json]' );

	delete textarea.dataset.alyntAgRawEdited;
	alyntAgSerializeDashboardLinks( editor );
}

function alyntAgAddDashboardLinkRow( editor ) {
	const template = editor.querySelector( '[data-alynt-ag-dashboard-link-template]' );
	const rows     = editor.querySelector( '[data-alynt-ag-dashboard-link-rows]' );
	const fragment = template.content.cloneNode( true );
	const row      = fragment.querySelector( '[data-alynt-ag-dashboard-link-row]' );
	const index    = String( Date.now() );

	alyntAgRenumberDashboardLinkRow( row, index );
	rows.appendChild( fragment );
	alyntAgHandleVisualDashboardLinkChange( editor );
}

document.querySelectorAll( '[data-alynt-ag-dashboard-links]' ).forEach(
	function ( editor ) {
		const addButton = editor.querySelector( '[data-alynt-ag-dashboard-link-add]' );

		alyntAgDashboardLinkRows( editor ).forEach(
			function ( row, index ) {
				alyntAgRenumberDashboardLinkRow( row, String( index ) );
			}
		);

		addButton.addEventListener(
			'click',
			function () {
				alyntAgAddDashboardLinkRow( editor );
			}
		);

		editor.addEventListener(
			'click',
			function ( event ) {
				const removeButton = event.target.closest( '[data-alynt-ag-dashboard-link-remove]' );

				if ( removeButton ) {
					removeButton.closest( '[data-alynt-ag-dashboard-link-row]' ).remove();
					alyntAgSerializeDashboardLinks( editor );
				}
			}
		);

		editor.addEventListener(
			'input',
			function ( event ) {
				if ( event.target.matches( '[data-alynt-ag-dashboard-link-json]' ) ) {
					event.target.dataset.alyntAgRawEdited = '1';
					return;
				}

				alyntAgHandleVisualDashboardLinkChange( editor );
			}
		);

		editor.addEventListener(
			'change',
			function ( event ) {
				if ( event.target.matches( '[data-alynt-ag-dashboard-link-json]' ) ) {
					event.target.dataset.alyntAgRawEdited = '1';
					return;
				}

				alyntAgHandleVisualDashboardLinkChange( editor );
			}
		);

		const form = editor.closest( 'form' );
		if ( form ) {
			form.addEventListener(
				'submit',
				function () {
					const textarea = editor.querySelector( '[data-alynt-ag-dashboard-link-json]' );

					if ( textarea.dataset.alyntAgRawEdited !== '1' ) {
						alyntAgSerializeDashboardLinks( editor );
					}
				}
			);
		}
	}
);
