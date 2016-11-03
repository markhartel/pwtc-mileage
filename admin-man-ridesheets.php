<?php
if (!current_user_can('manage_options')) {
        	return;
}
?>
<script type="text/javascript" >
jQuery(document).ready(function($) {       
    $('#ride-lookup-form').on('submit', function(evt) {
        evt.preventDefault();
		$('#ride-lookup-table caption').remove();
		$('#ride-lookup-table tr').remove();
        var action = $('#ride-lookup-form').attr('action');
        var data = {
			'action': 'pwtc_mileage_lookup_rides',
			'startdate': $('#ride-lookup-date').val()
		};
		$.post(action, data, function(response) {
            var res = JSON.parse(response);
			$('#ride-lookup-table').append('<caption>' + res.caption + '</caption>');
            res.rides.forEach(function(item) {
                $('#ride-lookup-table').append('<tr><td>' + item.title + 
					'</td><td><button rideid="' + item.rideid + '">Edit</button></td></tr>');    
            });
			$('#ride-lookup-table button').on('click', function(evt) {
            	evt.preventDefault();
                var rideid = $(this).attr('rideid');
            	var action = '<?php echo admin_url('admin-ajax.php'); ?>';
            	var data = {
			    	'action': 'pwtc_mileage_lookup_mileage',
                	'ride_id': rideid
		    	};
            });
		});
    });
});
</script>
<div class="wrap">
	<h1><?= esc_html(get_admin_page_title()); ?></h1>
	<form id="ride-lookup-form" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post">
    	<label>Start Date:</label>
		<input id="ride-lookup-date" type="date" name="date" required/>
		<input id="ride-lookup-submit" type="submit" value="Find Rides"/>
	</form>
	<div>
		<table id="ride-mileage-table">
		</table>
	</div>
	<div>
		<table id="ride-leader-table">
		</table>
	</div>
</div>
<?php
