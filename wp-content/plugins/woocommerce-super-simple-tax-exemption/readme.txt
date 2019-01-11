=== Woocommerce Super Simple Tax Exemption ===
Contributors: (poldira)
Donate link: http://mkt.com/bobbie-wilson/woocommerce-super-simple-tax-exemption-donate
Tags: woocommerce, no tax, tax exempt, tax exempt ID, tax-exempt, checkout
Requires at least: 3.5
Tested up to: 4.0
Stable tag: 1.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A plugin to add simple tax exemption to the Woocommerce checkout page. Records the Tax Exempt ID to the order meta.

== Description ==

This simple plugin will update the Woocommerce checkout page totals to remove calculated taxes. Adds the user input Tax Exempt ID to the order meta for easy tracking.

== Installation ==


1. Upload the directory `woocommerce-super-simple-tax-exempt` to the `/wp-content/plugins/` directory or upload the zip file using the 'Uploads' feature in the Plugins dashboard.
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= I don't see the checkout page totals updating. What am I missing? =

This could be many things. What stumped me at first, was that all the fields needs to be completed before the checkout page will calculate anything. Make sure that Tax Exempt ID has something in it. If none of these work, double-check your tax settings in Woocommerce.


== Changelog ==

= 1.0 =
* First version. Woot!

= 1.1 =
* Fixed Woocommerce detection

= 1.2 =
* Fixed Tax ID Order Meta (Woocommerce now uses get_post_meta)

= 1.3 =
* Fixed order processing error. Updated donation URL.