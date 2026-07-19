/**
 * Typography preset controls.
 *
 * @package Alynt_Account_Gateway
 */

export function alyntAgInitTypographyPresets() {
	const control      = document.querySelector( '[data-alynt-ag-typography-presets]' );
	const headingInput = document.querySelector( '#alynt-ag-heading_font_family' );
	const bodyInput    = document.querySelector( '#alynt-ag-body_font_family' );

	if ( ! control || ! headingInput || ! bodyInput ) {
		return;
	}

	const selector       = control.querySelector( '[data-alynt-ag-typography-select]' );
	const previewHeading = control.querySelector( '[data-alynt-ag-typography-heading]' );
	const previewBody    = control.querySelector( '[data-alynt-ag-typography-body]' );
	const status         = control.querySelector( '[data-alynt-ag-typography-status]' );
	const statusPrefix   = control.dataset.statusPrefix || 'Current pairing:';

	function selectedOption() {
		return selector.options[ selector.selectedIndex ];
	}

	function updateStatus() {
		status.textContent = `${ statusPrefix } ${ selectedOption().textContent.trim() }`;
	}

	function updatePreview() {
		previewHeading.style.fontFamily = headingInput.value || 'inherit';
		previewBody.style.fontFamily    = bodyInput.value || 'inherit';
	}

	function applyPreset() {
		const option = selectedOption();

		if ( option.value !== 'custom' ) {
			headingInput.value = option.dataset.heading;
			bodyInput.value    = option.dataset.body;
		}

		updatePreview();
		updateStatus();
	}

	function markCustom() {
		selector.value = 'custom';
		updatePreview();
		updateStatus();
	}

	selector.addEventListener( 'change', applyPreset );
	headingInput.addEventListener( 'input', markCustom );
	bodyInput.addEventListener( 'input', markCustom );
	updatePreview();
}
