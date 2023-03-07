/**
 * Sticky Header
 * Adds a class to header on scroll
 */

jQuery( document ).on( 'scroll', function() {
	if ( jQuery( document ).scrollTop() > 0 ) {
		jQuery( 'header, body' ).addClass( 'shrink' );
	} else {
		jQuery( 'header, body' ).removeClass( 'shrink' );
	}
} );
// var offsetheight = ;

/**
 * Document Ready Function
 * Triggered when document get's ready
 */
jQuery( document ).ready( function( jQuery ) {
	jQuery( '.menu-btn' ).click( function() {
		jQuery( this ).toggleClass( 'active' );
		jQuery( '.nav-overlay' ).toggleClass( 'open' );
		jQuery( 'html, body' ).toggleClass( 'no-overflow' );
		jQuery( '.header-nav ul li.active' ).removeClass( 'active' );
		jQuery( '.header-nav ul.sub-menu' ).slideUp();
	} );
	jQuery.noConflict();

	/**
	 * Add span tag to multi-level accordion menu for mobile menus
	 */
	jQuery( 'li' ).each( function() {
		if ( jQuery( this ).hasClass( 'menu-item-has-children' ) ) {
			jQuery( this ).prepend( '<span class="submenu-icon"></span>' );
		}
	} );

	/**
	 * Slide Up/Down internal sub-menu when mobile menu arrow clicked
	 */
	jQuery( '.header-nav .submenu-icon' ).click( function() {
		const link = jQuery( this );
		const closestUl = link.closest( 'ul' );
		const parallelActiveLinks = closestUl.find( '.active' );
		const closestLi = link.closest( 'li' );
		const linkStatus = closestLi.hasClass( 'active' );
		let count = 0;

		closestUl.find( 'ul' ).slideUp( function() {
			if ( ++count === closestUl.find( 'ul' ).length ) {
				parallelActiveLinks.removeClass( 'active' );
			}
		} );

		if ( ! linkStatus ) {
			closestLi.children( 'ul' ).slideDown();
			closestLi.addClass( 'active' );
		}
	} );

	/**
	 * Header Profile
	 */

	const profilePicture = jQuery( 'body' ).find( '#profile__picture' );

	profilePicture.on( 'click', function() {
		jQuery( '.profile-menu' ).toggleClass( 'active--menu' );
	} );

	/**
	 * Header Dropdown
	 */

	jQuery( '.header__content--dropdown' ).on( 'click', function() {
		jQuery( '.header-dropdown--box' ).toggleClass( 'header-dropdown--box--active' );
	} );

	 jQuery( document ).mouseup( function( e ) {
		const container = jQuery( '#profile__picture,.header__content--dropdown' );
		// if the target of the click isn't the container nor a descendant of the container
		if ( ! container.is( e.target ) && container.has( e.target ).length === 0 ) {
			jQuery( '.profile-menu' ).removeClass( 'active--menu' );
			jQuery( '.header-dropdown--box' ).removeClass( 'header-dropdown--box--active' );
		}
	} );

	/**
	 *
	 *	Header Dropdown
	 */

	jQuery( '.header-dropdown--box ul li' ).on( 'click', function() {
		jQuery( this ).toggleClass( 'checked--item' );
	} );
} );
