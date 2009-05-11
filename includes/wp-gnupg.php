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

 /**
 * Definitions get defined below. This are used to get some directory
 * namespace for the WordPress and WP-WebTrip installation.
 */

$cryptoshell = gnupg_init()
	or die( "Unable to initialise GnuPG." );;

/**
 * Functions are defined here. They are generic WP-WebTrip specific functions.
 */
    
function get_gpg_signature () {
	$content = file_get_contents( 'http://svn.wp-plugins.org/web-tripwire/trunk/central-signatures.txt.asc' )
		or die( "Unable to obtain GnuPG signature from central repository." );

	return $content;
}

function get_gpg_plaintext () {
	$content = file_get_contents( 'http://svn.wp-plugins.org/web-tripwire/trunk/central-signatures.txt' )
		or die( "Unable to obtain signature updates from central repository." );

	return $content;
}

function verify_signing_key () {
	global $cryptoshell;

	$info = gnupg_keyinfo( $cryptoshell, '1C1DC95C' );
	if ( !$info ) {	// Looks like my key's not here!
		$keydata = file_get_contents( plugins_url( 'web-tripwire/1C1DC95C.gpg') )
			or die( "Failed to load public key file." );

$cryptoshell = gnupg_init();
		$info = gnupg_import( $cryptoshell, $keydata )
			or die ( "Unable to import public key. Key ID = 1C1DC95C" );
		var_dump( $info );
	}	
}

function verify_gpg_signature ( $plaintext, $signature ) {
	global $cryptoshell;
	
	verify_signing_key();	
	
	$info = gnupg_verify( $cryptoshell, $plaintext, $signature )
		or die( "Unable to perform gnupg_verify()." );
	return $info;
}

?>
