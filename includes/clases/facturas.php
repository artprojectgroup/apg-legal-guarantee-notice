<?php
/**
 * Adds the guarantee to the PDF invoices of "PDF Invoices & Packing Slips for
 * WooCommerce" (WP Overnight).
 *
 * An invoice is what a customer keeps and what they bring when they claim the
 * guarantee, so it is a good place for a reminder of it. Not the notice itself:
 * it is an A4 page of its own and it already travels as a PDF with the order
 * email. What goes on the invoice is a short line with the Your Europe link of
 * the customer's language, the national note, and the GARAN label of each line
 * that has one, in words.
 *
 * Nothing here runs unless that plugin is active, because the hooks below are
 * only ever fired by it.
 *
 * @package APG_Legal_Guarantee_Notice
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether the merchant wants the guarantee on the invoices.
 *
 * @return bool
 */
function apg_guarantee_invoice_enabled() {
	$settings = apg_guarantee_get_settings();

	return '1' === (string) $settings['invoice_enabled'];
}

/**
 * The document types the reminder goes on.
 *
 * @return array<int,string>
 */
function apg_guarantee_invoice_types() {
	/**
	 * Filters the WP Overnight document types that carry the reminder.
	 *
	 * @param array $types Document types, `invoice` by default.
	 */
	return (array) apply_filters( 'apg_guarantee_invoice_document_types', array( 'invoice' ) );
}

/**
 * Prints the reminder under the order details of the invoice.
 *
 * @param string   $document_type Document type.
 * @param WC_Order $order         Order.
 * @return void
 */
function apg_guarantee_invoice_notice( $document_type, $order ) {
	if ( ! apg_guarantee_invoice_enabled() || ! in_array( $document_type, apg_guarantee_invoice_types(), true ) || ! $order instanceof WC_Order ) {
		return;
	}

	if ( apg_guarantee_no_goods( 'invoice', $order ) || ! apg_guarantee_show_notice( 'invoice', $order ) ) {
		return;
	}

	$settings = apg_guarantee_get_settings();
	$language = apg_guarantee_current_language();
	$note     = apg_guarantee_national_note_text( $settings, 'invoice', $order );
	?>
	<div class="apg-guarantee-invoice" style="margin-top:12px;font-size:8pt;">
		<p style="margin:0;"><strong><?php echo esc_html( apg_guarantee_trigger_text( 'invoice' ) ); ?></strong></p>
		<p style="margin:2px 0 0;">
			<?php esc_html_e( 'Under EU law the goods you have bought are covered by a legal guarantee of conformity of at least two years from delivery: if they are not in conformity, you are entitled to have them repaired or replaced free of charge, or to a price reduction or a refund.', 'apg-legal-guarantee-notice' ); ?>
		</p>
		<?php if ( '' !== $note ) : ?>
			<p style="margin:2px 0 0;"><?php echo esc_html( $note ); ?></p>
		<?php endif; ?>
		<p style="margin:2px 0 0;"><?php echo esc_html( apg_guarantee_your_europe_url( $language ) ); ?></p>
	</div>
	<?php
}
add_action( 'wpo_wcpdf_after_order_details', 'apg_guarantee_invoice_notice', 10, 2 );

/**
 * Prints the GARAN label of a line, in words, under its meta.
 *
 * @param string              $document_type Document type.
 * @param array|WC_Order_Item $item          Line, as the invoice plugin hands it.
 * @param WC_Order            $order         Order.
 * @return void
 */
function apg_guarantee_invoice_garan( $document_type, $item, $order = null ) {
	if ( ! apg_guarantee_invoice_enabled() || ! in_array( $document_type, apg_guarantee_invoice_types(), true ) ) {
		return;
	}

	// The invoice plugin passes its own array for each line, with the order
	// line under `item`; older versions passed the line itself.
	$line = is_array( $item ) && isset( $item['item'] ) ? $item['item'] : $item;
	$data = $line instanceof WC_Order_Item_Product ? $line->get_meta( '_apg_garan', true ) : null;

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
	<div class="apg-garan-invoice" style="font-size:7pt;">
		<?php
		echo esc_html( __( 'Commercial guarantee of durability', 'apg-legal-guarantee-notice' ) . ': ' . apg_guarantee_garan_summary( $data ) );

		if ( '' !== (string) $data['model'] ) {
			echo ' &middot; ' . esc_html( $data['model'] );
		}
		?>
	</div>
	<?php
}
add_action( 'wpo_wcpdf_after_item_meta', 'apg_guarantee_invoice_garan', 10, 3 );
