<?php
if (!current_user_can('manage_options')) {
        	return;
}
?>
<script type="text/javascript">
jQuery(document).ready(function($) { 
	function populate_riders_table(members) {
		$('#rider-inspect-section table tr').remove();
		$('#rider-inspect-section table').append(
			'<tr><th>Rider ID</th><th>Name</th><th>Expiration Date</th></tr>');
        members.forEach(function(item) {
            $('#rider-inspect-section table').append(
				'<tr memberid="' + item.member_id + '">' + 
				'<td>' + item.member_id + '</td>' +
				'<td>' + item.first_name + ' ' + item.last_name + '</td>' + 
				'<td>' + item.expir_date + '</td></tr>');    
		});
    }

	function lookup_riders_cb(response) {
        var res = JSON.parse(response);
		populate_riders_table(res.members);
	}   

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
        var action = $('#rider-inspect-section form').attr('action');
        var data = {
			'action': 'pwtc_mileage_lookup_riders',
			'lastname': $('#rider-lookup-last').val(),
            'firstname': $('#rider-lookup-first').val()
		};
        $.post(action, data, lookup_riders_cb);
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
