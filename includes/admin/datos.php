<?php
/**
 * Plugin identity and the wordpress.org rating, shared by the settings screen
 * and the plugin row. Mirrors the arrangement of the rest of the Art Project
 * Group plugins so their admin screens look and behave the same.
 *
 * @package APG_Legal_Guarantee_Notice
 */

defined( 'ABSPATH' ) || exit;

/**
 * Returns the plugin's own identity: name, slug and the addresses the
 * information box links to.
 *
 * Built on demand rather than at file load, because the name is translatable
 * and asking for a translation before `init` triggers WordPress 6.7's
 * "translation loading was triggered too early" notice.
 *
 * @return array<string,string>
 */
function apg_guarantee_datos() {
	$slug = 'apg-legal-guarantee-notice';

	return array(
		'plugin'     => __( 'APG Legal Guarantee Notice', 'apg-legal-guarantee-notice' ),
		'plugin_uri' => $slug,
		'plugin_url' => 'https://artprojectgroup.es/plugins-para-wordpress/apg-aviso-de-garantia-legal',
		'donacion'   => 'https://artprojectgroup.es/tienda/donacion',
		'soporte'    => 'https://wordpress.org/support/plugin/' . $slug . '/',
		'puntuacion' => 'https://wordpress.org/support/plugin/' . $slug . '/reviews/',
		'ajustes'    => 'admin.php?page=' . $slug,
	);
}

/**
 * Returns the star rating of the plugin on wordpress.org.
 *
 * The remote answer is cached for 24 hours, and a failure degrades to a plain
 * link rather than to an error: a rating is decoration, and the settings screen
 * has to work on a site with no outbound connection.
 *
 * @return string Markup for the stars, or a link when the rating is unknown.
 */
function apg_guarantee_puntuacion() {
	$datos = apg_guarantee_datos();
	$link  = $datos['puntuacion'] . '?rate=5#postform';
	/* translators: %s plugin name */
	$title = sprintf( esc_attr__( 'Please, rate %s:', 'apg-legal-guarantee-notice' ), $datos['plugin'] );

	// Only the two numbers the stars need are cached, not the whole HTTP
	// response: that was tens of kilobytes of headers and JSON sitting in the
	// options table for a day to draw five stars.
	$estrellas_datos = get_transient( 'apg_guarantee_plugin' );

	if ( 'error' === $estrellas_datos ) {
		return '<a title="' . $title . '" href="' . esc_url( $link ) . '" class="estrellas">'
			. esc_html__( 'Unknown rating', 'apg-legal-guarantee-notice' ) . '</a>';
	}

	if ( ! is_array( $estrellas_datos ) ) {
		$respuesta = wp_remote_get(
			'https://api.wordpress.org/plugins/info/1.2/?action=plugin_information&request[slug]=' . $datos['plugin_uri'],
			array( 'timeout' => 5 )
		);

		if ( is_wp_error( $respuesta ) || 200 !== (int) wp_remote_retrieve_response_code( $respuesta ) ) {
			// A failure is remembered for an hour so a site with no outbound
			// connection does not spend five seconds timing out every time the
			// screen is opened. An hour is short enough to pick the rating up
			// once the plugin is published or the connection comes back.
			set_transient( 'apg_guarantee_plugin', 'error', HOUR_IN_SECONDS );

			return '<a title="' . $title . '" href="' . esc_url( $link ) . '" class="estrellas">'
				. esc_html__( 'Unknown rating', 'apg-legal-guarantee-notice' ) . '</a>';
		}

		$plugin          = json_decode( wp_remote_retrieve_body( $respuesta ) );
		$estrellas_datos = array(
			'rating' => isset( $plugin->rating ) ? (float) $plugin->rating : 0,
			'number' => isset( $plugin->num_ratings ) ? (int) $plugin->num_ratings : 0,
		);

		set_transient( 'apg_guarantee_plugin', $estrellas_datos, DAY_IN_SECONDS );
	}

	ob_start();
	wp_star_rating(
		array(
			'rating' => $estrellas_datos['rating'],
			'type'   => 'percent',
			'number' => $estrellas_datos['number'],
		)
	);
	$estrellas = ob_get_clean();

	return '<a title="' . $title . '" href="' . esc_url( $link ) . '" class="estrellas">' . $estrellas . '</a>';
}

/**
 * Loads the admin stylesheet and the icon fonts on the plugin's own screens.
 *
 * @param string $hook Current admin page.
 * @return void
 */
function apg_guarantee_admin_assets( $hook ) {
	if ( false === strpos( (string) $hook, 'apg-legal-guarantee-notice' ) ) {
		return;
	}

	wp_enqueue_style(
		'apg-guarantee-fonts',
		plugins_url( 'assets/fonts/stylesheet.css', apg_guarantee_DIRECCION ),
		array(),
		apg_guarantee_VERSION
	);

	wp_enqueue_style(
		'apg-guarantee-admin',
		plugins_url( 'assets/css/admin.css', apg_guarantee_DIRECCION ),
		array( 'apg-guarantee-fonts' ),
		apg_guarantee_VERSION
	);

	apg_guarantee_select_mejorado();

	wp_enqueue_script(
		'apg-guarantee-admin',
		plugins_url( 'assets/js/admin.js', apg_guarantee_DIRECCION ),
		array(),
		apg_guarantee_VERSION,
		true
	);
}
add_action( 'admin_enqueue_scripts', 'apg_guarantee_admin_assets', 20 );

/**
 * Turns the page dropdown into a searchable control when WooCommerce can.
 *
 * WooCommerce's own `wc-enhanced-select` pulls SelectWoo in as a dependency,
 * carries its translations and initialises every `.wc-enhanced-select` on the
 * page by itself, which is why the dropdown only needs to carry that class and
 * there is nothing to initialise here.
 *
 * Without WooCommerce the control stays a native `<select>`, and that is the
 * right answer rather than a shortcoming. Reaching into another plugin's folder
 * to borrow its copy of Select2 means guessing where that plugin lives, and a
 * site is free to rename its plugin directory or move it elsewhere. A native
 * select works everywhere, needs nothing and is perfectly usable on a site with
 * a handful of pages.
 *
 * @return void
 */
function apg_guarantee_select_mejorado() {
	if ( ! wp_script_is( 'wc-enhanced-select', 'registered' ) ) {
		return;
	}

	wp_enqueue_style( 'woocommerce_admin_styles' );
	wp_enqueue_script( 'wc-enhanced-select' );
}


/**
 * Adds the usual Art Project Group links to the plugin row.
 *
 * @param array $links Existing meta links.
 * @param string $file Plugin file the row belongs to.
 * @return array
 */
function apg_guarantee_enlaces_fila( $links, $file ) {
	if ( plugin_basename( apg_guarantee_DIRECCION ) !== $file ) {
		return $links;
	}

	$datos = apg_guarantee_datos();

	$links[] = '<a href="' . esc_url( $datos['soporte'] ) . '">' . esc_html__( 'Support', 'apg-legal-guarantee-notice' ) . '</a>';
	$links[] = '<a href="' . esc_url( $datos['donacion'] ) . '" target="_blank">' . esc_html__( 'Donate', 'apg-legal-guarantee-notice' ) . '</a>';

	return $links;
}
add_filter( 'plugin_row_meta', 'apg_guarantee_enlaces_fila', 10, 2 );
