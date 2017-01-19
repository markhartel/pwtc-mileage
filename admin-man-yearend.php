<?php
if (!current_user_can('manage_options')) {
    return;
}
$message = '';
$notice_type = '';
$show_buttons = true;
$clear_button = false;
?>
<script type="text/javascript">
jQuery(document).ready(function($) { 

    var confirm_consolidate = true;
    var confirm_sync = true;
    var confirm_restore = true;

    $('.sync-frm').on('submit', function(evt) {
        if (confirm_sync) {
            evt.preventDefault();
            open_confirm_dialog(
                'Are you sure you want to synchronize with the membership list?', 
                function() {
                    confirm_sync = false;
                    $('.sync-frm input[name="member_sync"]').click();
                }
            );
        }
    });

    $('.consol-frm').on('submit', function(evt) {
        if (confirm_consolidate) {
            evt.preventDefault();
            open_confirm_dialog(
                'Are you sure you want to perform consolidation?', 
                function() {
                    confirm_consolidate = false;
                    $('.consol-frm input[name="consolidate"]').click();
               }
            );
        }
    });

    $('.restore-frm').on('submit', function(evt) {
        if (confirm_restore) {
            evt.preventDefault();
            open_confirm_dialog(
                'Are you sure you want to perform restore?', 
                function() {
                    confirm_restore = false;
                    $('.restore-frm input[name="restore"]').click();
               }
            );
        }
    });

    $(".restore-btn").on('click', function(evt) {
		$(".restore-frm input[type='file']").val('');
        $(".restore-frm input[type='file']").next('label').html('Select file...'); 
        $(".restore-btn").hide('fast', function() {
            $('.restore-blk').show('slow'); 
            $(".restore-frm input[name='members_file']").focus();
        })
    });

	$(".restore-blk .cancel-btn").on('click', function(evt) {
		$('.restore-blk').hide('slow', function() {
            $(".restore-btn").show('fast');
        });
    });

    $('.inputfile').each(function() {
		var $input	 = $(this),
			$label	 = $input.next('label'),
			labelVal = $label.html();

		$input.on('change', function(e) {
			var fileName = '';
            if (e.target.value) {
				fileName = e.target.value.split( '\\' ).pop();
            }
			if (fileName) {
				$label.html(fileName);
            }
			else {
				$label.html(labelVal);
            }
		});

		// Firefox bug fix
		$input
		.on('focus', function(){ $input.addClass('has-focus'); })
		.on('blur', function(){ $input.removeClass('has-focus'); });
	});

    $('.sync-frm input[name="member_sync"]').focus();

 });
</script>
<div class="wrap">
	<h1><?= esc_html(get_admin_page_title()); ?></h1>
<?php
if (null !== $job_status_s) {
    if ($job_status_s['status'] == 'triggered') {
        $message = 'Synchronize action has been triggered.';
        $notice_type = 'notice-warning';
        $show_buttons = false;
    } 
    else if ($job_status_s['status'] == 'started') {
        $message = 'Synchronize action is currently running.';
        $notice_type = 'notice-warning';
        $show_buttons = false;
    }
    else {
        $message = 'Synchronize action failed: ' . $job_status_s['error_msg'];
        $notice_type = 'notice-error';
        $clear_button = true;
    }
?>
    <div class="notice <?php echo $notice_type; ?>"><p><strong><?php echo $message; ?></strong></p></div>
<?php
} 
if (null !== $job_status_c) {
    if ($job_status_c['status'] == 'triggered') {
        $message = 'Consolidation action has been triggered.';
        $notice_type = 'notice-warning';
        $show_buttons = false;
    } 
    else if ($job_status_c['status'] == 'started') {
        $message = 'Consolidation action is currently running.';
        $notice_type = 'notice-warning';
        $show_buttons = false;
    }
    else {
        $message = 'Consolidation action failed: ' . $job_status_c['error_msg'];
        $notice_type = 'notice-error';
        $clear_button = true;
    }
?>
    <div class="notice <?php echo $notice_type; ?>"><p><strong><?php echo $message; ?></strong></p></div>
<?php
}
if (null !== $job_status_r) {
    if ($job_status_r['status'] == 'triggered') {
        $message = 'Restore action has been triggered.';
        $notice_type = 'notice-warning';
        $show_buttons = false;
    } 
    else if ($job_status_r['status'] == 'started') {
        $message = 'Restore action is currently running.';
        $notice_type = 'notice-warning';
        $show_buttons = false;
    }
    else {
        $message = 'Restore action failed: ' . $job_status_r['error_msg'];
        $notice_type = 'notice-error';
        $clear_button = true;
    }
?>
    <div class="notice <?php echo $notice_type; ?>"><p><strong><?php echo $message; ?></strong></p></div>
<?php
}
if ($show_buttons) {
    if ($clear_button) {
?>
        <div><form class="clear-frm" method="POST">
        	<?php wp_nonce_field('pwtc_mileage_clear_errs'); ?>
            <input type="submit" name="clear_errs" value="Clear Errors" class="button">
        </form></div>
<?php        
    }
?>
    <p>
        <div><strong>Synchronize rider list with current membership database.</strong></div>
        <div><form class="sync-frm" method="POST">
            <?php wp_nonce_field('pwtc_mileage_member_sync'); ?>
            <input type="submit" name="member_sync" value="Synchronize" 
                class="button button-primary button-large"/>
        </form></div><br>
        <div><strong>Consolidate <?php echo(intval(date('Y'))-2); ?> club rides to single entry.</strong></div>
        <div><form class="consol-frm" method="POST">
            <?php wp_nonce_field('pwtc_mileage_consolidate'); ?>
            <input type="submit" name="consolidate" value="Consolidate" 
                class="button button-primary button-large" 
                <?php if ($rides_to_consolidate <= 1) { echo 'disabled'; } ?>/>
        </form></div><br>
        <div><strong>Export database tables to CSV files.</strong></div>
        <div><form class="export-frm" method="POST">
            <?php wp_nonce_field('pwtc_mileage_export'); ?>
            <input type="submit" name="export_members" 
                value="Members (<?php echo $member_count; ?>)" 
                class="button button-primary button-large"/>
            <input type="submit" name="export_rides" 
                value="Rides (<?php echo $ride_count; ?>)" 
                class="button button-primary button-large"/>
            <input type="submit" name="export_mileage" 
                value="Mileage (<?php echo $mileage_count; ?>)" 
                class="button button-primary button-large"/>
            <input type="submit" name="export_leaders" 
                value="Leaders (<?php echo $leader_count; ?>)" 
                class="button button-primary button-large"/>
        </form></div><br>
        <div><strong>Restore database tables from exported CSV files.</strong></div>
        <div>
            <button class="restore-btn button button-primary button-large">Restore</button>
            <span class="restore-blk popup-frm initially-hidden">
			<form class="restore-frm stacked-form" method="post" enctype="multipart/form-data">
                <?php wp_nonce_field('pwtc_mileage_restore'); ?>
                <span>Members</span>
                <input id="select-members-file" class="inputfile" type="file" name="members_file" multiple="false" accept=".csv"/>
                <label for="select-members-file" class="button">Select file...</label>
                <span>Rides</span>
                <input id="select-rides-file" class="inputfile" type="file" name="rides_file" multiple="false" accept=".csv"/>
                <label for="select-rides-file" class="button">Select file...</label>
                <span>Mileage</span>
                <input id="select-mileage-file" class="inputfile" type="file" name="mileage_file" multiple="false" accept=".csv"/>
                <label for="select-mileage-file" class="button">Select file...</label>
                <span>Leaders</span>
                <input id="select-leaders-file" class="inputfile" type="file" name="leaders_file" multiple="false" accept=".csv"/>
                <label for="select-leaders-file" class="button">Select file...</label>
				<input class="button button-primary" type="submit" name="restore" value="Restore"/>
				<input class="cancel-btn button button-primary" type="button" value="Cancel"/>
			</form>
		    </span>
        </div><br>
    </p>
<?php
    include('admin-rider-lookup.php');
}
else if ($show_clear_lock) {
?>
    <div>
        <form class="clear-lock-frm" method="POST">
            <?php wp_nonce_field('pwtc_mileage_clear_lock'); ?>
            <input type="submit" name="clear_lock" value="Clear Lock" class="button">
        </form>
    </div>
<?php
}
else {
?>
    <div>
        <form class="refresh-frm" method="POST">
            <input type="submit" name="refresh_page" value="Refresh" class="button">
        </form>
    </div>
<?php
}
?>
</div>
<?php
