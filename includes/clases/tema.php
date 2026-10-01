<?php
/**
 * Reads the theme's own colours so the button can look like the rest of the site.
 *
 * There are two ways to be native, and the plugin uses whichever fits:
 *
 *  - For the "button" look it hands the element the theme's own button classes
 *    and gets out of the way. Nothing is read and nothing is copied: the theme
 *    paints it, hover and focus included, exactly as it paints its own buttons.
 *  - For the "link" look there is no class to borrow, because a link is styled
 *    by its element and this is a `<button>`. So the colours are read from the
 *    theme, which a block theme answers properly through `theme.json`, and a
 *    classic theme not at all — and then the button simply inherits, which on a
 *    classic theme is the closest thing to native there is.
 *
 * @package APG_Legal_Guarantee_Notice
 */

defined( 'ABSPATH' ) || exit;

/**
 * Returns the classes the active theme uses for its own buttons.
 *
 * `wp-element-button` is what a block theme styles from `theme.json`, and
 * `button` is what a classic theme and WooCommerce style. Handing over both
 * covers either kind of theme without the plugin choosing a single colour.
 *
 * @return string
 */
function apg_guarantee_theme_button_classes() {
	$classes = array( 'button' );

	if ( function_exists( 'wc_wp_theme_get_element_class_name' ) ) {
		$theme = wc_wp_theme_get_element_class_name( 'button' );

		if ( $theme ) {
			$classes[] = $theme;
		}
	} elseif ( function_exists( 'wp_theme_get_element_class_name' ) ) {
		$theme = wp_theme_get_element_class_name( 'button' );

		if ( $theme ) {
			$classes[] = $theme;
		}
	}

	/**
	 * Filters the classes that make the button look like the theme's buttons.
	 *
	 * @param array $classes Class names.
	 */
	return implode( ' ', array_unique( (array) apply_filters( 'apg_guarantee_theme_button_classes', $classes ) ) );
}

/**
 * Reads one colour out of the theme's global styles.
 *
 * Block themes answer this from `theme.json`. Classic themes have no such
 * thing and return nothing, which is the honest answer: there is no API that
 * knows what colour a classic theme paints its links.
 *
 * @param array $path Path inside the global styles, e.g. array( 'elements', 'link', 'color' ).
 * @param string $key Key to read at the end of the path, e.g. 'text'.
 * @return string Colour as CSS, or empty string.
 */
function apg_guarantee_global_style( $path, $key ) {
	if ( ! function_exists( 'wp_get_global_styles' ) ) {
		return '';
	}

	$styles = wp_get_global_styles( $path, array( 'transforms' => array( 'resolve-variables' ) ) );

	if ( ! is_array( $styles ) || ! isset( $styles[ $key ] ) || ! is_string( $styles[ $key ] ) ) {
		return '';
	}

	return trim( $styles[ $key ] );
}

/**
 * The colours the theme gives its links, resting and on hover.
 *
 * Cached per request: reading global styles parses `theme.json`, and the
 * notice can be rendered several times on one page.
 *
 * @return array{color:string,color_hover:string}
 */
function apg_guarantee_theme_link_colors() {
	static $colors = null;

	if ( null !== $colors ) {
		return $colors;
	}

	$colors = array(
		'color'       => apg_guarantee_global_style( array( 'elements', 'link', 'color' ), 'text' ),
		'color_hover' => apg_guarantee_global_style( array( 'elements', 'link', ':hover', 'color' ), 'text' ),
	);

	/**
	 * Filters the link colours read from the theme.
	 *
	 * A classic theme has no `theme.json`, so both values arrive empty and the
	 * button inherits instead. This is where a site can fill them in.
	 *
	 * @param array $colors Resting and hover colour.
	 */
	$colors = (array) apply_filters( 'apg_guarantee_theme_link_colors', $colors );

	return $colors;
}

/**
 * Resolves the appearance one placement should render with.
 *
 * @param array $appearance Stored appearance of the placement, including its `look`.
 * @return array{classes:string,vars:array}
 */
function apg_guarantee_resolve_appearance( $appearance ) {
	$look = isset( $appearance['look'] ) ? (string) $appearance['look'] : 'inherit';

	if ( 'button' === $look ) {
		// The theme paints it. Nothing of ours goes on it.
		return array(
			'classes' => apg_guarantee_theme_button_classes(),
			'vars'    => array(),
		);
	}

	if ( 'link' === $look ) {
		$link = apg_guarantee_theme_link_colors();

		return array(
			'classes' => '',
			'vars'    => array(
				'color'       => $link['color'],
				'color_hover' => $link['color_hover'],
			),
		);
	}

	if ( 'custom' === $look ) {
		return array(
			'classes' => '',
			'vars'    => array(
				'color'            => isset( $appearance['color'] ) ? (string) $appearance['color'] : '',
				'background'       => isset( $appearance['background'] ) ? (string) $appearance['background'] : '',
				'color_hover'      => isset( $appearance['color_hover'] ) ? (string) $appearance['color_hover'] : '',
				'background_hover' => isset( $appearance['background_hover'] ) ? (string) $appearance['background_hover'] : '',
				'font_size'        => isset( $appearance['font_size'] ) ? (string) $appearance['font_size'] : '',
			),
		);
	}

	// 'inherit': the button takes everything from whatever surrounds it.
	return array(
		'classes' => '',
		'vars'    => array(),
	);
}
