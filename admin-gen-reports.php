<?php
if (!current_user_can('manage_options')) {
        	return;
}
?>
<script type="text/javascript">
jQuery(document).ready(function($) { 

	function populate_report_table(data, header) {
	    $('#report-results-section table tr').remove();
        var str = '<tr>';
        header.forEach(function(item) {
            str += '<th>' + item + '</th>';
        });
        str += '</tr>';
        $('#report-results-section table').append(str);
        data.forEach(function(row) {
            str = '<tr>';
            row.forEach(function(item) {
                str += '<td>' + item + '</td>';
            });
            str += '</tr>';
            $('#report-results-section table').append(str);
        });
    }

	function generate_report_cb(response) {
        var res = JSON.parse(response);
        if (res.error) {
            alert(res.error);
            //show_error_msg('#report-msg', res.error);
        }
        else {
            $('#report-results-section h2').html(res.title);
            populate_report_table(res.data, res.header);
            $('#report-results-section').show();
	        $('#report-main-section').hide();
        }
	}   

    $('#reports-ride-mileage a').on('click', function(evt) {
        evt.preventDefault();
        var action = '<?php echo admin_url('admin-ajax.php'); ?>';
        var data = {
            'action': 'pwtc_mileage_generate_report',
            'report_id': $(this).attr('report-id'),
            'sort': $('#report-mileage-sort').val()
		};
        $.post(action, data, generate_report_cb);
    });

    $('#reports-ride-leader a').on('click', function(evt) {
        evt.preventDefault();
        var action = '<?php echo admin_url('admin-ajax.php'); ?>';
        var data = {
            'action': 'pwtc_mileage_generate_report',
            'report_id': $(this).attr('report-id'),
            'sort': $('#report-leader-sort').val()
		};
        $.post(action, data, generate_report_cb);
    });

    $('#reports-specific-rider a').on('click', function(evt) {
        evt.preventDefault();
        var action = '<?php echo admin_url('admin-ajax.php'); ?>';
        var data = {
            'action': 'pwtc_mileage_generate_report',
            'report_id': $(this).attr('report-id'),
            'member_id': $('#report-riderid').html(),
            'name': $('#report-ridername').html()
		};
        if (data.member_id === '') {
            alert('Select a rider.');
            //show_error_msg('#report-msg', 
            //    'You must first select a rider before choosing this report.');
        }
        else {
            $.post(action, data, generate_report_cb);
        }
    });

    $('#rider-lookup-button').on('click', function(evt) {
        lookup_pwtc_riders(function(riderid, name) {
            $('#report-riderid').html(riderid);
            $('#report-ridername').html(name);            
        });       
    });

    $('#report-back-button').on('click', function(evt) {
        evt.preventDefault();
        $('#report-results-section').hide();
	    $('#report-main-section').show();
    });

    $('#report-results-section').hide();
	$('#report-main-section').show();

});
</script>
<div class="wrap">
	<h1><?= esc_html(get_admin_page_title()); ?></h1>
    <div id='report-main-section'>
        <h3>Ride Mileage Reports</h3>
        <p>Sort by: 
            <select id='report-mileage-sort'>
                <option value="last_name, first_name">Name</option> 
                <option value="mileage desc" selected>Mileage</option>
            </select>
        </p>
        <p id='reports-ride-mileage'>
            <a href='#' report-id='ytd_miles'>Year-to-date mileage</a>,
            <a href='#' report-id='2'>Last year's mileage</a>,
            <a href='#' report-id='3'>Lifetime mileage</a>,
            <a href='#' report-id='ly_lt_achvmnt'>Last year's achievement awards</a>
        </p>
        <h3>Ride Leader Reports</h3>
        <p>Sort by: 
            <select id='report-leader-sort'>
                <option value="last_name, first_name">Name</option> 
                <option value="rides_led desc" selected>Rides Led</option>
            </select>
        </p>
        <p id='reports-ride-leader'>
            <a href='#' report-id='ytd_led'>Year-to-date ride leaders</a>,
            <a href='#' report-id='6'>Last year's ride leaders</a>
        </p>
        <h3>Individual Rider Reports</h3>
        <p>
            <button id="rider-lookup-button">Lookup Rider</button>&nbsp;
            <label id="report-riderid"/></label>&nbsp;
            <label id="report-ridername"></label>
        </p>
        <p id='reports-specific-rider'>
            <a href='#' report-id='ytd_rides'>Year-to-date rides</a>,
            <a href='#' report-id='8'>Last year's rides</a>,
            <a href='#' report-id='9'>Year-to-date rides led</a>,
            <a href='#' report-id='10'>Last year's rides led</a>
        </p>
    </div>
    <div id='report-results-section'>
		<button id="report-back-button">Back to Reports</button>
		<h2></h2>
        <table class="pretty"></table>
    </div>
<?php
    include('admin-rider-lookup.php');
?>
</div>
<?php
