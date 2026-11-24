<?php
/**
 * Cleanup on uninstall.
 *
 * What goes: the settings, the cached rating, the EU GARAN fields of products
 * and variations, and the label images drawn into the uploads folder.
 *
 * What stays, on purpose:
 *
 *  - The page a merchant may have created from the settings screen, which is
 *    their content: a plugin does not delete pages somebody wrote.
 *  - What each order recorded (the GARAN label kept on its lines and the notes
 *    saying what the customer was shown). That is the record of a sale, and it
 *    has to outlive the plugin that wrote it.
 *
 * On a network it runs for every site, since each one keeps its own settings
 * and its own products.
 *
 * @package APG_Legal_Guarantee_Notice
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

/**
 * Removes the plugin's data from the current site.
 *
 * @return void
 */
function apg_guarantee_uninstall_site() {
	delete_option( 'apg_guarantee_settings' );
	delete_transient( 'apg_guarantee_plugin' );

	$apg_guarantee_keys = array( '_apg_garan_years', '_apg_garan_producer', '_apg_garan_model', '_apg_garan_image' );

	// The products that carry any of them, found before they are deleted.
	$apg_guarantee_query = array( 'relation' => 'OR' );

	foreach ( $apg_guarantee_keys as $apg_guarantee_key ) {
		$apg_guarantee_query[] = array(
			'key'     => $apg_guarantee_key,
			'compare' => 'EXISTS',
		);
	}

	$apg_guarantee_products = get_posts(
		array(
			'post_type'        => array( 'product', 'product_variation' ),
			'post_status'      => 'any',
			'posts_per_page'   => -1,
			'fields'           => 'ids',
			'meta_query'       => $apg_guarantee_query, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Runs once, on uninstall, to find the products to clean.
		)
	);

	foreach ( $apg_guarantee_keys as $apg_guarantee_key ) {
		delete_post_meta_by_key( $apg_guarantee_key );
	}

	// WooCommerce keeps each product's meta in a cache of its own, which only
	// its own save clears. Deleting the rows is not enough: with a persistent
	// object cache the shop would go on showing the label until that cache
	// expired.
	foreach ( $apg_guarantee_products as $apg_guarantee_product ) {
		clean_post_cache( $apg_guarantee_product );

		if ( class_exists( 'WC_Cache_Helper' ) ) {
			WC_Cache_Helper::invalidate_cache_group( 'object_' . $apg_guarantee_product );
		}
	}

	$apg_guarantee_uploads = wp_upload_dir( null, false );

	if ( ! empty( $apg_guarantee_uploads['error'] ) ) {
		return;
	}

	$apg_guarantee_dir = trailingslashit( $apg_guarantee_uploads['basedir'] ) . 'apg-legal-guarantee-notice';

	if ( ! is_dir( $apg_guarantee_dir ) ) {
		return;
	}

	// Two patterns rather than GLOB_BRACE, which some systems (musl) lack.
	foreach ( array( 'svg', 'png' ) as $apg_guarantee_ext ) {
		foreach ( (array) glob( $apg_guarantee_dir . '/garan-*.' . $apg_guarantee_ext ) as $apg_guarantee_file ) {
			wp_delete_file( $apg_guarantee_file );
		}
	}

	// Only if nothing else was left in it.
	if ( ! glob( $apg_guarantee_dir . '/*' ) ) {
		rmdir( $apg_guarantee_dir ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_rmdir -- Removing the plugin's own, now empty, uploads folder.
	}
}

if ( is_multisite() ) {
	$apg_guarantee_sites = get_sites(
		array(
			'fields' => 'ids',
			'number' => 0,
		)
	);

	foreach ( $apg_guarantee_sites as $apg_guarantee_site ) {
		switch_to_blog( $apg_guarantee_site );
		apg_guarantee_uninstall_site();
		restore_current_blog();
	}
} else {
	apg_guarantee_uninstall_site();
}
