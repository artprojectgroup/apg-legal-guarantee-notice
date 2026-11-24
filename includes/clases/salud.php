<?php
/**
 * Site Health checks: whether the notice is reaching the shop, and whether the
 * GARAN labels can be drawn for the emails.
 *
 * The dashboard warning already covers the first one while somebody is in the
 * admin. Site Health is where a site's state is reviewed on purpose, and where
 * an agency checking a client's shop looks, so it says it there too.
 *
 * @package APG_Legal_Guarantee_Notice
 */

defined( 'ABSPATH' ) || exit;

/**
 * Adds the checks.
 *
 * @param array $tests Site Health tests.
 * @return array
 */
function apg_guarantee_site_health_tests( $tests ) {
	$tests['direct']['apg_guarantee_notice'] = array(
		'label' => __( 'EU legal guarantee notice', 'apg-legal-guarantee-notice' ),
		'test'  => 'apg_guarantee_site_health_notice',
	);

	if ( apg_guarantee_con_woocommerce() ) {
		$tests['direct']['apg_guarantee_garan'] = array(
			'label' => __( 'EU GARAN label', 'apg-legal-guarantee-notice' ),
			'test'  => 'apg_guarantee_site_health_garan',
		);
	}

	return $tests;
}
add_filter( 'site_status_tests', 'apg_guarantee_site_health_tests' );

/**
 * The common parts of a result.
 *
 * @param string $test        Test ID.
 * @param string $status      `good`, `recommended` or `critical`.
 * @param string $label       Heading.
 * @param string $description Explanation, plain text.
 * @return array
 */
function apg_guarantee_site_health_result( $test, $status, $label, $description ) {
	$settings_url = admin_url( ( apg_guarantee_con_woocommerce() ? 'admin.php' : 'options-general.php' ) . '?page=apg-legal-guarantee-notice' );

	return array(
		'label'       => $label,
		'status'      => $status,
		'badge'       => array(
			'label' => __( 'Legal', 'apg-legal-guarantee-notice' ),
			'color' => 'good' === $status ? 'blue' : 'orange',
		),
		'description' => '<p>' . esc_html( $description ) . '</p>',
		'actions'     => 'good' === $status ? '' : sprintf( '<p><a href="%1$s">%2$s</a></p>', esc_url( $settings_url ), esc_html__( 'Open the settings', 'apg-legal-guarantee-notice' ) ),
		'test'        => $test,
	);
}

/**
 * Whether the notice reaches the shop.
 *
 * @return array
 */
function apg_guarantee_site_health_notice() {
	if ( ! apg_guarantee_is_shown_anywhere() ) {
		return apg_guarantee_site_health_result(
			'apg_guarantee_notice',
			'critical',
			__( 'The EU legal guarantee notice is not shown anywhere on the shop', 'apg-legal-guarantee-notice' ),
			__( 'Article 22a of Directive 2011/83/EU requires the notice to be displayed at shop level, and every placement of the plugin is switched off. Turn on the floating button, the menu or the footer, or place it yourself with the shortcode or the block.', 'apg-legal-guarantee-notice' )
		);
	}

	$settings = apg_guarantee_get_settings();

	if ( apg_guarantee_con_woocommerce() && '1' !== (string) $settings['show_email'] ) {
		return apg_guarantee_site_health_result(
			'apg_guarantee_notice',
			'recommended',
			__( 'The EU legal guarantee notice is not included in the order emails', 'apg-legal-guarantee-notice' ),
			__( 'The Commission\'s guidelines say the notice should also be included in the confirmation email, and that placement is switched off.', 'apg-legal-guarantee-notice' )
		);
	}

	return apg_guarantee_site_health_result(
		'apg_guarantee_notice',
		'good',
		__( 'The EU legal guarantee notice is shown on the shop', 'apg-legal-guarantee-notice' ),
		__( 'At least one placement shows the official notice at shop level.', 'apg-legal-guarantee-notice' )
	);
}

/**
 * Whether the GARAN labels can be drawn for the emails.
 *
 * @return array
 */
function apg_guarantee_site_health_garan() {
	if ( ! apg_guarantee_garan_can_draw() ) {
		return apg_guarantee_site_health_result(
			'apg_guarantee_garan',
			'recommended',
			__( 'The EU GARAN label cannot be drawn for the order emails', 'apg-legal-guarantee-notice' ),
			__( 'Drawing the label as an image for the emails needs the GD extension of PHP with FreeType, and this server does not have it. The web shows the label anyway; the emails name the guarantee in words and link to Your Europe instead. Your hosting provider can enable GD.', 'apg-legal-guarantee-notice' )
		);
	}

	if ( ! apg_guarantee_garan_store() ) {
		return apg_guarantee_site_health_result(
			'apg_guarantee_garan',
			'recommended',
			__( 'The EU GARAN labels cannot be saved', 'apg-legal-guarantee-notice' ),
			__( 'The labels are kept as images in the uploads folder, and WordPress cannot write there.', 'apg-legal-guarantee-notice' )
		);
	}

	return apg_guarantee_site_health_result(
		'apg_guarantee_garan',
		'good',
		__( 'The EU GARAN label can be drawn', 'apg-legal-guarantee-notice' ),
		__( 'Products with a commercial guarantee of durability show the label on the web and in the order emails.', 'apg-legal-guarantee-notice' )
	);
}
