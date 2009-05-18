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
 * This validates that the URL requested in the JavaScript is actually a resource
 * located within the blog URL structure. If not, someone's trying to use us as
 * an anonymous proxy. So send them a HTTP/1.1 403 status code: 'Forbidden'.
 * See the IETF RFC 2616 (p. 65) for more details:
 * @link		http://www.ietf.org/rfc/rfc2616.txt?number=2616
 */

if ( isset( $_REQUEST['target'] ) ) {
	$referrer_url = rawurldecode( $_REQUEST['target'] );
	$blog_url = get_bloginfo( 'url' );
	
	if ( strncmp( $blog_url, $referrer_url, strlen( $blog_url ) ) != 0 ) {
		header( 'HTTP/1.1 403 Forbidden' );
		exit( 1 );
	}
}

/**
 * Time to request and read the URL we've been given, and implement any of the
 * selected cache mechanisms and options.
 */

$html_data_encoded = grab_cached_html( $_REQUEST['target'] );
	
/**
 * We're returning JavaScript as output, so make it look that way.
 */
 
header('Content-type: application/javascript');
?>
/* Client-side web tripwire script to detect page modifications.
 * Charles Reis, University of Washington, 2007.
 */

/**
*
*  URL encode / decode
*  http://www.webtoolkit.info/
*
**/
 
var Url = {
 
	// public method for url encoding
	encode : function (string) {
		return escape(this._utf8_encode(string));
	},
 
	// public method for url decoding
	decode : function (string) {
		return this._utf8_decode(unescape(string));
	},
 
	// private method for UTF-8 encoding
	_utf8_encode : function (string) {
		string = string.replace(/\r\n/g,"\n");
		var utftext = "";
 
		for (var n = 0; n < string.length; n++) {
 
			var c = string.charCodeAt(n);
 
			if (c < 128) {
				utftext += String.fromCharCode(c);
			}
			else if((c > 127) && (c < 2048)) {
				utftext += String.fromCharCode((c >> 6) | 192);
				utftext += String.fromCharCode((c & 63) | 128);
			}
			else {
				utftext += String.fromCharCode((c >> 12) | 224);
				utftext += String.fromCharCode(((c >> 6) & 63) | 128);
				utftext += String.fromCharCode((c & 63) | 128);
			}
 
		}
 
		return utftext;
	},
 
	// private method for UTF-8 decoding
	_utf8_decode : function (utftext) {
		var string = "";
		var i = 0;
		var c = c1 = c2 = 0;
 
		while ( i < utftext.length ) {
 
			c = utftext.charCodeAt(i);
 
			if (c < 128) {
				string += String.fromCharCode(c);
				i++;
			}
			else if((c > 191) && (c < 224)) {
				c2 = utftext.charCodeAt(i+1);
				string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
				i += 2;
			}
			else {
				c2 = utftext.charCodeAt(i+1);
				c3 = utftext.charCodeAt(i+2);
				string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
				i += 3;
			}
 
		}
 
		return string;
	}
 
}


WebTripwire = new Object();

// URL of the page to test:
WebTripwire.targetPageURL = "<?php echo $referrer_url; ?>";

// HTML of the page to test, as an encodedURI:
WebTripwire.encodedTargetPageHTML = "<?php echo $html_data_encoded; ?>";

// URL of the page to notify, in the event of a detected change:
WebTripwire.notifyChangeURL = "<?php echo plugins_url( WP_WEBTRIP_RDIR . "/webtrip-notifier.php" ); ?>"

/* Fetches the target page with an XmlHttpRequest and compares it to the
 * expected HTML string.  If they differ, report the modified HTML to
 * the server and optionally the user.
 * The callback argument is invoked if a change is detected.
 */
WebTripwire.detect = function(callback) {
  var req = WebTripwire.newXHR();
  
  // Create a handler for the test page request
  var handler = function() {
    // Check if the request state is loaded and OK
    if (req.readyState == 4 && req.status == 200) {
    
      // See if the actual HTML is the same as the expected HTML.
      var targetPageHTML = Url.decode(WebTripwire.encodedTargetPageHTML);
      if (req.responseText != targetPageHTML) {
        // Detected modification
        //alert(req.responseText);  // for debugging
        //alert(targetPageHTML);  // for debugging

        // Notify server
        if (WebTripwire.notifyChangeURL) {
          var notify = WebTripwire.newXHR();
          
          // Create a handler for the notification request
          var notifyHandler = function() {
            if (notify.readyState == 4 && notify.status == 200) {
              // Notify the user
              WebTripwire.react(targetPageHTML, req.responseText, notify.responseText);
            }
          };
          
          // Create a results string to send back
          var results;
          results = "actualHTML=" + encodeURIComponent(req.responseText) + "&target=" + WebTripwire.targetPageURL;
          notify.onreadystatechange = notifyHandler;
          notify.open("POST", WebTripwire.notifyChangeURL, true);
          notify.setRequestHeader("Content-Type", 
            "application/x-www-form-urlencoded");
          notify.send(results);
        }
        
        // Invoke the callback
        if (callback && typeof callback == 'function') {
          callback(req.responseText);
        }
        
      }
    }
  };
  
  // Make an asynchronous request for the test page
  req.onreadystatechange = handler;
  req.open("GET", WebTripwire.targetPageURL, true);
  req.send(null);
};

/* Function to display a message to the user when a change is detected.
 */
WebTripwire.react = function(expected, actual, message) {
  if (message == null) {
    // Don't print anything
    return;
  }
  // Escape angle brackets for printing HTML source code
  expected = expected.replace(/</g, '&lt;').replace(/>/g, '&gt;');
  actual = actual.replace(/</g, '&lt;').replace(/>/g, '&gt;');
  var insObj = { text:"" };
  var diff = jsd_diffString(expected, actual, insObj);
  diff = diff.replace(/\n/g, "<br>\n").replace(/"/g, "&quot;");
  
  WebTripwire.diff = "<html><body>\n" + message +
    "<hr>\n" + diff + "\n</body></html>";
    
  // Create a div for a message to the user
  var messagebar = document.createElement('div');
  messagebar.id = "webtripwirebar";

<?php

	switch( get_option( 'trip_javascript_element' ) ) {
		case '1':
			echo "	document.body.insertBefore(messagebar, document.getElementById(\"content\"));";
			break;
		default:
			echo "	document.body.insertBefore(messagebar, document.body.firstChild);";
	}

?>	
  
  // Display a message to the user
  var infobar = new informationbar();
  infobar.setContent('<?php printf( __( 'We have detected that this page has been modified in flight. ' .
  	'For more information, click %shere%s.', 'web-tripwire' ), '<a onclick="javascript:w=window.open();w.document.write' .
  	'(WebTripwire.diff);w.document.close()"><u>', '</u></a>' ) . __( 'here', 'web-tripwire' ); ?>'); 
  //infobar.setfrequency('session');  // make the bar appear once per session
  infobar.initialize();

}

/* Copied cross-browser code for getting an XMLHttpRequest object.
 * Source: http://www.hackorama.com/ajax/
 */
WebTripwire.newXHR = function() {
  var xmlreq = false;
  if (window.XMLHttpRequest) {
    xmlreq = new XMLHttpRequest();
  } else if (window.ActiveXObject) {
    // Try ActiveX
    try { 
      xmlreq = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e1) { 
      // first method failed 
      try {
        xmlreq = new ActiveXObject("Microsoft.XMLHTTP");
      } catch (e2) {
        // both methods failed 
      } 
    }
  }
  return xmlreq;
};


/* Add stylesheet for tripwire bar. */
WebTripwire.css = document.createElement('link');
WebTripwire.css.rel = "StyleSheet";
WebTripwire.css.href = "<?php echo plugins_url( WP_WEBTRIP_RDIR . "/css/webtrip.css" ); ?>"
WebTripwire.css.type = "text/css";
document.getElementsByTagName("head")[0].appendChild(WebTripwire.css);

/* Add an onload handler to run the tripwire. 
 * Credit: http://simonwillison.net/2004/May/26/addLoadEvent/
 */
WebTripwire.addLoadEvent = function(newonload) {
  var oldonload = window.onload;
  if (typeof window.onload != 'function') {
    window.onload = newonload;
  } else {
    window.onload = function() {
      if (oldonload) {
        oldonload();
      }
      newonload();
    }
  }
}
WebTripwire.addLoadEvent(WebTripwire.detect);

/*
 * Javascript Diff Algorithm
 *  By John Resig (http://ejohn.org/)
 *  Modified by Chu Alan "sprite"
 *  Modified by Charles Reis
 *
 * More Info:
 *  http://ejohn.org/projects/javascript-diff-algorithm/
 */

function jsd_escape(s) {
    var n = s;
    //n = n.replace(/&/g, "&amp;");
    n = n.replace(/</g, "&lt;");
    n = n.replace(/>/g, "&gt;");
    n = n.replace(/"/g, "&quot;");

    return n;
}

function jsd_diffString( o, n, insObj ) {
  o = o.replace(/\s+$/, '');
  n = n.replace(/\s+$/, '');
  if (insObj == null) insObj = new Object();

  var out = jsd_diff(o == "" ? [] : o.split(/\s+/), n == "" ? [] : n.split(/\s+/) );
  var str = "";

  var oSpace = o.match(/\s+/g);
  if (oSpace == null) {
    oSpace = ["\n"];
  } else {
    oSpace.push("\n");
  }
  var nSpace = n.match(/\s+/g);
  if (nSpace == null) {
    nSpace = ["\n"];
  } else {
    nSpace.push("\n");
  }

  if (out.n.length == 0) {
      for (var i = 0; i < out.o.length; i++) {
        str += "<del style='background:#FFE6E6;'>" + jsd_escape(out.o[i]) + oSpace[i] + "</del>";
      }
  } else {
    if (out.n[0].text == null) {
      for (n = 0; n < out.o.length && out.o[n].text == null; n++) {
        str += "<del style='background:#FFE6E6;'>" + jsd_escape(out.o[n]) + oSpace[n] + "</del>";
      }
    }

    for ( var i = 0; i < out.n.length; i++ ) {
      if (out.n[i].text == null) {
        str += "<ins style='background:#E6FFE6;'>" + jsd_escape(out.n[i]) + nSpace[i] + "</ins>";
        insObj.text += jsd_escape(out.n[i]) + nSpace[i];
      } else {
        var pre = "";

        for (n = out.n[i].row + 1; n < out.o.length && out.o[n].text == null; n++ ) {
          pre += "<del style='background:#FFE6E6;'>" + jsd_escape(out.o[n]) + oSpace[n] + "</del>";
        }
        str += " " + out.n[i].text + nSpace[i] + pre;
      }
    }
  }
  
  return str;
}

function jsd_diff( o, n ) {
  var ns = new Object();
  var os = new Object();
  
  for ( var i = 0; i < n.length; i++ ) {
    if ( ns[ n[i] ] == null )
      ns[ n[i] ] = { rows: new Array(), o: null };
    ns[ n[i] ].rows.push( i );
  }
  
  for ( var i = 0; i < o.length; i++ ) {
    if ( os[ o[i] ] == null )
      os[ o[i] ] = { rows: new Array(), n: null };
    os[ o[i] ].rows.push( i );
  }
  
  for ( var i in ns ) {
    if ( ns[i].rows.length == 1 && typeof(os[i]) != "undefined" && os[i].rows.length == 1 ) {
      n[ ns[i].rows[0] ] = { text: n[ ns[i].rows[0] ], row: os[i].rows[0] };
      o[ os[i].rows[0] ] = { text: o[ os[i].rows[0] ], row: ns[i].rows[0] };
    }
  }
  
  for ( var i = 0; i < n.length - 1; i++ ) {
    if ( n[i].text != null && n[i+1].text == null && n[i].row + 1 < o.length && o[ n[i].row + 1 ].text == null && 
         n[i+1] == o[ n[i].row + 1 ] ) {
      n[i+1] = { text: n[i+1], row: n[i].row + 1 };
      o[n[i].row+1] = { text: o[n[i].row+1], row: i + 1 };
    }
  }
  
  for ( var i = n.length - 1; i > 0; i-- ) {
    if ( n[i].text != null && n[i-1].text == null && n[i].row > 0 && o[ n[i].row - 1 ].text == null && 
         n[i-1] == o[ n[i].row - 1 ] ) {
      n[i-1] = { text: n[i-1], row: n[i].row - 1 };
      o[n[i].row-1] = { text: o[n[i].row-1], row: i - 1 };
    }
  }
  
  return { o: o, n: n };
}


/***********************************************

* Animated Information Bar- by JavaScript Kit (www.javascriptkit.com)
* This notice must stay intact for usage
* Visit JavaScript Kit at http://www.javascriptkit.com/ for this script and 100s more

***********************************************/

function informationbar(){
        this.displayfreq="always"
        this.content='<a href="javascript:informationbar.close()"><img src="<?php echo plugins_url( WP_WEBTRIP_RDIR . "/images/webtrip-close.gif" ); ?>" style="width: 14px; height: 14px; float: right; border: 0; margin-right: 5px" /></a>'
}

informationbar.prototype.setContent=function(data){
        this.content=this.content+data
        document.getElementById("webtripwirebar").innerHTML = this.content;
}

informationbar.prototype.animatetoview=function(){
        var barinstance=this
        if (parseInt(this.barref.style.top)<0){
                this.barref.style.top=parseInt(this.barref.style.top)+5+"px"
                setTimeout(function(){barinstance.animatetoview()}, 50)
        }
        else{
                if (document.all && !window.XMLHttpRequest)
                this.barref.style.setExpression("top", 'document.compatMode=="CSS1Compat"? document.documentElement.scrollTop+"px" : body.scrollTop+"px"')
        else
                this.barref.style.top=0
        }
}

informationbar.close=function(){
        document.getElementById("webtripwirebar").style.display="none"
        if (this.displayfreq=="session")
                document.cookie="infobarshown=1;path=/"
}

informationbar.prototype.setfrequency=function(type){
        this.displayfreq=type
}

informationbar.prototype.initialize=function(){
        if (this.displayfreq=="session" && document.cookie.indexOf("infobarshown")==-1 || this.displayfreq=="always"){
                this.barref=document.getElementById("webtripwirebar")
                this.barheight=parseInt(this.barref.offsetHeight)
                this.barref.style.top=this.barheight*(-1)+"px"
                this.animatetoview()
        }
}

window.onunload=function(){
        this.barref=null
}
