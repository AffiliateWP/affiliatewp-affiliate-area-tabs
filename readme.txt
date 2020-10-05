=== AffiliateWP - Affiliate Area Tabs ===
Contributors: sumobi, mordauk, ramiabraham, drewapicture
Tags: AffiliateWP, affiliate, affiliates, Pippin Williamson, Andrew Munro, mordauk, pippinsplugins, sumobi, ecommerce, e-commerce, e commerce, selling, membership, referrals, marketing, affiliate marketing
Requires at least: 3.9
Tested up to: 5.5
Requires PHP: 5.3
Stable tag: 1.2.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add and reorder tabs in AffiliateWP's Affiliate Area

== Description ==

> This plugin requires [AffiliateWP](https://affiliatewp.com/ "AffiliateWP") in order to function.

Affiliate Area Tabs allows you to add custom tabs to AffiliateWP's Affiliate Area. Each tab is linked to a WordPress page making it exceptionally easy to add and manage the tab's content. Pages assigned to a tab are only viewable to affiliates.

Affiliate Area Tabs allows you to:

1. Add new custom tabs to your Affiliate Area
2. Reorder any tab via drag and drop
3. Hide tabs from appearing on the front-end Affiliate Area
4. Manage tabs added by other AffiliateWP add-ons (Direct Link Tracking, Show Affiliate Coupons, Order Details For Affiliates)

Here are just some of the things you could do:

1. Add a "Request Payout" tab and then insert a form. Your affiliates can now request that they be paid at any time.
2. Add a "Contact" tab and insert a contact form. Your affiliates can now contact you from the Affiliate Area.
3. Add a "FAQs" tab and insert some common questions and answers for your affiliates.
4. Add an "Important Dates" tab and list some special dates and promotions for your affiliates to remember.
5. Add a "URLs" tab and list some special URLs (landing pages or product pages) for your affiliates. You can even show their own referral URL using AffiliateWP's [affiliate_referral_url] shortcode.
6. Add a "Leaderboard" tab and insert an affiliate leaderboard, using the [AffiliateWP Leaderboard](https://wordpress.org/plugins/affiliatewp-leaderboard/ "AffiliateWP Leaderboard") plugin.
7. Add a "News" tab and share important news with your affiliates (or link to other blog posts).
8. Add an “Affiliate Brand Assets” or “Downloadables” tab, and add documents or images from your WordPress Media Library that don’t suit the existing Creatives tab.

**What is AffiliateWP?**

[AffiliateWP](https://affiliatewp.com/ "AffiliateWP") provides a complete affiliate management system for your WordPress website that seamlessly integrates with all major WordPress e-commerce and membership platforms. It aims to provide everything you need in a simple, clean, easy to use system that you will love to use.

== Installation ==

1. Unpack the entire contents of this plugin zip file into your `wp-content/plugins/` folder locally
1. Upload to your site
1. Navigate to `wp-admin/plugins.php` on your site (your WP Admin plugin page)
1. Activate this plugin
1. Go to Affiliates &rarr; Settings &rarr; Affiliate Area Tabs to configure the tabs (or removing the existing ones)
1. View the new tabs by visiting the Affiliate Area page on the front-end of your website. This is the page defined as the "Affiliate Area" at Affiliates &rarr; Settings &rarr; General

OR you can just install it with WordPress by going to Plugins >> Add New >> and type this plugin's name

== Screenshots ==

1. Adding new tabs from the admin
2. Viewing the custom tabs on the front-end website

== Changelog ==

= 1.2.9 =
* Fix: Unable to hide Coupons tab with AffiliateWP v2.6
* Fix: Custom tabs not displaying dynamic blocks

= 1.2.8 =
* Fix: The "Lifetime Customers" tab title (added by the Lifetime Commissions add-on) was unable to be translated due to an incorrect textdomain

= 1.2.7 =
* Fix: [affiliate_area_graphs] short code doesn't render in custom tabs

= 1.2.6 =
* Fix: Conflict with Thrive Architect Page Builder

= 1.2.5 =
* Tweak: Provides a notice for the "Lifetime Customers" tab, new in Lifetime Commissions v1.3

= 1.2.4 =
* Fix: Tabs not saving correctly due to previous update

= 1.2.3 =
* Fix: A custom tab with no page assigned could potentially cause an issue when loading the Affiliate Area

= 1.2.2 =
* Fix: Navigating between tabs in the Affiliate Area could produce a 503 error on some servers

= 1.2.1 =
* Fix: Settings screen not loading CSS and JS when some site languages were used
* Tweak: If the site language is changed, re-saving the settings now retranslates tab titles (if the translation file is available)

= 1.2 =
* New: Improved admin UI
* New: Drag and drop tab reordering. Requires AffiliateWP version 2.1.7 or newer
* New: Improved integration with other add-ons that add tabs to the Affiliate Area

= 1.1.6 =
* Tweak: Compatibility updates for AffiliateWP v2.1.7

= 1.1.5 =
* Fix: A potential redirect could occur for a page not set as a custom tab

= 1.1.4 =
* New: Added the "payouts" tab introduced in AffiliateWP v1.9

= 1.1.3 =
* Fix: The last custom tab could be removed resulting in an issue when saved
* Fix: Custom tabs were sometimes not added in the correct order
* Fix: Duplicate content when 2 identical tabs were set
* Fix: Issue when custom tabs had special characters included
* Tweak: Add-on now requires PHP 5.3 or newer

= 1.1.2 =
* Fix: Fatal error that could occur in some instances
* Fix: Main Affiliate Area should not be able to be selected as a tab
* Fix: Tabs do not save if no title is entered
* Fix: Undefined index PHP Notice
* Tweak: Improved validation during save routine

= 1.1.1 =
* Fix: array_key_exists() PHP Warning
* Fix: Prevent removal of all affiliate area tabs
* Tweak: Now uses AffiliateWP's version constant to check for the existence of v1.8 or newer
* Tweak: Added a note to the admin screen about how tab pages are shown to non-affiliates.

= 1.1 =
* New: Existing Affiliate Area tabs can now be removed! Requires AffiliateWP v1.8 or newer.
* Fix: 2x PHP notices when no custom tabs were set.

= 1.0.2 =
* Fix: Custom tab content shown at bottom of Affiliate URLs tab

= 1.0.1 =
* Fix: Nesting PHP error that could occur if a tab's content is set to the same page as the one being displayed on the front-end.
* Fix: Prevent non-affiliates from accessing a page that is set as a tab
* Tweak: Renamed "Tab Page" to "Tab Content"
* Tweak: Removed the Affiliate Area page (if set from Affiliates &rarr; Settings &rarr; General ) from the select menu since this should never be selected
* Tweak: Added descriptions to each field to better explain the UI
* Tweak: widened the column that contains the select menu

= 1.0.0 =
* Initial release
