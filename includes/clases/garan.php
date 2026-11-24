<?php
/**
 * The EU GARAN label: its data, its validation and its drawing.
 *
 * Commission Implementing Regulation (EU) 2025/1960 fixes the label the same way
 * it fixes the notice, with one difference: three fields of it are meant to be
 * filled in. The duration in years, the producer and the model identifier are
 * the producer's to give, and everything else is fixed. So the plugin keeps the
 * Commission's own artwork and writes only those three fields into it, in the
 * typeface the guidelines name (Inter), at the positions and sizes the official
 * template sets them in.
 *
 * Two outputs come out of the same data, both written once into the uploads
 * folder and served from there as ordinary static images:
 *
 *  - An SVG for the web, built from the official vector template with the
 *    fonts embedded, because an SVG shown through `<img>` cannot load anything
 *    from outside itself.
 *  - A PNG for the order email, drawn with GD over a raster of the same
 *    template, because email clients do not show SVG.
 *
 * The label is the producer's, not the shop's: the guidelines say a seller
 * shows it when the producer offers that guarantee and should not go looking
 * for the data. That is why it is per product, and why a shop can also upload
 * the label image the producer handed over instead of having it drawn.
 *
 * @package APG_Legal_Guarantee_Notice
 */

defined( 'ABSPATH' ) || exit;

/**
 * The Your Europe page the QR code of the label leads to.
 *
 * Read off the QR code of the Commission's own file, so the clickable link the
 * guidelines ask for goes to exactly the same place.
 *
 * @return string
 */
function apg_guarantee_garan_your_europe_url() {
	/**
	 * Filters the Your Europe page the EU GARAN label links to.
	 *
	 * @param string $url Absolute URL.
	 */
	return (string) apply_filters( 'apg_guarantee_garan_your_europe_url', 'https://europa.eu/youreurope/commercial-guarantee-durability' );
}

/**
 * Brings a duration typed by the merchant to the form the label accepts.
 *
 * The guidelines allow whole years and half years, nothing else (4,1 is not
 * permitted), and the guarantee only qualifies for the label above two years.
 * A comma and a point are both accepted on the way in, because both get typed.
 *
 * @param mixed $raw Value typed in.
 * @return string Canonical value, `5` or `2.5`, or an empty string when not valid.
 */
function apg_guarantee_garan_normalize_years( $raw ) {
	$raw = trim( str_replace( ',', '.', (string) $raw ) );

	if ( ! preg_match( '/^(\d{1,2})(?:\.(5|50|0|00))?$/', $raw, $partes ) ) {
		return '';
	}

	$half  = isset( $partes[2] ) && '5' === substr( $partes[2], 0, 1 );
	$years = (int) $partes[1] + ( $half ? 0.5 : 0 );

	if ( $years <= 2 ) {
		return '';
	}

	return $half ? $partes[1] . '.5' : (string) (int) $partes[1];
}

/**
 * The duration as the label prints it: with a decimal comma, as the guidelines
 * ask, whatever the language of the site.
 *
 * @param string $years Canonical value.
 * @return string
 */
function apg_guarantee_garan_years_label( $years ) {
	return str_replace( '.', ',', (string) $years );
}

/**
 * The GARAN data of a product, or null when it does not carry the label.
 *
 * A variation inherits field by field from its parent, so a shop selling the
 * same guarantee across colours fills it once on the product and only types
 * the model identifier where it changes.
 *
 * @param WC_Product|int|null $product Product, variation or ID.
 * @return array|null `years`, `producer`, `model`, `image` and `product_id`.
 */
function apg_guarantee_garan_data( $product ) {
	if ( ! function_exists( 'wc_get_product' ) ) {
		return null;
	}

	$product = $product instanceof WC_Product ? $product : wc_get_product( absint( $product ) );

	if ( ! $product ) {
		return null;
	}

	$parent = $product->get_parent_id() ? wc_get_product( $product->get_parent_id() ) : null;
	$data   = array();

	foreach ( array( 'years', 'producer', 'model', 'image' ) as $field ) {
		$value = (string) $product->get_meta( '_apg_garan_' . $field, true );

		if ( '' === trim( $value ) && $parent ) {
			$value = (string) $parent->get_meta( '_apg_garan_' . $field, true );
		}

		$data[ $field ] = trim( $value );
	}

	$data['years']      = apg_guarantee_garan_normalize_years( $data['years'] );
	$data['image']      = (string) absint( $data['image'] );
	$data['product_id'] = (int) $product->get_id();

	$complete = '' !== $data['years']
		&& ( ( '' !== $data['producer'] && '' !== $data['model'] ) || '0' !== $data['image'] );

	/**
	 * Filters the EU GARAN data of a product. Return null to leave the label
	 * off, or an array with `years`, `producer`, `model` and `image` to show it.
	 *
	 * @param array|null $data    Resolved data, or null when incomplete.
	 * @param WC_Product $product Product or variation.
	 */
	$data = apply_filters( 'apg_guarantee_garan_data', $complete ? $data : null, $product );

	return is_array( $data ) && ! empty( $data['years'] ) ? $data : null;
}

/**
 * Identifies one label by what is printed on it.
 *
 * @param array $data GARAN data.
 * @return string
 */
function apg_guarantee_garan_hash( $data ) {
	return substr(
		md5(
			implode(
				'|',
				array(
					(string) $data['years'],
					(string) $data['producer'],
					(string) $data['model'],
					(string) ( isset( $data['image'] ) ? $data['image'] : '0' ),
					apg_guarantee_VERSION,
				)
			)
		),
		0,
		12
	);
}

/**
 * Advance widths of the bundled Inter subsets.
 *
 * @return array
 */
function apg_guarantee_garan_metrics() {
	static $metrics = null;

	if ( null === $metrics ) {
		$metrics = require plugin_dir_path( apg_guarantee_DIRECCION ) . 'includes/garan-metricas.php';
	}

	return $metrics;
}

/**
 * Width of a line of text in Inter, in the units of the label.
 *
 * @param string $text    Text.
 * @param string $weight  `regular` or `extrabold`.
 * @param float  $size    Font size.
 * @param float  $spacing Letter spacing, in em.
 * @return float
 */
function apg_guarantee_garan_text_width( $text, $weight, $size, $spacing = 0.0 ) {
	$metrics = apg_guarantee_garan_metrics();
	$widths  = isset( $metrics[ $weight ] ) ? $metrics[ $weight ] : $metrics['regular'];
	$chars   = preg_split( '//u', (string) $text, -1, PREG_SPLIT_NO_EMPTY );
	$total   = 0;

	foreach ( (array) $chars as $char ) {
		$code = function_exists( 'mb_ord' ) ? mb_ord( $char, 'UTF-8' ) : ord( $char );
		// A character the subset does not carry is measured as a wide one, so
		// the estimate errs on the side of leaving room.
		$total += isset( $widths[ $code ] ) ? $widths[ $code ] : 1400;
	}

	$count = is_array( $chars ) ? count( $chars ) : 0;

	return ( $total / $metrics['upm'] ) * $size + max( 0, $count - 1 ) * $spacing * $size;
}

/**
 * Where and how big each field goes on the full label.
 *
 * The positions and sizes are the official template's. Only when a value is
 * too long for its box does the size come down, as little as it takes, so a
 * long name stays on the label instead of running over the QR code or the
 * calendar. The duration has to shrink for half years too: the template is
 * drawn for two digits, and "12,5" at 80 is wider than its box.
 *
 * @param array $data GARAN data.
 * @return array Layout of `producer`, `model` and `years`: x, y and size.
 */
function apg_guarantee_garan_layout( $data ) {
	$producer = (string) $data['producer'];
	$model    = (string) $data['model'];
	$years    = apg_guarantee_garan_years_label( $data['years'] );

	// The rule under GARAN runs from 6.32 to 262.97; the two texts share it.
	$left  = 6.32;
	$right = 262.97;
	$gap   = 8.0;
	$size  = 9.0;

	$wp = apg_guarantee_garan_text_width( $producer, 'regular', $size );
	$wm = apg_guarantee_garan_text_width( $model, 'regular', $size );

	if ( $wp + $wm + $gap > $right - $left ) {
		$size = max( 5.0, $size * ( $right - $left - $gap ) / max( 1, $wp + $wm ) );
		$wp   = apg_guarantee_garan_text_width( $producer, 'regular', $size );
		$wm   = apg_guarantee_garan_text_width( $model, 'regular', $size );
	}

	// The template starts the model at 196.75. A longer one grows leftwards
	// from the end of the rule rather than past it.
	$model_x = $wm <= $right - 196.75 && $left + $wp + $gap <= 196.75 ? 196.75 : $right - $wm;

	// The duration has from 5.07 to the calendar at 121.47.
	$years_size = 80.0;
	$wy         = apg_guarantee_garan_text_width( $years, 'extrabold', 1, -0.03 );

	if ( $wy * $years_size > 112 ) {
		$years_size = 112 / $wy;
	}

	return array(
		'producer' => array(
			'x'    => $left,
			'y'    => 74.52,
			'size' => $size,
			'text' => $producer,
		),
		'model'    => array(
			'x'    => $model_x,
			'y'    => 74.52,
			'size' => $size,
			'text' => $model,
		),
		'years'    => array(
			'x'    => 5.07,
			'y'    => 150.57,
			'size' => $years_size,
			'text' => $years,
		),
	);
}

/**
 * Where the duration goes on the nested label, which carries nothing else.
 *
 * @param array $data GARAN data.
 * @return array x, y, size and text.
 */
function apg_guarantee_garan_nested_layout( $data ) {
	$years = apg_guarantee_garan_years_label( $data['years'] );
	$size  = 41.56;
	$wy    = apg_guarantee_garan_text_width( $years, 'extrabold', 1, -0.02 );

	// From 10.39 to the calendar, which starts at 70.87: room for two digits
	// at the template's size, and less for a half year.
	if ( $wy * $size > 57 ) {
		$size = 57 / $wy;
	}

	return array(
		'x'    => 10.39,
		'y'    => 46.65,
		'size' => $size,
		'text' => $years,
	);
}

/**
 * The fonts, as a stylesheet with the files inlined.
 *
 * The font has to travel inside the SVG: shown through `<img>`, an SVG cannot
 * fetch anything, a stylesheet included. The rules are shipped ready, with the
 * two Inter subsets already inlined as data URIs, so nothing is encoded here.
 *
 * @param bool $regular Whether the regular weight is needed (the nested label
 *                      only prints the duration).
 * @return string
 */
function apg_guarantee_garan_font_css( $regular = true ) {
	$file = plugin_dir_path( apg_guarantee_DIRECCION ) . 'assets/garan/garan-fonts-' . ( $regular ? 'label' : 'nested' ) . '.css';

	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Reading a stylesheet bundled with the plugin to inline it in the SVG.
	$css = file_get_contents( $file );

	return false === $css ? '' : $css;
}

/**
 * One `<text>` element for the SVG.
 *
 * @param array  $field   Layout of the field.
 * @param string $family  Font family.
 * @param float  $spacing Letter spacing, in em.
 * @return string
 */
function apg_guarantee_garan_svg_text( $field, $family, $spacing = 0.0 ) {
	return sprintf(
		'<text x="%1$s" y="%2$s" fill="#231f20" style="font-family:\'%3$s\';font-size:%4$spx;%5$s">%6$s</text>',
		round( (float) $field['x'], 2 ),
		round( (float) $field['y'], 2 ),
		$family,
		round( (float) $field['size'], 2 ),
		$spacing ? 'letter-spacing:' . $spacing . 'em;' : '',
		htmlspecialchars( (string) $field['text'], ENT_XML1 | ENT_QUOTES, 'UTF-8' )
	);
}

/**
 * The label as an SVG document, from the Commission's template.
 *
 * @param array  $data GARAN data.
 * @param string $type `label` for the full label, `nested` for the compact one.
 * @return string Empty when the template cannot be read.
 */
function apg_guarantee_garan_svg( $data, $type = 'label' ) {
	$file = plugin_dir_path( apg_guarantee_DIRECCION ) . 'assets/garan/garan-' . ( 'nested' === $type ? 'nested' : 'label' ) . '.svg';

	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Reading an SVG template bundled with the plugin.
	$svg = file_get_contents( $file );

	if ( false === $svg ) {
		return '';
	}

	if ( 'nested' === $type ) {
		$svg = str_replace( '/*APG_GARAN_FONTS*/', apg_guarantee_garan_font_css( false ), $svg );

		return str_replace( '<!--APG_GARAN_YEARS-->', apg_guarantee_garan_svg_text( apg_guarantee_garan_nested_layout( $data ), 'APG Inter ExtraBold', -0.02 ), $svg );
	}

	$layout = apg_guarantee_garan_layout( $data );

	return str_replace(
		array( '/*APG_GARAN_FONTS*/', '<!--APG_GARAN_PRODUCER-->', '<!--APG_GARAN_MODEL-->', '<!--APG_GARAN_YEARS-->' ),
		array(
			apg_guarantee_garan_font_css( true ),
			apg_guarantee_garan_svg_text( $layout['producer'], 'APG Inter' ),
			apg_guarantee_garan_svg_text( $layout['model'], 'APG Inter' ),
			apg_guarantee_garan_svg_text( $layout['years'], 'APG Inter ExtraBold', -0.03 ),
		),
		$svg
	);
}

/**
 * Address of the label as an SVG, writing the file the first time it is asked
 * for.
 *
 * The file is a static image in the uploads folder, named after what is printed
 * on it, so the web server serves it and caches it like any other image and a
 * change to the label is simply a new file.
 *
 * @param array  $data GARAN data.
 * @param string $type `label` or `nested`.
 * @return string URL, or an empty string when it cannot be written.
 */
function apg_guarantee_garan_url( $data, $type = 'label' ) {
	if ( 'label' === $type && '0' !== (string) $data['image'] ) {
		$own = wp_get_attachment_image_url( absint( $data['image'] ), 'full' );

		if ( $own ) {
			return $own;
		}
	}

	$store = apg_guarantee_garan_store();

	if ( ! $store ) {
		return '';
	}

	$type = 'nested' === $type ? 'nested' : 'label';
	$name = 'garan-' . apg_guarantee_garan_hash( $data ) . '-' . $type . '.svg';
	$path = $store['dir'] . '/' . $name;

	if ( ! file_exists( $path ) ) {
		$svg = apg_guarantee_garan_svg( $data, $type );

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents -- Writing a generated image into the plugin's own uploads folder, like the media library does.
		if ( '' === $svg || false === file_put_contents( $path, $svg ) ) {
			return '';
		}
	}

	return $store['url'] . '/' . $name;
}

/**
 * Whether this server can draw the PNG of the label for the emails.
 *
 * @return bool
 */
function apg_guarantee_garan_can_draw() {
	return function_exists( 'imagecreatefrompng' ) && function_exists( 'imagettftext' ) && function_exists( 'imagepng' );
}

/**
 * Folder the drawn labels are kept in, and its address.
 *
 * @return array{dir:string,url:string}|null
 */
function apg_guarantee_garan_store() {
	$uploads = wp_upload_dir();

	if ( ! empty( $uploads['error'] ) ) {
		return null;
	}

	$dir = trailingslashit( $uploads['basedir'] ) . 'apg-legal-guarantee-notice';

	if ( ! wp_mkdir_p( $dir ) ) {
		return null;
	}

	return array(
		'dir' => $dir,
		'url' => trailingslashit( $uploads['baseurl'] ) . 'apg-legal-guarantee-notice',
	);
}

/**
 * Writes one field onto the raster, a character at a time when it is spaced.
 *
 * GD has no letter spacing, and the duration is set at -0.03em in the
 * template, so the characters are placed one by one with the advances of the
 * font instead of in one call.
 *
 * @param resource|GdImage $image   Image.
 * @param array            $field   Layout of the field, in label units.
 * @param string           $font    TTF file.
 * @param string           $weight  `regular` or `extrabold`.
 * @param float            $scale   Pixels per label unit.
 * @param int              $colour  Allocated colour.
 * @param float            $spacing Letter spacing, in em.
 * @return void
 */
function apg_guarantee_garan_draw_text( $image, $field, $font, $weight, $scale, $colour, $spacing = 0.0 ) {
	$px = (float) $field['size'] * $scale;
	// GD measures type in points at 96 dpi.
	$pt = $px * 0.75;
	$x  = (float) $field['x'] * $scale;
	$y  = (int) round( (float) $field['y'] * $scale );

	if ( ! $spacing ) {
		imagettftext( $image, $pt, 0, (int) round( $x ), $y, $colour, $font, (string) $field['text'] );

		return;
	}

	foreach ( (array) preg_split( '//u', (string) $field['text'], -1, PREG_SPLIT_NO_EMPTY ) as $char ) {
		imagettftext( $image, $pt, 0, (int) round( $x ), $y, $colour, $font, $char );
		$x += apg_guarantee_garan_text_width( $char, $weight, $px ) + $spacing * $px;
	}
}

/**
 * The address of the label as a PNG, drawing it the first time it is asked for.
 *
 * Drawn once per distinct label and kept in the uploads folder, so every email
 * after the first one links to a file that is already there.
 *
 * @param array  $data GARAN data.
 * @param string $type `label` or `nested`.
 * @return string URL, or an empty string when it cannot be drawn here.
 */
function apg_guarantee_garan_png_url( $data, $type = 'label' ) {
	if ( 'label' === $type && '0' !== (string) $data['image'] ) {
		$own = wp_get_attachment_image_url( absint( $data['image'] ), 'full' );

		if ( $own ) {
			return $own;
		}
	}

	$store = apg_guarantee_garan_store();

	if ( ! $store ) {
		return '';
	}

	$type = 'nested' === $type ? 'nested' : 'label';
	$name = 'garan-' . apg_guarantee_garan_hash( $data ) . '-' . $type . '.png';
	$path = $store['dir'] . '/' . $name;

	if ( file_exists( $path ) ) {
		return $store['url'] . '/' . $name;
	}

	if ( ! apg_guarantee_garan_can_draw() ) {
		return '';
	}

	$assets = plugin_dir_path( apg_guarantee_DIRECCION ) . 'assets/garan/';
	$image  = imagecreatefrompng( $assets . 'garan-' . $type . '-base.png' );

	if ( ! $image ) {
		return '';
	}

	$colour = imagecolorallocate( $image, 35, 31, 32 );

	if ( 'nested' === $type ) {
		$scale = imagesx( $image ) / 368.5;
		apg_guarantee_garan_draw_text( $image, apg_guarantee_garan_nested_layout( $data ), $assets . 'inter-extrabold.ttf', 'extrabold', $scale, $colour, -0.02 );
	} else {
		$scale  = imagesx( $image ) / 269.29;
		$layout = apg_guarantee_garan_layout( $data );
		apg_guarantee_garan_draw_text( $image, $layout['producer'], $assets . 'inter-regular.ttf', 'regular', $scale, $colour );
		apg_guarantee_garan_draw_text( $image, $layout['model'], $assets . 'inter-regular.ttf', 'regular', $scale, $colour );
		apg_guarantee_garan_draw_text( $image, $layout['years'], $assets . 'inter-extrabold.ttf', 'extrabold', $scale, $colour, -0.03 );
	}

	$ok = imagepng( $image, $path, 9 );

	// Since PHP 8 an image is an object freed like any other, and 8.5 marks
	// imagedestroy() as deprecated; on PHP 7 it is still what frees it.
	if ( PHP_VERSION_ID < 80000 ) {
		imagedestroy( $image ); // phpcs:ignore Generic.PHP.DeprecatedFunctions.Deprecated -- Only reached on PHP 7, where it still frees the image.
	}

	return $ok ? $store['url'] . '/' . $name : '';
}

/**
 * Accessible description of one label, for its `alt`.
 *
 * @param array $data GARAN data.
 * @return string
 */
function apg_guarantee_garan_alt( $data ) {
	if ( '' === $data['producer'] ) {
		return sprintf(
			/* translators: %s: duration in years, e.g. 5 or 2,5. */
			__( 'EU GARAN label: commercial guarantee of durability of %s years offered by the producer', 'apg-legal-guarantee-notice' ),
			apg_guarantee_garan_years_label( $data['years'] )
		);
	}

	return sprintf(
		/* translators: 1: duration in years, e.g. 5 or 2,5. 2: producer. 3: model identifier. */
		__( 'EU GARAN label: commercial guarantee of durability of %1$s years offered by %2$s for model %3$s', 'apg-legal-guarantee-notice' ),
		apg_guarantee_garan_years_label( $data['years'] ),
		$data['producer'],
		$data['model']
	);
}

/**
 * Wording of the link to Your Europe that goes with the label.
 *
 * @return string
 */
function apg_guarantee_garan_link_text() {
	return __( 'What the commercial guarantee of durability covers', 'apg-legal-guarantee-notice' );
}

/**
 * Prints the full label, with the clickable link the guidelines ask for.
 *
 * The image opens on its own in a new tab when clicked, which is the larger,
 * zoomable view the guidelines want for a label shown with a product.
 *
 * @param array  $data    GARAN data.
 * @param string $context Where it is printed.
 * @return void
 */
function apg_guarantee_garan_print_label( $data, $context = 'product' ) {
	$url = apg_guarantee_garan_url( $data, 'label' );

	wp_enqueue_style( 'apg-legal-guarantee-notice' );
	?>
	<figure class="apg-garan apg-garan--label apg-garan--<?php echo esc_attr( sanitize_html_class( $context ) ); ?>">
		<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener" class="apg-garan__zoom">
			<img src="<?php echo esc_url( $url ); ?>" alt="<?php echo esc_attr( apg_guarantee_garan_alt( $data ) ); ?>" width="269" height="283" class="apg-garan__image" loading="lazy" decoding="async">
		</a>
		<figcaption class="apg-garan__link">
			<a href="<?php echo esc_url( apg_guarantee_garan_your_europe_url() ); ?>" target="_blank" rel="noopener">
				<?php echo esc_html( apg_guarantee_garan_link_text() ); ?>
				<span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'apg-legal-guarantee-notice' ); ?></span>
			</a>
		</figcaption>
	</figure>
	<?php
}

/**
 * Prints the nested label, which opens the full one on the first click.
 *
 * The guidelines allow the compact label where space is short, on condition
 * that the full label appears in its entirety on the first click and that it
 * carries accessible text. It opens in a native popover, like the notice.
 *
 * @param array  $data    GARAN data.
 * @param string $context Where it is printed.
 * @return void
 */
function apg_guarantee_garan_print_nested( $data, $context = 'product' ) {
	static $count = 0;
	++$count;

	$id    = 'apg-garan-' . absint( $data['product_id'] ) . '-' . $count;
	$label = apg_guarantee_garan_url( $data, 'label' );

	wp_enqueue_style( 'apg-legal-guarantee-notice' );
	?>
	<span class="apg-garan apg-garan--nested apg-garan--<?php echo esc_attr( sanitize_html_class( $context ) ); ?>">
		<button type="button" class="apg-garan__open" popovertarget="<?php echo esc_attr( $id ); ?>">
			<img src="<?php echo esc_url( apg_guarantee_garan_url( $data, 'nested' ) ); ?>" alt="<?php echo esc_attr( apg_guarantee_garan_alt( $data ) ); ?>" width="184" height="28" class="apg-garan__nested" loading="lazy" decoding="async">
		</button>
		<span id="<?php echo esc_attr( $id ); ?>" popover="auto" class="apg-guarantee-notice__modal apg-garan__modal" role="dialog" aria-label="<?php echo esc_attr( apg_guarantee_garan_alt( $data ) ); ?>">
			<span class="apg-guarantee-notice__head">
				<span class="apg-guarantee-notice__title"><?php esc_html_e( 'Commercial guarantee of durability', 'apg-legal-guarantee-notice' ); ?></span>
				<button type="button" class="apg-guarantee-notice__close" popovertarget="<?php echo esc_attr( $id ); ?>" popovertargetaction="hide" aria-label="<?php esc_attr_e( 'Close', 'apg-legal-guarantee-notice' ); ?>" autofocus>&times;</button>
			</span>
			<span class="apg-guarantee-notice__body">
				<img src="<?php echo esc_url( $label ); ?>" alt="<?php echo esc_attr( apg_guarantee_garan_alt( $data ) ); ?>" width="269" height="283" class="apg-garan__image" loading="lazy" decoding="async">
				<span class="apg-guarantee-notice__link">
					<a href="<?php echo esc_url( apg_guarantee_garan_your_europe_url() ); ?>" target="_blank" rel="noopener">
						<?php echo esc_html( apg_guarantee_garan_link_text() ); ?>
						<span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'apg-legal-guarantee-notice' ); ?></span>
					</a>
				</span>
			</span>
		</span>
	</span>
	<?php
}

/**
 * Prints the label in whichever form the settings ask for.
 *
 * @param array  $data    GARAN data.
 * @param string $mode    `label` or `nested`.
 * @param string $context Where it is printed.
 * @return void
 */
function apg_guarantee_garan_print( $data, $mode, $context ) {
	if ( 'nested' === $mode ) {
		apg_guarantee_garan_print_nested( $data, $context );

		return;
	}

	apg_guarantee_garan_print_label( $data, $context );
}

/**
 * The label as a string, for the places that need to return it.
 *
 * @param array  $data    GARAN data.
 * @param string $mode    `label` or `nested`.
 * @param string $context Where it is printed.
 * @return string
 */
function apg_guarantee_garan_markup( $data, $mode, $context ) {
	ob_start();
	apg_guarantee_garan_print( $data, $mode, $context );

	return (string) ob_get_clean();
}
