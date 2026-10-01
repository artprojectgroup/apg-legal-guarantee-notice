<?php
/**
 * Side information box for the plugin's settings page.
 *
 * @package APG_Legal_Guarantee_Notice
 */

defined( 'ABSPATH' ) || exit;

$apg_guarantee_datos = apg_guarantee_datos();
?>
<div class="informacion">
	<!-- Row: Donation and author -->
	<div class="fila">
		<div class="columna">
			<p><?php esc_html_e( 'If you enjoy this plugin and find it helpful, please make a donation:', 'apg-legal-guarantee-notice' ); ?></p>
			<p><a href="<?php echo esc_url( $apg_guarantee_datos['donacion'] ); ?>" target="_blank" title="<?php esc_attr_e( 'Make a donation by ', 'apg-legal-guarantee-notice' ); ?>APG"><span class="genericon genericon-cart"></span></a> </p>
		</div>
		<div class="columna">
			<p>Art Project Group:</p>
			<p><a href="https://www.artprojectgroup.es" title="Art Project Group" target="_blank"><strong class="artprojectgroup">APG</strong></a> </p>
		</div>
	</div>

	<!-- Row: Social networks and more plugins -->
	<div class="fila">
		<div class="columna">
			<p><?php esc_html_e( 'Follow us:', 'apg-legal-guarantee-notice' ); ?></p>
			<p><a href="https://www.facebook.com/artprojectgroup" title="<?php esc_attr_e( 'Follow us on ', 'apg-legal-guarantee-notice' ); ?>Facebook" target="_blank"><span class="genericon genericon-facebook-alt"></span></a> <a href="https://x.com/artprojectgroup" title="<?php esc_attr_e( 'Follow us on ', 'apg-legal-guarantee-notice' ); ?>X" target="_blank"><span class="genericon genericon-x-alt"></span></a> <a href="https://es.linkedin.com/in/artprojectgroup" title="<?php esc_attr_e( 'Follow us on ', 'apg-legal-guarantee-notice' ); ?>LinkedIn" target="_blank"><span class="genericon genericon-linkedin"></span></a> </p>
		</div>
		<div class="columna">
			<p><?php esc_html_e( 'More plugins:', 'apg-legal-guarantee-notice' ); ?></p>
			<p><a href="https://profiles.wordpress.org/artprojectgroup/" title="<?php esc_attr_e( 'More plugins on ', 'apg-legal-guarantee-notice' ); ?>WordPress" target="_blank"><span class="genericon genericon-wordpress"></span></a> </p>
		</div>
	</div>

	<!-- Row: Contact and Documentation/Support -->
	<div class="fila">
		<div class="columna">
			<p><?php esc_html_e( 'Contact us:', 'apg-legal-guarantee-notice' ); ?></p>
			<p><a href="mailto:info@artprojectgroup.es" title="<?php esc_attr_e( 'Contact us by ', 'apg-legal-guarantee-notice' ); ?>e-mail"><span class="genericon genericon-mail"></span></a> </p>
		</div>
		<div class="columna">
			<p><?php esc_html_e( 'Documentation and Support:', 'apg-legal-guarantee-notice' ); ?></p>
			<p><a href="<?php echo esc_url( $apg_guarantee_datos['plugin_url'] ); ?>" title="<?php echo esc_attr( $apg_guarantee_datos['plugin'] ); ?>"><span class="genericon genericon-book"></span></a> <a href="<?php echo esc_url( $apg_guarantee_datos['soporte'] ); ?>" title="<?php esc_attr_e( 'Support', 'apg-legal-guarantee-notice' ); ?>"><span class="genericon genericon-cog"></span></a> </p>
		</div>
	</div>

	<!-- Final row: Rating -->
	<div class="fila final">
		<div class="columna">
			<p>
				<?php
				/* translators: %s plugin name */
				echo esc_html( sprintf( __( 'Please, rate %s:', 'apg-legal-guarantee-notice' ), $apg_guarantee_datos['plugin'] ) );
				?>
			</p>
			<?php echo wp_kses_post( apg_guarantee_puntuacion() ); ?>
		</div>
		<div class="columna final"></div>
	</div>
</div>
