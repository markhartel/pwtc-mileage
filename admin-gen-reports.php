<?php
if (!current_user_can('manage_options')) {
        	return;
}
?>
<script type="text/javascript">
jQuery(document).ready(function($) { 

/*
    function populate_riders_table(members) {
		$('#rider-lookup-results table tr').remove();
        members.forEach(function(item) {
            $('#rider-lookup-results table').append(
				'<tr memberid="' + item.member_id + '">' + 
				'<td>' + item.member_id + '</td>' +
				'<td>' + item.first_name + ' ' + item.last_name + '</td></tr>');    
		});
        $('#rider-lookup-results table tr').on('click', function(evt) {
            $('#report-riderid').val($(this).attr('memberid'));
            $('#report-ridername').html($(this).find('td').first().next().html());
            $("#rider-lookup-results").hide(400);
        });
        return members.length;
    }

	function lookup_riders_cb(response) {
        var res = JSON.parse(response);
		var num_riders = populate_riders_table(res.members);
        if (num_riders == 1) {
            $('#report-riderid').val(res.members[0].member_id);
            $('#report-ridername').html(res.members[0].first_name + ' ' + res.members[0].last_name);
            $("#rider-lookup-results").hide(400);
        }
        else if (num_riders == 0) {
            $("#rider-lookup-results").hide(400);
        }
	} 
*/  

    $('#reports-club-wide a').on('click', function(evt) {
        evt.preventDefault();
        var reportid = $(this).attr('report-id');
        alert('Report ' + reportid + ' selected');
    });

    $('#reports-rider-specific a').on('click', function(evt) {
        evt.preventDefault();
        var reportid = $(this).attr('report-id');
        alert('Report ' + reportid + ' selected');
    });

    $('#report-dialog-close').on('click', function(evt) {
        $("#rider-lookup-results").hide(400);
    });

    $('#rider-lookup-button').on('click', function(evt) {
        //$("#rider-lookup-results").show(400);
        lookup_pwtc_riders(function(riderid, name) {
            $('#report-riderid').val(riderid);
            $('#report-ridername').html(name);            
        });
    });

/*
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
*/

});
</script>
<div class="wrap">
	<h1><?= esc_html(get_admin_page_title()); ?></h1>
    <h3>Club-wide Reports</h3>
    <p>Sort by: 
        <select id='report-sort'>
            <option value="name">Lastname</option> 
            <option value="count" selected>Count</option>
        </select>
    </p>
    <p id='reports-club-wide'>
        <a href='#' report-id='0'>Missing ride sheets</a>,
        <a href='#' report-id='1'>Year-to-date mileage</a>,
        <a href='#' report-id='2'>Last year's mileage</a>,
        <a href='#' report-id='3'>Lifetime mileage</a>,
        <a href='#' report-id='4'>Last year's achievement awards</a>,
        <a href='#' report-id='5'>Year-to-date ride leaders</a>,
        <a href='#' report-id='6'>Last year's ride leaders</a>
    </p>
    <h3>Rider-specific Reports</h3>
    <div>
    <button id="rider-lookup-button">Lookup Rider</button>
    </div>
    <p>Rider ID:
        <input id="report-riderid" type="text" pattern="[0-9]{5}"/>
        <label id="report-ridername"></label>
    </p>
    <p id='reports-rider-specific'>
        <a href='#' report-id='7'>Year-to-date rides</a>,
        <a href='#' report-id='8'>Last year's rides</a>,
        <a href='#' report-id='9'>Year-to-date rides led</a>,
        <a href='#' report-id='10'>Last year's rides led</a>
    </p>
<?php
    include('admin-rider-lookup.php');
?>
</div>
<?php
