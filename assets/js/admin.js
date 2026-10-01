/**
 * Settings screen: shows the colour pickers only for the look that uses them.
 *
 * Progressive enhancement. Without this the pickers are simply always visible
 * and the screen still works; nothing here is needed to save a setting.
 *
 * @package APG_Legal_Guarantee_Notice
 */
( function () {
	'use strict';

	function sync( select ) {
		var custom = document.getElementById( 'apg_guarantee_' + select.dataset.placement + '_custom' );

		if ( custom ) {
			custom.hidden = 'custom' !== select.value;
		}
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		var selects = document.querySelectorAll( '.apg-guarantee-look' );

		Array.prototype.forEach.call( selects, function ( select ) {
			sync( select );
			select.addEventListener( 'change', function () {
				sync( select );
			} );
		} );
	} );
}() );
