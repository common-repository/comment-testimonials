=== EZ Anti-Spam Comments and Testimonials Widget ===
Plugin URI: http://wordpress.ieonly.com/category/my-plugins/comment-testimonials/
Author: Eli Scheetz
Author URI: http://wordpress.ieonly.com/category/my-plugins/
Contributors: scheeeli
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=8VWNB5QEJ55TJ
Tags: Anti-Spam, testimonials, widget, shortcode, plugin, links, sidebar, sort, random, move, comments, karma, filter, spam
Stable tag: 2.16.41
Version: 2.16.41
Requires at least: 2.6
Tested up to: 4.6.2

A simple yet effective Spam Filter. A Widget and Shortcode to display Comments with Good Karma as Testimonials. Plus the ability to Move comments and modify Karma.

== Description ==

Automatic Spam Filter uses a simple yet effective JavaScript method to reject comments by robots. 

To display testimonials just place the Widget on your sidebar or insert the Shortcode into a Page or Post to display any comments with Good Karma. You can limit how many are displayed and reverse the sort order or even randomize what comments to show.

The following example Shortcode would display 8 Comments with the Karma Value of 100 under the heading Client Testimonials:
[TESTIMONIALS title="Client Testimonials" karma="100" number="8" /]

Last Updated October 17th

== Installation ==

1. Download and unzip the plugin into your WordPress plugins directory (usually `/wp-content/plugins/`).
1. Activate the plugin through the 'Plugins' menu in your WordPress Admin.
1. Place the widget on your sidebar through the 'Widgets' menu in your WordPress Admin.

== Frequently Asked Questions ==

= What do I do after I activate the Plugin? =

Go to the Widgets menu in the WordPress Admin and add the "Testimonials" Widget to your Sidebar or Footer Area.

= Why am I not seeing the Widget on my site after I add it to the sidebar? =

You might not have any comments matching the Karma value you selected.

= How do I use the shotcode? =

[TESTIMONIALS title="Random Testimonial" number="1" /]
This would display 1 random Comments with the Karma Value of 0 under the heading Random Testimonial

[TESTIMONIALS title="Client Testimonials" karma="100" number="8" order="DESC" /]
This would display your 8 most recent Comments with the Karma Value of 100 under the heading Client Testimonials

== Changelog ==

= 2.16.41 =
* Fix a PHP Warning about a Missing Argument.
* Added back the block for pingbacks and trackbacks that was removed in the last version.

= 2.16.16 =
* Added option to remove Author URL from the Comments Page in the admin.
* Changed keys to work better with comments on Pages as well as Posts.
* Removed duplicate testimonials when used more than on once on the same page.

= 2.15.42 =
* Added a block for pingbacks and trackbacks to the default automatic spam filter if default_ping_status is not "open".

= 2.15.34 =
* Added a default Double-Quote Gravatar.
* Improved the automatic spam filter.

= 2.15.33 =
* Upgraded Widget Class for compatibility with WordPress 4.3 changes.
* Added an automatic spam filter.

= 1.3.01.28 =
* Fixed "Missing argument" Warning in introduced in the last release.

= 1.3.01.09 =
* Added "Move" to the Comment menu so that a comment could be moved to another post or page.
* Added "Karma" to the Comment menu so you could manually set the karma to whatever you want.

= 1.2.11.02 =
* Added TESTIMONIALS Shortcode for use on pages and posts.

= 1.2.10.26 =
* First versions uploaded to WordPress.

== Upgrade Notice ==

= 2.16.41 =
Fix a PHP Warning about a Missing Argument, and added back the block for pingbacks and trackbacks.

= 2.16.16 =
Added option to remove Author URL, changed keys to work better with comments on Pages, and removed duplicate testimonials when used more than on once on the same page.

= 2.15.42 =
Added a block for pingbacks and trackbacks to the default automatic spam filter if default_ping_status is not "open".

= 2.15.34 =
Added a default Double-Quote Gravatar and improved the automatic spam filter.

= 2.15.33 =
Upgraded Widget Class for compatibility with WordPress 4.3 changes and added an automatic spam filter.

= 1.3.01.28 =
Fixed "Missing argument" Warning in introduced in the last release.

= 1.3.01.09 =
Added "Move" and "Karma" to the Comment menu so you could manually set the karma move the comments around.

= 1.2.11.02 =
Added TESTIMONIALS Shortcode for use on pages and posts.

= 1.2.10.26 =
First versions available through WordPress.

