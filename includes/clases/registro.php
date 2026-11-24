<?php
/**
 * Leaves a record on each order of what the customer was shown.
 *
 * Showing the notice is the obligation; being able to say afterwards that it
 * was shown is what a merchant needs when a customer or an authority asks. A
 * private note on the order is where WooCommerce keeps that kind of fact, it
 * travels with the order and it costs one row.
 *
 * Two moments are recorded: the checkout, with how the notice was shown and in
 * which language, and the first customer email that carried the official PDF.
 *
 * @package APG_Legal_Guarantee_Notice
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether the merchant wants the record.
 *
 * @return bool
 */
function apg_guarantee_order_note_enabled() {
	$settings = apg_guarantee_get_settings();

	return '1' === (string) $settings['order_note'];
}

/**
 * Why the checkout showed no notice, or an empty string when it did.
 *
 * @param WC_Order $order Order just placed.
 * @return string
 */
function apg_guarantee_checkout_missing_reason( $order ) {
	if ( ! apg_guarantee_checkout_enabled() ) {
		return __( 'the checkout placement is switched off', 'apg-legal-guarantee-notice' );
	}

	if ( apg_guarantee_no_goods( 'checkout', $order ) ) {
		return __( 'the order has no goods', 'apg-legal-guarantee-notice' );
	}

	if ( ! apg_guarantee_available() ) {
		return __( 'there is no notice file for the language of the visitor', 'apg-legal-guarantee-notice' );
	}

	if ( ! apg_guarantee_show_notice( 'checkout' ) ) {
		return __( 'the apg_guarantee_show_notice filter hid it', 'apg-legal-guarantee-notice' );
	}

	return '';
}

/**
 * Writes the checkout record on a new order.
 *
 * @param WC_Order $order Order just placed.
 * @return void
 */
function apg_guarantee_record_checkout( $order ) {
	if ( ! $order instanceof WC_Order || ! apg_guarantee_order_note_enabled() || $order->get_meta( '_apg_guarantee_recorded', true ) ) {
		return;
	}

	$settings = apg_guarantee_get_settings();
	$reason   = apg_guarantee_checkout_missing_reason( $order );
	$language = strtoupper( apg_guarantee_current_language() );

	if ( '' === $reason ) {
		$lines = array(
			sprintf(
				/* translators: 1: two-letter language code, e.g. ES. 2: how it was shown. */
				__( 'EU legal guarantee notice shown at the checkout, in %1$s: %2$s.', 'apg-legal-guarantee-notice' ),
				$language,
				'full' === (string) $settings['checkout_display']
					? __( 'the whole notice, on the page', 'apg-legal-guarantee-notice' )
					: __( 'a sentence that opens it on the first click', 'apg-legal-guarantee-notice' )
			),
		);
	} else {
		$lines = array(
			sprintf(
				/* translators: %s: reason, e.g. "the order has no goods". */
				__( 'EU legal guarantee notice not shown at the checkout: %s.', 'apg-legal-guarantee-notice' ),
				$reason
			),
		);
	}

	if ( function_exists( 'apg_guarantee_garan_order_labels' ) ) {
		foreach ( apg_guarantee_garan_order_labels( $order ) as $label ) {
			$lines[] = sprintf(
				/* translators: 1: product name. 2: the guarantee in one line, e.g. "5 years from Acme, EU GARAN label". */
				__( 'EU GARAN label shown for %1$s: %2$s.', 'apg-legal-guarantee-notice' ),
				$label['name'],
				apg_guarantee_garan_summary( $label['data'] )
			);
		}
	}

	$lines[] = sprintf( 'APG Legal Guarantee Notice %s', apg_guarantee_VERSION );

	$order->add_order_note( implode( "\n", $lines ) );
	$order->update_meta_data( '_apg_guarantee_recorded', '1' );
	$order->save();
}

/**
 * Classic checkout.
 *
 * @param int      $order_id    Order ID.
 * @param array    $posted_data Posted checkout data.
 * @param WC_Order $order       Order.
 * @return void
 */
function apg_guarantee_record_classic_checkout( $order_id, $posted_data, $order ) {
	apg_guarantee_record_checkout( $order instanceof WC_Order ? $order : wc_get_order( $order_id ) );
}
add_action( 'woocommerce_checkout_order_processed', 'apg_guarantee_record_classic_checkout', 20, 3 );
add_action( 'woocommerce_store_api_checkout_order_processed', 'apg_guarantee_record_checkout', 20 );

/**
 * The orders whose email is about to carry the official PDF, by order ID.
 *
 * @param int    $order_id Order ID to add, or 0 to only read.
 * @param string $email_id Email the PDF is attached to.
 * @return array<int,string>
 */
function apg_guarantee_pdf_pending( $order_id = 0, $email_id = '' ) {
	static $pending = array();

	if ( $order_id ) {
		$pending[ $order_id ] = $email_id;
	}

	return $pending;
}

/**
 * Records the first email that carried the official PDF, once it is sent.
 *
 * @param bool     $sent     Whether the email went out.
 * @param string   $email_id Email ID.
 * @param WC_Email $email    Email.
 * @return void
 */
function apg_guarantee_record_pdf( $sent, $email_id, $email = null ) {
	if ( ! $sent || ! apg_guarantee_order_note_enabled() || ! is_object( $email ) || empty( $email->object ) || ! $email->object instanceof WC_Order ) {
		return;
	}

	$order   = $email->object;
	$pending = apg_guarantee_pdf_pending();

	if ( ! isset( $pending[ $order->get_id() ] ) || $pending[ $order->get_id() ] !== $email_id || $order->get_meta( '_apg_guarantee_pdf_recorded', true ) ) {
		return;
	}

	$order->add_order_note(
		sprintf(
			/* translators: 1: two-letter language code, e.g. ES. 2: email title, e.g. "Processing order". */
			__( 'Official PDF of the EU legal guarantee notice, in %1$s, attached to the customer email "%2$s".', 'apg-legal-guarantee-notice' ),
			strtoupper( apg_guarantee_current_language() ),
			method_exists( $email, 'get_title' ) ? $email->get_title() : $email_id
		)
	);
	$order->update_meta_data( '_apg_guarantee_pdf_recorded', '1' );
	$order->save();
}
add_action( 'woocommerce_email_sent', 'apg_guarantee_record_pdf', 10, 3 );
