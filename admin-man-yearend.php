<?php
if (!current_user_can('manage_options')) {
        	return;
}
?>
<script type="text/javascript">
jQuery(document).ready(function($) { 
    $('#rider-update-button').on('click', function(evt) {
        evt.preventDefault();
        alert('Consolidate button pressed');
    });
});
</script>
<div class="wrap">
	<h1><?= esc_html(get_admin_page_title()); ?></h1>
    <p>Press button to perform year-end mileage database consolidation functions:
        <ol>
            <li>Backup mileage database to hard drive</li>
            <li>Compress <?php echo(intval(date('Y'))-2); ?> club rides to single entry</li>
        </ol>
    </p>
    <button id="rider-update-button">Consolidate</button>
</div>
<?php
