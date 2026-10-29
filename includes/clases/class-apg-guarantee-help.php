<?php
/**
 * Ayuda contextual de la pantalla de ajustes: la pestaña «Ayuda» nativa de WordPress, con un panel
 * por cada bloque de la pantalla. La versión completa vive en la web y se enlaza desde el lateral.
 *
 * @package APG_Legal_Guarantee_Notice
 */

defined( 'ABSPATH' ) || exit;

/**
 * Añade los paneles de ayuda a la pantalla de ajustes del plugin.
 */
class APG_Guarantee_Help {

	/**
	 * Dirección de la documentación completa, en español.
	 */
	const DOCS_URL = 'https://artprojectgroup.es/documentacion/apg-aviso-de-garantia-legal';

	/**
	 * Dirección de la documentación completa, en inglés.
	 */
	const DOCS_URL_EN = 'https://artprojectgroup.com/documentation/apg-aviso-de-garantia-legal';

	/**
	 * Sustituye la ayuda de la pantalla por los paneles del plugin.
	 *
	 * La pantalla cuelga del menú de WooCommerce cuando está instalado y del de Ajustes cuando no,
	 * y en los dos casos es del plugin: lo que hubiera antes no habla de esta pantalla.
	 *
	 * @return void
	 */
	public static function add_tabs() {
		$screen = get_current_screen();
		if ( ! $screen ) {
			return;
		}

		$screen->remove_help_tabs();

		foreach ( self::get_panels() as $id => $panel ) {
			$screen->add_help_tab(
				array(
					'id'      => 'apg_guarantee_help_' . $id,
					'title'   => $panel['title'],
					'content' => self::render( $panel['items'] ),
				)
			);
		}

		$screen->set_help_sidebar( self::sidebar() );
	}

	/**
	 * Lateral con el enlace a la documentación completa.
	 *
	 * @return string
	 */
	private static function sidebar() {
		return '<p><strong>' . esc_html__( 'More help', 'apg-legal-guarantee-notice' ) . '</strong></p>' .
			'<p><a href="' . esc_url( self::documentacion() ) . '" target="_blank">' . esc_html__( 'Full documentation', 'apg-legal-guarantee-notice' ) . '</a></p>';
	}

	/**
	 * Dirección de la documentación en el idioma de quien lee la pantalla.
	 *
	 * @return string
	 */
	private static function documentacion() {
		return ( 0 === strpos( get_user_locale(), 'es' ) ) ? self::DOCS_URL : self::DOCS_URL_EN;
	}

	/**
	 * Pinta un panel: un párrafo de entrada y una lista de término => explicación.
	 *
	 * @param array $items Primer elemento: texto de entrada (cadena). Después, término => explicación.
	 * @return string
	 */
	private static function render( array $items ) {
		$html = '';
		$list = '';

		foreach ( $items as $term => $text ) {
			if ( is_int( $term ) ) {
				$html .= '<p>' . esc_html( $text ) . '</p>';
			} else {
				$list .= '<li><strong>' . esc_html( $term ) . '</strong>: ' . esc_html( $text ) . '</li>';
			}
		}

		return $html . ( '' !== $list ? '<ul>' . $list . '</ul>' : '' );
	}

	/**
	 * Paneles de la pantalla, en el orden de sus bloques.
	 *
	 * Las dos ubicaciones de WooCommerce llevan su panel aunque la pantalla las oculte sin él: el
	 * plugin funciona con WooCommerce y sin WooCommerce, y el panel dice de qué depende cada una.
	 *
	 * @return array ID del panel => array( title, items ).
	 */
	private static function get_panels() {
		return array(
			'notice'    => array(
				'title' => __( 'The notice', 'apg-legal-guarantee-notice' ),
				'items' => array(
					__( 'Article 22a of Directive 2011/83/EU has required every shop selling goods to consumers to display the official EU notice on the legal guarantee of conformity since 27 September 2026. Commission Implementing Regulation (EU) 2025/1960 fixes its design and its content.', 'apg-legal-guarantee-notice' ),
					__( 'There is nothing to write and nothing to design here: the plugin ships the Commission\'s own files, in the 24 official EU languages, and serves them untouched. The notice cannot be edited, cropped or redrawn, and the plugin will not let you.', 'apg-legal-guarantee-notice' ),
					__( 'The language follows the visitor', 'apg-legal-guarantee-notice' ) => __( 'not the site. A shop read in German serves the German notice. Catalan, Basque and Galician fall back on the Spanish notice rather than the English one.', 'apg-legal-guarantee-notice' ),
					__( 'Preview it', 'apg-legal-guarantee-notice' )                      => __( 'opens the very notice a visitor would see right now, in the language named beside it.', 'apg-legal-guarantee-notice' ),
					__( 'It opens on the first click', 'apg-legal-guarantee-notice' )      => __( 'in a modal, the pattern the Commission\'s guidelines illustrate, built with the browser\'s own popover API and no JavaScript at all.', 'apg-legal-guarantee-notice' ),
				),
			),

			'where'     => array(
				'title' => __( 'Where it is shown', 'apg-legal-guarantee-notice' ),
				'items' => array(
					__( 'The obligation is to display the notice at shop level, so one placement reaching every page is enough. Switch them all off and the plugin warns you on the dashboard, because then the notice is not reaching anybody.', 'apg-legal-guarantee-notice' ),
					__( 'Floating button', 'apg-legal-guarantee-notice' ) => __( 'floats over every page of the shop, in any of six positions. On out of the box, at the bottom right, and on its own it satisfies the obligation.', 'apg-legal-guarantee-notice' ),
					__( 'Menu', 'apg-legal-guarantee-notice' )            => __( 'added as the last item of the menu you choose. With a page chosen below it is an ordinary link, which is what a theme knows how to style; with no page it opens the modal instead.', 'apg-legal-guarantee-notice' ),
					__( 'Site footer', 'apg-legal-guarantee-notice' )     => __( 'adds it to the footer of every page, wherever your theme prints the footer.', 'apg-legal-guarantee-notice' ),
				),
			),

			'look'      => array(
				'title' => __( 'How it looks', 'apg-legal-guarantee-notice' ),
				'items' => array(
					__( 'Every placement carries its own appearance, so the floating button can shout and the menu item can be indistinguishable from the rest of the menu. Out of the box nothing is imposed at all, which is what keeps the notice readable on a dark theme.', 'apg-legal-guarantee-notice' ),
					__( 'Shows', 'apg-legal-guarantee-notice' )                                => __( 'text, icon and text, or icon only. The Commission describes the notice as a sentence, and every example in its guidelines is written text, so text only is the recommended answer. Icon only keeps the wording for screen readers and in the tooltip, but a visitor on a phone never sees it, since there is no hover on a touch screen.', 'apg-legal-guarantee-notice' ),
					__( 'Takes its colours from', 'apg-legal-guarantee-notice' )               => __( 'whatever surrounds it, a link of this theme, a button of this theme, or colours you choose.', 'apg-legal-guarantee-notice' ),
					__( 'Whatever surrounds it (recommended)', 'apg-legal-guarantee-notice' )  => __( 'nothing of the plugin\'s goes on the element, which inherits everything from the theme.', 'apg-legal-guarantee-notice' ),
					__( 'Like a button of this theme', 'apg-legal-guarantee-notice' )          => __( 'hands the element the theme\'s own button classes, so the theme paints it, hover and focus included.', 'apg-legal-guarantee-notice' ),
					__( 'Like a link of this theme', 'apg-legal-guarantee-notice' )            => __( 'reads the link colours out of the theme, which a block theme answers through its theme.json and a classic theme not at all. With no answer the element simply inherits.', 'apg-legal-guarantee-notice' ),
					__( 'Colours I choose', 'apg-legal-guarantee-notice' )                     => __( 'opens the text, background and hover colours, and the font size in pixels. Each colour has an Inherit box next to it, and ticked it is the theme that decides; an empty font size leaves the size to the theme as well.', 'apg-legal-guarantee-notice' ),
				),
			),

			'woo'       => array(
				'title' => __( 'Checkout and emails', 'apg-legal-guarantee-notice' ),
				'items' => array(
					__( 'Both of these are WooCommerce\'s: without it there is no checkout to print above and no order email to go into, so the two settings are hidden rather than shown doing nothing. What you had configured is kept and comes back the day WooCommerce does.', 'apg-legal-guarantee-notice' ),
					__( 'Checkout', 'apg-legal-guarantee-notice' )    => __( 'shows it above the place-order button of the classic checkout, and under the form when the shop uses the Checkout block, where it is outside the React root and survives. The Commission\'s guidelines ask for the checkout page, prominently, not a precise spot.', 'apg-legal-guarantee-notice' ),
					__( 'Order email', 'apg-legal-guarantee-notice' ) => __( 'includes the notice in the emails the customer receives, as an image, since an email client cannot open a modal, and attaches the Commission\'s official PDF in the customer\'s language. Your own copies of the emails are left alone.', 'apg-legal-guarantee-notice' ),
				),
			),

			'wording'   => array(
				'title' => __( 'Wording and the national note', 'apg-legal-guarantee-notice' ),
				'items' => array(
					__( 'The notice is the Commission\'s and cannot be touched. These two texts are yours: the line that opens it and the national note printed beside it.', 'apg-legal-guarantee-notice' ),
					__( 'Wording that opens it', 'apg-legal-guarantee-notice' )     => __( 'used by every placement, and the title of the modal. Leave it empty for the default wording.', 'apg-legal-guarantee-notice' ),
					__( 'Show the note', 'apg-legal-guarantee-notice' )             => __( 'the official notice says "minimum two years" because that is the European floor and the file may not be edited. Where your own country grants longer, this is how you tell the customer, next to the notice and never inside it.', 'apg-legal-guarantee-notice' ),
					__( 'Wording', 'apg-legal-guarantee-notice' )                   => __( 'the text of that note. Empty, it states the three years Article 120.1 TRLGDCU grants in Spain, and it arrives switched on when the shop sells from Spain. The country is read from your WooCommerce base location, never guessed from the language.', 'apg-legal-guarantee-notice' ),
					__( 'Terms and conditions page', 'apg-legal-guarantee-notice' )  => __( 'linked from the national note, and the page the menu item points at. Defaults to the Terms and conditions page of WooCommerce, which is not the same thing as the privacy policy page.', 'apg-legal-guarantee-notice' ),
					__( 'Create the page', 'apg-legal-guarantee-notice' )           => __( 'creates a published page holding the official notice and your guarantee terms, and selects it here. Uninstalling the plugin never deletes it: it is your content.', 'apg-legal-guarantee-notice' ),
				),
			),

			'terms'     => array(
				'title' => __( 'Your guarantee terms', 'apg-legal-guarantee-notice' ),
				'items' => array(
					__( 'The official notice is the European summary. What it does not say is what your shop does: how long the guarantee runs here, how a customer claims it and who they write to.', 'apg-legal-guarantee-notice' ),
					__( 'Text', 'apg-legal-guarantee-notice' ) => __( 'your own terms, shown by the [apg_guarantee_terms] shortcode. Leave it empty to use the suggested text, which appears greyed out in the field. Read it before you publish it: it describes what the law already grants, but only you know how your shop handles a claim, and it has no legal value until you have made it yours.', 'apg-legal-guarantee-notice' ),
				),
			),

			'shortcodes' => array(
				'title' => __( 'Shortcodes', 'apg-legal-guarantee-notice' ),
				'items' => array(
					__( 'For placing the notice somewhere the settings above do not reach.', 'apg-legal-guarantee-notice' ),
					'[apg_guarantee_notice]' => __( 'the official notice printed in the page, at full size, with the Your Europe link and the national note under it. For a page devoted to the guarantee, where the notice is the content rather than a reminder.', 'apg-legal-guarantee-notice' ),
					'[apg_guarantee_terms]'  => __( 'your guarantee terms, the text above.', 'apg-legal-guarantee-notice' ),
					'[apg_guarantee_button]' => __( 'the button that opens the notice in a modal. Accepts label, class and style, the same three styles as the placements.', 'apg-legal-guarantee-notice' ),
				),
			),
		);
	}
}
