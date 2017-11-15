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
    $('#user-guide-page div a').on('click', function(evt) {
        var topic = $(this).attr('topic');
        $('#user-guide-page h3 a[topic="' + topic + '"]').click();
    });
});
</script>
<div class="wrap">
	<h1><?= esc_html(get_admin_page_title()); ?></h1>
    <div id="user-guide-page">
        <h1>How do I...</h1>
        <p>Click on your topic of interest to expand.</p>
        <h3><a href="#">prepare the mileage database for the upcoming year's activities?</a></h3>
        <div class="initially-hidden">
        <p>After the start of the new year, a set of housekeeping actions must be performed 
        on the mileage database before it can handle upcoming club activities. These actions 
        should be executed in the specified order.</p>
        <ol>
            <li>Ensure that the latest UPDMEMBS.DBF file from the membership secretary has been uploaded (see <a href="#" topic="updmembs">topic</a> for details.)</li>
            <li>Ensure that all the ride sign-up sheets for the previous year have been entered (see <a href="#" topic="ridesheet">topic</a> for details.)</li>
            <li>Backup the mileage database (see <a href="#" topic="backup">topic</a> for details.) Collect the files that were downloaded and archive to a secure location.</li> 
            <li>Generate the banquet award reports (see <a href="#" topic="awards">topic</a> for details.) Collect the files that were downloaded and archive to a secure location.</li>
            <li>Consolidate the obsolete rides from the year before last (see <a href="#" topic="consolidate">topic</a> for details.)</li>
        </ol>
        </div>
        <h3><a href="#" topic="ridesheet">enter a ride sign-in sheet into the mileage database?</a></h3>
        <div class="initially-hidden">
        <p>After a ride is complete, the ride leader sends the ride sign-in sheet to the
        club statistician who enters the information into the club mileage database.</p>
        <ol>
            <li>Select the <em>Create Ride Sheets</em> item under the <em>Rider Mileage</em> submenu.</li>
            <li>A page displays that shows a table of all the posted rides that are without ride sheets.</li>
            <li>Find the desired ride in the table (based on the ride sign-in sheet's name and date) and click its <em>Create</em> link in the <em>Actions</em> column.</li>
            <li>Press <em>OK</em> when the confirmation dialog pops up.</li>
            <li>A page displays that is the ride sheet's leader and mileage data entry form.</li>
            <li>Scroll down to the <em>Ride Leaders</em> section. This shows a table that identifies the leaders of the posted ride.</li>
            <li>If leaders are listed that should not be, remove them from the table by clicking their <em>Delete</em> link in the <em>Actions</em> column.</li>
            <li>If leaders are missing, add them to the table by pressing the <em>Lookup Leader</em> button.</li>
            <li>The <em>Lookup Riders</em> dialog pops up (see <a href="#" topic="lookup">topic</a> for details.) Use this to locate a ride leader.</li>
            <li>Two text fields appear, verify that the selected ride leader is correct.</li>
            <li>Press the <em>Add Leader</em> button.</li>
            <li>The new ride leader should appear in the table. Repeat as needed for each missing ride leader.</li>
            <li>Scroll down to the <em>Rider Mileage</em> section. This shows a table that contains the mileages of the riders (initially empty.)</li>
            <li>Add riders by pressing the <em>Lookup Riders</em> button.</li>
            <li>The <em>Lookup Riders</em> dialog pops up (see <a href="#" topic="lookup">topic</a> for details.) Use this to locate a rider.</li>
            <li>Three text fields appear, verify that the selected rider is correct.</li>
            <li>Enter the rider's mileage into the <em>Mileage</em> text field.</li>
            <li>Press the <em>Add Mileage</em> button.</li>
            <li>The new rider mileage should appear in the table. Repeat as needed for each rider on the ride sign-in sheet.</li>
            <li>Press the <em>Back</em> button at the top of the page.</li>
            <li>The page displaying the table of posted rides without ride sheets will return. Notice that the posted ride for which you just created a ride sheet has disappeared from the table.</li>
        </ol>
        </div>
        <h3><a href="#" topic="amendmiles">amend a rider's mileage for a ride in the mileage database?</a></h3>
        <div class="initially-hidden">
        <p>Occasionally, a rider wishes to amend their recorded mileage for a ride.
        To do so, they contact the club statistician who then makes the modification.</p>
        <ol>
            <li>Select the <em>Manage Ride Sheets</em> item under the <em>Rider Mileage</em> submenu.</li>
            <li>A page displays that shows a search form for existing ride sheets.</li>
            <li>Select the <em>From Date</em> field and choose the ride's date from the popup calendar.</li>
            <li>Press the <em>Search</em> button.</li>
            <li>A table displays that shows all the existing ride sheets for that day.</li>
            <li>Find the desired ride sheet in the table and click its <em>Edit</em> link in the <em>Actions</em> column.</li>
            <li>A page displays that is the ride sheet's leader and mileage data entry form.</li>
            <li>Scroll down to the <em>Rider Mileage</em> section.</li>
            <li>Find the desired rider in the table and click their <em>Edit</em> link in the <em>Actions</em> column.</li>
            <li>Three text fields appear, verify that the selected rider is correct.</li>
            <li>Enter the rider's amended mileage into the <em>Mileage</em> text field.</li>
            <li>Press the <em>Modify Mileage</em> button.</li>
            <li>The amended rider mileage should appear in the table.</li>
            <li>Press the <em>Back</em> button at the top of the page.</li>
            <li>The page displaying the search form for ride sheets will return.</li>
        </ol>
        </div>
        <h3><a href="#" topic="lookup">use the Lookup Rider dialog box?</a></h3>
        <div class="initially-hidden">
        <p>The club statistician needs to lookup riders in order to assign ride leaders
        and set rider mileages.</p>
        <p>If you know the rider ID, enter it into the <em>ID</em> text field and press 
        the <em>Lookup</em> button. If the ID is valid, the dialog will close and return 
        that rider.</p>
        <p>Otherwise, you can lookup riders by name using the <em>First Name</em> and 
        <em>Last Name</em> text fields. Enter text into those fields and press the 
        <em>Lookup</em> button. If only one match is found, the dialog will close and return 
        that rider. If multiple matches are found, they will be listed below and one can be 
        choosen which will close the dialog and return that rider.</p>
        </div>
        <h3><a href="#">add a new rider to the mileage database?</a></h3>
        <div class="initially-hidden">
        <p>Occasionally, a rider who has just joined the club will not be in the mileage
        datebase. The club statistician will need to add them so that their mileage can
        be recorded. (That rider must have an assigned rider ID from the membership 
        secretary.) Normally new riders are introducted into the mileage database via 
        the UPDMEMBS.DBF file (see <a href="#" topic="updmembs">topic</a> for details) 
        but sometimes a new rider will attend a ride before the latest UPDMEMBS.DBF file
        is delivered to the club statistician. This activity is to handle that case.</p>
        <ol>
            <li>Select the <em>Manage Riders</em> item under the <em>Rider Mileage</em> submenu.</li>
            <li>A page displays that shows a search form for existing riders.</li>
            <li>Press the <em>New</em> button.</li>
            <li>Press <em>OK</em> when the confirmation dialog pops up.</li>
            <li>Four text fields appear.</li>
            <li>Enter the new rider's ID into the <em>ID</em> text field.</li>
            <li>Enter the new rider's first name into the <em>First Name</em> text field.</li>
            <li>Enter the new rider's last name into the <em>Last Name</em> text field.</li>
            <li>Click on the <em>Expiration Date</em> field and select the new rider's membership expiration date from the popup calendar. (If not known, just use the current date.)</li>
            <li>Press the <em>Create</em> button.</li>
        </ol>
        </div>
        <h3><a href="#" topic="awards">generate the year-end banquet award reports?</a></h3>
        <div class="initially-hidden">
        <p>In January, the club holds an awards banquet. The club statistician generates
        reports that are used by the banquet organizers to identify the award receipents.
        These reports are based on rider activities for the previous year and should
        only be generated after the start of the new year and after all of the ride 
        sign-up sheets for the previous year have been entered. The reports consist of 
        two sets of four files: a CSV file set for spreadsheet applications and 
        a PDF file set for printing hardcopies.</p>
        <ol>
            <li>Select the <em>View Reports</em> option under the <em>Rider Mileage</em> submenu.</li>
            <li>A page displays that list the various reports that are available.</li>
            <li>Choose the <em>CSV File</em> option from the <em>Download</em> selection box.</li>
            <li>Click all four links in the <em>Award Reports</em> section, four CSV files will be downloaded.</li>
            <li>Choose the <em>PDF File</em> option from the <em>Download</em> selection box.</li>
            <li>Click all four links in the <em>Award Reports</em> section, four PDF files will be downloaded.</li>
            <li>Collect the eight downloaded files and send to the banquet organizers.</li>
        </ol>
        </div>
        <h3><a href="#" topic="updmembs">upload an UPDMEMBS.DBF file from the membership secretary?</a></h3>
        <div class="initially-hidden">
        <p>Every month, the club membership secretary updates their membership database
        with new club members. A new UPDMEMBS.DBF file is then emailed to the club statistician
        who uploads it into the mileage database to keep it synchronized with the
        master membership database.</p>
        <ol>
            <li>Select the <em>Database Ops</em> item under the <em>Rider Mileage</em> submenu.</li>
            <li>A page displays with buttons that execute various database operations.</li>
            <li>Press the <em>Synchronize</em> button.</li>
            <li>An <em>UPDMEMBS File</em> field appears, click on it.</li>
            <li>A file selection dialog pops up, use it to open the UPDMEMBS.DBF file on your computer.</li>
            <li>Press the <em>Synchronize</em> button.</li>
            <li>Press <em>OK</em> when the confirmation dialog pops up.</li>
            <li>The synchronize process will begin, wait for it to complete.</li>
            <li>If successful, the following message will appear: <em>Synchronize action success</em>.</li>
            <li>Press the <em>Clear Messages</em> button to clear the status message.</li>
       </ol>
        </div>
        <h3><a href="#" topic="consolidate">consolidate obsolete rides in the mileage database?</a></h3>
        <div class="initially-hidden">
        <p><strong>WARNING: this operation has the potential to corrupt the mileage database, 
        so you should first backup the mileage database before proceeding.</strong> Mileage data 
        for only the current and last years are required, all older data is obsolete 
        and should be consolidated to save space. The club statistician performs
        this function after the start of each new year.</p>
        <ol>
            <li>Select the <em>Database Ops</em> item under the <em>Rider Mileage</em> submenu.</li>
            <li>A page displays with buttons that execute various database operations.</li>
            <li>Backup the mileage database (see <a href="#" topic="backup">topic</a> for details.)</li>
            <li>Press the <em>Consolidate</em> button.</li>
            <li>Press <em>OK</em> when the confirmation dialog pops up.</li>
            <li>The consolidate process will begin, wait for it to complete.</li>
            <li>If successful, the following message will appear: <em>Consolidate action success</em>.</li>
            <li>Press the <em>Clear Messages</em> button to clear the status message.</li>
        </ol>
        </div>
        <h3><a href="#" topic="backup">backup the mileage database?</a></h3>
        <div class="initially-hidden">
        <p>Occasionally, the club statistician should backup the mileage database.
        This involves exporting four CSV files to the local file system.</p>
        <ol>
            <li>Select the <em>Database Ops</em> item under the <em>Rider Mileage</em> submenu.</li>
            <li>A page displays with buttons that execute various database operations.</li>
            <li>Press the <em>Members</em> button, a CSV file will be downloaded.</li>
            <li>Press the <em>Rides</em> button, a CSV file will be downloaded.</li>
            <li>Press the <em>Mileage</em> button, a CSV file will be downloaded.</li>
            <li>Press the <em>Leaders</em> button, a CSV file will be downloaded.</li>
            <li>Collect the four files that were downloaded and archive to a secure location.</li>
        </ol>
        </div>
        <h3><a href="#">restore the mileage database?</a></h3>
        <div class="initially-hidden">
        <p><strong>WARNING: this operation will overwrite the current mileage database, 
        therefore it should only be performed by the administrator.</strong> The 
        administrator may need to restore the mileage database from a saved backup. 
        A saved backup consists of four archived CSV files.</p>
        <ol>
            <li>Obtain the four CSV files that are a mileage database backup.</li>
            <li>Select the <em>Database Ops</em> item under the <em>Rider Mileage</em> submenu.</li>
            <li>A page displays with buttons that execute various database operations.</li>
            <li>Press the <em>Restore</em> button.</li>
            <li>Four file upload fields appear.</li>
            <li>Click on the <em>Members File</em> field.</li>
            <li>A file selection dialog pops up, use it to open the members backup CSV file on your computer.</li>
            <li>Click on the <em>Rides File</em> field.</li>
            <li>A file selection dialog pops up, use it to open the rides backup CSV file on your computer.</li>
            <li>Click on the <em>Mileage File</em> field.</li>
            <li>A file selection dialog pops up, use it to open the mileage backup CSV file on your computer.</li>
            <li>Click on the <em>Leaders File</em> field.</li>
            <li>A file selection dialog pops up, use it to open the leaders backup CSV file on your computer.</li>
            <li>Press the <em>Restore</em> button.</li>
            <li>Press <em>OK</em> when the confirmation dialog pops up.</li>
            <li>The restore process will begin, wait for it to complete.</li>
            <li>If successful, the following message will appear: <em>Restore action success</em>.</li>
            <li>Press the <em>Clear Messages</em> button to clear the status message.</li>
        </ol>
        </div>
    </div>
</div>
<?php
