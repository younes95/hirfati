/*  
	Header section same height as the height of the window
*/

jQuery(window).load( header_section_height );
jQuery(window).resize( header_section_height );
function header_section_height() {
    "use strict"
    var windowHeight    = window.innerHeight,
        headerSection   = jQuery( '.overlay-layer-wrap' );
    headerSection.height( 'auto' ).height( Math.max( windowHeight, headerSection.height() ) );
}

var isMobile = {
    Android: function() {
        return navigator.userAgent.match(/Android/i);
    },
    BlackBerry: function() {
        return navigator.userAgent.match(/BlackBerry/i);
    },
    iOS: function() {
        return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    },
    Opera: function() {
        return navigator.userAgent.match(/Opera Mini/i);
    },
    Windows: function() {
        return navigator.userAgent.match(/IEMobile/i);
    },
    any: function() {
        return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
    }
};


jQuery( document ).ready( function($) {

	jQuery( 'h2.header-widget-title' ).click( function() {
		if( isMobile.any() ) {
			var parentElement = $( this ).parent();
			if( parentElement.hasClass( 'mobile-header-cart-open' ) ) {
				parentElement.removeClass( 'mobile-header-cart-open' ); 
			} else {
				parentElement.addClass( 'mobile-header-cart-open' ); 
			}
		}
	} );

} );



/*** CENTERED MENU */
var callback_menu_align = function () {

    "use strict"

    var headerWrap      = jQuery('.header');
    var navWrap         = jQuery('#site-navigation');
    var logoWrap        = jQuery('.responsive-logo');
    var containerWrap   = jQuery('.container');
    var classToAdd      = 'menu-align-center';
    var cartHeaderWrap  = jQuery( '#header-sidebar' );

    if( cartHeaderWrap.length > 0 && jQuery( '.menu-align-center-cart' ).length < 1 ) {
        headerWrap.addClass( 'menu-align-center-cart' );
    }

    if ( headerWrap.hasClass(classToAdd) ) {
        headerWrap.removeClass(classToAdd);
    }
    var logoWidth       = logoWrap.outerWidth();
    var menuWidth       = navWrap.outerWidth();
    var containerWidth  = containerWrap.width();
    var cartHeaderWidth = cartHeaderWrap.outerWidth();
    if ( menuWidth + logoWidth + cartHeaderWidth > containerWidth ) {
        headerWrap.addClass(classToAdd);
    }
    else {
        if ( headerWrap.hasClass(classToAdd) ) {
            headerWrap.removeClass(classToAdd);
        }
    }
}
jQuery(window).load(callback_menu_align);
jQuery(window).resize(callback_menu_align);