<?php
/** 
 * Copyright 2009  Nathan L. Reynolds  (email : yibble@yibble.org)
 * <p>
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * <p>
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * <p>
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

?>

<div class="wrap">
<h2>Web Tripwire Plugin Overview</h2>
<p>Welcome to the WordPress Web Tripwire Plugin. This plugin is based upon public code and research released by the 
<a href="http://www.cs.washington.edu/research/security/web-tripwire.html" />University of Washington</a>. I am in no way 
affiliated with that work, or the University, but the link is provided for traceability, research and appropriate kudos.</p>

<h3>What is a Web Tripwire?</h3>
<p>A Web Tripwire is a mechanism which compares the web server’s view of a web-site, with the client’s view of the 
web-site, if the two are different, then the page has possibly been altered in transit. The alteration may have occurred 
at any point in the transit of the page, sometimes with the user's consent, sometimes without. Web Tripwires should not 
be thought of as a security control, but as a feedback mechanism to assist users in making informed choices about their 
Internet connectivity and their security process.</p>

<p>More information regarding this plugin is available from 
<a href="http://blog.yibble.org/webtripwire/" />http://blog.yibble.org/webtripwire/</a></p>

<h3>What does this plugin do?</h3>
<p>This plugin will apply additional stress to your WordPress blog's resources, as additional transfers, and database 
queries are made. You should be aware of the potential additional load this will place on your hosting resources. To 
assist you in making a decision on whether to use this plugin, or fine tune some of the options. Here is a brief run 
down of how a web tripwire functions:</p>

<ul>
	<li>&nbsp;1. The client request the object. The server responds, and embeds a local view of the object in a JavaScript 
	component.</li>
	<li>&nbsp;2. The client executes the JavaScript, and requests the object once more. Both the embdedded server view, and 
	client view are compared for differences.</li>
	<li>&nbsp;3. If differences are found the modified version is sent back to a Notifier component on the web server.</li>
	<li>&nbsp;4. The Notifier then performs logging of differences, and performs regex comparisons against the client view, 
	using a signature database in order to attempt to determine the cause of the alteration.</li>
	<li>&nbsp;5. If the Notifier determines the client should be alerted, the response triggers the client JavaScript to 
	notify the user, and present a summary report.</li>
</ul>

<p>As you can see, a few more requests are needed for each page served. In order to lower utilisation the plugin can 
cache objects in the WordPress database.</p> 
</div>