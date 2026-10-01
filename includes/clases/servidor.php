<?php
/**
 * Serves the official notice at `?apg_guarantee_notice=<language>`.
 *
 * The files are stored gzipped, and every browser announces `Accept-Encoding:
 * gzip`, so the stored bytes are streamed straight out with
 * `Content-Encoding: gzip` and nothing is decompressed per request. The rare
 * client that does not accept gzip gets the file decompressed as it is sent.
 *
 * Both paths stream the file rather than echoing it into the response, so the
 * body never passes through a variable and there is no output to escape: an
 * image is not text and no escaping function applies to it.
 *
 * The notice never changes, so the response is immutable and cached for a year,
 * with an ETag so a revalidating client gets a 304 instead of the file.
 *
 * @package APG_Legal_Guarantee_Notice
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether PHP is configured to compress the response body on its own.
 *
 * `zlib.output_compression` is a boolean that also accepts a buffer size, so it
 * can read back as an empty string, as "1", as "On" or as "4096". Everything
 * other than the off values means PHP will compress.
 *
 * @return bool
 */
function apg_guarantee_php_compresses_output() {
	$value = strtolower( trim( (string) ini_get( 'zlib.output_compression' ) ) );

	return ! in_array( $value, array( '', '0', 'off', 'false', 'no' ), true );
}

/**
 * Detects the request for a notice and serves it.
 *
 * @return void
 */
function apg_guarantee_maybe_serve_notice() {
	/*
	 * Read through the query var this plugin registers, not through `$_GET`.
	 *
	 * It is the same value, but it is WordPress that parsed it, so there is no
	 * superglobal read to sanitise and no nonce sniff to suppress. A nonce would
	 * be the wrong answer here anyway: this is a public asset URL that sits in an
	 * `<img src>` and is cached for a year, and a nonce is per user and expires
	 * in a day.
	 */
	$language = sanitize_key( (string) get_query_var( 'apg_guarantee_notice' ) );

	if ( '' === $language ) {
		return;
	}

	$path = apg_guarantee_notice_path( $language );

	if ( '' === $path ) {
		status_header( 404 );
		exit;
	}

	// The file is bundled and immutable, so its size and date identify it
	// without having to read it: a revalidating client is answered without
	// touching the contents at all.
	$etag = sprintf( '"%s-%s-%s"', $language, filemtime( $path ), filesize( $path ) );

	header( 'Content-Type: image/svg+xml; charset=UTF-8' );
	header( 'Cache-Control: public, max-age=31536000, immutable' );
	header( 'ETag: ' . $etag );
	header( 'Vary: Accept-Encoding' );
	header( 'X-Content-Type-Options: nosniff' );

	$sent = isset( $_SERVER['HTTP_IF_NONE_MATCH'] )
		? trim( sanitize_text_field( wp_unslash( $_SERVER['HTTP_IF_NONE_MATCH'] ) ) )
		: '';

	if ( $sent === $etag ) {
		status_header( 304 );
		exit;
	}

	$accepts = isset( $_SERVER['HTTP_ACCEPT_ENCODING'] )
		? strtolower( sanitize_text_field( wp_unslash( $_SERVER['HTTP_ACCEPT_ENCODING'] ) ) )
		: '';

	while ( ob_get_level() > 0 ) {
		ob_end_clean();
	}

	// When PHP is set to compress output itself, handing it a body that is
	// already gzipped would get it compressed twice and no browser could read
	// it. In that case the file is decompressed on the way out and PHP does the
	// compressing, which is the one thing it is already going to do well.
	if ( ! apg_guarantee_php_compresses_output() && false !== strpos( $accepts, 'gzip' ) ) {
		header( 'Content-Encoding: gzip' );
		header( 'Content-Length: ' . filesize( $path ) );
		readfile( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readfile -- Streaming an image bundled with the plugin; WP_Filesystem has no streaming equivalent.
		exit;
	}

	readgzfile( $path );
	exit;
}
add_action( 'template_redirect', 'apg_guarantee_maybe_serve_notice' );

/**
 * Declares the query argument so WordPress does not swallow it.
 *
 * @param array $vars Public query variables.
 * @return array
 */
function apg_guarantee_query_vars( $vars ) {
	$vars[] = 'apg_guarantee_notice';

	return $vars;
}
add_filter( 'query_vars', 'apg_guarantee_query_vars' );
