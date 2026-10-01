<?php
/**
 * Resolves which official notice to show and where it lives.
 *
 * The notice is not something this plugin writes. Commission Implementing
 * Regulation (EU) 2025/1960 fixes its design and content, and the Commission's
 * practical guidelines say it must not be distorted or cropped, so the plugin
 * ships the Commission's own files and serves them untouched.
 *
 * They travel gzipped only to keep the plugin small: gzip is lossless, so the
 * bytes that leave the server are the bytes the Commission published. There is
 * a test for exactly that in the plugin's test folder.
 *
 * @package APG_Legal_Guarantee_Notice
 */

defined( 'ABSPATH' ) || exit;

/**
 * The 24 official EU languages the Commission publishes the notice in.
 */
const APG_GUARANTEE_LANGUAGES = array(
	'bg', 'cs', 'da', 'de', 'el', 'en', 'es', 'et', 'fi', 'fr', 'ga', 'hr',
	'hu', 'it', 'lt', 'lv', 'mt', 'nl', 'pl', 'pt', 'ro', 'sk', 'sl', 'sv',
);

/**
 * Maps a WordPress locale to one of the 24 languages the notice exists in.
 *
 * Spain, and a few other Member States, have co-official languages the
 * Commission does not publish the notice in. A shop in Catalan, Basque or
 * Galician is still a Spanish shop, so it gets the Spanish notice rather than
 * the English one, which is what a consumer there would expect to read.
 *
 * @param string $locale WordPress locale, e.g. `es_ES`, `ca`, `pt_BR`.
 * @return string Two-letter language code present in APG_GUARANTEE_LANGUAGES.
 */
function apg_guarantee_language_from_locale( $locale ) {
	$locale = strtolower( str_replace( '-', '_', (string) $locale ) );
	$base   = substr( $locale, 0, 2 );

	/**
	 * Filters the fallback map for locales with no official notice of their own.
	 *
	 * Keyed by the two-letter code of the locale, valued with the official EU
	 * language to serve instead.
	 *
	 * @param array<string,string> $map Fallbacks.
	 */
	$fallbacks = (array) apply_filters(
		'apg_guarantee_language_fallbacks',
		array(
			// Co-official languages of Spain.
			'ca' => 'es',
			'eu' => 'es',
			'gl' => 'es',
			'an' => 'es',
			'ast' => 'es',
			// Luxembourgish is not an EU procedural language for this notice.
			'lb' => 'fr',
			// Non-EU variants of EU languages keep their language.
			'nb' => 'en',
			'nn' => 'en',
		)
	);

	if ( isset( $fallbacks[ $base ] ) ) {
		$base = $fallbacks[ $base ];
	}

	if ( ! in_array( $base, APG_GUARANTEE_LANGUAGES, true ) ) {
		$base = 'en';
	}

	/**
	 * Filters the language of the notice served to the current visitor.
	 *
	 * @param string $base   Resolved two-letter language code.
	 * @param string $locale Locale it was resolved from.
	 */
	return (string) apply_filters( 'apg_guarantee_language', $base, $locale );
}

/**
 * Returns the language of the notice for the visitor being served right now.
 *
 * `determine_locale()` already accounts for the user's own profile language and
 * for what a multilingual plugin has set for the request, so it is the right
 * question to ask: the notice has to be in the consumer's language, not in the
 * language the site was installed in.
 *
 * @return string
 */
function apg_guarantee_current_language() {
	return apg_guarantee_language_from_locale( determine_locale() );
}

/**
 * Absolute path of the packed notice for a language.
 *
 * @param string $language Two-letter language code.
 * @return string Path, or empty string when there is no file for it.
 */
function apg_guarantee_notice_path( $language ) {
	$language = strtolower( (string) $language );

	if ( ! in_array( $language, APG_GUARANTEE_LANGUAGES, true ) ) {
		return '';
	}

	$path = plugin_dir_path( apg_guarantee_DIRECCION ) . 'assets/notices/notice-' . $language . '.svgz';

	return file_exists( $path ) ? $path : '';
}

/**
 * Absolute path of the official PDF of the notice for a language.
 *
 * The same artwork as the SVG and from the same place: the Commission publishes
 * both in its asset pack. The PDF is what travels attached to the order email,
 * because that is the format a customer can file, print and open anywhere, and
 * because it is the Commission's own file rather than anything this plugin drew.
 *
 * They are not gzipped like the SVG: a PDF carries its own compression and
 * packing it again saves nothing.
 *
 * @param string $language Two-letter language code.
 * @return string Path, or empty string when there is no file for it.
 */
function apg_guarantee_notice_pdf_path( $language ) {
	$language = strtolower( (string) $language );

	if ( ! in_array( $language, APG_GUARANTEE_LANGUAGES, true ) ) {
		return '';
	}

	$path = plugin_dir_path( apg_guarantee_DIRECCION ) . 'assets/notices/notice-' . $language . '.pdf';

	return file_exists( $path ) ? $path : '';
}

/**
 * URL the notice is served from.
 *
 * @param string $language Two-letter language code.
 * @return string
 */
function apg_guarantee_notice_url( $language ) {
	return add_query_arg(
		array(
			'apg_guarantee_notice' => strtolower( (string) $language ),
			'v'                    => apg_guarantee_VERSION,
		),
		home_url( '/' )
	);
}

/**
 * The Your Europe address behind the QR code of the notice, per language.
 *
 * The Commission's practical guidelines require a clickable link to the same
 * destination as the QR code to be available alongside the notice, and list one
 * address per language.
 *
 * @param string $language Two-letter language code.
 * @return string
 */
function apg_guarantee_your_europe_url( $language ) {
	$paths = array(
		'bg' => 'гаранции',
		'cs' => 'záruky_cs',
		'da' => 'garantier',
		'de' => 'garantien',
		'el' => 'εγγυήσεις',
		'en' => 'guarantees',
		'es' => 'garantías',
		'et' => 'garantiid',
		'fi' => 'takuut',
		'fr' => 'garanties',
		'ga' => 'ráthaíochtaí',
		'hr' => 'jamstva_hr',
		'hu' => 'garanciak',
		'it' => 'garanzie',
		'lt' => 'garantijos_lt',
		'lv' => 'garantijas_lv',
		'mt' => 'garanziji',
		'nl' => 'garantie',
		'pl' => 'gwarancje',
		'pt' => 'garantias',
		'ro' => 'garantii',
		'sk' => 'záruky_sk',
		'sl' => 'jamstva_sl',
		'sv' => 'reklamationsratt',
	);

	$language = strtolower( (string) $language );
	$path     = isset( $paths[ $language ] ) ? $paths[ $language ] : $paths['en'];

	/**
	 * Filters the Your Europe address the notice links to.
	 *
	 * @param string $url      Absolute URL.
	 * @param string $language Language the notice is being shown in.
	 */
	return (string) apply_filters(
		'apg_guarantee_your_europe_url',
		'https://europa.eu/youreurope/' . $path,
		$language
	);
}
