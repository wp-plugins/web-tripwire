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

<h3>System Information</h3>

<?php
	if( function_exists( 'gnupg_init' ) ) { 
	?><p><strong>gnupg_init()</strong> found, centralised updating of signatures is support.</p> <?php
	} else {
	?><p><strong>gnupg_init()</strong> not found, unable to support centralised updates of signatures. This doesn't 
prevent you from using the plugin, but will prevent you from subscribing to signature updates. You can assess the 
signature updates manually by reviewing <a href="http://svn.wp-plugins.org/web-tripwire/trunk/central-signatures.txt">this file</a>.</p> <?php
	} ?>
		
</div>