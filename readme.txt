=== APG Legal Guarantee Notice ===
Contributors: artprojectgroup
Donate link: https://artprojectgroup.es/tienda/donacion
Tags: legal guarantee, consumer rights, woocommerce, eu, conformity
Requires at least: 6.0
Tested up to: 7.2
Requires PHP: 7.4
Stable tag: 0.2.0
WC requires at least: 7.0
WC tested up to: 11.1.2
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
* Four placements, each with its own settings: a floating button in any of six positions, the last item of any menu, the footer, and the checkout above the place-order button.
* Every placement chooses its own wording style — text, icon and text, or icon only — and its own colours, hover colours and font size, all starting on "inherit from the theme".
* In the customer order emails, which the guidelines also ask for, with the official PDF attached in the customer's language.
* The clickable link to Your Europe that has to accompany the notice, in the right language.
* A national note beside the notice for the three-year legal guarantee of Article 120.1 TRLGDCU in Spain, which the uneditable European notice cannot state.
* Your own guarantee terms, with a starting text you edit, and a button that creates a page holding both and selects it as your terms page.
* Shortcodes `[apg_guarantee_notice]`, `[apg_guarantee_terms]` and `[apg_guarantee_button]`.
* Warns you in the dashboard when no placement is enabled and the notice would not be reaching anyone.
* WPML and Polylang ready for the wording you write, through `wpml-config.xml` and runtime string registration.
* The notice is cached by the browser for a year, so it costs one request per visitor.
* Works with or without WooCommerce: with it the notice reaches the checkout and the order emails, without it everything else still works.
* No colour, border or font is set by the plugin unless you ask for it, so it inherits the look of your theme, dark themes included.

= Translations =
* English: by [Art Project Group](https://artprojectgroup.es/) (default language).
* Spanish: by [Art Project Group](https://artprojectgroup.es/).

= Technical support =
**APG Legal Guarantee Notice** is a free plugin. **Art Project Group** does not provide free technical support, but offers a paid [technical support](https://artprojectgroup.es/tienda/ticket-de-soporte) service for installation and configuration.

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
No, and the plugin will not let you. Implementing Regulation (EU) 2025/1960 fixes its design and content, and the guidelines say it must not be distorted or cropped. The plugin bundles the Commission's own files and serves them byte for byte. They travel gzipped only to keep the plugin small; gzip is lossless, so what leaves your server is what the Commission published.

= My country grants a longer guarantee than the two years in the notice. =
That is why the national note exists. The notice states the European minimum and cannot say anything else, so the plugin prints your national wording next to it, never inside it. For a site running in Spanish it starts enabled, stating the three years of Article 120.1 TRLGDCU, and you can replace the wording and link it to your terms.

= Does it need JavaScript? =
No. The notice opens with the native popover API, so it works with a keyboard, with a screen reader and with scripts disabled. Focus moves into the modal when it opens and Escape closes it.

= Which languages does it cover? =
The 24 official EU languages the Commission publishes: Bulgarian, Croatian, Czech, Danish, Dutch, English, Estonian, Finnish, French, German, Greek, Hungarian, Irish, Italian, Latvian, Lithuanian, Maltese, Polish, Portuguese, Romanian, Slovak, Slovenian, Spanish and Swedish. Locales with no official notice of their own fall back sensibly, and the `apg_guarantee_language` filter overrides the choice.

= What about the GARAN label? =
It is a different thing and it is voluntary. The EU GARAN label marks a commercial guarantee of durability that a **producer** offers free of charge, for the whole good, for more than two years. It is the producer's decision, not the seller's, so most shops have nothing to do about it. Support for it may come to this plugin later.

= Is this the same as the withdrawal button? =
No. That one is Article 11a, added by Directive (EU) 2023/2673, and it is covered by our [APG Withdrawal for WooCommerce](https://wordpress.org/plugins/apg-withdrawal-for-woocommerce/). This plugin covers Article 22a, which is a separate obligation. You can run both.

== Screenshots ==

1. The official notice as the visitor sees it.
2. The settings screen.

== Changelog ==
= 0.2.0 =
* WooCommerce is no longer required: without it the settings move under Settings and the checkout and order email placements are hidden.
* The customer order email now carries the official PDF of the notice as an attachment, in the customer's language.

= 0.1.1 =
* The three-year Spanish guarantee note now follows the shop's country instead of the site's language.

= 0.1.0 =
* First release.

== Upgrade Notice ==
= 0.2.0 =
* WooCommerce is no longer required: without it the settings move under Settings and the checkout and order email placements are hidden.
* The customer order email now carries the official PDF of the notice as an attachment, in the customer's language.


== Thanks ==

Thanks to everyone who uses the plugin, helps improve it, makes a donation or encourages us with their comments.

If you find this plugin useful, you can support its development with a [small donation](https://artprojectgroup.es/tienda/donacion).

== The bundled notice files ==

The 24 SVG and 24 PDF files under `assets/notices/` are the official harmonised notice on the legal guarantee of conformity, as published by the European Commission and as Commission Implementing Regulation (EU) 2025/1960 fixes it. They are not the plugin's own work and they are not covered by its GPL licence: they are European Commission documents, reused under Commission Decision 2011/833/EU, which authorises the reuse of Commission documents free of charge provided the source is acknowledged. The source is acknowledged here and beside the notice itself, which links to the Commission's Your Europe portal.

The plugin never edits them. The SVG files are stored gzipped only to keep the download small, and gzip is lossless, so the bytes that leave your server are the bytes the Commission published. The PDFs are stored as they came, because a PDF already carries its own compression.

== External services ==

The notice files travel inside the plugin and nothing is downloaded to serve them.

The plugin makes one request to an external service, and only there:

* **wordpress.org plugin API** (`https://api.wordpress.org/plugins/info/1.2/`). Asked for the plugin's own star rating, so the settings screen can show it. It happens only while an administrator is looking at the plugin's settings screen, at most once a day, and the answer is cached for 24 hours. The request carries the plugin slug and nothing else: no personal data, no site data, no visitor data. It is never made on the front end. Governed by the [WordPress.org privacy policy](https://wordpress.org/about/privacy/) and the [terms](https://wordpress.org/about/privacy/). If the request fails the screen simply says the rating is unknown.

The notice itself contains a QR code, and the plugin prints the matching clickable link, both pointing at the European Commission's Your Europe portal (`https://europa.eu/youreurope/...`). Those are links the visitor may choose to follow; nothing is requested from that site by the plugin.
