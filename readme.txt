=== APG Legal Guarantee Notice ===
Contributors: artprojectgroup
Donate link: https://artprojectgroup.com/shop/donation
Tags: legal guarantee, consumer rights, woocommerce, eu, conformity
Requires at least: 6.0
Tested up to: 7.2
Requires PHP: 7.4
Stable tag: 0.5.0
WC requires at least: 7.0
WC tested up to: 11.3.1
License: GNU General Public License v3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Shows the official EU notice on the legal guarantee of conformity, mandatory since 27 September 2026, in the 24 official EU languages.

== Description ==

Since **27 September 2026**, Article 22a of Directive 2011/83/EU requires every shop selling goods to consumers to display the **official EU harmonised notice on the legal guarantee of conformity** in a prominent manner. Commission Implementing Regulation (EU) 2025/1960 fixes its design and content, and the notice may not be edited, cropped or redrawn.

**APG Legal Guarantee Notice** ships the notice exactly as the European Commission publishes it, in all 24 official EU languages, and shows each visitor the one in their own language. There is nothing to design and nothing to write.

= Features =
* The official notice in the 24 official EU languages, served unaltered.
* The language follows the visitor, not the site: a shop reading in German serves the German notice.
* Catalan, Basque and Galician fall back to the Spanish notice, not the English one.
* Opens in a modal on the first click, the pattern the Commission's practical guidelines illustrate, built with the native popover API and no JavaScript at all.
* Six placements, each with its own settings: a floating button in any of six positions, the last item of any menu, the footer, the checkout above the place-order button, the product page under the add-to-cart button, and the cart.
* At the checkout, the product page and the cart, either the sentence that opens the notice or the whole notice with nothing to click, which is what part of the German doctrine asks for.
* The EU GARAN label of the commercial guarantee of durability, per product and per variation, drawn on the Commission's own template with the duration, the producer and the model identifier, or the label the producer handed over. It is shown on the product page, before the place-order button, in the cart and in the order email.
* Left out of purchases made only of virtual products, since the legal guarantee covers goods.
* A private note on each order with what the customer was shown: how and in which language the notice appeared at the checkout, the GARAN labels and the email that carried the official PDF.
* Every placement chooses its own wording style — text, icon and text, or icon only — and its own colours, hover colours and font size, all starting on "inherit from the theme".
* In the customer order emails, which the guidelines also ask for, with the official PDF attached in the customer's language.
* The clickable link to Your Europe that has to accompany the notice, in the right language.
* A national note beside the notice for the three years of legal guarantee in Spain and Portugal and the three years to claim in Sweden, which the uneditable European notice cannot state.
* Your own guarantee terms, with a starting text you edit, and a button that creates a page holding both and selects it as your terms page.
* Shortcodes `[apg_guarantee_notice]`, `[apg_guarantee_terms]` and `[apg_guarantee_button]`, and the same three as blocks, plus a GARAN label block for the product template.
* A reminder of the legal guarantee and the GARAN label of each line on the PDF invoices of PDF Invoices & Packing Slips for WooCommerce.
* Hooks to hide the notice per placement and per customer, for shops that also sell to trade customers, to change its wording and the email attachment, and to print your own content before, after or instead of it.
* Warns you in the dashboard and in Site Health when no placement is enabled and the notice would not be reaching anyone.
* WPML and Polylang ready for the wording you write, through `wpml-config.xml` and runtime string registration.
* The notice is cached by the browser for a year, so it costs one request per visitor.
* Works with or without WooCommerce: with it the notice reaches the checkout and the order emails, without it everything else still works.
* No colour, border or font is set by the plugin unless you ask for it, so it inherits the look of your theme, dark themes included.
* Contextual help inside the plugin, in the *Help* tab of the settings screen, with one panel per block and a link to the full documentation.

= Translations =
* English: by [Art Project Group](https://artprojectgroup.com/) (default language).
* Spanish: by [Art Project Group](https://artprojectgroup.com/).

= Technical support =
**APG Legal Guarantee Notice** is a free plugin. **Art Project Group** does not provide free technical support, but offers a paid [technical support](https://artprojectgroup.com/shop/technical-support) service for installation and configuration.

= More information =
You can learn more about **APG Legal Guarantee Notice** on our [official website](https://artprojectgroup.com/wordpress-plugins/apg-legal-guarantee-notice), and follow the development on [GitHub](https://github.com/artprojectgroup/apg-legal-guarantee-notice).

You can also read the [full documentation](https://artprojectgroup.com/documentation/apg-legal-guarantee-notice), with every setting explained one by one.

== Installation ==

1. Install the plugin in one of the following ways:
 * Upload the `apg-legal-guarantee-notice` folder to `/wp-content/plugins/` over FTP.
 * Upload the ZIP file from *Plugins -> Add New -> Upload* in your WordPress admin.
 * Search for **APG Legal Guarantee Notice** under *Plugins -> Add New* and click *Install Now*.
2. Activate it from the *Plugins* menu.
3. The floating button appears straight away, which on its own satisfies the obligation. Review *WooCommerce -> Legal guarantee* to move it, restyle it or place it elsewhere.

== Frequently Asked Questions ==

= Do I have to do anything for the notice to be compliant? =
No. Activating the plugin puts a floating button on every page of your shop, which is the "general reminder on the website of the seller" the Commission's practical guidelines describe, at shop level. The checkout and the order email are on by default too because the guidelines illustrate both. If you switch every placement off, the plugin tells you in the dashboard.

= Can I edit the notice, translate it myself or crop it? =
No, and the plugin will not let you. Implementing Regulation (EU) 2025/1960 fixes its design and content, and the guidelines say it must not be distorted or cropped. The plugin bundles the Commission's own files and serves them byte for byte, exactly as they come out of its asset pack.

= My country grants a longer guarantee than the two years in the notice. =
That is why the national note exists. The notice states the European minimum and cannot say anything else, so the plugin prints your national wording next to it, never inside it. For a shop based in Spain, Portugal or Sweden it starts enabled with the wording of that country, and you can replace it and link it to your terms. The country is read from the WooCommerce base location, never from the language of the site.

= Does it need JavaScript? =
No. The notice opens with the native popover API, so it works with a keyboard, with a screen reader and with scripts disabled. Focus moves into the modal when it opens and Escape closes it.

= Which languages does it cover? =
The 24 official EU languages the Commission publishes: Bulgarian, Croatian, Czech, Danish, Dutch, English, Estonian, Finnish, French, German, Greek, Hungarian, Irish, Italian, Latvian, Lithuanian, Maltese, Polish, Portuguese, Romanian, Slovak, Slovenian, Spanish and Swedish. Locales with no official notice of their own fall back sensibly, and the `apg_guarantee_language` filter overrides the choice.

= What about the GARAN label? =
The EU GARAN label marks a commercial guarantee of durability that a **producer** offers free of charge, for the whole good, for more than two years. Offering it is the producer's decision; showing it, when the producer offers it, is the seller's obligation. Fill in the *EU GARAN* tab of the product with the duration, the producer and the model identifier the producer gives you, and the plugin draws the official label with them, in the Inter typeface the guidelines name. Each variation can set its own. If the producer gave you the label already filled in, upload it in the same tab instead.

= Do I have to show the notice in full at the checkout? =
The Commission's guidelines illustrate a sentence that opens the notice on the first click, and that is the default. Part of the German doctrine reads Article 246a EGBGB as asking for the whole notice at the checkout, with nothing to click. If you sell in Germany or Austria, or prefer to be on the safe side, choose *The whole notice* under *How it is shown*.

= I also sell to trade customers. Can I hide the notice from them? =
Yes. The notice is owed to consumers only, but WordPress and WooCommerce cannot tell a consumer from a trade customer, and every wholesale or B2B plugin marks them its own way. So the plugin asks you through the `apg_guarantee_show_notice` filter, once per placement. For example, to hide it from the `wholesale_customer` role:

`add_filter( 'apg_guarantee_show_notice', function ( $show, $context, $order ) {
	$user_id = $order ? $order->get_customer_id() : get_current_user_id();
	return $show && ! user_can( $user_id, 'wholesale_customer' );
}, 10, 3 );`

The contexts are `cart`, `checkout`, `email`, `email_attachment`, `float`, `footer`, `invoice`, `menu`, `product` and `shortcode`, and `$order` is only set in the email and invoice contexts. In the emails the decision is exact because the order says who bought; elsewhere it relies on the customer being logged in, since an anonymous visitor cannot be told apart and a full-page cache serves them all the same page.

To print your own trade terms where the notice was hidden, use the `apg_guarantee_notice_hidden` action. The rest of the hooks:

* `apg_guarantee_before_notice` and `apg_guarantee_after_notice` (actions): content around the notice at the checkout, in the email and in `[apg_guarantee_notice]`. They receive `$context`, `$order` and `$plain_text`, as `apg_guarantee_notice_hidden` does.
* `apg_guarantee_trigger_text`: the line that opens the notice, with `$context` (`panel` for the modal title).
* `apg_guarantee_your_europe_link_text`: the wording of the link to Your Europe.
* `apg_guarantee_national_note_text`: the national note; an empty string leaves it out.
* `apg_guarantee_email_attachment_path`: the file attached to the email; an empty string attaches nothing.
* `apg_guarantee_has_goods`: whether a purchase contains goods, for a catalogue that does not fit the rule "not virtual means goods".
* `apg_guarantee_national_note_countries`: the countries that get the national note.
* `apg_guarantee_garan_data`: the GARAN data of a product; null leaves the label off.
* `apg_guarantee_invoice_document_types`: the invoice document types that carry the reminder.

= Is this the same as the withdrawal button? =
No. That one is Article 11a, added by Directive (EU) 2023/2673, and it is covered by our [APG Withdrawal for WooCommerce](https://wordpress.org/plugins/apg-withdrawal-for-woocommerce/). This plugin covers Article 22a, which is a separate obligation. You can run both.

== Screenshots ==

1. The official notice as the visitor sees it.
2. The settings screen.

== Changelog ==
= 0.5.0 =
* New EU GARAN label per product and variation, drawn on the Commission's template and shown on the product page, before the place-order button, in the cart and in the order email.
* The checkout can now show the whole notice with nothing to click, instead of the sentence that opens it.
* New placements on the product page and in the cart, off by default.
* The notice is left out of purchases made only of virtual products.
* Each order keeps a private note of what the customer was shown.
* National notes for Portugal and Sweden, besides Spain.
* New blocks for the notice, the button, the terms and the GARAN label.
* New Site Health check, and the guarantee on the PDF invoices of PDF Invoices & Packing Slips for WooCommerce.
* The Your Europe link now goes to the same page as the QR code of the notice: in Finnish, Hungarian, Latvian, Lithuanian and Romanian it led to a missing page.
* Tested with WooCommerce 11.3.1, and "Tested up to" is declared only in the readme, as Plugin Check now requires.

= 0.4.1 =
* The links to the website, the documentation, the donation and the support now open in the language of whoever is looking at the screen.
= 0.4.0 =
* New contextual help on the settings screen, with one panel per block and a link to the full documentation.
* The warning that the notice is not being shown anywhere no longer appears twice on the plugin's own screen.

= 0.3.0 =
* New hooks to hide the notice per placement and per customer (for instance from trade customers), change its texts and the email attachment, and add your own content before, after or instead of it.
* The national note in the email is now translated with WPML and Polylang, as on the website.

= 0.2.0 =
* WooCommerce is no longer required: without it the settings move under Settings and the checkout and order email placements are hidden.
* The customer order email now carries the official PDF of the notice as an attachment, in the customer's language.

= 0.1.1 =
* The three-year Spanish guarantee note now follows the shop's country instead of the site's language.

= 0.1.0 =
* First release.

== Upgrade Notice ==
= 0.5.0 =
* EU GARAN label per product, the whole notice at the checkout as an option, new product page and cart placements, a record on each order, blocks and a fix for five broken Your Europe links.

= 0.4.0 =
* New contextual help on the settings screen, with one panel per block.

= 0.3.0 =
* New hooks to hide the notice per placement and per customer, change its texts and add your own content around it.

= 0.2.0 =
* WooCommerce is no longer required: without it the settings move under Settings and the checkout and order email placements are hidden.
* The customer order email now carries the official PDF of the notice as an attachment, in the customer's language.


== Thanks ==

Thanks to everyone who uses the plugin, helps improve it, makes a donation or encourages us with their comments.

If you find this plugin useful, you can support its development with a [small donation](https://artprojectgroup.com/shop/donation).

== The bundled notice files ==

The image and PDF files under `assets/notices/` are the official harmonised notice on the legal guarantee of conformity, as published by the European Commission and as Commission Implementing Regulation (EU) 2025/1960 fixes it. They are not the plugin's own work and they are not covered by its GPL licence: they are European Commission documents, reused under Commission Decision 2011/833/EU, which authorises the reuse of Commission documents free of charge provided the source is acknowledged. The source is acknowledged here and beside the notice itself, which links to the Commission's Your Europe portal.

The plugin never edits them, never redraws them and never resizes them: it ships them as they came and serves the bytes it was given. Twenty-three languages are the Commission's PNG, and English is its SVG, because its raster pack has no English file while its vector pack does. The PDF of every language is the one attached to the order email.

The files under `assets/garan/` are the Commission's template of the EU GARAN label, from the same source and under the same Decision. The regulation makes three of its fields editable (the duration, the producer and the model identifier), and those are the only ones the plugin writes into; the sample text of those three fields has been taken out of the template so they can be filled in. The raster versions used for the emails are that template rendered as it is.

The label is set in Inter, the typeface the guidelines name, by The Inter Project Authors, licensed under the SIL Open Font License 1.1 (`assets/garan/inter-OFL.txt`). The plugin ships a subset of it with the characters the label needs.

== External services ==

The notice files and the GARAN template travel inside the plugin and nothing is downloaded to serve them. The GARAN labels are drawn on your own server and kept in your uploads folder.

The plugin makes one request to an external service, and only there:

* **wordpress.org plugin API** (`https://api.wordpress.org/plugins/info/1.2/`). Asked for the plugin's own star rating, so the settings screen can show it. It happens only while an administrator is looking at the plugin's settings screen, at most once a day, and the answer is cached for 24 hours. The request carries the plugin slug and nothing else: no personal data, no site data, no visitor data. It is never made on the front end. Governed by the [WordPress.org privacy policy](https://wordpress.org/about/privacy/) and the [terms](https://wordpress.org/about/privacy/). If the request fails the screen simply says the rating is unknown.

The notice itself contains a QR code, and the plugin prints the matching clickable link, both pointing at the European Commission's Your Europe portal (`https://europa.eu/youreurope/...`). Those are links the visitor may choose to follow; nothing is requested from that site by the plugin.
