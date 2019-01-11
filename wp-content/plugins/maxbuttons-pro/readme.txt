=======================================
MaxButtons Pro Plugin for WordPress
Copyright (c) 2018 Max Foundry, LLC
http://maxfoundry.com
=======================================

========== LICENSING ==========
All files and code for this plugin are licensed with the GNU General Public License
version 2 (GPLv2), which can be found at http://www.gnu.org/licenses/gpl-2.0.html.

========== REQUIREMENTS ==========
This plugin has been tested with the self-hosted version of WordPress 3.4 and later, which
can be downloaded from http://wordpress.org. This plugin cannot be used on WordPress.com.
For more details, see http://en.support.wordpress.com/plugins/.

This plugin requires at least PHP Version 5.3 and a recent WordPress version


========== INSTALLATION ==========
These instructions assume you already have WordPress installed or are at least familiar with
doing so. If not, see the "Installing WordPress" codex article on the official wordpress.org
site for detailed installation instructions (http://codex.wordpress.org/Installing_WordPress).

For automatic installation:

- Login to your website and go to the Plugins section of your admin panel.
- Click the Add New button.
- Under Install Plugins, click the Upload link.
- Select the plugin zip file from your computer then click the Install Now button.
- You should see a message stating that the plugin was installed successfully.
- Click the Activate Plugin link.

For manual installation:

- You should have access to the server where WordPress is installed. If you don't, see your system administrator.
- Copy the plugin zip file up to your server and unzip it somewhere on the file system.
- Copy the "maxbuttons-pro" folder into the /wp-content/plugins directory of your WordPress installation.
- Login to your website and go to the Plugins section of your admin panel.
- Look for "MaxButtons Pro" and click Activate.

========== UPGRADING ==========
For automatic upgrading:

- When new updates are available, you should see an update notice in your WordPress admin.
- Go to Dashboard > Updates to see the list of all updates available.
- Select "MaxButtons Pro" from the plugins list then click the "Update Plugins" button.
- After a moment, you should see a message stating that the plugin has been updated.

For manual upgrading:

- You must have access to the server where WordPress is installed, either directly or through FTP.
- It's always a good idea to backup your website files and database, so do that first.
- Login to your website and go to the Plugins section of your admin panel.
- Look for "MaxButtons Pro" and click Deactivate.
- Copy the updated plugin zip file up to your server and unzip it somewhere on the file system.
- Copy the "maxbuttons-pro" folder into the /wp-content/plugins directory of your WordPress installation. You want it to overwrite the plugin folder that is already there.
- Go back to the Plugins section of your admin panel.
- Click the Activate link for the "MaxButtons Pro" plugin.

========== SUPPORT ==========
Please direct all support issues and questions to http://maxbuttons.com/forums.

========== VERSION HISTORY ==========

= 7.8. =

* Fix - Crashes on PHP 7.3 due to Simple HTML DOM library
* Tweak - If no Box Shadow is defined, it's not set on the MaxButton.
* Fix - Box shadow with only spread now works and properly removes values from stylesheet
* Fix - Box shadow blur minimum is now zero
* Fix - Gradient stop field hidden when gradient option is off


= 7.7. =

* [PRO] New - Custom CSS option for buttons
* [PRO] New - Advanced option for ID on the button
* [PRO] Tweak - Removed unused empty position option
* [PRO] Fix - When textbound, incorrect positioning of icon on hover
* [PRO] Fix - Bound to text didn't align right, top properly

* Tweak - Warning when button name is duplicate
* Tweak - Removed unused action hooks in field
* Tweak - Small speed improvement in Javascript

= 7.6.1 =

* Fix - Icon color applied wrongly

= 7.6 =

** Please check your buttons if you use background Icons / Images **

* [PRO] New -  Background position for icons / images with background
* [PRO] New -  Bound icons to text to help positioning
* [PRO] Fix -  Hover position on icons wrong with top / bottom positions

* Fix - Saving buttons in Gutenberg block
* Fix - Extra classes could conflict with styling in certain situations
* Fix - Removed requirements for any user roles in adding buttons to content
* Fix - Icon in Gutenberg classic block
* Fix - Activation check could trigger notices when missing database table fields
* Fix - Bug when using Siteorigins in Widgets area
* Removed - Shortcake Font Awesome reference


= 7.5.3 =

* Fix - Wrong version number causing update notices
* Fix - Fatal error when update API fails

= 7.5.2 =

* Fix - Further fixes for TinyMCE while being loaded out of WP-editor context.
* Fix - Hide 'add button' setting now also doesn't show Tinymce button

= 7.5.1 =

* Fix - Error when loading Tinymce button resulted in crash for some users

= 7.5 =

* [PRO] Google Analytics are now settable via shortcode: google_label, google_action, google_category
* [PRO] Fixed issue with plugin information for updates
* [PRO] Setting to load CSS via CSS file instead of inline style.
* New - Support for Gutenberg
* New - Button icon in TinyMCE interface
* Fixed - False positive compatibility notice for PHP 7.x
* Tweak - Prevents certain events from loading twice (performance)

= 7.4 =

* [PRO] - Fixed issue with icon changes and page refreshing
* [PRO] - Icon for plugin update screen
* Fixed issue with Font Awesome in certain button packs

= 7.3.2 =

* [PRO] Fixed - Issue with Google Fonts not active, but still being loaded
* [PRO] Fixed - Plugin double checking versions
* [PRO] Fixed - Crash on Error in MaxUpdater
* [PRO] Fixed - Notices when adding buttons
* [PRO] Performance upgrade loading Google Fonts

= 7.3.1 =

* [PRO] Fixed - Webfont handler was running too often
* Fix - Issue with Link Picker styling / picker not always working properly

= 7.3 =

* [PRO] Updated Font Manager layout to look better
* [PRO] Font Awesome library updated to 5.10, including 430 new icons
* [PRO] Updated Fonts and Icons editor to improve speed
* [PRO] Changed ID of EDD checkout submit integration due to changes in EDD, this might change the button style.

* Link picker - Easier way of selecting links to your content
* Updated parser SCSSPHP to version 0.7.5
* Fixed - Issue with modals, causing issues on certain themes / plugins
* Cleanup unused scripting
* Fixed issue with unicode button name crashing style output
* Fixed issue with custom shortcode handlers
* Removed references to any Font Awesome scripts

= 7.2.3 =

* [PRO] Fixed: Gtag.js now works with MaxButtons GA settings

= 7.2.2 =

* [PRO] Fixed Text2 shortcode bug that prevented it from working

= 7.2.1 =

* [PRO] Easy Digital Download 2.9.3. has it's own AJAX submit now, removed integration javascript

= 7.2 =

* [PRO] Palette colors are now customizable
* [PRO] Text option for underline
* [PRO] Changes to 'expired license' handling
* [PRO] Second text line now in shortcode dialog
* [PRO] Fixed PHP notice in button import

* 'Add button'-Dialog improvements, plus all shortcode attributes now available in interface
* New shortcode attribute 'extraclass' to add extra classes
* Fixed issue where media script and modal script would interfere with each other
* Added No optimize string for Autoptimize users
* Updates to support page
* Improvements to shortcode visibility
* Fixed Javascript bug that could cause issues with other not-isolated plugins
* Tested on WordPress 4.9.6.

= 7.1.3 =

* Fixed legacy issue with social share that could crash plugin in certain cases
* If license has no expire date, it will automatically check against remote license, fixing bug showing incorrect expiration

= 7.1.2 =

* Fixed issue when other sources call jQuery.noConfict()
* Fetches remote license if expired field is empty.

= 7.1.1 =

* Removing Debug data in Visual Composer integration

= 7.1 =

* Vertical align now working
* Fixed issue with selecting buttons via SiteOrigin Page Builder

* [PRO] Fixed MaxButtons Popup issue with Visual Composer
* [PRO] More robust License handling
* [PRO] Fixed crash when Visual Composer shortcodes were in use without active VC plugin

= 7.0.2 =

* Moved FA-SVG generation to server side
* Removed FA5 JS and Shims
* Issues with icon hover color fixed
* Fixed bug in Contact Form 7 integration
* Fixed notice when Icon alt was missing
* Vertical align now working

= 7.0.1 =

* Fixed bug in import packs

= 7.0 =

* PRO - Fixed issue with image icons not hiding in editor
* PRO  - Fixed - Font Families showing up correct again in add button dialog

* Removed old Social Share from base plugin. Use WordPress Share Buttons ( http://wordpress.org/plugins/share-button/ )
* Upgrade to Font Awesome 5 (Please check your FA icons after updating)
* Added vertical-align: middle as standard property
* Streamlined plugin Ajax processes
* Fixed - Issue with button updated warning when nothing was updated.
* Fixed - Issue with shortcake integration

= 6.28 =

* Tested for 4.9.4
* Improved performance of editor javascript
* Fixed issue with showing certain conditional fields
* Fixed issue in maxmodal with double init

= 6.27 =

* Tested for 4.9.2
* [PRO] Fixed issue with EDD Free Downloads Plugin and EDD integration
* [PRO] Fixed bug in using EDD integration with Containers
* [PRO] Fixed bug in icon set to Background
* [PRO] Fixed bug with images set to Background
* Security - Added rel='noopener' for links opening in new window
* Fixed missing values on a template

= 6.26.1 =

* [PRO] Fixed - Data options under Advanced
* Fixed - Advanced options

= 6.26 =

* [PRO] Different Image and Icon for Hover now possible.
* [PRO] Fixed crash in license activation screen, fixed incorrect message
* [PRO] Fixed hanging plugin update message
* [PRO] Fixed issues with Qtranslate integration, fixed issue with translatable fields
* Technical maintenance

= 6.25.1 =

* Fixing issue with plugin and media library

= 6.25 =

* [PRO] Fixed issue with Google Fonts wrongly trying to load
* [PRO] Image selection now includes standard WP sizes
* New setting for extra URL Schemes
* Small layout color picker fix
* Renamed button title to tooltip for clarity
* Rename empty font setting to 'site default' for clarity

= 6.24 =

* Social Share deprecated. For Social Sharing please check the new MaxButton Social Share addon
* New allowed link URL's schemes - ms-windows-store and steam.
* Fixed Copy Color interface
* Improvements how Modals handle scrolling
* Change / Issue in MaxCSSParser which didn't allow to properly parse Pseudo CSS element which are a parent of the main anchor class.
* Improvements in editor

= 6.23.1 =

* Changes to Plugin updates to make it more robust.

= 6.23 =

* PRO Fixed bug in button search in combination with paging
* Fixed font-size in color picker styling
* Adapted to changes on color picker layout ( WP 4.9 Alpha )


= 6.22 =

* PRO Fixed issue with abrupt change of WP prepare function in 4.8.2
* Fixed bug in External CSS function and is now working again
* Enhanced visibility of 'show shortcode examples' option

= 6.21 =

* PRO - Fixed issue with hooks loading multiple times.
* PRO - Multiple data fields are now possible

* Feature - Add simple link title to buttons plus shortcode attribute linktitle
* Updated Font Awesome override to work only where needed
* Removed PHP 5.2 warning - not functional


= 6.20.2 =

* PRO - Fixes for Beaver Builder integration
* PRO - Small fix for Qtranslate

= 6.20 =

* PRO - Button name is properly imported now

* Big technical update and clean up of code
* Moved plugin to proper use of namespaces
* File:// now allowed for URL's
* Moved color class to mbcolor due to frequent issues with offending plugins.
* Tested for WP 4.8


= 6.19 =

* Fixed minor security issue regarding cross-site scripting (JVN#70411623). Thanks to JPCert for responsible disclosure.

= 6.18 =

* PRO - Icon Search - quickly find the needed Font-Awesome icon
* Fixed bug where scrollbar didn't show in Add Button dialog
* Extra check for multi-byte string support

= 6.17 =

* PRO - Qtranslate integration support
* Button load data hook added
* Fixed - Add button dialog showing scrollbars without need in certain cases
* Fixed - Minor notice in Social share when buttons are not in database

= 6.16 =

* New option for better accuracy of preview if site theme runs 'border-box'
* Fixed Install class to check and create Social share transient table
* Added check for SimpleXML module

= 6.15 =

* PRO Improved Contact Form 7 interface
* PRO Fixed deprecated call to CF7 Api
* New setting to solve FontAwesome conflict
* Updated settings page

= 6.14 =

* PRO - Changes to export button layout
* PRO - Multiple button selection when importing via packs
* PRO - Renewed Button Pack layout
* PRO - Fixed error in delete pack dialog on import screen
* Fixed small layout issue in responsive section
* Updated copy warning to be even more clear
* Added shortcode options to FAQ

= 6.13.1 =

* Fixed error message on action buttons.

= 6.13 =

* Enter on text fields now moves to the next field
* Fixed bug that could trigger action buttons in editor when using enter button

= 6.12 =

* Updated Copy / Trash / Delete interfaces
* Fixed issue with refresh page warning when removing buttons
* Fixed Social Share Facebook count
* Fixed lower save button not working in social share
* Fixed layout issue in Social Share

= 6.11.1 =

* Fixed crash on older version of Contact Form 7

= 6.11 =

* [PRO] Fixed font loading issue with Chrome and Google Fonts with spaces
* Shortcode options in add button dialog
* Fixed crash when running PHP 7.1.0

= 6.10 =

* [PRO] Fixed license connection error didn't provide correct response
* [PRO] Removing icon didn't remove it from preview
* [PRO] Fixed deprecation notice due to new Contact Form 7 version
* Fixed color issue with preview color running one click behind
* Fixed issue with hover gradients, when gradients are off
* Fixed text shadow issue in CSS output

= 6.9 =

* [PRO] Fixed button preview error if (font-awesome) icon is not set.
* [PRO] Several small bug fixes
* [PRO] Enter on an input no longer submits form
* Tested for WP 4.7
* Improved copy colors

= 6.8 =

* PRO - Updated Font Awesome to 4.7
* Fixed several smaller bugs in responsive
* Fixed smaller layout issues ( consistency )
* Upgraded SCSSPHP parser to 0.40
* Fixed 'undefined variable' in responsive

= 6.7 =

* Button height in responsive settings
* Fixed responsive bug regarding hiding option
* Responsive settings updated
* Add Button Dialog updated (more clear)
* Colorpicker slightly bigger


= 6.6 =

* PRO Fixed bug causing pack not to display in Social Sharing
* Improved tab index on button editor.
* Fixed Copy Color button position in Chrome
* Fixed minor CSS issues


= 6.5 =

* PRO Icon and Images section interface improved
* PRO Fixed bug in Google Interaction setting
* Gradient background color option can be switched on and off
* New material switches to replace the checkboxes


= 6.4 =

* PRO Fixed crash on packs page
* PRO Added confirmation before deleting a pack
* Copy colors feature added
* Fixed several small issues with the color picker

= 6.2 =

* Improved visibility of the color picker
* Fixed possible rendering issue with Box Shadow
* Fixed: in rare case the button could overlay the action buttons in the overview

= 6.1.2 =

* PRO - Fixed a problem with color picker

= 6.1 =

* PRO - Added data attribute to advanced section
* PRO - Fixed Icon padding right values not being saved
* PRO - Fixed small display bug on Easy Digital Downloads integration
* Several updates to the Color Picker
* Box Shadow spread option added
* Fixed conflict with !important and box shadow setting

= 6.0 =

* PRO Switched to a new license system - see maxbuttons.com for renewals
* Version 6 milestone
* Fixed small styling issue in header
* Updated link in plugin

= 5.13 =

* PRO Fixed Bug in Font Manager text search
* PRO Fixed issue with export buttons containing images
* PRO Improved navigation on packs page
* Fixed several notices when creating new buttons
* Buttons will now always have 'pointer' cursor

= 5.12 =

* PRO Fixed 404 issue with Font Manager
* PRO Easier selection using the Font Manager
* Replaced color picker with WordPress default color picker
* Border radius can be locked to change all sides at onces.
* Cleanup of JS functions.


= 5.11 =

* PRO - Some cleanup of packs and export interface
* Improvements to layout for small and mobile views
* Optimizations to modal on resizing
* Social icons for Eyeem added

= 5.10.2 =

* Another fix for PHP 5.3

= 5.10.1 =

* Fix for function call not compatible with PHP 5.3

= 5.10 =

* PRO Fixed issue with removing selected images.
* PRO Better error handling on directory creation issues
* PRO Error check for ZipArchive handler
* PRO Fixed several minor styling issues on Pack export / import
* Fixed issue with background when using gradients
* Collection name is shown in button overview when button is in collection
* Database checks and failovers improved
* Several small updates to styling and layout
* Fixed display issue showing pack name / description when empty

= 5.9.2 =

* Fixed bug on icons window not scrolling correctly.

= 5.9.1 =

* Fixed background issue for icon button with text
* Fixed background issue for second text element
* Fixed background position for background icons

= 5.9 =

* PRO Beaver Builder integration
* PRO Fixed Visual Composer issue not displaying shortcode correctly on frontend editor
* Greatly improved modal layouts
* Fixed - Social Share : non-existing buttons will not show.
* Child-spans now inherit background settings from parent anchor

= 5.8 =

* Fixed - Social Share remove button visible again
* Fixed - Social Share button without attached network no longer open as popup by default.
* Fixed - Custom Media Queries disabled after saving
* Description field can now be hidden via settings
* Various layout optimizations
* Tested for version 4.6

= 5.7 =

* Fixed omission in button clear function causing not to clear fully
* Fixed typo in Social Share shortcode
* Fixed social share bug, not correctly removing counts in certain situations
* Fixed social share icons not having cursor pointer
* Fixed Text Shadow bug with buttons having only shadow top set.
* Inline loading of social share collections

= 5.6.2 =

* Fixed Font Library loading URL instead of path

= 5.6.1 =

* Fixing Font related bug

= 5.6 =

* PRO Enhanced - Fonts now load within styles instead of front-side scripting (performance)
* PRO Fixed - Fonts without regular font set didn't load properly
* PRO Updated Google Fonts list
* PRO Security: Empty index file in directory to prevent unintentional listing
* Updated Social Share welcome message
* Tested for WP 4.5.3

= 5.5 =

* PRO Images now removable
* PRO Image title and alt now show in editor
* PRO Image title and Alt only show when they have a value
* PRO Fixed warning in Updater on request time out
* Fixed CSS in review notice
* Fixed title issue with support topic titles
* Fixed small outlining issues in button editor

= 5.4 =

* PRO Revamped image selection
* PRO Alt + Title from images now included
* Colorpicker code enhanced
* Updated container options interface

= 5.3 =

* PRO Integration with Contact Form 7
* Increased performance for shortcodes when having an URL in shortcode.
* Fixed bug Border Shadow not showing when blur is zero.
* Fixed bug Text Shadow not showing when blur is zero.
* Fixed issue Text not displaying in preview when button was saved without text
* Minified all Javascript in plugin for faster performance.

= 5.2 =

* Fixed version check to enhance database upgrades
* Fixed Add buttons dialog in Beaver Builder
* Extra check for social sharing not to load on WP login pages
* PRO Fixed small issue with font loading in editor

= 5.1 =

* Better pagination and display on Add Button dialog
* Fixed certain Social Share default values .
* Made number fields slightly larger
* Tested up to 4.5.2

= 5.0.1 =

* PRO : Extra check to prevent old Visual Composer versions from crashing.
* PRO : Google Fonts now load in Add Button Dialog
* PRO : Fixed broken JS dependencies

= 5.0 =

* New button editor interface
* Removed maximum length of text fields
* Performance: Javascript loading streamlined
* Fixed: Removal of social sharing collections
* Improved social share picker
* Fixed Window resize bug in popups
* Fixed Array Bug in Social block
* PRO: Font Awesome upgrade to 4.6.1.


= 4.22.1 =

* Removed Splinfo getExtension call since it's not compatible with all PHP 5.3 installations

= 4.22 =

* Major upgrade of user interface
* Major cleanup of styling code
* Description now handles multiple lines correctly

= 4.21 =

* Cleaner user interface at font options
* Check if jquery function exists on front.

= 4.20 =

* Tested for WordPress 4.5
* Moved old error modals in social sharing to new modal
* New copy dialog
* PRO Fixed error notice on add plugin screen

= 4.19 =

* PRO - Fixed plugin conflict with Toolset Types on icons
* PRO - Extra check on passing version to updater
* PRO - CSS fixes in Font Manager
* PRO - Fixed packs previews

* Fixed: Relative URL's without starting slash no longer are prepended with default scheme
* Tel: and sms: URL's not accepted in URL field

= 4.18 =

* PRO - All google fonts are now easily available in the font library
* PRO - Icons can be set as background
* PRO - Box for custom CSS ( under 'settings' )

* Fixed problem responsive settings not correctly enabling / disabling options
* Button editor now warns when moving away without saving

= 4.17.1 =

* Fix for button save crash

= 4.17 =

* Moving from leanModal to better popup code
* Fixed Modal background and CSS error on delete button / view CSS
* Put Font style options (italics) back
* PRO Fixed - Google fonts not being displayed in add media screen
* PRO Support for Visual Composer

= 4.16 =

* Version number added to JS scripts to prevent browser caching on updates
* Tweaks to the main CSS class statement to avoid clutter
* Fix for Cloudflare's email obfuscation in combination with responsive buttons.
* PRO Moved API URL's to HTTPS

= 4.15 =

* Option to add custom class names
* Added changes to increase plugin page speed performance

= 4.14 =

* Buttons in Shortcake didn't show Font Awesome buttons correctly
* Fixed bug in Shortcake rendering with ID's over 10
* Fixed error when adding button via Shortcake dialog
* Fixed paging and popup issues in button add dialog
* Made previous / next buttons in button add dialog more clear

= 4.13 =

* PRO Fixed Free Button Packs downloads
* PRO Fixed warning when exporting pack
* PRO Fixed warning when deleting a pack
* PRO Error handling upgraded in Update checker
* PRO Update Checker should return less warnings

* New option: Custom rel (Advanced) - for targeting popups
* Upgraded to Font Awesome 4.5.0
* Fixed remove collection link not working

= 4.12 =

* PRO - Fixed bug in responsive on resizing second text lines
* SO Pagebuilder : Fixed callbacks
* Shortcake : Fixed faulty callback when mixing add button functionality
* Social Share: fixed caching problem on blog pages results in same share URL's
* Social Share: fixed no image sharing with Pinterest.
* Editor: Fixed Javascript crash when clicking No in Delete button dialog

= 4.11 =

* Fixed possible crash due to extra return characters in the plugin causing WordPress to malfunction.
* Fixed SiteOrigin PageBuilder error when adding button
* Shortcake integration
* Add button dialog improvements
* Removed Twitter share count since Twitter doesn't support this anymore.
* Fixed issue with color picker
* Color picker window much larger now
* Add button interface now available in SiteOrigin editor widget

= 4.09 =

* PRO - Added Roboto as Web Font
* PRO - Issue with strange directory names solved

* Fixed conflict with WPMU Popup Pro
* New shortcode tag style="inline" - this forces the button style to load in document. This can be useful with JS-heavy sites who don't properly
load wp_footer();
* Several issues were fixed with upgrading the plugin and database tables
* Fixed link in edit post/page screen when having no buttons
* Fixed integration crash with new version of SiteOrigin Page Builder

= 4.07 =

* Fixed problem with URL encodings
* Fixed issue with Siteorigin widget not selecting button
* Changes in styling ( WP 4.4. update )


= 4.06 =

* Default button updated
* Integration with SiteOrigin Page Builder
* Updates to the color picker interface.
* Shortcode examples popup no longer expands the editor window
* Small fixes to title bar

= 4.02 =

* Fixed message in button inserter incorrectly stating no buttons are found.
* Javascript hardening to prevent conflicts in rare cases
* Better Settings interface
* PRO Fixed bug where Google Analytics buttons would not open in new window
* PRO Supports Text2 in shortcodes

= 4.01 =

* Add check for function 'maybe_convert_table_to_utf8mb4' which doesn't exists before WP 4.2.0
* Several interface fixes


= 4.0.2 =

* Fixed issue that could cause database save to fail on button edit.
* Security validation on bulk edit form added
* PRO All Social Share packs now available


= 4.0 =

* Social sharing [BETA]
* Fixed possible conflicts with colpick.js
* Fixed problem in URL with mailto: scheme
* Changed few PHP calls which possibly were causing crashes on older versions.
* Bug in internal social sharing block function.
* PRO Fixed issue with SSL and pack images
* PRO Fixed javascript bug with Google Analytics and links

= 3.19.1 =

* Fix for Google Font loading in Chrome

= 3.19 =

* Added posibility for bulk editing - at own risk
* Changed URL handling in certain cases to better URL encode the non-domain part.


= 3.18 =

* New filter for button url
* By default overview shows your latest buttons.
* Overview now sortable by button id
* PRO - License error was displayed on all pages. Now only within the plugin.
* PRO - New filter for adding google fonts.

= 3.17 =

* Position and size of preview window updated to be less in the way
* Small layout and text updates
* PRO Font Arimo added

= 3.16 =

* Fixed update box running through page title
* Fixed rating screen in some cases could not be closed
* Fixed plugin crash when database table is not present or wrong
* Lot of interface updates to make plugin more in line with WordPress admin defaults.

= 3.15 =

- Small validation fix on custom responsive field
- Fixed rare bug occurence in SCSS parser

* PRO Fixed bug with Google Analytics JS code in combination with a shortcode by name
* PRO Fixed bug with displaying icons in packs

= 3.14 =

* Fixed crash on servers without character encoding module enabled
* Attempts to upgrade utf-8 table to utf-8mb4 (plugin activation)
* Groups CSS statements without repeating <style> tag.

* PRO Font Awesome by default loads from plugin path
* PRO Add 'mb_fa_url' filter to change Font Awesome load path
* PRO Font Awesome icons updated to 4.4.0


= 3.13.2 =

* PRO Fixes issues with button search

= 3.13.1 =

* Fixed issue with font size not being displayed correctly in editor

= 3.13 =

* Fixed checkbox size on Chrome
* Fixed several layout issues
* Fixed issue with non-latin button names in the css declaration of button
* Paging buttons now disabled if there are no more pages to browse
* Updated SCSS parser to latest version

* PRO Fixed issue with license check if due date is today


= 3.12 =

* Moved from serialize to json_encode
* ID on button is now unique

* Fixed responsive bug with multiple custom settings
* Fixed checkbox interface bug with responsive settings
* Fixed CSS parse errors with multiple responsive queries

* PRO Fixed bug with google tracking and multiple same buttons
* PRO Small improvement to export page

= 3.11 =

* Better checking of number values in interface
* Button name is now also a class on the button ( for custom work )
* CSS output can now be compressed ( minified )

* PRO : Fixed font examples page

= 3.10 =

* Fixed: Now possible to add javascript to button URL
* Fixed: Several JS plugin conflicts ( notably with sidekick )
* Fixed: Now able to add spaces to URL

* PRO : Fixed bug in export when file open doesn't allow http

= 3.09 =

* Responsiveness bug fixes
* Fixed - custom sizes now allow width or height to be zero or not set
* Fixed rare bug when dbDelta was not properly loaded during installation


= 3.08 =

* PRO : New font Trocchi
* PRO : Fixed theme conflict in certain cases with icon styling

* Text align now defaults to empty
* New color picker fixing several bugs
* Fixed Divi themes / sitebuilder issue with add button
* Fixed small interface issue with paging and zero buttons
* Fixed interface issue with bulk actions notices
* Fixed various small unset variable issues

= 3.07 =

* Added Text align option
* Fixed interface issue with removing responsive parts
* Fixed URL Escaping issue
* Added German translation
* Better detection if all needed database fields are present
* Updates to the plugin styles
* Fixed a bug on the support page when allow_url_fopen is off
* Some shortcode examples in button editor
* Fixed a bug in responsive data handling
* Responsive items now can be hidden per screen size

* PRO new feature: Google Event Tracking

= 3.04.2 =

* Reworked add button interface in the post editor

* PRO: Fixed bug dropping second text line when remigrating in settings

= 3.04.1 =

* Version numbering change to allow better version management in WP
* Improved: Wordpress style pagination
* Improved: The button editor interface now warns before permanently deleting a button
* Fixed: Layout issue in button overview disrupting interface
* Fixed: Support area not showing correctly and deprecated error on mysql info.
* Fixed: Close button didn't show in the external css dialog

* PRO : Fixed bug sometimes preventing automatic updating
* PRO : New search functionality in button overview.
* PRO : Fixed new text line not showing up when adding new button in preview before saving
* PRO : New Google Web Font: Raleway


= 3.04a =

* Fixed : License check could hang in exceptionable situation

= 3.04 =

* New : Pagination

* Fixed : Issue in parser causing issues when saving buttons
* Fixed : Button list can now display buttons from cache
* Fixed : Big buttons in list would be too large for screen causing problems
* Several smaller issues and interface hardening
* Updated several links

* PRO : Fixed several button packs interface bugs
* PRO : Fixed issue in license check for updates

= 3.03 =

* Fixed: Text shadow and border shadow were still showing with zero width.
* Fixed: Migrate script from old version moves button id correctly.
* Fixed: Link to buttons missing on plugins page


= 3.02 =

* Fixed: IK Facebook plugin jamming the colorpicker
* Fixed: Bug where hover cursor wouldn't show up when url was added via shortcode
* Fixed: Moving table to UTF-8 in settings works again

* Option for remigration of settings from old to new table in case upgrade didn't complete
* Added PHP 5.3 requirement in readme
* Added checks for both PHP version, and if activation did run.
* Removed default 'white-space: nowrap'.
* Tested with WP 4.2

= 3.01 =

* Code rebuilt
* Major performance enhancements
* Responsive module
* Dimensions, set width and height

= 3.0 =

* Beta release of new codebase

v2.8: Nov 6, 2014
- CSS removed from inline, placed in footer
- JS removed from inline, placed in footer
- Roboto added as a font
- Database updated to allow for negative container margins

v2.7.1: Oct 2, 2014
- Fixed XSS Vulnerability

v2.7: Sep 15, 2014
- Updated Font Awesome to 4.2
- Added 40 new Font Awesome icons

v2.6.2: Jul 21, 2014
- Added permissions setting to allow for Contributor to Admin Only

v2.6.1: Jun 19, 2014
- Fixed Google Font HTTPS/HTTP issue
- Minor CSS adjustments to .mb-icon

v2.6: Jun 16, 2014
- Added Font Awesome Icon capabilities.
- Fixed box-sizing to avoid overflow with extra padding.

v2.5.2: Apr 30, 2014
- Fixed button editor editor issue where button background colors weren't being reflected in real-time in Firefox and Internet Explorer.

v2.5.1: Apr 21, 2014
- Replaced TinyMCE button with "Add Button" media button.

v2.5.0: Apr 4, 2014
- Added "Copy and Invert" for button gradients.

v2.4.0: Mar 11, 2014
- Added "Save" button to button edit page
- Added EB Garamond and Spinnaked Google Web Fonts

v2.3.6: Feb 5, 2014
- Added Settings page.
- Added "Alter Table" button for foreign text issue.

v2.3.5: Jan 11, 2014
- Replaced separate PHP page for viewing button CSS with lean modal box.

v2.3.4: Jan 6, 2014
- Fixed vulnerability issue when viewing the button CSS page.

v2.3.3: Dec 16, 2013
- Minor UI and style changes to better support WP 3.8.

v2.3.2 : Dec 6, 2013
- Fixed issue with file.php being loaded twice when exporting button packs.

v2.3.1 : Oct 20, 2013
- Added 12 more Google Web Fonts (Bigelow Rules, Cherry Swash, Courgette, Devonshire, Grand Hotel, Karla, Lily Script One, Montserrat, Noto Sans, Questrial, Special Elite, and Tangerine).

v2.3.0 : Oct 9, 2013
- Added shortcut links in Colors section for enhanced usability.
- Updated the shortcode so that it doesn't render the HREF or the hover colors when button URL is empty.

v2.2.0 : Sep 17, 2013
- Added gradient and opacity options.
- Changed the button output window so that the button isn't clickable.

v2.1.0 : Jul 27, 2013
- Changed MAXBUTTONS_PRO_PLUGIN_URL constant to call the plugins_url() function instead of WP_PLUGIN_URL so that the proper url scheme is used.
- Changed MAXBUTTONS_PRO_PLUGIN_DIR constant to call the plugin_dir_path() function instead of WP_PLUGIN_DIR.

v2.0.0 : May 8, 2013
- Added License page and activation functionality.
- Changed storage location of button packs to /uploads/maxbuttons-pro/packs.
- Changed storage location of exports folder to /uploads/maxbuttons-pro/exports.
- Increased the TinyMCE plugin button window to 700px wide.
- Ignoring the container element on the button list pages so that the button alignment is consistent on those pages.
- Added 'exclude' parameter to shortcode to exclude button from rendering on certain posts/pages.
- Replace get_theme_data() with wp_get_theme() on the support page; forces WP 3.4 as minimum requirement.

v1.9.1 : Feb 16, 2013
- Plugin now uses updater class from WP Updates to better support update notifications and auto-updating.

v1.9.0 : Feb 1, 2013
- Fixed issue where wp-load.php couldn't be found in some cases when viewing the button CSS.
- Added TinyMCE plugin to be able to insert button shortcode from the Visual tab in the WP text editor.

v1.8.0 : Jan 24, 2013
- Added width style to .mb-text and .mb-text2 elements.
- Added icon_width column to database table so that proper width style can be set on the .mb-icon element.
- Added box-shadow and border-radius styles for the button icon image to clear those styles added by theme stylesheets.
- The "Use Container" option is now enabled by default.
- Added 10 more Google Web Fonts (Arvo, Changa One, Crafty Girls, Droid Serif, Exo, Lora, Nunito, Open Sans Condensed, Shadows Into Light, Source Sans Pro).

v1.7.1 : Jan 21, 2013
- Fixed issue where click events for buttons that were used with the Shopp plugin stopped working.

v1.7.0 : Jan 20, 2013
- Added ability to externalize the button CSS code.
- Added option to use !important on button styles.
- Added "mb-" prefix to several CSS classes to help avoid conflicts with theme styles.
- Fixed issue with Shopp integration where only the first button worked on a category list page.

v1.6.1 : Dec 17, 2012
- Minor fix for supporting the Shopp "Submit Order" button.

v1.6.0 : Dec 16, 2012
- Added support for Shopp ecommerce integration.
- Added the Support page.

v1.5.0 : Nov 14, 2012
- Added support for localization.

v1.4.1 : Aug 30, 2012
- Fixed bug where shortcode was using wrong constant for plugin URL to include PIE.htc in button hover style.

v1.4.0 : Aug 27, 2012
- Added center div wrapper option to Container section in button editor.
- Added nofollow option to button editor.
- Added 10 more Google Web Fonts (Antic Slab, Asap, Bitter, Cabin, Economica, Gudea, Josefin Slab, Krona One, Lato, Rokkitt).
- Added button font examples to show how each font looks when used in a button.
- Added status field to database table to provide ability to move buttons to trash (default = 'publish').
- Added actions for Move to Trash, Restore, and Delete Permanently.
- Added CSS3PIE for better IE support.
- Wrapped some fields with htmlspecialchars() to properly format them during the export process.

v1.3.0 : Mar 19, 2012
- Added plugin update notification mechanism.
- The container is now enabled by default.
- Fixed bug where container options weren't being set properly when a button was copied from a button pack.
- Removed the IE-specific gradient filter and -ms-filter styles from shortcode output due to issue when used with rounded corners.

v1.2.0 : Feb 20, 2012
- Added container options.

v1.1.0 : Feb 13, 2012
- Added option for the icon alt text.
- Added additional styles for icon image to help avoid theme image styles from creeping into the button.
- Added checks to only render icon elements and styles if the button actually has an icon.

v1.0.5 : Feb 3, 2012
- Added :visited style to the shortcode output.

v1.0.4 : Feb 2, 2012
- Fixed another issue with the colorpickers for gradient start/hover and gradient end/hover.

v1.0.3 : Feb 1, 2012
- Fixed issue in button editor where the colorpickers for text shadow, gradient start, gradient end, border, and box shadow changed the value of their respective hover colorpicker.

v1.0.2 : Jan 31, 2012
- Fixed issue where the button text color was being overriden by theme styles.
- Fixed issue in button editor where the text colorpicker changed the value of the text hover colorpicker.

v1.0.1 : Jan 22, 2012
- Fixed style and script loading so that they are only loaded on this plugin's admin pages, not *all* admin pages. Will help avoid theme and plugin conflicts.
- Added copy and button that links to button packs on the Packs page.
- Added filter for widget_text to recognize and execute the button shortcode.

v1.0.0 : Jan 4, 2012
- Initial version.
