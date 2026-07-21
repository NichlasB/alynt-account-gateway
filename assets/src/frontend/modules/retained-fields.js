/**
 * Restore non-secret form fields after a redirected validation error.
 *
 * @package Alynt_Account_Gateway
 */

const errorParameters = [
	'login_error',
	'reset_error',
	'registration_error',
	'resend_error',
	'password_error',
];

function storageKey( form ) {
	const action = form.querySelector( '[name="alynt_ag_action"]' );
	return `alynt_ag_form:${ action ? action.value : form.action }`;
}

function retainFields( form ) {
	const values = {};
	for ( const field of form.querySelectorAll( '[data-agw-retain][name]' ) ) {
		values[ field.name ] = field.type === 'checkbox' ? field.checked : field.value;
	}

	try {
		window.sessionStorage.setItem( storageKey( form ), JSON.stringify( values ) );
	} catch ( error ) {
		// Storage can be unavailable in privacy-restricted browser contexts.
	}
}

function restoreFields( form ) {
	let values = null;
	try {
		values = JSON.parse( window.sessionStorage.getItem( storageKey( form ) ) || 'null' );
		window.sessionStorage.removeItem( storageKey( form ) );
	} catch ( error ) {
		return;
	}

	if ( ! values || typeof values !== 'object' ) {
		return;
	}

	for ( const field of form.querySelectorAll( '[data-agw-retain][name]' ) ) {
		if ( ! Object.prototype.hasOwnProperty.call( values, field.name ) ) {
			continue;
		}

		if ( field.type === 'checkbox' ) {
			field.checked = Boolean( values[ field.name ] );
		} else {
			field.value = String( values[ field.name ] );
		}
	}

	form.dispatchEvent( new Event( 'input', { bubbles: true } ) );
	form.dispatchEvent( new Event( 'change', { bubbles: true } ) );
}

export function alyntAgInitRetainedFields() {
	const hasError = errorParameters.some( ( parameter ) => new URLSearchParams( window.location.search ).has( parameter ) );

	for ( const form of document.querySelectorAll( '[data-agw-retain-fields]' ) ) {
		form.addEventListener( 'submit', () => retainFields( form ) );
		if ( hasError ) {
			restoreFields( form );
		}
	}

	const invalidField = document.querySelector( '.agw-form [aria-invalid="true"]' );
	if ( invalidField ) {
		invalidField.focus();
	}
}
