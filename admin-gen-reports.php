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
            open_error_dialog(res.error);
        }
        else {
            $('#report-results-section h2').html(res.title);
            populate_report_table(res.data, res.header);
	        $('#report-main-section').hide('fast', function() {
                $('#report-results-section').fadeIn('slow');
                $('#report-results-section .back-btn').focus();                
            });
        }
	}   

    $('#report-main-section .ride-mileage a').on('click', function(evt) {
        evt.preventDefault();
        if ($('#report-main-section .dwnld-file').is(':checked')) {
            $('#report-main-section .download').html(
                '<form method="post">' + 
                '<input type="hidden" name="export_report"/>' +
                '<input type="hidden" name="report_id" value="' + $(this).attr('report-id') + '"/>' +
                '<input type="hidden" name="sort" value="' + 
                    $('#report-main-section .mileage-sort-slt').val() + '"/>' +
                '</form>');
            $('#report-main-section .download form').submit();
        }
        else {
            var action = '<?php echo admin_url('admin-ajax.php'); ?>';
            var data = {
                'action': 'pwtc_mileage_generate_report',
                'report_id': $(this).attr('report-id'),
                'sort': $('#report-main-section .mileage-sort-slt').val()
            };
            $.post(action, data, generate_report_cb);
        }
    });

    $('#report-main-section .ride-leader a').on('click', function(evt) {
        evt.preventDefault();
        if ($('#report-main-section .dwnld-file').is(':checked')) {
            $('#report-main-section .download').html(
                '<form method="post">' + 
                '<input type="hidden" name="export_report"/>' +
                '<input type="hidden" name="report_id" value="' + $(this).attr('report-id') + '"/>' +
                '<input type="hidden" name="sort" value="' + 
                    $('#report-main-section .leader-sort-slt').val() + '"/>' +
                '</form>');
            $('#report-main-section .download form').submit();
        }
        else {
            var action = '<?php echo admin_url('admin-ajax.php'); ?>';
            var data = {
                'action': 'pwtc_mileage_generate_report',
                'report_id': $(this).attr('report-id'),
                'sort': $('#report-main-section .leader-sort-slt').val()
            };
            $.post(action, data, generate_report_cb);
        }
    });

    $('#report-main-section .specific-rider a').on('click', function(evt) {
        evt.preventDefault();
        var memberid = $('#report-main-section .riderid').html();
        if (memberid === '') {
            open_error_dialog('You must first select a rider before choosing this report.');
        }
        else {
            if ($('#report-main-section .dwnld-file').is(':checked')) {
                $('#report-main-section .download').html(
                    '<form method="post">' + 
                    '<input type="hidden" name="export_report"/>' +
                    '<input type="hidden" name="report_id" value="' + $(this).attr('report-id') + '"/>' +
                    '<input type="hidden" name="member_id" value="' + memberid + '"/>' +
                    '<input type="hidden" name="name" value="' + 
                        $('#report-main-section .ridername').html() + '"/>' +
                    '</form>');
                $('#report-main-section .download form').submit();
            }
            else {
                var action = '<?php echo admin_url('admin-ajax.php'); ?>';
                var data = {
                    'action': 'pwtc_mileage_generate_report',
                    'report_id': $(this).attr('report-id'),
                    'member_id': memberid,
                    'name': $('#report-main-section .ridername').html()
                };
                $.post(action, data, generate_report_cb);
            }
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
        $('#report-results-section').fadeOut('slow', function() {
	        $('#report-main-section').show('fast');
        });
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
    <div id='report-main-section'>
        <p><input type="checkbox" class="dwnld-file"/>download report file</p>
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
        <div class='download'></div>
    </div>
    <div id='report-results-section' class="initially-hidden">
        <p><button class='back-btn button button-primary button-large'>Back</button></p>
		<p><h2></h2></p>
        <p><table class="results-tbl pretty"></table></p>
    </div>
<?php
    include('admin-rider-lookup.php');
}
?>
</div>
<?php
