( function( $ ) {
    'use strict';

    // Footer about text
    wp.customize( 'appforge_footer_about', function( value ) {
        value.bind( function( newval ) {
            $( '.footer-brand p' ).text( newval );
        } );
    } );

    // Copyright text
    wp.customize( 'appforge_copyright', function( value ) {
        value.bind( function( newval ) {
            $( '.footer-bottom p' ).text( '© ' + new Date().getFullYear() + ' ' + newval );
        } );
    } );

    // Primary color — live preview via CSS var
    wp.customize( 'appforge_color_primary', function( value ) {
        value.bind( function( newval ) {
            $( '#appforge-live-colors' ).remove();
            $( 'head' ).append(
                '<style id="appforge-live-colors">' +
                '.btn-primary,.btn-download{background:' + newval + '!important}' +
                '.nav-cat-link:hover,.nav-cat-link.active{color:' + newval + '!important;border-bottom-color:' + newval + '!important}' +
                '.apk-widget__header::before,.apk-section-header__title::before{background:' + newval + '!important}' +
                '.app-info-item__icon{color:' + newval + '!important}' +
                '.apk-tag:hover{background:' + newval + '!important}' +
                '</style>'
            );
        } );
    } );

} )( jQuery );
