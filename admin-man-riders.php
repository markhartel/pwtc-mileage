<?php
if (!current_user_can('edit_published_pages')) {
    return;
}
?>
<script type="text/javascript">
jQuery(document).ready(function($) { 

	function populate_riders_table(members) {
		$('#rider-inspect-section .riders-tbl tr').remove();
		$('#rider-inspect-section .riders-tbl').append(
			'<tr><th>Member ID</th><th>First Name</th><th>Last Name</th><th>Expiration Date</th><th>Actions</th></tr>');
        members.forEach(function(item) {
			var fmtdate = getPrettyDate(item.expir_date);
            $('#rider-inspect-section .riders-tbl').append(
				'<tr memberid="' + item.member_id + '">' + 
				'<td>' + item.member_id + '</td>' +
				'<td>' + item.first_name + '</td><td>' + item.last_name + '</td>' + 
				'<td date="' + item.expir_date + '">' + fmtdate + '</td>' + 
                '<td><a class="modify-btn">Edit</a>' + ' ' +
                '<a class="remove-btn">Delete</a></td></tr>');    
		});
        $('#rider-inspect-section .riders-tbl .modify-btn').on('click', function(evt) {
            evt.preventDefault();
            var action = '<?php echo admin_url('admin-ajax.php'); ?>';
            var data = {
			    'action': 'pwtc_mileage_get_rider',
                'member_id': $(this).parent().parent().attr('memberid')
		    };
            $.post(action, data, modify_rider_cb);
        });
		$('#rider-inspect-section .riders-tbl .remove-btn').on('click', function(evt) {
            evt.preventDefault();
            var action = '<?php echo admin_url('admin-ajax.php'); ?>';
            var data = {
			    'action': 'pwtc_mileage_remove_rider',
                'member_id': $(this).parent().parent().attr('memberid')
		    };
<?php if ($plugin_options['disable_delete_confirm']) { ?>
            $.post(action, data, remove_rider_cb);
<?php } else { ?>
            open_confirm_dialog(
                'Are you sure you want to delete rider ' + data.member_id + '?', 
                function() {
                    $.post(action, data, remove_rider_cb);
                }
            );
<?php } ?>		
        });
    }

	function lookup_riders_cb(response) {
        var res = JSON.parse(response);
        $("#rider-inspect-section .search-frm input[name='memberid']").val(res.memberid);
        $("#rider-inspect-section .search-frm input[name='firstname']").val(res.firstname);
        $("#rider-inspect-section .search-frm input[name='lastname']").val(res.lastname);
		populate_riders_table(res.members);
	}   

	function create_rider_cb(response) {
        var res = JSON.parse(response);
		if (res.error) {
            open_error_dialog(res.error);
		}
		else {
            $('#rider-inspect-section .add-blk').hide();
            load_rider_table();
        }
	}   

	function modify_rider_cb(response) {
        var res = JSON.parse(response);
		if (res.error) {
            open_error_dialog(res.error);
		}
		else {
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
            $('#rider-inspect-section .add-blk').show(500);
            $("#rider-inspect-section .add-blk .add-frm input[name='firstname']").focus();
        }
	}   

	function remove_rider_cb(response) {
        var res = JSON.parse(response);
		if (res.error) {
            open_error_dialog(res.error);
		}
		else {
            load_rider_table();
        }
	}   

    function load_rider_table() {
        var action = $('#rider-inspect-section .search-frm').attr('action');
        var data = {
			'action': 'pwtc_mileage_lookup_riders',
			'memberid': $("#rider-inspect-section .search-frm input[name='memberid']").val(),
			'lastname': $("#rider-inspect-section .search-frm input[name='lastname']").val(),
			'firstname': $("#rider-inspect-section .search-frm input[name='firstname']").val()
		};
		$.post(action, data, lookup_riders_cb);   
    }

    $('#rider-inspect-section .search-frm').on('submit', function(evt) {
        evt.preventDefault();
        load_rider_table();
    });

    $('#rider-inspect-section .search-frm .reset-btn').on('click', function(evt) {
        evt.preventDefault();
        $("#rider-inspect-section .search-frm input[type='text']").val(''); 
    });

    $("#rider-inspect-section .add-btn").on('click', function(evt) {
		$("#rider-inspect-section .add-blk .add-frm input[type='text']").val(''); 
		$("#rider-inspect-section .add-blk .add-frm input[type='hidden']").val(''); 
        $("#rider-inspect-section .add-blk .add-frm input[name='mode']").val('insert');
        $("#rider-inspect-section .add-blk .add-frm input[name='memberid']").removeAttr("disabled");
		$('#rider-inspect-section .add-blk').show(500); 
        $("#rider-inspect-section .add-blk .add-frm input[name='memberid']").focus();          
    });

	$("#rider-inspect-section .add-blk .cancel-btn").on('click', function(evt) {
		$('#rider-inspect-section .add-blk').hide();
    });

    $("#rider-inspect-section .add-blk .add-frm input[name='expdate']").datepicker({
  		dateFormat: 'D M d yy',
		altField: "#rider-inspect-section .add-blk .add-frm input[name='fmtdate']",
		altFormat: 'yy-mm-dd',
		changeMonth: true,
      	changeYear: true
	});

    $('#rider-inspect-section .add-blk .add-frm').on('submit', function(evt) {
        evt.preventDefault();
        var action = $('#rider-inspect-section .add-blk .add-frm').attr('action');
        var data = {
			'action': 'pwtc_mileage_create_rider',
            'mode': $("#rider-inspect-section .add-blk .add-frm input[name='mode']").val(),
			'member_id': $("#rider-inspect-section .add-blk .add-frm input[name='memberid']").val(),
			'lastname': $("#rider-inspect-section .add-blk .add-frm input[name='lastname']").val(),
			'firstname': $("#rider-inspect-section .add-blk .add-frm input[name='firstname']").val(),
			'exp_date': $("#rider-inspect-section .add-blk .add-frm input[name='fmtdate']").val()
		};
		$.post(action, data, create_rider_cb);
    });

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
    <div id='rider-inspect-section'>
        <p>
        	<form class="search-frm" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post">
				<table>
				<tr><td>Member ID:</td><td><input name="memberid" type="text"/></td></tr>
				<tr><td>First Name:</td><td><input name="firstname" type="text"/></td></tr>
				<tr><td>Last Name:</td><td><input name="lastname" type="text"/></td></tr>
				</table>
				<input class="button button-primary" type="submit" value="Search"/>
				<input class="reset-btn button button-primary" type="button" value="Reset"/>
			</form>
        </p>

        <div><button class="add-btn button button-primary button-large">New</button>
		<span class="add-blk initially-hidden">
			<form class="add-frm" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post">
				<table>
				<tr><td>Member ID:</td><td><input name="memberid" type="text" required/></td></tr>
				<tr><td>First Name:</td><td><input name="firstname" type="text" required/></td></tr>
				<tr><td>Last Name:</td><td><input name="lastname" type="text" required/></td></tr>
				<tr><td>Expiration Date:</td><td><input name="expdate" type="text" required/></td></tr>
				</table>
				<input type="hidden" name="fmtdate"/>
				<input type="hidden" name="mode"/>
				<input class="button button-primary" type="submit" value="Create"/>
				<input class="cancel-btn button button-primary" type="button" value="Cancel"/>
			</form>
		</span></div>

        <p><table class="riders-tbl pretty"></table></p>
    </div>
<?php
    include('admin-rider-lookup.php');
}
?>
</div>
<?php
