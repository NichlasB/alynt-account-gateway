/**
 * Registration form readiness.
 *
 * @package Alynt_Account_Gateway
 */

/**
 * Update registration submit readiness.
 *
 * @param {HTMLFormElement} form Registration form.
 * @return {void}
 */
function alyntAgUpdateRegistrationForm( form ) {
	const requiredFields = form.querySelectorAll( '[data-agw-registration-required]' );
	const terms          = form.querySelector( '[data-agw-registration-terms]' );
	const submit         = form.querySelector( '[data-agw-registration-submit]' );

	if ( ! requiredFields.length || ! terms || ! submit ) {
		return;
	}

	const fieldsReady = Array.from( requiredFields ).every( ( field ) => field.value.trim() !== '' && field.checkValidity() );
	const isReady     = fieldsReady && terms.checked;

	submit.disabled = ! isReady;
	submit.setAttribute( 'aria-disabled', isReady ? 'false' : 'true' );
}

export function alyntAgInitRegistrationForms() {
	const forms = document.querySelectorAll( '[data-agw-registration-form]' );

	for ( const form of forms ) {
		const update = () => alyntAgUpdateRegistrationForm( form );

		form.addEventListener( 'input', update );
		form.addEventListener( 'change', update );
		update();
	}
}
