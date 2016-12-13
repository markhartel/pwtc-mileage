<?php
if (!current_user_can('manage_options')) {
    return;
}
?>
<script type="text/javascript">
jQuery(document).ready(function($) { 
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
    <form method="POST">
    <div><p>
        <h2>Export Memberships</h2>
        <input type="submit" name="export_members" value="Export to CSV" class="button button-primary button-large">
    </p></div>
    <div><p>
        <h2>Export Ridesheets</h2>
        <input type="submit" name="export_rides" value="Export to CSV" class="button button-primary button-large">
    </p></div>
    <div><p>
        <h2>Export Rider Mileage</h2>
        <input type="submit" name="export_mileage" value="Export to CSV" class="button button-primary button-large">
    </p></div>
    <div><p>
        <h2>Export Ride Leaders</h2>
        <input type="submit" name="export_leaders" value="Export to CSV" class="button button-primary button-large">
    </p></div>
    </form>
<?php
    include('admin-rider-lookup.php');
}
?>
</div>
<?php
