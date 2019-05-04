<div class="wrap">
	<h1><?= esc_html(get_admin_page_title()); ?></h1>
<?php
if (!current_user_can($capability)) {
?> 
    <p><strong>Access Denied</strong> - you do not have the rights to view this page.</p>
<?php   
}
else if ($plugin_options['admin_maint_mode'] and !current_user_can('manage_options')) {
?> 
    <p><strong>Access Denied</strong> - the mileage database is maintenance mode.</p>
<?php       
}
else {
?>
<script type="text/javascript">
jQuery(document).ready(function($) { 

	function populate_users_table(users) {
        $('#rider-edit-section .users-div').empty();
        if (users.length > 0) {
            $('#rider-edit-section .users-div').append('<table class="rwd-table">' +
                '<tr><th>First Name</th><th>Last Name</th><th>Email</th><th>Expiration Date</th><th>Role</th><th>Note</th></tr>' +
                '</table>');
            users.forEach(function(item) {
                var fmtdate = '';
                if (item.expir_date.length > 0) {
                    fmtdate = getPrettyDate(item.expir_date);
                }
                $('#rider-edit-section .users-div table').append(
                    '<tr userid="' + item.userid + '">' + 
                    '<td data-th="First Name">' + item.first_name + '</td>' +
                    '<td data-th="Last Name">' + item.last_name + '</td>' + 
                    '<td data-th="Email">' + item.email + '</td>' +
                    '<td data-th="Expiration">' + fmtdate + '</td>' + 
                    '<td data-th="Role">' + item.role + '</td>' + 
                    '<td data-th="Note">' + item.note + '</td>' + 
                    '</tr>');    
            });
        }
        else {
            $('#rider-edit-section .users-div').append(
                '<span class="empty-tbl">No user profile found for this rider.</span>');
        }
    }

	function populate_riders_table(members) {
		$('#rider-inspect-section .riders-div').empty();
        if (members.length > 0) {
            $('#rider-inspect-section .riders-div').append('<table class="rwd-table">' +
                '<tr><th>ID</th><th>First Name</th><th>Last Name</th><th>Expiration Date</th><th>Actions</th></tr>' +
                '</table>');
            viewlink = '<a title="View this rider\'s information." class="view-btn">View</a>';
            editlink = '<a title="Edit this rider\'s information." class="modify-btn">Edit</a>';
            deletelink = '<a title="Delete this rider." class="remove-btn">Delete</a>';
            synclink = '';
    <?php if ($plugin_options['user_lookup_mode'] == 'woocommerce') { ?>
            synclink = ''; //'<a title="Sync this rider with their membership record." class="sync-btn">Sync</a>';
    <?php } ?>		
            members.forEach(function(item) {
                var fmtdate = getPrettyDate(item.expir_date);
                $('#rider-inspect-section .riders-div table').append(
                    '<tr memberid="' + item.member_id + '">' + 
                    '<td data-th="ID">' + item.member_id + '</td>' +
                    '<td data-th="First Name">' + item.first_name + 
                    '</td><td data-th="Last Name">' + item.last_name + '</td>' + 
                    '<td data-th="Expiration" date="' + item.expir_date + '">' + fmtdate + '</td>' + 
                    '<td data-th="Actions">' + viewlink + ' ' + editlink + ' ' + deletelink + ' ' + synclink +
                    '</td></tr>');    
            });
            $('#rider-inspect-section .riders-div .view-btn').on('click', function(evt) {
                evt.preventDefault();
                var action = '<?php echo admin_url('admin-ajax.php'); ?>';
                var data = {
                    'action': 'pwtc_mileage_get_rider',
                    'mode' : 'view',
                    'member_id': $(this).parent().parent().attr('memberid')
                };
                $('body').addClass('waiting');
                $.post(action, data, view_rider_cb);
            });
            $('#rider-inspect-section .riders-div .modify-btn').on('click', function(evt) {
                evt.preventDefault();
                var action = '<?php echo admin_url('admin-ajax.php'); ?>';
                var data = {
                    'action': 'pwtc_mileage_get_rider',
                    'mode' : 'edit',
                    'member_id': $(this).parent().parent().attr('memberid')
                };
                $('body').addClass('waiting');
                $.post(action, data, modify_rider2_cb);
            });
            $('#rider-inspect-section .riders-div .remove-btn').on('click', function(evt) {
                evt.preventDefault();
                var action = '<?php echo admin_url('admin-ajax.php'); ?>';
                var data = {
                    'action': 'pwtc_mileage_remove_rider',
                    'member_id': $(this).parent().parent().attr('memberid'),
                    'nonce': '<?php echo wp_create_nonce('pwtc_mileage_remove_rider'); ?>'
                };
    <?php if ($plugin_options['disable_delete_confirm']) { ?>
                $('body').addClass('waiting');
                $.post(action, data, remove_rider_cb);
    <?php } else { ?>
                open_confirm_dialog(
                    'Are you sure you want to delete rider ' + data.member_id + '?', 
                    function() {
                        $('body').addClass('waiting');
                        $.post(action, data, remove_rider_cb);
                    }
                );
    <?php } ?>		
            });
            $('#rider-inspect-section .riders-div .sync-btn').on('click', function(evt) {
                evt.preventDefault();
                var action = '<?php echo admin_url('admin-ajax.php'); ?>';
                var data = {
                    'action': 'pwtc_mileage_sync_rider',
                    'member_id': $(this).parent().parent().attr('memberid'),
                    'nonce': '<?php echo wp_create_nonce('pwtc_mileage_sync_rider'); ?>'
                };
                open_confirm_dialog(
                    'Are you sure you want to sync rider ' + data.member_id + ' with their membership record?', 
                    function() {
                        $('body').addClass('waiting');
                        $.post(action, data, sync_rider_cb);
                    }
                );
            });
        }
        else {
            $('#rider-inspect-section .riders-div').append(
                '<span class="empty-tbl">No riders found.</span>');
        }
    }

	function lookup_riders_cb(response) {
        var res = JSON.parse(response);
        $("#rider-inspect-section .search-frm input[name='memberid']").val(res.memberid);
        $("#rider-inspect-section .search-frm input[name='firstname']").val(res.firstname);
        $("#rider-inspect-section .search-frm input[name='lastname']").val(res.lastname);
		populate_riders_table(res.members);
        $('body').removeClass('waiting');
    }   

	function create_rider_cb(response) {
        $('body').removeClass('waiting');
        var res = JSON.parse(response);
		if (res.error) {
            open_error_dialog(res.error);
		}
		else {
            $('#rider-inspect-section .add-blk').hide('slow', function() {
                $("#rider-inspect-section .add-btn").show('fast');     
            });
            load_rider_table();
        }
    }   

	function modify_rider_cb(response) {
        $('body').removeClass('waiting');
        var res = JSON.parse(response);
		if (res.error) {
            open_error_dialog(res.error);
		}
		else {
            var fmtdate = getPrettyDate(res.exp_date);
		    $('#rider-edit-section .rider-name').html(res.firstname + ' ' + res.lastname);
            $('#rider-edit-section .exp-date').html(fmtdate);
            $('#rider-edit-section .modify-blk').hide('slow', function() {
                $("#rider-edit-section .modify-btn").show('fast');     
            });
        }
    }   

	function next_rider_id_cb(response) {
        var res = JSON.parse(response);
		if (res.error) {
            open_error_dialog(res.error);
		}
		else {
            $("#rider-inspect-section .add-blk .add-frm input[type='submit']").val('Create');
            $("#rider-inspect-section .add-blk .add-frm input[type='text']").val(''); 
            $("#rider-inspect-section .add-blk .add-frm input[type='hidden']").val(''); 
            $("#rider-inspect-section .add-blk .add-frm input[name='mode']").val('insert');
            $("#rider-inspect-section .add-blk .add-frm input[name='memberid']").removeAttr("disabled");
            $("#rider-inspect-section .add-blk .add-frm input[name='memberid']").val(res.next_member_id);
            $("#rider-inspect-section .add-btn").hide('fast', function() {
                $('#rider-inspect-section .add-blk').show('slow'); 
                $("#rider-inspect-section .add-blk .add-frm input[name='memberid']").focus();
            });
        }
        $('body').removeClass('waiting');
    }

	function modify_rider2_cb(response) {
        var res = JSON.parse(response);
		if (res.error) {
            open_error_dialog(res.error);
		}
		else {
			$("#rider-inspect-section .add-blk .add-frm input[type='submit']").val(
                'Modify'
            );
			$("#rider-inspect-section .add-blk .add-frm input[name='memberid']").val(
                res.member_id
            );
			$("#rider-inspect-section .add-blk .add-frm input[name='firstname']").val(
                res.firstname
            );
			$("#rider-inspect-section .add-blk .add-frm input[name='lastname']").val(
                res.lastname
            );
			$("#rider-inspect-section .add-blk .add-frm input[name='expdate']").val(
                getPrettyDate(res.exp_date)
            );
			$("#rider-inspect-section .add-blk .add-frm input[name='fmtdate']").val(
                res.exp_date
            );
            $("#rider-inspect-section .add-blk .add-frm input[name='mode']").val('update');
            $("#rider-inspect-section .add-blk .add-frm input[name='memberid']").attr("disabled", "disabled");
            $("#rider-inspect-section .add-btn").hide('fast', function() {
		        $('#rider-inspect-section .add-blk').show('slow'); 
                $("#rider-inspect-section .add-blk .add-frm input[name='firstname']").focus();          
            });
        }
        $('body').removeClass('waiting');
	}   

    function modify_rider_setup_cb(response) {
        var res = JSON.parse(response);
		if (res.error) {
            open_error_dialog(res.error);
		}
		else {
			$("#rider-edit-section .modify-blk .modify-frm input[name='memberid']").val(
                res.member_id
            );
			$("#rider-edit-section .modify-blk .modify-frm input[name='firstname']").val(
                res.firstname
            );
			$("#rider-edit-section .modify-blk .modify-frm input[name='lastname']").val(
                res.lastname
            );
			$("#rider-edit-section .modify-blk .modify-frm input[name='expdate']").val(
                getPrettyDate(res.exp_date)
            );
			$("#rider-edit-section .modify-blk .modify-frm input[name='fmtdate']").val(
                res.exp_date
            );
            $("#rider-edit-section .modify-btn").hide('fast', function() {
		        $('#rider-edit-section .modify-blk').show('slow'); 
                $("#rider-edit-section .modify-blk .modify-frm input[name='firstname']").focus();          
            });
        }
        $('body').removeClass('waiting');
	}   

	function view_rider_cb(response) {
        var res = JSON.parse(response);
		if (res.error) {
            open_error_dialog(res.error);
		}
		else {
			show_rider_section(res, false);
			if (history.pushState) {
				var state = {
					'action': 'pwtc_mileage_get_rider',
					'member_id': res.member_id
				};
				history.pushState(state, '');
			}
        }
        $('body').removeClass('waiting');
	}   

	function restore_rider_cb(response) {
        var res = JSON.parse(response);
		if (res.error) {
			open_error_dialog(res.error);
		}
		else {
			show_rider_section(res, false);
		}
		$('body').removeClass('waiting');
	}

	function remove_rider_cb(response) {
        $('body').removeClass('waiting');
        var res = JSON.parse(response);
		if (res.error) {
            open_error_dialog(res.error);
		}
		else {
            load_rider_table();
        }
	}   

	function sync_rider_cb(response) {
        $('body').removeClass('waiting');
        var res = JSON.parse(response);
		if (res.error) {
            open_error_dialog(res.error);
		}
		else {
            var fmtdate = getPrettyDate(res.exp_date);
		    $('#rider-edit-section .rider-name').html(res.firstname + ' ' + res.lastname);
		    $('#rider-edit-section .exp-date').html(fmtdate);
        }
	}   

	function xfer_ridersheets_cb(response) {
        $('body').removeClass('waiting');
        var res = JSON.parse(response);
		if (res.error) {
            open_error_dialog(res.error);
		}
		else {
        }
	}   

	function xfer_user_profile_cb(response) {
        $('body').removeClass('waiting');
        var res = JSON.parse(response);
		if (res.error) {
            open_error_dialog(res.error);
		}
		else {
            show_rider_section(res, true);
        }
	}   

	function purge_rider_cb(response) {
        $('body').removeClass('waiting');
        var res = JSON.parse(response);
		if (res.error) {
            open_error_dialog(res.error);
		}
		else {
        }
	}   

    function load_rider_table() {
        var memberid = $("#rider-inspect-section .search-frm input[name='memberid']").val().trim();
        var lastname = $("#rider-inspect-section .search-frm input[name='lastname']").val().trim();
        var firstname = $("#rider-inspect-section .search-frm input[name='firstname']").val().trim();
        var active = false;
    <?php if ($plugin_options['user_lookup_mode'] != 'woocommerce') { ?>
        if ($("#rider-inspect-section .search-frm input[name='active']").is(':checked')) {
            active = true;
        }
    <?php } ?>
        if (memberid.length > 0 || lastname.length > 0 || firstname.length > 0) {
            var action = $('#rider-inspect-section .search-frm').attr('action');
            var data = {
                'action': 'pwtc_mileage_lookup_riders',
                'memberid': memberid,
                'lastname': lastname,
                'firstname': firstname,
                'active': active
            };
            $('body').addClass('waiting');
            $.post(action, data, lookup_riders_cb); 
        }
        else {
            $('#rider-inspect-section .riders-div').empty();  
        }  
    }

    function show_rider_section(rider, refresh_only) {
        var fmtdate = getPrettyDate(rider.exp_date);
		$('#rider-edit-section .rider-name').html(rider.firstname + ' ' + rider.lastname);
		$('#rider-edit-section .rider-id').html(rider.member_id);
        $('#rider-edit-section .exp-date').html(fmtdate);
        if (rider.mileage_count == 0 && rider.leader_count == 0) {
            $('#rider-edit-section .ridesheet-count').html('This rider does not appear on any ridesheets.');
        }
        else {
            $('#rider-edit-section .ridesheet-count').html('This rider appears as a rider on <strong>' + rider.mileage_count + '</strong> ride sheets and as a ride leader on <strong>' + rider.leader_count + '</strong> ride sheets.');
        }
        populate_users_table(rider.user_profiles);
        $('#rider-edit-section .profile-msg').empty();
        $('#rider-edit-section .sync-btn').hide();
        $('#rider-edit-section .purge-btn').hide();
        $('#rider-edit-section .xfer-rs-btn').hide();
        $('#rider-edit-section .xfer-prof-btn').hide();
        $('#rider-edit-section .modify-blk').hide();
		$('#rider-edit-section .modify-btn').show();
        if (rider.user_profiles.length == 0) {
            if (rider.mileage_count > 0 || rider.leader_count > 0) {
                $('#rider-edit-section .purge-btn').show();
                $('#rider-edit-section .xfer-rs-btn').show();
            }
        }
        else if (rider.user_profiles.length == 1) {
            $('#rider-edit-section .xfer-prof-btn').show();
            var user = rider.user_profiles[0];
            if (user.expir_date.length > 0) {
                if (user.expir_date !== rider.exp_date || 
                    user.first_name !== rider.firstname ||
                    user.last_name !== rider.lastname) {
                    $('#rider-edit-section .sync-btn').show();
                    $('#rider-edit-section .modify-btn').hide();
                }
            }
        }
        else if (rider.user_profiles.length > 1) {
            $('#rider-edit-section .profile-msg').html("Warning, this rider's ID is used in multiple user profiles! This should be corrected, only once is allowed.");
        }
        if (!refresh_only) {
            $('#rider-inspect-section').hide('fast', function() {
                $('#rider-edit-section').fadeIn('slow');
                $('#rider-edit-section .back-btn').focus();
            });
        }
    }

	function return_main_section() {
		$('#rider-edit-section').hide('fast', function() {
            load_rider_table();
			$('#rider-inspect-section .add-blk').hide();
			$('#rider-inspect-section .add-btn').show();
			$('#rider-inspect-section').fadeIn('slow');
		});
	}

    $('#rider-inspect-section .search-frm').on('submit', function(evt) {
        evt.preventDefault();
        load_rider_table();
    });

    $('#rider-inspect-section .search-frm .reset-btn').on('click', function(evt) {
        evt.preventDefault();
        $("#rider-inspect-section .search-frm input[type='text']").val(''); 
        $('#rider-inspect-section .riders-div').empty();
    });

    $("#rider-inspect-section .add-btn").on('click', function(evt) {
        var action = '<?php echo admin_url('admin-ajax.php'); ?>';
        var data = {
            'action': 'pwtc_mileage_next_rider_id'
        };
        $('body').addClass('waiting');
        $.post(action, data, next_rider_id_cb); 
    });

	$("#rider-inspect-section .add-blk .cancel-btn").on('click', function(evt) {
		$('#rider-inspect-section .add-blk').hide('slow', function() {
            $("#rider-inspect-section .add-btn").show('fast'); 
        });
    });

    $("#rider-edit-section .modify-btn").on('click', function(evt) {
        var action = '<?php echo admin_url('admin-ajax.php'); ?>';
        var data = {
            'action': 'pwtc_mileage_get_rider',
            'mode' : 'edit',
            'member_id': $('#rider-edit-section .rider-id').html()
        };
        $('body').addClass('waiting');
        $.post(action, data, modify_rider_setup_cb);
    });

	$("#rider-edit-section .modify-blk .cancel-btn").on('click', function(evt) {
		$('#rider-edit-section .modify-blk').hide('slow', function() {
            $("#rider-edit-section .modify-btn").show('fast'); 
        });
    });

    $("#rider-inspect-section .add-blk .add-frm input[name='expdate']").datepicker({
  		dateFormat: 'D M d yy',
		altField: "#rider-inspect-section .add-blk .add-frm input[name='fmtdate']",
		altFormat: 'yy-mm-dd',
		changeMonth: true,
      	changeYear: true
	});

    $("#rider-edit-section .modify-blk .modify-frm input[name='expdate']").datepicker({
  		dateFormat: 'D M d yy',
		altField: "#rider-edit-section .modify-blk .modify-frm input[name='fmtdate']",
		altFormat: 'yy-mm-dd',
		changeMonth: true,
      	changeYear: true
	});

    $('#rider-edit-section .modify-blk .modify-frm').on('submit', function(evt) {
        evt.preventDefault();
        var action = $('#rider-edit-section .modify-blk .modify-frm').attr('action');
        var data = {
			'action': 'pwtc_mileage_create_rider',
            'nonce': '<?php echo wp_create_nonce('pwtc_mileage_create_rider'); ?>',
            'mode': $("#rider-edit-section .modify-blk .modify-frm input[name='mode']").val(),
			'member_id': $("#rider-edit-section .modify-blk .modify-frm input[name='memberid']").val(),
			'lastname': $("#rider-edit-section .modify-blk .modify-frm input[name='lastname']").val(),
			'firstname': $("#rider-edit-section .modify-blk .modify-frm input[name='firstname']").val(),
			'exp_date': $("#rider-edit-section .modify-blk .modify-frm input[name='fmtdate']").val()
		};
        $('body').addClass('waiting');
        $.post(action, data, modify_rider_cb);
    });

    $('#rider-inspect-section .add-blk .add-frm').on('submit', function(evt) {
        evt.preventDefault();
        var action = $('#rider-inspect-section .add-blk .add-frm').attr('action');
        var data = {
			'action': 'pwtc_mileage_create_rider',
            'nonce': '<?php echo wp_create_nonce('pwtc_mileage_create_rider'); ?>',
            'mode': $("#rider-inspect-section .add-blk .add-frm input[name='mode']").val(),
			'member_id': $("#rider-inspect-section .add-blk .add-frm input[name='memberid']").val(),
			'lastname': $("#rider-inspect-section .add-blk .add-frm input[name='lastname']").val(),
			'firstname': $("#rider-inspect-section .add-blk .add-frm input[name='firstname']").val(),
			'exp_date': $("#rider-inspect-section .add-blk .add-frm input[name='fmtdate']").val()
		};
        $('body').addClass('waiting');
        $.post(action, data, create_rider_cb);
    });

    $('#rider-edit-section .back-btn').on('click', function(evt) {
        if (history.pushState) {
			history.back();
		}
		else {
			return_main_section();
		}
	});

    $('#rider-edit-section .sync-btn').on('click', function(evt) {
        evt.preventDefault();
        var action = '<?php echo admin_url('admin-ajax.php'); ?>';
        var data = {
            'action': 'pwtc_mileage_sync_rider',
            'member_id': $('#rider-edit-section .rider-id').html(),
            'nonce': '<?php echo wp_create_nonce('pwtc_mileage_sync_rider'); ?>'
        };
        open_confirm_dialog(
            'Are you sure you want to sync rider ' + data.member_id + ' with their user profile record (first name, last name and expiration date?)', 
            function() {
                $('body').addClass('waiting');
                $.post(action, data, sync_rider_cb);
            }
        );
    });

    $('#rider-edit-section .xfer-rs-btn').on('click', function(evt) {
        lookup_pwtc_riders_all(function(riderid, name) {
            var action = '<?php echo admin_url('admin-ajax.php'); ?>';
            var data = {
                'action': 'pwtc_mileage_xfer_ridesheets',
                'from_memberid': $('#rider-edit-section .rider-id').html(),
                'to_memberid': riderid,
                'nonce': '<?php echo wp_create_nonce('pwtc_mileage_xfer_ridesheets'); ?>'
            };
            open_confirm_dialog(
                "Are you sure you want to transfer this rider's ridesheet entries to rider <strong>" + riderid + "</strong> - <strong>" + name + "</strong>?", 
                function() {
                    $('body').addClass('waiting');
                    $.post(action, data, xfer_ridersheets_cb);
                }
            );
        });
    });

    $('#rider-edit-section .xfer-prof-btn').on('click', function(evt) {
        lookup_pwtc_riders_all(function(riderid, name) {
            var action = '<?php echo admin_url('admin-ajax.php'); ?>';
            var data = {
                'action': 'pwtc_mileage_xfer_user_profile',
                'from_memberid': $('#rider-edit-section .rider-id').html(),
                'to_memberid': riderid,
                'nonce': '<?php echo wp_create_nonce('pwtc_mileage_xfer_user_profile'); ?>'
            };
            open_confirm_dialog(
                "Are you sure you want to transfer this rider's user profile to rider <strong>" + riderid + "</strong> - <strong>" + name + "</strong>?", 
                function() {
                    $('body').addClass('waiting');
                    $.post(action, data, xfer_user_profile_cb);
                }
            );
        });
    });

    $('#rider-edit-section .purge-btn').on('click', function(evt) {
        var action = '<?php echo admin_url('admin-ajax.php'); ?>';
        var data = {
            'action': 'pwtc_mileage_purge_rider',
            'member_id': $('#rider-edit-section .rider-id').html(),
            'nonce': '<?php echo wp_create_nonce('pwtc_mileage_purge_rider'); ?>'
        };
        open_confirm_dialog(
            "Are you sure you want to purge this rider's ridesheet entries?", 
            function() {
                $('body').addClass('waiting');
                $.post(action, data, purge_rider_cb);
            }
        );
    });

    if (history.pushState) {
		$(window).on('popstate', function(evt) {
			var state = evt.originalEvent.state;
			if (state !== null) {
				//console.log("Popstate event, state is " + JSON.stringify(state));
				var action = '<?php echo admin_url('admin-ajax.php'); ?>';
				$('body').addClass('waiting');
				$.post(action, state, restore_rider_cb);
			}
			else {
				//console.log("Popstate event, state is null.");
				return_main_section();
			}
		});
	}
    else {
        //console.log("history.pushState is not supported");
    }

    $("#rider-inspect-section .search-frm input[type='text']").val('');
    
    $("#rider-inspect-section .search-frm input[name='memberid']").focus();

});
</script>
<?php
if ($running_jobs > 0) {
?>
    <div class="notice notice-warning"><p><strong>
        A database batch operation is currently running!
    </strong></p></div>
<?php
} else {
?>
    <div id='rider-inspect-section'>
        <p>Manage riders registered in the mileage database. New riders are assigned rider IDs based on this list.</p>
        <div class='search-sec'>
        <p><strong>Enter search parameters to find riders.</strong>
        	<form class="search-frm stacked-form" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post">
                <span>ID</span>
                <input name="memberid" type="text"/>
                <span>First Name</span>
                <input name="firstname" type="text"/>
                <span>Last Name</span>
                <input name="lastname" type="text"/>
    <?php if ($plugin_options['user_lookup_mode'] != 'woocommerce') { ?>
		        <span>Active Members Only</span>
		        <span class="checkbox-wrap">
			        <input type="checkbox" name="active"/>
		        </span>
    <?php } ?>
				<input class="button button-primary" type="submit" value="Search"/>
				<input class="reset-btn button button-primary" type="button" value="Reset"/>
			</form>
        </p>
        </div>

        <p><div><button class="add-btn button button-primary button-large">New</button>
		<span class="add-blk popup-frm initially-hidden">
			<form class="add-frm stacked-form" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post">
                <span>ID</span>
                <input name="memberid" type="text" required/>
                <span>First Name</span>
                <input name="firstname" type="text" required/>
                <span>Last Name</span>
                <input name="lastname" type="text" required/>
                <span>Expiration Date</span>
                <input name="expdate" type="text" required/>
				<input type="hidden" name="fmtdate"/>
				<input type="hidden" name="mode"/>
				<input class="button button-primary" type="submit" value="Create"/>
				<input class="cancel-btn button button-primary" type="button" value="Cancel"/>
			</form>
		</span></div></p>

        <p><div class="riders-div"></div></p>
    </div>
    <div id="rider-edit-section" class="initially-hidden">
        <p>Use this page to inspect the ride sheet status and user profile of a rider.</p>
		<p><button class="back-btn button button-primary button-large">&lt; Back</button></p>  
		<div class="report-sec">
		    <h3>Rider <span class="rider-id"></span> - <span class="rider-name"></span></h3>
            <p>This rider expires on <strong><span class="exp-date"></span></strong>.</p> 
            <p><div><button class="modify-btn button button-primary button-large">Modify</button>
		    <span class="modify-blk popup-frm initially-hidden">
			<form class="modify-frm stacked-form" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post">
                <span>First Name</span>
                <input name="firstname" type="text" required/>
                <span>Last Name</span>
                <input name="lastname" type="text" required/>
                <span>Expiration Date</span>
                <input name="expdate" type="text" required/>
                <input type="hidden" name="memberid"/>
				<input type="hidden" name="fmtdate"/>
				<input type="hidden" name="mode" value="update"/>
				<input class="button button-primary" type="submit" value="Modify"/>
				<input class="cancel-btn button button-primary" type="button" value="Cancel"/>
			</form>
		    </span></div></p>
            <p><span class="ridesheet-count"></span></p>
            <p>
                <button class="purge-btn button button-primary button-large">Purge Rides</button>
                <button class="xfer-rs-btn button button-primary button-large">Transfer Rides</button>
            </p>
        </div>
		<div class="report-sec">
		    <h3>User Profile</h3>
            <p><span class="profile-msg"></span><div class="users-div"></div></p>
            <p>
                <button class="sync-btn button button-primary button-large">Synchronize</button>
                <button class="xfer-prof-btn button button-primary button-large">Transfer Profile</button>
            </p>
        </div>
    </div>
<?php
    include('admin-rider-lookup.php');
}
}
?>
</div>
<?php
