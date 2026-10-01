<?php
/**
 * Creates the guarantee page and selects it as the terms page.
 *
 * A shop that has no page of its own for this would otherwise have to create
 * one, paste two shortcodes into it, publish it and then come back here to pick
 * it. The button does the four steps and leaves the merchant on the page to
 * edit the wording.
 *
 * @package APG_Legal_Guarantee_Notice
 */

defined( 'ABSPATH' ) || exit;

/**
 * Prints the control that creates the page, right beside the select it fills.
 *
 * It is a link rather than a nested form, because a form cannot live inside
 * another one and this sits inside the settings form. The link carries its own
 * nonce and lands on the same handler.
 *
 * @return void
 */
function apg_guarantee_print_create_page_button() {
	if ( ! current_user_can( 'publish_pages' ) ) {
		return;
	}

	$url = wp_nonce_url(
		add_query_arg( 'action', 'apg_guarantee_create_page', admin_url( 'admin-post.php' ) ),
		'apg_guarantee_create_page',
		'apg_guarantee_page_nonce'
	);
	?>
	<p class="apg-guarantee-create-page">
		<a href="<?php echo esc_url( $url ); ?>" class="button button-secondary"><?php esc_html_e( 'Create the page', 'apg-legal-guarantee-notice' ); ?></a>
		<span class="description">
			<?php esc_html_e( 'Creates a published page holding the official notice and your guarantee terms, and selects it here.', 'apg-legal-guarantee-notice' ); ?>
		</span>
	</p>
	<?php
}

/**
 * Handles the button.
 *
 * @return void
 */
function apg_guarantee_handle_create_page() {
	$nonce = isset( $_REQUEST['apg_guarantee_page_nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['apg_guarantee_page_nonce'] ) ) : '';

	if ( ! wp_verify_nonce( $nonce, 'apg_guarantee_create_page' ) ) {
		wp_die( esc_html__( 'Security check failed.', 'apg-legal-guarantee-notice' ) );
	}

	if ( ! current_user_can( 'manage_woocommerce' ) || ! current_user_can( 'publish_pages' ) ) {
		wp_die( esc_html__( 'You do not have permission to create this page.', 'apg-legal-guarantee-notice' ) );
	}

	$page_id = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => __( 'Legal guarantee', 'apg-legal-guarantee-notice' ),
			'post_content' => "[apg_guarantee_notice]\n\n[apg_guarantee_terms]",
		),
		true
	);

	if ( is_wp_error( $page_id ) || ! $page_id ) {
		// The helper exits, but the `exit` is repeated here on purpose: neither a
		// static analyser nor a reader of this function follows execution into a
		// helper, and without it a later change to that helper would let a
		// WP_Error fall through into the update_option below.
		apg_guarantee_redirect_after_create( 0, 'error' );
		exit;
	}

	// Only this key changes. Running the whole array back through the form
	// sanitiser would be wrong: that function reads raw form input, where an
	// unticked checkbox is an absent key, and stored settings are not that
	// shape.
	$settings               = (array) get_option( 'apg_guarantee_settings', array() );
	$settings['terms_page'] = (string) absint( $page_id );
	update_option( 'apg_guarantee_settings', $settings );

	// A persistent object cache that does not invalidate cleanly would hand the
	// next request the settings as they were a moment ago, and the page the
	// merchant just created would come back unselected. These two are cheap and
	// make the outcome independent of how well the cache behaves.
	wp_cache_delete( 'alloptions', 'options' );
	wp_cache_delete( 'apg_guarantee_settings', 'options' );
	clean_post_cache( $page_id );

	apg_guarantee_redirect_after_create( absint( $page_id ), 'created' );
}
add_action( 'admin_post_apg_guarantee_create_page', 'apg_guarantee_handle_create_page' );

/**
 * Returns to the settings screen with a signed result.
 *
 * The flag is signed so the notice on the other side can tell its own redirect
 * apart from a link somebody else built.
 *
 * @param int    $page_id Page that was created, 0 on failure.
 * @param string $result  'created' or 'error'.
 * @return void
 */
function apg_guarantee_redirect_after_create( $page_id, $result ) {
	wp_safe_redirect(
		add_query_arg(
			array(
				'page'                   => 'apg-legal-guarantee-notice',
				'apg_guarantee_result'   => sanitize_key( $result ),
				'apg_guarantee_page'     => absint( $page_id ),
				'apg_guarantee_result_n' => wp_create_nonce( 'apg_guarantee_result' ),
			),
			admin_url( 'admin.php' )
		)
	);
	exit;
}

/**
 * Shows the result of the button on the settings screen.
 *
 * @return void
 */
function apg_guarantee_admin_notice() {
	$nonce = isset( $_GET['apg_guarantee_result_n'] ) ? sanitize_text_field( wp_unslash( $_GET['apg_guarantee_result_n'] ) ) : '';

	if ( ! wp_verify_nonce( $nonce, 'apg_guarantee_result' ) || empty( $_GET['apg_guarantee_result'] ) ) {
		return;
	}

	$result  = sanitize_key( wp_unslash( $_GET['apg_guarantee_result'] ) );
	$page_id = isset( $_GET['apg_guarantee_page'] ) ? absint( wp_unslash( $_GET['apg_guarantee_page'] ) ) : 0;

	if ( 'created' !== $result || ! $page_id ) {
		printf(
			'<div class="notice notice-error is-dismissible"><p>%s</p></div>',
			esc_html__( 'The page could not be created.', 'apg-legal-guarantee-notice' )
		);

		return;
	}

	printf(
		'<div class="notice notice-success is-dismissible"><p>%1$s <a href="%2$s">%3$s</a> <a href="%4$s">%5$s</a></p></div>',
		esc_html__( 'The page has been created and selected as your terms and conditions page.', 'apg-legal-guarantee-notice' ),
		esc_url( (string) get_edit_post_link( $page_id ) ),
		esc_html__( 'Edit it', 'apg-legal-guarantee-notice' ),
		esc_url( (string) get_permalink( $page_id ) ),
		esc_html__( 'See it', 'apg-legal-guarantee-notice' )
	);
}

/**
 * Whether the notice reaches the shop at all.
 *
 * Every placement can be switched off, and a shop with all of them off has a
 * plugin installed and an obligation unmet. That is worth saying out loud
 * rather than leaving the merchant to notice it themselves.
 *
 * @return bool
 */
function apg_guarantee_is_shown_anywhere() {
	$settings = apg_guarantee_get_settings();

	if ( in_array( (string) $settings['float_position'], apg_guarantee_float_positions(), true ) ) {
		return true;
	}

	if ( '1' === (string) $settings['footer_enabled'] || absint( $settings['menu_id'] ) ) {
		return true;
	}

	/**
	 * Filters whether the notice is considered reachable.
	 *
	 * A shop placing it by hand with the shortcode has it covered, and the
	 * plugin has no way of knowing that, so this is the way to say so.
	 *
	 * @param bool $shown Whether a placement is enabled.
	 */
	return (bool) apply_filters( 'apg_guarantee_is_shown_anywhere', false );
}

/**
 * Warns when the notice would not appear anywhere on the shop.
 *
 * Only the checkout being on is not enough: the obligation is to display the
 * notice at shop level, and a visitor who never reaches the checkout would
 * never see it.
 *
 * @return void
 */
function apg_guarantee_missing_placement_notice() {
	if ( ! current_user_can( 'manage_woocommerce' ) || apg_guarantee_is_shown_anywhere() ) {
		return;
	}

	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	$own    = $screen && false !== strpos( (string) $screen->id, 'apg-legal-guarantee-notice' );

	printf(
		'<div class="notice notice-warning%1$s"><p><strong>%2$s</strong> %3$s%4$s</p></div>',
		$own ? '' : ' is-dismissible',
		esc_html__( 'The legal guarantee notice is not being shown anywhere.', 'apg-legal-guarantee-notice' ),
		esc_html__( 'Article 22a requires it to be displayed at shop level. Turn on the floating button, the menu or the footer, or place it yourself with the shortcode.', 'apg-legal-guarantee-notice' ),
		$own ? '' : sprintf(
			' <a href="%1$s">%2$s</a>',
			esc_url( admin_url( 'admin.php?page=apg-legal-guarantee-notice' ) ),
			esc_html__( 'Open the settings', 'apg-legal-guarantee-notice' )
		)
	);
}
add_action( 'admin_notices', 'apg_guarantee_missing_placement_notice' );
