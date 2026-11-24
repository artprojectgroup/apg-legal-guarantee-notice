<?php
/**
 * The EU GARAN fields of a product and of each variation.
 *
 * The guarantee is the producer's, so these fields are for copying what the
 * producer has said, not for the shop to decide: the duration in years, the
 * producer's name and the model identifier, the three editable fields of the
 * label. A producer that hands over the label itself can be uploaded instead.
 *
 * @package APG_Legal_Guarantee_Notice
 */

defined( 'ABSPATH' ) || exit;

/**
 * Adds the "EU GARAN" tab to the product data box.
 *
 * @param array $tabs Product data tabs.
 * @return array
 */
function apg_guarantee_garan_tab( $tabs ) {
	$tabs['apg_garan'] = array(
		'label'    => __( 'EU GARAN', 'apg-legal-guarantee-notice' ),
		'target'   => 'apg_garan_product_data',
		'class'    => array(),
		'priority' => 75,
	);

	return $tabs;
}
add_filter( 'woocommerce_product_data_tabs', 'apg_guarantee_garan_tab' );

/**
 * Prints the panel of the tab.
 *
 * @return void
 */
function apg_guarantee_garan_panel() {
	global $post;

	$product = $post ? wc_get_product( $post->ID ) : null;

	if ( ! $product ) {
		return;
	}

	$image = absint( $product->get_meta( '_apg_garan_image', true ) );
	$data  = apg_guarantee_garan_data( $product );
	?>
	<div id="apg_garan_product_data" class="panel woocommerce_options_panel hidden">
		<div class="options_group">
			<p class="form-field apg-garan-intro">
				<?php esc_html_e( 'Fill this in only when the producer offers a commercial guarantee of durability for this product: free of charge, for the whole product and for more than two years. The label shows it; the three fields below are the ones the producer gives you, and the label stays off until they are all there.', 'apg-legal-guarantee-notice' ); ?>
			</p>
			<?php
			woocommerce_wp_text_input(
				array(
					'id'          => '_apg_garan_years',
					'label'       => __( 'Duration in years', 'apg-legal-guarantee-notice' ),
					'placeholder' => __( 'e.g. 5 or 2,5', 'apg-legal-guarantee-notice' ),
					'desc_tip'    => true,
					'description' => __( 'More than two years, in whole or half years: 3, 5, 10 or 2,5. Other decimals are not allowed on the label.', 'apg-legal-guarantee-notice' ),
					'value'       => apg_guarantee_garan_years_label( (string) $product->get_meta( '_apg_garan_years', true ) ),
				)
			);

			woocommerce_wp_text_input(
				array(
					'id'          => '_apg_garan_producer',
					'label'       => __( 'Producer', 'apg-legal-guarantee-notice' ),
					'desc_tip'    => true,
					'description' => __( 'The name or brand of the producer offering the guarantee, as the producer gives it.', 'apg-legal-guarantee-notice' ),
					'value'       => (string) $product->get_meta( '_apg_garan_producer', true ),
				)
			);

			woocommerce_wp_text_input(
				array(
					'id'          => '_apg_garan_model',
					'label'       => __( 'Model identifier', 'apg-legal-guarantee-notice' ),
					'desc_tip'    => true,
					'description' => __( 'The model the guarantee covers. In a variable product each variation can set its own, and leaves the rest to these fields.', 'apg-legal-guarantee-notice' ),
					'value'       => (string) $product->get_meta( '_apg_garan_model', true ),
				)
			);
			?>
			<p class="form-field">
				<label for="_apg_garan_image"><?php esc_html_e( 'Label from the producer', 'apg-legal-guarantee-notice' ); ?></label>
				<input type="hidden" id="_apg_garan_image" name="_apg_garan_image" value="<?php echo esc_attr( $image ? (string) $image : '' ); ?>">
				<button type="button" class="button apg-garan-image-choose"><?php esc_html_e( 'Choose image', 'apg-legal-guarantee-notice' ); ?></button>
				<button type="button" class="button-link apg-garan-image-remove"<?php echo $image ? '' : ' hidden'; ?>><?php esc_html_e( 'Remove', 'apg-legal-guarantee-notice' ); ?></button>
				<span class="description"><?php esc_html_e( 'Optional. If the producer gave you the label already filled in, upload it here and it is shown as it came instead of being drawn.', 'apg-legal-guarantee-notice' ); ?></span>
				<span class="apg-garan-image-preview">
					<?php
					if ( $image ) {
						echo wp_get_attachment_image( $image, 'thumbnail' );
					}
					?>
				</span>
			</p>
			<?php if ( $data ) : ?>
				<p class="form-field apg-garan-preview">
					<label><?php esc_html_e( 'Preview', 'apg-legal-guarantee-notice' ); ?></label>
					<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage -- Generated label, it has no attachment ID. ?>
					<img src="<?php echo esc_url( apg_guarantee_garan_url( $data, 'label' ) ); ?>" alt="<?php echo esc_attr( apg_guarantee_garan_alt( $data ) ); ?>" width="269" height="283">
				</p>
			<?php endif; ?>
		</div>
	</div>
	<?php
}
add_action( 'woocommerce_product_data_panels', 'apg_guarantee_garan_panel' );

/**
 * Validates and stores one set of GARAN fields on a product or variation.
 *
 * @param WC_Product $product  Product or variation.
 * @param string     $years    Duration as typed.
 * @param string     $producer Producer as typed.
 * @param string     $model    Model as typed.
 * @param string     $label    How to name it in a message.
 * @return void
 */
function apg_guarantee_garan_store_fields( $product, $years, $producer, $model, $label ) {
	$years    = trim( $years );
	$normal   = apg_guarantee_garan_normalize_years( $years );
	$producer = sanitize_text_field( $producer );
	$model    = sanitize_text_field( $model );

	if ( '' !== $years && '' === $normal ) {
		WC_Admin_Meta_Boxes::add_error(
			sprintf(
				/* translators: 1: product or variation name. 2: the value typed. */
				__( 'EU GARAN, %1$s: "%2$s" is not a valid duration. It has to be more than two years, in whole or half years, such as 3, 5 or 2,5. The duration has not been saved.', 'apg-legal-guarantee-notice' ),
				$label,
				$years
			)
		);
	} else {
		$product->update_meta_data( '_apg_garan_years', $normal );
	}

	$product->update_meta_data( '_apg_garan_producer', $producer );
	$product->update_meta_data( '_apg_garan_model', $model );
}

/**
 * Saves the fields of a simple or parent product.
 *
 * @param WC_Product $product Product being saved.
 * @return void
 */
function apg_guarantee_garan_save_product( $product ) {
	// WooCommerce has checked this nonce before firing the hook. It is checked
	// again here so this function does not depend on who calls it.
	if ( ! isset( $_POST['woocommerce_meta_nonce'] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['woocommerce_meta_nonce'] ) ), 'woocommerce_save_data' ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $product->get_id() ) ) {
		return;
	}

	$years    = isset( $_POST['_apg_garan_years'] ) ? sanitize_text_field( wp_unslash( $_POST['_apg_garan_years'] ) ) : '';
	$producer = isset( $_POST['_apg_garan_producer'] ) ? sanitize_text_field( wp_unslash( $_POST['_apg_garan_producer'] ) ) : '';
	$model    = isset( $_POST['_apg_garan_model'] ) ? sanitize_text_field( wp_unslash( $_POST['_apg_garan_model'] ) ) : '';
	$image    = isset( $_POST['_apg_garan_image'] ) ? absint( wp_unslash( $_POST['_apg_garan_image'] ) ) : 0;

	apg_guarantee_garan_store_fields( $product, $years, $producer, $model, $product->get_name() );

	$product->update_meta_data( '_apg_garan_image', $image && wp_attachment_is_image( $image ) ? (string) $image : '' );

	apg_guarantee_garan_warn( $product, $product->get_name() );
}
add_action( 'woocommerce_admin_process_product_object', 'apg_guarantee_garan_save_product' );

/**
 * Tells the merchant when what was saved will not show the way they expect.
 *
 * @param WC_Product $product Product or variation, with its new values set.
 * @param string     $label   How to name it in a message.
 * @return void
 */
function apg_guarantee_garan_warn( $product, $label ) {
	$years    = (string) $product->get_meta( '_apg_garan_years', true );
	$producer = (string) $product->get_meta( '_apg_garan_producer', true );
	$model    = (string) $product->get_meta( '_apg_garan_model', true );

	if ( '' === $years && '' === $producer && '' === $model ) {
		return;
	}

	$data = apg_guarantee_garan_data( $product );

	if ( ! $data ) {
		WC_Admin_Meta_Boxes::add_error(
			sprintf(
				/* translators: %s: product or variation name. */
				__( 'EU GARAN, %s: the label is not shown until the duration, the producer and the model identifier are all filled in, or a label from the producer is uploaded.', 'apg-legal-guarantee-notice' ),
				$label
			)
		);

		return;
	}

	$layout = apg_guarantee_garan_layout( $data );

	if ( $layout['producer']['size'] < 9 && '0' === (string) $data['image'] ) {
		WC_Admin_Meta_Boxes::add_error(
			sprintf(
				/* translators: %s: product or variation name. */
				__( 'EU GARAN, %s: the producer and the model identifier are too long for the line they share on the label, so they are printed smaller than the template sets them. Check the preview and shorten them if you can.', 'apg-legal-guarantee-notice' ),
				$label
			)
		);
	}
}

/**
 * Prints the GARAN fields of one variation.
 *
 * Each field shows what it inherits from the product as its placeholder, so
 * leaving one empty is visibly a choice to inherit.
 *
 * @param int     $loop           Index of the variation in the form.
 * @param array   $variation_data Variation data.
 * @param WP_Post $variation      Variation post.
 * @return void
 */
function apg_guarantee_garan_variation_fields( $loop, $variation_data, $variation ) {
	$product = wc_get_product( $variation->ID );

	if ( ! $product ) {
		return;
	}

	$parent = wc_get_product( $product->get_parent_id() );
	$fields = array(
		'years'    => __( 'EU GARAN: duration in years', 'apg-legal-guarantee-notice' ),
		'producer' => __( 'EU GARAN: producer', 'apg-legal-guarantee-notice' ),
		'model'    => __( 'EU GARAN: model identifier', 'apg-legal-guarantee-notice' ),
	);
	?>
	<div class="apg-garan-variation">
		<?php
		foreach ( $fields as $field => $label ) {
			$own       = (string) $product->get_meta( '_apg_garan_' . $field, true );
			$inherited = $parent ? (string) $parent->get_meta( '_apg_garan_' . $field, true ) : '';

			if ( 'years' === $field ) {
				$own       = apg_guarantee_garan_years_label( $own );
				$inherited = apg_guarantee_garan_years_label( $inherited );
			}

			woocommerce_wp_text_input(
				array(
					'id'            => 'apg_garan_' . $field . '_' . $loop,
					'name'          => 'apg_garan_' . $field . '[' . $loop . ']',
					'label'         => $label,
					'value'         => $own,
					'placeholder'   => '' !== $inherited ? $inherited : '',
					'wrapper_class' => 'model' === $field ? 'form-row form-row-full' : 'form-row form-row-first',
				)
			);
		}
		?>
	</div>
	<?php
}
add_action( 'woocommerce_product_after_variable_attributes', 'apg_guarantee_garan_variation_fields', 10, 3 );

/**
 * Saves the GARAN fields of one variation.
 *
 * @param int $variation_id Variation ID.
 * @param int $i            Index of the variation in the form.
 * @return void
 */
function apg_guarantee_garan_save_variation( $variation_id, $i ) {
	// Variations are saved over AJAX with WooCommerce's `save-variations`
	// nonce, which it has already checked; checked again for the same reason as
	// above.
	if ( ! isset( $_POST['security'] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['security'] ) ), 'save-variations' ) ) {
		return;
	}

	$variation = wc_get_product( $variation_id );

	if ( ! $variation || ! current_user_can( 'edit_post', $variation->get_parent_id() ) ) {
		return;
	}

	$i      = absint( $i );
	$values = array();

	foreach ( array( 'years', 'producer', 'model' ) as $field ) {
		$values[ $field ] = isset( $_POST[ 'apg_garan_' . $field ][ $i ] )
			? sanitize_text_field( wp_unslash( $_POST[ 'apg_garan_' . $field ][ $i ] ) )
			: '';
	}

	apg_guarantee_garan_store_fields( $variation, $values['years'], $values['producer'], $values['model'], $variation->get_name() );
	$variation->save();

	apg_guarantee_garan_warn( $variation, $variation->get_name() );
}
add_action( 'woocommerce_save_product_variation', 'apg_guarantee_garan_save_variation', 10, 2 );

/**
 * Loads the image picker on the product screen.
 *
 * @param string $hook Admin page being loaded.
 * @return void
 */
function apg_guarantee_garan_admin_assets( $hook ) {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) || ! $screen || 'product' !== $screen->post_type ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_script(
		'apg-legal-guarantee-notice-garan-admin',
		plugins_url( 'assets/js/garan-admin.js', apg_guarantee_DIRECCION ),
		array( 'jquery' ),
		apg_guarantee_VERSION,
		true
	);
	wp_localize_script(
		'apg-legal-guarantee-notice-garan-admin',
		'apgGaranAdmin',
		array(
			'title'  => __( 'Label from the producer', 'apg-legal-guarantee-notice' ),
			'button' => __( 'Use this image', 'apg-legal-guarantee-notice' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'apg_guarantee_garan_admin_assets' );
