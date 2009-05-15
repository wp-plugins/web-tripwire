=== Plugin Name ===
Contributors: yibble
Donate link: http://blog.yibble.org/webtripwire/
Tags: tripwire, security, inject, advertisments
Requires at least: 2.7.1
Tested up to: 2.7.1
Stable tag: 0.1.0

Detects in-flight modifications to the HTML, then notifies users, and weblog administrators.

== Description ==

A Web Tripwire is a mechanism which compares the web server’s view of a web-site, with the client’s view of the web-site,
if the two are different, then the page has possibly been altered in transit. The alteration may have occurred at any
point in the transit of the page, sometimes with the user’s consent, sometimes without. Web Tripwires should not be thought
of as a security control, but as a feedback mechanism to assist users in making informed choices about their Internet
connectivity and their security process.

This plugin will apply additional stress to your WordPress blog’s resources, as additional transfers, and database queries
are made. You should be aware of the potential additional load this will place on your hosting resources.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload extract the archive to the `/wp-content/plugins/` directory, which should create a `/wp-content/plugins/web-tripwire/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Adjust the plugin's settings in the 'Web Tripwire' 'Options' menu.

== Frequently Asked Questions ==

= Where can I get more information? =

You can visit the [Web Tripwire Plugin](http://blog.yibble.org/webtripwire/) home page.

= Where can I get support, make suggestions, ETC.? =

You can visit the [Support Forums](http://forums.yibble.org/viewforum.php?f=1).

== Screenshots ==

1. This is a screenshot of the Overview sub-menu.
2. The development Change Log, which is retrieved from the Subversion repository.
3. The Options sub-menu.
4. The Log sub-menu, which shows events of tripwires previously triggered.
5. The Signatures sub-menu, where tripwire signatures can be added.
6. A handy link to the Support Forums.
