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
			$("#ridesheet-sheet-page .leader-div .remove-btn").hide();
			$("#ridesheet-sheet-page .mileage-div .edit-btn").hide();
			$("#ridesheet-sheet-page .mileage-div .remove-btn").hide();
			$("#ridesheet-sheet-page .leader-section .lookup-btn").attr("disabled", "disabled");
			$("#ridesheet-sheet-page .mileage-section .lookup-btn").attr("disabled", "disabled");
		}
		else {
			$("#ridesheet-sheet-page .leader-div .remove-btn").show();
       		$("#ridesheet-sheet-page .mileage-div .edit-btn").show();
       		$("#ridesheet-sheet-page .mileage-div .remove-btn").show();
       		$("#ridesheet-sheet-page .leader-section .lookup-btn").removeAttr("disabled");
       		$("#ridesheet-sheet-page .mileage-section .lookup-btn").removeAttr("disabled");
		}
	}

	function populate_posts_table(posts) {
		$('#ridesheet-post-page .posts-div').empty();
		if (posts.length > 0) {
			$('#ridesheet-post-page .posts-div').append('<table class="rwd-table"></table>');
			if (show_ride_id) {
				$('#ridesheet-post-page .posts-div table').append(
					'<tr><th>Posted Ride</th><th>Start Date</th><th>Post ID</th><th>Actions</th></tr>');
			}
			else {
				$('#ridesheet-post-page .posts-div table').append(
					'<tr><th>Posted Ride</th><th>Start Date</th><th>Actions</th></tr>');
			}
			var fmt = new DateFormatter();
			posts.forEach(function(post) {
				var fmtdate = getPrettyDate(post[2]);
				if (show_ride_id) {
					$('#ridesheet-post-page .posts-div table').append(
						'<tr postid="' + post[0] + '" ridedate="' + post[2] + '"><td data-th="Ride">' +
						post[1] + '</td><td data-th="Date">' + fmtdate + '</td><td data-th="ID">' + post[0] + '</td>' +
						' <td data-th="Actions"><a class="create-btn">Create Sheet</a></td>' + 
						'</tr>');
				}
				else {
					$('#ridesheet-post-page .posts-div table').append(
						'<tr postid="' + post[0] + '" ridedate="' + post[2] + '"><td data-th="Ride">' +
						post[1] + '</td><td data-th="Date">' + fmtdate + '</td>' + 
						' <td data-th="Actions"><a class="create-btn">Create Sheet</a></td>' + 
						'</tr>');
				} 
			});
			$('#ridesheet-post-page .posts-div .create-btn').on('click', function(evt) {
				evt.preventDefault();
				var action = '<?php echo admin_url('admin-ajax.php'); ?>';
				var data = {
					'action': 'pwtc_mileage_create_ride_from_event',
					'post_id': $(this).parent().parent().attr('postid'),
					'startdate': $(this).parent().parent().attr('ridedate'),
					'title': $(this).parent().parent().find('td').first().html(),
					'nonce': '<?php echo wp_create_nonce('pwtc_mileage_create_ride_from_event'); ?>'
				};
				$.post(action, data, create_ride_from_event_cb);
			});
		}
		else {
			$('#ridesheet-post-page .posts-div').append(
				'<span class="empty-tbl">No missing ride sheets found!</span>');
		}
	}

	function populate_ridesheet_table(rides) {
		$('#ridesheet-ride-page .rides-div').empty();
		if (rides.length > 0) {
			$('#ridesheet-ride-page .rides-div').append('<table class="rwd-table"></table>');
			if (show_ride_id) {
				$('#ridesheet-ride-page .rides-div table').append(
					'<tr><th>Ride Sheet</th><th>Start Date</th><th>ID</th><th>Post ID</th><th>Actions</th></tr>'); 
			}
			else {
				$('#ridesheet-ride-page .rides-div table').append(
					'<tr><th>Ride Sheet</th><th>Start Date</th><th>Actions</th></tr>'); 
			}   
			var fmt = new DateFormatter();
			rides.forEach(function(item) {
				var fmtdate = getPrettyDate(item.date);
				if (show_ride_id) {
					$('#ridesheet-ride-page .rides-div table').append(
						'<tr rideid="' + item.ID + '" ridedate="' + item.date + '"><td data-th="Ride">' +
						item.title + '</td><td data-th="Date">' + fmtdate + '</td><td data-th="ID">' + 
						item.ID + '</td><td data-th="Post ID">' + 
						item.post_id + '</td><td data-th="Actions"><a class="edit-btn">Edit</a>' + ' ' +
						'<a class="remove-btn">Delete</a></td></tr>'); 
				}
				else {
					$('#ridesheet-ride-page .rides-div table').append(
						'<tr rideid="' + item.ID + '" ridedate="' + item.date + '"><td data-th="Ride">' +
						item.title + '</td><td data-th="Date">' + fmtdate + '</td><td data-th="Actions">' + 
						'<a class="edit-btn">Edit</a>' + ' ' +
						'<a class="remove-btn">Delete</a></td></tr>'); 
				}   
			});
			$('#ridesheet-ride-page .rides-div .edit-btn').on('click', function(evt) {
				evt.preventDefault();
				var action = '<?php echo admin_url('admin-ajax.php'); ?>';
				var data = {
					'action': 'pwtc_mileage_lookup_ridesheet',
					'ride_id': $(this).parent().parent().attr('rideid')
				};
				$.post(action, data, lookup_ridesheet_cb);
			});
			$('#ridesheet-ride-page .rides-div .remove-btn').on('click', function(evt) {
				evt.preventDefault();
				var action = '<?php echo admin_url('admin-ajax.php'); ?>';
				var data = {
					'action': 'pwtc_mileage_remove_ride',
					'ride_id': $(this).parent().parent().attr('rideid'),
					'nonce': '<?php echo wp_create_nonce('pwtc_mileage_remove_ride'); ?>'
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
		else {
			$('#ridesheet-ride-page .rides-div').append(
				'<span class="empty-tbl">No ridesheets found!</span>');
		}
	}

	function populate_ride_leader_table(ride_id, leaders) {
		$('#ridesheet-sheet-page .leader-div').empty();
		if (leaders.length) {
			$('#ridesheet-sheet-page .leader-div').append(
				'<table class="rwd-table"><tr><th>ID</th><th>Name</th><th>Actions</th></tr></table>');
			leaders.forEach(function(item) {
				$('#ridesheet-sheet-page .leader-div table').append(
					'<tr rideid="' + ride_id + '" memberid="' + item.member_id + '">' + 
					'<td data-th="ID">' + item.member_id + '</td>' +
					'<td data-th="Name">' + item.first_name + ' ' + item.last_name + '</td>' + 
					'<td data-th="Actions"><a class="remove-btn">Delete</a></td></tr>');    
			});
			$('#ridesheet-sheet-page .leader-div .remove-btn').on('click', function(evt) {
				evt.preventDefault();
				var action = '<?php echo admin_url('admin-ajax.php'); ?>';
				var data = {
					'action': 'pwtc_mileage_remove_leader',
					'ride_id': $(this).parent().parent().attr('rideid'),
					'member_id': $(this).parent().parent().attr('memberid'),
					'nonce': '<?php echo wp_create_nonce('pwtc_mileage_remove_leader'); ?>'
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
		else {
			$('#ridesheet-sheet-page .leader-div').append(
				'<span class="empty-tbl">No leaders entered!</span>');
		}
	}

	function populate_ride_mileage_table(ride_id, mileage) {
		$('#ridesheet-sheet-page .mileage-div').empty();
		if (mileage.length > 0) {
			$('#ridesheet-sheet-page .mileage-div').append(
				'<table class="rwd-table"><tr><th>ID</th><th>Name</th><th>Mileage</th><th>Actions</th></tr></table>');
			mileage.forEach(function(item) {
				$('#ridesheet-sheet-page .mileage-div table').append(
					'<tr rideid="' + ride_id + '" memberid="' + item.member_id + '">' + 
					'<td data-th="ID">' + item.member_id + '</td>' +
					'<td data-th="Name">' + item.first_name + ' ' + item.last_name + '</td>' + 
					'<td data-th="Mileage">' + item.mileage + '</td>' +
					'<td data-th="Actions"><a class="edit-btn">Edit</a>' + ' ' +
					'<a class="remove-btn">Delete</a></td></tr>');    
			});
			$('#ridesheet-sheet-page .mileage-div .edit-btn').on('click', function(evt) {
				evt.preventDefault();
				$("#ridesheet-sheet-page .mileage-section .add-frm input[name='riderid']").val(
					$(this).parent().parent().attr('memberid')
				);
				$("#ridesheet-sheet-page .mileage-section .add-frm input[name='ridername']").val(
					$(this).parent().parent().find('td').eq(1).html()
				);
				$("#ridesheet-sheet-page .mileage-section .add-frm input[name='mileage']").val(
					$(this).parent().parent().find('td').eq(2).html()
				); 
				$('#ridesheet-sheet-page .mileage-section .lookup-btn').hide('fast', function() {
					$('#ridesheet-sheet-page .mileage-section .add-blk').show('slow');  
					$("#ridesheet-sheet-page .mileage-section .add-frm input[name='mileage']").focus();         
				});
			});
			$('#ridesheet-sheet-page .mileage-div .remove-btn').on('click', function(evt) {
				evt.preventDefault();
				var action = '<?php echo admin_url('admin-ajax.php'); ?>';
				var data = {
					'action': 'pwtc_mileage_remove_mileage',
					'ride_id': $(this).parent().parent().attr('rideid'),
					'member_id': $(this).parent().parent().attr('memberid'),
					'nonce': '<?php echo wp_create_nonce('pwtc_mileage_remove_mileage'); ?>'
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
		else {
			$('#ridesheet-sheet-page .mileage-div').append(
				'<span class="empty-tbl">No mileage entered!</span>');
		}
	}

	function lookup_posts_cb(response) {
        var res = JSON.parse(response);
		populate_posts_table(res.posts);
	}   

	function lookup_rides_cb(response) {
        var res = JSON.parse(response);
		if (res.error) {
			open_error_dialog(res.error);
		}
		else {
			populate_ridesheet_table(res.rides);
		}
	}   

	function create_ride_cb(response) {
        var res = JSON.parse(response);
		if (res.error) {
			open_error_dialog(res.error);
		}
		else {
			var fmtdate = getPrettyDate(res.startdate);
			$('#ridesheet-sheet-page h2').html(res.title + ' (' + fmtdate + ')');
			$("#ridesheet-sheet-page .leader-section .add-frm input[name='rideid']").val(res.ride_id); 
			$("#ridesheet-sheet-page .mileage-section .add-frm input[name='rideid']").val(res.ride_id); 
			populate_ride_leader_table(res.ride_id, res.leaders);
			populate_ride_mileage_table(res.ride_id, res.mileage);
			$("#ridesheet-sheet-page .leader-section .lookup-btn").show();
			$("#ridesheet-sheet-page .leader-section .add-blk").hide(); 
			$("#ridesheet-sheet-page .mileage-section .lookup-btn").show();
			$("#ridesheet-sheet-page .mileage-section .add-blk").hide(); 
			ridesheet_back_btn_cb = function() {
				$('#ridesheet-ride-page .add-blk').hide();
				$('#ridesheet-ride-page .add-btn').show();
				$('#ridesheet-ride-page').fadeIn('slow');
				load_ride_table();
			};
			set_ridesheet_lock(false);
			$('#ridesheet-ride-page').hide('fast', function() {
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
			var fmtdate = getPrettyDate(res.startdate);
			$('#ridesheet-sheet-page h2').html(res.title + ' (' + fmtdate + ')');
			$("#ridesheet-sheet-page .leader-section .add-frm input[name='rideid']").val(res.ride_id); 
			$("#ridesheet-sheet-page .mileage-section .add-frm input[name='rideid']").val(res.ride_id); 
			populate_ride_leader_table(res.ride_id, res.leaders);
			populate_ride_mileage_table(res.ride_id, res.mileage);
			$("#ridesheet-sheet-page .leader-section .lookup-btn").show();
			$("#ridesheet-sheet-page .leader-section .add-blk").hide(); 
			$("#ridesheet-sheet-page .mileage-section .lookup-btn").show();
			$("#ridesheet-sheet-page .mileage-section .add-blk").hide(); 
			ridesheet_back_btn_cb = function() {
				$('#ridesheet-post-page').fadeIn('slow');
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
		var fmtdate = getPrettyDate(res.startdate);
		$('#ridesheet-sheet-page h2').html(res.title + ' (' + fmtdate + ')');
		$("#ridesheet-sheet-page .leader-section .add-frm input[name='rideid']").val(res.ride_id); 
		$("#ridesheet-sheet-page .mileage-section .add-frm input[name='rideid']").val(res.ride_id); 
		populate_ride_leader_table(res.ride_id, res.leaders);
		populate_ride_mileage_table(res.ride_id, res.mileage);
		$("#ridesheet-sheet-page .leader-section .lookup-btn").show();
		$("#ridesheet-sheet-page .leader-section .add-blk").hide(); 
		$("#ridesheet-sheet-page .mileage-section .lookup-btn").show();
		$("#ridesheet-sheet-page .mileage-section .add-blk").hide(); 
		ridesheet_back_btn_cb = function() {
			$('#ridesheet-ride-page .add-blk').hide();
			$('#ridesheet-ride-page .add-btn').show();
			$('#ridesheet-ride-page').fadeIn('slow');
			load_ride_table();
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
			load_ride_table();
		}
	}

	function load_ride_table() {
		var title = $("#ridesheet-ride-page .ride-search-frm input[name='title']").val().trim();
		var startdate = $("#ridesheet-ride-page .ride-search-frm input[name='fmtdate']").val().trim();
		var enddate = $("#ridesheet-ride-page .ride-search-frm input[name='tofmtdate']").val().trim();
        if (title.length > 0 || startdate.length > 0 || enddate.length > 0) {
			var action = $('#ridesheet-ride-page .ride-search-frm').attr('action');
			var data = {
				'action': 'pwtc_mileage_lookup_rides',
				'title': title,
				'startdate': startdate,
				'enddate': enddate
			};
			$.post(action, data, lookup_rides_cb);
		}
		else {
			$('#ridesheet-ride-page .rides-div').empty();
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
			populate_ride_leader_table(res.ride_id, res.leaders);
			$('#ridesheet-sheet-page .leader-section .add-blk').hide('fast', function() {
				$("#ridesheet-sheet-page .leader-section .lookup-btn").show('slow');
				$("#ridesheet-sheet-page .leader-section .lookup-btn").focus();
			});
		}
	}

	function add_mileage_cb(response) {
		var res = JSON.parse(response);
		if (res.error) {
			open_error_dialog(res.error);
		}
		else {
			populate_ride_mileage_table(res.ride_id, res.mileage);
			$('#ridesheet-sheet-page .mileage-section .add-blk').hide('fast', function() {
				$("#ridesheet-sheet-page .mileage-section .lookup-btn").show('slow');
				$("#ridesheet-sheet-page .mileage-section .lookup-btn").focus();
			});
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
		$('#ridesheet-main-page').hide('fast', function() {
			$('#ridesheet-post-page').fadeIn('slow');
		});
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
		$('#ridesheet-ride-page .rides-div').empty();
		$("#ridesheet-ride-page .ride-search-frm input[name='date']").val('');
		$("#ridesheet-ride-page .ride-search-frm input[name='todate']").val('');
		$("#ridesheet-ride-page .ride-search-frm input[name='title']").val('');
		$('#ridesheet-ride-page .add-btn').show();
		$('#ridesheet-ride-page .add-blk').hide();
	});

	/* Back button click handler for sheets page. */
	$('#ridesheet-sheet-page .back-btn').on('click', function(evt) {
		$('#ridesheet-sheet-page').hide('fast', function() {
			ridesheet_back_btn_cb();
		});
	});

    $('#ridesheet-ride-page .ride-search-frm').on('submit', function(evt) {
        evt.preventDefault();
		load_ride_table();
    });

	$('#ridesheet-ride-page .ride-search-frm .reset-btn').on('click', function(evt) {
        evt.preventDefault();
        $("#ridesheet-ride-page .ride-search-frm input[type='text']").val(''); 
        $("#ridesheet-ride-page .ride-search-frm input[type='hidden']").val(''); 
        $('#ridesheet-ride-page .rides-div').empty();
    });

	
    $('#ridesheet-sheet-page .leader-section .add-frm').on('submit', function(evt) {
        evt.preventDefault();
        var action = $('#ridesheet-sheet-page .leader-section .add-frm').attr('action');
        var data = {
			'action': 'pwtc_mileage_add_leader',
			'member_id': $("#ridesheet-sheet-page .leader-section .add-frm input[name='riderid']").val(),
			'ride_id': $("#ridesheet-sheet-page .leader-section .add-frm input[name='rideid']").val(),
			'nonce': '<?php echo wp_create_nonce('pwtc_mileage_add_leader'); ?>'
		};
		$.post(action, data, add_leader_cb);
    });

    $('#ridesheet-sheet-page .mileage-section .add-frm').on('submit', function(evt) {
        evt.preventDefault();
        var action = $('#ridesheet-sheet-page .mileage-section .add-frm').attr('action');
        var data = {
			'action': 'pwtc_mileage_add_mileage',
			'member_id': $("#ridesheet-sheet-page .mileage-section .add-frm input[name='riderid']").val(),
			'ride_id': $("#ridesheet-sheet-page .mileage-section .add-frm input[name='rideid']").val(),
			'mileage': $("#ridesheet-sheet-page .mileage-section .add-frm input[name='mileage']").val(),
			'nonce': '<?php echo wp_create_nonce('pwtc_mileage_add_mileage'); ?>'
		};
		$.post(action, data, add_mileage_cb);
    });

	$("#ridesheet-sheet-page .leader-section .lookup-btn").on('click', function(evt) {
        lookup_pwtc_riders(function(riderid, name) {
            $("#ridesheet-sheet-page .leader-section .add-frm input[name='riderid']").val(riderid);
            $("#ridesheet-sheet-page .leader-section .add-frm input[name='ridername']").val(name); 
			$("#ridesheet-sheet-page .leader-section .lookup-btn").hide('fast', function() {
				$('#ridesheet-sheet-page .leader-section .add-blk').show('slow'); 
				$("#ridesheet-sheet-page .leader-section .add-frm input[type='submit']").focus();          
			});
        });
    });

	$("#ridesheet-sheet-page .leader-section .add-frm .cancel-btn").on('click', function(evt) {
 		$('#ridesheet-sheet-page .leader-section .add-blk').hide('fast', function() {
			$("#ridesheet-sheet-page .leader-section .lookup-btn").show('slow');          
			$("#ridesheet-sheet-page .leader-section .lookup-btn").focus();
		}); 
    });

	$("#ridesheet-sheet-page .mileage-section .lookup-btn").on('click', function(evt) {
        lookup_pwtc_riders(function(riderid, name) {
            $("#ridesheet-sheet-page .mileage-section .add-frm input[name='riderid']").val(riderid);
            $("#ridesheet-sheet-page .mileage-section .add-frm input[name='ridername']").val(name); 
			$("#ridesheet-sheet-page .mileage-section .add-frm input[name='mileage']").val(''); 
			$("#ridesheet-sheet-page .mileage-section .lookup-btn").hide('fast', function() {
				$('#ridesheet-sheet-page .mileage-section .add-blk').show('slow');  
				$("#ridesheet-sheet-page .mileage-section .add-frm input[name='mileage']").focus();         
			});
        });
    });

	$("#ridesheet-sheet-page .mileage-section .add-frm .cancel-btn").on('click', function(evt) {
 		$('#ridesheet-sheet-page .mileage-section .add-blk').hide('fast', function() {
			$('#ridesheet-sheet-page .mileage-section .lookup-btn').show('slow');           
			$("#ridesheet-sheet-page .mileage-section .lookup-btn").focus();
		});
    });

	$("#ridesheet-ride-page .add-btn").on('click', function(evt) {
		$("#ridesheet-ride-page .add-blk .add-frm input[type='text']").val(''); 
		$("#ridesheet-ride-page .add-blk .add-frm input[type='hidden']").val(''); 
		$("#ridesheet-ride-page .add-btn").hide('fast', function() {
			$('#ridesheet-ride-page .add-blk').show('slow'); 
			$("#ridesheet-ride-page .add-blk .add-frm input[name='title']").focus();          
		});
    });

	$("#ridesheet-ride-page .add-blk .cancel-btn").on('click', function(evt) {
		$('#ridesheet-ride-page .add-blk').hide('slow', function() {
			$("#ridesheet-ride-page .add-btn").show('fast');
		});
    });

	$('#ridesheet-ride-page .add-blk .add-frm').on('submit', function(evt) {
        evt.preventDefault();
        var action = $('#ridesheet-ride-page .add-blk .add-frm').attr('action');
        var data = {
			'action': 'pwtc_mileage_create_ride',
			'title': $("#ridesheet-ride-page .add-blk .add-frm input[name='title']").val(),
			'startdate': $("#ridesheet-ride-page .add-blk .add-frm input[name='fmtdate']").val(),
			'nonce': '<?php echo wp_create_nonce('pwtc_mileage_create_ride'); ?>'
		};
		$.post(action, data, create_ride_cb);
    });

	$("#ridesheet-ride-page .add-blk .add-frm input[name='date']").datepicker({
  		dateFormat: 'D M d yy',
		altField: "#ridesheet-ride-page .add-blk .add-frm input[name='fmtdate']",
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
		</p>
	</div>
	<div id="ridesheet-post-page" class="initially-hidden">
		<p><button class='back-btn button button-primary button-large'>Back</button></p>
		<p>
			<h3>Posted Rides without Ride Sheets</h3>
			<div class="posts-div"></div>
		</p>
	</div>
	<div id="ridesheet-ride-page" class="initially-hidden">
		<p><button class='back-btn button button-primary button-large'>Back</button></p>
		<p><form class="ride-search-frm stacked-form" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post">
			<span>Title</span>
			<input type="text" name="title"/>
			<span>From Date</span>
			<input type="text" name="date" required/>
			<span>To Date</span>
			<input type="text" name="todate" required/>
			<input type="hidden" name="fmtdate"/>
			<input type="hidden" name="tofmtdate"/>
			<input class="button button-primary" type="submit" value="Search"/>
			<input class="reset-btn button button-primary" type="button" value="Reset"/>
		</form></p>	

		<div><button class="add-btn button button-primary button-large">New</button>
		<span class="add-blk popup-frm initially-hidden">
			<form class="add-frm stacked-form" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post">
				<span>Ride Title</span>
				<input name="title" type="text" required/>
				<span>Start Date</span>
				<input name="date" type="text" required/>				
				<input type="hidden" name="fmtdate"/>
				<input class="button button-primary" type="submit" value="Create"/>
				<input class="cancel-btn button button-primary" type="button" value="Cancel"/>
			</form>
		</span></div>

		<p><div class="rides-div"></div></p>
	</div>
	<div id='ridesheet-sheet-page' class="initially-hidden">
		<p><button class='back-btn button button-primary button-large'>Back</button></p>
		<h2></h2>
		<div class="leader-section">
			<h3>Ride Leaders</h3>
			<div><button class="lookup-btn button button-primary">Lookup Leader</button>
				<span class="add-blk popup-frm initially-hidden">
					<form class="add-frm stacked-form" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post">
						<span>ID</span>
						<input name="riderid" type="text" disabled/>
						<span>Name</span>
						<input name="ridername" type="text" disabled/>
						<input name="rideid" type="hidden"/>
						<input class="button button-primary" type="submit" value="Add Leader"/>
						<input class="cancel-btn button button-primary" type="button" value="Cancel"/>
					</form>
				</span>
			</div>
			<p><div class="leader-div"></div></p>
		</div>
		<div class="mileage-section">
			<h3>Rider Mileage</h3>
			<div><button class="lookup-btn button button-primary">Lookup Rider</button>
				<span class="add-blk popup-frm initially-hidden">
					<form class="add-frm stacked-form" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post">
						<span>ID</span>
						<input name="riderid" type="text" disabled/>
						<span>Name</span>
						<input name="ridername" type="text" disabled/>
						<span>Mileage</span>
						<input name="mileage" type="text" required/>
						<input name="rideid" type="hidden"/>
						<input class="button button-primary" type="submit" value="Add Mileage"/>
						<input class="cancel-btn button button-primary" type="button" value="Cancel"/>
					</form>
				</span>
			</div>
			<p><div class="mileage-div"></div></p>
		</div>
	</div>
<?php
	include('admin-rider-lookup.php');
}
?>
</div>
<?php
