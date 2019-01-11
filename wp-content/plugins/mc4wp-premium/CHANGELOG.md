Changelog
==========

#### 4.5.5 - January 8, 2019

**Improvements**

- E-commerce: don't send product variants in bulk to prevent HTTP timeouts.
- E-commerce: only send address property on customer objects if it looks like a complete address.
- E-commerce: properly exclude orders without an email address from being attempted using the order sync wizard.
- E-commerce: trim special characters from order number.
- E-commerce: update pending background jobs number after queue is processed over AJAX.
- Forms: add `.mc4wp-loading` class attribute to form elements while request is loading.


#### 4.5.4 - November 26, 2018

**Improvements**

- E-commerce: Rewrite SQL queries on e-commerce settings page for improved performance.
- E-commerce: Add visiblity property to product data sent to MailChimp.
- E-commerce: For guest checkouts, create cart in MailChimp right away instead of waiting 5 seconds.

**Additions**

- E-commerce: Add setting to disconnect store from MailChimp without deleting it.


#### 4.5.3 - October 22, 2018

**Fixes**

- E-commerce: WooCommerce 2.3 compatibility.
- E-commerce: Don't create new transactional subscriber when customer changes their email in WooCommerce.
- E-commerce: Timezone not taken into account for displaying time on e-commerce settings page.

**Improvements**

- E-commerce: Automatically update email addresses of subscribers for the connected list in MailChimp when they change it in WooCommerce.


#### 4.5.2 - September 11, 2018

**Improvements**

- E-commerce: process background jobs every minute.
- E-commerce: update fulfilment_status to trigger shipping notifications.
- E-commerce: improved compatibility with older WooCommerce versions.


#### 4.5.1 - Jul 25, 2018

**Fixes**

- AJAX Forms: fixed issue with caching an unset variable.


#### 4.5 - July 11, 2018

**Improvements**

- E-commerce: Database query performance improvements on settings page.
- E-commerce: Show when next run is scheduled to run for processing pending background jobs.
- E-commerce: Send billing company field to MailChimp when updating customer.

**Additions**

- E-commerce: Add bulk action to (re-)synchronise orders, products or coupons with MailChimp.
- E-commerce: Added support for promo codes (coupons).
- E-commerce: New WP-CLI command wp mc4wp-ecommerce sync-coupons
- E-commerce: New filter hook mc4wp_ecommerce_promo_rule_data
- E-commerce: New filter hook mc4wp_ecommerce_promo_code_data


#### 4.4.4 - June 27, 2018

**Fixes**

- Method parameter error when using WooCommerce 2.x.
- Abandoned cart data error when using WooCommerce 2.x.

**Improvements**

- Sanity check on orders for lines that have a quantity of 0.



#### 4.4.3 - June 25, 2018

**Improvements**

- Email notifications: Accept a broader range of date inputs (year first, leading zeroes for day's and month's)


#### 4.4.2 - June 11, 2018

**Fixes**

- E-commerce: WP CLI command error because user object can not be represented as string.
- Append form: Catch exceptions in append form to posts feature.

**Improvements**

- E-commerce: Reset transient cache when resetting store.
- E-commerce: Reset transient cache after adding/updating/deleting products and orders.
- E-commerce: Failsafe against invalid session data for abandoned carts.
- Email notifications: Format date in local format


#### 4.4.1 - May 21, 2018

**Fixes**

- E-commerce: Error when running abandoned cart recovery with older WooCommerce versions (before 3.2).
- AJAX forms: Notice for unexisting variable.

**Improvements**

- E-commerce: Failsafe return value of `wc_get_product`


#### 4.4 - May 11, 2018

**Improvements**

- E-commerce: move slower operations (like calculating the customer's order total) when generating abandoned cart data to a background job, to speed up website for the visitor.
- E-commerce: store results of slow queries in a 1-hour cache to speed up e-commerce settings page.

**Additions**

- E-commerce: added filter `mc4wp_ecommerce_schedule_process_queue_event` which can be used to disable the WP Cron event that is scheduled.
- Logging: add setting to automatically delete log items older than a number of days.
- Logging: include log data in GDPR export request.
- Logging: erase log data in GDPR erase request.


#### 3.3.34 - April 23, 2018

**Fixes**

- E-commerce: issue with updating orders when using custom order numbering.

**Improvements**

- E-commerce: add button to clear all pending background jobs.
- E-commerce: soften error message when an order without items is skipped through the order synchronisation wizard.
- Logging: all logging can now be disabled by setting `define( 'MC4WP_LOGGING', false );`


#### 3.3.33 - April 17, 2018

**Fixes**

- Links in "Need help?" box search results not pointing to the correct domain.

**Improvements**

- E-commerce: Process queue every 3 minutes instead of every 5 minutes.
- E-commerce: Use less important log level when order or cart contains no items.


#### 3.3.32 - April 10, 2018

**Fixes**

- E-commerce: Send correct product variation ID to MailChimp again.
- E-commerce: Redirect after re-populating cart from MailChimp to clear URL parameters.

**Improvements**

- Forms: Blur active input element after submitting form with RETURN key, so that keyboard disappears on mobile.


#### 3.3.31 - March 28, 2018

**Fixes**

- Reports: Log items were sometimes counted on multiple days in the reports graph.
- E-commerce: On certain servers, background jobs were not queued up for updated carts or orders.

**Improvements**

- E-commerce: Stop unnecessary info logging for guest checkouts.
- E-commerce: MailChimp does not allow connecting more than one site sharing the same domain, so attempting to load MC.js on WordPress Multisite with a subdirectory set-up would not work. We are now generating a unique subdomain and sending that to MailChimp (instead of the subdirectory) so that everything works as expected.
- E-commerce: When order update fails because of an invalid `campaign_id`, retry without that parameter.


#### 3.3.30 - March 20, 2018

**Improvements**

- E-commerce: campaign tracking cookies are now set via JavaScript so that it works with pages served from cache.

**Additions**

- Form setting to automatically append that form to all posts (in a certain category).


#### 3.3.29 - March 12, 2018

**Fixes**

- "Order already exists" errors when using custom order numbers.

**Improvements**

- Improved handling of deleted product variations for historical orders.


#### 3.3.28 - February 26, 2018

**Fixes**

- Catch exception when periodic license check fails.

**Improvements** 

- Forms: show success message even when redirecting to another page.
- E-commerce: validate product existence before sending to MailChimp.
- E-commerce: only send product variants if variable product has existing children.
- E-commerce: throw exception when order has no order lines.
- Reports: Print submission data as JSON instead of PHP object.


### 3.3.27 - February 2, 2018

**Fixes**

- Reports: "Last year" view not showing the correct graph.
- E-Commerce: Send products in "trash" to MailChimp too, but with stock inventory of 0.

**Improvements**

- Minor memory usage improvements.
- Updated all JavaScript dependencies.
- E-Commerce: Fallback on `user_email` for abandoned cart tracking, when `billing_email` is unknown.

**Additions**

- E-Commerce: Add `--delay` option to [all bulk WP CLI commands](https://kb.mc4wp.com/ecommerce-wp-cli-commands/).
- E-Commerce: Add `wp mc4wp-ecommerce reset-queue` command to clear all queued jobs.


### 3.3.25 - January 8, 2018

**Fixes**

- Fire correct event `updated_subscriber` when using AJAX forms.
- Fire individual form events when using AJAX forms.


### 3.3.24 - December 22, 2017

**Additions**

- E-commerce: added support for loading MC.js file from MailChimp, allowing for product retargeting and pop-ups.
- E-commerce: added `mc4wp_ecommerce_store_data` filter hook for modifying store data before it is sent to MailChimp.

**Improvements**

- E-commerce: use order getter methods to generate order data for MailChimp.
- E-commerce: send site URL instead of just the domain part to allow for two sites to connect their store from the same domain.


### 3.3.23 - December 11, 2017

**Improvements**

Styles-Builder: Add option to set `background-size: cover` so that image stretches entire element.
Styles-Builder: Check form validity after every input change.


#### 3.3.22 - November 17, 2017

**Improvements**

- E-commerce: Look for billing_email for customer & order objects in more places.
- Email notifications: Replace `[_MC4WP_LISTS]` tag properly.
- Email notifications: Send email for "unsubscribed" event too.


#### 3.3.21 - October 31, 2017

**Fixes**

- In-plugin [knowledge base](https://kb.mc4wp.com/) returned no results regardless of search query (since a few days).

**Improvements**

- No longer flashing success message when form has a redirect URL.
- Minor performance optimisations for generating asset URL's.


#### 3.3.20 - October 19, 2017

**Fixes**

- Issue with abandoned cart links showing an empty cart for guest visitors on some specific site configurations.


#### 3.3.19 - October 6, 2017

- e-commerce: fix campaign tracking when using Sequential Order Numbers plugin for WooCommerce.
- e-commerce: refactor tracking class so that request values are always excluded on forced sync.
- email notifications: catch exceptions when generating email message.
- email notifications: replace `INTERESTS` tag in email message with readable interest group names.
- added `mc4wp_ecommerce_options` filter hook.


#### 3.3.17 - July 25, 2017

**Improvements**

- e-commerce: Improve unique cart ID for abandoned carts so that automations can be re-triggered.


#### 3.3.16 - July 24, 2017

**Improvements**

- e-commerce: Clear local store ID too when resetting e-commerce data.
- e-commerce: Only set landing page cookie when mc_cid URL parameter is set.


#### 3.3.15 - July 13, 2017

**Improvements**

- e-commerce: allow other plugins to filter the order number by using `get_order_number()`
- e-commerce: save store ID in options so that site URL can change without affecting automations.
- e-commerce: Send shipping & billing address with every order, so they show in Order Notifications.


#### 3.3.14 - June 16, 2017

**Fixes**

- e-commerce: timeout when synchronising products if running an outdated version of WooCommerce.

#### 3.3.13 - June 14, 2017

**Improvements**

- e-commerce: enforce MailChimp API JSON schema for abandoned cart data.
- e-commerce: protect against endless loops when synchronising products with corrupt data.
- e-commerce: include more (optional) store parameters in API calls to MailChimp: email address, primary locale, platform & store domain.

#### 3.3.12 - June 1, 2017

**Improvements**

- e-commerce: Added a setting to send all order statuses to MailChimp (incl. failed, refunded and cancelled orders)
- e-commerce: No longer triggering a database query on every checkout page load.
- Use new mc4wp.com API for license & update checks.


#### 3.3.11 - May 16, 2017

**Fixes**

- E-commerce: order notifications never triggering.

**Improvements**

- E-commerce: improved backwards compatibility with with WordPress versions before 4.5
- Styles Builder: improved permission check for writing stylesheet file(s).
- Email Notifications: accept flexible array format in `mc4wp_form_email_notification_headers` filter hook.

**Additions**

- E-commerce: track landing page URL and send to MailChimp when synchronising orders.


#### 3.3.10 - April 28, 2017

**Fixes**

- E-commerce: fixes notice when synchronising products using WooCommerce 2.x
- E-commerce: error when repopulating abandoned cart with product variations.
- Styles Builder: image upload button not setting URL as input field value.


#### 3.3.9 - April 13, 2017

**Fixes**

- E-commerce: unexisting method calls when using WooCommerce 3.0 or later.


#### 3.3.8 - April 13, 2017

**Fixes**:

- Headers already sent error when trying to redirect to e-commerce setup wizard.

**Improvements**

- Reports: Update JavaScript dependencies for graph.
- Reports: Fix jumpy graphs when MySQL has an incorrect timezone setting.
- E-Commerce: WooCommerce 3.0 compatibility.
- E-Commerce: Send correct stock status when manually managing stock.
- Reduced memory footprint.

**Additions**

- E-Commerce: `order_url` is now sent to MailChimp.
- E-Commerce: support for Order Notifications.
- New filter hook: `mc4wp_ecommerce_cart_data` for filtering abandoned cart data.
- New filter hook: `mc4wp_ecommerce_order_data` for filtering order data.
- New action hook: `mc4wp_ecommerce_restore_abandoned_cart` for restoring an abandoned cart.


#### 3.3.7 - February 14, 2017

**Fixes**

- E-commerce: Fixes "Schema describes ..." errors by enforcing MailChimp's JSON scheme.
- Use correct email address when updating an abandoned cart if user has two email addresses.

**Improvements**

- E-commerce: Strip HTML from product titles.
- E-commerce: Update customer data in MailChimp separately from updating a cart.

**Additions**

- E-commerce: Added `mc4wp_ecommerce_product_data` filter to modify product data before sending to MailChimp.


#### 3.3.6 - January 16, 2017

**Improvements**

- E-commerce: Handle deleted products in (old) orders by sending a "generic deleted product" to MailChimp.
- E-commerce: Update parent product instead of individual product variants.
- Email notifications: Write info line to debug log whenever an email is sent.

**Additions**

- E-commerce: Add `mc4wp_ecommerce_send_order_to_mailchimp` filter hook.

**Fixes**

- E-commerce: Products missing top level `url` attribute would break product block in MailChimp campaigns.


#### 3.3.5 - December 20, 2016

**Fixes**

- Fatal error on sites still running on PHP 5.2.


#### 3.3.4 - December 12, 2016

**Improvements**

- E-commerce: Force-save queue when processing items, because save action may never be called on very long-lived processes.
- AJAX Forms: Replace configuration with lazy loaded config store to work better with WP Rocket.
- E-commerce: Add help text about connecting your store to a different MailChimp list.


#### 3.3.3 - November 30, 2016

**Fixes**

- E-commerce: `ajaxurl` not set on WooCommerce checkout when capturing guest email for cart tracking.

**Improvements**

- E-commerce: Various SQL performance optimisations
- E-commerce: Don't exit WP CLI command on errors.
- E-commerce: Use `shop_single` image size for products in MailChimp (instead of the smaller thumbnail)


#### 3.3.2 - November 23, 2016

**Improvements**

- E-commerce: Background queue won't stall on PHP errors now.
- E-commerce: Show "last updated" time on settings page.
- E-commerce: Show status text while processing background jobs.
- E-commerce: Don't try to send abandoned carts without an email address.
- E-commerce: Write PHP errors in background queue to debug log.


#### 3.3.1  - November 2, 2016

**Fixes**

- E-commerce: "Schema describes string, integer found instead" error when adding orders.

**Improvements**

- Don't show e-commerce settings when store is not connected to a list yet.
- E-commerce: When campaign data for an order becomes corrupted, we'll now auto-retry without that campaign data.
- E-commerce: Check if user has `billing_email` before updating remote customer.
- E-commerce: Send "draft" products to MailChimp too. These will not be included in Product Recommendations.
- E-commerce wizard now always starts with most recent orders.
- Failsafe against including AJAX script for forms twice.

**Additions**

- Add `mc4wp_ecommerce_customer_data` to filter customer data that is sent to MailChimp.


#### 3.3 - October 18, 2016

This release allows you to [integrate your WooCommerce store with MailChimp's newest API](https://mc4wp.com/kb/upgrading-ecommerce-api-v3/), enabling cool things like [product recommendations](https://mailchimp.com/features/product-recommendations/) & [abandoned cart recovery](https://mailchimp.com/features/abandoned-cart/).


#### 3.2.2 - September 8, 2016

**Fixes**

- Remove usage of PHP 5.3 constant in activation hook.
- Don't send admin cookies when manually adding past e-commerce orders.

**Improvements**

- Strip special characters from e-commerce product names.
- Show Top Bar sing-ups in Reports > Statistics graph.
- Make all columns in log table sortable.
- Memory usage improvements for AJAX forms & in-plugin knowledge base search.
- Improved compatibility with older versions of WooCommerce.


#### 3.2.1 - August 11, 2016

**Fixes**

- HTML tags showing in plain text email notifications when using `[_ALL_]` tag.
- Order of radio inputs on RTL sites.

**Improvements**

- Better margin in Styles Builder when running MailChimp for WordPress 4.0.
- Default to `px` widths in Styles Builder so they don't have to be explicitly given.
- Minor performance improvements.

**Additions**

- Added text color option to Styles Builder for notices.


#### 3.2 - August 4, 2016

**Fixes**

- Never use `[UNIQID]` as a campaign identifier in e-commerce tracking.
- Fatal error when adding old orders that reference deleted WooCommerce products.

**Improvements**

- Better data structure for sign-up logging, so importing into MailChimp is easier.
- New detailed item view for all successful sign-up attempts.
- Log export now uses TAB as a delimiter.
- Logging compatibility with upcoming [MailChimp for WordPress 4.0 release](https://mc4wp.com/kb/upgrading-to-4-0/).


#### 3.1.10 - July 13, 2016

**Fixes**

- Issue with Manual CSS in Styles Builder being escaped.


##### 3.1.9 - June 21, 2016

**Fixes**

- eCommerce360: campaign cookie wasn't taken into account for orders that require manual action before they are "completed"

**Improvements**

- Styles Builder: Improved file creation.
- Styles Builder: Better error messages
- Styles Builder: Don't show "copy from other form" dropdown if there is just 1 form.


##### 3.1.8 - June 7, 2016

**Fixes**

- eCommerce360 campaign cookie was stored for just 7 hours, instead of 7 days.
- Custom colored theme not printing CSS styles.
- Log export not working on Windows servers.

**Improvements**

- eCommerce360 order sync now stops on errors.
- Show "draft" forms on Forms overview page.


##### 3.1.7 - May 23, 2016

**Fixes**

- AJAX loading animation now works with `<button>` elements too.

**Additions**

- Filter: `mc4wp_ecommerce360_order_data`, to modify order data before it is sent to MailChimp.
- Filter: `mc4wp_ecommerce360_order_statuses`, specifies which order statuses are sent to MailChip.

##### 3.1.6 - May 10, 2016

**Fixes**

- `GROUPINGS:123` tag not working in email notifications.

**Improvements**

- Set `action` GET parameter for AJAX requests for compatibility with WP-SpamShield.
- Set proper `Allow-Control-Allow-Origin` headers for AJAX requests.
- eCommerce360: Add WooCommerce variations to product name.

**Additions**

- New "Advanced" tab under Reports with form to delete all Log items at once.


##### 3.1.5 - April 18, 2016

**Fixes**

- WooCommerce orders with an associated user account (instead of guest email address) were not being recorded by the "add past orders" screen.
- Running the log export on PHP 5.2 would return an empty CSV file


##### 3.1.4 - March 30, 2016

**Fixes**

- eCommerce360 would try to add orders without an email address to MailChimp.

 **Improvements**

 - Ensure all WooCommerce filters run when sending order data to MailChimp.
 - When AJAX form script errors, the form now falls back to a default form submission.
 - Grouping data is now shown in log table again.

**Additions**

- New "Order Action" for WooCommerce to manually add or delete an eCommerce360 order to/from MailChimp.


##### 3.1.3 - March 22, 2016

**Fixes**

- Script for plotting Reports graph wasn't loaded on some servers.

**Improvements**

- Use later hook priority for rendering form preview in Styles Builder for compatibility with Pagelines DMS.
- Update script dependencies to their latest versions.
- Escape form name throughout settings pages.


##### 3.1.2 - February 29, 2016

**Fixes**

- Email notification didn't get correct `Content-Type` header for HTML.

**Improvements**

- Get form preview to work with unsaved Styles Builder stylesheet.
- Minor improvements for setting button colors in Styles Builder stylesheet.

##### 3.1.1 - February 16, 2016

**Fixes**

- Log would throw error when list had percentage-sign in their name.

**Improvements**

- All `mc4wp_form_email_notification_*` filters now have access to the submitted form (as the second parameter).
- Add JavaScript sourcemaps to minified JS scripts.
- Remove sourcemaps from unminified JS scripts
- Log now takes `mc4wp_lists` filter into account.
- Use earlier hook for Styles Builder preview to prevent incompatibility with some themes.
- Load Styles Builder stylesheet in TinyMCE editor for improved [Shortcake](https://wordpress.org/plugins/shortcode-ui/) support.

**Additions**

- Added `mc4wp_form_email_notification_headers` filter.
- Added `mc4wp_form_email_notification_attachments` filter.


##### 3.1 - January 26, 2016

**Additions**

- [eCommerce360 integration](https://mc4wp.com/kb/what-is-ecommerce360/) for WooCommerce and Easy Digital Downloads.
- [WP-CLI commands for eCommerce360](https://mc4wp.com/kb/ecommerce360-wp-cli-commands/).

**Improvements**

- Log: modify items per page in screen options when viewing log.
- Miscellaneous code improvements


##### 3.0.6 - January 18, 2016

**Fixes**

- Pagination not showing on Log overview page.

**Improvements**

- Memory usage improvements to Log export for huge datasets.
- Make sure Styles Builder stylesheet is loaded over HTTPS when needed.


##### 3.0.5 - December 15, 2015

**Fixes**

- Button to export log was no longer working after version 3.0

**Improvements**

- Reintroduced support for `data-loading-text` on submit buttons.
- Improved logic for loading animation in submit buttons.
- Styles Builder: Success & error color is now applied to paragraph element, to make sure theme doesn't override the given style.

**Additions**

- Improved email notifications. You can now easily modify the subject line & message body of the email that is sent.


##### 3.0.4 - December 10, 2015

**Fixes**

- Not being able to access Forms page when using strict error reporting.

**Additions**

- Use `mc4wp_use_sslverify` filter to detect whether SSL verification should be used in remote requests.


##### 3.0.3 - December 1, 2015

**Fixes**

- Fatal error when visiting Forms overview page on older PHP versions.

##### 3.0.2 - November 26, 2015

**Fixes**

- Stylesheet file not loaded because of 403 error (due to incorrect file permissions).

= 3.0.1 - November 25, 2015 =

**Improvements**

- AJAX Forms: Perform core logic before triggering events, to prevent erorrs in event callbacks from messing up form flow.
- Styles Builder: Changes are now applied instantly.
- Styles Builder: Clearing a color no longer resets all styles.
- Styles Builder: Try to set correct permissions before writing stylesheet to file.
- KB Search: Make sure all links point to [mc4wp.com](https://mc4wp.com).

**Additions**

- Add link to "Appearance" tab on Forms overview page.


##### 3.0.0 - November 23, 2015

Initial release.

This plugin requires [MailChimp for WordPress](https://wordpress.org/plugins/mailchimp-for-wp/) v3.0 or higher to work.
