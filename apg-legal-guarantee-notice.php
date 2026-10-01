<?php
/*
Plugin Name: APG Legal Guarantee Notice
Requires Plugins: woocommerce
Version: 0.1.0
Plugin URI: https://artprojectgroup.es/plugins-para-woocommerce/apg-aviso-de-garantia-legal-para-woocommerce
Description: Shows the official EU harmonised notice on the legal guarantee of conformity, as Article 22a of Directive 2011/83/EU requires since 27 September 2026.
Author URI: https://artprojectgroup.es/
Author: Art Project Group
License: GNU General Public License v3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Requires at least: 6.0
Requires PHP: 7.4
WC requires at least: 7.0
WC tested up to: 11.1.2

Text Domain: apg-legal-guarantee-notice
Domain Path: /languages

@package APG_Legal_Guarantee_Notice
@category Core
@author Art Project Group
*/

defined( 'ABSPATH' ) || exit;

define( 'apg_guarantee_VERSION', '0.1.0' );
define( 'apg_guarantee_DIRECCION', __FILE__ );

/**
 * Declares compatibility with WooCommerce High-Performance Order Storage.
 */
add_action(
	'before_woocommerce_init',
	function () {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, true );
		}
	}
);

/**
 * Loads the bundled translation ahead of the centralised language pack, the way
 * the rest of the catalogue does.
 *
 * @param string $path   Directory WordPress is about to read the translation from.
 * @param string $domain Text domain being loaded.
 * @return string
 */
function apg_guarantee_lang_dir( $path, $domain ) {
	if ( 'apg-legal-guarantee-notice' !== $domain ) {
		return $path;
	}

	$locale = determine_locale();

	// The trailing slash is not cosmetic: WordPress builds the file name by
	// concatenating straight onto this path ("{$path}{$domain}-{$locale}.mo"),
	// so without it the translation is simply never found.
	$own = plugin_dir_path( __FILE__ ) . 'languages/';

	if ( file_exists( $own . 'apg-legal-guarantee-notice-' . $locale . '.mo' )
		|| file_exists( $own . 'apg-legal-guarantee-notice-' . $locale . '.l10n.php' ) ) {
		return $own;
	}

	return $path;
}
add_filter( 'lang_dir_for_domain', 'apg_guarantee_lang_dir', 10, 2 );

require_once plugin_dir_path( __FILE__ ) . 'includes/admin/funciones-apg.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/admin/datos.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/clases/aviso.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/clases/tema.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/clases/servidor.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/clases/salida.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/clases/checkout.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/clases/emails.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/admin/pagina.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/admin/ajustes.php';

/**
 * Registers the plugin settings.
 *
 * @return void
 */
function apg_guarantee_registra_opciones() {
	register_setting(
		'apg_guarantee_settings_group',
		'apg_guarantee_settings',
		array(
			'sanitize_callback' => 'apg_guarantee_sanitiza_opciones',
		)
	);
}
add_action( 'admin_init', 'apg_guarantee_registra_opciones' );

/**
 * Lets whoever can open this screen also save it.
 *
 * The screen is registered with `manage_woocommerce`, which is how WooCommerce
 * gates its own settings, but `options.php` asks for `manage_options` unless it
 * is told otherwise. Without this a shop manager could open the screen, change
 * everything and be refused on save, which is the worst of the two answers.
 *
 * @return string
 */
function apg_guarantee_capacidad_opciones() {
	return 'manage_woocommerce';
}
add_filter( 'option_page_capability_apg_guarantee_settings_group', 'apg_guarantee_capacidad_opciones' );

/**
 * Adds the settings page under the WooCommerce menu.
 *
 * @return void
 */
function apg_guarantee_admin_menu() {
	add_submenu_page(
		'woocommerce',
		esc_attr__( 'Legal guarantee notice', 'apg-legal-guarantee-notice' ),
		esc_attr__( 'Legal guarantee', 'apg-legal-guarantee-notice' ),
		'manage_woocommerce',
		'apg-legal-guarantee-notice',
		'apg_guarantee_pantalla_ajustes'
	);
}
add_action( 'admin_menu', 'apg_guarantee_admin_menu' );

/**
 * Adds a Settings link to the plugin row.
 *
 * @param array $links Existing action links.
 * @return array
 */
function apg_guarantee_enlace_ajustes( $links ) {
	$url = admin_url( 'admin.php?page=apg-legal-guarantee-notice' );

	array_unshift(
		$links,
		sprintf( '<a href="%s">%s</a>', esc_url( $url ), esc_html__( 'Settings', 'apg-legal-guarantee-notice' ) )
	);

	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'apg_guarantee_enlace_ajustes' );

/**
 * Warns when WooCommerce is missing. `Requires Plugins` already stops the
 * activation on WordPress 6.5 and later; this covers an older install and the
 * case of WooCommerce being deactivated afterwards.
 *
 * @return void
 */
function apg_guarantee_requiere_wc() {
	if ( class_exists( 'WooCommerce' ) || ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	?>
<div class="notice notice-error">
    <p><?php esc_html_e( 'APG Legal Guarantee Notice requires WooCommerce to be installed and active.', 'apg-legal-guarantee-notice' ); ?>
    </p>
</div>
<?php
}
add_action( 'admin_notices', 'apg_guarantee_requiere_wc' );