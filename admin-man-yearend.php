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

    var confirm_backup = true;
    var confirm_consolidate = true;
    var confirm_sync = true;

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

    $('.backup-frm').on('submit', function(evt) {
        if (confirm_backup) {
            evt.preventDefault();
            open_confirm_dialog(
                'Are you sure you want to perform backup?', 
                function() {
                    confirm_backup = false;
                    $('.backup-frm input[name="backup"]').click();
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
if (null !== $job_status_b) {
    if ($job_status_b['status'] == 'triggered') {
        $message = 'Backup action has been triggered.';
        $notice_type = 'notice-warning';
        $show_buttons = false;
    } 
    else if ($job_status_b['status'] == 'started') {
        $message = 'Backup action is currently running.';
        $notice_type = 'notice-warning';
        $show_buttons = false;
    }
    else {
        $message = 'Backup action failed: ' . $job_status_b['error_msg'];
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
            <input type="submit" name="clear_errs" value="Clear Errors" class="button">
        </form></div>
<?php        
    }
?>
    <p>
        <div><strong>Synchronize rider list with current membership database.</strong></div>
        <div><form class="sync-frm" method="POST">
            <input type="submit" name="member_sync" value="Synchronize" class="button button-primary button-large">
        </form></div><br>
        <div><strong>Backup mileage database to hard drive.</strong></div>
        <div><form class="backup-frm" method="POST">
            <input type="submit" name="backup" value="Backup" class="button button-primary button-large">
        </form></div><br>
        <div><strong>Consolidate <?php echo(intval(date('Y'))-2); ?> club rides to single entry.</strong></div>
        <div><form class="consol-frm" method="POST">
            <input type="submit" name="consolidate" value="Consolidate" class="button button-primary button-large">
        </form></div>
    </p>
<?php
    include('admin-rider-lookup.php');
}
?>
</div>
<?php
