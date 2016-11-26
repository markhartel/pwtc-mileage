<?php
if (!current_user_can('manage_options')) {
        	return;
}
?>
<script type="text/javascript">
jQuery(document).ready(function($) { 
    function consolidate_cb(response) {
        var res = JSON.parse(response);
        $('#consolidate-msg').html(res.message);
	}   

    $('#consolidate-btn').on('click', function(evt) {
        evt.preventDefault();
        var action = '<?php echo admin_url('admin-ajax.php'); ?>';
        var data = {
            'action': 'pwtc_mileage_consolidate',
		};
        $.post(action, data, consolidate_cb);
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
    <button id="consolidate-btn">Consolidate</button>
    <p id="consolidate-msg"></p>
</div>
<?php
