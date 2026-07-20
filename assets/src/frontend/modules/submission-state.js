/**
 * Prevent duplicate gateway form submissions and expose progress to assistive
 * technology.
 *
 * @package Alynt_Account_Gateway
 */

/**
 * Mark a gateway form as submitting.
 *
 * @param {HTMLFormElement} form Gateway form.
 * @return {void}
 */
function alyntAgSetFormSubmitting( form ) {
	form.dataset.agwSubmitting = '1';
	form.classList.add( 'agw-form--submitting' );
	form.setAttribute( 'aria-busy', 'true' );

	form.querySelectorAll( 'button[type="submit"], input[type="submit"]' ).forEach(
		function ( submit ) {
			submit.disabled = true;
			submit.setAttribute( 'aria-disabled', 'true' );
		}
	);
}

function alyntAgHandleFormSubmit( event ) {
	const form = event.currentTarget;

	if ( event.defaultPrevented || ! form.checkValidity() ) {
		return;
	}

	if ( form.dataset.agwSubmitting === '1' ) {
		event.preventDefault();
		return;
	}

	alyntAgSetFormSubmitting( form );
}

export function alyntAgInitSubmissionState() {
	document.querySelectorAll( '.agw-form' ).forEach(
		function ( form ) {
			form.addEventListener( 'submit', alyntAgHandleFormSubmit );
		}
	);
}
