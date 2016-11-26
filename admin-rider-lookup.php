<?php
?>
<script type="text/javascript">
jQuery(document).ready(function($) { 

    $('#rider-lookup-form').on('submit', function(evt) {
        evt.preventDefault();
        var action = $('#rider-lookup-form').attr('action');
        var data = {
			'action': 'pwtc_mileage_lookup_riders',
			'lastname': $('#rider-lookup-last').val(),
            'firstname': $('#rider-lookup-first').val()
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
	<form id="rider-lookup-form" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post">
        <input id="rider-lookup-first" type="text" name="firstname" placeholder="Enter first name"/>   
        <input id="rider-lookup-last" type="text" name="lastname" placeholder="Enter last name"/> 
        <input type="submit" value="Lookup"/>       
    </form>
    <table></table>
</div>
<?php
