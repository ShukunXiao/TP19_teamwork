=== Passster - Password Protection ===
Contributors: patrickposner
Tags: password protection, content protection, content restriction, captcha, membership, cookie restriction, members area, password protect
Requires at least: 4.6
Tested up to: 5.9
Requires PHP: 7.0
Stable tag: 3.5.5.2

Passster is the best and simplest solution to password-protect your content.

== Description ==

Protect your entire website, only single pages or posts or just areas of content within it.
Passster brings a lot of flexibility in creating password-protected areas on your website.

**Area protection**

Create protected areas that can be added with a simple shortcode.
The shortcode will be fully generated for you based on your configuration of your area.

**Page protection**

Protect entire pages with the same settings you already know from the area protection, but without even using a shortcode.
It blocks the entire page while keeping your layout intact instead of showing an ugly protection layout.

**Global Protection**

On each page, you can select to activate the global protection. Each visitor without the required password will be redirected to the page
as long as they are not authenticated. The Cookie option from Passster->Options is required for this.

**Pagebuilder**

All three protection modes supporting all well-known page builders.
Currently successfully tested are:

* WPBakery Pagebuilder
* Elementor / Elementor Pro
* Beaver Builder
* Divi Builder
* Oxygen Builder
* Gutenberg

To avoid any layout shifts or rendering problems, it's highly recommended to activate the "Reload after successful validation" option in Passster->Options->Compatibility Mode.


= Features =

**Free**

* restrict areas and add them via shortcode
* restrict pages
* restrict your entire website
* use a single password or a captcha for protection
* AJAX for unlocking with caching and without a reload
* change the global styles and texts in the WordPress Customizer
* Use cookies to save encrypted passwords for longer access

**Pro**

Additionally to all free features, the pro version includes:

* use multiple passwords
* use large passwords lists with password expiration by usage and date
* use Google ReCaptcha (v2 and v3) for protection
* expire passwords from a list after one-time usage, after several usages, or by time (hours, days, weeks, months)
* modify headline, description, placeholder, buttons per shortcode
* modify headline, description, placeholder, buttons per page
* auto-unlock content per user and user role
* unlock content with encrypted links and use Bitly to shorten the entire URL (optional)

Paired with exceptional support directly from the developer, timely updates and feature integrations, and extensive documentation you can't go wrong with Passster Pro.
Get it now on [patrickposner.dev](https://patrickposner.dev/plugins/passster/)

**Documentation**

I regularly optimize the documentation and release extensive tutorials on how to use Passster in a multitude of use-cases.

Learn more on [patrickposner.dev/docs](https://patrickposner.dev/docs/)

== Support ==

The free support is exclusively limited to the wordpress.org support forum.

Any kind of email priority support, customization, and integration help need a valid premium license.

=== CODING STANDARDS MADE IN GERMANY ===

The plugin is coded with **modern PHP** and **WordPress standards** in mind. It’s fully OOP coded. It’s highly extendable for developers through several actions and filter hooks.

Passster keeps your website performance in mind. Every script is **loaded conditionally** and all input and output data is secured.


=== MULTI-LANGUAGE ===

All major texts and information can be modified from the admin area of Passster.

The plugin is **fully translatable** in your language. At the moment there are only en_EN and de_DE, but you can easily add your preferred language as a .po/.mo.

It’s also fully compatible with WPML and Polylang.

== Screenshots ==
1. Passster Password Form
2. Passster Areas
3. Passster Settings
3. Passster Customizer Options

== Installation ==

Passster is simple to install:

1. Download the .zip'
1. Unzip
1. Upload the directory to your '/wp-content/plugins' directory
1. Go to the plugin management page and enable the Passster Plugin
1. Browse to Settings > Passster
1. Customise your settings and your good to go!

== Changelog ==

= 3.5.5.2 =

* filter to disable base64 encryption for cookies and links
* Freemius SDK update
* jQuery migrate fix for captcha
* improved escaping of shortcode attributes (area and password lists)

= 3.5.5.1 =

* better permission check settings page
* better permission check metaboxes
* improved german translation
* added redirect parameter and settings
* updated cookie js for better compatibility
* added support for WooCommerce products and shop pages
* filter to allow spaces in passwords
* fixed version number

= 3.5.4 =

* fixed notice for checking areas
* updated dependencies
* fixed security issue in is_valid()
* fixed validation with reload option

= 3.5.3 =

* security patch
* better error handling with fade and clear input
* better area restriction
* better performance for password checkups
* removed rych hash dependency
* improved WooCommerce product restriction
* Gutenberg Support for areas
* fixed notice for bitly check
* fixed notice for recaptcha validation

= 3.5.2 =

* fix for shortcode validation capabilities
* improved validation for global protection

= 3.5.1 =
* bugfix for link protection
* pagebuilder editing support for areas
* fixed show password function
* is_valid() for areas
* dynamic {post-id} parameter for shortcodes in AJAX
* hide parameter for areas
* check form values before AJAX submit (required attributes for example)

= 3.5 =

* introduced protected areas
* shortcode configurator in areas
* bugfix: spaces in password lists
* PHP 8 support improvements
* removed auto-updater for options
* automatically add metaboxes to all registered (public) post types
* removed wp_auto_p() for Oxygen Builder support
* removed deprecated Pagebuilder modules (handled with areas now)
* min value for Cookie set to 1 and no negativ values possible
* improved widget support with areas
* performance optimization for large password lists


= 3.4.2 =

* lighter freemius integration
* admin UI improvements
* direct links for documentation and support in admin header

= 3.4.1 =

* added expire by usage and time for password lists (pro only)
* added global password protection
* base64 encryption for cookies
* bugfix for bitly URL toggle (pro only)
* improved uninstall with latest options
* auto activate reload option if pagebuilder is activated

= 3.4 =

* added support to unlock widgets via ajax
* added support to unlock acf fields via ajax
* added shortcode params to page protection (pro only)
* added user restriction to page protection (pro only)
* fixed one-time usage for password lists without ajax (pro only)
* added option to redirect to source URL after link unlock (pro only)

= 3.3.9 =
* latest freemius SDK
* fixed ID parameter for multiple forms per page
* custom headline, ID parameter for Recaptcha v2/v3
* better enqueue for ReCaptcha v2

= 3.3.8 =
* removed old cache busting prevent 404 errors for files
* wp_enqueue_script for ReCaptcha preventing cache issues
* introduced ajax loader to indicate verification
* fixed metabox showing wrong selected password list
* improved admin wording for more clarification
*  updated german translation files

= 3.3.7 =

* WordPress 5.5 compatibility
* Elementor with Ajax mode compatibility
* added WPML config file
* update german translation
* prevent fatal error if ps_run_plugin() is already declared
* number field instead of text for cookie duration
* ReCaptcha without async defer (handled via Caching plugins)
* fixed PHP notice for bitly integration

= 3.3.6 =
* cache-busting for no-ajax mode and cookies
* fixed double docs link
* support option for third-party-shortcodes with pre-render
* removed auto-space from password lists
* new link encryption solution with metabox and bitly
* hide parameter for WPBakery integration
* updated and fixed german translation

= 3.3.5 =
* performance improvements for password lists
* support for Google ReCaptcha v2 with selection
* more efficient ajax handling for different unlock methods.
* auto-update cookie settings if no ajax mode is used.

= 3.3.4.1 =
* more robust regex for various shortcode implementations
* added action to track unlocks with Google Analytics and other tracking solutions

= 3.3.4 =
* fixed additional params to overwrite texts in the shortcode
* fixed empty content while using additional parameters
* added tablepress support for ajax
* implemented old recaptcha parameters for backwards compatibility

= 3.3.3 =
* better compatibility mode with cache busting
* better ReCaptcha integration with ajax and with cookies
* compatibility: full page protection with divi builder
* more reliable way to get valid response via ajax
* mobile-friendly cache-busting after authentication

= 3.3.2 =
* added compatibility mode for forcing reload
* compatibility fix Elementor full page protection
* compatibility fix WpBakery Pagebuilder full page protection
* re-added error message in Customizer
* improved german translation

* fixed captcha loading while not in use
* fixed wrong redirection after activation
* fixed wrong object call for elementor users.

= 3.3.1 =
* fixed captcha loading while not in use
* fixed wrong redirection after activation
* fixed wrong object call for elementor users.

= 3.3 =
* major release
* new admin UI and simplfied settings
* password protection for pages, posts and products
* new captcha solution with canvas objects
* new Google ReCaptcha v3 integration
* removed requirements for PHP sessions for better compatibility
* removed old Google API vendor for better compatibility
* refactored the entire shortcode and submit solution
* ajax-based submit and validation - no page reload required anymore
* fixed cookie solution for captcha, ReCaptcha
* easier template function is_valid() for complete checks of all parameters
* fixed shortcode parameters for headline and id 
* better uninstall cleanup
* intrated metabox for setting Passster settings for complete pages


= 3.2.6.1 =
* cookie for passwords conditional function fixed
* introduced API parameter to elementor and beaver builder
* fixed notice if api not available in helper methods

= 3.2.6 =
* WPBakery Page Builder row protection with correct default values
* new helper class for cookies
* api parameter possibility to add external apis

= 3.2.5 =
* Another VC protection row fix..
* compatibility WPBakery 6.0.5

= 3.2.4 =
* VC row protection fix
* new partly parameter
* cookie set fix and conditional function to check for
* new type hint solution (better jQuery compatibility)
* is_cookie_valid check for all password related protection types
* admin css fixes with prefix


= 3.2.3 =
* Password Lists fix for all page builder
* prevent autoload error if free and premium version installed
* customizer as default values for page builder options
* placeholder now configurable in the customizer

= 3.2.2 =
* fixed captcha notice
* fixed rows shortcode for WPBakery Pagebuilder
* more efficient notice handling in admin area

= 3.2.1 =
* adding the "hide" parameter to hide forms if set and multiple forms used
* compatibility AAM plugin fix for multiple user roles
* captcha is now a free addon - lower php version needed for basic password usage
* check_atts method now working correctly
* WPBakery Pagebuilder addon fix (free)
* WPBakery Pagebuilder addon protect rows (only pro)
* add message for captcha usage
* new (and working) solution for show passwords before submitting

= 3.2.0.6 =
* new AMP support with cookies
* Fixed delete error notice for passster_lists function not exists
* introduced new helper function for AMP set_amp_headers()
* drop db table for sessions if full uninstall option set
* customizer option to show password while typing

= 3.2.0.5 =
* fixed amp notice
* fixed backend_admin_notice error
* fixed customizer for themify ultra theme

= 3.2.0.4 =
* PS_List collision fix

= 3.2.0.3 =
* autoload backupwp collision fix

= 3.2.0.2 =
* SVN fix for missing files
* cookies for conditional functions

= 3.2.0.1 =
* pagebuilder path fix
* admin amp option fix

= 3.2 =
* security patch freemius
* add cookie option for multiple passwords
* add pagebuilder addons in free version
* fix php notices for php 7 support
* remove OptionsHandler class for support older php versions
* add password lists (admin + shortcode)
* update translation files
* added AMP support for all protection types
* improve default values after Installation

= 3.1.9.1 =
* Fix PHP 5.6 upgrader problems
* Moved autoloader up so database upgrade is handeled correctly


= 3.1.9 =
* PHP 5.6 compatibility
* function naming fixes
* optimize session handler class

= 3.1.8 =
* introduce conditional functions for template usage
* completely remove the autofocus
* fixes save settings for user_toggle option
* updates the session handling for captcha to PHP 7.2 compatibility
* prevents autofill for safari, chrome and webkit supported browsers

= 3.1.7 =
* includes fixes for beaver builder module support

= 3.1.6 =
* Support Release
* Fixed multiple passwords runtime
* add customizer notice on Installation
* improved german translation
* add an seprate atts function for more readable code
* add new users addon
* 

= 3.1.5 =
* Support Release
* Add auth parameter for multiple shortcodes per page
* Fixed <span> for error messages
* Fixed wp_enqueue_styles for windows servers
* Fixed php notice for captcha options

= 3.1.4 =
* Support Release
* fixed problems with WP Sessions table and Database Handler
* fixed License Activation
* Add option for autofocus
* fixed helper for addon activation

= 3.1.3 =
* Support Release
* Major improvements for captcha
* set width and height for captcha
* integrate wp-sessions-manager for session handling via database
* adding page builder support for elementor, WPBakery Pagebuilder and beaver builder (pro only)
* fix one pager bug with passster forms

= 3.1.2 =
* Support Release
* Add placeholder and button label per shortcode
* Fix option set issues for captcha
* get rid of HTTP API and all external calls and replace with object cache

= 3.1.1 =
* Support Release
* Fixing PHP notice for addons
* replace_file_get_contents() with WP HTTP API

= 3.1 =
* new admin ui
* captcha is back!
* cache-compatible cookie solution
* design modifications via customizer
* cross-browser-compatible forms
* shortcode generator
* password generation with newset bcrypt standards
* password generator
* fix several bugs like instructions text, translations, php errors

= 3.0 =
* under new development
* compatibilty with WordPress 4.9+
* clean up and restructure whole plugin
* remove deprecated solutions for ajax and captcha
* removed date based selection of cookie expires

= 2.11 =
* Setting "Password Field Placeholder" now accessible through "Settings -> Passster -> Password/CAPTCHA Field"

= 2.10 =
* Form and CAPTCHA instructions moved to outside the form.
* `content_protector_unlocked_content` filter bug in AJAX mode fixed.
* CSS for `div.content-protector-form-instructions` fixed.
* New Setting "CAPTCHA Case Insensitive" - to allow users to enter CAPTCHAs w/o case-sensitivity.
* New action `content_protector_ajax_support` - for loading any extra files needed to support your protected content in AJAX mode.

= 2.9.0.1 =
* Fixed bug crashing `content_protector_unlocked_content` filter.
* Full AJAX support for `[caption]` built-in shortcode.

= 2.9 =
* Full AJAX support for `[embed]`, `[audio]`, and `[video]` built-in shortcodes.
* Added full support for `[playlist]` and `[gallery]` built-in shortcodes.
* Fixed Encrypted Passwords Storage setting message bug.
* `content_protector_content` filter now called `content_protector_unlocked_content`.
* `content_protector_unlocked_content` filter can now be customized from the Settings -> General tab.
* `the_content` filter now applied to form and CAPTCHA instructions.

= 2.8 =
* Partial AJAX support for `[embed]`, `[audio]`, and `[video]` built-in shortcodes. (experimental)
* Fixed AJAX error from code refactoring

= 2.7 =
* Displaying Form CSS on unlocked content is now a user option (on the Form CSS tab).
* When saving settings, the Settings page will now remember which tab you were on and load it automatically,
* Fixed potential cookie expiry bug for sessions meant to last until the browser closes (expiry time set explicitly to '0').
* Improved error checking for conflicting settings.
* Some code refactoring.

= 2.6.2 =
* Fixed output buffering bug for access form introduced in 2.6.1.

= 2.6.1 =
* Fixed AJAX security nonce bugs.

= 2.6 =
* jQuery UI theme updated to 1.11.4
	
= 2.5.0.1 =
* New setting to manage encrypted passwords transient storage.
* New settings for Password/CAPTCHA Fields character lengths.
* Improved option initialization and cleanup routines.
* `content-protector-ajax.js` now loads in the footer.
* WPML/Polylang compatibility (beta).
* New partial translation into Serbian (Latin); thanks to Andrijana Nikolic from WebHostingGeeks (Novi parcijalni prevod na Srpski ( latinski ); Hvala Andrijana Nikolic iz WebHostingGeeks)

= 2.5 =
* Skipped

= 2.4 =
* Skipped

= 2.3 =
* Settings admin page now limited to users with `manage_options` permission (i.e., admin users only).
* Fixed bug where when using AJAX and CAPTCHA together, CAPTCHA image didn't reload on incorrect password.
* New settings: use either a text or password field for entering passwords/CAPTCHAs, and set placeholder text for those fields.
* Added `autocomplete="off"` to the access form.
* Streamlined i18n for date/time pickers (Use values available in Wordpress settings and `$wp_locale` when available, combined *-i18n.js files into one).

= 2.2.1 =
* Fixed AJAX bug where shortcode couldn't be found if already enclosed in another shortcode.
* Clarified error message if AJAX method cannot find shortcode.
* Changed calls from `die()` to `wp_die()`.

= 2.2 =
* Removed `content-protector-admin-tinymce.js` (No need anymore; required JS variables now hooked directly into editor). Fixes incompatibility with OptimizePress.

= 2.1.1 =
* Added custom filter `content_protector_content` to emulate `apply_filter( 'the_content', ... )` functionality for form and CAPTCHA instructions.

= 2.1 =
* Rich text editors for form and CAPTCHA instructions.
* NEW Template/Conditional Tag: `content_protector_is_logged_in()` (See Usage for details).
* Performance improvements via Transients API.

= 2.0 =
* New CAPTCHA feature! Check out the CAPTCHA tab on Settings -> Content Protector for details.
* Improved i18n.
* Various minor bug fixes.
	
= 1.4.1 =
* Dashicons support for WP 3.8 + added. Support for old-style icons in Admin/TinyMCE is deprecated.
* Unified dashicons among all of my plugins.

= 1.4 =
* Added "Display Success Message" option.

= 1.3 =
* Added "Shared Authorization" feature.
* Renamed "Password Settings" to "General Settings".

= 1.2.2 =
* Added support for Contact Form 7 when using AJAX.

= 1.2.1 =
* Fixed label repetition on "Cookie expires after" drop-down menu.

= 1.2 =
* Various CSS settings now controllable from the admin panel.
* Palettes on Settings color controls are now loaded from colors read from the active Theme's stylesheet.  This
should help in choosing colors that fit in with the active Theme.
* Spinner image now preloaded.
* Some language strings changed.

= 1.1 =
* AJAX loading message now customizable.

= 1.0.1 =
* Added required images for jQuery UI theme.
* Fixed some i18n strings.

= 1.0 =
* Initial release.

== Upgrade Notice ==
= 2.8 =
New features and bug fixes. Please upgrade.

= 2.6.1 =
New bug fixes. Please upgrade.

= 2.3 =
New features and bug fixes. Please upgrade.
	
= 2.1.1 =
Added custom filter `content_protector_content` to emulate `apply_filter( 'the_content', ... )` functionality for form and CAPTCHA instructions. Please upgrade.

= 2.1 =
New features. Please upgrade.

= 2.0 =
New features and bug fixes. Please upgrade.

= 1.2.1 =
Fixed label repetition on "Cookie expires after" drop-down menu. Please upgrade.

= 1.0.1 =
Added required images for JQuery UI theme and fixed some i18n strings.

= 1.0 =
Initial release.
