/**
 * WordPress media-library controls.
 *
 * @package Alynt_Account_Gateway
 */

/**
 * Open a WordPress media frame for an image field.
 *
 * @param {HTMLElement} field Media field wrapper.
 * @return {void}
 */
function alyntAgOpenMediaFrame( field ) {
	const input        = field.querySelector( '[data-alynt-ag-media-input]' );
	const preview      = field.querySelector( '[data-alynt-ag-media-preview]' );
	const removeButton = field.querySelector( '[data-alynt-ag-media-remove]' );
	const labels       = window.alyntAgAdmin || {};
	const frame        = window.wp.media(
		{
			title: labels.selectImage || 'Select Image',
			button: {
				text: labels.useImage || 'Use Image',
			},
			multiple: false,
		}
	);

	frame.on(
		'select',
		function () {
			const attachment = frame.state().get( 'selection' ).first().toJSON();
			const medium     = attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium : null;
			const imageUrl   = medium ? medium.url : attachment.url;
			const image      = document.createElement( 'img' );

			image.src = imageUrl;
			image.alt = '';

			input.value = attachment.id;
			preview.replaceChildren( image );
			removeButton.disabled = false;
		}
	);

	frame.open();
}

export function alyntAgHandleMediaClick( event ) {
	const selectButton = event.target.closest( '[data-alynt-ag-media-select]' );
	const removeButton = event.target.closest( '[data-alynt-ag-media-remove]' );

	if ( selectButton ) {
		alyntAgOpenMediaFrame( selectButton.closest( '[data-alynt-ag-media-field]' ) );
	}

	if ( removeButton ) {
		const field   = removeButton.closest( '[data-alynt-ag-media-field]' );
		const input   = field.querySelector( '[data-alynt-ag-media-input]' );
		const preview = field.querySelector( '[data-alynt-ag-media-preview]' );

		input.value = '0';
		preview.replaceChildren();
		removeButton.disabled = true;
	}
}
