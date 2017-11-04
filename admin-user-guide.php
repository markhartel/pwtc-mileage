<?php
if (!current_user_can($capability)) {
    return;
}
?>
<script type="text/javascript" >
jQuery(document).ready(function($) { 
    $('#user-guide-page h3').on('click', function(evt) {
        $('#user-guide-page div').hide('fast'); 
        $(this).next().show('fast');
    }); 
});
</script>
<div class="wrap">
	<h1><?= esc_html(get_admin_page_title()); ?></h1>
    <div id="user-guide-page">
        <h1>How do I...</h1>
        <h3 style="cursor: pointer">enter mileage from a ride sign-in sheet into the mileage database?</h3>
        <div class="initially-hidden">
        <p>After a ride is complete, the ride leader sends the ride sign-in sheet to the
        club statistician who enters the information into the club mileage database.</p>
        <ol>
        <li>Login to the <em>pwtc.com</em> website.</li>
        <li>Navigate to the administrator menus page.</li>
        <li>Select the <em>Manage Ride Sheets</em> option under the <em>Rider Mileage</em> submenu.</li>
        <li>A page displays that shows a table of all the posted rides that are without ride sheets.</li>
        <li>Find the appropriate ride based on the ride sign-in sheet's name and date.</li>
        <li>Select the <em>Create</em> link in the <em>Action</em> column of the ride's table row.</li>
        <li>Select <em>OK</em> when the confirmation dialog pops up.</li>
        <li>A page displays that is the ride sheet leader and mileage entry form.</li>        <li>more...</li>
        </ol>
        </div>
        <h3 style="cursor: pointer">amend a rider's mileage for a ride in the mileage database?</h3>
        <div class="initially-hidden">
        <p>Occasionally, a rider wishes to amend their recorded mileage for a ride.
        To do so, they contact the club statistician who then makes the modification.</p>
        <ol>
        <li>Login to the <em>pwtc.com</em> website.</li>
        <li>Navigate to the administrator menus page.</li>
        <li>Select the <em>Manage Riders</em> option under the <em>Rider Mileage</em> submenu.</li>
        <li>A page displays that shows a search form for existing ride sheets.</li>
        <li>Select the <em>From Date</em> field and choose the ride's date from the popup calendar.</li>
        <li>A table displays that shows all the existing ride sheets for that day.</li>
        <li>Find the appropriate ride sheet based on the information given by the rider.</li>
        <li>Select the <em>Edit</em> link in the <em>Action</em> column of the ride sheet's table row.</li>
        <li>A page displays that is the ride sheet leader and mileage entry form.</li>
        <li>more...</li>
        </ol>
        </div>
        <h3 style="cursor: pointer">add a new rider to the mileage database?</h3>
        <div class="initially-hidden">
        <p>Occasionally, a rider who has just joined the club will not be in the mileage
        datebase. The club statistician will then add them so that their mileage can
        be recorded. (That rider will still need to have an assigned rider ID from the
        membership secretary.)</p>
        <ol>
        <li>Login to the <em>pwtc.com</em> website.</li>
        <li>Navigate to the administrator menus page.</li>
        <li>Select the <em>Manage Riders</em> option under the <em>Rider Mileage</em> submenu.</li>
        <li>more...</li>
        </ol>
        </div>
        <h3 style="cursor: pointer">generate the year-end banquet award reports?</h3>
        <div class="initially-hidden">
        <p>In January, the club holds an awards banquet. The club statistician generates
        reports that are used by the banquet organizers to identify the award receipents.</p>
        <ol>
        <li>Login to the <em>pwtc.com</em> website.</li>
        <li>Navigate to the administrator menus page.</li>
        <li>Select the <em>View Reports</em> option under the <em>Rider Mileage</em> submenu.</li>
        <li>more...</li>
        </ol>
        </div>
        <h3 style="cursor: pointer">upload a UPDMEMBS.DBF file from the membership secretary?</h3>
        <div class="initially-hidden">
        <p>Every month, the club membership secretary updates their membership database
        with new club members. A new UPDMEMBS.DBF file is then emailed to the club statistician
        who uploads it into the mileage database to keep its rider list in sync with the
        membership database.</p>
        <ol>
            <li>Login to the <em>pwtc.com</em> website.</li>
            <li>Navigate to the administrator menus page.</li>
            <li>Select the <em>Database Ops</em> option under the <em>Rider Mileage</em> submenu.</li>
            <li>more...</li>
        </ol>
        </div>
        <h3 style="cursor: pointer">backup the mileage database?</h3>
        <div class="initially-hidden">
        <p></p>
        <ol>
            <li>Login to the <em>pwtc.com</em> website.</li>
            <li>Navigate to the administrator menus page.</li>
            <li>Select the <em>Database Ops</em> option under the <em>Rider Mileage</em> submenu.</li>
            <li>more...</li>
        </ol>
        </div>
        <h3 style="cursor: pointer">restore the mileage database?</h3>
        <div class="initially-hidden">
        <p></p>
        <ol>
            <li>Login to the <em>pwtc.com</em> website.</li>
            <li>Navigate to the administrator menus page.</li>
            <li>Select the <em>Database Ops</em> option under the <em>Rider Mileage</em> submenu.</li>
            <li>more...</li>
        </ol>
        </div>
    </div>
</div>
<?php
