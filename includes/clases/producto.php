<?php
/**
 * Places the notice on the product page and in the cart.
 *
 * Neither is required: the obligation is met at shop level. They are there for
 * a merchant who wants the notice next to the decision to buy, which is where
 * part of the German doctrine wants it, and where the Commission's guidelines
 * illustrate it too ("on the product catalogue page").
 *
 * Both have two routes, like the checkout, because a classic theme prints the
 * product and the cart from PHP templates with actions in them, while a block
 * theme prints them from blocks that fire none of those actions.
 *
 * @package APG_Legal_Guarantee_Notice
 */

defined( 'ABSPATH' ) || exit;

/**
 * The product the page is about, when it is a product page.
 *
 * @return WC_Product|null
 */
function apg_guarantee_current_product() {
	global $product;

	if ( $product instanceof WC_Product ) {
		return $product;
	}

	$id = get_the_ID();

	$found = $id && function_exists( 'wc_get_product' ) ? wc_get_product( $id ) : null;

	return $found instanceof WC_Product ? $found : null;
}

/**
 * Prints the notice on the product page, once per product.
 *
 * @return void
 */
function apg_guarantee_product_notice() {
	static $printed = array();

	$settings = apg_guarantee_get_settings();
	$mode     = (string) $settings['product_display'];
	$product  = apg_guarantee_current_product();

	if ( ! in_array( $mode, apg_guarantee_display_modes(), true ) || ! $product || isset( $printed[ $product->get_id() ] ) ) {
		return;
	}

	if ( apg_guarantee_no_goods( 'product', $product ) || ! apg_guarantee_show_notice( 'product' ) ) {
		return;
	}

	$printed[ $product->get_id() ] = true;

	echo '<div class="apg-guarantee-product">';
	apg_guarantee_print_placement( 'product', $mode );
	echo '</div>';
}
// Right under the add-to-cart form, in a classic theme and in a block theme
// alike, since the block renders the classic form.
add_action( 'woocommerce_after_add_to_cart_form', 'apg_guarantee_product_notice' );

/**
 * Prints the notice on the page of a product that cannot be bought right now.
 *
 * Such a product prints no add-to-cart form, so the hook above never fires.
 * Only then does the summary carry it: in a block theme WooCommerce fires the
 * summary hooks before the add-to-cart block, which would put the notice above
 * the form of every product that does have one.
 *
 * @return void
 */
function apg_guarantee_product_notice_fallback() {
	$product = apg_guarantee_current_product();

	if ( $product && ( ! $product->is_purchasable() || ! $product->is_in_stock() ) ) {
		apg_guarantee_product_notice();
	}
}
add_action( 'woocommerce_single_product_summary', 'apg_guarantee_product_notice_fallback', 35 );

/**
 * Prints the notice in the cart, under the totals.
 *
 * @return void
 */
function apg_guarantee_cart_notice() {
	$settings = apg_guarantee_get_settings();
	$mode     = (string) $settings['cart_display'];

	if ( ! in_array( $mode, apg_guarantee_display_modes(), true ) || apg_guarantee_no_goods( 'cart' ) || ! apg_guarantee_show_notice( 'cart' ) ) {
		return;
	}

	echo '<div class="apg-guarantee-cart">';
	apg_guarantee_print_placement( 'cart', $mode );
	echo '</div>';
}
add_action( 'woocommerce_after_cart_totals', 'apg_guarantee_cart_notice' );

/**
 * Adds the notice, and the GARAN label, to the blocks of a block theme.
 *
 * The add-to-cart block of the product template and the Cart block render
 * empty placeholders that React fills in, so the markup goes before or after
 * them, never inside, where it would be wiped on hydration.
 *
 * @param string $content Rendered block.
 * @param array  $block   Parsed block.
 * @return string
 */
function apg_guarantee_product_blocks( $content, $block ) {
	if ( is_admin() || empty( $block['blockName'] ) ) {
		return $content;
	}

	if ( in_array( $block['blockName'], array( 'woocommerce/add-to-cart-form', 'woocommerce/add-to-cart-with-options' ), true ) ) {
		ob_start();

		if ( function_exists( 'apg_guarantee_garan_product' ) ) {
			apg_guarantee_garan_product();
		}

		$before = (string) ob_get_clean();

		ob_start();
		apg_guarantee_product_notice();
		$after = (string) ob_get_clean();

		return $before . $content . $after;
	}

	if ( 'woocommerce/cart' === $block['blockName'] ) {
		ob_start();
		apg_guarantee_cart_notice();

		return $content . (string) ob_get_clean();
	}

	return $content;
}
add_filter( 'render_block', 'apg_guarantee_product_blocks', 10, 2 );
