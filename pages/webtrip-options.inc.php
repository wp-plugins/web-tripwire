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
<h2>Web Tripwire Plugin Options</h2>

<form name="form1" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	<input type="hidden" name="trip_submit_hidden" value="Y">

	<table class="form-table">
		<tr valign="top">
			<th scope="row">Client Notifications </th>
			<td><fieldset><legend class="hidden">Client Notifications </legend>

			<input id="trip_notify_all" type="radio" name="trip_notify" value="3" <?php if (get_option('trip_notify') == "3") { echo "checked=\"checked\""; } ?> />
			<label for="trip_notify_all">Enable notifications of all in-flight modifications</label><br />

			<input id="trip_notify_unknown" type="radio" name="trip_notify" value="2" <?php if (get_option('trip_notify') == "2") { echo "checked=\"checked\""; } ?> />
			<label for="trip_notify_unknown">Enable notifications of urgent, and unclassified in-flight modifications</label><br />

			<input id="trip_notify_case" type="radio" name="trip_notify" value="1" <?php if (get_option('trip_notify') == "1") { echo "checked=\"checked\""; } ?> />
			<label for="trip_notify_case">Enable only notifications of urgent in-flight modifications</label><br />

			<input id="trip_notify_none" type="radio" name="trip_notify" value="0" <?php if (get_option('trip_notify') == "0") { echo "checked=\"checked\""; } ?> />
			<label for="trip_notify_none">Disable all notifications</label><br />

			</fieldset></td>
		</tr>
		<tr valign="top">
			<th scope="row">Logging </th>
			<td><fieldset><legend class="hidden">Logging </legend>

			<input id="trip_logging_all" type="radio" name="trip_logging" value="2" <?php if (get_option('trip_logging') == "2") { echo "checked=\"checked\""; } ?> />
			<label for="trip_logging_all">Enable logging of all in-flight modifications</label><br />

			<input id="trip_logging_unknown" type="radio" name="trip_logging" value="1" <?php if (get_option('trip_logging') == "1") { echo "checked=\"checked\""; } ?> />
			<label for="trip_logging_unknown">Enable logging of unknown in-flight modifications</label><br />

			<input id="trip_logging_none" type="radio" name="trip_logging" value="0" <?php if (get_option('trip_logging') == "0") { echo "checked=\"checked\""; } ?> />
			<label for="trip_logging_none">Disable all logging</label><br />

			</fieldset></td>
		</tr>
		<tr valign="top">
			<th scope="row">Logging Detail </th>
			<td><fieldset><legend class="hidden">Logging Detail </legend>

			<input id="trip_log_detail_all" type="radio" name="trip_log_detail" value="2" <?php if (get_option('trip_log_detail') == "2") { echo "checked=\"checked\""; } ?> />
			<label for="trip_log_detail_all">Enable recording of server-side page, client-side page, and full difference output</label><br />

			<input id="trip_log_detail_fdiff" type="radio" name="trip_log_detail" value="1" <?php if (get_option('trip_log_detail') == "1") { echo "checked=\"checked\""; } ?> />
			<label for="trip_log_detail_fdiff">Enable recording of full difference output</label><br />

			<input id="trip_log_detail_sdiff" type="radio" name="trip_log_detail" value="0" <?php if (get_option('trip_log_detail') == "0") { echo "checked=\"checked\""; } ?> />
			<label for="trip_log_detail_sdiff">Enable recording of summary difference output</label><br />

			</fieldset></td>
		</tr>

		<tr valign="top">
			<th scope="row">Caching Mechanism </th>
			<td><fieldset><legend class="hidden">Caching Mechanism </legend>

			<input id="trip_cache_db" type="radio" name="trip_cache" value="2" <?php if (get_option('trip_cache') == "2") { echo "checked=\"checked\""; } ?> />
			<label for="trip_cache_db">Use database-based caching of requested pages (compatible with all hosting providers)</label><br />

			<input id="trip_cache_none" type="radio" name="trip_cache" value="0" <?php if (get_option('trip_cache') == "0") { echo "checked=\"checked\""; } ?> />
			<label for="trip_cache_none">Disable all caching of requested pages</label><br />
			
			<select name="trip_cache_expire" id="trip_cache_expire">
				<option value="30"<?php if (get_option('trip_cache_expire') == "30") { echo " selected=\"selected\""; } ?> >30</option>
				<option value="60"<?php if (get_option('trip_cache_expire') == "60") { echo " selected=\"selected\""; } ?> >60</option>
				<option value="90"<?php if (get_option('trip_cache_expire') == "90") { echo " selected=\"selected\""; } ?> >90</option>
				<option value="120"<?php if (get_option('trip_cache_expire') == "120") { echo " selected=\"selected\""; } ?> >120</option>
			</select>
			<label for="trip_cache_expire">Expiry time of cached object (in seconds)</label><br />

			</fieldset></td>
		</tr>
		<tr valign="top">
			<th scope="row">Miscellaneous </th>
			<td><fieldset><legend class="hidden">Miscellaneous </legend>

			<select name="trip_items_per_page" id="trip_items_per_page">
				<option value="3"<?php if (get_option('trip_items_per_page') == "3") { echo " selected=\"selected\""; } ?> >3</option>
				<option value="5"<?php if (get_option('trip_items_per_page') == "5") { echo " selected=\"selected\""; } ?> >5</option>
				<option value="10"<?php if (get_option('trip_items_per_page') == "10") { echo " selected=\"selected\""; } ?> >10</option>
			</select>
			<label for="trip_items_per_page">Number of items to display per page in administration interface</label><br />

			</fieldset></td>
		</tr>		
	</table>

	<p class="submit">
		<input type="submit" name="Submit" value="<?php _e('Update Options', 'mt_trans_domain' ) ?>" class="button-primary" />
	</p>

</form>
</div>