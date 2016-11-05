<?php
if (!current_user_can('manage_options')) {
        	return;
}
?>
<script type="text/javascript" >
jQuery(document).ready(function($) {   
	function populate_rides_table(date, rides) {
		$('#ride-lookup-section table tr').remove();
        rides.forEach(function(item) {
            $('#ride-lookup-section table').append(
				'<tr rideid="' + item.rideid + '" ridedate="' + date + '"><td>' +
				item.title + '</td><td><button>Edit Sheet</button></td></tr>');    
        });
		$('#ride-lookup-section table button').on('click', function(evt) {
            evt.preventDefault();
            var action = '<?php echo admin_url('admin-ajax.php'); ?>';
            var data = {
			    'action': 'pwtc_mileage_lookup_ridesheet',
                'ride_id': $(this).parent().parent().attr('rideid'),
				'date': $(this).parent().parent().attr('ridedate'),
				'title': $(this).parent().parent().find('td').first().html()
		    };
			$.post(action, data, lookup_ridesheet_cb);
		});
	}

	function populate_ride_leader_table(ride_id, leaders) {
		$('#ride-leader-section table tr').remove();
		$('#ride-leader-section table').append(
			'<tr><th>Rider ID</th><th>Name</th><th></th></tr>');
        leaders.forEach(function(item) {
            $('#ride-leader-section table').append(
				'<tr rideid="' + ride_id + '" memberid="' + item.member_id + '">' + 
				'<td>' + item.member_id + '</td>' +
				'<td>' + item.first_name + ' ' + item.last_name + '</td>' + 
				'<td><button>Remove</button></td></tr>');    
		});
	}

	function populate_ride_mileage_table(ride_id, mileage) {
		$('#ride-mileage-section table tr').remove();
		$('#ride-mileage-section table').append(
			'<tr><th>Rider ID</th><th>Name</th><th>Mileage</th><th></th></tr>');
        mileage.forEach(function(item) {
            $('#ride-mileage-section table').append(
				'<tr rideid="' + ride_id + '" memberid="' + item.member_id + '">' + 
				'<td>' + item.member_id + '</td>' +
				'<td>' + item.first_name + ' ' + item.last_name + '</td>' + 
				'<td>' + item.mileage + '</td>' +
				'<td><button>Remove</button></td></tr>');    
		});
	}

	function lookup_rides_cb(response) {
        var res = JSON.parse(response);
		$('#ride-lookup-section h3').html('Rides on ' + res.date);
		populate_rides_table(res.date, res.rides);
	}   

	function lookup_ridesheet_cb(response) {
		$('#ride-sheet-section').show();
		$('#ride-lookup-section').hide();
        var res = JSON.parse(response);
		$('#ride-sheet-section h2').html(res.title + ' (' + res.date + ')');
		populate_ride_leader_table(res.ride_id, res.leaders);
		populate_ride_mileage_table(res.ride_id, res.mileage);

	}   

    $('#ride-lookup-section form').on('submit', function(evt) {
        evt.preventDefault();
		$('#ride-lookup-section h3').html('');
        var action = $('#ride-lookup-section form').attr('action');
        var data = {
			'action': 'pwtc_mileage_lookup_rides',
			'startdate': $('#ride-lookup-date').val()
		};
		$.post(action, data, lookup_rides_cb);
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
