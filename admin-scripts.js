
function populate_riders_table(members) {
		jQuery('#rider-lookup-results table tr').remove();
        members.forEach(function(item) {
            jQuery('#rider-lookup-results table').append(
				'<tr memberid="' + item.member_id + '">' + 
				'<td>' + item.member_id + '</td>' +
				'<td>' + item.first_name + ' ' + item.last_name + '</td></tr>');    
		});
        jQuery('#rider-lookup-results table tr').on('click', function(evt) {
            window.pwtc_rider_cb(jQuery(this).attr('memberid'), 
                jQuery(this).find('td').first().next().html());
            jQuery("#rider-lookup-results").hide(400);
        });
        return members.length;
}

function lookup_riders_cb(response) {
        var res = JSON.parse(response);
		var num_riders = populate_riders_table(res.members);
        if (num_riders == 1) {
            window.pwtc_rider_cb(res.members[0].member_id, 
                res.members[0].first_name + ' ' + res.members[0].last_name);
            jQuery("#rider-lookup-results").hide(400);
        }
        else if (num_riders == 0) {
            jQuery("#rider-lookup-results").hide(400);
        }
}   

function lookup_pwtc_riders(mycb) {
    jQuery("#rider-lookup-results").show(400);
    window.pwtc_rider_cb = mycb;
    //window.pwtc_rider_cb('99999', 'John Doe');
} 