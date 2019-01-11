=== Plugin Name ===
Contributors: mandsconsulting
Donate link: http://www.mandsconsulting.com/
Tags: email, download
Requires at least: 4.x
Tested up to: 4.9.2
Stable tag: trunk

Email Before Download presents your users with a form where they submit information, like their name and email address, prior to receiving a download.

Plugin homepage: http://www.mandsconsulting.com/products/wp-email-before-download

== Description ==

------------
5.x Release is Here!
We've completely rewritten EBD from the ground up. Since the plugin was built many years ago, we decided to implement the latest architecture and features that WordPress offers to plugin developers. In addition to testing as much as possible that this is backwards compatible with the original EBD version -- while also retaining a familiar experience for our existing admins/users -- we have added some popular requests such as the ability to change the from email address as well as support for Download Monitor's latest major release (4.0+).
-------------

Email Before Download presents your users with a form where they submit information, like their name and email address, prior to receiving a download. This plugin integrates with the popular [Contact Form 7](http://bit.ly/dNzVJd) and [WordPress Download Monitor](http://bit.ly/ifff4y) plugins, allowing you to create any form you like and manage/monitor your file downloads.  You can also EXPORT a list of users that have downloaded files from the plug-in's settings page.  Prior to installing Email Before Download, please confirm each of the dependent plugins is already installed and working independently.
We recently updated the deprecated WPDB escape functionality to new one. You can download the version compatible with older version of WordPress at [Email Before Download](http://bit.ly/1uThydb)

As an option, you can configure Email Before Download to:

1. Display a link to your file directly under the contact form once it is submitted.  This happens dynamically, inline of your post/page.
1. Send the user an email with a link and/or attachment to download your file.
1. Both #1 and #2

[Plugin Homepage with Live Demos and Test Download](http://www.mandsconsulting.com/products/wp-email-before-download) | [Support Forums](http://bit.ly/lU7Tdt)


Usage

Note: You can see screenshots at [http://wordpress.org/extend/plugins/email-before-download/screenshots/](http://bit.ly/g4r1w2)

1. Create a contact form used by Email Before Download using Contact Form 7 and note the Contact Form ID
1. Upload a file using Download Monitor and note the Download ID
1. Navigate to the Post (or Page) you wish to include
1. Add the following short code using the IDs collected in the first two steps
   [email-download download_id="X" contact_form_id="Y"]



Plugin Homepage with Live Demos and Test Download: [http://www.mandsconsulting.com/products/wp-email-before-download](http://www.mandsconsulting.com/products/wp-email-before-download)

Please use the [Support Forums](http://bit.ly/lU7Tdt) for any questions and issues. Sometimes other users can help you as well.


== Installation ==

1. Download from [http://wordpress.org/extend/plugins/email-before-download/](http://bit.ly/dF9AxV)
1. Upload the entire email-before-download folder to the /wp-content/plugins/ directory.
1. Activate the plugin through the "Plugins" menu in WordPress.
1. Locate the "Email Before Download" menu item in your WordPress Admin panel under "Settings" to configure.


== Frequently Asked Questions ==

= Can I see a list of download requests people have made? =

Yes.  We store a log of the form submissions and generated links. You can view and export a CSV file of them from the Email Before Download settings page in your admin screens.

= What if I don't use the Contact Form 7 and/or Download Monitor Plugins? =

You will not be able to use this version of Email Before Download without these dependent plugins.  If you have specific reasons to avoid using the dependent plugins, please contact us and let us know the reason so we can take it into consideration.

= Anything special I need to do with my contact form? =

If you decide to configure the Email Before Download option to send the user an email with a link to the download, then you will want to name the email field "your-email" as shown in the example screenshots.  Outside of that, nothing special.

= What happens after the user completes the form? =

By default, the user is presented with a link or links to download their file(s).  There is also an option to email the user (with a link to the file and/or attachment) if you choose that route.  You can even provide both the inline link as well as the email if you choose.

= Are you changing any of my file or directory permissions? =

WordPress allows direct access to files in your upload directories using a direct URL and we do not change those permissions.  We do provide an option to mask the URL to your downloads if you have cURL enabled.

= What are the available shortcode options? =

This is the list of all short code attributes that can be used.   Some of them override the global admin settings.

* download_id - either one single download id from Wordpress Download Monitor, or a comma separated list of such ids, eg. '1,2,3'
* contact_form_id - Contact Form 7 ID. Overrides default contact for id from settings if used.
* title - this attribute overrides the  title from Download Monitor. if multiple, put them in a comma separated list in the same order as your download_ids.
* file  - use to point to external url
* delivered_as - possible values: "Send Email", "Both", "Inline Link"
* attachment  - "yes", "no" Attachments only work if files were uploaded with Download Monitor.
* force_download - any value that is passed considered as "yes" (we don't have a global menu item for that) only works with a single download_id.
* checked -  Loads form with item checked. If you are using checkboxes and have multiple items, the will all be checked,  Accepts "no", any other value is "yes"
* hide_form - "yes", "no"
* radio - "yes", "no"
* from_email - valid email address
* from_name - any alphanumeric string


== Screenshots ==

1. Note the ID of a file you have uploaded to Download Monitor.
2. Note the ID of a contact form you have created using Contact Form 7.
3. Use the following shortcode in your page or post: [email-download download_id="X" contact_form_id="Y"].
4. Upon installation and use of the plugin on a post/page, an end-user will see your contact form.
5. User will be required to enter valid data in accordance with Contact Form 7 validation rules.
6. Upon submission, user will either see a direct link below the form.  (Note: there is also an option to only email the link to the user.)
7. Example Contact Form 7 form code, including tag required to display multiple download selection checkboxes.


== Changelog ==
=5.1.9=
* Changed the way DOMDocument removes wrappers to be compatible with older versions of PHP
=5.1.8=
* Fixed issue with some browsers not showing UTC-8 characters properly
* cleaned up some code to remove PHP warnings
=5.1.7=
* Fixed issue with some malformed HTML being created by DOMDocument()
=5.1.6=
* fixed issue where some people weren't getting ajaxed links
=5.1.5=
* fixed issue with false positives on invalid uids
=5.1.4=
* minor bugfixes
=5.1.3=
* fixed issue with link displaying when email only selected.
=5.1.2=
* Simplified use of PHPDomDocument to better function with older versions of PHP
* Let Javascript do more of the work for hiding/showing forms
* Removed custom API endpoint that was giving issue for some users
* Using Download Monitor hook to serve files

=5.1.1=
* Fixed issue where some instances wouldn't activate after update
* Fixed issue where some servers wouldn't set session variables
* Added css class to download selectors for easier formatting
* Fixed issue where some settings would be reset to default on update
* Fixed issue with older versions of PHP not liking HTML5 tags

=5.1.0=
* Added compatibility with version 5.0 of Contact Form 7
* Fixed issue where blacklist wasn't parsing correctly for some people
* Fixed issue with response box showing for some people

=5.0.9=
* Fixed issue with character encoding when building form

=5.0.8=
* Fixed issue with older versions of PHP giving parse errors.
* Added banner and icons for plugin.

=5.0.7=
* Fixed issue with [your-message] not being parsed properly
* Fixed downloaded files not showing in admin email
* Fixed issue where some people couldn't activate the plugin
* Fixed issue where form reloaded the page on submit for some people

=5.0.6=
* Fixed issue where pages with multiple forms weren't displaying inline links properly
* Fixed issee with link_format not working

=5.0.5=
* Fixed issue with some servers not attaching files to emails
* Fixed issue with malformed email headers if from_name was left blank
* Fixed issue with some multiple download forms
* Fixed issue with smart quotes being used in shortcode by third party content editors
* Removed some default styling that was conflicting with some custom styles
* Fixed issue with non standard upload folder names

=5.0.4=
* Fixed emails not being sent for some people
* Fixed display issues with colons in download titles
* Modified settings so EBD stays compatible with Custom Sender plugin.
* Added shortcode attributes for from_email and from_name.

=5.0.3=
* Fixed issue with form not displaying for some people

=5.0.2=
* Fixed issue with admin tables not showing.

=5.0.1=
* Bug fixes

=5.0=
* Complete tear down and rewrite of entire EBD plugin.
* Better integration with Wordpress.
* Added admin view, export, and purge of download links and posted data.
* Added better integration with Contact Form 7 and Download Monitor.
* Removed masked option from settings as this is pretty much done by default now.
* Added option for default contact form id.
* Added some CSS to the generated inline links to make them look a little better by default. Can be overwritten easily if needed.
* generates shorter URLs.
* More settings available in shortcode.
* Can override multiple titles now, as long as they are in the same order as the download ids.

=4.1=
* Made a few more modifications at the request of email from "plugins@wordpress.org".

=4.0=
* Made modifications to address items requested by "plugins@wordpress.org" such as ensuring further sanitization of email addresses and database inserts. Also, removed ability to access files directly from within the plugin folder. Finally, while making these changes, changed the URL for downloads to use "ebd_dl" instead of "dl" just in case another plugin were to begin using the "dl" parameter in order to prevent possible conflicts. Due to these changes, download links from prior to EBD v4.0 will no longer work with EBD v4.0+.
=3.6=
* Made modifications to shorten the download URL and potentially improve email deliverability by removing "download.php" from the download link

=3.5=
* Made minor changes to support PHP 7.

=3.4.2=
* Fixed issue regarding the download button on some themes where download button would display in wrong format.

=3.4.1=
* Fixed issue related to SQL functions used in code.

=3.4=
* Updated deprecated WPDB escape functionality to new one.

=3.3=
* Updated to be compatible with Contact Form Version 3.9

= 3.2.9 =
* Fixed issues related to a recent release of Contact Form 7 version 3.9

= 3.2.8 =
* Fixed the issue with multiple download ids checkboxes when user selects hidden contact form option.
The checkbox can now be to the left or right, depending on the custom tag (<ebd /> or <ebd left/>)"

= 3.2.7 =
* Fixed the issue with loading contact form as XML to DOM parser, when html entities were breaking the validity of XML.

= 3.2.6 =
* Added new option that allows user to hide contact form until user selects at least one downloads (for multiple download ids )
* Added option that turns checkboxes to radio buttons (for multiple download ids)

= 3.2.5 =
* Fixed bug with single quotes that led to javascript error.

= 3.2.4 =
* Added support to new version of the Download monitor
* Modified download logic: If the masked option is turned on and Internet Explorer is detected, download will be forced.

= 3.2.3 =
* Two new fields added to the plugin: user_name and email
* CSV export now includes new fields
* Added checks for the existence of the new fields, adds them if needed
* In admin settings, added new option that changes Multiple Checkboxes' default state
* Added new short code attribute that overrides admin settings for Multiple Checkboxes' default state

= 3.2.2 =
* Removed extra spacing in multiple download output

= 3.2.1 =
* Create table SQL script updated (now the title column has utf8 character set and utf8_unicode_ci collation)
* Added a patch that checks this specific column and alters it if needed

= 3.2 =
* Fixed bug related to logging multiple downloads correctly
* Added field to CSV export
* Added PayPal button in Admin Panel
* Added character encoding in case it helps to support languages other than English


= 3.1.7 =
* Default multiple file downloads to pre-selected (checked) by default

= 3.1.6 =
* Minor fix for various multi-file issues and logging

= 3.1.5 =
* fixed event handling
* stubbed email_from, though it is not active

= 3.1 =
* New modification to help support for Contact Form 7 v3.0+

= 3.0 =
* Modification to help support for Contact Form 7 v3.0+
* Added ability to force a file download using attribute in shortcode [email-download download_id="X" contact_form_id="Y" force-download="true"]; Download Monitor Force Download Option is recommended for files stored in Download Monitor
* Added option in admin panel to clear Email Before Download log entries
* Minor fomatting updates to admin panel
* Updates to allow Download Monitor to track clicks/downloads of files accessed using various scenarios of the Email Before Download plugin; Download Monitor still does not track clicks when using the masked URL option of Email Before Download, but the Email Before Download log does track these

= 2.6 =
* Bigger export link
* Support for special characters in filenames like "&"
* Fix for empty page interaction
* Change of function name to avoid conflict with other plugins
* Support for left checkboxes on multiple file download form using "&lt;ebd_left /&gt;"

= 2.5.1 =
* Minor cleanup of admin panel

= 2.5 =
* Added ability to prevent specific domain names
* Fixed download filename issue for .zip files


= 2.0 =
* Support multiple download selection (within shortcut code, use comma-separated list of download IDs: download_id="1,2,3" -- within the contact form 7 form used for multiple download selection, ensure you place the tag "&lt;ebd /&gt;" where you want to checkbox list to be generated) as shown in [screenshot 7](http://wordpress.org/extend/plugins/email-before-download/screenshots/)
* Add more information in the download history EXPORT .csv file
* Added support for Download Monitor format code for the inline link that is displayed (within shortcut code, specify the format code: format="1")
* Allow overriding the default settings with the shortcode (i.e. within shortcode, use delivered_as="Inline Link" even though the general setting in admin panel is setup for "Both" -- options are "Inline Link", "Send Email", "Both")
* Updates to avoid potential conflicts with other plugins
* Added ability to customize subject line when emailing file download

= 1.0 =
* Added ability to export log in CSV format from admin settings page.
* Added ability to mask download file's URL if cURL is enabled.
* Added ability to expire the download link after a given timeframe.
* In addition to emailing a link to the file, added ability to email the file as an attachment.
* Added ability to download files outside of Download Monitor (within shortcode, use file="http://mydomain.com/file.pdf" -- no need to include download_id="X" in this case).

= 0.5 =
* First release.

== Upgrade Notice ==

= 1.0 =
Automatically upgrade the plugin and all previous settings should remain intact.

= 0.5 =
First release.
