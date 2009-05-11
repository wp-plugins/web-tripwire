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
 * Include files get defined below. This section handles getting the
 * WordPress configuration, and database handling functions.
 */

$root = dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) );
	if ( file_exists( $root . '/wp-load.php' ) ) {
		// WP 2.6
		require_once( $root . '/wp-load.php' );
	} else {
		// Before 2.6
		require_once( $root . '/wp-config.php' );
}
require_once( $root . '/wp-includes/wp-db.php' );



 /**
 * Definitions get defined below. This are used to get some directory
 * namespace for the WordPress and WP-WebTrip installation.
 */

if ( ! defined( 'WP_CONTENT_URL' ) )
      define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
      define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) )
      define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
      define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );

/**
 * Functions are defined here. They are generic WP-WebTrip specific functions.
 */
    
/**
 * wt_strip_url() is used to strip special characters from URLs.
 */

function wt_strip_url( $text )
{
	#### FUNCTION BY WWW.WEBUNE.COM AND WALLPAPERAMA.COM
	## PLEASE DO NOT REMOVE THIS.. THANK YOU

	$text = strtolower( $text );
	$code_entities_match = array( '&quot;' ,'!' ,'@' ,'#' ,'$' ,'%' ,'^' ,'&' ,'*' ,
		'(' ,')' ,'+' ,'{' ,'}' ,'|' ,':' ,'"' ,'<' ,'>' ,'?' ,'[' ,']' ,'' ,';' ,"'" ,
		',' ,'.' ,'_' ,'/' ,'*' ,'+' ,'~' ,'`' ,'=' ,' ' ,'---' ,'--','--' );
	$code_entities_replace = array( '' ,'-' ,'-' ,'' ,'' ,'' ,'-' ,'-' ,'' ,'' ,'' ,'' ,
		'' ,'' ,'' ,'-' ,'' ,'' ,'' ,'' ,'' ,'' ,'' ,'' ,'' ,'-' ,'' ,'-' ,'-' ,'' ,'' ,
		'' ,'' ,'' ,'-' ,'-' ,'-','-' );
	$text = str_replace( $code_entities_match, $code_entities_replace, $text );
	return $text;
}

/**
 * get_http_data() is used to grab the object at the specified URL.
 */

function get_html_data ( $referrer_url )
{
	$content = "";

	$html_stream = @fopen( $referrer_url, "r" )
		or abort_notification();
	while( !feof( $html_stream ) ) {
		$buffer = fread( $html_stream, 1024 )
			or abort_notification();
		$content .= $buffer;
	}
	fclose ( $html_stream )
		or abort_notification();

	$html_data_encoded = rawurlencode( $content );

	return $html_data_encoded;
}

/**
 * grab_cached_html request and read the URL we've been given, and implement
 * any of the selected cache mechanisms and options.
 */

function grab_cached_html ( $referrer_url ) {
	global $wpdb;

	//$expire = "100000";	// FIXME, this should be a configurable option!


/**
 * This validates that the URL requested in the JavaScript is actually a resource
 * located within the blog URL structure. If not, someone's trying to use us as
 * an anonymous proxy. So send them a HTTP/1.1 403 status code: 'Forbidden'.
 * See the IETF RFC 2616 (p. 65) for more details:
 * @link		http://www.ietf.org/rfc/rfc2616.txt?number=2616
 */

	$blog_url = get_bloginfo( 'url' );
	
	if ( strncmp( $blog_url, rawurldecode( $referrer_url ), strlen( $blog_url ) ) != 0 ) {
		header( 'HTTP/1.1 403 Forbidden' );
		exit( 1 );
	}
	if ( substr( rawurldecode( $referrer_url ), - 20, 20 ) == "webtrip-notifier.php" ) {
		header( 'HTTP/1.1 403 Forbidden' );
		exit( 1 );
	}	

	switch( get_option( 'trip_cache' ) ) {
		case '0':		// Caching has been disabled.
			$html_data_encoded = get_html_data( $referrer_url );
			break;
		case '2':		// Database caching.
			$cache_table_name = $wpdb->prefix . "wtcache";

			$query = "SELECT * FROM " . $cache_table_name . " WHERE url='" . $referrer_url . "'";
			
			if ( $results = $wpdb->get_row( $query, ARRAY_A ) ) {
				if ( $results['timestamp'] > ( time() - get_option( 'trip_cache_expire' ) ) ) {
					$html_data_encoded = $results['server_html'];
				} else {
					$html_data_encoded = get_html_data( $referrer_url );
					
					$query = "UPDATE " . $cache_table_name . " SET timestamp='" . time() . 
					"', server_html='" .	$html_data_encoded . "' WHERE url='" . $referrer_url . "'";
					
					$results = $wpdb->query( $query );
				}
			} else {
				$html_data_encoded = get_html_data( $referrer_url );
				
				$query = "INSERT INTO " . $cache_table_name . " (url, timestamp, server_html) " .
				" VALUES ('" . $referrer_url . "', '" . time() . "', '" .
				$html_data_encoded . "')";
				
				$results = $wpdb->query( $query );
			}
			break;
	}
	return $html_data_encoded;
}

/*
        Paul's Simple Diff Algorithm v 0.1
        (C) Paul Butler 2007 <http://www.paulbutler.org/>
        May be used and distributed under the zlib/libpng license.
       
        This code is intended for learning purposes; it was written with short
        code taking priority over performance. It could be used in a practical
        application, but there are a few ways it could be optimized.
       
        Given two arrays, the function diff will return an array of the changes.
        I won't describe the format of the array, but it will be obvious
        if you use print_r() on the result of a diff on some test data.
       
        htmlDiff is a wrapper for the diff command, it takes two strings and
        returns the differences in HTML. The tags used are <ins> and <del>,
        which can easily be styled with CSS. 
*/

function diff($old, $new){
        foreach($old as $oindex => $ovalue){
                $nkeys = array_keys($new, $ovalue);
                foreach($nkeys as $nindex){
                        $matrix[$oindex][$nindex] = isset($matrix[$oindex - 1][$nindex - 1]) ?
                                $matrix[$oindex - 1][$nindex - 1] + 1 : 1;
                        if($matrix[$oindex][$nindex] > $maxlen){
                                $maxlen = $matrix[$oindex][$nindex];
                                $omax = $oindex + 1 - $maxlen;
                                $nmax = $nindex + 1 - $maxlen;
                        }
                }       
        }
        if($maxlen == 0) return array(array('d'=>$old, 'i'=>$new));
        return array_merge(
                diff(array_slice($old, 0, $omax), array_slice($new, 0, $nmax)),
                array_slice($new, $nmax, $maxlen),
                diff(array_slice($old, $omax + $maxlen), array_slice($new, $nmax + $maxlen)));
}

function htmlDiff($old, $new){
	$ret = array ('small_diff' => '', 'full_diff' => ''); 
   $diff = diff(explode(' ', $old), explode(' ', $new));

	switch( get_option( 'trip_log_detail' ) ) {
		case 0: 
        foreach($diff as $k){
                if(is_array($k)) {
                        $ret['small_diff'] .= (!empty($k['d'])?"<del>".implode(' ',$k['d'])."</del> ":'').
                                					 (!empty($k['i'])?"<ins>".implode(' ',$k['i'])."</ins> ":'');
                }
        }
        break;
      default:
        foreach($diff as $k){
                if(is_array($k)) {
                        $ret['small_diff'] .= (!empty($k['d'])?"<del>".implode(' ',$k['d'])."</del> ":'').
                                					 (!empty($k['i'])?"<ins>".implode(' ',$k['i'])."</ins> ":'');
                        $ret['full_diff'] .= (!empty($k['d'])?"<del>".implode(' ',$k['d'])."</del> ":'').
                                					(!empty($k['i'])?"<ins>".implode(' ',$k['i'])."</ins> ":'');
                }
                else $ret['full_diff'] .= $k . ' ';
        }
        break;
   }      
        return $ret;
} 

/**
 * log_modification() logs the differences to the prefix.wtunknown table,
 * for further review.
 */

function log_modification( $url, $client_html) {
	global $wpdb;
	
	$encoded_url = rawurlencode( $url );
	$encoded_server_html = grab_cached_html( $url );
	$encoded_client_html = rawurlencode( $client_html );

	$encoded_diff_html = htmlDiff( rawurldecode( $encoded_server_html ), $client_html );
	
	$unknown_table_name = $wpdb->prefix . "wtunknown";
	
	switch( get_option( 'trip_log_detail' ) ) {
		case 0:	//Only record the difference.
			$insert = "INSERT INTO " . $unknown_table_name . " (url, timestamp, user_agent, small_diff) " .
  		       "VALUES ('" . $url . "', '" . time() . "', '" . $_SERVER['HTTP_USER_AGENT'] . "', '" .
  		       rawurlencode( $encoded_diff_html['small_diff'] ) . "')";

			break;
		case 1:	//Only record the difference.
			$insert = "INSERT INTO " . $unknown_table_name . " (url, timestamp, user_agent, small_diff, full_diff) " .
  		       "VALUES ('" . $url . "', '" . time() . "', '" . $_SERVER['HTTP_USER_AGENT'] . "', '" .
  		       rawurlencode( $encoded_diff_html['small_diff'] ) . "', '" . rawurlencode( $encoded_diff_html['full_diff'] ) . "')";

			break;
		case 2:	//Record all pages
			$insert = "INSERT INTO " . $unknown_table_name . " (url, timestamp, user_agent, server_html, client_html, small_diff, full_diff) " .
  		       "VALUES ('" . $url . "', '" . time() . "', '" . $_SERVER['HTTP_USER_AGENT'] . "', '". $encoded_server_html . "', '" .
  		       $encoded_client_html . "', '" . rawurlencode( $encoded_diff_html['small_diff'] ) . "', '" . rawurlencode( $encoded_diff_html['full_diff'] ) . "')";
  		   break;
  	}
  		       
   $results = $wpdb->query( $insert );
}

/**
 * abort_notification() is called to surpress notifiying the client's
 * JavaScript component. For this, we return a HTTP/1.1 204 status code:
 * 'No Content'. See the IETF RFC 2616 (p. 59) for more details:
 * @link		http://www.ietf.org/rfc/rfc2616.txt?number=2616
 */

function abort_notification() {
		header( 'HTTP/1.1 204 No Content' );
		exit( 1 );
}

?>
