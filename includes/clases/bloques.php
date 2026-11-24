<?php
/**
 * Blocks for the block editor: the notice, the button, the terms and the GARAN
 * label.
 *
 * Each is a dynamic block that renders on the server with the very functions
 * the shortcodes and placements use, so a block and its shortcode can never
 * drift apart. The editor shows the server's own rendering of them.
 *
 * @package APG_Legal_Guarantee_Notice
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registers the blocks and their editor script.
 *
 * @return void
 */
function apg_guarantee_register_blocks() {
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	wp_register_script(
		'apg-legal-guarantee-notice-blocks',
		plugins_url( 'assets/js/bloques.js', apg_guarantee_DIRECCION ),
		array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-server-side-render' ),
		apg_guarantee_VERSION,
		true
	);

	wp_localize_script(
		'apg-legal-guarantee-notice-blocks',
		'apgGuaranteeBlocks',
		array(
			'label'    => __( 'Wording', 'apg-legal-guarantee-notice' ),
			'labelTip' => __( 'Leave it empty to use the wording from the settings.', 'apg-legal-guarantee-notice' ),
			'shows'    => __( 'Shows', 'apg-legal-guarantee-notice' ),
			'styles'   => array(
				array(
					'value' => 'text',
					'label' => __( 'Text only (recommended)', 'apg-legal-guarantee-notice' ),
				),
				array(
					'value' => 'icon_text',
					'label' => __( 'Icon and text', 'apg-legal-guarantee-notice' ),
				),
				array(
					'value' => 'icon',
					'label' => __( 'Icon only', 'apg-legal-guarantee-notice' ),
				),
			),
			'mode'     => __( 'Form', 'apg-legal-guarantee-notice' ),
			'modes'    => array(
				array(
					'value' => 'label',
					'label' => __( 'Full label', 'apg-legal-guarantee-notice' ),
				),
				array(
					'value' => 'nested',
					'label' => __( 'Nested label, opens the full one', 'apg-legal-guarantee-notice' ),
				),
			),
			'empty'    => __( 'The EU GARAN label of the product appears here when the product has one.', 'apg-legal-guarantee-notice' ),
			'garan'    => apg_guarantee_con_woocommerce(),
		)
	);

	$common = array(
		'api_version'   => 2,
		'category'      => 'widgets',
		'icon'          => 'shield',
		'editor_script' => 'apg-legal-guarantee-notice-blocks',
		'supports'      => array( 'html' => false ),
	);

	register_block_type(
		'apg-legal-guarantee-notice/notice',
		array_merge(
			$common,
			array(
				'title'           => __( 'Legal guarantee notice', 'apg-legal-guarantee-notice' ),
				'description'     => __( 'The official EU notice on the legal guarantee of conformity, at full size, in the visitor\'s language.', 'apg-legal-guarantee-notice' ),
				'keywords'        => array( __( 'guarantee', 'apg-legal-guarantee-notice' ), 'EU' ),
				'render_callback' => 'apg_guarantee_block_notice',
			)
		)
	);

	register_block_type(
		'apg-legal-guarantee-notice/button',
		array_merge(
			$common,
			array(
				'title'           => __( 'Legal guarantee button', 'apg-legal-guarantee-notice' ),
				'description'     => __( 'The sentence that opens the official notice on the first click.', 'apg-legal-guarantee-notice' ),
				'keywords'        => array( __( 'guarantee', 'apg-legal-guarantee-notice' ), 'EU' ),
				'attributes'      => array(
					'label'       => array(
						'type'    => 'string',
						'default' => '',
					),
					'buttonStyle' => array(
						'type'    => 'string',
						'default' => 'text',
					),
				),
				'render_callback' => 'apg_guarantee_block_button',
			)
		)
	);

	register_block_type(
		'apg-legal-guarantee-notice/terms',
		array_merge(
			$common,
			array(
				'title'           => __( 'Guarantee terms', 'apg-legal-guarantee-notice' ),
				'description'     => __( 'Your own guarantee terms, from the plugin settings.', 'apg-legal-guarantee-notice' ),
				'keywords'        => array( __( 'guarantee', 'apg-legal-guarantee-notice' ) ),
				'render_callback' => 'apg_guarantee_block_terms',
			)
		)
	);

	if ( apg_guarantee_con_woocommerce() ) {
		register_block_type(
			'apg-legal-guarantee-notice/garan',
			array_merge(
				$common,
				array(
					'title'           => __( 'EU GARAN label', 'apg-legal-guarantee-notice' ),
					'description'     => __( 'The EU GARAN label of the product, for placing it somewhere else in the product template.', 'apg-legal-guarantee-notice' ),
					'keywords'        => array( 'GARAN', __( 'guarantee', 'apg-legal-guarantee-notice' ) ),
					'icon'            => 'awards',
					'attributes'      => array(
						'mode' => array(
							'type'    => 'string',
							'default' => 'label',
						),
					),
					'uses_context'    => array( 'postId' ),
					'render_callback' => 'apg_guarantee_block_garan',
				)
			)
		);
	}
}
add_action( 'init', 'apg_guarantee_register_blocks' );

/**
 * Renders the notice block.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function apg_guarantee_block_notice( $attributes ) {
	return apg_guarantee_notice_shortcode( array( 'class' => isset( $attributes['className'] ) ? (string) $attributes['className'] : '' ) );
}

/**
 * Renders the button block.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function apg_guarantee_block_button( $attributes ) {
	return apg_guarantee_button_shortcode(
		array(
			'label' => isset( $attributes['label'] ) ? (string) $attributes['label'] : '',
			'style' => isset( $attributes['buttonStyle'] ) ? (string) $attributes['buttonStyle'] : 'text',
			'class' => isset( $attributes['className'] ) ? (string) $attributes['className'] : '',
		)
	);
}

/**
 * Renders the terms block.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function apg_guarantee_block_terms( $attributes ) {
	return apg_guarantee_terms_shortcode( array( 'class' => isset( $attributes['className'] ) ? (string) $attributes['className'] : '' ) );
}

/**
 * Renders the GARAN block for the product it sits in.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Inner content, unused.
 * @param WP_Block $block      Block instance, for the product in context.
 * @return string
 */
function apg_guarantee_block_garan( $attributes, $content = '', $block = null ) {
	$id = $block instanceof WP_Block && ! empty( $block->context['postId'] ) ? absint( $block->context['postId'] ) : get_the_ID();

	$product = $id && function_exists( 'wc_get_product' ) ? wc_get_product( $id ) : null;
	$data    = $product ? apg_guarantee_garan_data( $product ) : null;

	if ( ! $data ) {
		return '';
	}

	$mode = isset( $attributes['mode'] ) && 'nested' === $attributes['mode'] ? 'nested' : 'label';

	return '<div class="apg-garan-block">' . apg_guarantee_garan_markup( $data, $mode, 'block' ) . '</div>';
}
