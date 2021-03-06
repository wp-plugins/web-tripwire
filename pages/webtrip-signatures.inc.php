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
<h2><?php _e( 'Web Tripwire Plugin Signatures', 'web-tripwire' ); ?></h2>

<?php

if (count($results)) {
	?> <form method="post" action="">
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
	         <button type="submit" name="op" value="refresh" class="button-secondary refresh"><?php _e( 'Refresh', 'web-tripwire' ); ?></button>
	         <button type="submit" name="op" value="delete" class="button-secondary delete"><?php _e( 'Delete', 'web-tripwire' ); ?></button>
		  <?php if( class_exists( 'gnupg' ) && get_option( 'trip_gpg' ) ) { ?>
	         <button type="submit" name="op" value="update" class="button-primary update"><?php _e( 'Update Signatures', 'web-tripwire' ); ?></button>
	     <?php } ?>
		  </div>
        <br class="clear" />
    </div>

    <br class="clear" />
    <table class="widefat">
        <thead>
            <tr>
                <th scope="col" class="check-column"><input type="checkbox" /></th>
                <th scope="col" class="num" ><?php _e( 'id', 'web-tripwire' ); ?></th>
                <th scope="col" ><?php _e( 'Detect', 'web-tripwire' ); ?></th>
                <th scope="col" ><?php _e( 'Regex', 'web-tripwire' ); ?></th>
                <th scope="col" ><?php _e( 'Notify', 'web-tripwire' ); ?></th>
                <th scope="col" ><?php _e( 'Message', 'web-tripwire' ); ?></th>
            </tr>
        </thead>
        <tbody>
        
            <?php foreach ($results as $result) { ?>
                <tr valign="top">
                    <th scope="row" class="check-column"><input type="checkbox" name="id[]" value="<?php echo $result->id; ?>" /></th>
                    <td class="num"><?php echo $result->id; ?></td>
                    <?php
                        if ($result->detect) {
                        ?> <td class="text"> <?php
									echo htmlentities( $result->detect, ENT_QUOTES );
                        }
                        else {
								?> <td colspan="2"> <?php
                        }
					  ?> </td> <?php
                        if ($result->regex) {
                        ?> <td class="text"> <?php
                            echo htmlentities( $result->regex, ENT_QUOTES );
                        }
                        else {
								?> <td colspan="2"> <?php
                        }
					  ?> </td> <?php
                        if ($result->notify != '0' || $result->notify != '1') {
                        ?> <td class="num"> <?php
                            echo htmlentities( $result->notify, ENT_QUOTES );
                        }
                        else {
								?> <td colspan="2"> <?php
                        }
   					?> </td> <?php
                        if ($result->message) {
                        ?> <td class="text"> <?php
                            echo htmlentities( $result->message, ENT_QUOTES );
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
	         <button type="submit" name="op" value="refresh" class="button-secondary refresh"><?php _e( 'Refresh', 'web-tripwire' ); ?></button>
	         <button type="submit" name="op" value="delete" class="button-secondary delete"><?php _e( 'Delete', 'web-tripwire' ); ?></button>

    </div>
    <br class="clear" />
    </form>
<?php } else { ?>
<p><?php _e( 'There are no entries in the signatures.', 'web-tripwire' ); ?></p>
<?php } ?>
</ br>

<h2><?php _e( 'Add a Signature', 'web-tripwire' ); ?></h2>
	<form method="post" action="">
    <table class="widefat">
        <thead>
            <tr>
                <th scope="col" ><?php _e( 'Detect', 'web-tripwire' ); ?></th>
                <th scope="col" ><?php _e( 'Regex', 'web-tripwire' ); ?></th>
                <th scope="col" ><?php _e( 'Notify', 'web-tripwire' ); ?></th>
                <th scope="col" ><?php _e( 'Message', 'web-tripwire' ); ?></th>
            </tr>
        </thead>
        <tbody>
        	<tr valign="top">
				<td class="text">
            	<input type="text" class="text" name="detect" id="detect" value=""/>
				</td>
				<td class="text">
            	<input type="text" class="text" name="regex" id="regex" value=""/>
				</td>				
				<td class="text">
				<select name="notify" id="notify">
					<option value="0" >0</option>
					<option value="1" selected="selected" >1</option>
				</select>
			</td>
				<td class="text">
            	<textarea class="textarea" name="message" id="message" cols="50" rows="2" value=""/></textarea>
				</td>
			</tr>
        </tbody>
    </table>
    <div class="tablenav">
        <div class="alignleft">
	         <button type="submit" name="op" value="add" class="button-secondary add"><?php _e( 'Add', 'web-tripwire' ); ?></button>
	     </div>
        <br class="clear" />
    </div>
   </form>