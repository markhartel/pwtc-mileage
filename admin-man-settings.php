<?php
if (!current_user_can('manage_options')) {
    return;
}
?>
<script type="text/javascript">
jQuery(document).ready(function($) { 
});
</script>
<div class="wrap">
	<h1><?= esc_html(get_admin_page_title()); ?></h1>
	<form method="POST">
		<p>
		<table>
			<tr><td>
				<label for="ride_post_type">Post Type for the Ride Object</label>
			</td><td>
    			<input type="text" name="ride_post_type" id="ride_post_type" 
					value="<?php echo $plugin_options['ride_post_type']; ?>" required/>
			</td></tr>
			<tr><td>
				<label for="ride_date_metakey">Metakey for the Ride Start Date</label>
			</td><td>
    			<input type="text" name="ride_date_metakey" id="ride_date_metakey" 
					value="<?php echo $plugin_options['ride_date_metakey']; ?>" required/>
			</td></tr>
			<tr><td>
				<label for="ride_date_format">Storage Format for the Ride Start Date</label>
			</td><td>
    			<input type="text" name="ride_date_format" id="ride_date_format" 
					value="<?php echo $plugin_options['ride_date_format']; ?>" required/>
			</td></tr>
			<tr><td>
				<label for="date_display_format">Date Display Format</label>
			</td><td>
    			<input type="text" name="date_display_format" id="date_display_format" 
					value="<?php echo $plugin_options['date_display_format']; ?>" required/>
			</td></tr>
			<tr><td>
				<label for="db_backup_location">Location of Year-End Backups</label>
			</td><td>
    			<input type="text" name="db_backup_location" id="db_backup_location" 
					value="<?php echo $plugin_options['db_backup_location']; ?>"/>
			</td></tr>
			<tr><td>
				<label for="plugin_menu_label">Plugin Menu Label</label>
			</td><td>
    			<input type="text" name="plugin_menu_label" id="plugin_menu_label" 
					value="<?php echo $plugin_options['plugin_menu_label']; ?>" required/>
			</td></tr>
			<tr><td>
				<label for="plugin_menu_location">Plugin Menu Location</label>
			</td><td>
    			<input type="text" name="plugin_menu_location" id="plugin_menu_location" 
					value="<?php echo $plugin_options['plugin_menu_location']; ?>" required/>
			</td></tr>
			<tr><td>
				<label for="ride_lookback_date">Posted Ride Minimum Lookback Date</label>
			</td><td>
    			<input type="text" name="ride_lookback_date" id="ride_lookback_date" 
					value="<?php echo $plugin_options['ride_lookback_date']; ?>"/>
			</td></tr>
			<tr><td>
				<label for="db_lock_time_limit">DB Batch Job Lock Time Limit</label>
			</td><td>
    			<input type="text" name="db_lock_time_limit" id="db_lock_time_limit" 
					value="<?php echo $plugin_options['db_lock_time_limit']; ?>"/>
			</td></tr>
			<tr><td>
				<label for="disable_expir_check">Disable Rider Expiration Check</label>
			</td><td>
    			<input type="checkbox" name="disable_expir_check" id="disable_expir_check" 
					<?php if ($plugin_options['disable_expir_check']) { echo 'checked'; } ?>/>
			</td></tr>
			<tr><td>
				<label for="disable_delete_confirm">Disable Delete Action Confirm</label>
			</td><td>
    			<input type="checkbox" name="disable_delete_confirm" id="disable_delete_confirm" 
					<?php if ($plugin_options['disable_delete_confirm']) { echo 'checked'; } ?>/>
			</td></tr>
			<tr><td>
				<label for="drop_db_on_delete">Drop Tables/Views Upon Plugin Delete</label>
			</td><td>
				<input type="checkbox" id="drop_db_on_delete" name="drop_db_on_delete" 
					<?php if ($plugin_options['drop_db_on_delete']) { echo 'checked'; } ?>/>
			</td></tr>
		</table>
		</p>
		<p>
		<input type="submit" value="Save" class="button button-primary button-large"/>
		</p>
	</form>
</div>
<?php
