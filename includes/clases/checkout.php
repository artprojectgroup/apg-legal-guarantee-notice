<?php
/**
 * Places the notice at the checkout.
 *
 * The Commission's guidelines list the check-out page as one of the three
 * online placements. Getting there takes two different routes, because the two
 * checkouts of WooCommerce are two different things:
 *
 *  - The classic checkout is PHP, so an action in the right place is enough.
 *  - The Checkout block renders empty `<div data-block-name="…">` placeholders
 *    that React fills in on hydration. Anything injected inside one of them is
 *    wiped the moment the script runs, so the notice goes after the block
 *    instead, where it is outside the React root and survives. That puts it
 *    under the form rather than above the button, which the guidelines allow:
 *    what they ask for is the checkout page, prominently, not a precise spot.
 *
 * @package APG_Legal_Guarantee_Notice
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether the merchant asked for the notice at the checkout.
 *
 * @return bool
 */
function apg_guarantee_checkout_enabled() {
	$settings = apg_guarantee_get_settings();

	return '1' === ( isset( $settings['checkout_enabled'] ) ? (string) $settings['checkout_enabled'] : '1' );
}

/**
 * Prints the notice above the place-order button of the classic checkout.
 *
 * Prints rather than echoing the string above, so there is no output to escape
 * at the call site and no security sniff to suppress.
 *
 * @return void
 */
function apg_guarantee_checkout_notice() {
	if ( ! apg_guarantee_checkout_enabled() || apg_guarantee_no_goods( 'checkout' ) ) {
		return;
	}

	if ( ! apg_guarantee_show_notice( 'checkout' ) ) {
		apg_guarantee_notice_hidden( 'checkout' );

		return;
	}

	/**
	 * Fires right before the notice is printed at the checkout, in the
	 * customer email and in the `[apg_guarantee_notice]` shortcode.
	 *
	 * @param string        $context    `checkout`, `email` or `shortcode`.
	 * @param WC_Order|null $order      Order, in the email context; null elsewhere.
	 * @param bool          $plain_text Whether the output is a plain-text email.
	 */
	do_action( 'apg_guarantee_before_notice', 'checkout', null, false );

	$settings = apg_guarantee_get_settings();

	echo '<div class="apg-guarantee-checkout">';
	apg_guarantee_print_placement( 'checkout', (string) $settings['checkout_display'] );
	echo '</div>';

	/**
	 * Fires right after the notice is printed at the checkout, in the
	 * customer email and in the `[apg_guarantee_notice]` shortcode.
	 *
	 * @param string        $context    `checkout`, `email` or `shortcode`.
	 * @param WC_Order|null $order      Order, in the email context; null elsewhere.
	 * @param bool          $plain_text Whether the output is a plain-text email.
	 */
	do_action( 'apg_guarantee_after_notice', 'checkout', null, false );
}
add_action( 'woocommerce_review_order_before_submit', 'apg_guarantee_checkout_notice' );

/**
 * Returns what goes after the Checkout block: the GARAN labels of the order and
 * the notice, each only when there is something to show.
 *
 * @return string
 */
function apg_guarantee_checkout_markup() {
	ob_start();

	if ( function_exists( 'apg_guarantee_garan_checkout' ) ) {
		apg_guarantee_garan_checkout();
	}

	apg_guarantee_checkout_notice();

	return (string) ob_get_clean();
}

/**
 * Appends the notice after the Checkout block.
 *
 * @param string $content Rendered block.
 * @param array  $block   Parsed block.
 * @return string
 */
function apg_guarantee_checkout_block( $content, $block ) {
	if ( is_admin() || empty( $block['blockName'] ) || 'woocommerce/checkout' !== $block['blockName'] ) {
		return $content;
	}

	return $content . apg_guarantee_checkout_markup();
}
add_filter( 'render_block', 'apg_guarantee_checkout_block', 10, 2 );
