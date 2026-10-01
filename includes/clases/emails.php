<?php
/**
 * Adds the notice to the order confirmation email.
 *
 * The Commission's guidelines say the harmonised notice "should also be
 * included in the confirmation email". An email client will not honour a
 * `<details>` disclosure, so here the notice goes in as a plain image with the
 * Your Europe link beside it.
 *
 * @package APG_Legal_Guarantee_Notice
 */

defined( 'ABSPATH' ) || exit;

/**
 * Prints the notice inside the customer's order email.
 *
 * @param WC_Order $order         Order the email is about.
 * @param bool     $sent_to_admin Whether this copy goes to the shop.
 * @param bool     $plain_text    Whether the email is the plain-text version.
 * @return void
 */
function apg_guarantee_email_notice( $order, $sent_to_admin = false, $plain_text = false ) {
	$settings = apg_guarantee_get_settings();

	if ( '1' !== ( isset( $settings['show_email'] ) ? (string) $settings['show_email'] : '1' ) ) {
		return;
	}

	if ( $sent_to_admin ) {
		return;
	}

	$language = apg_guarantee_current_language();

	if ( '' === apg_guarantee_notice_path( $language ) ) {
		return;
	}

	$url       = apg_guarantee_notice_url( $language );
	$your_eu   = apg_guarantee_your_europe_url( $language );
	$intro     = apg_guarantee_trigger_text();

	if ( $plain_text ) {
		echo "\n" . esc_html( $intro ) . "\n";
		echo esc_url_raw( $your_eu ) . "\n";

		if ( '1' === (string) $settings['national_note'] ) {
			$text = trim( (string) $settings['national_note_text'] );
			echo esc_html( '' !== $text ? $text : apg_guarantee_default_national_note() ) . "\n";
		}

		return;
	}
	?>
	<div style="margin:24px 0;">
		<p style="margin:0 0 8px;"><strong><?php echo esc_html( $intro ); ?></strong></p>
		<img src="<?php echo esc_url( $url ); ?>" alt="<?php esc_attr_e( 'Official EU notice on the legal guarantee of conformity', 'apg-legal-guarantee-notice' ); ?>" style="max-width:100%;height:auto;">
		<p style="margin:8px 0 0;">
			<a href="<?php echo esc_url( $your_eu ); ?>"><?php esc_html_e( 'More about your guarantee rights in your country', 'apg-legal-guarantee-notice' ); ?></a>
		</p>
		<?php if ( '1' === (string) $settings['national_note'] ) : ?>
			<?php $text = trim( (string) $settings['national_note_text'] ); ?>
			<p style="margin:8px 0 0;"><?php echo esc_html( '' !== $text ? $text : apg_guarantee_default_national_note() ); ?></p>
		<?php endif; ?>
	</div>
	<?php
}
add_action( 'woocommerce_email_after_order_table', 'apg_guarantee_email_notice', 20, 3 );

/**
 * Attaches the official PDF of the notice to the customer order email.
 *
 * The image in the body is what the customer reads at a glance; the PDF is what
 * they can file or print, and it is the Commission's own file. It goes only on
 * the emails that carry the notice, so the shop's own copies are left alone and
 * nothing is attached when the merchant has switched the notice off.
 *
 * @param array    $adjuntos Attachment paths.
 * @param string   $id       Email id.
 * @param mixed    $objeto   Object the email is about.
 * @param WC_Email $correo   Email being sent.
 * @return array
 */
function apg_guarantee_email_attachment( $adjuntos, $id, $objeto = null, $correo = null ) {
	$adjuntos = (array) $adjuntos;
	$settings = apg_guarantee_get_settings();

	if ( '1' !== ( isset( $settings['show_email'] ) ? (string) $settings['show_email'] : '1' ) ) {
		return $adjuntos;
	}

	// Only the emails the customer receives, which are the ones the notice is
	// printed into. `customer_` is how WooCommerce names them.
	if ( ! is_string( $id ) || 0 !== strpos( $id, 'customer_' ) ) {
		return $adjuntos;
	}

	$pdf = apg_guarantee_notice_pdf_path( apg_guarantee_current_language() );

	if ( '' === $pdf || in_array( $pdf, $adjuntos, true ) ) {
		return $adjuntos;
	}

	$adjuntos[] = $pdf;

	return $adjuntos;
}
add_filter( 'woocommerce_email_attachments', 'apg_guarantee_email_attachment', 10, 4 );
