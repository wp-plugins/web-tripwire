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
<h2><?php _e( 'Web Tripwire Plugin Overview', 'web-tripwire' ); ?></h2>
<p><?php _e( 'Welcome to the WordPress Web Tripwire Plugin. This plugin is based upon public code ' .
	'and research released by the %sUniversity of Washington%s. I am in no way affiliated with that ' .
	'work, or the University, but the link is provided for traceability, research and appropriate kudos.',
	'<a href=\"http://www.cs.washington.edu/research/security/web-tripwire.html\" />', '</a>',
	'web-tripwire' ); ?></p>

<h3><?php _e( 'What is a Web Tripwire?', 'web-tripwire' ); ?></h3>
<p><?php _e( 'A Web Tripwire is a mechanism which compares the web server’s view of a web-site, with the ' .
	'client’s view of the web-site, if the two are different, then the page has possibly been altered in ' .
	'transit. The alteration may have occurred at any point in the transit of the page, sometimes with the ' .
	'user\'s consent, sometimes without. Web Tripwires should not be thought of as a security control, but ' .
	'as a feedback mechanism to assist users in making informed choices about their Internet connectivity ' .
	'and their security process.', 'web-tripwire' ); ?></p>

<p><?php _e( 'More information regarding this plugin is available from', 'web-tripwire' ); ?> 
<a href="http://blog.yibble.org/webtripwire/" />http://blog.yibble.org/webtripwire/</a></p>

<h3><?php _e( 'System Information', 'web-tripwire' ); ?></h3>

<?php
	if( function_exists( 'gnupg_init' ) ) { 
	?><p><strong><?php _e( 'PECL GnuPG Module%s found. GnuPG-based cryptographic functionality is now ' .
		'accessible. This includes the ability to verify the integrity of the installation against the ' .
		'WordPress Subversion repository, and the ability to verify signature updates from the WordPress ' .
		'Subversion repository.', '</strong>', 'web-tripwire' ); ?></p>
<?php
		if( get_option( 'trip_gpg' ) ) { ?>
<form method="post" action="">
	<div class="tablenav">
		<div class="alignright">
	   	<button type="submit" name="op" value="verify" class="button-primary verify"><?php _e( 'Verify Installation',
	   		'web-tripwire' ); ?></button>
	   </div>
		<br class="clear" />
	</div>
</form>
<?php	} else { ?>
<p><?php _e( 'In order access the GnuPG-based cryptographic functionality, you will need to enable it in ' .
	'the %sOptions%s sub-menu.', '<strong>', '</strong>', 'web-tripwire' ); ?></p>
<?php } } else { ?>
<p><strong><?php _e( 'PECL GnuPG Module%s not found. Don\'t panic! Only some features will be inaccessible. ' .
	'You will not be able to cryptographically validate the integrity of your installation against the ' .
	'WordPress Subversion repository, and you will not be able to verify signature updates from the WordPress ' .
	'Subversion repository.', '</strong>', 'web-tripwire' ); ?></p>
<p><?php _e( 'This functionality is also very experimental. In fact, it\'s still undergoing a lot development ' .
	'to get it to a functional level. So you %sreally%s aren\'t missing out.', '<em>', '</em>', 'web-tripwire' ); ?></p>
<?php } ?>
</div>