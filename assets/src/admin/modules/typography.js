/**
 * Typography preset controls.
 *
 * @package Alynt_Account_Gateway
 */

/**
 * Get the selected typography option.
 *
 * @param {Object} state Typography control state.
 *
 * @return {HTMLOptionElement} Selected option.
 */
function alyntAgSelectedTypographyOption( state ) {
	return state.selector.options[ state.selector.selectedIndex ];
}

function alyntAgUpdateTypographyControl( state ) {
	const option = alyntAgSelectedTypographyOption( state );

	state.previewHeading.style.fontFamily = state.headingInput.value || 'inherit';
	state.previewBody.style.fontFamily    = state.bodyInput.value || 'inherit';
	state.status.textContent              = `${ state.statusPrefix } ${ option.textContent.trim() }`;
}

function alyntAgApplyTypographyPreset( state ) {
	const option = alyntAgSelectedTypographyOption( state );

	if ( option.value !== 'custom' ) {
		state.headingInput.value = option.dataset.heading;
		state.bodyInput.value    = option.dataset.body;
	}

	alyntAgUpdateTypographyControl( state );
}

export function alyntAgInitTypographyPresets() {
	const state = {
		control: document.querySelector( '[data-alynt-ag-typography-presets]' ),
		headingInput: document.querySelector( '#alynt-ag-heading_font_family' ),
		bodyInput: document.querySelector( '#alynt-ag-body_font_family' ),
	};

	if ( ! state.control || ! state.headingInput || ! state.bodyInput ) {
		return;
	}

	state.selector       = state.control.querySelector( '[data-alynt-ag-typography-select]' );
	state.previewHeading = state.control.querySelector( '[data-alynt-ag-typography-heading]' );
	state.previewBody    = state.control.querySelector( '[data-alynt-ag-typography-body]' );
	state.status         = state.control.querySelector( '[data-alynt-ag-typography-status]' );
	state.statusPrefix   = state.control.dataset.statusPrefix || 'Current pairing:';

	const applyPreset        = () => alyntAgApplyTypographyPreset( state );
	const markCustom         = () => {
		state.selector.value = 'custom';
		alyntAgUpdateTypographyControl( state );
	};

	state.selector.addEventListener( 'change', applyPreset );
	state.headingInput.addEventListener( 'input', markCustom );
	state.bodyInput.addEventListener( 'input', markCustom );
	alyntAgUpdateTypographyControl( state );
}
