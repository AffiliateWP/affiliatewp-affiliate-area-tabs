=== AffiliateWP - Affiliate Area Tabs ===
Contributors: sumobi, mordauk
Tags: AffiliateWP, affiliate, affiliates, Pippin Williamson, Andrew Munro, mordauk, pippinsplugins, sumobi, ecommerce, e-commerce, e commerce, selling, membership, referrals, marketing, affiliate marketing
Requires at least: 3.9
Tested up to: 4.6-alpha-37456
Stable tag: 1.1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add custom tabs to AffiliateWP's Affiliate Area

== Description ==

> This plugin requires [AffiliateWP](https://affiliatewp.com/ "AffiliateWP") in order to function.

Affiliate Area Tabs allows you to add custom tabs to AffiliateWP's Affiliate Area. Each tab is linked to a WordPress page making it exceptionally easy to add and manage the tab's content. Pages assigned to a tab are only viewable to affiliates. Since version 1.8 of AffiliateWP it's possible to remove any of the existing tabs in the Affiliate Area (Affiliate URLs, Statistics etc).

Here are just some of the things you could do:

1. Add a "Request Payout" tab and then insert a form. Your affiliates can now request that they be paid at any time.
2. Add a "Contact" tab and insert a contact form. Your affiliates can now contact you from the Affiliate Area
3. Add a "FAQs" tab and insert some common questions and answers for your affiliates
4. Add an "Important Dates" tab and list some special dates and promotions for your affiliates to remember
5. Add a "URLs" tab and list some special URLs (landing pages or product pages) for your affiliates. You can even show their own referral URL using AffiliateWP's [affiliate_referral_url] shortcode.
6. Add a "Leaderboard" tab and insert an affiliate leaderboard, using the [AffiliateWP Leaderboard](https://wordpress.org/plugins/affiliatewp-leaderboard/ "AffiliateWP Leaderboard") plugin
7. Add a "News" tab and share important news with your affiliates (or link to other blog posts)
8. Add an “Affiliate Brand Assets” or “Downloadables” tab, and add documents or images from your WordPress Media Library that don’t suit the existing Creatives tab.

**What is AffiliateWP?**

[AffiliateWP](http://affiliatewp.com/ "AffiliateWP") provides a complete affiliate management system for your WordPress website that seamlessly integrates with all major WordPress e-commerce and membership platforms. It aims to provide everything you need in a simple, clean, easy to use system that you will love to use.

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

= 1.1.1 =
* Fix: array_key_exists() PHP Warning
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
