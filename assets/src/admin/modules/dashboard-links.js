/**
 * Dashboard custom-link editor.
 *
 * @package Alynt_Account_Gateway
 */

/**
 * Renumber a dashboard-link editor row.
 *
 * @param {HTMLElement} row   Dashboard-link row.
 * @param {string}      index Row index.
 * @return {void}
 */
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

function alyntAgHandleDashboardLinkInput( editor, event ) {
	if ( event.target.matches( '[data-alynt-ag-dashboard-link-json]' ) ) {
		event.target.dataset.alyntAgRawEdited = '1';
		return;
	}

	alyntAgHandleVisualDashboardLinkChange( editor );
}

function alyntAgBindDashboardLinkEvents( editor ) {
	const addButton = editor.querySelector( '[data-alynt-ag-dashboard-link-add]' );

	addButton.addEventListener( 'click', () => alyntAgAddDashboardLinkRow( editor ) );
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
	editor.addEventListener( 'input', ( event ) => alyntAgHandleDashboardLinkInput( editor, event ) );
	editor.addEventListener( 'change', ( event ) => alyntAgHandleDashboardLinkInput( editor, event ) );
}

function alyntAgBindDashboardLinkSubmit( editor ) {
	const form = editor.closest( 'form' );
	if ( ! form ) {
		return;
	}

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

function alyntAgInitDashboardLinkEditor( editor ) {
	alyntAgDashboardLinkRows( editor ).forEach(
		function ( row, index ) {
			alyntAgRenumberDashboardLinkRow( row, String( index ) );
		}
	);
	alyntAgBindDashboardLinkEvents( editor );
	alyntAgBindDashboardLinkSubmit( editor );
}

export function alyntAgInitDashboardLinks() {
	document.querySelectorAll( '[data-alynt-ag-dashboard-links]' ).forEach( alyntAgInitDashboardLinkEditor );
}
