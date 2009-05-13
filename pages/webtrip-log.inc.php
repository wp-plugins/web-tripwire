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
<h2>Web Tripwire Plugin Log</h2>

<?php

if (count($results)) {
    ?>
	<form method="post" action="">
    <div class="tablenav">
        <?php
        $page_links = paginate_links( array(
            'base' => add_query_arg( 'paged', '%#%' ),
            'format' => '',
            'total' => $number_of_pages,
            'current' => $page,
        ));

        if ( $page_links )
            echo "<div class='tablenav-pages'>$page_links</div>";
        ?>
        
        <div class="alignleft">
	         <button type="submit" name="op" value="refresh" class="button-secondary refresh">Refresh</button>
        		<button type="submit" name="op" value="delete" class="button-secondary delete">Delete</button>
        		<button type="submit" name="op" value="clear" class="button-primary delete">Clear All</button>
		  </div>
        <br class="clear" />
    </div>

    <br class="clear" />
    <table class="widefat">
        <thead>
            <tr>
                <th scope="col" class="check-column"><input type="checkbox" /></th>
                <th scope="col" class="num" >id</th>
                <th scope="col" >Time</th>
                <th scope="col" >URL</th>
                <th scope="col" >Agent String</th>
                <th scope="col" >Summary Difference</th>
                <th scope="col" ></th>
            </tr>
        </thead>
        <tbody>
        
            <?php foreach ($results as $result) { ?>
                <tr valign="top">
                    <th scope="row" class="check-column"><input type="checkbox" name="id[]" value="<?php echo $result->id; ?>" /></th>
                    <td class="num"><?php echo $result->id; ?></td>
                    <?php
                        if ($result->timestamp) {
                    ?> <td class="num"> <?php
									echo date( DATE_RFC822, $result->timestamp );
                        }
                        else {
                            ?> <td colspan="2"> <?php
                        }
					  ?> </td> <?php
                        if ($result->url) {
                        ?> <td class="text"> 
                            <a href="<?php echo $result->url; ?>" target="_blank" /><?php echo $result->url; ?></a><?php
                        }
                        else {
								?> <td colspan="2"> <?php
                        }
   					?> </td> <?php
                        if ($result->user_agent) {
                        ?> <td class="text"> <?php
                            echo htmlentities( $result->user_agent, ENT_QUOTES );
                        }
                        else {
								?> <td colspan="2"> <?php
                        }
   					?> </td> <?php
                        if ($result->small_diff) {
                        ?> <td class="text"><?php
                        
                            echo preg_replace( array( "/&lt;del&gt;/", "/&lt;\/del&gt;/", "/&lt;ins&gt;/",
                            "/&lt;\/ins&gt;/" ), array( "<del>", "</del>", "<ins>", "</ins>" ),
                            htmlentities( rawurldecode( $result->small_diff ), ENT_QUOTES ) );
                        }
                        else {
								?> <td colspan="2"> <?php
                        }
   					?> </td> <?php
                        if ($result->full_diff) {
                        ?> <td class="text">
									<img src="<?php echo plugins_url( 'web-tripwire/images/fulldiff_icon16.png' ) ?>" alt="Full Difference" /><?php                         
									if ( $result->server_html && $result->client_html ) {
									?><br><img src="<?php echo plugins_url( 'web-tripwire/images/fullhtml_icon16.png' ) ?>" alt="Server and Client HTML" /><?php
									}                         
                        }
                        else {
								?> <td colspan="2"> <?php
                        }
   					?> </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <div class="tablenav">
        <?php
        if ( $page_links )
            echo "<div class='tablenav-pages'>$page_links</div>";
        ?>
        <div class="alignleft">
	         <button type="submit" name="op" value="refresh" class="button-secondary refresh">Refresh</button>
        		<button type="submit" name="op" value="delete" class="button-secondary delete">Delete</button>
		  </div>
        <br class="clear" />
    </div>
    </form>
<?php } else { ?>
<p>There are no entries in the log.</p>
<?php } ?>