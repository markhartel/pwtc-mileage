<?php
if (!current_user_can('manage_options')) {
        	return;
}
?>
<script type="text/javascript" >
jQuery(document).ready(function($) {  
	function populate_posts_table(posts) {
		console.log(posts);
	}

	function populate_rides_table(startdate, rides, ridecal) {
		$('#ride-lookup-section table tr').remove();
		//console.log(rides);
		//console.log(ridecal);
        ridecal.forEach(function(item) {
			var found = false;
			rides.forEach(function(ride) {
				if (ride.post_id === item.ID) {
					found = true;
				}
			});
			if (!found) {
            	$('#ride-lookup-section table').append(
					'<tr postid="' + item.ID + '" ridedate="' + startdate + '"><td>' +
					item.post_title + '</td><td><button class="create_btn">Create Sheet</button></td>' + 
					'<td></td></tr>'); 
			}   
        });
        rides.forEach(function(item) {
            $('#ride-lookup-section table').append(
				'<tr rideid="' + item.ID + '" ridedate="' + startdate + '"><td>' +
				item.title + '</td><td><button class="edit_btn">Edit Sheet</button></td>' + 
				'<td><button class="remove_btn">Remove Ride</button></td></tr>');    
        });
		$('#ride-lookup-section table .create_btn').on('click', function(evt) {
            evt.preventDefault();
            var action = '<?php echo admin_url('admin-ajax.php'); ?>';
            var data = {
			    'action': 'pwtc_mileage_create_ride_from_event',
                'post_id': $(this).parent().parent().attr('postid'),
				'startdate': $(this).parent().parent().attr('ridedate'),
				'title': $(this).parent().parent().find('td').first().html()
		    };
			$.post(action, data, create_ride_from_event_cb);
		});
		$('#ride-lookup-section table .edit_btn').on('click', function(evt) {
            evt.preventDefault();
            var action = '<?php echo admin_url('admin-ajax.php'); ?>';
            var data = {
			    'action': 'pwtc_mileage_lookup_ridesheet',
                'ride_id': $(this).parent().parent().attr('rideid'),
				'startdate': $(this).parent().parent().attr('ridedate'),
				'title': $(this).parent().parent().find('td').first().html()
		    };
			$.post(action, data, lookup_ridesheet_cb);
		});
		$('#ride-lookup-section table .remove_btn').on('click', function(evt) {
            evt.preventDefault();
            var action = '<?php echo admin_url('admin-ajax.php'); ?>';
            var data = {
			    'action': 'pwtc_mileage_remove_ride',
                'ride_id': $(this).parent().parent().attr('rideid'),
				'startdate': $(this).parent().parent().attr('ridedate'),
				'title': $(this).parent().parent().find('td').first().html()
		    };
			$.post(action, data, remove_ride_cb);
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
		$('#ride-leader-section table button').on('click', function(evt) {
            evt.preventDefault();
            var action = '<?php echo admin_url('admin-ajax.php'); ?>';
            var data = {
			    'action': 'pwtc_mileage_remove_leader',
                'ride_id': $(this).parent().parent().attr('rideid'),
				'member_id': $(this).parent().parent().attr('memberid')
		    };
			$.post(action, data, remove_leader_cb);
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
		$('#ride-mileage-section table button').on('click', function(evt) {
            evt.preventDefault();
            var action = '<?php echo admin_url('admin-ajax.php'); ?>';
            var data = {
			    'action': 'pwtc_mileage_remove_mileage',
                'ride_id': $(this).parent().parent().attr('rideid'),
				'member_id': $(this).parent().parent().attr('memberid')
		    };
			$.post(action, data, remove_mileage_cb);
		});
	}

	function lookup_posts_cb(response) {
        var res = JSON.parse(response);
		populate_posts_table(res.posts);
	}   

	function lookup_rides_cb(response) {
        var res = JSON.parse(response);
		$('#ride-lookup-section h3').html('Rides on ' + res.date);
		populate_rides_table(res.startdate, res.rides, res.ridecal);
		$('#ride-create-date').val(res.startdate);
		$('#ride-create-form').show();
	}   

	function create_ride_cb(response) {
        var res = JSON.parse(response);
		if (res.error) {
			alert(res.error);
		}
		else {
			populate_rides_table(res.startdate, res.rides, res.ridecal);
			$('#ride-create-title').val('');
		}
	}   

	function create_ride_from_event_cb(response) {
        var res = JSON.parse(response);
		if (res.error) {
			alert(res.error);
		}
		else {
			populate_rides_table(res.startdate, res.rides, res.ridecal);
		}
	}   

	function lookup_ridesheet_cb(response) {
		$('#ride-sheet-section').show();
		$('#ride-lookup-section').hide();
        var res = JSON.parse(response);
		$('#ride-sheet-section h2').html(res.title + ' (' + res.date + ')');
		$('#leader-rideid').val(res.ride_id); 
		$('#mileage-rideid').val(res.ride_id); 
		populate_ride_leader_table(res.ride_id, res.leaders);
		populate_ride_mileage_table(res.ride_id, res.mileage);
	}   

	function remove_ride_cb(response) {
		var res = JSON.parse(response);
		if (res.error) {
			alert(res.error);
		}
		else {
			populate_rides_table(res.startdate, res.rides, res.ridecal);
		}
	}

	function remove_leader_cb(response) {
		var res = JSON.parse(response);
		if (res.error) {
			alert(res.error);
		}
		else {
			populate_ride_leader_table(res.ride_id, res.leaders);
		}
	}

	function remove_mileage_cb(response) {
		var res = JSON.parse(response);
		if (res.error) {
			alert(res.error);
		}
		else {
			populate_ride_mileage_table(res.ride_id, res.mileage);
		}
	}

	function add_leader_cb(response) {
		var res = JSON.parse(response);
		if (res.error) {
			alert(res.error);
		}
		else {
			$('#leader-add-btn').hide();
			populate_ride_leader_table(res.ride_id, res.leaders);
		}
	}

	function add_mileage_cb(response) {
		var res = JSON.parse(response);
		if (res.error) {
			alert(res.error);
		}
		else {
			$('#mileage-add-btn').hide();
			populate_ride_mileage_table(res.ride_id, res.mileage);
		}
	}

	function load_posts_without_rides() {
        var action = '<?php echo admin_url('admin-ajax.php'); ?>';
        var data = {
			'action': 'pwtc_mileage_lookup_posts'
		};
		$.post(action, data, lookup_posts_cb);
	}	
	
    $('#ride-lookup-form').on('submit', function(evt) {
        evt.preventDefault();
		$('#ride-lookup-section h3').html('');
		$('#ride-create-form').hide();
        var action = $('#ride-lookup-form').attr('action');
        var data = {
			'action': 'pwtc_mileage_lookup_rides',
			'startdate': $('#ride-lookup-date').val()
		};
		$.post(action, data, lookup_rides_cb);
    });

    $('#ride-create-form').on('submit', function(evt) {
        evt.preventDefault();
        var action = $('#ride-create-form').attr('action');
        var data = {
			'action': 'pwtc_mileage_create_ride',
			'startdate': $('#ride-create-date').val(),
			'title': $('#ride-create-title').val()
		};
		$.post(action, data, create_ride_cb);
    });

    $('#ride-leader-section form').on('submit', function(evt) {
        evt.preventDefault();
        var action = $('#ride-leader-section form').attr('action');
        var data = {
			'action': 'pwtc_mileage_add_leader',
			'member_id': $('#leader-riderid').html(),
			'ride_id': $('#leader-rideid').val()
		};
		$.post(action, data, add_leader_cb);
    });

    $('#ride-mileage-section form').on('submit', function(evt) {
        evt.preventDefault();
        var action = $('#ride-mileage-section form').attr('action');
        var data = {
			'action': 'pwtc_mileage_add_mileage',
			'member_id': $('#mileage-riderid').html(),
			'ride_id': $('#mileage-rideid').val(),
			'mileage': $('#mileage-amount').val()
		};
		$.post(action, data, add_mileage_cb);
    });

    $('#ride-sheet-back-btn').on('click', function(evt) {
        evt.preventDefault();
		$('#leader-add-btn').hide();
		$('#mileage-add-btn').hide();
		$('#ride-sheet-section').hide();
		$('#ride-lookup-section').show();
    });

	$('#leader-lookup-btn').on('click', function(evt) {
        lookup_pwtc_riders(function(riderid, name) {
            $('#leader-riderid').html(riderid);
            $('#leader-ridername').html(name); 
			$('#leader-add-btn').show();           
        });
    });

	$('#rider-lookup-btn').on('click', function(evt) {
        lookup_pwtc_riders(function(riderid, name) {
            $('#mileage-riderid').html(riderid);
            $('#mileage-ridername').html(name); 
			$('#mileage-amount').val(''); 
			$('#mileage-add-btn').show();           
        });
    });

	$("#ride-lookup-date").datepicker({
  		dateFormat: "yy-mm-dd"
	});

	$('#ride-sheet-section').hide();
	$('#ride-lookup-section').show();
	$('#ride-create-form').hide();
	$('#leader-add-btn').hide(); 
	$('#mileage-add-btn').hide(); 
	load_posts_without_rides();
});
</script>
<div class="wrap">
	<h1><?= esc_html(get_admin_page_title()); ?></h1>
	<div id="ride-lookup-section">
		<form id="ride-lookup-form" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post">
    		<label>Start Date:</label>
			<input id="ride-lookup-date" type="text" name="date" required/>
			<input type="submit" value="Find Rides"/>
		</form>
		<h3></h3>
		<form id="ride-create-form" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post">
            <input id="ride-create-title" type="text" name="title" placeholder="Enter ride title" required/> 
			<input id="ride-create-date" type="hidden"/>
			<input type="submit" value="Create Ride"/>
		</form>
		<table></table>
	</div>
	<div id='ride-sheet-section'>
		<button id='ride-sheet-back-btn'>Back to Rides</button>
		<h2></h2>
		<div id="ride-leader-section">
			<h3>Ride Leaders</h3>
			<form action="<?php echo admin_url('admin-ajax.php'); ?>" method="post">
				<input id="leader-lookup-btn" type="button" value="Lookup Leader"/>
				<span id="leader-add-btn">
					<label id="leader-riderid"/></label>
            		<label id="leader-ridername"></label>
					<input id="leader-rideid" type="hidden"/>
					<input type="submit" value="Add Leader"/>
				</span>
			</form>
			<table class="pretty"></table>
		</div>
		<div id="ride-mileage-section">
			<h3>Rider Mileage</h3>
			<form action="<?php echo admin_url('admin-ajax.php'); ?>" method="post">
				<input id="rider-lookup-btn" type="button" value="Lookup Rider"/>
				<span id="mileage-add-btn">
					<label id="mileage-riderid"/></label>
            		<label id="mileage-ridername"></label>
					<input id="mileage-amount" type="number" min="1" step="1" placeholder="Enter mileage" required/>
					<input id="mileage-rideid" type="hidden"/>
					<input type="submit" value="Add Mileage"/>
				</span>
			</form>
			<table class="pretty"></table>
		</div>
	</div>
<?php
    include('admin-rider-lookup.php');
?>
</div>
<?php
