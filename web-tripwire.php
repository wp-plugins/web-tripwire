<?php
/*
Plugin Name: Web Tripwire
Plugin URI: http://blog.yibble.org/webtripwire/
Description: Detect in-flight alterations made to the served pages between server and client. Allowing you to inform your users if their World-Wide-Web traffic is being modified by ISPs, ETC.
Version: 0.1.0
Author: Nathan L. Reynolds
Author URI: http://blog.yibble.org/
*/

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
if( class_exists( 'gnupg' ) )
	require_once ( 'includes/wp-gnupg.php');
else {
	function get_gpg_clearsign () { }
	function verify_gpg_signature ( $clearsign ) {}
}

/**
 * Variables get defined below:
 */
 
$webtrip_db_version = "0.1";

/**
 * Functions specific to the handling of the WordPress API get declared below:
 */

/**
 * webtrip_install() is called when WordPress 'activates' the plug-in. This function is used to
 * set-up the database environment.
 */

function webtrip_install () {
  	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

   global $wpdb, $webtrip_db_version;

/**
 * Check for the prefix.wtsignatures table in the WordPress database, and create and populate it
 * if it doesn't exist.
 * <p>
 * This table is used to store regex expressions, and notification messages which is matched to
 * the HTML that the client received.
 */

   $signature_table_name = $wpdb->prefix . "wtsignatures";
   if($wpdb->get_var ( "show tables like '" . $signature_table_name . "'" ) != $signature_table_name ) {

		$sql = "CREATE TABLE " . $signature_table_name . " (
		`id` int(11) NOT NULL auto_increment,
  		`detect` tinytext,
  		`regex` tinytext,
  		`notify` int(11) default NULL,
  		`message` text,
  		UNIQUE KEY  (`id`)
		);";

  		dbDelta( $sql );

/**
 * Here we populate the database with some common initial signatures.
 */

   	$insert = "INSERT INTO " . $signature_table_name . " (detect, regex, notify, message) " .
   	          "VALUES ('NebuAd', 'faireagle', '1', 'NebuAd, a company that " .
  		          "contracts with ISPs to inject advertisements')";

  		$results = $wpdb->query( $insert );
 
   	$insert = "INSERT INTO " . $signature_table_name . " (detect, regex, notify, message) " .
   	          "VALUES ('Ad Muncher', 'Begin Ad Muncher', '1', 'Ad Muncher, a " .
   	          "program designed to block ads.  You can test this by disabling " .
   	          "Ad Muncher and revisiting the page, to see if this message goes " .
   	 			 "away. If so, Ad Muncher is the cause, and you can safely re-enable " .
   	 			 "it.')";

  		$results = $wpdb->query( $insert );

 		$insert = "INSERT INTO " . $signature_table_name . " (detect, regex, notify, message) " .
  		          "VALUES ('Ad Muncher', 'Begin Ad Muncher.* Original URL', '1', '<b>" .
       		    "Warning:</b> This version of Ad Muncher is vulnerable to cross-site " .
            	 "scripting attacks.  Be sure to upgrade to Ad Muncher v4.71 or newer " .
					 "as soon as possible.')";

  		$results = $wpdb->query( $insert );
  		
 		$insert = "INSERT INTO " . $signature_table_name . " (detect, regex, notify, message) " .
  		          "VALUES ('WordPress User Logged In #1', 'post-edit-link', '0', '" .
       		    "The client is logged in as a WordPress user, or has rights to edit " .
       		    "the post, or view the <em>edit post</em> link. This signature typically " .
       		    "surpresses this error message, as it\'s normal behaviour for the weblog.')";

  		$results = $wpdb->query( $insert );
	}

/**
 * Check for the prefix.wtunknown table in the WordPress database, and create and populate it if
 * it doesn't exist.
 * <p>
 * This table is used to store log entries for differences which didn't trigger a known
 * signature. This way, they can be reviewed.
 */

   $unknown_table_name = $wpdb->prefix . "wtunknown";
   if($wpdb->get_var( "show tables like '" . $unknown_table_name . "'" ) != $unknown_table_name ) {

		$sql = "CREATE TABLE " . $unknown_table_name . " (
		`id` int(11) NOT NULL auto_increment,
		`url` text,
		`timestamp` int(11),
		`user_agent` text,
  		`server_html` text,
  		`client_html` text,
  		`small_diff` text,
  		`full_diff` text,
  		`has_viewed` tinyint(4) default '0', 
  		UNIQUE KEY  (`id`)
		);";

  		dbDelta( $sql );
   }

/**
 * Check for the prefix.wtcache table in the WordPress database, and create and populate
 * it if it doesn't exist.
 * <p>
 * This table is used to store log entries for differences which didn't trigger a known
 * signature. This way, they can be reviewed.
 */

   $cache_table_name = $wpdb->prefix . "wtcache";
   if($wpdb->get_var( "show tables like '" . $cache_table_name . "'" ) != $cache_table_name ) {

		$sql = "CREATE TABLE " . $cache_table_name . " (
		`id` int(11) NOT NULL auto_increment,
		`url` text,
		`timestamp` int(11),
  		`server_html` text,
  		UNIQUE KEY  (`id`)
		);";

  		dbDelta( $sql );
   }

/**
 * Add WordPress options, which we use to track configuration settings.
 */

   add_option( "webtrip_db_version" , $webtrip_db_version );	// Store the database revision.
   add_option( "trip_notify" , '1' );									// Client notification option.
   add_option( "trip_logging" , '1' );									// Server-side logging option.
   add_option( "trip_log_detail" , '0' );								// Server-side logging detail.
   add_option( "trip_cache" , '2' );									// Caching option.
   add_option( "trip_cache_expire" , '60');							// Expiry time.
   add_option( "trip_items_per_page" , '5');							// Items per page -- clearly!
}

/**
 * web_tripwire_menu() is called by WordPress, and adds an option page
 * hook for the plug-in.
 */

function web_tripwire_menu() {
	global $wpdb;

	$results =& $wpdb->get_results("SELECT COUNT(*) as `count` FROM `" . 
		$wpdb->prefix."wtunknown` WHERE has_viewed = 0");
	$events = $results[0]->count;

	add_menu_page( 'Web Tripwire Plugin Overview', 'Web Tripwire', 8, __FILE__, 'web_tripwire_overview',
		plugins_url( 'web-tripwire/images/icon16.png' ) );
	add_submenu_page( __FILE__, 'Web Tripwire Plugin Overview', 'Overview', 8, __FILE__, 'web_tripwire_overview' );
   add_submenu_page( __FILE__, 'Web Tripwire Plugin Change Log', 'Change Log', 8, 'changes', 'web_tripwire_changes' );
   add_submenu_page( __FILE__, 'Web Tripwire Plugin Options', 'Options', 8, 'options', 'web_tripwire_options' );
   add_submenu_page( __FILE__, 'Web Tripwire Plugin Log', 'Log (' . $events . ')', 8, 'log', 'web_tripwire_log' );
   add_submenu_page( __FILE__, 'Web Tripwire Plugin Signatures', 'Signatures', 8, 'signatures', 'web_tripwire_signatures' );
   add_submenu_page( __FILE__, 'Web Tripwire Plugin Support Forums', 'Support Forums', 8, 'forums', 'web_tripwire_forums' );
}

function web_tripwire_overview() {
	include(dirname(__FILE__) . '/pages/webtrip-overview.inc.php');    // Now display the overview screen
	return;
}

function web_tripwire_changes() {
	include(dirname(__FILE__) . '/pages/webtrip-changes.inc.php');    // Now display the overview screen
	return;
}

function web_tripwire_options() {
/** See if the user has posted us some information
  * If they did, this hidden field will be set to 'Y'
  */
  
	if( $_POST[ 'trip_submit_hidden' ] == 'Y' ) {
		// Save the posted value in the database
		update_option( 'trip_notify', $_POST[ 'trip_notify' ] );
		update_option( 'trip_logging', $_POST[ 'trip_logging' ] );
		update_option( 'trip_log_detail', $_POST[ 'trip_log_detail' ] );
		update_option( 'trip_cache', $_POST[ 'trip_cache' ] );
		update_option( 'trip_cache_expire', $_POST[ 'trip_cache_expire' ] );
		update_option( 'trip_items_per_page', $_POST[ 'trip_items_per_page' ] );

?> <div class="updated"><p><strong>Options saved.</strong></p></div> <?php

   }
	include(dirname(__FILE__) . '/pages/webtrip-options.inc.php');    // Now display the options editing screen
	return;
}

function web_tripwire_log() {
	global $wpdb;
	$items_per_page = get_option('trip_items_per_page');
	$page = $_GET['paged'] ? $_GET['paged'] : 1;
	
	switch ($_REQUEST['op']) {
		case 'clear':
			$wpdb->query("DELETE FROM `".$wpdb->prefix."wtunknown`");
			
			?><div class="updated"><p><strong>Log cleared.</strong></p></div><?php
			
			break;
		case 'delete':
			if (!($ids = $_REQUEST['id'])) {
				break;
			}
			if (!is_array($ids)) {
				$ids = array($ids);
			}
			foreach ($ids as $id) {
				$result =& $wpdb->get_results($wpdb->prepare("SELECT * FROM `".$wpdb->prefix."wtunknown` WHERE `id` = %d", $id));
				if (!$result) {
					$errors[] = 'The selected entries were not found.';
				} else {
					$result =& $result[0];
					$wpdb->query($wpdb->prepare("DELETE FROM `".$wpdb->prefix."wtunknown` WHERE `id` = %d", $id));
				}
				$message = 'The selected entries were deleted.';
			}
			
			?><div class="updated"><p><strong><?php echo $message; ?></strong></p></div><?php
			
			break;			
	}
	
	$start = ($page-1)*$items_per_page;
	$end = $page*$items_per_page;

	$wpdb->query("UPDATE `".$wpdb->prefix."wtunknown` SET has_viewed = 1");

	$results =& $wpdb->get_results("SELECT COUNT(*) as `count` FROM `".$wpdb->prefix."wtunknown`");
	$number_of_pages = ceil($results[0]->count/$items_per_page);
	$results =& $wpdb->get_results($wpdb->prepare("SELECT * FROM `".$wpdb->prefix."wtunknown` ORDER BY `timestamp`" . 
		" DESC LIMIT %d, %d", $start, $end));

	include(dirname(__FILE__) . '/pages/webtrip-log.inc.php');    // Now display the log viewing screen

   return;
}

function web_tripwire_signatures() {
	global $wpdb;
	$items_per_page = get_option('trip_items_per_page');
	$page = $_GET['paged'] ? $_GET['paged'] : 1;

	switch ($_REQUEST['op']) {
		case 'delete':
			if (!($ids = $_REQUEST['id'])) {
				break;
			}
			if (!is_array($ids)) {
				$ids = array($ids);
			}
			foreach ($ids as $id) {
				$result =& $wpdb->get_results($wpdb->prepare("SELECT * FROM `".$wpdb->prefix."wtsignatures` WHERE `id` = %d", $id));
				if (!$result) {
					$errors[] = 'The selected signatures were not found.';
				} else {
					$result =& $result[0];
					$wpdb->query($wpdb->prepare("DELETE FROM `".$wpdb->prefix."wtsignatures` WHERE `id` = %d", $id));
				}
					$message = 'The selected signatures were deleted.';
			}
			
			?><div class="updated"><p><strong><?php echo $message; ?></strong></p></div><?php
			
			break;
		case 'add':
			if ( !empty( $_REQUEST['detect'] ) && !empty( $_REQUEST['regex'] ) && !empty( $_REQUEST['message'] ) ) {
				$wpdb->query($wpdb->prepare("INSERT INTO `".$wpdb->prefix."wtsignatures` (detect, regex, notify, message) " .
					"VALUES ('" . addslashes( $_REQUEST['detect'] ) . "', '" . addslashes( $_REQUEST['regex'] ) . "', '" .
					addslashes( $_REQUEST['notify'] ) . "', '" . addslashes( $_REQUEST['message'] ) . "')"));
				$message = 'The signature was added.';
			} else {
				$message = 'All fields must be completed.';
			}
			
			?><div class="updated"><p><strong><?php echo $message; ?></strong></p></div><?php
			
			break;
		case 'update':
			if( class_exists( 'gnupg' ) ) {
				//$info = verify_gpg_signature ( get_gpg_plaintext (), get_gpg_signature () );
				$info = verify_gpg_signature ( get_gpg_clearsign () );
				if ( $info[0]['fingerprint'] !== 'A20087E339CE514446E6AFEEC716E6331C1DC95C' ) {
					$message = 'Failed to verify signature!';
				} else {
					$message = 'Successful verification of signature. Fingerprint = ' . $info[0]['fingerprint'];
				}
			} else {
				$message = 'gnupg_init() is not available to PHP.';
			}

			?><div class="updated"><p><strong><?php echo $message; ?></strong></p></div><?php
			
			break;			
	}


	$start = ($page-1)*$items_per_page;
	$end = $page*$items_per_page;

	$results =& $wpdb->get_results("SELECT COUNT(*) as `count` FROM `".$wpdb->prefix."wtsignatures`");
	$number_of_pages = ceil($results[0]->count/$items_per_page);
	$results =& $wpdb->get_results($wpdb->prepare("SELECT * FROM `".$wpdb->prefix."wtsignatures` ORDER BY `id`" .
		" DESC LIMIT %d, %d", $start, $end));

	include(dirname(__FILE__) . '/pages/webtrip-signatures.inc.php');    // Now display the options editing screen

	return;
}

function web_tripwire_forums() {
	include(dirname(__FILE__) . '/pages/webtrip-forums.inc.php');    // Now display the overview screen
	return;
}

/**
 * webtrip_js() is added as a WordPress hook, and inserts code into the head
 * of each weblog page (not Dashboard pages). This basically gets our
 * JavaScript run on each page view.
 */
 
function webtrip_js() {
	global $plugin_directory;

echo <<<END
\n\n<!-- web-tripwire begin -->\n
END;

	echo "<script type=\"text/javascript\" src=\"" . WP_PLUGIN_URL . "/web-tripwire/webtrip-javascript.php?target=" .
		rawurlencode(get_bloginfo('url').$_SERVER['REQUEST_URI'])."\"></script>";

echo <<<END
\n<!-- web-tripwire end -->\n\n
END;
}

/**
 * Get the plug-in installed into the WordPress installation.
 */
 
register_activation_hook(__FILE__,'webtrip_install');

/**
 * Register our header components, and the administration menu.
 */
 
add_action('wp_head', 'webtrip_js');
add_action('admin_menu', 'web_tripwire_menu');
?>
