/**
 * Block editor side of the plugin's blocks.
 *
 * The blocks are registered on the server, which hands their titles,
 * descriptions and attributes to the editor. This file only adds what the
 * editor needs to show them: the server's own rendering, and the two settings
 * the button and the GARAN label have. Nothing is saved in the post content
 * but the block comment, since all four render on the server.
 *
 * Plain JavaScript with the editor's globals, so the plugin needs no build step.
 *
 * @package APG_Legal_Guarantee_Notice
 */
( function ( wp, texts ) {
	'use strict';

	if ( ! wp || ! wp.blocks || ! wp.serverSideRender ) {
		return;
	}

	var el                = wp.element.createElement;
	var ServerSideRender  = wp.serverSideRender;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var useBlockProps     = wp.blockEditor.useBlockProps;
	var PanelBody         = wp.components.PanelBody;
	var TextControl       = wp.components.TextControl;
	var SelectControl     = wp.components.SelectControl;
	var Placeholder       = wp.components.Placeholder;

	texts = texts || {};

	function preview( name, attributes, empty ) {
		return el( ServerSideRender, {
			block: name,
			attributes: attributes,
			EmptyResponsePlaceholder: empty ? function () {
				return el( Placeholder, { label: 'GARAN', instructions: empty } );
			} : undefined
		} );
	}

	function simple( name ) {
		wp.blocks.registerBlockType( name, {
			edit: function ( props ) {
				return el( 'div', useBlockProps(), preview( name, props.attributes ) );
			},
			save: function () {
				return null;
			}
		} );
	}

	simple( 'apg-legal-guarantee-notice/notice' );
	simple( 'apg-legal-guarantee-notice/terms' );

	wp.blocks.registerBlockType( 'apg-legal-guarantee-notice/button', {
		edit: function ( props ) {
			var attributes = props.attributes;

			return el(
				'div',
				useBlockProps(),
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: texts.shows || '' },
						el( TextControl, {
							label: texts.label || '',
							help: texts.labelTip || '',
							value: attributes.label,
							onChange: function ( value ) {
								props.setAttributes( { label: value } );
							}
						} ),
						el( SelectControl, {
							label: texts.shows || '',
							value: attributes.buttonStyle,
							options: texts.styles || [],
							onChange: function ( value ) {
								props.setAttributes( { buttonStyle: value } );
							}
						} )
					)
				),
				preview( 'apg-legal-guarantee-notice/button', attributes )
			);
		},
		save: function () {
			return null;
		}
	} );

	// Registered on the server only when WooCommerce is there.
	if ( texts.garan ) {
		wp.blocks.registerBlockType( 'apg-legal-guarantee-notice/garan', {
			edit: function ( props ) {
				return el(
					'div',
					useBlockProps(),
					el(
						InspectorControls,
						null,
						el(
							PanelBody,
							{ title: 'GARAN' },
							el( SelectControl, {
								label: texts.mode || '',
								value: props.attributes.mode,
								options: texts.modes || [],
								onChange: function ( value ) {
									props.setAttributes( { mode: value } );
								}
							} )
						)
					),
					preview( 'apg-legal-guarantee-notice/garan', props.attributes, texts.empty )
				);
			},
			save: function () {
				return null;
			}
		} );
	}
}( window.wp, window.apgGuaranteeBlocks ) );
