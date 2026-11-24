<?php
/**
 * Whether there are goods in what is being sold.
 *
 * Article 22a is about goods: the legal guarantee of conformity of Directive
 * (EU) 2019/771 covers tangible items. A course, a subscription or a download
 * is not one, and a notice telling the buyer of an online course that the item
 * is guaranteed for two years is information that does not apply to them.
 *
 * WooCommerce has its own word for "not a tangible item": virtual. A
 * downloadable product that is not also virtual is still shipped, so it counts
 * as goods. That is the whole rule, and the `apg_guarantee_has_goods` filter is
 * there for any shop whose catalogue does not fit it.
 *
 * Only the placements tied to one purchase follow this: the checkout, the cart,
 * the product page and the order email. The floating button, the menu and the
 * footer are the shop-level reminder and stay where they are.
 *
 * @package APG_Legal_Guarantee_Notice
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether the merchant asked to limit the notice to purchases with goods.
 *
 * @return bool
 */
function apg_guarantee_goods_only() {
	$settings = apg_guarantee_get_settings();

	return '1' === (string) $settings['goods_only'];
}

/**
 * Whether one product is a good.
 *
 * A variable product counts as goods when any of its variations does, since
 * the product page is shown before the customer has chosen one.
 *
 * @param WC_Product|null $product Product.
 * @return bool
 */
function apg_guarantee_product_is_goods( $product ) {
	if ( ! $product instanceof WC_Product ) {
		return true;
	}

	if ( $product->is_type( 'variable' ) ) {
		foreach ( $product->get_children() as $child_id ) {
			$child = wc_get_product( $child_id );

			if ( $child && ! $child->is_virtual() ) {
				return true;
			}
		}

		return false;
	}

	return ! $product->is_virtual();
}

/**
 * Whether a placement tied to a purchase has goods in it.
 *
 * @param string                   $context `checkout`, `cart`, `product`, `email` or `email_attachment`.
 * @param WC_Order|WC_Product|null $subject Order or product, when there is one; the cart otherwise.
 * @return bool
 */
function apg_guarantee_has_goods( $context, $subject = null ) {
	$has = true;

	if ( $subject instanceof WC_Order ) {
		$has = false;

		foreach ( $subject->get_items() as $item ) {
			$product = $item instanceof WC_Order_Item_Product ? $item->get_product() : null;

			// A product deleted since the order was placed cannot be asked; it
			// was sold, so it is treated as what most things sold are.
			if ( ! $product || ! $product->is_virtual() ) {
				$has = true;
				break;
			}
		}
	} elseif ( $subject instanceof WC_Product ) {
		$has = apg_guarantee_product_is_goods( $subject );
	} elseif ( function_exists( 'WC' ) && WC()->cart && ! WC()->cart->is_empty() ) {
		$has = false;

		foreach ( WC()->cart->get_cart() as $item ) {
			$product = isset( $item['data'] ) ? $item['data'] : null;

			if ( ! $product instanceof WC_Product || ! $product->is_virtual() ) {
				$has = true;
				break;
			}
		}
	}

	/**
	 * Filters whether a purchase contains goods, and so whether the notice
	 * belongs in it.
	 *
	 * @param bool                     $has     Whether there are goods.
	 * @param string                   $context Placement being rendered.
	 * @param WC_Order|WC_Product|null $subject Order or product; null for the cart.
	 */
	return (bool) apply_filters( 'apg_guarantee_has_goods', $has, $context, $subject );
}

/**
 * Whether the setting rules the notice out of this placement.
 *
 * @param string                   $context Placement being rendered.
 * @param WC_Order|WC_Product|null $subject Order or product; null for the cart.
 * @return bool True when the notice should be left out.
 */
function apg_guarantee_no_goods( $context, $subject = null ) {
	return apg_guarantee_goods_only() && ! apg_guarantee_has_goods( $context, $subject );
}
