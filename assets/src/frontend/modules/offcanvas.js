/**
 * Dashboard off-canvas navigation.
 *
 * @package Alynt_Account_Gateway
 */

let alyntAgLastOffcanvasTrigger = null;

function alyntAgSetOffcanvasSiblingsInert( offcanvas, isInert ) {
	const container = offcanvas.parentElement;

	if ( ! container ) {
		return;
	}

	for ( const sibling of container.children ) {
		if ( sibling === offcanvas ) {
			continue;
		}

		if ( isInert ) {
			if ( ! sibling.hasAttribute( 'inert' ) ) {
				sibling.setAttribute( 'inert', '' );
				sibling.setAttribute( 'data-agw-offcanvas-inert', '' );
			}
		} else if ( sibling.hasAttribute( 'data-agw-offcanvas-inert' ) ) {
			sibling.removeAttribute( 'inert' );
			sibling.removeAttribute( 'data-agw-offcanvas-inert' );
		}
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
	alyntAgSetOffcanvasSiblingsInert( offcanvas, true );

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
	alyntAgSetOffcanvasSiblingsInert( offcanvas, false );

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

export function alyntAgHandleOffcanvasClick( event ) {
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

export function alyntAgHandleOffcanvasKeydown( event ) {
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
