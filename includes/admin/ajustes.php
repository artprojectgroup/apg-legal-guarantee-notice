<?php
/**
 * Settings screen.
 *
 * @package APG_Legal_Guarantee_Notice
 */

defined( 'ABSPATH' ) || exit;

/**
 * Prints the "text / icon and text / icon only" choice for one placement.
 *
 * @param string $placement Placement key.
 * @param array  $settings  Plugin settings.
 * @return void
 */
function apg_guarantee_field_style( $placement, $settings ) {
	$styles = array(
		'text'      => __( 'Text only (recommended)', 'apg-legal-guarantee-notice' ),
		'icon_text' => __( 'Icon and text', 'apg-legal-guarantee-notice' ),
		'icon'      => __( 'Icon only', 'apg-legal-guarantee-notice' ),
	);

	$current = isset( $settings[ $placement . '_style' ] ) ? (string) $settings[ $placement . '_style' ] : 'text';

	foreach ( $styles as $key => $label ) :
		?>
		<label class="apg-guarantee-radio">
			<input type="radio" name="apg_guarantee_settings[<?php echo esc_attr( $placement ); ?>_style]" value="<?php echo esc_attr( $key ); ?>" <?php checked( $key, $current ); ?>>
			<?php echo esc_html( $label ); ?>
		</label>
		<?php
	endforeach;
}

/**
 * Prints the colour and size fields for one placement.
 *
 * @param string $placement Placement key.
 * @param array  $settings  Plugin settings.
 * @return void
 */
function apg_guarantee_field_appearance( $placement, $settings ) {
	$look = isset( $settings[ $placement . '_look' ] ) ? (string) $settings[ $placement . '_look' ] : 'inherit';
	$size = isset( $settings[ $placement . '_font_size' ] ) ? (string) $settings[ $placement . '_font_size' ] : '';
	$name = 'apg_guarantee_settings[' . $placement . '_';
	$id   = 'apg_guarantee_' . $placement . '_';

	$looks = array(
		'inherit' => __( 'Whatever surrounds it (recommended)', 'apg-legal-guarantee-notice' ),
		'link'    => __( 'Like a link of this theme', 'apg-legal-guarantee-notice' ),
		'button'  => __( 'Like a button of this theme', 'apg-legal-guarantee-notice' ),
		'custom'  => __( 'Colours I choose', 'apg-legal-guarantee-notice' ),
	);

	$colours = array(
		'color'            => __( 'Text', 'apg-legal-guarantee-notice' ),
		'background'       => __( 'Background', 'apg-legal-guarantee-notice' ),
		'color_hover'      => __( 'Text on hover', 'apg-legal-guarantee-notice' ),
		'background_hover' => __( 'Background on hover', 'apg-legal-guarantee-notice' ),
	);
	?>
	<select class="apg-guarantee-look" id="<?php echo esc_attr( $id ); ?>look" name="<?php echo esc_attr( $name ); ?>look]" data-placement="<?php echo esc_attr( $placement ); ?>">
		<?php foreach ( $looks as $key => $label ) : ?>
			<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $look ); ?>><?php echo esc_html( $label ); ?></option>
		<?php endforeach; ?>
	</select>

	<div class="apg-guarantee-custom" id="<?php echo esc_attr( $id ); ?>custom"<?php echo 'custom' === $look ? '' : ' hidden'; ?>>
		<?php foreach ( $colours as $key => $label ) : ?>
			<?php $value = isset( $settings[ $placement . '_' . $key ] ) ? (string) $settings[ $placement . '_' . $key ] : ''; ?>
			<p class="apg-guarantee-colour">
				<label for="<?php echo esc_attr( $id . $key ); ?>"><?php echo esc_html( $label ); ?></label>
				<input type="color" id="<?php echo esc_attr( $id . $key ); ?>" name="<?php echo esc_attr( $name . $key ); ?>]" value="<?php echo esc_attr( '' !== $value ? $value : ( false !== strpos( $key, 'background' ) ? '#ffffff' : '#000000' ) ); ?>">
				<label class="apg-guarantee-inherit"><input type="checkbox" name="<?php echo esc_attr( $name . $key ); ?>_off]" value="1" <?php checked( '', $value ); ?>> <?php esc_html_e( 'Inherit', 'apg-legal-guarantee-notice' ); ?></label>
			</p>
		<?php endforeach; ?>
		<p class="apg-guarantee-colour">
			<label for="<?php echo esc_attr( $id ); ?>font_size"><?php esc_html_e( 'Font size, in pixels', 'apg-legal-guarantee-notice' ); ?></label>
			<input type="number" min="10" max="32" step="1" id="<?php echo esc_attr( $id ); ?>font_size" name="<?php echo esc_attr( $name ); ?>font_size]" value="<?php echo esc_attr( $size ); ?>" placeholder="<?php esc_attr_e( 'Theme', 'apg-legal-guarantee-notice' ); ?>">
		</p>
	</div>
	<?php
}

/**
 * Renders the settings screen.
 *
 * @return void
 */
function apg_guarantee_pantalla_ajustes() {
	if ( ! current_user_can( apg_guarantee_capacidad() ) ) {
		wp_die( esc_html__( 'You do not have permission to access this page.', 'apg-legal-guarantee-notice' ) );
	}

	$settings = apg_guarantee_get_settings();
	$language = apg_guarantee_current_language();
	$datos    = apg_guarantee_datos();
	?>
	<div class="wrap woocommerce">
		<h2><?php esc_html_e( 'APG Legal Guarantee Notice Options.', 'apg-legal-guarantee-notice' ); ?></h2>
		<h3><a href="<?php echo esc_url( $datos['plugin_url'] ); ?>" title="Art Project Group"><?php echo esc_html( $datos['plugin'] ); ?></a></h3>
		<p>
			<?php esc_html_e( 'Article 22a of Directive 2011/83/EU has required every shop selling goods to display the official EU notice on the legal guarantee of conformity since 27 September 2026. The plugin ships the notice exactly as the European Commission publishes it, in the 24 official EU languages, and serves the one that matches each visitor.', 'apg-legal-guarantee-notice' ); ?>
		</p>
		<?php require plugin_dir_path( apg_guarantee_DIRECCION ) . 'includes/cuadro-informacion.php'; ?>
		<div class="cabecera">
			<a href="<?php echo esc_url( $datos['plugin_url'] ); ?>" title="<?php echo esc_attr( $datos['plugin'] ); ?>" target="_blank">
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage -- Static plugin image, it has no attachment ID. ?>
				<img src="<?php echo esc_url( plugins_url( 'assets/images/cabecera.jpg', apg_guarantee_DIRECCION ) ); ?>" class="imagen" alt="<?php echo esc_attr( $datos['plugin'] ); ?>" />
			</a>
		</div>

		<?php apg_guarantee_admin_notice(); ?>
		<?php apg_guarantee_missing_placement_notice(); ?>

		<p>
			<?php
			printf(
				/* translators: %s: two-letter language code, e.g. ES. */
				esc_html__( 'Right now this screen would show the notice in: %s.', 'apg-legal-guarantee-notice' ),
				'<strong>' . esc_html( strtoupper( $language ) ) . '</strong>'
			);
			?>
			<?php
			// Rendered with no appearance of its own on purpose: this previews
			// the notice, not how the merchant styled one of the buttons.
			apg_guarantee_print_trigger(
				array(
					'label' => __( 'Preview it', 'apg-legal-guarantee-notice' ),
					'style' => 'text',
				)
			);
			?>
		</p>
		<?php apg_guarantee_print_panel(); ?>

		<form id="formulario" method="post" action="options.php">
			<?php settings_fields( 'apg_guarantee_settings_group' ); ?>

			<h3><?php esc_html_e( 'Floating button', 'apg-legal-guarantee-notice' ); ?></h3>
			<p><?php esc_html_e( 'Floats over the page wherever you choose, on every page of the shop. On its own this satisfies the obligation: the Commission describes the notice as a general reminder placed on the seller\'s website, at shop level rather than per product.', 'apg-legal-guarantee-notice' ); ?></p>
			<table class="form-table apg-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Position', 'apg-legal-guarantee-notice' ); ?></th>
					<td>
						<select name="apg_guarantee_settings[float_position]">
							<option value="" <?php selected( '', (string) $settings['float_position'] ); ?>><?php esc_html_e( '— Do not show it —', 'apg-legal-guarantee-notice' ); ?></option>
							<?php
							$apg_guarantee_positions = array(
								'top-left'     => __( 'Top left', 'apg-legal-guarantee-notice' ),
								'top-right'    => __( 'Top right', 'apg-legal-guarantee-notice' ),
								'middle-left'  => __( 'Middle left', 'apg-legal-guarantee-notice' ),
								'middle-right' => __( 'Middle right', 'apg-legal-guarantee-notice' ),
								'bottom-left'  => __( 'Bottom left', 'apg-legal-guarantee-notice' ),
								'bottom-right' => __( 'Bottom right', 'apg-legal-guarantee-notice' ),
							);
							foreach ( $apg_guarantee_positions as $apg_guarantee_key => $apg_guarantee_label ) :
								?>
								<option value="<?php echo esc_attr( $apg_guarantee_key ); ?>" <?php selected( $apg_guarantee_key, (string) $settings['float_position'] ); ?>><?php echo esc_html( $apg_guarantee_label ); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'How it looks', 'apg-legal-guarantee-notice' ); ?></th>
					<td>
						<p class="apg-guarantee-sublabel"><?php esc_html_e( 'Shows', 'apg-legal-guarantee-notice' ); ?></p>
						<?php apg_guarantee_field_style( 'float', $settings ); ?>
						<p class="apg-guarantee-sublabel"><?php esc_html_e( 'Takes its colours from', 'apg-legal-guarantee-notice' ); ?></p>
						<?php apg_guarantee_field_appearance( 'float', $settings ); ?>
					</td>
				</tr>
			</table>

			<h3><?php esc_html_e( 'Menu', 'apg-legal-guarantee-notice' ); ?></h3>
			<table class="form-table apg-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Add it to', 'apg-legal-guarantee-notice' ); ?></th>
					<td>
						<?php $apg_guarantee_menus = wp_get_nav_menus(); ?>
						<?php if ( $apg_guarantee_menus ) : ?>
							<select name="apg_guarantee_settings[menu_id]">
								<option value="0" <?php selected( 0, absint( $settings['menu_id'] ) ); ?>><?php esc_html_e( '— Do not add it —', 'apg-legal-guarantee-notice' ); ?></option>
								<?php foreach ( $apg_guarantee_menus as $apg_guarantee_menu ) : ?>
									<option value="<?php echo esc_attr( $apg_guarantee_menu->term_id ); ?>" <?php selected( $apg_guarantee_menu->term_id, absint( $settings['menu_id'] ) ); ?>><?php echo esc_html( $apg_guarantee_menu->name ); ?></option>
								<?php endforeach; ?>
							</select>
							<p class="description"><?php esc_html_e( 'Added as the last item of that menu, wherever the theme prints it.', 'apg-legal-guarantee-notice' ); ?></p>
							<?php if ( absint( $settings['terms_page'] ) ) : ?>
								<p class="description">
									<?php
									printf(
										/* translators: %s: title of the page the menu item links to. */
										esc_html__( 'It is an ordinary link to "%s", so your theme styles it exactly like the rest of the menu.', 'apg-legal-guarantee-notice' ),
										esc_html( (string) get_the_title( absint( $settings['terms_page'] ) ) )
									);
									?>
								</p>
							<?php else : ?>
								<p class="description">
									<?php esc_html_e( 'With no page chosen below, the item opens the notice in the modal instead of linking anywhere. Choosing or creating a page turns it into an ordinary menu link, which is what a theme knows how to style.', 'apg-legal-guarantee-notice' ); ?>
								</p>
							<?php endif; ?>
						<?php else : ?>
							<p class="description"><?php esc_html_e( 'This site has no menus yet.', 'apg-legal-guarantee-notice' ); ?></p>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'How it looks', 'apg-legal-guarantee-notice' ); ?></th>
					<td>
						<p class="apg-guarantee-sublabel"><?php esc_html_e( 'Shows', 'apg-legal-guarantee-notice' ); ?></p>
						<?php apg_guarantee_field_style( 'menu', $settings ); ?>
						<p class="apg-guarantee-sublabel"><?php esc_html_e( 'Takes its colours from', 'apg-legal-guarantee-notice' ); ?></p>
						<?php apg_guarantee_field_appearance( 'menu', $settings ); ?>
						<p class="description"><?php esc_html_e( 'Left on the first option the item is indistinguishable from the rest of the menu, which is usually what you want here.', 'apg-legal-guarantee-notice' ); ?></p>
					</td>
				</tr>
			</table>

			<h3><?php esc_html_e( 'Site footer', 'apg-legal-guarantee-notice' ); ?></h3>
			<table class="form-table apg-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Show it', 'apg-legal-guarantee-notice' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="apg_guarantee_settings[footer_enabled]" value="1" <?php checked( '1', (string) $settings['footer_enabled'] ); ?>>
							<?php esc_html_e( 'Add it to the footer of every page', 'apg-legal-guarantee-notice' ); ?>
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'How it looks', 'apg-legal-guarantee-notice' ); ?></th>
					<td>
						<p class="apg-guarantee-sublabel"><?php esc_html_e( 'Shows', 'apg-legal-guarantee-notice' ); ?></p>
						<?php apg_guarantee_field_style( 'footer', $settings ); ?>
						<p class="apg-guarantee-sublabel"><?php esc_html_e( 'Takes its colours from', 'apg-legal-guarantee-notice' ); ?></p>
						<?php apg_guarantee_field_appearance( 'footer', $settings ); ?>
					</td>
				</tr>
			</table>

			<?php
			/*
			 * Both placements are WooCommerce's: without it there is no checkout to
			 * print above and no order email to go into. The settings are hidden
			 * rather than shown doing nothing, and the stored values are left
			 * untouched so they come back if WooCommerce does.
			 */
			if ( apg_guarantee_con_woocommerce() ) :
				?>
			<h3><?php esc_html_e( 'Checkout and emails', 'apg-legal-guarantee-notice' ); ?></h3>
			<table class="form-table apg-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Checkout', 'apg-legal-guarantee-notice' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="apg_guarantee_settings[checkout_enabled]" value="1" <?php checked( '1', (string) $settings['checkout_enabled'] ); ?>>
							<?php esc_html_e( 'Show it above the place-order button', 'apg-legal-guarantee-notice' ); ?>
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'How it looks', 'apg-legal-guarantee-notice' ); ?></th>
					<td>
						<p class="apg-guarantee-sublabel"><?php esc_html_e( 'Shows', 'apg-legal-guarantee-notice' ); ?></p>
						<?php apg_guarantee_field_style( 'checkout', $settings ); ?>
						<p class="apg-guarantee-sublabel"><?php esc_html_e( 'Takes its colours from', 'apg-legal-guarantee-notice' ); ?></p>
						<?php apg_guarantee_field_appearance( 'checkout', $settings ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Order email', 'apg-legal-guarantee-notice' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="apg_guarantee_settings[show_email]" value="1" <?php checked( '1', (string) $settings['show_email'] ); ?>>
							<?php esc_html_e( 'Include the notice in the customer order emails', 'apg-legal-guarantee-notice' ); ?>
						</label>
						<p class="description"><?php esc_html_e( 'The notice goes in as an image, since an email client cannot open a modal. The Commission\'s guidelines ask for it here too.', 'apg-legal-guarantee-notice' ); ?></p>
					</td>
				</tr>
			</table>

			<?php endif; ?>

			<h3><?php esc_html_e( 'Wording', 'apg-legal-guarantee-notice' ); ?></h3>
			<table class="form-table apg-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="apg_guarantee_trigger_text"><?php esc_html_e( 'Wording that opens it', 'apg-legal-guarantee-notice' ); ?></label>
					</th>
					<td>
						<input type="text" class="regular-text" id="apg_guarantee_trigger_text" name="apg_guarantee_settings[trigger_text]" value="<?php echo esc_attr( $settings['trigger_text'] ); ?>" placeholder="<?php echo esc_attr( apg_guarantee_default_trigger_text() ); ?>">
						<p class="description">
							<?php esc_html_e( 'Used by every placement. The notice opens on the first click on it, which is the pattern the Commission illustrates. Leave it empty for the default wording.', 'apg-legal-guarantee-notice' ); ?>
						</p>
						<p class="description">
							<?php esc_html_e( 'The Commission\'s guidelines describe this as "a sentence informing consumers about their legal guarantee rights", and every example they give is written text. They do provide a compact graphic for the GARAN label, and deliberately none for this notice. Icon only keeps the wording for screen readers and in the tooltip, but a visitor on a phone never sees it, since there is no hover on a touch screen.', 'apg-legal-guarantee-notice' ); ?>
						</p>
					</td>
				</tr>
			</table>

			<h3><?php esc_html_e( 'National note', 'apg-legal-guarantee-notice' ); ?></h3>
			<p><?php esc_html_e( 'The official notice says "minimum two years" because that is the European floor, and the file may not be edited. Where your country grants longer, tell the customer beside the notice.', 'apg-legal-guarantee-notice' ); ?></p>
			<table class="form-table apg-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Show the note', 'apg-legal-guarantee-notice' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="apg_guarantee_settings[national_note]" value="1" <?php checked( '1', (string) $settings['national_note'] ); ?>>
							<?php esc_html_e( 'Show a national note next to the notice', 'apg-legal-guarantee-notice' ); ?>
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="apg_guarantee_national_note_text"><?php esc_html_e( 'Wording', 'apg-legal-guarantee-notice' ); ?></label>
					</th>
					<td>
						<textarea class="large-text" rows="3" id="apg_guarantee_national_note_text" name="apg_guarantee_settings[national_note_text]" placeholder="<?php echo esc_attr( apg_guarantee_default_national_note() ); ?>"><?php echo esc_textarea( $settings['national_note_text'] ); ?></textarea>
						<p class="description"><?php esc_html_e( 'Leave it empty for the default, which states the three years Article 120.1 TRLGDCU grants in Spain.', 'apg-legal-guarantee-notice' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="apg_guarantee_terms_page"><?php esc_html_e( 'Terms and conditions page', 'apg-legal-guarantee-notice' ); ?></label>
					</th>
					<td>
						<?php
						/*
						 * `wc-enhanced-select` is the class WooCommerce's own
						 * script looks for, so WooCommerce itself turns this
						 * into the searchable control the rest of its screens
						 * use, with its own translations.
						 *
						 * With no pages on the site at all `wp_dropdown_pages()`
						 * prints nothing, which would leave the label pointing
						 * at a control that is not there. Asking first costs one
						 * cached row and keeps the printing to WordPress, so
						 * there is no markup of ours to escape.
						 */
						if ( get_pages( array( 'number' => 1 ) ) ) {
							wp_dropdown_pages(
								array(
									'name'              => 'apg_guarantee_settings[terms_page]',
									'id'                => 'apg_guarantee_terms_page',
									'class'             => 'wc-enhanced-select',
									'selected'          => absint( $settings['terms_page'] ),
									'show_option_none'  => esc_html__( '— None —', 'apg-legal-guarantee-notice' ),
									'option_none_value' => '0',
								)
							);
						} else {
							?>
							<select name="apg_guarantee_settings[terms_page]" id="apg_guarantee_terms_page" class="wc-enhanced-select">
								<option value="0"><?php esc_html_e( '— None —', 'apg-legal-guarantee-notice' ); ?></option>
							</select>
							<?php
						}
						?>
						<p class="description"><?php esc_html_e( 'Linked from the national note, and the page the menu item points at. Defaults to the Terms and conditions page of WooCommerce, which is not the same thing as the privacy policy page.', 'apg-legal-guarantee-notice' ); ?></p>
						<?php apg_guarantee_print_create_page_button(); ?>
					</td>
				</tr>
			</table>

			<h3><?php esc_html_e( 'Your guarantee terms', 'apg-legal-guarantee-notice' ); ?></h3>
			<p><?php esc_html_e( 'The official notice is the European summary and cannot be edited. What it does not say is what your shop does: how long the guarantee runs here, how a customer claims it and who they write to. That text is yours, and the plugin only gives you a starting point for it.', 'apg-legal-guarantee-notice' ); ?></p>
			<table class="form-table apg-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="apg_guarantee_terms_text"><?php esc_html_e( 'Text', 'apg-legal-guarantee-notice' ); ?></label>
					</th>
					<td>
						<textarea class="large-text" rows="10" id="apg_guarantee_terms_text" name="apg_guarantee_settings[terms_text]" placeholder="<?php echo esc_attr( apg_guarantee_default_terms_text() ); ?>"><?php echo esc_textarea( $settings['terms_text'] ); ?></textarea>
						<p class="description">
							<?php
							printf(
								/* translators: %s: the shortcode, already wrapped in a code tag. */
								esc_html__( 'Shown by the %s shortcode. Leave it empty to use the suggested text, which appears greyed out above.', 'apg-legal-guarantee-notice' ),
								'<code>[apg_guarantee_terms]</code>'
							);
							?>
						</p>
						<p class="description">
							<strong><?php esc_html_e( 'Read it before you publish it.', 'apg-legal-guarantee-notice' ); ?></strong>
							<?php esc_html_e( 'It describes what the law already grants, but only you know how your shop handles a claim, and the text has no legal value until you have made it yours.', 'apg-legal-guarantee-notice' ); ?>
						</p>
					</td>
				</tr>
			</table>

			<h3><?php esc_html_e( 'Shortcodes', 'apg-legal-guarantee-notice' ); ?></h3>
			<table class="form-table apg-table" role="presentation">
				<tr>
					<th scope="row"><code>[apg_guarantee_notice]</code></th>
					<td><?php esc_html_e( 'The official notice printed in the page, at full size. For a page devoted to the guarantee, where the notice is the content rather than a reminder.', 'apg-legal-guarantee-notice' ); ?></td>
				</tr>
				<tr>
					<th scope="row"><code>[apg_guarantee_terms]</code></th>
					<td><?php esc_html_e( 'Your guarantee terms, the text above.', 'apg-legal-guarantee-notice' ); ?></td>
				</tr>
				<tr>
					<th scope="row"><code>[apg_guarantee_button]</code></th>
					<td><?php esc_html_e( 'The button that opens the notice in a modal, for placing it somewhere the settings above do not reach.', 'apg-legal-guarantee-notice' ); ?></td>
				</tr>
			</table>

			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
