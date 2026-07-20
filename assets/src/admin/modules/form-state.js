/**
 * Admin form progress, confirmation, and unsaved-change safeguards.
 *
 * @package Alynt_Account_Gateway
 */

/**
 * Serialize the current settings form state.
 *
 * @param {HTMLFormElement} form Settings form.
 * @return {string} Serialized form values.
 */
function alyntAgSerializeSettingsForm( form ) {
	return JSON.stringify( Array.from( new window.FormData( form ).entries() ) );
}

function alyntAgSetAdminFormSubmitting( form ) {
	form.classList.add( 'alynt-ag-form--submitting' );
	form.setAttribute( 'aria-busy', 'true' );

	form.querySelectorAll( 'button[type="submit"], input[type="submit"]' ).forEach(
		function ( submit ) {
			submit.disabled = true;
			submit.setAttribute( 'aria-disabled', 'true' );
		}
	);
}

function alyntAgResetAdminFormSubmitting( form ) {
	form.classList.remove( 'alynt-ag-form--submitting' );
	form.removeAttribute( 'aria-busy' );

	form.querySelectorAll( 'button[type="submit"], input[type="submit"]' ).forEach(
		function ( submit ) {
			submit.disabled = false;
			submit.removeAttribute( 'aria-disabled' );
		}
	);
}

function alyntAgBindAdminActionForm( form ) {
	form.addEventListener(
		'submit',
		function ( event ) {
			const confirmation = form.getAttribute( 'data-alynt-ag-confirm' );

			if (
				event.defaultPrevented ||
				! form.checkValidity() ||
				( confirmation && ! window.confirm( confirmation ) )
			) {
				if ( confirmation ) {
					event.preventDefault();
				}
				return;
			}

			if ( form.getAttribute( 'aria-busy' ) === 'true' ) {
				event.preventDefault();
				return;
			}

			alyntAgSetAdminFormSubmitting( form );

			if ( form.target === '_blank' ) {
				window.setTimeout( () => alyntAgResetAdminFormSubmitting( form ), 1000 );
			}
		}
	);
}

function alyntAgBindSettingsSaveState( form ) {
	const state = {
		initial: alyntAgSerializeSettingsForm( form ),
		isDirty: false,
	};

	const updateDirtyState   = function () {
		state.isDirty = alyntAgSerializeSettingsForm( form ) !== state.initial;
	};
	const handleBeforeUnload = function ( event ) {
		if ( ! state.isDirty ) {
			return;
		}

		event.preventDefault();
		event.returnValue = '';
	};

	form.addEventListener( 'input', updateDirtyState );
	form.addEventListener( 'change', updateDirtyState );
	form.addEventListener(
		'submit',
		function () {
			state.isDirty = false;
		}
	);
	window.addEventListener( 'beforeunload', handleBeforeUnload );
}

export function alyntAgInitAdminFormState() {
	document.querySelectorAll( '[data-alynt-ag-action-form]' ).forEach( alyntAgBindAdminActionForm );
	document.querySelectorAll(
		'[data-alynt-ag-settings-form]:not([data-alynt-ag-email-settings])'
	).forEach( alyntAgBindSettingsSaveState );
}
