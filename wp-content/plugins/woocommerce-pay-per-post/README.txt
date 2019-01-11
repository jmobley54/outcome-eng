=== WooCommerce Pay Per Post ===
Contributors: mattpramschufer, freemius
Tags: woocommerce, payperpost, pay-per-post, pay per post, woo commerce, sell posts, sell pages
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=mattpram%40gmail%2ecom
Requires at least: 3.8
Requires PHP: 5.6
Tested up to: 5.0.2
Stable tag: 2.1.16
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Sell Pages/Posts through WooCommerce 2.6+ quickly and easily. Tested up to WooCommerce Version 3.5.2.

== Description ==
Quickly and easily sell access to pages, posts and custom post types through WooCommerce with WooCommerce Pay Per Post.

= Requirements =
This plugin DOES require WooCommerce to be installed and active.  I have tested this with the latest version to date Version 3.5.2.

I originally created this plugin because I looked everywhere and I couldn't find a plugin already out there, free or premium, that would do the simple fact of selling access to a particular page or post through WooCommerce.  So I decided to write my own.

This plugin creates a custom field which you simply need to browse for your product, or multiple products, which are needed to be purchased in order view the post.

It checks to make sure the user is logged in, AND has purchased that particular product before.  If they have, they see the general post content.  If they have NOT purchased they are then displayed a Restriction Content message with a buy now button for the product.

= Shortcodes =

`[woocommerce-payperpost template='purchased']`
This outputs an unordered list of the posts that have been purchased by the current user logged in.

`[woocommerce-payperpost template='remaining']`
This outputs an unordered list of the posts that have yet to be purchased by the current user logged in.

`[woocommerce-payperpost template='all']`
This outputs an unordered list of the posts that can be purchased by a user.

= Template Functions =
Out of the box this plugin will work with any theme which uses the standard Wordpress function `the_content()` for those themes that do not utilize `the_content()` you can use the following static functions in your templates.

`Woocommerce_Pay_Per_Post_Helper::has_access()`
This checks if the current user has access to the page.  It returns true/false

`Woocommerce_Pay_Per_Post_Helper::get_no_access_content()`
This returns the content specified in the PPP Options.

And example of this working together would be something like the following
```
<?php if(Woocommerce_Pay_Per_Post_Helper::has_access()): ?>
    This is the content that a user should see if they paid for the post
<?php else: ?>
    <?php echo Woocommerce_Pay_Per_Post_Helper::get_no_access_content(); ?>
<?php endif; ?>
```

== PREMIUM ONLY FEATURES ==
The premium version of this plugin consists of over 300 hours of development!

Features:

* Page View Restriction
* Post Expiration Restriction
* Delay Restriction
* Powerful shortcode features
* Ability to override restricted content message per post
* Premium support
* and much more!


== Installation ==
1. Activate the plugin through the `Plugins` menu in WordPress
1. Browse through the WooCommerce Pay Per Post settings
1. Go to Page or Post and you should see a meta box for WooCommerce Pay Per Post.


== Frequently Asked Questions ==

= Can this plugin work with custom post types? =

Yes, this plugin worked with all custom post types.  In the settings you can add and remove which post types you would like the metabox to show up on.

= How do you link to your post after an order has been placed? =

What I have done in the past is use the Order Notes for the product in WooCommerce. So what will happen is after they purchase, on the Payment Received page they will see the order notes, and they will get sent in the receipt also.

So for instance, I have a Vimeo video that I embed in a page, on the Vimeo Product in WooCommerce I add the Password and notes on how to view the video, they gets transmitted via email and on the thank you page for the user.

= Do I need to have user accounts turned on? =

Yes, in order to keep track of who purchased what, it is a requirement that all customers have user accounts/

= Do you offer support? =

Yes, I do the absolute best I can to support the free version of the plugin.  If you upgrade to the premium version you will have priority support.

== Screenshots ==

1. Settings Screen
4. Admin view of Pay Per Post CONTAINS PREMIUM FEATURES IN SCREEN SHOT
5. Frontend view of Pay Per Post NOT Purchased
6. Frontend view of Pay Per Post PURCHASED
7. Shortcode view Admin
8. Shortcode view Frontend


== Changelog ==

= 2.1.16 =
* BUG FIX - Fixed issue if admin's were allowed to view paid content, and comment restriction was enabled, to allow admins to view comments too.

= 2.1.15 =
* BUG FIX - Fixed plugin conflict with WC Email Verification by XL plugins

= 2.1.14 =
* BUG FIX - Fixed issue with PHP Warning on PHP 7.1
* UPDATE - Updated to latest Freemius SDK
* UPDATE - Confirmed compatibility with Wordpress 5.0 & WooCommerce 3.5.2
* UPDATE - Confirmed working, not pretty, but working with Gutenberg

= 2.1.13 =
* NEW FEATURE - Added in two new filters wc_pay_per_post_all_product_args and wc_pay_per_post_virtual_product_args
* UPDATE - Updated the virtual product filter to adhere to new WooCommerce meta values
* UPDATE - Addressed more pages to make them translatable
* BUG FIX - Corrected PHP warning message for invalid argument when using implode()

= 2.1.12 =
* BUG FIX - Corrected issue with debug code displaying on protected products

= 2.1.11 =
* BUG FIX - Fixed issue with comments being displayed through entire site instead of just on protected posts

= 2.1.10 =
* BUG FIX - Fixed issue where if multiple products were associated with post it would only look for the first product

= 2.1.9 =
* Updated composer to default to php 5.6 instead of php 7

= 2.1.8 =
* Modified codebase to conform with Wordpress coding standards.
* NEW PREMIUM FEATURE - Ability to turn comments off for JUST folks that have not purchased page
* NEW PREMIUM FEATURE - Added the ability to show how many pageviews / how much time was remaining before post expired.
* FIXED issue with premium page view expiration
* FIXED issue with help page tabs not working

= 2.1.7 =
* NEW PREMIUM FEATURE Ability to utilize product variations
* NEW Shortcode replacement for {{parent_id}} which is to be used with Variations to get the main product ID
* FIXED issue which help page tabs would not work

= 2.1.6 =
* REFACTOR refactored the way the Select2 javascript library was enqueued to minimize conflicts with other plugins using Select2

= 2.1.5 =
* FIXED minor bug with upgrade script that accounts for blank records on post_meta

= 2.1.4 =
* FIXED bug which if upgraded would still show as FREE license sometimes.
* UPDATED Freemius Wordpress SDK to latest version
* UPDATED POT File for translations
* UPDATED Spanish translation
* UPDATED French translation

= 2.1.3 =
* FIXED allow for multiple product ids to be show in shortcode
* FIXED issue with trial subscriptions which still showed upgrade to premium even though you were in premium trial

= 2.1.2 =
* FIXED PHP Notice on Upgrade complete page
* FIXED bug which did not account for custom post types in upgrade process
* FIXED bug in shortcode that did not account for custom post types

= 2.1.1 =
* FIXED bug which if toggle for allow admins to view protected content it would allow users to view protected content
* FIXED bug which was double encoding the restricted message before saving in database.

= 2.1.0 =
* Initial public release!

= 2.0.3 =
* fixed issue when activating and post_type options blank causing PHP notice
* Added text to clarify the Override Restricted Content Message
* Fixed issue when viewing posts in EXCEPT view that restricted content message would appear.
* Added new option to only show Virtual / Downloadable products in Products Dropdown
* Added English POT file for translations
* Added Spanish translation

= 2.0.0 =
* Complete Rewrite

= 1.4.9 =
* Changed dependency code for WC to work correctly with MU. Thanks @sdbox

= 1.4.8 =
* Tested Wordpress 4.8 Compatibility
* Tested WooCommerce 3.0.8 Compatibility
* Added in template wrapper functions to be able to integrate with more themes.

= 1.4.7 =
* Tested Wordpress 4.7 Compatibility
* Tested WooCommerce 2.6.11 Compatibility
* Laid the ground work for many new features
* Delay Restriction Coming Soon in PRO version
* Page View Restriction Coming Soon in PRO version
* Post Expiry Restriction Coming Soon in PRO version

= 1.4.6 =
* Added in a "Do Not Protect" option to the dropdown of the PPP Meta box.  You can now select that to remove restrictions from post.

= 1.4.5 =
* Changed the way I query the products to display the meta box on the admin pages.  This should correct issue with other plugin meta boxes not displaying previously inputted data.

= 1.4.4 =
* Changed the logic on custom post types.  Instead of including all post types by default and allowing users to exclude specific post types.  I now include only page, and post by default and then let users INCLUDE specific post types.
* Not sure why I didn't program it that way to begin with.  Sorry all!

= 1.4.3 =
* Updated the PURCHASED Shortcode to work with all custom post types by default.  Uses same Exclude post type functionality from settings screen.
* Fixed PHP Warning message due to type

= 1.4.2 =
* Excluded WooCommerce default custom post types from adding PPP Meta Box on.
* Added in field in settings for users to be able to exclude specific custom post types.

= 1.4.1 =
* Quick fix for the multiple select field for product ID.  Add in nopaging=true.

= 1.4 =
* Made it so if you are an ADMIN you can view the post content.  If you need to see what the Oops screen looks like, just use a non logged in user.
* Add in support for all registered custom post types, so you now do not need to hack the plugin to make it work for your custom post type!
* Made it easier to enter in product ids, you now have a multiple select box instead of just a text field
* Confirmed support for Wordpress 4.3
* Confirmed support for WooCommerce 2.4.5

= 1.3 =
* Added in the ability for multiple product IDs per post/page *
* Updated FAQ Section *
= 1.2.2 =
* Removed the pagination from the products listed out on the purchased page. *
= 1.2.1 =
* Fixed error displaying when debug mode is enabled for Missing argument 2 on get() function *
= 1.2 =
* Initial Release

== Upgrade Notice ==

= 2.1.16 =
BUG FIX - Fixed issue if admin's were allowed to view paid content, and comment restriction was enabled, to allow admins to view comments too.