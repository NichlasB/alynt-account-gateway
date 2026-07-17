/**
 * Frontend entry point.
 *
 * @package Alynt_Account_Gateway
 */

import './style.css';

document.documentElement.classList.add( 'alynt-ag-frontend-ready' );

const alyntAgLabels = window.alyntAgFrontend && window.alyntAgFrontend.labels ? window.alyntAgFrontend.labels : {};

let alyntAgLastOffcanvasTrigger = null;

function alyntAgTogglePassword( event ) {
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
	const label      = shouldShow ? alyntAgLabels.hidePassword || 'Hide password' : alyntAgLabels.showPassword || 'Show password';
	const statusText = shouldShow ? alyntAgLabels.passwordVisible || 'Password is visible.' : alyntAgLabels.passwordHidden || 'Password is hidden.';

	input.type         = shouldShow ? 'text' : 'password';
	toggle.textContent = shouldShow ? alyntAgLabels.hide || 'Hide' : alyntAgLabels.show || 'Show';
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
		const requirementState = passed ? 'Met' : 'Not met';

		item.classList.toggle( 'is-met', passed );
		item.setAttribute( 'aria-label', `${ requirementState }: ${ requirementLabel }` );
	}

	strength.setAttribute( 'data-agw-strength-score', String( isValid ? 4 : score ) );
	label.textContent = isValid ? messages.ready : messages[ score <= 1 ? 'weak' : 'good' ];

	if ( password.length === 0 ) {
		label.textContent = messages.empty;
	} else {
		label.textContent = `${ label.textContent } ${ metRequirements } of ${ totalRequirements } requirements met.`;
	}

	passwordInput.setAttribute( 'aria-invalid', password.length > 0 && corePass !== coreKeys.length ? 'true' : 'false' );
	confirmInput.setAttribute( 'aria-invalid', confirm.length > 0 && ! checks.match ? 'true' : 'false' );
	submit.disabled = ! isValid;
	submit.setAttribute( 'aria-disabled', isValid ? 'false' : 'true' );
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

function alyntAgPrepareTurnstileWidgets() {
	const widgets = document.querySelectorAll( '[data-agw-turnstile-widget]' );

	for ( const widget of widgets ) {
		const slot      = widget.closest( '.agw-verification-slot' );
		const slotWidth = slot ? slot.getBoundingClientRect().width : widget.getBoundingClientRect().width;

		widget.setAttribute( 'data-size', slotWidth > 0 && slotWidth < 300 ? 'compact' : 'normal' );
	}
}

function alyntAgFocusableElements( container ) {
	return Array.from(
		container.querySelectorAll(
			'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
		)
	).filter( ( element ) => element.offsetParent !== null || element === document.activeElement );
}

function alyntAgOpenOffcanvas( trigger ) {
	const controlledId = trigger.getAttribute( 'aria-controls' );
	const offcanvas    = controlledId ? document.getElementById( controlledId ) : document.querySelector( '[data-agw-offcanvas]' );
	const panel        = offcanvas ? offcanvas.querySelector( '[data-agw-offcanvas-panel]' ) : null;

	if ( ! offcanvas || ! panel ) {
		return;
	}

	alyntAgLastOffcanvasTrigger = trigger;
	offcanvas.classList.add( 'is-open' );
	offcanvas.setAttribute( 'aria-hidden', 'false' );
	trigger.setAttribute( 'aria-expanded', 'true' );
	document.documentElement.classList.add( 'agw-offcanvas-open' );
	document.documentElement.classList.toggle( 'agw-has-admin-bar', Boolean( document.getElementById( 'wpadminbar' ) ) );

	const focusPanel    = () => {
		const focusable = alyntAgFocusableElements( panel );
		( focusable[0] || panel ).focus();
	};

	window.setTimeout( focusPanel, 0 );
}

function alyntAgCloseOffcanvas( offcanvas ) {
	const panel = offcanvas ? offcanvas.querySelector( '[data-agw-offcanvas-panel]' ) : null;

	if ( ! offcanvas || ! panel ) {
		return;
	}

	offcanvas.classList.remove( 'is-open' );
	offcanvas.setAttribute( 'aria-hidden', 'true' );
	document.documentElement.classList.remove( 'agw-offcanvas-open' );

	if ( alyntAgLastOffcanvasTrigger ) {
		alyntAgLastOffcanvasTrigger.setAttribute( 'aria-expanded', 'false' );
		alyntAgLastOffcanvasTrigger.focus();
	}

	alyntAgLastOffcanvasTrigger = null;
}

function alyntAgToggleOffcanvasSubmenu( toggle ) {
	const menuItem  = toggle.closest( '.agw-offcanvas__menu-item' );
	const submenuId = toggle.getAttribute( 'aria-controls' );
	const submenu   = submenuId ? document.getElementById( submenuId ) : null;
	const isOpen    = toggle.getAttribute( 'aria-expanded' ) === 'true';

	if ( ! menuItem || ! submenu ) {
		return;
	}

	toggle.setAttribute( 'aria-expanded', isOpen ? 'false' : 'true' );
	toggle.setAttribute(
		'aria-label',
		isOpen ?
			toggle.dataset.agwExpandLabel || toggle.getAttribute( 'aria-label' ) || '' :
			toggle.dataset.agwCollapseLabel || toggle.getAttribute( 'aria-label' ) || ''
	);
	submenu.hidden = isOpen;
	menuItem.classList.toggle( 'is-open', ! isOpen );
}

function alyntAgHandleOffcanvasClick( event ) {
	const openTrigger = event.target.closest( '[data-agw-offcanvas-open]' );

	if ( openTrigger ) {
		event.preventDefault();
		alyntAgOpenOffcanvas( openTrigger );
		return;
	}

	const submenuToggle = event.target.closest( '[data-agw-submenu-toggle]' );

	if ( submenuToggle ) {
		event.preventDefault();
		alyntAgToggleOffcanvasSubmenu( submenuToggle );
		return;
	}

	const closeTrigger = event.target.closest( '[data-agw-offcanvas-close]' );

	if ( closeTrigger ) {
		event.preventDefault();
		alyntAgCloseOffcanvas( closeTrigger.closest( '[data-agw-offcanvas]' ) );
	}
}

function alyntAgHandleOffcanvasKeydown( event ) {
	const openOffcanvas = document.querySelector( '[data-agw-offcanvas].is-open' );

	if ( ! openOffcanvas ) {
		return;
	}

	if ( event.key === 'Escape' ) {
		event.preventDefault();
		alyntAgCloseOffcanvas( openOffcanvas );
		return;
	}

	if ( event.key !== 'Tab' ) {
		return;
	}

	const panel     = openOffcanvas.querySelector( '[data-agw-offcanvas-panel]' );
	const focusable = panel ? alyntAgFocusableElements( panel ) : [];

	if ( ! panel ) {
		return;
	}

	if ( ! focusable.length ) {
		event.preventDefault();
		panel.focus();
		return;
	}

	const first = focusable[0];
	const last  = focusable[ focusable.length - 1 ];

	if ( event.shiftKey && document.activeElement === first ) {
		event.preventDefault();
		last.focus();
	} else if ( ! event.shiftKey && document.activeElement === last ) {
		event.preventDefault();
		first.focus();
	}
}

document.addEventListener( 'click', alyntAgTogglePassword );
document.addEventListener( 'click', alyntAgHandleOffcanvasClick );
document.addEventListener( 'keydown', alyntAgHandleOffcanvasKeydown );

function alyntAgInitFrontend() {
	alyntAgPrepareTurnstileWidgets();
	alyntAgInitPasswordPolicy();
	alyntAgInitRegistrationForms();
}

if ( document.readyState === 'loading' ) {
	document.addEventListener( 'DOMContentLoaded', alyntAgInitFrontend );
} else {
	alyntAgInitFrontend();
}
