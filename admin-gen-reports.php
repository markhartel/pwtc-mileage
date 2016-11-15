<?php
if (!current_user_can('manage_options')) {
        	return;
}
?>
<script type="text/javascript">
jQuery(document).ready(function($) { 

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

    $('#rider-lookup-button').on('click', function(evt) {
        lookup_pwtc_riders(function(riderid, name) {
            $('#report-riderid').html(riderid);
            $('#report-ridername').html(name);            
        });
    });

    $('#report-results-section').hide();
	$('#report-main-section').show();

});
</script>
<div class="wrap">
	<h1><?= esc_html(get_admin_page_title()); ?></h1>
    <div id='report-main-section'>
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
        <p>
            <button id="rider-lookup-button">Lookup Rider</button>&nbsp;
            <label id="report-riderid"/></label>&nbsp;
            <label id="report-ridername"></label>
        </p>
        <p id='reports-rider-specific'>
            <a href='#' report-id='7'>Year-to-date rides</a>,
            <a href='#' report-id='8'>Last year's rides</a>,
            <a href='#' report-id='9'>Year-to-date rides led</a>,
            <a href='#' report-id='10'>Last year's rides led</a>
        </p>
    </div>
    <div id='report-results-section'>
		<button>Back to Reports</button>
		<h2></h2>
        <table></table>
    </div>
<?php
    include('admin-rider-lookup.php');
?>
</div>
<?php
