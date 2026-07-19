/**
 * Turnstile widget preparation.
 *
 * @package Alynt_Account_Gateway
 */

export function alyntAgPrepareTurnstileWidgets() {
	const widgets = document.querySelectorAll( '[data-agw-turnstile-widget]' );

	for ( const widget of widgets ) {
		const slot      = widget.closest( '.agw-verification-slot' );
		const slotWidth = slot ? slot.getBoundingClientRect().width : widget.getBoundingClientRect().width;

		widget.setAttribute( 'data-size', slotWidth > 0 && slotWidth < 300 ? 'compact' : 'normal' );
	}
}
