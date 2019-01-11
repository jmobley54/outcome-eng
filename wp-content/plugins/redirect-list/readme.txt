=== Plugin Name ===
Contributors: sourcefound
Donate link: https://membershipworks.com
Tags: 301, 302, 307, forwarding, redirect, url redirect, http redirect, redirection
Requires at least: 3.0.1
Tested up to: 4.7.5
Stable tag: 1.8
License: GPL2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A really lightweight, clean and simple 301, 302 or 307 HTTP redirect plugin that also supports matching of GET query parameters. 

== Description ==

Lightweight and clean Redirect plugin performs HTTP redirection, and supports matching of GET query parameters without regular expressions (for those pesky ?page=123 or ?_escaped_fragment_=xxx urls).

* No advertising, links or tracking code.
* Lightweight code (~100 LOC).
* 301, 302 or 307 HTTP redirect.
* Specify from and destination urls in a list manually.
* Does not require creating a custom page.
* Match url regardless of GET parameters.
* Match url only if no GET parameter exists.
* Match url only if GET parameter exists (value does not matter).
* Match url only if GET parameter exists and value matches.
* Does not support Multisite (sorry!).

A free plugin from the workshop of [MembershipWorks](https://membershipworks.com). Serbian translation thanks to Ogi Djuraskovic [firstsiteguide.com](http://firstsiteguide.com/).

== Installation ==

1. Install the plugin via the WordPress.org plugin directory or upload it to your plugins directory.
1. Activate the plugin.
1. Under 'Settings' -> 'Redirects', enter the urls to redirect from and to.

== Changelog ==

= 1.0 =
* Initial release

= 1.2 =
* Fixes issue with Microsoft IIS servers

= 1.3 =
* Fixes compatibility with PHP 5.2 and earlier

= 1.4 =
* Adds import and export feature

= 1.5 =
* Moves hook for redirects to execute earlier (after plugins_loaded)
* Support for large (>330) lists

= 1.6 =
* Supports 307 redirect

= 1.7 =
* Translation ready

= 1.8 =
* Corrects URL matching so exact match required