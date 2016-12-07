<?php
if (!current_user_can('edit_posts')) {
    return;
}
?>
<script type="text/javascript">
jQuery(document).ready(function($) { 

	function populate_report_table(data, header) {
	    $('#report-results-section .results-tbl tr').remove();
        var str = '<tr>';
        header.forEach(function(item) {
            str += '<th>' + item + '</th>';
        });
        str += '</tr>';
        $('#report-results-section .results-tbl').append(str);
        data.forEach(function(row) {
            str = '<tr>';
            row.forEach(function(item) {
                str += '<td>' + item + '</td>';
            });
            str += '</tr>';
            $('#report-results-section .results-tbl').append(str);
        });
    }

	function generate_report_cb(response) {
        var res = JSON.parse(response);
        if (res.error) {
            show_error_msg('#report-error-msg', res.error);
        }
        else {
            $('#report-results-section h2').html(res.title);
            populate_report_table(res.data, res.header);
            $('#report-results-section').show();
	        $('#report-main-section').hide();
        }
	}   

    $('#report-main-section .ride-mileage a').on('click', function(evt) {
        evt.preventDefault();
        var action = '<?php echo admin_url('admin-ajax.php'); ?>';
        var data = {
            'action': 'pwtc_mileage_generate_report',
            'report_id': $(this).attr('report-id'),
            'sort': $('#report-main-section .mileage-sort-slt').val()
		};
        $.post(action, data, generate_report_cb);
    });

    $('#report-main-section .ride-leader a').on('click', function(evt) {
        evt.preventDefault();
        var action = '<?php echo admin_url('admin-ajax.php'); ?>';
        var data = {
            'action': 'pwtc_mileage_generate_report',
            'report_id': $(this).attr('report-id'),
            'sort': $('#report-main-section .leader-sort-slt').val()
		};
        $.post(action, data, generate_report_cb);
    });

    $('#report-main-section .specific-rider a').on('click', function(evt) {
        evt.preventDefault();
        var action = '<?php echo admin_url('admin-ajax.php'); ?>';
        var data = {
            'action': 'pwtc_mileage_generate_report',
            'report_id': $(this).attr('report-id'),
            'member_id': $('#report-main-section .riderid').html(),
            'name': $('#report-main-section .ridername').html()
		};
        if (data.member_id === '') {
            show_error_msg('#report-error-msg', 
                'You must first select a rider before choosing this report.');
        }
        else {
            $.post(action, data, generate_report_cb);
        }
    });

    $('#report-main-section .lookup-btn').on('click', function(evt) {
        lookup_pwtc_riders(function(riderid, name) {
            $('#report-main-section .riderid').html(riderid);
            $('#report-main-section .ridername').html(name);            
        });       
    });

    $('#report-results-section .back-btn').on('click', function(evt) {
        evt.preventDefault();
        $('#report-results-section').hide();
	    $('#report-main-section').show();
    });

});
</script>
<div class="wrap">
	<h1><?= esc_html(get_admin_page_title()); ?></h1>
    <div id='report-error-msg'></div>
    <div id='report-main-section'>
        <h3>Ride Mileage Reports</h3>
        <p>Sort by: 
            <select class='mileage-sort-slt'>
                <option value="last_name, first_name">Name</option> 
                <option value="mileage desc" selected>Mileage</option>
            </select>
        </p>
        <div class='ride-mileage'>
            <div><a href='#' report-id='ytd_miles'>Year-to-date mileage</a></div>
            <div><a href='#' report-id='ly_miles'>Last year's mileage</a></div>
            <div><a href='#' report-id='lt_miles'>Lifetime mileage</a></div>
            <div><a href='#' report-id='ly_lt_achvmnt'>Last year's achievement awards</a></div>
        </div>
        <h3>Ride Leader Reports</h3>
        <p>Sort by: 
            <select class='leader-sort-slt'>
                <option value="last_name, first_name">Name</option> 
                <option value="rides_led desc" selected>Rides Led</option>
            </select>
        </p>
        <div class='ride-leader'>
            <div><a href='#' report-id='ytd_led'>Year-to-date ride leaders</a></div>
            <div><a href='#' report-id='ly_led'>Last year's ride leaders</a></div>
        </div>
        <h3>Individual Rider Reports</h3>
        <p>
            <button class="lookup-btn button button-primary">Lookup Rider</button>&nbsp;
            <label class="riderid"/></label>&nbsp;
            <label class="ridername"></label>
        </p>
        <div class='specific-rider'>
            <div><a href='#' report-id='ytd_rides'>Year-to-date rides</a></div>
            <div><a href='#' report-id='ly_rides'>Last year's rides</a></div>
            <div><a href='#' report-id='ytd_rides_led'>Year-to-date rides led</a></div>
            <div><a href='#' report-id='ly_rides_led'>Last year's rides led</a></div>
        </div>
    </div>
    <div id='report-results-section' class="initially-hidden">
        <p><button class='back-btn button button-primary button-large'>Back</button></p>
		<p><h2></h2></p>
        <p><table class="results-tbl pretty"></table></p>
    </div>
<?php
    include('admin-rider-lookup.php');
?>
</div>
<?php
