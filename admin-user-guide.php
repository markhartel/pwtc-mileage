<?php
if (!current_user_can($capability)) {
    return;
}
?>
<script type="text/javascript" >
jQuery(document).ready(function($) { 
    $('#user-guide-page h3 a').on('click', function(evt) {
        var wasHidden = $(this).parent().next().is(':hidden');
        $('#user-guide-page div').hide('fast'); 
        if (wasHidden) {
            $(this).parent().next().show('fast');
        }
    }); 
});
</script>
<div class="wrap">
	<h1><?= esc_html(get_admin_page_title()); ?></h1>
    <div id="user-guide-page">
        <h1>How do I...</h1>
        <p>Click on your topic of interest to expand.</p>
        <h3><a href="#">enter mileage from a ride sign-in sheet into the mileage database?</a></h3>
        <div class="initially-hidden">
        <p>After a ride is complete, the ride leader sends the ride sign-in sheet to the
        club statistician who enters the information into the club mileage database.</p>
        <ol>
            <li>Select the <em>Create Ride Sheets</em> item under the <em>Rider Mileage</em> submenu.</li>
            <li>A page displays that shows a table of all the posted rides that are without ride sheets.</li>
            <li>Find the appropriate ride based on the ride sign-in sheet's name and date.</li>
            <li>Select the <em>Create</em> link in the <em>Action</em> column of the ride's table row.</li>
            <li>Press <em>OK</em> when the confirmation dialog pops up.</li>
            <li>A page displays that is the ride sheet leader and mileage entry form.</li>       <li>more to come...</li>
        </ol>
        </div>
        <h3><a href="#">amend a rider's mileage for a ride in the mileage database?</a></h3>
        <div class="initially-hidden">
        <p>Occasionally, a rider wishes to amend their recorded mileage for a ride.
        To do so, they contact the club statistician who then makes the modification.</p>
        <ol>
            <li>Select the <em>Manage Ride Sheets</em> item under the <em>Rider Mileage</em> submenu.</li>
            <li>A page displays that shows a search form for existing ride sheets.</li>
            <li>Select the <em>From Date</em> field and choose the ride's date from the popup calendar.</li>
            <li>A table displays that shows all the existing ride sheets for that day.</li>
            <li>Find the appropriate ride sheet based on the information given by the rider.</li>
            <li>Select the <em>Edit</em> link in the <em>Action</em> column of the ride sheet's table row.</li>
            <li>A page displays that is the ride sheet leader and mileage entry form.</li>
            <li>more to come...</li>
        </ol>
        </div>
        <h3><a href="#">add a new rider to the mileage database?</a></h3>
        <div class="initially-hidden">
        <p>Occasionally, a rider who has just joined the club will not be in the mileage
        datebase. The club statistician will need to add them so that their mileage can
        be recorded. (That rider must have an assigned rider ID from the membership 
        secretary.)</p>
        <ol>
            <li>Select the <em>Manage Riders</em> item under the <em>Rider Mileage</em> submenu.</li>
            <li>A page displays that shows a search form for existing riders.</li>
            <li>more to come...</li>
        </ol>
        </div>
        <h3><a href="#">generate the year-end banquet award reports?</a></h3>
        <div class="initially-hidden">
        <p>In January, the club holds an awards banquet. The club statistician generates
        reports that are used by the banquet organizers to identify the award receipents.
        These reports are based on rider activities for the previous year and should
        only be generated after the start of the new year and after all of the ride 
        sign-up sheets have been entered for the previous year.</p>
        <ol>
            <li>Select the <em>View Reports</em> option under the <em>Rider Mileage</em> submenu.</li>
            <li>A page displays that list the various reports that are available.</li>
            <li>more to come...</li>
        </ol>
        </div>
        <h3><a href="#">upload a UPDMEMBS.DBF file from the membership secretary?</a></h3>
        <div class="initially-hidden">
        <p>Every month, the club membership secretary updates their membership database
        with new club members. A new UPDMEMBS.DBF file is then emailed to the club statistician
        who uploads it into the mileage database to keep it synchronized with the
        master membership database.</p>
        <ol>
            <li>Select the <em>Database Ops</em> item under the <em>Rider Mileage</em> submenu.</li>
            <li>A page displays with buttons that trigger various database operations.</li>
            <li>Press the <em>Synchronize</em> button.</li>
            <li>An <em>UPDMEMBS File</em> field appears, click on it.</li>
            <li>A file selection dialog pops up, use it to open the UPDMEMBS.DBF file on your computer.</li>
            <li>Press the <em>Synchronize</em> button.</li>
            <li>Press <em>OK</em> when the confirmation dialog pops up.</li>
            <li>The synchronize process will begin, wait for it to complete.</li>
            <li>If successful, the following message will appear: <em>Synchronize action success</em>.</li>
            <li>Press the <em>Clear Messages</em> button to remove status messages.</li>
       </ol>
        </div>
        <h3><a href="#">consolidate obsolete rides in the mileage database?</a></h3>
        <div class="initially-hidden">
        <p>Mileage data for only the current and last years are required, all older data 
        is obsolete and should be consolidated to save space. The club statistician performs
        this function after the start of each new year.</p>
        <ol>
            <li>Select the <em>Database Ops</em> item under the <em>Rider Mileage</em> submenu.</li>
            <li>A page displays with buttons that trigger various database operations.</li>
            <li>Press the <em>Consolidate</em> button.</li>
            <li>Press <em>OK</em> when the confirmation dialog pops up.</li>
            <li>The consolidate process will begin, wait for it to complete.</li>
            <li>If successful, the following message will appear: <em>Consolidate action success</em>.</li>
            <li>Press the <em>Clear Messages</em> button to remove status messages.</li>
        </ol>
        </div>
        <h3><a href="#">backup the mileage database?</a></h3>
        <div class="initially-hidden">
        <p>Occasionally, the club statistician should backup the mileage database.
        This involves exporting four CSV files to the local file system.</p>
        <ol>
            <li>Select the <em>Database Ops</em> item under the <em>Rider Mileage</em> submenu.</li>
            <li>A page displays with buttons that trigger various database operations.</li>
            <li>Press the <em>Members</em> button, a file will be downloaded.</li>
            <li>Press the <em>Rides</em> button, a file will be downloaded.</li>
            <li>Press the <em>Mileage</em> button, a file will be downloaded.</li>
            <li>Press the <em>Leaders</em> button, a file will be downloaded.</li>
            <li>Collect the four files that were downloaded and archive to a secure location.</li>
        </ol>
        </div>
        <h3><a href="#">restore the mileage database?</a></h3>
        <div class="initially-hidden">
        <p>The club statistician may need to restore the mileage database from a 
        saved backup. A saved backup consists of four archived CSV files.</p>
        <ol>
            <li>Select the <em>Database Ops</em> item under the <em>Rider Mileage</em> submenu.</li>
            <li>A page displays with buttons that trigger various database operations.</li>
            <li>Press the <em>Restore</em> button.</li>
            <li>more to come...</li>
        </ol>
        </div>
    </div>
</div>
<?php
