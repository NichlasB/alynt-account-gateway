/**
 * Password visibility and policy controls.
 *
 * @package Alynt_Account_Gateway
 */

import { alyntAgLabels } from './labels.js';

export function alyntAgTogglePassword( event ) {
	const toggle = event.target.closest( '[data-agw-password-toggle]' );

	if ( ! toggle ) {
		return;
	}

	const controlledId = toggle.getAttribute( 'aria-controls' );
	const controlled   = controlledId ? document.getElementById( controlledId ) : null;
	const wrapper      = toggle.closest( '.agw-password' );
	const input        = controlled || ( wrapper ? wrapper.querySelector( 'input' ) : null );
	const status       = wrapper ? wrapper.querySelector( '[data-agw-password-visibility-status]' ) : null;

	if ( ! input ) {
		return;
	}

	const shouldShow = input.type === 'password';
	const label      = shouldShow ? alyntAgLabels.hidePassword || '' : alyntAgLabels.showPassword || '';
	const statusText = shouldShow ? alyntAgLabels.passwordVisible || '' : alyntAgLabels.passwordHidden || '';

	input.type         = shouldShow ? 'text' : 'password';
	toggle.textContent = shouldShow ? alyntAgLabels.hide || '' : alyntAgLabels.show || '';
	toggle.setAttribute( 'aria-label', label );
	toggle.setAttribute( 'aria-pressed', shouldShow ? 'true' : 'false' );

	if ( status ) {
		status.textContent = statusText;
	}
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

	const password          = passwordInput.value;
	const confirm           = confirmInput.value;
	const checks            = alyntAgGetPasswordChecks( password, confirm );
	const coreKeys          = [ 'length', 'uppercase', 'lowercase', 'number', 'symbol' ];
	const corePass          = coreKeys.filter( ( key ) => checks[ key ] ).length;
	const totalRequirements = coreKeys.length + 1;
	const metRequirements   = corePass + ( checks.match ? 1 : 0 );
	const isValid           = corePass === coreKeys.length && checks.match;
	const score             = password.length === 0 ? 0 : Math.min( 4, Math.max( 1, Math.ceil( ( corePass / coreKeys.length ) * 4 ) ) );
	const messages          = {
		empty: strength.dataset.agwMessageEmpty || '',
		weak: strength.dataset.agwMessageWeak || '',
		good: strength.dataset.agwMessageGood || '',
		ready: strength.dataset.agwMessageReady || '',
	};

	for ( const item of requirements ) {
		const key              = item.getAttribute( 'data-agw-requirement' );
		const passed           = Boolean( checks[ key ] );
		const requirementLabel = item.getAttribute( 'data-agw-requirement-label' ) || item.textContent.trim();
		const requirementState = passed ? alyntAgLabels.requirementMet || '' : alyntAgLabels.requirementNotMet || '';

		item.classList.toggle( 'is-met', passed );
		item.setAttribute( 'aria-label', `${ requirementState }: ${ requirementLabel }` );
	}

	strength.setAttribute( 'data-agw-strength-score', String( isValid ? 4 : score ) );
	label.textContent = isValid ? messages.ready : messages[ score <= 1 ? 'weak' : 'good' ];

	if ( password.length === 0 ) {
		label.textContent = messages.empty;
	} else {
		const requirementsSummary = ( alyntAgLabels.requirementsMet || '' )
			.replace( '%1$d', String( metRequirements ) )
			.replace( '%2$d', String( totalRequirements ) );
		label.textContent         = `${ label.textContent } ${ requirementsSummary }`.trim();
	}

	passwordInput.setAttribute( 'aria-invalid', password.length > 0 && corePass !== coreKeys.length ? 'true' : 'false' );
	confirmInput.setAttribute( 'aria-invalid', confirm.length > 0 && ! checks.match ? 'true' : 'false' );
	submit.disabled = ! isValid;
	submit.setAttribute( 'aria-disabled', isValid ? 'false' : 'true' );
}

export function alyntAgInitPasswordPolicy() {
	const forms = document.querySelectorAll( '[data-agw-password-form]' );

	for ( const form of forms ) {
		const update = () => alyntAgUpdatePasswordPolicy( form );

		form.addEventListener( 'input', update );
		form.addEventListener( 'change', update );
		update();
	}
}
