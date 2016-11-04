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

    $('#rider-inspect-button').on('click', function(evt) {
        evt.preventDefault();
	    $('#rider-inspect-section').show();
	    $('#rider-manage-section').hide();
    });

    $('#rider-back-button').on('click', function(evt) {
        evt.preventDefault();
	    $('#rider-inspect-section').hide();
	    $('#rider-manage-section').show();
    });

    $('#rider-inspect-section form').on('submit', function(evt) {
        evt.preventDefault();
		$('#rider-inspect-section table tr').remove();
        var action = $('#rider-inspect-section form').attr('action');
        var data = {
			'action': '???',
			'lastname': $('#rider-lookup-last').val(),
            'firstname': $('#rider-lookup-first').val()
		};
        alert('Lookup button pressed');
    });

	$('#rider-inspect-section').hide();
	$('#rider-manage-section').show();
});
</script>
<div class="wrap">
	<h1><?= esc_html(get_admin_page_title()); ?></h1>
    <div id='rider-manage-section'>
        <p>Press button to synchronize rider list with current PWTC membership database.</p>
        <button id="rider-update-button">Update Riders</button>
        <p>Press button to inspect and modify rider list.</p>
        <button id="rider-inspect-button">Inspect Riders</button>
    </div>
    <div id='rider-inspect-section'>
		<button id='rider-back-button'>Go Back</button>
		<form action="<?php echo admin_url('admin-ajax.php'); ?>" method="post">
            <input id="rider-lookup-first" type="text" name="firstname" placeholder="Enter first name"/>        
            <input id="rider-lookup-last" type="text" name="lastname" placeholder="Enter last name"/> 
            <input type="submit" value="Lookup"/>       
        </form>
        <table>
        </table>
    </div>
</div>
<?php
