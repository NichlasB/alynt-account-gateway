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

	let isDirty = false;

	function setDirty() {
		if ( isDirty ) {
			return;
		}

		isDirty       = true;
		notice.hidden = false;
		settingsForm.classList.add( 'alynt-ag-email-settings--dirty' );

		actionForms.forEach(
			function ( form ) {
				const submit = form.querySelector( 'button[type="submit"], input[type="submit"]' );

				if ( submit ) {
					submit.disabled = true;
					submit.setAttribute( 'aria-disabled', 'true' );
				}
			}
		);
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
				editor.alyntAgEmailSaveStateTracked = true;
				editor.on( 'change input undo redo', setDirty );
			},
			0
		);
	}

	function handleSettingsChange( event ) {
		if ( event.target.matches( '.wp-editor-area' ) && ! event.isTrusted ) {
			return;
		}

		setDirty();
	}

	settingsForm.addEventListener( 'input', handleSettingsChange );
	settingsForm.addEventListener( 'change', handleSettingsChange );

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
