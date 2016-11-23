<?php
if (!current_user_can('manage_options')) {
        	return;
}
?>
<script type="text/javascript">
jQuery(document).ready(function($) { 
	function populate_riders_table(members, lastname, firstname) {
		$('#rider-inspect-section table tr').remove();
		$('#rider-inspect-section table').append(
			'<tr><th>Rider ID</th><th>Name</th><th>Expiration Date</th><th></th></tr>');
        members.forEach(function(item) {
            $('#rider-inspect-section table').append(
				'<tr memberid="' + item.member_id + '">' + 
				'<td>' + item.member_id + '</td>' +
				'<td>' + item.first_name + ' ' + item.last_name + '</td>' + 
				'<td>' + item.expir_date + '</td>' + 
                '<td><button class="remove_btn">Remove</button></td></tr>');    
		});
		$('#rider-inspect-section table .remove_btn').on('click', function(evt) {
            evt.preventDefault();
            var action = '<?php echo admin_url('admin-ajax.php'); ?>';
            var data = {
			    'action': 'pwtc_mileage_remove_rider',
                'member_id': $(this).parent().parent().attr('memberid'),
                'lastname': lastname,
                'firstname': firstname
		    };
			$.post(action, data, remove_rider_cb);
		});
    }

	function lookup_riders_cb(response) {
        var res = JSON.parse(response);
		populate_riders_table(res.members, res.lastname, res.firstname);
        $('#rider-create-lookup-first').val(res.firstname);
        $('#rider-create-lookup-last').val(res.lastname);
        $('#rider-create-form').show();
	}   

	function create_rider_cb(response) {
        var res = JSON.parse(response);
		if (res.error) {
			alert(res.error);
		}
		else {
            populate_riders_table(res.members, res.lastname, res.firstname);
            $('#rider-create-id').val('');
			$('#rider-create-last').val('');
            $('#rider-create-first').val('');
        }
	}   

	function remove_rider_cb(response) {
        var res = JSON.parse(response);
		if (res.error) {
			alert(res.error);
		}
		else {
            populate_riders_table(res.members, res.lastname, res.firstname);
        }
	}   

    $('#rider-update-button').on('click', function(evt) {
        evt.preventDefault();
        alert('Update Riders button pressed');
    });

    $('#rider-inspect-button').on('click', function(evt) {
        evt.preventDefault();
	    $('#rider-inspect-section').show();
	    $('#rider-manage-section').hide();
    });

    $('#rider-back-button').on('click', function(evt) {
        evt.preventDefault();
	    $('#rider-inspect-section').hide();
	    $('#rider-manage-section').show();
    });

    $('#rider-lookup-form').on('submit', function(evt) {
        evt.preventDefault();
        var action = $('#rider-lookup-form').attr('action');
        var data = {
			'action': 'pwtc_mileage_lookup_riders',
			'lastname': $('#rider-lookup-last').val(),
            'firstname': $('#rider-lookup-first').val()
		};
        $.post(action, data, lookup_riders_cb);
    });

    $('#rider-create-form').on('submit', function(evt) {
        evt.preventDefault();
        var action = $('#rider-create-form').attr('action');
        var data = {
			'action': 'pwtc_mileage_create_rider',
			'member_id': $('#rider-create-id').val(),
			'lastname': $('#rider-create-last').val(),
            'firstname': $('#rider-create-first').val(),
            'lookup_first': $('#rider-create-lookup-first').val(),
            'lookup_last': $('#rider-create-lookup-last').val()
		};
        $.post(action, data, create_rider_cb);
    });

	$('#rider-inspect-section').hide();
	$('#rider-create-form').hide();
	$('#rider-manage-section').show();
});
</script>
<div class="wrap">
	<h1><?= esc_html(get_admin_page_title()); ?></h1>
    <div id='rider-manage-section'>
        <p>Press button to synchronize rider list with current PWTC membership database.</p>
        <button id="rider-update-button">Update Riders</button>
        <p>Press button to inspect and modify rider list.</p>
        <button id="rider-inspect-button">Inspect Riders</button>
    </div>
    <div id='rider-inspect-section'>
		<button id='rider-back-button'>Go Back</button>
		<form id="rider-lookup-form" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post">
            <input id="rider-lookup-first" type="text" name="firstname" placeholder="Enter first name"/>        
            <input id="rider-lookup-last" type="text" name="lastname" placeholder="Enter last name"/> 
            <input type="submit" value="Lookup"/>       
        </form>
		<form id="rider-create-form" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post">
            <input id="rider-create-id" type="text" name="memberid" placeholder="Enter member ID" required/>        
            <input id="rider-create-first" type="text" name="firstname" placeholder="Enter first name" required/>        
            <input id="rider-create-last" type="text" name="lastname" placeholder="Enter last name" required/> 
			<input id="rider-create-lookup-first" type="hidden"/>
			<input id="rider-create-lookup-last" type="hidden"/>
            <input type="submit" value="Create Rider"/>       
        </form>
        <table class="pretty"></table>
    </div>
</div>
<?php
