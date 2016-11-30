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
if (null === $job_status) {
?>
    <p>Press button to perform year-end mileage database consolidation functions:
        <ol>
            <li>Backup mileage database to hard drive</li>
            <li>Compress <?php echo(intval(date('Y'))-2); ?> club rides to single entry</li>
        </ol>
    </p>
	<form method="POST">
		<input type="submit" name="consolidate" value="Consolidate" class="button button-primary button-large">
    </form>
<?php
} else {
?>
    <p>Status: <?php echo $job_status['status']; ?><br>Timestamp: <?php echo $job_status['timestamp']; ?></p>
<?php
}
?>
</div>
<?php
