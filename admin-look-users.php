<div class="wrap">
	<h1><?= esc_html(get_admin_page_title()); ?></h1>
<?php
if (!current_user_can($capability)) {
?> 
    <p><strong>Access Denied</strong> - you do not have the rights to view this page.</p>
<?php   
}
else if ($plugin_options['admin_maint_mode'] and !current_user_can('manage_options')) {
?> 
    <p><strong>Access Denied</strong> - the mileage database is maintenance mode.</p>
<?php       
}
else {
?>
<script type="text/javascript">
jQuery(document).ready(function($) { 
	function populate_users_table(users) {
		$('#user-lookup-section .users-div').empty();
        if (users.length > 0) {
            var action_label = '';
            if (users[0].editurl) {
                action_label = '<th>Actions</th>';
            }
            $('#user-lookup-section .users-div').append('<table class="rwd-table">' +
                '<tr><th>First Name</th><th>Last Name</th><th>Email</th><th>Role</th><th>Rider ID</th><th>Expiration Date</th><th>Note</th>' + action_label + '</tr>' +
                '</table>');
            users.forEach(function(item) {
                var fmtdate = '';
                if (item.expir_date.length > 0) {
                    fmtdate = getPrettyDate(item.expir_date);
                }
                var action_link = '';
                if (item.editurl) {
                    action_link = '<td data-th="Actions">' + item.editurl + '</td>';
                }
                $('#user-lookup-section .users-div table').append(
                    '<tr userid="' + item.userid + '">' + 
                    '<td data-th="First Name">' + item.first_name + '</td>' +
                    '<td data-th="Last Name">' + item.last_name + '</td>' + 
                    '<td data-th="Email">' + item.email + '</td>' +
                    '<td data-th="Role">' + item.role + '</td>' + 
                    '<td data-th="Rider ID">' + item.riderid + '</td>' +
                    '<td data-th="Expiration">' + fmtdate + '</td>' + 
                    '<td data-th="Note">' + item.note + '</td>' + action_link +
                    '</tr>');    
            });
        }
        else {
            $('#user-lookup-section .users-div').append(
                '<span class="empty-tbl">No users found.</span>');
        }
    }
    
	function lookup_users_cb(response) {
        var res = JSON.parse(response);
        $("#user-lookup-section .search-frm input[name='memberid']").val(res.memberid);
        $("#user-lookup-section .search-frm input[name='firstname']").val(res.firstname);
        $("#user-lookup-section .search-frm input[name='lastname']").val(res.lastname);
		populate_users_table(res.users);
        $('body').removeClass('waiting');
    }

    function load_user_table() {
        var memberid = $("#user-lookup-section .search-frm input[name='memberid']").val().trim();
        var firstname = $("#user-lookup-section .search-frm input[name='firstname']").val().trim();
        var lastname = $("#user-lookup-section .search-frm input[name='lastname']").val().trim();
        var exact = false;
        if ($("#user-lookup-section .search-frm input[name='exact']").is(':checked')) {
            exact = true;
        }
        if (memberid.length > 0 || lastname.length > 0 || firstname.length > 0) {
            var action = $('#user-lookup-section .search-frm').attr('action');
            var data = {
                'action': 'pwtc_mileage_lookup_users',
                'memberid': memberid,
                'firstname': firstname,
                'lastname': lastname,
                'exact': exact
            };
            $('body').addClass('waiting');
            $.post(action, data, lookup_users_cb);
        }
        else {
            $('#user-lookup-section .users-div').empty();
        } 
    }

    $('#user-lookup-section .search-frm').on('submit', function(evt) {
        evt.preventDefault();
        load_user_table();
    });

    $('#user-lookup-section .search-frm .reset-btn').on('click', function(evt) {
        evt.preventDefault();
        $("#user-lookup-section .search-frm input[type='text']").val(''); 
        $('#user-lookup-section .users-div').empty();
    });

    $("#user-lookup-section .search-frm input[type='text']").val('');   
    $("#user-lookup-section .search-frm input[name='memberid']").focus();
});
</script>
<?php
if ($running_jobs > 0) {
?>
    <div class="notice notice-warning"><p><strong>
        A database batch operation is currently running!
    </strong></p></div>
<?php
} else {
?>
    <div id='user-lookup-section'>
        <p>Lookup user accounts by their first and last name or rider ID.</p>
        <div class='search-sec'>
        <p><strong>Enter search parameters to lookup users.</strong>
        	<form class="search-frm stacked-form" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post">
                <span>Rider ID</span>
                <input name="memberid" type="text"/>
                <span>First Name</span>
                <input name="firstname" type="text"/>
                <span>Last Name</span>
                <input name="lastname" type="text"/>
		        <span>Exact Match</span>
		        <span class="checkbox-wrap">
			        <input type="checkbox" name="exact" checked/>
		        </span>
				<input class="button button-primary" type="submit" value="Search"/>
				<input class="reset-btn button button-primary" type="button" value="Reset"/>
			</form>
        </p>
        </div>
        <p><div class="users-div"></div></p>
    </div>
<?php
    include('admin-rider-lookup.php');
}
}
?>
</div>
<?php
