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

$CONFIG['gnupg_home'] = '/var/www/.gnupg';

$gpg = new gnupg()
	or die( "Unable to initialise GnuPG." );

putenv("GNUPGHOME={$CONFIG['gnupg_home']}");


$gpg -> seterrormode(gnupg::ERROR_WARNING);

/**
 * Functions are defined here. They are generic WP-WebTrip specific functions.
 */
    
function get_gpg_clearsign () {
	$content = file_get_contents( 'http://svn.wp-plugins.org/web-tripwire/trunk/central-signatures.txt.asc' )
		or die( "Unable to obtain signature updates from central repository." );

	return $content;
}

function verify_signing_key () {
	global $gpg;

	$info = $gpg->keyinfo( "1C1DC95C" );

	if ( !$info ) {	// Looks like my key's not here!
	
$keydata = '
-----BEGIN PGP PUBLIC KEY BLOCK-----
Version: GnuPG v1.4.9 (GNU/Linux)

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
+/7gLUHs+GWDGxmJ4Hyf
=+01n
-----END PGP PUBLIC KEY BLOCK-----
';

		$info = $gpg->import( $keydata );
	}
}

function verify_gpg_signature ( $clearsign ) {
	verify_signing_key();	

	global $gpg;
	$plaintext = "";
	
	$info = $gpg->verify( $clearsign, FALSE, $plaintext );

	$info[0]['plaintext'] = $plaintext;
	//var_dump ($info);
	return $info;
}
?>
