<?php
if (!current_user_can('manage_options')) {
        	return;
}
?>
<script type="text/javascript">
jQuery(document).ready(function($) { 
    $('#rider-update-button').on('click', function(evt) {
        evt.preventDefault();
        alert('Update Riders button pressed');
    });
});
</script>
<div class="wrap">
	<h1><?= esc_html(get_admin_page_title()); ?></h1>
    <p>Press button to synchronize rider list with current PWTC membership database.</p>
    <button id="rider-update-button">Update Riders</button>
</div>
<?php
