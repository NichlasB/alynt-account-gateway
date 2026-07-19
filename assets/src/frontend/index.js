/**
 * Frontend entry point.
 *
 * @package Alynt_Account_Gateway
 */

import './style.css';
import { alyntAgTogglePassword, alyntAgInitPasswordPolicy } from './modules/password.js';
import { alyntAgInitRegistrationForms } from './modules/registration.js';
import { alyntAgPrepareTurnstileWidgets } from './modules/turnstile.js';
import { alyntAgHandleOffcanvasClick, alyntAgHandleOffcanvasKeydown } from './modules/offcanvas.js';

document.documentElement.classList.add( 'alynt-ag-frontend-ready' );
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
