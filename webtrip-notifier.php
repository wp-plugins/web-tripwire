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
 * Include files get defined below:
 */
require_once ( 'includes/wp-common.php');

/**
 * Variables get defined below:
 */

$signature_hit = 0;
$will_notify = 0;

/**
 * Load each signature, and compare against the returned HTML, in order
 * to identify the cause(s) of the modification(s).
 */

$results = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "wtunknown", ARRAY_A );

$likely_causes = array();

foreach ( $results as $row ) {
	if ( ( preg_match( "/" . $row['regex'] . "/", $_REQUEST['actualHTML'] ) ) ) {
		
		// Copy the signature data to a seperate array for the report.
		array_push( $likely_causes, $row );
	   
	   // Determine if notification precedence.
		$will_notify = $will_notify || $row['notify'];
		
		// Flag that we detected something we have a signature for.
		$signature_hit = 1;
	}
}

/**
 * Determine whether to create a log entry or not.
 * FIXME: Logging is not currently functioning.
 */

switch( get_option( 'trip_logging' ) ) {
	case '0':	// Disable all logging
		break;
	case '2':	// Enable logging of all
		log_modification( $_REQUEST['target'], $_REQUEST['actualHTML'] );
		break;
	default:	// Enable logging of unknowns
		if ( !$signature_hit ) {
			log_modification( $_REQUEST['target'], $_REQUEST['actualHTML'] );
		}
}

/**
 * Determine whether to send a client notification or not.
 */
 
switch( get_option( 'trip_notify' ) ) {
	case '0':	// Disable all notifications
		abort_notification();
		break;
	case '2':	// Enable urgent and unclassified [FIXME]
		if ( !$signature_hit )
			$will_notify = 1;
		else if (( $signature_hit ) && ( $will_notify == 0))
			abort_notification();
		break;
	case '3':	// Enable all.
		$will_notify = 1;
		break;			
	default: 	// Enable Only urgent modifications
		if ( $will_notify )
			continue;
		else
			abort_notification();
}

echo <<<END
<h1>Page Modification Detected</h1>
<p>We have detected that our web page was modified between leaving our server and arriving in your browser. There are 
many possible causes for such a modification, ranging from the use of personal firewalls to Internet Service Providers 
that inject advertisements.</p>
<p>
END;

/**
 * Work through the likely causes, and output them.
 */

if ( $signature_hit ) {
	echo "<ul>\n";
	foreach( $likely_causes as $likely_cause ) {	
		echo "<li>Detected \"" . $likely_cause['detect'] . "\", " . $likely_cause['message'] . "</li>\n";
	}
	echo "</ul>\n";
} else {
	echo "<p>We were unable to determine what altered the web page.</p>";
}	

echo <<<END
</p>
<p>For your reference, the actual HTML your browser received is shown below, with the modifications highlighted.</p>
END;
?>