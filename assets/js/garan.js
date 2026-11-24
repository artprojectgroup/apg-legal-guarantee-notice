/**
 * Product page: shows the EU GARAN label of the variation the customer picks.
 *
 * The markup arrives already built and escaped by the server, in the variation
 * data WooCommerce hands to its own script, so nothing here builds HTML out of
 * values. Without this file the product's own label stays in place, which is
 * still correct for every variation that inherits it.
 *
 * @package APG_Legal_Guarantee_Notice
 */
( function ( $ ) {
	'use strict';

	$( function () {
		$( '.variations_form' ).each( function () {
			var form      = $( this );
			var productId = String( form.data( 'product_id' ) || '' );
			var box       = $( '.apg-garan-product[data-apg-garan-product="' + productId + '"]' );

			if ( ! box.length ) {
				return;
			}

			var original = box.html();

			form.on( 'found_variation', function ( event, variation ) {
				// A variation inherits the product's label field by field on the
				// server, so an empty answer means this one really has none.
				box.html( variation && typeof variation.apg_garan_html === 'string' ? variation.apg_garan_html : original );
			} );

			form.on( 'reset_data', function () {
				box.html( original );
			} );
		} );
	} );
}( jQuery ) );
