<?php
/**
 * Public rendering of the notice.
 *
 * The Commission's practical guidelines describe the online pattern: "a
 * sentence informing consumers about their legal guarantee rights (e.g. 'Your
 * legal guarantee rights')", with the full notice appearing "on the first mouse
 * click or mouse roll-over". The guidelines illustrate it opening over the
 * page, so the notice lives in a native popover: a real modal in the top layer,
 * dismissed with Escape or by clicking outside, without a line of JavaScript.
 *
 * There is one panel per page and as many triggers as the merchant asked for,
 * all pointing at it, so the notice is never duplicated in the document no
 * matter how many places it can be opened from.
 *
 * A browser too old for the popover API ignores the attribute and shows the
 * notice inline instead. Less tidy, but the notice is visible, which is what
 * the law asks for.
 *
 * @package APG_Legal_Guarantee_Notice
 */

defined( 'ABSPATH' ) || exit;

/**
 * The id every trigger on the page points at.
 */
const APG_GUARANTEE_PANEL_ID = 'apg-guarantee-notice-modal';

/**
 * Whether a placement is going to print on this request no matter what.
 *
 * The floating button, the footer and the menu item are decided by a setting,
 * so it is known here, before anything is printed. The shortcodes and the
 * checkout are not, and they do not need to be: they run while the content is
 * being built, which is early enough to enqueue for themselves.
 *
 * @return bool
 */
function apg_guarantee_styles_needed() {
	$settings = apg_guarantee_get_settings();

	return in_array( (string) $settings['float_position'], apg_guarantee_float_positions(), true )
		|| '1' === (string) $settings['footer_enabled']
		|| (bool) absint( $settings['menu_id'] );
}

/**
 * Registers the stylesheet, and enqueues it when a placement will need it.
 *
 * Registering alone is not enough for the placements that print on `wp_footer`.
 * That hook prints late-enqueued styles at priority 20, and the floating button,
 * the footer item and the modal are printed at 99, so asking for the stylesheet
 * from there arrives after the stylesheets have already gone out and the button
 * comes out unstyled, sitting at the end of the document instead of floating.
 *
 * @return void
 */
function apg_guarantee_enqueue_styles() {
	wp_register_style(
		'apg-legal-guarantee-notice',
		plugins_url( 'assets/css/frontend.css', apg_guarantee_DIRECCION ),
		array(),
		apg_guarantee_VERSION
	);

	if ( ! is_admin() && apg_guarantee_available() && apg_guarantee_styles_needed() ) {
		wp_enqueue_style( 'apg-legal-guarantee-notice' );
	}
}
add_action( 'wp_enqueue_scripts', 'apg_guarantee_enqueue_styles' );
// Registered in the admin too, so the settings screen can open the very same
// modal a visitor sees instead of sending the merchant off to another page.
add_action( 'admin_enqueue_scripts', 'apg_guarantee_enqueue_styles' );

/**
 * Default wording of the line that opens the notice.
 *
 * @return string
 */
function apg_guarantee_default_trigger_text() {
	return __( 'Your legal guarantee rights', 'apg-legal-guarantee-notice' );
}

/**
 * Notes that a trigger has been rendered, so the panel is printed later.
 *
 * @param bool $set Whether to mark it as needed.
 * @return bool Whether the panel is needed.
 */
function apg_guarantee_panel_needed( $set = false ) {
	static $needed = false;

	if ( $set ) {
		$needed = true;
	}

	return $needed;
}

/**
 * Whether the notice can be shown to this visitor at all.
 *
 * @return bool
 */
function apg_guarantee_available() {
	return '' !== apg_guarantee_notice_path( apg_guarantee_current_language() );
}

/**
 * Whether one placement shows the notice on this request.
 *
 * The notice is owed to consumers only. A shop that also sells to trade
 * customers has to tell them apart, and WordPress and WooCommerce have no
 * native way of doing it: every wholesale or B2B plugin marks them its own way
 * (a role, a user meta, a VAT number on the order). So the plugin does not
 * guess; it asks, once per placement, and whoever knows the shop answers.
 *
 * Contexts: `checkout`, `email`, `email_attachment`, `float`, `footer`, `menu`
 * and `shortcode`. Only the email contexts have an order; everywhere else the
 * visitor is whoever `get_current_user_id()` says, and a full-page cache will
 * serve every anonymous visitor the same page.
 *
 * @param string        $context Placement being rendered.
 * @param WC_Order|null $order   Order the placement is about, when there is one.
 * @return bool
 */
function apg_guarantee_show_notice( $context, $order = null ) {
	/**
	 * Filters whether one placement shows the notice.
	 *
	 * @param bool          $show    Whether to show it. Default true.
	 * @param string        $context Placement being rendered.
	 * @param WC_Order|null $order   Order, in the email contexts; null elsewhere.
	 */
	return (bool) apply_filters( 'apg_guarantee_show_notice', true, $context, $order );
}

/**
 * Runs the hook that lets a shop print its own content where the notice was
 * hidden, such as its trade guarantee terms for a wholesale customer.
 *
 * @param string        $context    Placement being rendered.
 * @param WC_Order|null $order      Order, in the email context; null elsewhere.
 * @param bool          $plain_text Whether the output is a plain-text email.
 * @return void
 */
function apg_guarantee_notice_hidden( $context, $order = null, $plain_text = false ) {
	/**
	 * Fires where the notice would have been printed and the
	 * `apg_guarantee_show_notice` filter hid it. Runs at the checkout, in the
	 * customer email and in the `[apg_guarantee_notice]` shortcode.
	 *
	 * @param string        $context    Placement being rendered.
	 * @param WC_Order|null $order      Order, in the email context; null elsewhere.
	 * @param bool          $plain_text Whether the output is a plain-text email.
	 */
	do_action( 'apg_guarantee_notice_hidden', $context, $order, $plain_text );
}

/**
 * Wording of the link to Your Europe that goes with the notice.
 *
 * @param string        $context Placement being rendered.
 * @param WC_Order|null $order   Order, in the email context; null elsewhere.
 * @return string
 */
function apg_guarantee_your_europe_link_text( $context, $order = null ) {
	/**
	 * Filters the wording of the link to Your Europe.
	 *
	 * @param string        $text    Link wording.
	 * @param string        $context Placement being rendered.
	 * @param WC_Order|null $order   Order, in the email context; null elsewhere.
	 */
	return (string) apply_filters(
		'apg_guarantee_your_europe_link_text',
		__( 'More about your guarantee rights in your country', 'apg-legal-guarantee-notice' ),
		$context,
		$order
	);
}

/**
 * Wording of the national note, or an empty string when it is off.
 *
 * @param array         $settings Plugin settings.
 * @param string        $context  Placement being rendered.
 * @param WC_Order|null $order    Order, in the email context; null elsewhere.
 * @return string
 */
function apg_guarantee_national_note_text( $settings, $context, $order = null ) {
	if ( '1' !== ( isset( $settings['national_note'] ) ? (string) $settings['national_note'] : '0' ) ) {
		return '';
	}

	$text = trim( (string) ( isset( $settings['national_note_text'] ) ? $settings['national_note_text'] : '' ) );

	if ( '' === $text ) {
		$text = apg_guarantee_default_national_note();
	}

	$text = apg_guarantee_translate_string( $text, 'National note' );

	/**
	 * Filters the national note printed beside the notice. An empty string
	 * leaves it out.
	 *
	 * @param string        $text    Note wording.
	 * @param string        $context Placement being rendered.
	 * @param WC_Order|null $order   Order, in the email context; null elsewhere.
	 */
	return trim( (string) apply_filters( 'apg_guarantee_national_note_text', $text, $context, $order ) );
}

/**
 * Prints the icon that can accompany or replace the wording.
 *
 * Deliberately a neutral shield of our own drawing: the shield with the circle
 * of stars belongs to the official notice and to the GARAN label, and putting a
 * look-alike of it on a shop's own button would be passing off our artwork as
 * the Commission's.
 *
 * @return void
 */
function apg_guarantee_print_icon() {
	?>
	<svg class="apg-guarantee-notice__icon" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false">
		<path fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M12 2.8 4.8 5.6v5.6c0 4.4 3 8.2 7.2 9.6 4.2-1.4 7.2-5.2 7.2-9.6V5.6Z"></path>
		<path fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="m8.8 11.8 2.3 2.3 4.1-4.6"></path>
	</svg>
	<?php
}

/**
 * Prints one button that opens the notice.
 *
 * @param array $args Optional. `label`, `class`, `style` (text|icon_text|icon)
 *                    and `context`, the placement handed to the wording filter.
 * @return void
 */
function apg_guarantee_print_trigger( $args = array() ) {
	if ( ! apg_guarantee_available() ) {
		return;
	}

	$args = wp_parse_args(
		$args,
		array(
			'label'      => '',
			'class'      => '',
			'style'      => 'text',
			'context'    => '',
			// An empty array means "no look of its own": the button inherits
			// everything, which is what the settings preview wants and what a
			// placement with nothing configured gets anyway.
			'appearance' => array(),
		)
	);

	$label = '' !== trim( (string) $args['label'] ) ? (string) $args['label'] : apg_guarantee_trigger_text( (string) $args['context'] );
	$style = in_array( $args['style'], array( 'text', 'icon_text', 'icon' ), true ) ? $args['style'] : 'text';

	wp_enqueue_style( 'apg-legal-guarantee-notice' );
	apg_guarantee_panel_needed( true );

	$resolved = apg_guarantee_resolve_appearance( (array) $args['appearance'] );
	$look     = isset( $args['appearance']['look'] ) ? (string) $args['appearance']['look'] : 'inherit';

	$classes = array(
		'apg-guarantee-notice__trigger',
		'apg-guarantee-notice__trigger--' . $style,
		'apg-guarantee-notice__trigger--look-' . sanitize_html_class( $look ),
	);

	if ( ! empty( $resolved['vars']['background'] ) ) {
		$classes[] = 'apg-guarantee-notice__trigger--filled';
	}

	if ( '' !== $resolved['classes'] ) {
		$classes[] = $resolved['classes'];
	}

	if ( '' !== trim( (string) $args['class'] ) ) {
		$classes[] = sanitize_html_class( (string) $args['class'] );
	}

	$custom = apg_guarantee_trigger_style_attr( $resolved['vars'] );
	?>
	<button
		type="button"
		class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
		<?php echo '' !== $custom ? ' style="' . esc_attr( $custom ) . '"' : ''; ?>
		popovertarget="<?php echo esc_attr( APG_GUARANTEE_PANEL_ID ); ?>"
		<?php echo 'icon' === $style ? ' title="' . esc_attr( $label ) . '"' : ''; ?>
	>
		<?php if ( 'text' !== $style ) : ?>
			<?php apg_guarantee_print_icon(); ?>
		<?php endif; ?>
		<span class="<?php echo 'icon' === $style ? 'screen-reader-text' : 'apg-guarantee-notice__label'; ?>"><?php echo esc_html( $label ); ?></span>
	</button>
	<?php
}

/**
 * Registers the strings the merchant can edit with WPML and Polylang, and
 * returns them translated on a multilingual site.
 *
 * The notice itself needs none of this: it is a file per language and the
 * language is resolved per visitor. What does need it is the wording the shop
 * writes — the line that opens the notice, the national note and the guarantee
 * terms — which is ordinary content of theirs.
 *
 * @param string $value Stored value.
 * @param string $name  Name the string is registered under.
 * @return string
 */
function apg_guarantee_translate_string( $value, $name ) {
	$value = (string) $value;

	if ( '' === $value ) {
		return $value;
	}

	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Third-party hook of WPML and Polylang.
	do_action( 'wpml_register_single_string', 'apg-legal-guarantee-notice', $name, $value );

	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Third-party filter of WPML and Polylang.
	return (string) apply_filters( 'wpml_translate_single_string', $value, 'apg-legal-guarantee-notice', $name );
}

/**
 * Accepts a value only if it can be a CSS colour, and nothing else.
 *
 * The values reaching the style attribute come from two places: the colours the
 * merchant picked, which `sanitize_hex_color()` has already validated, and the
 * colours read out of the theme's `theme.json` for the "like a link" look, which
 * nothing has validated and which the `apg_guarantee_theme_link_colors` filter
 * can also set.
 *
 * `esc_attr()` at the output keeps any of it from escaping the attribute, so
 * this is not about breaking out of the markup: it is about not letting a value
 * close one declaration and open another one. Validating here rather than at
 * each call site is the point, because the next caller will not know it had to.
 *
 * Hex, `rgb()`, `hsl()`, a CSS keyword and a `var()` reference all pass, which
 * is what a block theme actually puts in `theme.json`.
 *
 * @param mixed $value Candidate colour.
 * @return string The value, or an empty string when it is not usable.
 */
function apg_guarantee_css_colour( $value ) {
	$value = trim( (string) $value );

	if ( '' === $value || strlen( $value ) > 100 ) {
		return '';
	}

	// Anything that could end a declaration, open a block or start a comment,
	// plus the two functions that can fetch or run something.
	if ( preg_match( '/[;{}@\\\\]|\/\*|url\s*\(|expression\s*\(/i', $value ) ) {
		return '';
	}

	// Hex.
	if ( preg_match( '/^#(?:[0-9a-f]{3,4}|[0-9a-f]{6}|[0-9a-f]{8})$/i', $value ) ) {
		return $value;
	}

	// A bare keyword: `red`, `transparent`, `currentColor`, `CanvasText`.
	if ( preg_match( '/^[a-z]+$/i', $value ) ) {
		return $value;
	}

	// rgb(), rgba(), hsl(), hsla(), color-mix() and var(), whose arguments are
	// limited to what a colour can be made of.
	if ( preg_match( '/^(?:rgba?|hsla?|hwb|lab|lch|oklab|oklch|color-mix|var)\\([a-z0-9\\s,.%#\\/()_-]*\\)$/i', $value ) ) {
		return $value;
	}

	return '';
}

/**
 * Builds the inline style that carries the merchant's chosen look.
 *
 * The values travel as CSS custom properties rather than as declarations, so
 * the stylesheet stays static and every rule keeps a sensible fallback: leave
 * a setting empty and the button goes on inheriting from the theme, which is
 * what it does out of the box.
 *
 * @param array $appearance Colour, background and font size of one placement.
 * @return string Value for a `style` attribute, empty when nothing was set.
 */
function apg_guarantee_trigger_style_attr( $appearance ) {
	$vars = array();

	$map = array(
		'color'            => '--apg-guarantee-fg',
		'background'       => '--apg-guarantee-bg',
		'color_hover'      => '--apg-guarantee-fg-hover',
		'background_hover' => '--apg-guarantee-bg-hover',
	);

	foreach ( $map as $key => $property ) {
		$value = apg_guarantee_css_colour( isset( $appearance[ $key ] ) ? $appearance[ $key ] : '' );

		if ( '' !== $value ) {
			$vars[] = $property . ':' . $value;
		}
	}

	$fs = isset( $appearance['font_size'] ) ? absint( $appearance['font_size'] ) : 0;

	if ( $fs ) {
		$vars[] = '--apg-guarantee-size:' . $fs . 'px';
	}

	return implode( ';', $vars );
}

/**
 * Prints the panel holding the official notice. Called once per page.
 *
 * @return void
 */
function apg_guarantee_print_panel() {
	if ( ! apg_guarantee_panel_needed() || ! apg_guarantee_available() ) {
		return;
	}

	$settings = apg_guarantee_get_settings();
	$language = apg_guarantee_current_language();
	$title    = APG_GUARANTEE_PANEL_ID . '-title';
	?>
	<div id="<?php echo esc_attr( APG_GUARANTEE_PANEL_ID ); ?>" popover="auto" class="apg-guarantee-notice__modal" role="dialog" aria-labelledby="<?php echo esc_attr( $title ); ?>">
		<div class="apg-guarantee-notice__head">
			<h2 id="<?php echo esc_attr( $title ); ?>" class="apg-guarantee-notice__title"><?php echo esc_html( apg_guarantee_trigger_text( 'panel' ) ); ?></h2>
			<?php
			// The popover API only moves focus into the panel when something in
			// it asks for it, so the close button does. Without this the
			// keyboard stays behind on the trigger and Escape is the only way
			// back out, which a screen reader user would never find.
			?>
			<button type="button" class="apg-guarantee-notice__close" popovertarget="<?php echo esc_attr( APG_GUARANTEE_PANEL_ID ); ?>" popovertargetaction="hide" aria-label="<?php esc_attr_e( 'Close', 'apg-legal-guarantee-notice' ); ?>" autofocus>&times;</button>
		</div>
		<div class="apg-guarantee-notice__body">
			<img
				src="<?php echo esc_url( apg_guarantee_notice_url( $language ) ); ?>"
				alt="<?php esc_attr_e( 'Official EU notice on the legal guarantee of conformity', 'apg-legal-guarantee-notice' ); ?>"
				class="apg-guarantee-notice__image"
				loading="lazy"
				decoding="async"
			>
			<p class="apg-guarantee-notice__link">
				<a href="<?php echo esc_url( apg_guarantee_your_europe_url( $language ) ); ?>" target="_blank" rel="noopener">
					<?php echo esc_html( apg_guarantee_your_europe_link_text( 'panel' ) ); ?>
					<span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'apg-legal-guarantee-notice' ); ?></span>
				</a>
			</p>
			<?php apg_guarantee_print_national_note( $settings, 'panel' ); ?>
		</div>
	</div>
	<?php
}

/**
 * Prints the national note that goes beside the notice, never inside it.
 *
 * The official notice reads "minimum two years" because that is the EU floor
 * and the file cannot be edited. Spain raised the legal guarantee on new goods
 * to three years from delivery in Article 120.1 TRLGDCU, so a Spanish shop has
 * to tell the consumer that, next to the notice.
 *
 * @param array  $settings Plugin settings.
 * @param string $context  Placement being rendered.
 * @return void
 */
function apg_guarantee_print_national_note( $settings, $context = 'panel' ) {
	$text = apg_guarantee_national_note_text( $settings, $context );

	if ( '' === $text ) {
		return;
	}

	$terms = absint( isset( $settings['terms_page'] ) ? $settings['terms_page'] : 0 );
	$link  = $terms ? get_permalink( $terms ) : '';
	?>
	<p class="apg-guarantee-notice__national">
		<?php echo esc_html( $text ); ?>
		<?php if ( $link ) : ?>
			<a href="<?php echo esc_url( $link ); ?>"><?php esc_html_e( 'See our terms and conditions', 'apg-legal-guarantee-notice' ); ?></a>
		<?php endif; ?>
	</p>
	<?php
}

/**
 * Shortcode `[apg_guarantee_notice]`: the official notice printed in the page.
 *
 * The placements on the shop open the notice in a modal, which is the right
 * shape for a reminder that has to be reachable from everywhere without
 * getting in the way. A page devoted to the guarantee is the opposite case:
 * there the notice is the content, so here it goes inline, at full size, with
 * the Your Europe link and the national note under it.
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function apg_guarantee_notice_shortcode( $atts ) {
	$atts = shortcode_atts( array( 'class' => '' ), (array) $atts, 'apg_guarantee_notice' );

	if ( ! apg_guarantee_available() ) {
		return '';
	}

	ob_start();

	if ( ! apg_guarantee_show_notice( 'shortcode' ) ) {
		apg_guarantee_notice_hidden( 'shortcode' );

		return (string) ob_get_clean();
	}

	wp_enqueue_style( 'apg-legal-guarantee-notice' );

	$settings = apg_guarantee_get_settings();
	$language = apg_guarantee_current_language();
	$classes  = trim( 'apg-guarantee-inline ' . sanitize_html_class( (string) $atts['class'] ) );

	/** This action is documented in includes/clases/checkout.php */
	do_action( 'apg_guarantee_before_notice', 'shortcode', null, false );
	?>
	<div class="<?php echo esc_attr( $classes ); ?>">
		<img
			src="<?php echo esc_url( apg_guarantee_notice_url( $language ) ); ?>"
			alt="<?php esc_attr_e( 'Official EU notice on the legal guarantee of conformity', 'apg-legal-guarantee-notice' ); ?>"
			class="apg-guarantee-notice__image"
			loading="lazy"
			decoding="async"
		>
		<p class="apg-guarantee-notice__link">
			<a href="<?php echo esc_url( apg_guarantee_your_europe_url( $language ) ); ?>" target="_blank" rel="noopener">
				<?php echo esc_html( apg_guarantee_your_europe_link_text( 'shortcode' ) ); ?>
				<span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'apg-legal-guarantee-notice' ); ?></span>
			</a>
		</p>
		<?php apg_guarantee_print_national_note( $settings, 'shortcode' ); ?>
	</div>
	<?php
	/** This action is documented in includes/clases/checkout.php */
	do_action( 'apg_guarantee_after_notice', 'shortcode', null, false );

	return (string) ob_get_clean();
}
add_shortcode( 'apg_guarantee_notice', 'apg_guarantee_notice_shortcode' );

/**
 * Shortcode `[apg_guarantee_terms]`: the shop's own guarantee terms.
 *
 * The official notice is the European summary and cannot be edited. What it
 * does not carry is what this particular shop does: how long the guarantee
 * runs here, how a customer claims it and who they write to. That text is the
 * merchant's, so the plugin only ships a starting point for it.
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function apg_guarantee_terms_shortcode( $atts ) {
	$atts     = shortcode_atts( array( 'class' => '' ), (array) $atts, 'apg_guarantee_terms' );
	$settings = apg_guarantee_get_settings();
	$text     = trim( (string) ( isset( $settings['terms_text'] ) ? $settings['terms_text'] : '' ) );

	if ( '' === $text ) {
		$text = apg_guarantee_default_terms_text();
	}

	if ( '' === $text ) {
		return '';
	}

	$text = apg_guarantee_translate_string( $text, 'Guarantee terms' );

	$classes = trim( 'apg-guarantee-terms ' . sanitize_html_class( (string) $atts['class'] ) );

	return '<div class="' . esc_attr( $classes ) . '">' . wp_kses_post( wpautop( $text ) ) . '</div>';
}
add_shortcode( 'apg_guarantee_terms', 'apg_guarantee_terms_shortcode' );

/**
 * Shortcode `[apg_guarantee_button]`: the button that opens the modal, for
 * placing it somewhere the settings do not reach.
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function apg_guarantee_button_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'label' => '',
			'class' => '',
			'style' => '',
		),
		(array) $atts,
		'apg_guarantee_button'
	);

	if ( '' === $atts['style'] ) {
		unset( $atts['style'] );
	}

	if ( ! apg_guarantee_show_notice( 'shortcode' ) ) {
		return '';
	}

	$atts['context'] = 'shortcode';

	ob_start();
	echo '<span class="apg-guarantee-notice">';
	apg_guarantee_print_trigger( $atts );
	echo '</span>';

	return (string) ob_get_clean();
}
add_shortcode( 'apg_guarantee_button', 'apg_guarantee_button_shortcode' );

/**
 * Appends the notice as the last item of the menu the merchant chose.
 *
 * A menu item is a link, and a theme styles it by `.menu-item a`. A `<button>`
 * dropped in there matches none of that and comes out looking foreign, which is
 * what the first version of this did. So when there is a page to point at, the
 * item is a real link to it, carrying the same classes WordPress gives its own
 * items, and the theme styles it without knowing the difference.
 *
 * Only when no page has been chosen does it fall back to the button that opens
 * the modal, because a menu entry that goes nowhere would be worse.
 *
 * @param string   $items Menu items markup.
 * @param stdClass $args  Arguments `wp_nav_menu()` was called with.
 * @return string
 */
function apg_guarantee_menu_item( $items, $args ) {
	$settings = apg_guarantee_get_settings();
	$menu_id  = absint( isset( $settings['menu_id'] ) ? $settings['menu_id'] : 0 );

	if ( ! $menu_id || is_admin() || ! apg_guarantee_available() || ! apg_guarantee_show_notice( 'menu' ) ) {
		return $items;
	}

	// `wp_nav_menu()` can be given the menu by id, by slug or by theme location,
	// so the object it resolved to is the only reliable thing to compare.
	$menu = isset( $args->menu ) ? wp_get_nav_menu_object( $args->menu ) : false;

	if ( ! $menu && ! empty( $args->theme_location ) ) {
		$locations = get_nav_menu_locations();
		$menu      = isset( $locations[ $args->theme_location ] ) ? wp_get_nav_menu_object( $locations[ $args->theme_location ] ) : false;
	}

	if ( ! $menu || absint( $menu->term_id ) !== $menu_id ) {
		return $items;
	}

	ob_start();
	apg_guarantee_print_menu_item();

	return $items . (string) ob_get_clean();
}
add_filter( 'wp_nav_menu_items', 'apg_guarantee_menu_item', 10, 2 );

/**
 * Prints the `<li>` of the menu.
 *
 * @return void
 */
function apg_guarantee_print_menu_item() {
	$settings = apg_guarantee_get_settings();
	$page     = absint( isset( $settings['terms_page'] ) ? $settings['terms_page'] : 0 );
	$url      = $page ? get_permalink( $page ) : '';
	$args     = apg_guarantee_placement_args( 'menu' );
	$label    = apg_guarantee_trigger_text( 'menu' );

	// The classes WordPress puts on a page item of its own, so a theme that
	// styles `.menu-item-object-page` or the current item finds them here too.
	$classes = array( 'menu-item', 'apg-guarantee-menu-item' );

	if ( $url ) {
		$classes[] = 'menu-item-type-post_type';
		$classes[] = 'menu-item-object-page';
		$classes[] = 'menu-item-' . $page;

		if ( is_page( $page ) ) {
			$classes[] = 'current-menu-item';
			$classes[] = 'current_page_item';
		}
	}

	printf( '<li class="%s">', esc_attr( implode( ' ', $classes ) ) );

	if ( ! $url ) {
		// No page to go to, so the item opens the modal instead.
		apg_guarantee_print_trigger( $args );
		echo '</li>';

		return;
	}

	$resolved = apg_guarantee_resolve_appearance( (array) $args['appearance'] );
	$custom   = apg_guarantee_trigger_style_attr( $resolved['vars'] );
	$style    = in_array( $args['style'], array( 'text', 'icon_text', 'icon' ), true ) ? $args['style'] : 'text';

	$link_classes = array( 'apg-guarantee-menu-link', 'apg-guarantee-menu-link--' . $style );

	if ( '' !== $resolved['classes'] ) {
		$link_classes[] = $resolved['classes'];
	}
	?>
	<a
		href="<?php echo esc_url( $url ); ?>"
		class="<?php echo esc_attr( implode( ' ', $link_classes ) ); ?>"
		<?php echo '' !== $custom ? ' style="' . esc_attr( $custom ) . '"' : ''; ?>
		<?php echo 'icon' === $style ? ' title="' . esc_attr( $label ) . '"' : ''; ?>
	>
		<?php if ( 'text' !== $style ) : ?>
			<?php apg_guarantee_print_icon(); ?>
		<?php endif; ?>
		<span class="<?php echo 'icon' === $style ? 'screen-reader-text' : 'apg-guarantee-menu-link__label'; ?>"><?php echo esc_html( $label ); ?></span>
	</a>
	</li>
	<?php
}

/**
 * Prints the floating trigger and, last of all, the panel every trigger opens.
 *
 * @return void
 */
function apg_guarantee_footer() {
	if ( is_admin() ) {
		return;
	}

	$settings = apg_guarantee_get_settings();
	$position = isset( $settings['float_position'] ) ? (string) $settings['float_position'] : '';

	if ( in_array( $position, apg_guarantee_float_positions(), true ) && apg_guarantee_show_notice( 'float' ) ) {
		// The wrapper only positions. Every colour lives on the button, so the
		// two can never paint a surface on top of each other.
		printf( '<div class="apg-guarantee-float apg-guarantee-float--%1$s">', esc_attr( $position ) );
		apg_guarantee_print_trigger( apg_guarantee_placement_args( 'float' ) );
		echo '</div>';
	}

	if ( '1' === (string) ( isset( $settings['footer_enabled'] ) ? $settings['footer_enabled'] : '0' ) && apg_guarantee_show_notice( 'footer' ) ) {
		echo '<div class="apg-guarantee-footer">';
		apg_guarantee_print_trigger( apg_guarantee_placement_args( 'footer' ) );
		echo '</div>';
	}

	apg_guarantee_print_panel();
}
add_action( 'wp_footer', 'apg_guarantee_footer', 99 );

/**
 * The positions the floating trigger can take.
 *
 * @return array<int,string>
 */
function apg_guarantee_float_positions() {
	return array( 'top-left', 'top-right', 'middle-left', 'middle-right', 'bottom-left', 'bottom-right' );
}
