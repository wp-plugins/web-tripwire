=== Plugin Name ===
Contributors: yibble
Donate link: http://blog.yibble.org/webtripwire/
Tags: tripwire, security, inject, advertisments
Requires at least: 2.7.1
Tested up to: 2.7.1
Stable tag: 0.0.7

Detects in-flight modifications to the HTML, notifies users, and weblog administrators.

== Description ==

A Web Tripwire is a mechanism which compares the web server’s view of a web-site, with the client’s view of the web-site,
if the two are different, then the page has possibly been altered in transit. The alteration may have occurred at any
point in the transit of the page, sometimes with the user’s consent, sometimes without. Web Tripwires should not be thought
of as a security control, but as a feedback mechanism to assist users in making informed choices about their Internet
connectivity and their security process.

This plugin will apply additional stress to your WordPress blog’s resources, as additional transfers, and database queries
are made. You should be aware of the potential additional load this will place on your hosting resources. To assist you in
making a decision on whether to use this plugin, or fine tune some of the options. Here is a brief run down of how a web
tripwire functions:

1. The client request the object. The server responds, and embeds a local view of the object in a JavaScript component.
1. The client executes the JavaScript, and requests the object once more. Both the embdedded server view, and client view are compared for differences.
1. If differences are found the modified version is sent back to a Notifier component on the web server.
1. The Notifier then performs logging of differences, and performs regex comparisons against the client view, using a signature database in order to attempt to determine the cause of the alteration.
1. If the Notifier determines the client should be alerted, the response triggers the client JavaScript to notify the user, and present a summary report.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload extract the archive to the `/wp-content/plugins/` directory, which should create a '/wp-content/plugins/web-tripwire/' directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Adjust the plugin's settings in the 'Web Tripwire' 'Options' menu.

== Frequently Asked Questions ==

= Where can I get more information? =

You can visit the [Web Tripwire Plugin](http://blog.yibble.org/webtripwire/) home page.

= Where can I get support, make suggestions, ETC.? =

You can visit the [Support Forums](http://forums.yibble.org/viewforum.php?f=1).
