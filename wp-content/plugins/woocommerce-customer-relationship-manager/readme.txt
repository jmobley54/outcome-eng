=== WooCommerce Customer Relationship Manager ===

Author: actualityextensions
Tags: woocommerce, customers, customer, relationship, manager, crm, email, phone, call, tasks, events, groups, accounts, acf, integration, subscriptions
Requires at least: 5.0
Tested up to: 5.0.2
Stable tag: 3.5.16
Requires WooCommerce at least: 3.5
Tested WooCommerce up to: 3.5.3

Easily manage your customers and leads who purchase through your WooCommerce store.

== Description ==
Allows for an overview management of customers and their related accounts as well as managing the communication between your store and them.

= Add / Edit Customers =
Adding customers has never been easier, simply click on the 'Add Customer' button and fill the required fields you need to fill. There are extra fields added by default, however you can add more fields using the free [Advanced Custom Fields](https://wordpress.org/plugins/advanced-custom-fields/) plugin.

= Customer Status =
Manage your customers effectively using the default customer statuses which can be assigned in bulk or individually from the customer profile. Choose out of:

* Favourite
* Blocked
* Flagged
* Follow-up
* Prospect
* Lead
* Customer

= Import & Export =
Filter through your customers as you like and export the filtered result into CSV for other usage. This plugin also supports importing of customers using a mapping approach where you can map CSV fields to the existing fields.

= Existing Customers =
This plugin works instantly without needing to import your WooCommerce orders / customers. On first install, this plugin will load the customers into the CRM database. By default, the customer status of a customer who makes a purchase through your store is 'Customer'.

= Sending Emails =
Send emails to each customer individually or in bulk. These emails then appear under the customers activity for better management of communication.

= Place Calls =
Make phone calls using the default tel: client on your computer e.g. Microsoft Communicator, Skype or FaceTime. When placing the phone call, you can log the necessary information such as related information, duration of the call and purpose of the call.

= Customer Notes =
Similar to the order notes function within WooCommerce, you can also take customer notes for each customer to remember the important things for future use.

= Integrations =
This plugin is compatible with the following plugins:

* [Advanced Custom Fields](https://wordpress.org/plugins/advanced-custom-fields/)
* [WooCommerce Subscriptions](https://www.woothemes.com/products/woocommerce-subscriptions/)
* [Groups for WooCommerce](https://www.woothemes.com/products/groups-woocommerce/)

= Documentation =
You can find the documentation to [here](http://actualityextensions.com/documentation/woocommerce-customer-relationship-manager/). If there is anything missing from the documentation that you would like help on, please fill in our contact [form](http://actualityextensions.com/contact/).

= Bugs =
Should you find a bug, please do not hesitate to contact us through our support form found [here](http://actualityextensions.com/contact/).

== Installation ==
1. Upload the entire 'wocoommerce-customer-relationship-manager' folder to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Changelog ==
= 3.5.16 - 2019.01.08 =
* Fix - money spent not showing properly for some users.

= 3.5.15 - 2019.01.05 =
* Fix - issue with newsletter API not working with Mailchimp.
* Fix - WordPress 5.0 checks.

= 3.5.14 - 2018.11.19 =
* Fix - agent can view non assigned customers.
* Fix - accounts were not being respected when changed from customer to another.

= 3.5.13 - 2018.11.13 =
* Feature - control what customers the agent can view.
* Fix - customer not assigning after creation of new task after WC 3.5.x update.

= 3.5.12 - 2018.11.10 =
* Fix - guests not appearing after WooCommerce 3.5.x update.
* Fix - update customer not working after WooCommerce 3.5.x update.

= 3.5.11 - 2018.10.29 =
* Fix - agents were not being automatically assigned when creating a new customer.

= 3.5.10 - 2018.10.29 =
* Integration - integration with the new ACF plugin.
* Tweak - assigning the favourite products to the customer is now search based.

= 3.5.9 - 2018.10.22 =
* Fix - email log errors were appearing when emails were being sent.
* Feature - ability to change and view the customer status from the order details page.

= 3.5.8 - 2018.10.10 =
* Fix - agent cannot view assigned customers.

= 3.5.7 - 2018.10.10 =
* Fix - agent cannot be assigned to guest users.
* Fix - language profile for user was not being respected.
* Tweak - new document types: flights, insurance.

= 3.5.6 - 2018.09.18 =
* Feature - date of birth field can now be enabled to be displayed on checkout page.
* Fix - customer indicator was not showing for some users.

= 3.5.5 - 2018.09.04 =
* Fix - documents page was not loading correctly for front end users.
* Fix - customer indicator for repeat customers was incorrect for guests.
* Fix - agent email notification and note email.
* Feature - set favourite products for customers on their customer page.
* Feature - export CSV includes source, industry and status.

= 3.5.4.1 - 2018.08.13 =
* Fix - accounts were not showing assigned customers.

= 3.5.4 - 2018.08.08 =
* Fix - notices of error 500 appearing for some users when using documents module.
* Tweak - enable or disable the documents module.

= 3.5.3 - 2018.07.31 =
* Fix - notices were appearing in error console after new document feature was deployed.
* Feature - agents are notified of new orders of their assigned customers.
* Tweak - documents page was not showing pretty status formatting when no documents have been uploaded.

= 3.5.2 - 2018.07.27 =
* Tweak - document previews are universal and not dependant on file type.

= 3.5.1 - 2018.07.26 =
* Fix - notices were showing when updating plugin for some users.

= 3.5.0 - 2018.07.23 =
* Feature - customers can now upload documents for them to be verified and approved for customer management.
* Feature - keeping an eye on customers can now be easily done using the pin customer feature.
* Feature - editing fields of the customers from the My Account > Account Details page is now possible.

= 3.4.7.1 - 2018.07.10 =
* Fix - hardcoded table prefix was causing errors for some users.

= 3.4.7 - 2018.07.09 =
* Tweak - importer UI has been tweaked to match WooCommerce ecosystem.
* Feature - agents receive email when customer is assigned to them.
* Feature - Google Maps API key option to enable or disable integrated maps.
* Fix - agents were not loading with certain user role plugins activated.
* Fix - filters were not showing correct details.

= 3.4.6 - 2018.05.12 =
* Fix - error after saving a call was appearing.

= 3.4.5 - 2018.04.04 =
* Fix - export was not including email address for some users.

= 3.4.4 - 2018.03.29 =
* Fix - import was not importing the first and last names after 3.3.
* Fix - export was not including ACF fields for some users.
* Fix - notice on setup wizard.
* Tweak - relocated customer location map to right.

= 3.4.3 - 2018.03.20 =
* Feature - ACF fields are not exportable.
* Feature - ACF fields can now be set as filters.

= 3.4.2 - 2018.03.15 =
* Fix - tasks were not being assigned the right customer after saving.

= 3.4.1 - 2018.03.06 =
* Fix - orders were not being deleted when deleting customer.
* Fix - indicators column were not loading correctly on first install.
* Tweak - customers orders page and profile page.
* Tweak - remove shipping column from customers page.
* Tweak - agent column hidden by default.
* Tweak - repeated user icon clickable to show orders by this customer.
* Tweak - Google maps moved to header of customer profile page.
* Tweak - filters have been tidied for layout and appearance.

= 3.4.0 - 2018.02.24 =
* Feature - customer indicators on orders page to show key information.
* Feature - action button to send emails and create new order.
* Tweaks - redesigned customer page and customer status badges.

= 3.3.15 - 2018.02.15 =
* Fix - buttons were not set correctly.
* Tweak - calls page and tasks page more in line with WooCommerce 3.3.

= 3.3.14 - 2018.01.07 =
* Fix - new orders were not having customer assigned after latest WooCommerce update.
* Fix - order statuses were being centred on orders page.

= 3.3.13 - 2018.01.30 =
* Fix - purchased products table were not displaying for some customers.
* Tweak - customers page tweaked tables.
* Tweak - calls fields order.

= 3.3.12 - 2018.01.15 =
* Fix - SQL logs appearing have been hidden.

= 3.3.11 - 2018.01.10 =
* Fix - select2 filter was not working on certain filters.

= 3.3.10 - 2018.10.31 =
* Fix - Customer group don't show any results fix.

= 3.3.9 - 2018.12.08 =
* Fix - dynamic groups were not loading correctly for some users.

= 3.3.8 - 2018.10.26 =
* Tweak - updater matching of new server.
* Fix - products not appearing correctly on customers details page.
* Fix - SQL on big selects are now working correctly.

= 3.3.7 - 2018.10.28 =
* Fix bug with tasks and email not assigning customers properly.

= 3.3.6 - 2018.10.20 =
* Fix bug with product filters on customers screen.

= 3.3.5 - 2018.08.31 =
* Fix bug with tasks not preserving old customers after latest update.

= 3.3.4 - 2018.08.29 =
* Fix bug with tasks not being assigned the right customer.
* Fix bug with timer not stable when call is being made.
* Fix bug with actions not showing on orders page.
* Tweak to the account page, calls page and tasks page.

= 3.3.3 - 2018.08.21 =
* Fix bug with phone search not being searchable.
* Tweak to orders page filters look.

= 3.3.2 - 2018.05.22 =
* Fix bug with width box of filters to match WordPress style.

= 3.3.1 - 2018.04.25 =
* Fix bug with products filter on customer page.
* Fix bug with variations filter on customer page.

= 3.3.0 - 2018.04.20 =
* WooCommerce 3.0 compatibility.
* Fix bug appearing on orders page from WC3 update.
* Fix bug with select2 boxes on calls, tasks and customers.
* Fix bug layout style of customers page.

= 3.2.1 - 2018.03.31 =
* Fix bug with screen options not being preserved on Customers page.
* Fix bug with orders page being affected by customers page.
* Fix bug with View Customer button not showing.
* Tweak to notices appearing.

= 3.2.0 - 2018.03.27 =
* Tweak to add buttons for accounts, tasks and call.
* Tweak to layout of the customers profile page.
* Feature added to sort customers by agents assigned.
* Feature added to sort customers by date of birth.
* Feature added to sort customers by location.
* Feature added to define the address of the Google Map.
* Feature added to sort tasks by filter.
* Feature added to sort calls by filter.
* Feature added to automatically uppercase first letter of first and last name as well as copy into billing and shipping fields.
* Feature added to include shortcodes in emails.
* Fix bug of call icon not loading correctly on new call page.
* Fix bug of call timer not staying straight when timing.
* Fix bug of call actions on new call page.

= 3.1.6.2 - 2018.03.03 =
* Fix bug with Google API keys not working on SSL sites.

= 3.1.6.1 - 2018.02.24 =
* Tweak to language files.
* Feature added to set search parameters.

= 3.1.6 - 2018.01.15 =
* Feature added where customer notes can be added and sent to the agent, customer or both.
* Feature added where Agents can only see their assigned agents (user role added).
* Tweak to exporting of large numbers.

= 3.1.5.3 - 2016.12.24 =
* Tweak to filter to filter agents.

= 3.1.5.2 - 2016.12.19 =
* Tweak to Setup Wizard CSS.

= 3.1.5.1 - 2016.12.14 =
* Fix bug with setup not initialising.
* Fix bug with MailChimp.

= 3.1.5 - 2016.11.10 =
* Fix bug with customer filters not working with groups.

= 3.1.4.6 - 2016.10.19 =
* Fix bug with address not showing on Google Maps after API key is updated.
* Fix bug with telephone not appearing in export.

= 3.1.4.5 - 2016.10.14 =
* Fix bug with import not working correctly.
* Fix bug with Google Maps API not allowing to enter. You need to now enter your own API key under Settings page.

= 3.1.4.4 - 2016.09.02 =
* Fix bug with importing customers using CSV tool.
* Fix bug with bulk emails not being sent properly.
* Fix bug with the update customers function.
* Feature request to set what is displayed instead of username.

= 3.1.4.3 - 2016.07.28 =
* Added Russian language.
* Tweak to layout page.

= 3.1.4.2 - 2016.07.13 =
* Fix bug with support for WC2.5.x.

= 3.1.4.1 - 2016.06.16 =
* Fix bug with setup wizard not appearing after activation.
* Fix bug with pre WC2.6 support.

= 3.1.4 - 2016.06.15 =
* Fix bug with WC 2.6 upon activation.
* Tweak to customer status icons.

= 3.1.3.2 - 2016.05.12 =
* Fix bug with groups not working correctly.
* Fix bug with activation on latest PHP.

= 3.1.3.1 - 2016.04.21 =
* Fix bug with font icons with Order Status & Actions manager.

= 3.1.3 - 2016.04.20 =
* Fix bug with tasks metabox not appearing on customers page.

= 3.1.2 - 2016.04.18 =
* Tweak to call timer when using call, time is displayed in admin bar.

= 3.1.1 - 2016.04.16 =
* Feature added to allow you to delete customers. Notification will appear with instructions on what to do with existing order and linked WP User profile.

= 3.1.0 - 2016.04.11 =
* Feature added where you can add tasks to customers.
* Tweak to placing call page.
* Tweak to loading customers in large sizes.

= 3.0.5 - 2016.02.15 =
* Fix bug with customer link on Orders page not going to customer.
* Fix bug with activity page not being sortable.

= 3.0.4 - 2016.02.02 =
* Fix bug with customers not being added for some users.
* Fix bug with customers not updating properly.

= 3.0.3 - 2015.12.11 =
* Fix bug with setup wizard not loading customers.
* Fix bug with export not working correctly.

= 3.0.2 - 2015.11.06 =
* Fix bug with automatic emails being sent when they shouldn't upon customer creation.

= 3.0.1 - 2015.09.23 =
* Fix bug when sorting customers by value spent.
* Fix bug for the Advanced Custom Fields integration.
* Fix bug with the default status used for registration.
* Tweak to documentation link and referral link.

= 3.0 - 2015.09.15 =
* Refactored entire plugin.
* Feature ability to edit guest users.
* Feature setup wizard for quick and easy installation.
* Tweak to logging call page.
* Tweak to logging email page.
* Tweak to activity page.

= 2.6.13 - 2015.08.17 =
* Fix bug with payment method not being defined for customers.
* WooCommerce 2.4 compatibility.
* WooCommerce Subscriptions integration.
* Groups for WooCommerce integration.
* Fix bug with products purchased not correct.

= 2.6.12 - 2015.07.13 =
* Fix bug with order statuses not appearing properly within customer profile.
* Fix bug with Google Maps not appearing on HTTPS sites.
* Fix bug with billing/shipping address not appearing until clicking on the edit pencil.
* Fix bug with categories of user not working correctly.
* Fix bug with Guest values being the same.
* Feature to assign agents to customers (work in progress).
* Tweak to the import function, can now map fields from CRM to the imported CSV file.
* Tweak to the notes page and tidying of CSS.

= 2.6.11 - 2015.06.25 =
* Fix bug with activation and customer groups error.
* Fix bug with mailer not working properly.
* Feature included NL language.
* Tweak to documentation links now in plugin links.

= 2.6.10 - 2015.06.17 =
* Fix bug with customers not appearing.
* Fix bug with emails not being sent.
* Fix bug with syntax upon plugin deactivation.
* Feature added to define default customer statuses.
* Feature added with Products Purchased table showing more details on products purchased.
* Tweak to searching by email.

= 2.6.9 - 2015.06.09 =
* Fix bug with emptying trash for activities after deleting them.
* Fix bug with groups not being pulled correctly.
* Fix bug with customers not showing for some users.
* Fix bug with tables not being collated correctly.
* Feature with new table on customers profile page called 'Products Purchased'.
* Tweak to when not adding all items to the customer profile page.

= 2.6.8 - 2015.05.20 =
* Feature added to disable the automatic emails that are sent when adding a customer.

= 2.6.7 - 2015.05.18 =
* Fix bug with activity showing for all customers.
* Feature added where you can define the from name and email address when sending an email.
* Tweak to the customers page, email address field is required.
* Fix bug with no customers showing for some users.

= 2.6.6 - 2015.05.12 =
* Fix bug with customers not adding/showing after recent update.
* Feature of filtering customers based on their status.

= 2.6.5 - 2015.04.05 =
* Feature added to customer profile page; avatar, value and number of orders.
* Feature added to customer profile page; more fields related to leads.
* Fix bug with customer notes not saving.
* Fix bug with customer groups not appearing.
* Fix bug with new order not being pre-filled.
* Fix bug with MailChimp checks.
* Fix bug with admin menu editor conflict.
* Fix bug with debugged log of formatted address.
* Tweak to the address layout.
* Fix bug with unique identifier issue.
* Tweak to the customer table page.
* Fix bug with related to window on create call page.
* Fix bug with order statuses filter.
* Tweak to the email, remove the blog name from email.
* Tweak to the name format of the customer table.
* Tweak to the icons on the actions panel.

= 2.6.4 - 2015.04.24 =
* WordPress 4.2 support.
* Fix bug for XSS vulnerability.
* Fix bug when sending emails.
* Fix bug with registered customers not showing.
* Tweak to customer and column pages.  

= 2.6.3 - 2015.03.27 =
* Tweak to sales icon on the Customers page.
* Fix header bug when making a call.
* Added ability to add accounts for customers.
* Move customer status to settings.
* Remove date in groups, fixed groups.
* Strict error bug.

= 2.6.1 - 2015.03.12 =
* Fixed Advanced Custom Fields bug with date picker and Google Maps.
* Remove select2 filters from the customers page.

= 2.6 - 2015.03.09 =
* WooCommerce 2.3 compatibility.
* Fix bug with order status not showing to some users.
* Fix bug with guest customers not showing to some users.
* Restored sorting of number of orders.
* Fix issue with exporting customers.
* MailChimp bug fixed.
* Added ability to add customer to groups from Customer Details page.

= 2.5.8 - 2015.02.05 =
* Tweak texts around the plugin to give further clarification.
* Fix bug with email not appearing (for some users).
* Fix bug with MailChimp API.
* Fix add customer error (for some users).
* Fix last order date not showing upon installation and after.
* Fix jQuery issue with chosen plugin.
* Fix order status not showing on filters.
* Fix bug with new order and customer information being passed on.

= 2.5.7 - 2015.01.19 =
* Fix conflict with WooCommerce Memberships plugin.

= 2.5.6 - 2015.01.15 =
* Fix fatal bug regarding user email not defined.
* Fix bug with customer icon now showing.
* Localisation support for DE.

= 2.5.5 - 2015.01.13 =
* Tweak SQL functions to allow more records to load, store and process.
* Fix bug when editing email addresses.

= 2.5.4 - 2014.12.29 =
* Fix bug with Google maps not appearing.
* Fix bug with WooSidebars compatibility.

= 2.5.3 - 2014.12.23 =
* Fix bug with importing customers.
* Fix bug when ACF fields not appearing.
* Fix bug with order notes.

= 2.5.2 - 2014.11.21 =
* Feature added in creating custom customer status.
* Fix bug with exporting contacts.

= 2.5.1 - 2014.11.18 =
* Fix with ACF bugs, sorry about this.

= 2.5 - 2014.11.17 =
* Feature integration with Advanced Custom Fields now supported.
* Feature importing customers is now possible.
* Feature where you can set what the username will be when adding a customer.
* Fix bugs when adding a new customer.

= 2.4.6 - 2014.10.28 =
* Feature added, multisite support.

= 2.4.5 - 2014.10.27 =
* Fix bug with automatic customer table update.
* Fix bug when relating calls to orders/products.

= 2.4.4 - 2014.10.24 =
* Feature added localisation, sorry for the wait.
* Tweak to 'Related Products' when making a call.
* Fix database handling.

= 2.4.3 - 2014.10.11 =
* Feature added where you can now decide how customers are loaded in the customers table.
* Tweak to the groups feature.

= 2.4.2 - 2014.09.29 =
* Feature added where you can view a Google map of the customers.
* Tweak to the page title of when you view a customer.
* Fix bug with the Orders Status and Product Category filter.

= 2.4.1 - 2014.09.27 =
* Feature added to assign what orders can be shown as Number of Orders and Total Value in customers table.

= 2.4 - 2014.09.10 =
* Feature compatibility for WooCommerce 2.2.
* Tweak unused files have been removed.
* Tweak to the customer status.
* Tweak to the menu order.

= 2.3.4 - 2014.07.29 =
* Fix customers page showing added customers, customers with customer role and purchasing customers.
* Tweak integration with POS plugin to show customers added through that plugin.
* Tweak the follow up page after adding a customer.

= 2.3.3 - 2014.07.15 =
* Fix order of customers when sorting through them.
* Feature added to set what page loads when clicking on customer name on Orders page. This can be configured via Customers > Settings > Orders Page.
* Tweak to emailing page, when sending emails without subject, a warning message is displayed.
* Tweak to activity email page, now shows what email address was sent from and what email addresses were sent to.
* Fix customers appearing even though no orders and user profile.
* Fix number of orders appearing for uncompleted orders.

= 2.3.2 - 2014.07.14 =
* Fix Total Value sales are showing completed orders only.
* Feature added telephone number when placing a phone call.
* Fix bug when filtering customers and going to next and previous pages.
* Tweak to the orders page to view the customer directly.

= 2.3.1 - 2014.07.10 =
* Fix table displaying no customers.

= 2.3 - 2014.07.09 =
* Fix customers not showing bug.
* Feature added more fields to set Brands (official WooThemes extension).
* Feature added more fields to set Product Category.

= 2.2.1 - 2014.07.07 =
* Fix username formatting on customers page.
* Fix searching customers on customers page.
* Fix triple status icon issue.

= 2.2 - 2014.07.04 =
* Tweak with loading speed for 1000+ customers.
* Fix bug with date sorting.

= 2.1.2 - 2014.06.03 =
* Fix bug with email being displayed when exporting customers.
* Fix bug when emailing multiple customers.

= 2.1.1 - 2014.05.25 =
* Fix bug when customers status icon was not showing.
* Fix bug when view icon was not showing.
* Fix response time.
* Fix blank screen issue.

= 2.1 - 2014.05.20 =
* Feature added where you can add groups static or dynamic.
* Fix bug when new customer is added via shop, status is automatically "Customer".
* Tweak filters to include "Customer Status" now.
* Fix filter for searching for customers.
* Tweak in the settings, moved into separate page.

= 2.0.2 - 2014.05.13 =
* Fix a few bugs related to viewing customer and editing their details.
* Fix a few bugs related to customer status losing details.

= 2.0.1 - 2014.05.13 =
* Feature added where you can now add a customer and it appears in the customers table without order being made.
* Fix a few bugs when WP_DEBUG mode was turned on.
* Tweak when adding a customer, user created with default role as "Customer".

= 2.0.0 - 2014.04.03 =
* Feature added where you can now log a call.
* Feature added where you can now log email.
* Feature added where you can add customer.
* Feature added where you can view customer activity i.e. call or email.
* Feature added where you can choose what filters to have.
* Feature added where you can set a customer status i.e. lead, blocked etc.
* Feature added where you can add a customer note.
* Fix a few bugs with emailing, logging calls and activity.

= 1.4.1 - 2014.02.20 =
* Feature added where you can filter between users and guests.
* Feature added where you can filter for last 24 hours.
* Tweak for font icons in action buttons. 2.0.x support has gone.
* Fix bugs of adding notes 2.1 issue.
* Fix bugs of linking variations 2.1 issue.
* Feature email logs are now displayed.

= 1.4 - 2014.02.12 =
* Feature added where you can search customers by name, email address and phone number.
* Fix for when sending emails, no longer breaking paragraphs.
* Tweak to the email handling.

= 1.3.5 - 2014.02.07 =
* Tweak made to the menu, now a separate menu called Customers.
* Tweak made to use latest font icons to match WordPress 3.8.

= 1.3.4 - 2014.01.31 =
* Fix the screen options number of customers to show on page.
* Fix alignment for filters.

= 1.3.3 - 2013.11.30 =
* Feature implemented where you can now filter customer by States & Cities.
* Feature implemented where you can enter your custom email address when you send an email.

= 1.3.2 - 2013.11.15 =
* When products are deleted, customers are still shown with their orders in place. This corrects the �blank issue� issue on some WordPress installations.

= 1.3.1 - 2013.10.19 =
* MailChimp API class renamed to prevent collisions with certain themes or plugins. This corrects the "blank page" issue on some WordPress installations.

= 1.3 - 2013.10.14 =
* Filter by product feature added.
* Listing now contains all values of ONLY complete orders, not ALL orders.

= 1.2.2 - 2013.10.09 =
* Listing now contains Total amount of ONLY complete orders, not ALL orders.

= 1.2.1 - 2013.10.02 =
* Quotes between terms added to CSV export file.
* Minor code reformatting and adjustment, CSS fixes.

= 1.2 - 2013.09.29 =
* Added a new Action button allowing users to Call Customer using Skype etc.

= 1.1 - 2013.09.25 =
* Added possibility of exporting customers in CSV format.

= 1.0.1 - 2013.09.20 =
* Width for Actions column is now in PX instead of % to ensure all view frames can view actions.
* Telephone column added.

= 1.0 - 2013.09.09 =
* Initial release!

== FAQ ==
= Where can I get support or talk to other users? =
If you come across a bug or a problem, please contact us [here](http://actualityextensions.com/contact/).

For queries on customisation and modifications to the plugin, please fill this [form](http://actualityextensions.com/contact/).

You can view comments on this plugin on [Envato](http://codecanyon.net/item/woocommerce-customer-relationship-manager/5712695/comments).

= Where can I find the documentation? =
You can find the documentation of our Point of Sale plugin on our [documentations page](http://actualityextensions.com/documentation/woocommerce-customer-relationship-manager/).