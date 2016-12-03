
function lookup_pwtc_riders(mycb) {
    jQuery('#rider-lookup-results .error-msg').html('');
    jQuery('#rider-lookup-results .lookup-tlb tr').remove();
    jQuery("#rider-lookup-results .lookup-frm input[type='text']").val('');
    jQuery("#rider-lookup-results").dialog('open');
    window.pwtc_rider_cb = mycb;
} 

function click_to_close_msg(selector) {
    jQuery(selector + ' div').on('click', function(evt) {
        jQuery(this).parent().html('');
    });
}

function show_error_msg(selector, msg) {
    jQuery(selector).html('<div class="notice notice-error is-dismissible">' +
        '<p><strong>' + msg + '</strong></p></div>');
    click_to_close_msg(selector);
}

function show_warning_msg(selector, msg) {
    jQuery(selector).html('<div class="notice notice-warning is-dismissible">' +
        '<p><strong>' + msg + '</strong></p></div>');
    click_to_close_msg(selector);
}

function show_success_msg(selector, msg) {
    jQuery(selector).html('<div class="notice notice-success is-dismissible">' +
        '<p><strong>' + msg + '</strong></p></div>');
    click_to_close_msg(selector);
}

function show_info_msg(selector, msg) {
    jQuery(selector).html('<div class="notice notice-info is-dismissible">' +
        '<p><strong>' + msg + '</strong></p></div>');
    click_to_close_msg(selector);
}

function clear_msg(selector) {
    jQuery(selector).html('');
}

