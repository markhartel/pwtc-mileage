<?php
if (!current_user_can('manage_options')) {
        	return;
}
?>
<script type="text/javascript">
jQuery(document).ready(function($) { 
	function populate_riders_table(members, lastname, firstname) {
		$('#rider-inspect-section .riders-tbl tr').remove();
		$('#rider-inspect-section .riders-tbl').append(
			'<tr><th>Member ID</th><th>First Name</th><th>Last Name</th><th>Expiration Date</th><th></th></tr>');
		var fmt = new DateFormatter();
        members.forEach(function(item) {
			var d = fmt.parseDate(item.expir_date, 'Y-m-d');
			var fmtdate = fmt.formatDate(d, 
				'<?php echo $plugin_options['date_display_format']; ?>');
            $('#rider-inspect-section .riders-tbl').append(
				'<tr memberid="' + item.member_id + '">' + 
				'<td>' + item.member_id + '</td>' +
				'<td>' + item.first_name + '</td><td>' + item.last_name + '</td>' + 
				'<td date="' + item.expir_date + '">' + fmtdate + '</td>' + 
                '<td><button class="modify-btn button">Modify</button>' + 
                '<button class="remove-btn button">Remove</button></td></tr>');    
		});
        $('#rider-inspect-section .riders-tbl .modify-btn').on('click', function(evt) {
            evt.preventDefault();
			$("#rider-inspect-section .add-blk .add-frm input[name='memberid']").val(
                $(this).parent().parent().attr('memberid')
            );
			$("#rider-inspect-section .add-blk .add-frm input[name='firstname']").val(
                $(this).parent().parent().find('td').eq(1).html()
            );
			$("#rider-inspect-section .add-blk .add-frm input[name='lastname']").val(
                $(this).parent().parent().find('td').eq(2).html()
            );
			$("#rider-inspect-section .add-blk .add-frm input[name='expdate']").val(
                $(this).parent().parent().find('td').eq(3).html()
            );
			$("#rider-inspect-section .add-blk .add-frm input[name='fmtdate']").val(
                $(this).parent().parent().find('td').eq(3).attr('date')
            );
            $("#rider-inspect-section .add-blk .add-frm input[name='mode']").val('update');
            $("#rider-inspect-section .add-blk .add-frm input[name='memberid']").attr("disabled", "disabled");
            $('#rider-inspect-section .add-blk').show(500);
            $("#rider-inspect-section .add-blk .add-frm input[name='firstname']").focus();
        });
		$('#rider-inspect-section .riders-tbl .remove-btn').on('click', function(evt) {
            evt.preventDefault();
            var action = '<?php echo admin_url('admin-ajax.php'); ?>';
            var data = {
			    'action': 'pwtc_mileage_remove_rider',
                'member_id': $(this).parent().parent().attr('memberid'),
                'lastname': lastname,
                'firstname': firstname
		    };
			$.post(action, data, remove_rider_cb);
		});
    }

	function lookup_riders_cb(response) {
        var res = JSON.parse(response);
		populate_riders_table(res.members, res.lastname, res.firstname);
	}   

	function create_rider_cb(response) {
        var res = JSON.parse(response);
		if (res.error) {
            show_error_msg('#rider-error-msg', res.error);
		}
		else {
            $('#rider-inspect-section .add-blk').hide();
            populate_riders_table(res.members, res.lastname, res.firstname);
        }
	}   

	function remove_rider_cb(response) {
        var res = JSON.parse(response);
		if (res.error) {
            show_error_msg('#rider-error-msg', res.error);
		}
		else {
            populate_riders_table(res.members, res.lastname, res.firstname);
        }
	}   

    $('#rider-manage-section .populate-btn').on('click', function(evt) {
        evt.preventDefault();
        alert('Populate button pressed');
    });

    $('#rider-manage-section .inspect-btn').on('click', function(evt) {
        evt.preventDefault();
	    $('#rider-manage-section').hide();
        $('#rider-inspect-section .riders-tbl tr').remove();
        $('#rider-inspect-section .add-blk').hide();
	    $('#rider-inspect-section').show();
    });

    $('#rider-inspect-section .back-btn').on('click', function(evt) {
        evt.preventDefault();
	    $('#rider-inspect-section').hide();
	    $('#rider-manage-section').show();
    });

    $('#rider-inspect-section .lookup-btn').on('click', function(evt) {
        evt.preventDefault();
        var action = '<?php echo admin_url('admin-ajax.php'); ?>';
        var data = {
            'action': 'pwtc_mileage_lookup_riders',
            'lastname': $(this).attr('lastname'),
            'firstname': ''
		};
        $.post(action, data, lookup_riders_cb);
    });

    $("#rider-inspect-section .add-btn").on('click', function(evt) {
		$("#rider-inspect-section .add-blk .add-frm input[type='text']").val(''); 
		$("#rider-inspect-section .add-blk .add-frm input[type='hidden']").val(''); 
        $("#rider-inspect-section .add-blk .add-frm input[name='mode']").val('insert');
        $("#rider-inspect-section .add-blk .add-frm input[name='memberid']").removeAttr("disabled");
		$('#rider-inspect-section .add-blk').show(500); 
        $("#rider-inspect-section .add-blk .add-frm input[name='memberid']").focus();          
    });

	$("#rider-inspect-section .add-blk .cancel-btn").on('click', function(evt) {
		$('#rider-inspect-section .add-blk').hide();
    });

    $("#rider-inspect-section .add-blk .add-frm input[name='expdate']").datepicker({
  		dateFormat: 'D M d yy',
		altField: "#rider-inspect-section .add-blk .add-frm input[name='fmtdate']",
		altFormat: 'yy-mm-dd'
	});

    $('#rider-inspect-section .add-blk .add-frm').on('submit', function(evt) {
        evt.preventDefault();
        var action = $('#rider-inspect-section .add-blk .add-frm').attr('action');
        var data = {
			'action': 'pwtc_mileage_create_rider',
            'mode': $("#rider-inspect-section .add-blk .add-frm input[name='mode']").val(),
			'member_id': $("#rider-inspect-section .add-blk .add-frm input[name='memberid']").val(),
			'lastname': $("#rider-inspect-section .add-blk .add-frm input[name='lastname']").val(),
			'firstname': $("#rider-inspect-section .add-blk .add-frm input[name='firstname']").val(),
			'exp_date': $("#rider-inspect-section .add-blk .add-frm input[name='fmtdate']").val()
		};
		$.post(action, data, create_rider_cb);
    });

});
</script>
<div class="wrap">
	<h1><?= esc_html(get_admin_page_title()); ?></h1>
    <div id='rider-error-msg'></div>
    <div id='rider-manage-section'>
        <p>
        <div><strong>Populate rider list with current membership database.</strong></div>
        <div><button class="populate-btn button button-primary button-large">Populate</button></div><br>
        <div><strong>Inspect and modify rider list.</strong></div>
        <div><button class="inspect-btn button button-primary button-large">Inspect</button></div>
        </p>
    </div>
    <div id='rider-inspect-section' class="initially-hidden">
        <p><button class='back-btn button button-primary button-large'>Back</button></p>
        <p>
            <button class='lookup-btn button' lastname='a'>A</button>
            <button class='lookup-btn button' lastname='b'>B</button>
            <button class='lookup-btn button' lastname='c'>C</button>
            <button class='lookup-btn button' lastname='d'>D</button>
            <button class='lookup-btn button' lastname='e'>E</button>
            <button class='lookup-btn button' lastname='f'>F</button>
            <button class='lookup-btn button' lastname='g'>G</button>
            <button class='lookup-btn button' lastname='h'>H</button>
            <button class='lookup-btn button' lastname='i'>I</button>
            <button class='lookup-btn button' lastname='j'>J</button>
            <button class='lookup-btn button' lastname='k'>K</button>
            <button class='lookup-btn button' lastname='l'>L</button>
            <button class='lookup-btn button' lastname='m'>M</button>
            <button class='lookup-btn button' lastname='n'>N</button>
            <button class='lookup-btn button' lastname='o'>O</button>
            <button class='lookup-btn button' lastname='p'>P</button>
            <button class='lookup-btn button' lastname='q'>Q</button>
            <button class='lookup-btn button' lastname='r'>R</button>
            <button class='lookup-btn button' lastname='s'>S</button>
            <button class='lookup-btn button' lastname='t'>T</button>
            <button class='lookup-btn button' lastname='u'>U</button>
            <button class='lookup-btn button' lastname='v'>V</button>
            <button class='lookup-btn button' lastname='w'>W</button>
            <button class='lookup-btn button' lastname='x'>X</button>
            <button class='lookup-btn button' lastname='y'>Y</button>
            <button class='lookup-btn button' lastname='z'>Z</button>
        </p>

        <div><button class="add-btn button button-primary button-large">New</button>
		<span class="add-blk initially-hidden">
			<form class="add-frm" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post">
				<table>
				<tr><td>Member ID:</td><td><input name="memberid" type="text" required/></td></tr>
				<tr><td>First Name:</td><td><input name="firstname" type="text" required/></td></tr>
				<tr><td>Last Name:</td><td><input name="lastname" type="text" required/></td></tr>
				<tr><td>Expiration Date:</td><td><input name="expdate" type="text" required/></td></tr>
				</table>
				<input type="hidden" name="fmtdate"/>
				<input type="hidden" name="mode"/>
				<input class="button button-primary" type="submit" value="Create"/>
				<input class="cancel-btn button button-primary" type="button" value="Cancel"/>
			</form>
		</span></div>

        <p><table class="riders-tbl pretty"></table></p>
    </div>
</div>
<?php
