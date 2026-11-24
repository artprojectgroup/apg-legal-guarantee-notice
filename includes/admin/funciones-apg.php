<?php
/**
 * Settings storage, defaults and sanitisation.
 *
 * Every placement carries its own settings: where it goes, whether it shows
 * text or an icon, and how it looks. They live in the same flat option under a
 * prefix of their own rather than in nested arrays, because that keeps the
 * sanitiser one pass long and the form field names readable.
 *
 * Each appearance starts empty, which means "inherit from the theme". A shop
 * that never opens these settings gets a plugin that imposes no colour at all,
 * which is what keeps it readable on a dark theme.
 *
 * @package APG_Legal_Guarantee_Notice
 */

defined( 'ABSPATH' ) || exit;

/**
 * The placements the notice can be opened from.
 *
 * @return array<int,string>
 */
function apg_guarantee_placements() {
	return array( 'float', 'footer', 'menu', 'checkout', 'product', 'cart' );
}

/**
 * How the notice can be shown where it is tied to a purchase.
 *
 * `link` is the sentence that opens the notice on the first click, the pattern
 * the Commission's guidelines illustrate. `full` prints the whole notice where
 * the sentence would be, with nothing to click. The guidelines allow the first;
 * part of the German doctrine reads Article 246a EGBGB as asking for the second
 * at the checkout, so the merchant chooses.
 *
 * @return array<int,string>
 */
function apg_guarantee_display_modes() {
	return array( 'link', 'full' );
}

/**
 * Returns the plugin settings merged with their defaults.
 *
 * The national note ships enabled for a shop whose WordPress runs in Spanish,
 * because that is the only country this plugin currently knows a divergence
 * for. Everywhere else it starts off and the merchant turns it on with their
 * own wording.
 *
 * @return array
 */
function apg_guarantee_get_settings() {
	$defaults = array(
		// The floating button is on out of the box: on its own it satisfies the
		// shop-level obligation, with nothing for the merchant to place.
		'float_position'     => 'bottom-right',
		'footer_enabled'     => '0',
		'menu_id'            => '0',
		'checkout_enabled'   => '1',
		// The link keeps the checkout as it was for every shop updating from an
		// earlier version; the full notice is there for whoever asks for it.
		'checkout_display'   => 'link',
		'product_display'    => 'off',
		'cart_display'       => 'off',
		'show_email'         => '1',
		'goods_only'         => '1',
		'order_note'         => '1',
		'garan_product'      => 'label',
		'invoice_enabled'    => '1',
		'trigger_text'       => '',

		'national_note'      => apg_guarantee_default_national_note_enabled(),
		'national_note_text' => '',
		'terms_text'         => '',
		// WooCommerce's own Terms and conditions page, set under WooCommerce →
		// Settings → Advanced → Page setup. Not the WordPress privacy policy
		// page, which is the data-protection document and says nothing about
		// guarantees.
		'terms_page'         => (string) apg_guarantee_default_terms_page(),
	);

	foreach ( apg_guarantee_placements() as $placement ) {
		$defaults[ $placement . '_style' ]      = 'text';
		$defaults[ $placement . '_look' ]       = 'inherit';
		$defaults[ $placement . '_color' ]            = '';
		$defaults[ $placement . '_background' ]       = '';
		$defaults[ $placement . '_color_hover' ]      = '';
		$defaults[ $placement . '_background_hover' ] = '';
		$defaults[ $placement . '_font_size' ]        = '';
	}

	return wp_parse_args( get_option( 'apg_guarantee_settings', array() ), $defaults );
}

/**
 * The country the shop sells from.
 *
 * Deliberately never guessed from the language: `es` is Spanish, not Spain, and
 * a shop in Mexico or Argentina running WordPress in Spanish is not governed by
 * Spanish consumer law.
 *
 * @return string Two-letter ISO country code, or an empty string when unknown.
 */
function apg_guarantee_shop_country() {
	if ( function_exists( 'wc_get_base_location' ) ) {
		$base = wc_get_base_location();

		if ( ! empty( $base['country'] ) ) {
			return strtoupper( (string) $base['country'] );
		}
	}

	// WooCommerce stores it as `ES` or as `ES:MA`, country first either way.
	$guardado = (string) get_option( 'woocommerce_default_country', '' );

	if ( '' !== $guardado ) {
		return strtoupper( substr( $guardado, 0, 2 ) );
	}

	return '';
}

/**
 * The countries whose own law gives the consumer more than the notice says.
 *
 * The notice states the two-year European minimum and cannot be edited, so a
 * shop in one of these countries has to tell the customer the rest beside it:
 *
 *  - Spain: three years from delivery on new goods, Article 120.1 TRLGDCU.
 *  - Portugal: three years from delivery, Article 12 of Decree-Law 84/2021.
 *  - Sweden: three years from delivery to claim a lack of conformity, under
 *    the Consumer Sales Act (konsumentköplagen, 2022:260).
 *
 * Countries such as Ireland, the Netherlands or Finland tie the period to how
 * long the goods can be expected to last rather than to a number of years, so
 * there is no fixed sentence to write for them; the merchant writes their own.
 *
 * @return array<int,string> Two-letter ISO country codes.
 */
function apg_guarantee_national_note_countries() {
	/**
	 * Filters the countries that get the national note beside the notice.
	 *
	 * @param array $paises Two-letter ISO country codes.
	 */
	return array_map( 'strtoupper', (array) apply_filters( 'apg_guarantee_national_note_countries', array( 'ES', 'PT', 'SE' ) ) );
}

/**
 * Whether the shop sits in a country whose own law grants more than the notice
 * says, and therefore needs the national note beside it.
 *
 * @return bool
 */
function apg_guarantee_national_note_applies() {
	return in_array( apg_guarantee_shop_country(), apg_guarantee_national_note_countries(), true );
}

/**
 * Whether the national note should be on out of the box.
 *
 * @return string '1' or '0'.
 */
function apg_guarantee_default_national_note_enabled() {
	return apg_guarantee_national_note_applies() ? '1' : '0';
}

/**
 * The page the national note links to when the merchant has not picked one.
 *
 * WooCommerce keeps the ID it was given even after the page is deleted, so the
 * setting is only worth offering when the page is really there and readable.
 * Otherwise the link would point at a 404 and the dropdown would show a value
 * it has no option for.
 *
 * @return int Page ID, or 0 when there is no usable page.
 */
function apg_guarantee_default_terms_page() {
	// Cached per request: the settings are read again for every placement, and
	// this asks the database whether the page is still there.
	static $page_id = null;

	if ( null !== $page_id ) {
		return $page_id;
	}

	$page_id = 0;

	if ( ! function_exists( 'wc_get_page_id' ) ) {
		return $page_id;
	}

	$candidate = max( 0, (int) wc_get_page_id( 'terms' ) );

	if ( $candidate && 'publish' === get_post_status( $candidate ) ) {
		$page_id = $candidate;
	}

	return $page_id;
}

/**
 * The note shown beside the notice by default.
 *
 * @return string
 */
function apg_guarantee_default_national_note() {
	switch ( apg_guarantee_shop_country() ) {
		case 'PT':
			return __( 'In Portugal the legal guarantee on goods is three years from delivery, longer than the two-year European minimum stated in the notice.', 'apg-legal-guarantee-notice' );

		case 'SE':
			return __( 'In Sweden you can claim a lack of conformity for three years from delivery, longer than the two-year European minimum stated in the notice.', 'apg-legal-guarantee-notice' );

		default:
			// Spain, and also the fallback for a country added with the filter:
			// it is the wording the plugin has always shipped.
			return __( 'In Spain the legal guarantee on new goods is three years from delivery, longer than the two-year European minimum stated in the notice.', 'apg-legal-guarantee-notice' );
	}
}

/**
 * The starting point for the shop's own guarantee terms.
 *
 * Deliberately a description of what the law already grants, with the places a
 * shop has to fill in left as plain sentences rather than as legal boilerplate:
 * the terms belong to the merchant, who has to read them and adapt them. The
 * settings screen says so next to the field.
 *
 * @return string
 */
function apg_guarantee_default_terms_text() {
	// Same decision as the national note, and for the same reason: what the
	// shop owes its customers follows from where the shop is, not from the
	// language its website happens to be written in.
	$country = apg_guarantee_national_note_applies() ? apg_guarantee_shop_country() : '';

	$lines = array(
		__( 'Every item we sell is covered by the legal guarantee of conformity. If what you receive does not match its description, or does not work as it should, you are entitled to have it repaired or replaced free of charge, and where that is not possible, to a price reduction or a refund.', 'apg-legal-guarantee-notice' ),
	);

	if ( 'ES' === $country ) {
		$lines[] = __( 'The guarantee runs for three years from delivery on new goods, as Article 120.1 of the Spanish consumer act establishes. On second-hand goods the period is the one agreed at the time of sale, and never less than one year.', 'apg-legal-guarantee-notice' );
	} elseif ( '' !== $country ) {
		$lines[] = __( 'You can claim the guarantee for three years from delivery, as the consumer law of this country establishes. On second-hand goods the period may be shorter where the law allows it.', 'apg-legal-guarantee-notice' );
	} else {
		$lines[] = __( 'The guarantee runs for at least two years from delivery. On second-hand goods the period may be shorter, and never less than one year.', 'apg-legal-guarantee-notice' );
	}

	$lines[] = __( 'To claim it, write to us with your order number and a description of the problem, along with proof of purchase such as the receipt, the invoice or a bank statement. We will tell you how to proceed and cover the cost of returning the item when it has to come back to us.', 'apg-legal-guarantee-notice' );
	$lines[] = __( 'This legal guarantee applies on top of any commercial guarantee offered by us or by the producer, and is never replaced by it.', 'apg-legal-guarantee-notice' );

	return implode( "\n\n", $lines );
}

/**
 * The wording of the line that opens the notice.
 *
 * @param string $context Optional. Placement being rendered: `checkout`, `email`,
 *                        `float`, `footer`, `menu`, `shortcode` or `panel`.
 * @return string
 */
function apg_guarantee_trigger_text( $context = '' ) {
	$settings = apg_guarantee_get_settings();
	$text     = trim( (string) ( isset( $settings['trigger_text'] ) ? $settings['trigger_text'] : '' ) );

	if ( '' === $text ) {
		$text = apg_guarantee_default_trigger_text();
	}

	$text = apg_guarantee_translate_string( $text, 'Wording that opens the notice' );

	/**
	 * Filters the line that opens the notice.
	 *
	 * @param string $text    Trigger wording.
	 * @param string $context Placement being rendered; empty when unknown.
	 */
	return (string) apply_filters( 'apg_guarantee_trigger_text', $text, (string) $context );
}

/**
 * Returns the arguments one placement renders its button with.
 *
 * @param string $placement One of {@see apg_guarantee_placements()}.
 * @return array{context:string,style:string,appearance:array}
 */
function apg_guarantee_placement_args( $placement ) {
	$settings = apg_guarantee_get_settings();

	return array(
		'context'    => (string) $placement,
		'style'      => isset( $settings[ $placement . '_style' ] ) ? (string) $settings[ $placement . '_style' ] : 'text',
		'appearance' => array(
			'look'             => isset( $settings[ $placement . '_look' ] ) ? (string) $settings[ $placement . '_look' ] : 'inherit',
			'color'            => isset( $settings[ $placement . '_color' ] ) ? (string) $settings[ $placement . '_color' ] : '',
			'background'       => isset( $settings[ $placement . '_background' ] ) ? (string) $settings[ $placement . '_background' ] : '',
			'color_hover'      => isset( $settings[ $placement . '_color_hover' ] ) ? (string) $settings[ $placement . '_color_hover' ] : '',
			'background_hover' => isset( $settings[ $placement . '_background_hover' ] ) ? (string) $settings[ $placement . '_background_hover' ] : '',
			'font_size'        => isset( $settings[ $placement . '_font_size' ] ) ? (string) $settings[ $placement . '_font_size' ] : '',
		),
	);
}

/**
 * The settings that only mean anything with WooCommerce.
 *
 * The settings screen hides them when WooCommerce is not there, and a hidden
 * field is an absent field when the form is posted. The sanitiser needs the same
 * list so it can tell "the merchant turned this off" from "the merchant never
 * saw it".
 *
 * @return array<int,string>
 */
function apg_guarantee_claves_woocommerce() {
	$claves = array( 'checkout_enabled', 'checkout_display', 'product_display', 'cart_display', 'show_email', 'goods_only', 'order_note', 'garan_product', 'invoice_enabled' );

	foreach ( array( 'checkout', 'product', 'cart' ) as $placement ) {
		foreach ( array( '_style', '_look', '_color', '_background', '_color_hover', '_background_hover', '_font_size' ) as $sufijo ) {
			$claves[] = $placement . $sufijo;
		}
	}

	return $claves;
}

/**
 * Sanitises the settings before they are stored.
 *
 * @param array $options Raw values from the settings form.
 * @return array
 */
function apg_guarantee_sanitiza_opciones( $options ) {
	$options = is_array( $options ) ? $options : array();
	$clean   = array();
	$styles  = array( 'text', 'icon_text', 'icon' );

	$looks = array( 'inherit', 'link', 'button', 'custom' );

	foreach ( apg_guarantee_placements() as $placement ) {
		$style                          = isset( $options[ $placement . '_style' ] ) ? sanitize_key( $options[ $placement . '_style' ] ) : 'text';
		$clean[ $placement . '_style' ] = in_array( $style, $styles, true ) ? $style : 'text';

		$look                          = isset( $options[ $placement . '_look' ] ) ? sanitize_key( $options[ $placement . '_look' ] ) : 'inherit';
		$clean[ $placement . '_look' ] = in_array( $look, $looks, true ) ? $look : 'inherit';

		// The colour inputs always post a value, so the "inherit from the
		// theme" box next to each one is what decides whether anything is
		// stored at all.
		$clean[ $placement . '_color' ] = ! empty( $options[ $placement . '_color_off' ] ) || ! isset( $options[ $placement . '_color' ] )
			? ''
			: (string) sanitize_hex_color( $options[ $placement . '_color' ] );

		$clean[ $placement . '_background' ] = ! empty( $options[ $placement . '_background_off' ] ) || ! isset( $options[ $placement . '_background' ] )
			? ''
			: (string) sanitize_hex_color( $options[ $placement . '_background' ] );

		$clean[ $placement . '_color_hover' ] = ! empty( $options[ $placement . '_color_hover_off' ] ) || ! isset( $options[ $placement . '_color_hover' ] )
			? ''
			: (string) sanitize_hex_color( $options[ $placement . '_color_hover' ] );

		$clean[ $placement . '_background_hover' ] = ! empty( $options[ $placement . '_background_hover_off' ] ) || ! isset( $options[ $placement . '_background_hover' ] )
			? ''
			: (string) sanitize_hex_color( $options[ $placement . '_background_hover' ] );

		// A font size only means something inside a sane range, and an empty
		// value has to stay empty so the button keeps inheriting from the theme.
		$size = isset( $options[ $placement . '_font_size' ] ) ? trim( (string) $options[ $placement . '_font_size' ] ) : '';

		$clean[ $placement . '_font_size' ] = '' === $size ? '' : (string) max( 10, min( 32, absint( $size ) ) );
	}

	$position                = isset( $options['float_position'] ) ? sanitize_key( $options['float_position'] ) : '';
	$clean['float_position'] = in_array( $position, apg_guarantee_float_positions(), true ) ? $position : '';

	$clean['footer_enabled']   = ! empty( $options['footer_enabled'] ) ? '1' : '0';
	$clean['checkout_enabled'] = ! empty( $options['checkout_enabled'] ) ? '1' : '0';
	$clean['show_email']       = ! empty( $options['show_email'] ) ? '1' : '0';
	$clean['national_note']    = ! empty( $options['national_note'] ) ? '1' : '0';
	$clean['goods_only']       = ! empty( $options['goods_only'] ) ? '1' : '0';
	$clean['order_note']       = ! empty( $options['order_note'] ) ? '1' : '0';
	$clean['invoice_enabled']  = ! empty( $options['invoice_enabled'] ) ? '1' : '0';

	$display                   = isset( $options['checkout_display'] ) ? sanitize_key( $options['checkout_display'] ) : 'link';
	$clean['checkout_display'] = in_array( $display, apg_guarantee_display_modes(), true ) ? $display : 'link';

	foreach ( array( 'product', 'cart' ) as $placement ) {
		$display                           = isset( $options[ $placement . '_display' ] ) ? sanitize_key( $options[ $placement . '_display' ] ) : 'off';
		$clean[ $placement . '_display' ] = in_array( $display, apg_guarantee_display_modes(), true ) ? $display : 'off';
	}

	$garan                  = isset( $options['garan_product'] ) ? sanitize_key( $options['garan_product'] ) : 'label';
	$clean['garan_product'] = in_array( $garan, array( 'label', 'nested' ), true ) ? $garan : 'label';

	$clean['menu_id']            = isset( $options['menu_id'] ) ? (string) absint( $options['menu_id'] ) : '0';
	$clean['trigger_text']       = isset( $options['trigger_text'] ) ? sanitize_text_field( $options['trigger_text'] ) : '';
	$clean['national_note_text'] = isset( $options['national_note_text'] ) ? sanitize_textarea_field( $options['national_note_text'] ) : '';
	$clean['terms_text']         = isset( $options['terms_text'] ) ? wp_kses_post( $options['terms_text'] ) : '';
	$clean['terms_page']         = isset( $options['terms_page'] ) ? (string) absint( $options['terms_page'] ) : '0';

	/*
	 * Without WooCommerce the checkout and order-email settings are not on the
	 * screen, so they are not in the post either. Falling through to the
	 * defaults would read that silence as "switch it off" and quietly wipe what
	 * the shop had configured, so the stored values are carried over instead and
	 * come back the day WooCommerce does.
	 */
	if ( ! apg_guarantee_con_woocommerce() ) {
		$previos = (array) get_option( 'apg_guarantee_settings', array() );

		foreach ( apg_guarantee_claves_woocommerce() as $clave ) {
			if ( isset( $previos[ $clave ] ) ) {
				$clean[ $clave ] = $previos[ $clave ];
			}
		}
	}

	return $clean;
}
