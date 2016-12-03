<?php
?>
<script type="text/javascript">
jQuery(document).ready(function($) { 

    function populate_riders_table(members) {
        $('#rider-lookup-results .error-msg').html('');
		$('#rider-lookup-results .lookup-tlb tr').remove();
        members.forEach(function(item) {
            $('#rider-lookup-results .lookup-tlb').append(
				'<tr memberid="' + item.member_id + '">' + 
				'<td>' + item.member_id + '</td>' +
				'<td>' + item.first_name + ' ' + item.last_name + '</td></tr>');    
		});
        $('#rider-lookup-results .lookup-tlb tr').on('click', function(evt) {
            window.pwtc_rider_cb($(this).attr('memberid'), 
                $(this).find('td').first().next().html());
            $("#rider-lookup-results").dialog('close');
        });
        return members.length;
    }

    function lookup_riders_cb(response) {
        var res = JSON.parse(response);
		var num_riders = populate_riders_table(res.members);
        if (num_riders == 1) {
            window.pwtc_rider_cb(res.members[0].member_id, 
                res.members[0].first_name + ' ' + res.members[0].last_name);
            $("#rider-lookup-results").dialog('close');
        }
        else if (num_riders == 0) {
            $('#rider-lookup-results .lookup-tlb tr').remove();
            $('#rider-lookup-results .error-msg').html('No riders found!');
        }
    } 

    $('#rider-lookup-results .lookup-frm').on('submit', function(evt) {
        evt.preventDefault();
        var action = $('#rider-lookup-results .lookup-frm').attr('action');
        var data = {
			'action': 'pwtc_mileage_lookup_riders',
			'memberid': $("#rider-lookup-results .lookup-frm input[name='riderid']").val(),
			'lastname': $("#rider-lookup-results .lookup-frm input[name='lastname']").val(),
            'firstname': $("#rider-lookup-results .lookup-frm input[name='firstname']").val()
		};
        $.post(action, data, lookup_riders_cb);
    });

    $( "#rider-lookup-results" ).dialog({
        autoOpen: false,
        height: 400,
        width: 350,
        modal: true
    });

});
</script>
<div id="rider-lookup-results" title="Lookup Riders">
	<form class="lookup-frm" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post">
        <table>
        <td><label>ID</label></td><td><input type="text" name="riderid"/></td></tr>   
        <td><label>First Name</label></td><td><input type="text" name="firstname"/></td></tr>   
        <td><label>Last Name</label></td><td><input type="text" name="lastname"/></td></tr> 
        </table>
        <input class="button button-primary" type="submit" value="Lookup"/>       
    </form>
    <p>
        <div class='error-msg'></div>
        <table class="lookup-tlb"></table>
    </p>
</div>
<?php
