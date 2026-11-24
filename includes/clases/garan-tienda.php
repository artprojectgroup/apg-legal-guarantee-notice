<?php
/**
 * Shows the EU GARAN label wherever the guidelines ask for it.
 *
 * The seller's obligation is to show the label "in a prominent manner in a way
 * that allows consumers to easily identify which particular good benefits"
 * from it. The guidelines then name three places: with the product before
 * purchase, directly before the order is placed at the checkout, and in the
 * confirmation email. This file covers the three, plus the cart and the order
 * screen, where it is a line of text.
 *
 * What was shown at the checkout is kept on the order line, so the email, the
 * order screen and an invoice printed a year later all say what the customer
 * was told when they bought, not what the product says today.
 *
 * @package APG_Legal_Guarantee_Notice
 */

defined( 'ABSPATH' ) || exit;

/**
 * How the label is shown on the product page: the full label or the nested one.
 *
 * @return string `label` or `nested`.
 */
function apg_guarantee_garan_product_mode() {
	$settings = apg_guarantee_get_settings();

	return 'nested' === (string) $settings['garan_product'] ? 'nested' : 'label';
}

/**
 * Prints the label on the product page.
 *
 * A variable product prints the container even without a label of its own,
 * because the label usually belongs to a variation: the script swaps the
 * variation's label in when the customer picks it, and puts the product's back
 * when they clear the choice.
 *
 * @return void
 */
function apg_guarantee_garan_product() {
	static $printed = array();

	$product = function_exists( 'apg_guarantee_current_product' ) ? apg_guarantee_current_product() : null;

	if ( ! $product || isset( $printed[ $product->get_id() ] ) ) {
		return;
	}

	$data     = apg_guarantee_garan_data( $product );
	$variable = $product->is_type( 'variable' );

	if ( ! $data && ! ( $variable && apg_guarantee_garan_variations_have_label( $product ) ) ) {
		return;
	}

	$printed[ $product->get_id() ] = true;

	if ( $variable ) {
		wp_enqueue_script( 'apg-legal-guarantee-notice-garan' );
	}
	?>
	<div class="apg-garan-product" data-apg-garan-product="<?php echo esc_attr( (string) $product->get_id() ); ?>">
		<?php
		if ( $data ) {
			apg_guarantee_garan_print( $data, apg_guarantee_garan_product_mode(), 'product' );
		}
		?>
	</div>
	<?php
}
add_action( 'woocommerce_single_product_summary', 'apg_guarantee_garan_product', 25 );

/**
 * Whether any variation of a product carries the label.
 *
 * @param WC_Product $product Variable product.
 * @return bool
 */
function apg_guarantee_garan_variations_have_label( $product ) {
	foreach ( $product->get_children() as $child_id ) {
		if ( apg_guarantee_garan_data( $child_id ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Registers the script that swaps the label when a variation is chosen.
 *
 * @return void
 */
function apg_guarantee_garan_register_script() {
	wp_register_script(
		'apg-legal-guarantee-notice-garan',
		plugins_url( 'assets/js/garan.js', apg_guarantee_DIRECCION ),
		array( 'jquery' ),
		apg_guarantee_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'apg_guarantee_garan_register_script' );

/**
 * Hands each variation its own label, ready to drop into the page.
 *
 * @param array                $data      Variation data sent to the browser.
 * @param WC_Product           $product   Variable product.
 * @param WC_Product_Variation $variation Variation.
 * @return array
 */
function apg_guarantee_garan_variation_data( $data, $product, $variation ) {
	$garan = apg_guarantee_garan_data( $variation );

	$data['apg_garan_html'] = $garan ? apg_guarantee_garan_markup( $garan, apg_guarantee_garan_product_mode(), 'product' ) : '';

	return $data;
}
add_filter( 'woocommerce_available_variation', 'apg_guarantee_garan_variation_data', 10, 3 );

/**
 * Names the guarantee on each cart line, in text.
 *
 * Text rather than the label, because this list is printed in the cart, the
 * mini cart and the checkout summary, by templates and by blocks alike, and
 * a line of item data is the one thing all of them show. The label itself
 * goes before the place-order button.
 *
 * @param array $item_data Data shown under the line.
 * @param array $cart_item Cart line.
 * @return array
 */
function apg_guarantee_garan_item_data( $item_data, $cart_item ) {
	$product = isset( $cart_item['data'] ) ? $cart_item['data'] : null;
	$data    = $product instanceof WC_Product ? apg_guarantee_garan_data( $product ) : null;

	if ( $data ) {
		$item_data[] = array(
			'key'   => __( 'Commercial guarantee of durability', 'apg-legal-guarantee-notice' ),
			'value' => apg_guarantee_garan_summary( $data ),
		);
	}

	return $item_data;
}
add_filter( 'woocommerce_get_item_data', 'apg_guarantee_garan_item_data', 10, 2 );

/**
 * The guarantee in one line of text.
 *
 * @param array $data GARAN data.
 * @return string
 */
function apg_guarantee_garan_summary( $data ) {
	$years = apg_guarantee_garan_years_label( $data['years'] );

	if ( '' === (string) $data['producer'] ) {
		/* translators: %s: duration in years, e.g. 5 or 2,5. */
		return sprintf( __( '%s years, EU GARAN label', 'apg-legal-guarantee-notice' ), $years );
	}

	/* translators: 1: duration in years, e.g. 5 or 2,5. 2: producer. */
	return sprintf( __( '%1$s years from %2$s, EU GARAN label', 'apg-legal-guarantee-notice' ), $years, $data['producer'] );
}

/**
 * The GARAN labels of what is in the cart, one per distinct label.
 *
 * @return array<int,array{name:string,data:array}>
 */
function apg_guarantee_garan_cart_labels() {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return array();
	}

	$labels = array();

	foreach ( WC()->cart->get_cart() as $item ) {
		$product = isset( $item['data'] ) ? $item['data'] : null;
		$data    = $product instanceof WC_Product ? apg_guarantee_garan_data( $product ) : null;

		if ( $data ) {
			$labels[ apg_guarantee_garan_hash( $data ) ] = array(
				'name' => $product->get_name(),
				'data' => $data,
			);
		}
	}

	return array_values( $labels );
}

/**
 * Prints the labels of the order right before the place-order button.
 *
 * The guidelines want the label "shown directly before the consumer places
 * his/her order at the product check-out page". Here it is nested, one per
 * product, each opening the full label on the first click.
 *
 * @return void
 */
function apg_guarantee_garan_checkout() {
	$labels = apg_guarantee_garan_cart_labels();

	if ( ! $labels ) {
		return;
	}

	wp_enqueue_style( 'apg-legal-guarantee-notice' );
	?>
	<div class="apg-garan-checkout">
		<p class="apg-garan-checkout__title"><?php esc_html_e( 'Commercial guarantee of durability offered by the producer', 'apg-legal-guarantee-notice' ); ?></p>
		<ul class="apg-garan-checkout__list">
			<?php foreach ( $labels as $label ) : ?>
				<li class="apg-garan-checkout__item">
					<span class="apg-garan-checkout__name"><?php echo esc_html( $label['name'] ); ?></span>
					<?php apg_guarantee_garan_print_nested( $label['data'], 'checkout' ); ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php
}
add_action( 'woocommerce_review_order_before_submit', 'apg_guarantee_garan_checkout', 5 );

/**
 * Keeps on the order line the label the customer was shown.
 *
 * Runs for the classic checkout and for the Checkout block alike, since both
 * build the order lines through WooCommerce's checkout.
 *
 * @param WC_Order_Item_Product $item   Order line being created.
 * @param string                $key    Cart line key.
 * @param array                 $values Cart line.
 * @return void
 */
function apg_guarantee_garan_order_line( $item, $key, $values ) {
	$product = isset( $values['data'] ) ? $values['data'] : null;
	$data    = $product instanceof WC_Product ? apg_guarantee_garan_data( $product ) : null;

	if ( $data ) {
		$item->add_meta_data( '_apg_garan', $data, true );
	}
}
add_action( 'woocommerce_checkout_create_order_line_item', 'apg_guarantee_garan_order_line', 10, 3 );

/**
 * The labels kept on an order, one per distinct label.
 *
 * @param WC_Order $order Order.
 * @return array<int,array{name:string,data:array}>
 */
function apg_guarantee_garan_order_labels( $order ) {
	if ( ! $order instanceof WC_Order ) {
		return array();
	}

	$labels = array();

	foreach ( $order->get_items() as $item ) {
		$data = $item->get_meta( '_apg_garan', true );

		if ( is_array( $data ) && ! empty( $data['years'] ) ) {
			$data = wp_parse_args(
				$data,
				array(
					'producer'   => '',
					'model'      => '',
					'image'      => '0',
					'product_id' => 0,
				)
			);

			$labels[ apg_guarantee_garan_hash( $data ) ] = array(
				'name' => $item->get_name(),
				'data' => $data,
			);
		}
	}

	return array_values( $labels );
}

/**
 * Hides the stored label from the list of line meta, which would print the
 * raw array; the order screen gets a readable line instead.
 *
 * @param array $hidden Meta keys hidden on the order screen.
 * @return array
 */
function apg_guarantee_garan_hidden_meta( $hidden ) {
	$hidden[] = '_apg_garan';

	return $hidden;
}
add_filter( 'woocommerce_hidden_order_itemmeta', 'apg_guarantee_garan_hidden_meta' );

/**
 * Shows on the order screen which label each line was sold with.
 *
 * @param int           $item_id Line ID.
 * @param WC_Order_Item $item    Line.
 * @return void
 */
function apg_guarantee_garan_admin_line( $item_id, $item ) {
	$data = $item instanceof WC_Order_Item_Product ? $item->get_meta( '_apg_garan', true ) : null;

	if ( ! is_array( $data ) || empty( $data['years'] ) ) {
		return;
	}

	$data = wp_parse_args(
		$data,
		array(
			'producer' => '',
			'model'    => '',
		)
	);
	?>
	<p class="apg-garan-admin-line">
		<strong><?php esc_html_e( 'Commercial guarantee of durability', 'apg-legal-guarantee-notice' ); ?>:</strong>
		<?php echo esc_html( apg_guarantee_garan_summary( $data ) ); ?>
		<?php if ( '' !== (string) $data['model'] ) : ?>
			&middot; <?php echo esc_html( $data['model'] ); ?>
		<?php endif; ?>
	</p>
	<?php
}
add_action( 'woocommerce_after_order_itemmeta', 'apg_guarantee_garan_admin_line', 10, 2 );

/**
 * Prints the labels of the order in the customer's emails.
 *
 * The full label, as a PNG, since an email client shows no SVG and opens no
 * popover. Where this server cannot draw it, each line says the guarantee in
 * words and links to Your Europe, which is the information the label carries.
 *
 * @param WC_Order $order         Order the email is about.
 * @param bool     $sent_to_admin Whether this copy goes to the shop.
 * @param bool     $plain_text    Whether the email is the plain-text version.
 * @return void
 */
function apg_guarantee_garan_email( $order, $sent_to_admin = false, $plain_text = false ) {
	if ( $sent_to_admin ) {
		return;
	}

	$labels = apg_guarantee_garan_order_labels( $order );

	if ( ! $labels ) {
		return;
	}

	$title = __( 'Commercial guarantee of durability offered by the producer', 'apg-legal-guarantee-notice' );

	if ( $plain_text ) {
		echo "\n" . esc_html( $title ) . "\n";

		foreach ( $labels as $label ) {
			echo esc_html( $label['name'] . ': ' . apg_guarantee_garan_summary( $label['data'] ) ) . "\n";
		}

		echo esc_url_raw( apg_guarantee_garan_your_europe_url() ) . "\n";

		return;
	}
	?>
	<div style="margin:24px 0;">
		<p style="margin:0 0 8px;"><strong><?php echo esc_html( $title ); ?></strong></p>
		<?php foreach ( $labels as $label ) : ?>
			<?php $png = apg_guarantee_garan_png_url( $label['data'], 'label' ); ?>
			<p style="margin:0 0 4px;"><?php echo esc_html( $label['name'] . ': ' . apg_guarantee_garan_summary( $label['data'] ) ); ?></p>
			<?php if ( '' !== $png ) : ?>
				<p style="margin:0 0 12px;">
					<img src="<?php echo esc_url( $png ); ?>" alt="<?php echo esc_attr( apg_guarantee_garan_alt( $label['data'] ) ); ?>" width="269" style="width:269px;max-width:100%;height:auto;">
				</p>
			<?php endif; ?>
		<?php endforeach; ?>
		<p style="margin:0;">
			<a href="<?php echo esc_url( apg_guarantee_garan_your_europe_url() ); ?>"><?php echo esc_html( apg_guarantee_garan_link_text() ); ?></a>
		</p>
	</div>
	<?php
}
add_action( 'woocommerce_email_after_order_table', 'apg_guarantee_garan_email', 15, 3 );
