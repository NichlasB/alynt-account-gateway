/**
 * Frontend entry point.
 *
 * @package Alynt_Account_Gateway
 */

import './style.css';

document.documentElement.classList.add( 'alynt-ag-frontend-ready' );

const alyntAgLabels = window.alyntAgFrontend && window.alyntAgFrontend.labels ? window.alyntAgFrontend.labels : {};

function alyntAgTogglePassword( event ) {
	const toggle = event.target.closest( '[data-agw-password-toggle]' );

	if ( ! toggle ) {
		return;
	}

	const controlledId = toggle.getAttribute( 'aria-controls' );
	const controlled   = controlledId ? document.getElementById( controlledId ) : null;
	const wrapper      = toggle.closest( '.agw-password' );
	const input        = controlled || ( wrapper ? wrapper.querySelector( 'input' ) : null );

	if ( ! input ) {
		return;
	}

	const shouldShow = input.type === 'password';
	const label      = shouldShow ? alyntAgLabels.hidePassword || 'Hide password' : alyntAgLabels.showPassword || 'Show password';

	input.type         = shouldShow ? 'text' : 'password';
	toggle.textContent = shouldShow ? alyntAgLabels.hide || 'Hide' : alyntAgLabels.show || 'Show';
	toggle.setAttribute( 'aria-label', label );
	toggle.setAttribute( 'aria-pressed', shouldShow ? 'true' : 'false' );
}

function alyntAgGetPasswordChecks( password, confirm ) {
	return {
		length: password.length >= 12,
		uppercase: /[A-Z]/.test( password ),
		lowercase: /[a-z]/.test( password ),
		number: /[0-9]/.test( password ),
		symbol: /[^A-Za-z0-9]/.test( password ),
		match: password.length > 0 && password === confirm,
	};
}

function alyntAgUpdatePasswordPolicy( form ) {
	const passwordInput = form.querySelector( '[data-agw-password-input]' );
	const confirmInput  = form.querySelector( '[data-agw-password-confirm]' );
	const submit        = form.querySelector( '[data-agw-password-submit]' );
	const strength      = form.querySelector( '[data-agw-strength]' );
	const label         = form.querySelector( '[data-agw-strength-label]' );
	const requirements  = form.querySelectorAll( '[data-agw-requirement]' );

	if ( ! passwordInput || ! confirmInput || ! submit || ! strength || ! label ) {
		return;
	}

	const password = passwordInput.value;
	const confirm  = confirmInput.value;
	const checks   = alyntAgGetPasswordChecks( password, confirm );
	const coreKeys = [ 'length', 'uppercase', 'lowercase', 'number', 'symbol' ];
	const corePass = coreKeys.filter( ( key ) => checks[ key ] ).length;
	const isValid  = corePass === coreKeys.length && checks.match;
	const score    = password.length === 0 ? 0 : Math.min( 4, Math.max( 1, Math.ceil( ( corePass / coreKeys.length ) * 4 ) ) );
	const messages = {
		empty: strength.dataset.agwMessageEmpty || '',
		weak: strength.dataset.agwMessageWeak || '',
		good: strength.dataset.agwMessageGood || '',
		ready: strength.dataset.agwMessageReady || '',
	};

	for ( const item of requirements ) {
		const key    = item.getAttribute( 'data-agw-requirement' );
		const passed = Boolean( checks[ key ] );

		item.classList.toggle( 'is-met', passed );
		item.setAttribute( 'aria-current', passed ? 'true' : 'false' );
	}

	strength.setAttribute( 'data-agw-strength-score', String( isValid ? 4 : score ) );
	label.textContent = isValid ? messages.ready : messages[ score <= 1 ? 'weak' : 'good' ];

	if ( password.length === 0 ) {
		label.textContent = messages.empty;
	}

	passwordInput.setAttribute( 'aria-invalid', password.length > 0 && corePass !== coreKeys.length ? 'true' : 'false' );
	confirmInput.setAttribute( 'aria-invalid', confirm.length > 0 && ! checks.match ? 'true' : 'false' );
	submit.disabled = ! isValid;
}

function alyntAgInitPasswordPolicy() {
	const forms = document.querySelectorAll( '[data-agw-password-form]' );

	for ( const form of forms ) {
		const update = () => alyntAgUpdatePasswordPolicy( form );

		form.addEventListener( 'input', update );
		form.addEventListener( 'change', update );
		update();
	}
}

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

function alyntAgInitRegistrationForms() {
	const forms = document.querySelectorAll( '[data-agw-registration-form]' );

	for ( const form of forms ) {
		const update = () => alyntAgUpdateRegistrationForm( form );

		form.addEventListener( 'input', update );
		form.addEventListener( 'change', update );
		update();
	}
}

document.addEventListener( 'click', alyntAgTogglePassword );

function alyntAgInitFrontend() {
	alyntAgInitPasswordPolicy();
	alyntAgInitRegistrationForms();
}

if ( document.readyState === 'loading' ) {
	document.addEventListener( 'DOMContentLoaded', alyntAgInitFrontend );
} else {
	alyntAgInitFrontend();
}
