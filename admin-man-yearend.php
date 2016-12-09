<?php
if (!current_user_can('manage_options')) {
    return;
}
$message = '';
$notice_type = '';
$show_buttons = true;
?>
<script type="text/javascript">
jQuery(document).ready(function($) { 

    var confirm_backup = true;
    var confirm_consolidate = true;

    $("#b-confirm-dlg").dialog({
        autoOpen: false,
        resizable: false,
        height: "auto",
        width: 400,
        modal: true,
        buttons: {
            "OK": function(evt) {
                $(this).dialog("close");
                confirm_backup = false;
                $('.backup-frm input[name="backup"]').click();
            },
            Cancel: function() {
                $(this).dialog("close");
            }
        }
    });

    $("#c-confirm-dlg").dialog({
        autoOpen: false,
        resizable: false,
        height: "auto",
        width: 400,
        modal: true,
        buttons: {
            "OK": function(evt) {
                $(this).dialog("close");
                confirm_consolidate = false;
                $('.consol-frm input[name="consolidate"]').click();
            },
            Cancel: function() {
                $(this).dialog("close");
            }
        }
    });

    $('.backup-frm').on('submit', function(evt) {
        if (confirm_backup) {
            evt.preventDefault();
            $("#b-confirm-dlg").dialog('open');
        }
    });

    $('.consol-frm').on('submit', function(evt) {
        if (confirm_consolidate) {
            evt.preventDefault();
            $("#c-confirm-dlg").dialog('open');
        }
    });

 });
</script>
<div class="wrap">
	<h1><?= esc_html(get_admin_page_title()); ?></h1>
<?php
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
        $message = 'Consolidation action is in an unknown state: ' . $job_status_c['status'] . '.';
        $notice_type = 'notice-error';
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
        $message = 'Backup action is in an unknown state: ' . $job_status_b['status'] . '.';
        $notice_type = 'notice-error';
    }
?>
    <div class="notice <?php echo $notice_type; ?>"><p><strong><?php echo $message; ?></strong></p></div>
<?php
}
if ($show_buttons) {
?>
    <p>
        <div><strong>Backup mileage database to hard drive.</strong></div>
        <div><form class="backup-frm" method="POST">
            <input type="submit" name="backup" value="Backup" class="button button-primary button-large">
        </form></div><br>
        <div><strong>Consolidate <?php echo(intval(date('Y'))-2); ?> club rides to single entry.</strong></div>
        <div><form class="consol-frm" method="POST">
            <input type="submit" name="consolidate" value="Consolidate" class="button button-primary button-large">
        </form></div>
    </p>
    <div id="b-confirm-dlg" title="Run Backup?">
        <p>Blah, blah, blah</p>   
    </div>
    <div id="c-confirm-dlg" title="Run Consolidate?">
        <p>Blah, blah, blah</p>   
    </div>
<?php
}
?>
</div>
<?php
