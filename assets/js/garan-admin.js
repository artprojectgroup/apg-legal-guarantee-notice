/**
 * Product screen: picks the EU GARAN label the producer handed over.
 *
 * The media library's own picker, so the file goes through WordPress's upload
 * checks like any other image. Only the attachment ID is kept in the form; the
 * preview is the library's own thumbnail.
 *
 * @package APG_Legal_Guarantee_Notice
 */
( function ( $ ) {
	'use strict';

	$( function () {
		var field   = $( '#_apg_garan_image' );
		var preview = $( '.apg-garan-image-preview' );
		var remove  = $( '.apg-garan-image-remove' );
		var frame;

		if ( ! field.length || typeof wp === 'undefined' || ! wp.media ) {
			return;
		}

		$( '.apg-garan-image-choose' ).on( 'click', function ( event ) {
			event.preventDefault();

			if ( ! frame ) {
				frame = wp.media( {
					title: window.apgGaranAdmin ? window.apgGaranAdmin.title : '',
					button: { text: window.apgGaranAdmin ? window.apgGaranAdmin.button : '' },
					library: { type: 'image' },
					multiple: false
				} );

				frame.on( 'select', function () {
					var attachment = frame.state().get( 'selection' ).first().toJSON();
					var sizes      = attachment.sizes || {};
					var thumb      = sizes.thumbnail ? sizes.thumbnail.url : attachment.url;
					var img        = document.createElement( 'img' );

					// Built as a node with properties, never as an HTML string,
					// so nothing in the attachment's data can become markup.
					img.src    = thumb;
					img.alt    = attachment.alt || '';
					img.width  = 150;

					field.val( String( parseInt( attachment.id, 10 ) || '' ) );
					preview.empty().append( img );
					remove.prop( 'hidden', false );
				} );
			}

			frame.open();
		} );

		remove.on( 'click', function ( event ) {
			event.preventDefault();
			field.val( '' );
			preview.empty();
			remove.prop( 'hidden', true );
		} );
	} );
}( jQuery ) );
