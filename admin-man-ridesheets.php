<?php
if (!current_user_can('manage_options')) {
        	return;
}
?>
<script type="text/javascript" >
jQuery(document).ready(function($) {       
    $('#ride-lookup-section form').on('submit', function(evt) {
        evt.preventDefault();
		$('#ride-lookup-section h3').html('');
		$('#ride-lookup-section table tr').remove();
        var action = $('#ride-lookup-section form').attr('action');
        var data = {
			'action': 'pwtc_mileage_lookup_rides',
			'startdate': $('#ride-lookup-date').val()
		};
		$.post(action, data, function(response) {
            var res = JSON.parse(response);
			$('#ride-lookup-section h3').html(res.caption);
            res.rides.forEach(function(item) {
                $('#ride-lookup-section table').append('<tr><td>' + item.title + 
					'</td><td><button rideid="' + item.rideid + '">Edit Sheet</button></td></tr>');    
            });
			$('#ride-lookup-section table button').on('click', function(evt) {
            	evt.preventDefault();
                var rideid = $(this).attr('rideid');
            	var action = '<?php echo admin_url('admin-ajax.php'); ?>';
            	var data = {
			    	'action': 'pwtc_mileage_lookup_mileage',
                	'ride_id': rideid
		    	};
				$('#ride-sheet-section').show();
				$('#ride-lookup-section').hide();
				//alert('Remove button pressed');
            });
		});
    });

    $('#ride-sheet-section button').on('click', function(evt) {
        evt.preventDefault();
		$('#ride-sheet-section').hide();
		$('#ride-lookup-section').show();
    });

	$('#ride-sheet-section').hide();
	$('#ride-lookup-section').show();
});
</script>
<div class="wrap">
	<h1><?= esc_html(get_admin_page_title()); ?></h1>
	<div id="ride-lookup-section">
		<form action="<?php echo admin_url('admin-ajax.php'); ?>" method="post">
    		<label>Start Date:</label>
			<input id="ride-lookup-date" type="date" name="date" required/>
			<input type="submit" value="Find Rides"/>
		</form>
		<h3></h3>
		<table></table>
	</div>
	<div id='ride-sheet-section'>
		<button>Back to Rides</button>
		<h2></h2>
		<div id="ride-leader-section">
			<h3>Ride Leaders</h3>
			<form action="<?php echo admin_url('admin-ajax.php'); ?>" method="post">
			</form>
			<table></table>
		</div>
		<div id="ride-mileage-section">
			<h3>Rider Mileage</h3>
			<form action="<?php echo admin_url('admin-ajax.php'); ?>" method="post">
			</form>
			<table></table>
		</div>
	</div>
</div>
<?php
