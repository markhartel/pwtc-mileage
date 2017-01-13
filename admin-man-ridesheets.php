<?php
if (!current_user_can('edit_published_pages')) {
    return;
}
?>
<script type="text/javascript" >
jQuery(document).ready(function($) {  

	var ridesheet_back_btn_cb;

<?php if ($plugin_options['show_ride_ids']) { ?>
	var show_ride_id = true;
<?php } else { ?>
	var show_ride_id = false;
<?php } ?>		

<?php if ($plugin_options['disable_delete_confirm']) { ?>
	var disable_delete_confirm = true;
<?php } else { ?>
	var disable_delete_confirm = false;
<?php } ?>		

	function set_ridesheet_lock(locked) {
		if (locked) {
			$("#ridesheet-sheet-page .leader-tbl .remove-btn").hide();
			$("#ridesheet-sheet-page .mileage-tbl .edit-btn").hide();
			$("#ridesheet-sheet-page .mileage-tbl .remove-btn").hide();
			$("#ridesheet-sheet-page .leader-section .add-frm input[name='lookup']").attr("disabled", "disabled");
			$("#ridesheet-sheet-page .mileage-section .add-frm input[name='lookup']").attr("disabled", "disabled");
		}
		else {
			$("#ridesheet-sheet-page .leader-tbl .remove-btn").show();
       		$("#ridesheet-sheet-page .mileage-tbl .edit-btn").show();
       		$("#ridesheet-sheet-page .mileage-tbl .remove-btn").show();
       		$("#ridesheet-sheet-page .leader-section .add-frm input[name='lookup']").removeAttr("disabled");
       		$("#ridesheet-sheet-page .mileage-section .add-frm input[name='lookup']").removeAttr("disabled");
		}
	}

	function populate_posts_table(posts) {
		$('#ridesheet-post-page .posts-tbl tr').remove();
		if (show_ride_id) {
			$('#ridesheet-post-page .posts-tbl').append(
				'<tr><th>Posted Ride</th><th>Start Date</th><th>Post ID</th><th>Actions</th></tr>');
		}
		else {
			$('#ridesheet-post-page .posts-tbl').append(
				'<tr><th>Posted Ride</th><th>Start Date</th><th>Actions</th></tr>');
		}
		var fmt = new DateFormatter();
		posts.forEach(function(post) {
			var d = fmt.parseDate(post[2], 'Y-m-d');
			var fmtdate = fmt.formatDate(d, 
				'<?php echo $plugin_options['date_display_format']; ?>');
			if (show_ride_id) {
				$('#ridesheet-post-page .posts-tbl').append(
					'<tr postid="' + post[0] + '" ridedate="' + post[2] + '"><td>' +
				 	post[1] + '</td><td>' + fmtdate + '</td><td>' + post[0] + '</td>' +
					' <td><a class="create-btn">Create Sheet</a></td>' + 
					'<td></td></tr>');
			}
			else {
				$('#ridesheet-post-page .posts-tbl').append(
					'<tr postid="' + post[0] + '" ridedate="' + post[2] + '"><td>' +
					post[1] + '</td><td>' + fmtdate + '</td>' + 
					' <td><a class="create-btn">Create Sheet</a></td>' + 
					'<td></td></tr>');
			} 
		});
		$('#ridesheet-post-page .posts-tbl .create-btn').on('click', function(evt) {
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
	}

	function populate_ridesheet_table(rides) {
		$('#ridesheet-ride-page .rides-tbl tr').remove();
		if (show_ride_id) {
       		$('#ridesheet-ride-page .rides-tbl').append(
				'<tr><th>Ride Sheet</th><th>Start Date</th><th>ID</th><th>Post ID</th><th>Actions</th></tr>'); 
		}
		else {
       		$('#ridesheet-ride-page .rides-tbl').append(
				'<tr><th>Ride Sheet</th><th>Start Date</th><th>Actions</th></tr>'); 
		}   
		var fmt = new DateFormatter();
        rides.forEach(function(item) {
			var d = fmt.parseDate(item.date, 'Y-m-d');
			var fmtdate = fmt.formatDate(d, 
				'<?php echo $plugin_options['date_display_format']; ?>');
			if (show_ride_id) {
				$('#ridesheet-ride-page .rides-tbl').append(
					'<tr rideid="' + item.ID + '" ridedate="' + item.date + '"><td>' +
					item.title + '</td><td>' + fmtdate + '</td><td>' + item.ID + '</td><td>' + 
					item.post_id + '</td><td><a class="edit-btn">Edit</a>' + ' ' +
					'<a class="remove-btn">Delete</a></td></tr>'); 
			}
			else {
				$('#ridesheet-ride-page .rides-tbl').append(
					'<tr rideid="' + item.ID + '" ridedate="' + item.date + '"><td>' +
					item.title + '</td><td>' + fmtdate + '</td><td>' + 
					'<a class="edit-btn">Edit</a>' + ' ' +
					'<a class="remove-btn">Delete</a></td></tr>'); 
			}   
        });
		$('#ridesheet-ride-page .rides-tbl .edit-btn').on('click', function(evt) {
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
		$('#ridesheet-ride-page .rides-tbl .remove-btn').on('click', function(evt) {
            evt.preventDefault();
            var action = '<?php echo admin_url('admin-ajax.php'); ?>';
            var data = {
			    'action': 'pwtc_mileage_remove_ride',
                'ride_id': $(this).parent().parent().attr('rideid'),
				'title': $("#ridesheet-ride-page .ride-search-frm input[name='title']").val(),
				'startdate': $("#ridesheet-ride-page .ride-search-frm input[name='fmtdate']").val(),
				'enddate': $("#ridesheet-ride-page .ride-search-frm input[name='tofmtdate']").val()
		    };
			if (disable_delete_confirm) {
				$.post(action, data, remove_ride_cb);
			} 
			else {
				open_confirm_dialog(
					'Are you sure you want to delete ride "' + 
						$(this).parent().parent().find('td').first().html() + '"?', 
					function() {
						$.post(action, data, remove_ride_cb);
					}
				);
			}		
		});
	}

	function populate_ride_leader_table(ride_id, leaders) {
		$('#ridesheet-sheet-page .leader-tbl tr').remove();
		$('#ridesheet-sheet-page .leader-tbl').append(
			'<tr><th>Rider ID</th><th>Name</th><th>Actions</th></tr>');
        leaders.forEach(function(item) {
            $('#ridesheet-sheet-page .leader-tbl').append(
				'<tr rideid="' + ride_id + '" memberid="' + item.member_id + '">' + 
				'<td>' + item.member_id + '</td>' +
				'<td>' + item.first_name + ' ' + item.last_name + '</td>' + 
				'<td><a class="remove-btn">Delete</a></td></tr>');    
		});
		$('#ridesheet-sheet-page .leader-tbl .remove-btn').on('click', function(evt) {
            evt.preventDefault();
            var action = '<?php echo admin_url('admin-ajax.php'); ?>';
            var data = {
			    'action': 'pwtc_mileage_remove_leader',
                'ride_id': $(this).parent().parent().attr('rideid'),
				'member_id': $(this).parent().parent().attr('memberid')
		    };
			if (disable_delete_confirm) {
				$.post(action, data, remove_leader_cb);
			}
			else {
				open_confirm_dialog(
					'Are you sure you want to delete ride leader ' + data.member_id + '?', 
					function() {
						$.post(action, data, remove_leader_cb);
					}
				);
			}		
		});
	}

	function populate_ride_mileage_table(ride_id, mileage) {
		$('#ridesheet-sheet-page .mileage-tbl tr').remove();
		$('#ridesheet-sheet-page .mileage-tbl').append(
			'<tr><th>Rider ID</th><th>Name</th><th>Mileage</th><th>Actions</th></tr>');
        mileage.forEach(function(item) {
            $('#ridesheet-sheet-page .mileage-tbl').append(
				'<tr rideid="' + ride_id + '" memberid="' + item.member_id + '">' + 
				'<td>' + item.member_id + '</td>' +
				'<td>' + item.first_name + ' ' + item.last_name + '</td>' + 
				'<td>' + item.mileage + '</td>' +
				'<td><a class="edit-btn">Edit</a>' + ' ' +
				'<a class="remove-btn">Delete</a></td></tr>');    
		});
		$('#ridesheet-sheet-page .mileage-tbl .edit-btn').on('click', function(evt) {
            evt.preventDefault();
            $('#ridesheet-sheet-page .mileage-section .add-frm .riderid').html(
				$(this).parent().parent().attr('memberid')
			);
            $('#ridesheet-sheet-page .mileage-section .add-frm .ridername').html(
				$(this).parent().parent().find('td').eq(1).html()
			);
			$("#ridesheet-sheet-page .mileage-section .add-frm input[name='mileage']").val(
				$(this).parent().parent().find('td').eq(2).html()
			); 
			$('#ridesheet-sheet-page .mileage-section .add-blk').show('slow');  
			$("#ridesheet-sheet-page .mileage-section .add-frm input[name='mileage']").focus();         
		});
		$('#ridesheet-sheet-page .mileage-tbl .remove-btn').on('click', function(evt) {
            evt.preventDefault();
            var action = '<?php echo admin_url('admin-ajax.php'); ?>';
            var data = {
			    'action': 'pwtc_mileage_remove_mileage',
                'ride_id': $(this).parent().parent().attr('rideid'),
				'member_id': $(this).parent().parent().attr('memberid')
		    };
			if (disable_delete_confirm) {
				$.post(action, data, remove_mileage_cb);
			}
			else {
				open_confirm_dialog(
					'Are you sure you want to delete the mileage for rider ' + data.member_id + '?', 
					function() {
						$.post(action, data, remove_mileage_cb);
					}
				);
			}		
		});
	}

	function lookup_posts_cb(response) {
        var res = JSON.parse(response);
		populate_posts_table(res.posts);
		$('#ridesheet-main-page').hide('fast', function() {
			$('#ridesheet-post-page').fadeIn('slow');
		});
	}   

	function lookup_rides_cb(response) {
        var res = JSON.parse(response);
		if (res.error) {
			open_error_dialog(res.error);
		}
		else {
			populate_ridesheet_table(res.rides);
			$('#ridesheet-ride-page .rides-section').show();
		}
	}   

	function create_ride_cb(response) {
        var res = JSON.parse(response);
		if (res.error) {
			open_error_dialog(res.error);
		}
		else {
 			var fmt = new DateFormatter();
			var fmtdate = fmt.formatDate(fmt.parseDate(res.startdate, 'Y-m-d'), 
				'<?php echo $plugin_options['date_display_format']; ?>');
			$('#ridesheet-sheet-page h2').html(res.title + ' (' + fmtdate + ')');
			$("#ridesheet-sheet-page .leader-section .add-frm input[name='rideid']").val(res.ride_id); 
			$("#ridesheet-sheet-page .mileage-section .add-frm input[name='rideid']").val(res.ride_id); 
			populate_ride_leader_table(res.ride_id, res.leaders);
			populate_ride_mileage_table(res.ride_id, res.mileage);
			$("#ridesheet-sheet-page .leader-section .add-blk").hide(); 
			$("#ridesheet-sheet-page .mileage-section .add-blk").hide(); 
			ridesheet_back_btn_cb = function() {
				$('#ridesheet-main-page .add-blk').hide();
				$('#ridesheet-main-page').fadeIn('slow');
			};
			set_ridesheet_lock(false);
			$('#ridesheet-main-page').hide('fast', function() {
				$('#ridesheet-sheet-page').fadeIn('slow');
				$('#ridesheet-sheet-page .back-btn').focus();
			});
		}
	} 

	function create_ride_from_event_cb(response) {
        var res = JSON.parse(response);
		console.log(res);
		if (res.error) {
			open_error_dialog(res.error);
		}
		else {
 			var fmt = new DateFormatter();
			var fmtdate = fmt.formatDate(fmt.parseDate(res.startdate, 'Y-m-d'), 
				'<?php echo $plugin_options['date_display_format']; ?>');
			$('#ridesheet-sheet-page h2').html(res.title + ' (' + fmtdate + ')');
			$("#ridesheet-sheet-page .leader-section .add-frm input[name='rideid']").val(res.ride_id); 
			$("#ridesheet-sheet-page .mileage-section .add-frm input[name='rideid']").val(res.ride_id); 
			populate_ride_leader_table(res.ride_id, res.leaders);
			populate_ride_mileage_table(res.ride_id, res.mileage);
			$("#ridesheet-sheet-page .leader-section .add-blk").hide(); 
			$("#ridesheet-sheet-page .mileage-section .add-blk").hide(); 
			ridesheet_back_btn_cb = function() {
				load_posts_without_rides();
			};
			set_ridesheet_lock(false);
			$('#ridesheet-post-page').hide('fast', function() {
				$('#ridesheet-sheet-page').fadeIn('slow');
				$('#ridesheet-sheet-page .back-btn').focus();
			});
		}
	}   

	function lookup_ridesheet_cb(response) {
        var res = JSON.parse(response);
 		var fmt = new DateFormatter();
		var fmtdate = fmt.formatDate(fmt.parseDate(res.startdate, 'Y-m-d'), 
			'<?php echo $plugin_options['date_display_format']; ?>');
		$('#ridesheet-sheet-page h2').html(res.title + ' (' + fmtdate + ')');
		$("#ridesheet-sheet-page .leader-section .add-frm input[name='rideid']").val(res.ride_id); 
		$("#ridesheet-sheet-page .mileage-section .add-frm input[name='rideid']").val(res.ride_id); 
		populate_ride_leader_table(res.ride_id, res.leaders);
		populate_ride_mileage_table(res.ride_id, res.mileage);
		$("#ridesheet-sheet-page .leader-section .add-blk").hide(); 
		$("#ridesheet-sheet-page .mileage-section .add-blk").hide(); 
		ridesheet_back_btn_cb = function() {
			$('#ridesheet-ride-page').fadeIn('slow');
		};
		set_ridesheet_lock(res.title.startsWith('Totals Through '));
		$('#ridesheet-ride-page').hide('fast', function() {
			$('#ridesheet-sheet-page').fadeIn('slow');
			$('#ridesheet-sheet-page .back-btn').focus();
		});
	}   

	function remove_ride_cb(response) {
		var res = JSON.parse(response);
		if (res.error) {
			open_error_dialog(res.error);
		}
		else {
			populate_ridesheet_table(res.rides);
		}
	}

	function remove_leader_cb(response) {
		var res = JSON.parse(response);
		if (res.error) {
			open_error_dialog(res.error);
		}
		else {
			populate_ride_leader_table(res.ride_id, res.leaders);
		}
	}

	function remove_mileage_cb(response) {
		var res = JSON.parse(response);
		if (res.error) {
			open_error_dialog(res.error);
		}
		else {
			populate_ride_mileage_table(res.ride_id, res.mileage);
		}
	}

	function add_leader_cb(response) {
		var res = JSON.parse(response);
		if (res.error) {
			open_error_dialog(res.error);
		}
		else {
			$('#ridesheet-sheet-page .leader-section .add-blk').hide();
			populate_ride_leader_table(res.ride_id, res.leaders);
			$("#ridesheet-sheet-page .leader-section .add-frm input[name='lookup']").focus();
		}
	}

	function add_mileage_cb(response) {
		var res = JSON.parse(response);
		if (res.error) {
			open_error_dialog(res.error);
		}
		else {
			$('#ridesheet-sheet-page .mileage-section .add-blk').hide();
			populate_ride_mileage_table(res.ride_id, res.mileage);
			$("#ridesheet-sheet-page .mileage-section .add-frm input[name='lookup']").focus();
		}
	}

	function load_posts_without_rides() {
        var action = '<?php echo admin_url('admin-ajax.php'); ?>';
        var data = {
			'action': 'pwtc_mileage_lookup_posts'
		};
		$.post(action, data, lookup_posts_cb);
	}

	$('#ridesheet-main-page .create-btn').on('click', function(evt) {
		load_posts_without_rides();
	});

	$('#ridesheet-main-page .modify-btn').on('click', function(evt) {
		$('#ridesheet-main-page').hide('fast', function() {
			$('#ridesheet-ride-page').fadeIn('slow');
		});
	});

	/* Back button click handler for posts page. */
	$('#ridesheet-post-page .back-btn').on('click', function(evt) {
		$('#ridesheet-post-page').hide('fast', function() {
			$('#ridesheet-main-page').fadeIn('slow');
		});
	});

	/* Back button click handler for rides page. */
	$('#ridesheet-ride-page .back-btn').on('click', function(evt) {
		$('#ridesheet-ride-page').hide('fast', function() {
			$('#ridesheet-main-page').fadeIn('slow');
		});
		$('#ridesheet-ride-page .rides-tbl tr').remove();
		$("#ridesheet-ride-page .ride-search-frm input[name='date']").val('');
		$("#ridesheet-ride-page .ride-search-frm input[name='todate']").val('');
		$("#ridesheet-ride-page .ride-search-frm input[name='title']").val('');
	});

	/* Back button click handler for sheets page. */
	$('#ridesheet-sheet-page .back-btn').on('click', function(evt) {
		$('#ridesheet-sheet-page').hide('fast', function() {
			ridesheet_back_btn_cb();
		});
	});

    $('#ridesheet-ride-page .ride-search-frm').on('submit', function(evt) {
        evt.preventDefault();
        var action = $('#ridesheet-ride-page .ride-search-frm').attr('action');
        var data = {
			'action': 'pwtc_mileage_lookup_rides',
			'title': $("#ridesheet-ride-page .ride-search-frm input[name='title']").val(),
			'startdate': $("#ridesheet-ride-page .ride-search-frm input[name='fmtdate']").val(),
			'enddate': $("#ridesheet-ride-page .ride-search-frm input[name='tofmtdate']").val()
		};
		$.post(action, data, lookup_rides_cb);
    });
	
    $('#ridesheet-sheet-page .leader-section .add-frm').on('submit', function(evt) {
        evt.preventDefault();
        var action = $('#ridesheet-sheet-page .leader-section .add-frm').attr('action');
        var data = {
			'action': 'pwtc_mileage_add_leader',
			'member_id': $('#ridesheet-sheet-page .leader-section .add-frm .riderid').html(),
			'ride_id': $("#ridesheet-sheet-page .leader-section .add-frm input[name='rideid']").val()
		};
		$.post(action, data, add_leader_cb);
    });

    $('#ridesheet-sheet-page .mileage-section .add-frm').on('submit', function(evt) {
        evt.preventDefault();
        var action = $('#ridesheet-sheet-page .mileage-section .add-frm').attr('action');
        var data = {
			'action': 'pwtc_mileage_add_mileage',
			'member_id': $('#ridesheet-sheet-page .mileage-section .add-frm .riderid').html(),
			'ride_id': $("#ridesheet-sheet-page .mileage-section .add-frm input[name='rideid']").val(),
			'mileage': $("#ridesheet-sheet-page .mileage-section .add-frm input[name='mileage']").val()
		};
		$.post(action, data, add_mileage_cb);
    });

	$("#ridesheet-sheet-page .leader-section .add-frm input[name='lookup']").on('click', function(evt) {
        lookup_pwtc_riders(function(riderid, name) {
            $('#ridesheet-sheet-page .leader-section .add-frm .riderid').html(riderid);
            $('#ridesheet-sheet-page .leader-section .add-frm .ridername').html(name); 
			$('#ridesheet-sheet-page .leader-section .add-blk').show('slow'); 
			$("#ridesheet-sheet-page .leader-section .add-frm input[type='submit']").focus();          
        });
    });

	$("#ridesheet-sheet-page .leader-section .add-frm .cancel-btn").on('click', function(evt) {
 		$('#ridesheet-sheet-page .leader-section .add-blk').hide();           
		$("#ridesheet-sheet-page .leader-section .add-frm input[name='lookup']").focus();
    });

	$("#ridesheet-sheet-page .mileage-section .add-frm input[name='lookup']").on('click', function(evt) {
        lookup_pwtc_riders(function(riderid, name) {
            $('#ridesheet-sheet-page .mileage-section .add-frm .riderid').html(riderid);
            $('#ridesheet-sheet-page .mileage-section .add-frm .ridername').html(name); 
			$("#ridesheet-sheet-page .mileage-section .add-frm input[name='mileage']").val(''); 
			$('#ridesheet-sheet-page .mileage-section .add-blk').show('slow');  
			$("#ridesheet-sheet-page .mileage-section .add-frm input[name='mileage']").focus();         
        });
    });

	$("#ridesheet-sheet-page .mileage-section .add-frm .cancel-btn").on('click', function(evt) {
 		$('#ridesheet-sheet-page .mileage-section .add-blk').hide();           
		$("#ridesheet-sheet-page .mileage-section .add-frm input[name='lookup']").focus();
    });

	$("#ridesheet-main-page .add-btn").on('click', function(evt) {
		$("#ridesheet-main-page .add-blk .add-frm input[type='text']").val(''); 
		$('#ridesheet-main-page .add-blk').show('slow'); 
		$("#ridesheet-main-page .add-blk .add-frm input[name='title']").focus();          
    });

	$("#ridesheet-main-page .add-blk .cancel-btn").on('click', function(evt) {
		$('#ridesheet-main-page .add-blk').hide();
    });

	$('#ridesheet-main-page .add-blk .add-frm').on('submit', function(evt) {
        evt.preventDefault();
        var action = $('#ridesheet-main-page .add-blk .add-frm').attr('action');
        var data = {
			'action': 'pwtc_mileage_create_ride',
			'title': $("#ridesheet-main-page .add-blk .add-frm input[name='title']").val(),
			'startdate': $("#ridesheet-main-page .add-blk .add-frm input[name='fmtdate']").val(),
		};
		$.post(action, data, create_ride_cb);
    });

	$("#ridesheet-main-page .add-blk .add-frm input[name='date']").datepicker({
  		dateFormat: 'D M d yy',
		altField: "#ridesheet-main-page .add-blk .add-frm input[name='fmtdate']",
		altFormat: 'yy-mm-dd',
		changeMonth: true,
      	changeYear: true
	});

	function getDate( element ) {
    	var date;
      	try {
        	date = $.datepicker.parseDate('D M d yy', element.value);
      	} catch( error ) {
        	date = null;
      	}
     	return date;
    }

	var fromDate = $("#ridesheet-ride-page .ride-search-frm input[name='date']").datepicker({
  		dateFormat: 'D M d yy',
		altField: "#ridesheet-ride-page .ride-search-frm input[name='fmtdate']",
		altFormat: 'yy-mm-dd',
		changeMonth: true,
      	changeYear: true
	}).on( "change", function() {
        toDate.datepicker("option", "minDate", getDate(this));
    });

	var toDate = $("#ridesheet-ride-page .ride-search-frm input[name='todate']").datepicker({
  		dateFormat: 'D M d yy',
		altField: "#ridesheet-ride-page .ride-search-frm input[name='tofmtdate']",
		altFormat: 'yy-mm-dd',
		changeMonth: true,
      	changeYear: true
	}).on( "change", function() {
        fromDate.datepicker("option", "maxDate", getDate(this));
    });

	$('#ridesheet-main-page .create-btn').focus();

});
</script>
<div class="wrap">
	<h1><?= esc_html(get_admin_page_title()); ?></h1>
<?php
if ($running_jobs > 0) {
?>
    <div class="notice notice-warning"><p><strong>
        A database batch operation is currently running!
    </strong></p></div>
<?php
} else {
?>
	<div id="ridesheet-main-page">
		<p>
        <div><strong>Create Ride Sheets from Posted Rides</strong></div>
        <div><button class="create-btn button button-primary button-large">Create</button></div><br>
        <div><strong>Edit Existing Ride Sheets</strong></div>
        <div><button class="modify-btn button button-primary button-large">Edit</button></div><br>
        <div><strong>Add a New Ride Sheet</strong></div>
        <div><button class="add-btn button button-primary button-large">New</button>
		<span class="add-blk initially-hidden">
			<form class="add-frm" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post">
				<table>
				<tr><td>Ride Title:</td><td><input name="title" type="text" required/></td></tr>
				<tr><td>Start Date:</td><td><input name="date" type="text" required/></td></tr>
				</table>
				<input type="hidden" name="fmtdate"/>
				<input class="button button-primary" type="submit" value="Create"/>
				<input class="cancel-btn button button-primary" type="button" value="Cancel"/>
			</form>
		</span></div>
		</p>
	</div>
	<div id="ridesheet-post-page" class="initially-hidden">
		<p><button class='back-btn button button-primary button-large'>Back</button></p>
		<p>
			<h3>Posted Rides without Ride Sheets</h3>
			<table class="posts-tbl pretty"></table>
		</p>
	</div>
	<div id="ridesheet-ride-page" class="initially-hidden">
		<p><button class='back-btn button button-primary button-large'>Back</button></p>
		<p><form class="ride-search-frm" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post">
			<table>
    			<tr><td><label>Title:</label></td>
					<td><input type="text" name="title"/></td>
				</tr>
    			<tr><td><label>Start Date:</label></td>
					<td><input type="text" name="date" required/><label> to</label>
						<input type="text" name="todate" required/>
					</td>
				</tr>
			</table>
			<input type="hidden" name="fmtdate"/>
			<input type="hidden" name="tofmtdate"/>
			<input class="button button-primary" type="submit" value="Search"/>
		</form></p>	
		<div class="rides-section initially-hidden"><p>
			<h3></h3>
			<table class="rides-tbl pretty"></table>
		</p></div>
	</div>
	<div id='ridesheet-sheet-page' class="initially-hidden">
		<p><button class='back-btn button button-primary button-large'>Back</button></p>
		<h2></h2>
		<div class="leader-section">
			<h3>Ride Leaders</h3>
			<p><form class="add-frm" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post">
				<input class="button button-primary" name="lookup" type="button" value="Lookup Leader"/>
				<span class="add-blk initially-hidden">
					<table>
					<tr><td>ID:</td><td><label class="riderid"></label></td></tr>
            		<tr><td>Name:</td><td><label class="ridername"></label></td></tr>
					</table>
					<input name="rideid" type="hidden"/>
					<input class="button button-primary" type="submit" value="Add Leader"/>
					<input class="cancel-btn button button-primary" type="button" value="Cancel"/>
				</span>
			</form></p>
			<table class="leader-tbl pretty"></table>
		</div>
		<div class="mileage-section">
			<h3>Rider Mileage</h3>
			<p><form class="add-frm" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post">
				<input class="button button-primary" name="lookup" type="button" value="Lookup Rider"/>
				<span class="add-blk initially-hidden">
					<table>
					<tr><td>ID:</td><td><label class="riderid"></label></td></tr>
            		<tr><td>Name:</td><td><label class="ridername"></label></td></tr>
					<tr><td>Mileage:</td><td><input name="mileage" type="text" required/></td></tr>
					</table>
					<input name="rideid" type="hidden"/>
					<input class="button button-primary" type="submit" value="Add Mileage"/>
					<input class="cancel-btn button button-primary" type="button" value="Cancel"/>
				</span>
			</form></p>
			<table class="mileage-tbl pretty"></table>
		</div>
	</div>
<?php
	include('admin-rider-lookup.php');
}
?>
</div>
<?php
