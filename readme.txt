=== Swift Control (Now Better Admin Bar) ===
Contributors: davidvongries
Tags: Quick Edit, Admin Bar, Replace Admin Bar, Frontend Access, Swift Edit, Swift
Requires at least: 4.0
Tested up to: 5.9
Stable tag: 2.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==
**Swift Control is now Better Admin Bar. Please install [Better Admin Bar](https://wordpress.org/plugins/better-admin-bar/) instead.**

If you have Swift Control installed on your site, please make use of the 1-click migration that you're prompted with on your WordPress admin dashboard immediately.

**This plugin will be removed from the WordPress directory soon.**

== Installation ==
1. Download the swift-control.zip file to your computer.
1. Unzip the file.
1. Upload the swift-control folder to your `/wp-content/plugins/` directory.
1. Activate the plugin through the *Plugins* widget in WordPress.

== Frequently Asked Questions ==
= Where can I define what controls are shown on the front end? =
Navigate to Settings > Swift Control to change the order the controls are displayed in and choose which ones you would like to show.

== Screenshots ==
1. Swift Control
2. Swift Control Admin Settings Page

== Changelog ==

= 2.0.1 March 03, 2022 =
* **Caution:** Swift Control is now Better Admin Bar. Please use the one-click migration to upgrade from Swift Control to Better Admin Bar on your WordPress admin.

= 2.0 April 19, 2022 =
* Show the migration notice everywhere

= 1.5.4 March 01, 2022 =
* Migrate to Better Admin Bar

= 1.5.2 May 25, 2021 =
* Fixed: FontAwesome icons don't render properly on Swift Controls settings page if other 3rd party plugins load an older version of FA across the entire WordPress admin

= 1.5.1 April 23, 2021 =
* Fixed: Discount notice was not dismissable

= 1.5 April 22, 2021 =
* Tweak: Refactored icon picker
* Admin notice to promote Swift Control PRO with a pretty decent discount :)

= 1.4.3 August 17, 2020 =
* Fixed: FontAwesome doesn't load properly on frontend in some cases

= 1.4.2 August 14, 2020 =
* Fixed: Color picker issue in WordPress 5.5
* Fixed: Swift Control was able to be thrown outside of window
* New: Added "has-swift-control" body class

= 1.4.1 March 05, 2020 =
* New: filter (swift_control_frontend_display) to hide/show panel from certain pages/archives
* Tweak: Properly prefix classes to prevent plugin & theme incompatibilities
* Fixed: Defined jQuery as a dependency when enqueuing scripts to prevent JS errors

= 1.4 February 04, 2020 =
* New: Hide admin bar based on user roles
* New: Drag & Drop Swift Control panel
* New: Option to expand Swift Control panel by default
* New: Option to remove indicator arrow

= 1.3 January 28, 2020 =
* New: Export/Import functionality
* New: RTL support
* New: Support for Page Builder Cloud

= 1.2.1 January 08, 2020 =
* Tweak: Second click on the Swift Control Icon will now close the Swift Control panel
* Tweak: Changed capabilities so that Swift Control is now also visible to Editors & not only Administrators
* Fixed: In editing mode, hitting ESC doesn't close icon picker
* Fixed: Conflicts if PRO version is active

= 1.2 January 08, 2020 =
* New: Swift Control is now collapsed by default
* Tweak: Hide Swift Control from WordPress customizer
* Tweak: Hide Swift Control while inside a Page Builder
* Fixed: Customize widget now respects current URL

= 1.1 December 27, 2019 =
* New: Color settings
* New: Setting to remove Admin Bar from frontend
* New: Setting to not include FontAwesome 5 on frontend
* New: Setting to remove data on uninstall
* New: Plugins & Themes widget
* New: Double-click to enter edit mode
* Fix: Swift Control is being shown to subscribers & other user roles below admin

= 1.0 December 23, 2019 =
* Initial Release