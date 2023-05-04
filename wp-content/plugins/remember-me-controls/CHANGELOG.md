# Changelog

## 1.9.1 _(2021-02-13)_
* Fix: Add missing textdomain. Props @kittmedia.
* Change: Enhance a FAQ answer to make clear that an existing login session will not be affected by an update to the remember me duration (must log in again)
* Change: Note compatibility through WP 5.6+
* Change: Update copyright date (2021)

## 1.9 _(2020-07-25)_

### Highlights:

This minor release adds support for using commas when setting the remember me duration, adds HTML5 compliance when supported by the theme, improves settings help text and other documentation, updates its plugin framework, adds a TODO.md file, updates a few URLs to be HTTPS, expands unit testing, updates compatibility to be WP 4.9 through 5.4+, and other minor behind-the-scenes tweaks.

### Details:

* New: Add HTML5 compliance by omitting `type` attribute to 'script' and 'style' tags when the theme supports 'html5'
* New: Add help text to settings whose value change won't take effect until subsequent logins regarding as much
* New: Add TODO.md and move existing TODO list from top of main plugin file into it (and add items to it)
* Change: Allow use of commas in user-submitted value for `remember_me_duration` setting
* Change: Update JavaScript coding syntax
* Change; Add help text to the top of the settings page
* Change: Use a superscript for footnote asterisk and extract markup from translatable string
* Change: Update plugin framework to 051
    * 051:
    * Allow setting integer input value to include commas
    * Use `number_format_i18n()` to format integer value within input field
    * Update link to coffee2code.com to be HTTPS
    * Update `readme_url()` to refer to plugin's readme.txt on plugins.svn.wordpress.org
    * Remove defunct line of code
    * 050:
    * Allow a hash entry to literally have '0' as a value without being entirely omitted when saved
    * Output donation markup using `printf()` rather than using string concatenation
    * Update copyright date (2020)
    * Note compatibility through WP 5.4+
    * Drop compatibility with version of WP older than 4.9
* Change: Tweak text on help tab
* Change: Add a few new FAQ entries and amend another
* Change: Include another example scenario in which login cookies could be invalidated
* Change: Tweak verbiage of various documentation
* Change: Note compatibility through WP 5.4+
* Change: Drop compatibility with versions of WP older than 4.9
* Change: Update links to coffee2code.com to be HTTPS
* Unit tests:
    * New: Add `get_default_hooks()` as a helper method for getting the default hooks
    * New: Add tests for `add_css()`, `add_js()`, `help_tabs_content()`, `maybe_add_hr()`, `options_page_description()`
    * New: Add test for setting name
    * New: Add test for hook registering
    * Change: Store plugin instance in test object to simplify referencing it
    * Change: Remove unnecessary unregistering of hooks in `tearDown()`
    * Change: Remove duplicative `reset_options()` call
    * Change: Use HTTPS for link to WP SVN repository in bin script for configuring unit tests
* Change: Updated screenshot

## 1.8.1 _(2020-01-01)_
* Change: Note compatibility through WP 5.3+
* Change: Update copyright date (2020)
* Change: Tweak changelog formatting for v1.8 release

## 1.8 _(2019-06-28)_

### Highlights:

This release is a minor update that verifies compatibility through WordPress 5.2+ and makes minor behind-the-scenes improvements.

### Details:

* Change: Initialize plugin on `plugins_loaded` action instead of on load
* Change: Update plugin framework to 049
    * 049:
    * Correct last arg in call to `add_settings_field()` to be an array
    * Wrap help text for settings in `label` instead of `p`
    * Only use `label` for help text for checkboxes, otherwise use `p`
    * Ensure a `textarea` displays as a block to prevent orphaning of subsequent help text
    * Note compatibility through WP 5.1+
    * Update copyright date (2019)
    * 048:
    * When resetting options, delete the option rather than setting it with default values
    * Prevent double "Settings reset" admin notice upon settings reset
* New: Add CHANGELOG.md file and move all but most recent changelog entries into it
* Unit tests:
    * Change: Update unit test install script and bootstrap to use latest WP unit test repo
    * Change: Ensure settings get reset before assigning newly set values
    * Fix: Fix broken unit test
* Change: Note compatibility through WP 5.2+
* Change: Add link to plugin's page in Plugin Directory to README.md
* Change: Update copyright date (2019)
* Change: Update License URI to be HTTPS
* Change: Split paragraph in README.md's "Support" section into two

## 1.7 _(2018-04-19)_
* New: Add support for BuddyPress Login widget
* New: Add support for Sidebar Login plugin (https://wordpress.org/plugins/sidebar-login/)
* New: Add support for Login Widget With Shortcode plugin (https://wordpress.org/plugins/login-sidebar-widget/)
* New: Change login form defaults according to plugin settings
* Change: Update plugin framework to 047
    * 047:
    * Don't save default setting values to database on install
    * Change "Cheatin', huh?" error messages to "Something went wrong.", consistent with WP core
    * Note compatibility through WP 4.9+
    * Drop compatibility with version of WP older than 4.7
    * 046:
    * Fix `reset_options()` to reference instance variable `$options`
    * Note compatibility through WP 4.7+
    * Update copyright date (2017)
    * 045:
    * Ensure `reset_options()` resets values saved in the database
    * 044:
    * Add `reset_caches()` to clear caches and memoized data. Use it in `reset_options()` and `verify_config()`
    * Add `verify_options()` with logic extracted from `verify_config()` for initializing default option attributes
    * Add  `add_option()` to add a new option to the plugin's configuration
    * Add filter 'sanitized_option_names' to allow modifying the list of whitelisted option names
    * Change: Refactor `get_option_names()`
    * 043:
    * Disregard invalid lines supplied as part of hash option value
    * 042:
    * Update `disable_update_check()` to check for HTTP and HTTPS for plugin update check API URL
    * Translate "Donate" in footer message
* Change: Store setting name in class constant
* New: Add README.md
* New: Add FAQ indicating that the plugin is GDPR-compliant
* Change: Unit tests:
    * Add and improve unit tests
    * Default `WP_TESTS_DIR` to `/tmp/wordpress-tests-lib` rather than erroring out if not defined via environment variable
    * Enable more error output for unit tests
* Change: Add GitHub link to readme
* Change: Note compatibility through WP 4.9+
* Change: Drop compatibility with versions of WP older than 4.7
* Change: Update copyright date (2018)
* Change: Update installation instruction to prefer built-in installer over .zip file

## 1.6 _(2016-03-23)_

### Highlights:

* This release largely consists of minor behind-the-scenes changes.

### Details:

* Change: Update plugin framework to 041:
    * Change class name to `c2c_RememberMeControls_Plugin_041` to be plugin-specific.
    * Set textdomain using a string instead of a variable.
    * Don't load textdomain from file.
    * Change admin page header from 'h2' to 'h1' tag.
    * Add `c2c_plugin_version()`.
    * Formatting improvements to inline docs.
* Change: Add support for language packs:
    * Set textdomain using a string instead of a variable.
    * Remove .pot file and /lang subdirectory.
* Change: Express WP default cookie expiration duration as 2 days instead of 48 hours.
* Change: Declare class as final.
* Change: Explicitly declare methods in unit tests as public or protected.
* Change: Minor code reformatting.
* Change: Minor tweak to description.
* Change: Minor improvements to inline docs and test docs.
* New: Add LICENSE file.
* New: Create empty index.php to prevent files from being listed if web server has enabled directory listings.
* Change: Note compatibility through WP 4.4+.
* Change: Remove support for versions of WordPress older than 4.1.
* Change: Update copyright date (2016).

## 1.5 (2015-02-22)
* Add unit tests
* Update plugin framework to 039
* Explicitly declare `activation()` and `uninstall()` static
* Reformat plugin header
* Minor code reformatting (spacing, bracing)
* Change documentation links to wp.org to be https
* Minor documentation spacing changes throughout
* Note compatibility through WP 4.1+
* Update copyright date (2015)
* Add plugin icon
* Regenerate .pot

## 1.4 (2014-01-15)
* Add 'About' section to help panel
* Move descriptive text from top of settings page into 'About' section of help panel
* Remove a bunch of pre-WP3.5 compatibility code
* Update plugin framework to 037
* Better singleton implementation:
    * Add `get_instance()` static method for returning/creating singleton instance
    * Make static variable 'instance' private
    * Make constructor protected
    * Make class final
    * Additional related changes in plugin framework (protected constructor, erroring `__clone()` and `__wakeup()`)
* Add checks to prevent execution of code if file is directly accessed
* Use explicit path for `require_once()`
* Discontinue use of PHP4-style constructor
* Discontinue use of explicit pass-by-reference for objects
* Minor documentation improvements
* Minor code reformatting (spacing, bracing)
* Note compatibility through WP 3.8+
* Drop compatibility with version of WP older than 3.6
* Update copyright date (2014)
* Regenerate .pot
* Change donate link
* Update screenshots
* Add banner

## 1.3
* Use `YEAR_IN_SECONDS` and `HOUR_IN_SECONDS` constants instead of doing the time calculation
* Add backwards compatibility for `*_IN_SECONDS` constants added to WP 3.5
* Update plugin framework to 035
* Discontinue use of explicit pass-by-reference for objects
* Regenerate .pot
* Re-license as GPLv2 or later (from X11)
* Add 'License' and 'License URI' header tags to readme.txt and plugin file
* Note compatibility through WP 3.5+
* Update copyright date (2013)
* Remove ending PHP close tag
* Create repo's WP.org assets directory
* Move screenshots into repo's assets directory

## 1.2
* Add setting `remember_me_forever` to allow user to forego having to make up a large number
* Set a max expiration of 100 years in the future to prevent error if user supplies a high enough number to exceed the year 9999
* Use pure JS instead of jQuery for checking checkbox
* Hook into `login_footer` action to output JS
* Change hooking of `login_head` to output CSS rather than calling `login_head()`
* Remove `login_head()`
* Allow setting minimum duration of 1 hour (as was documented)
* Remove support for global `$c2c_remember_me_controls` variable
* Update plugin framework to 031
* Note compatibility through WP 3.3+
* Drop compatibility with versions of WP older than 3.1
* Create 'lang' subdirectory and move .pot file into it
* Regenerate .pot
* Update screenshot
* Add screenshots 2 and 3
* Add more description, FAQ question
* Add link to plugin directory page to readme.txt
* Update copyright date (2012)

## 1.1
* Fix bug with missing remember_me_duration setting conversion from hours to seconds
* Update plugin framework to version v023
* Save a static version of itself in class variable $instance
* Deprecate use of global variable `$c2c_remember_me_controls` to store instance
* Fix to properly register activation and uninstall hooks
* Add `__construct()`, `activation()`, `uninstall()`
* Explicitly declare all class functions public
* Note compatibility through WP 3.2+
* Drop compatibility with versions of WP older than 3.0
* Minor code formatting changes (spacing)
* Minor readme.txt formatting changes
* Fix plugin homepage and author links in description in readme.txt
* Update copyright date (2011)

## 1.0.1
* Fix bug where having "Remember Me" checked but having no remember me duration configured resulted in login error
* Fix bug where incorrect number of arguments were requested from the `auth_cookie_expiration` action

## 1.0
* Initial release
