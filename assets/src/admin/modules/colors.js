/**
 * Color picker and hexadecimal field synchronization.
 *
 * @package Alynt_Account_Gateway
 */

export function alyntAgInitColorControls() {
	document.querySelectorAll( '[data-alynt-ag-color-control]' ).forEach(
		function ( control ) {
			const picker = control.querySelector( '[data-alynt-ag-color-picker]' );
			const text   = control.querySelector( '[data-alynt-ag-color-text]' );
			const hex    = /^#[0-9a-f]{6}$/i;

			if ( ! picker || ! text ) {
				return;
			}

			function updatePicker() {
				const value = text.value.trim();

				if ( hex.test( value ) ) {
					picker.value = value;
					text.removeAttribute( 'aria-invalid' );
					control.classList.remove( 'alynt-ag-color-control--invalid' );
					return;
				}

				text.setAttribute( 'aria-invalid', 'true' );
				control.classList.add( 'alynt-ag-color-control--invalid' );
			}

			function updateText() {
				text.value = picker.value.toUpperCase();
				text.removeAttribute( 'aria-invalid' );
				control.classList.remove( 'alynt-ag-color-control--invalid' );
				text.dispatchEvent( new window.Event( 'input', { bubbles: true } ) );
			}

			function normalizeText() {
				if ( hex.test( text.value.trim() ) ) {
					text.value = text.value.trim().toUpperCase();
				}

				updatePicker();
			}

			picker.addEventListener( 'input', updateText );
			text.addEventListener( 'input', updatePicker );
			text.addEventListener( 'change', normalizeText );
			updatePicker();
		}
	);
}
