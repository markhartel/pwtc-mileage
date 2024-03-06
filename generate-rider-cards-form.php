<style>
    #pwtc-mileage-rider-card-div .leaders-div div {
        margin: 10px; 
        padding: 10px; 
        border: 1px solid;
    }
    #pwtc-mileage-rider-card-div .leaders-div div i {
        cursor: pointer;
    }
    #pwtc-mileage-rider-card-div .leaders-div input {
        width: 150px;
        margin: 10px; 
        padding: 10px; 
        border: none;
    }
    #pwtc-mileage-rider-card-div .leader-search-div ul {
        list-style-type: none;
    }
    #pwtc-mileage-rider-card-div .leader-search-div li {
        cursor: pointer;
    }
    #pwtc-mileage-rider-card-div .leader-search-div li:hover {
        font-weight: bold;
    }
</style>
<script type="text/javascript">
    jQuery(document).ready(function($) { 

	var leaderTimeoutID = 0;
        var leaderSentCount = 0;
        var leaderRecvCount = 0;
	    
        function show_warning(msg) {
            $('#pwtc-mileage-rider-card-div .errmsg').html('<div class="callout small warning"><p>' + msg + '</p></div>');
        }

        function show_waiting() {
            $('#pwtc-mileage-rider-card-div .errmsg').html('<div class="callout small"><i class="fa fa-spinner fa-pulse"></i> please wait...</div>');
        }


        function has_user_id(id) {
            id = Number(id);
            var found = false;
            $('#pwtc-mileage-rider-card-div .leaders-div div').each(function() {
                var userid = Number($(this).attr('userid'));
                if (userid == id) {
                    found = true;
                }
            });
            return found;
        }

	    function remove_leader_event(evt) {
            is_dirty = true;
            $(this).parent().remove();
            $('#pwtc-mileage-rider-card-div .leader-search-div').hide();
            evt.stopPropagation();
        }

        function add_leader_event(evt) {
            var userid = $(this).attr('userid');
            if (!has_user_id(userid)) {
                var name = $(this).html();
                is_dirty = true;
                $('#pwtc-mileage-rider-card-div .leaders-div').removeClass('indicate-error');
                $('#pwtc-mileage-rider-card-div .leaders-div input').before('<div userid="' + userid + '"><i class="fa fa-times"></i> ' + name + '</div>');
                $('#pwtc-mileage-rider-card-div .leaders-div div[userid="' + userid + '"] .fa-times').on('click', remove_leader_event);
            }
            $('#pwtc-mileage-rider-card-div .leader-search-div').hide();
        }

        function leaders_lookup_cb(response) {
            var res;
            try {
                res = JSON.parse(response);
            }
            catch (e) {
                $('#pwtc-mileage-rider-card-div .leader-search-div').html('<div class="callout small alert"><p>' + e.message + '</p></div>');
                return;
            }
	        if (res.error) {
                $('#pwtc-mileage-rider-card-div .leader-search-div').html('<div class="callout small alert"><p>' + res.error + '</p></div>');
                return;
            }
	        if (res.count !== undefined) {
                if (res.count < leaderRecvCount) {
                    //console.log('response ' + res.count + ' discarded!');
                    return;
                }
                leaderRecvCount = res.count;
            }
            $('#pwtc-mileage-rider-card-div .leader-search-div').removeAttr('offset');
            if (res.users.length == 0 && res.offset == 0) {
                $('#pwtc-mileage-rider-card-div .leader-search-div').empty();
            }
            else {
		        if (res.offset == 0) {
                	$('#pwtc-mileage-rider-card-div .leader-search-div').empty();
                	$('#pwtc-mileage-rider-card-div .leader-search-div').append('<ul></ul>');
		        }
		        else {
                    $('#pwtc-mileage-rider-card-div .leader-search-div li .fa-spinner').parent().remove();
                }
                res.users.forEach(function(item) {
                    $('#pwtc-mileage-rider-card-div .leader-search-div ul').append(
                        '<li userid="' + item.userid + '">' + item.display_name + '</li>'); 
		            $('#pwtc-mileage-rider-card-div .leader-search-div li[userid="' + item.userid + '"]').on('click', add_leader_event);
                });
		        if (res.more !== undefined) {
                    $('#pwtc-mileage-rider-card-div .leader-search-div').attr('offset', res.offset+10);
                }
		        if (res.select !== undefined) {
                    $('#pwtc-mileage-rider-card-div input[name="leader-pattern"]').blur();
                    $('#pwtc-mileage-rider-card-div .leader-search-div ul li:first-child').trigger( 'click');
                }
            }
        }

        function fetch_ride_leaders(offset, select) {
	    leaderSentCount++;
            var searchstr = $('#pwtc-mileage-rider-card-div input[name="leader-pattern"]').val();
            var action = "<?php echo admin_url('admin-ajax.php'); ?>";
            var data = {
                'action': 'pwtc_mapdb_lookup_current_members',
		        'limit': 10,
                'search': searchstr,
                'offset': offset,
                'select': select,
                'count': leaderSentCount
            };
            $.post(action, data, leaders_lookup_cb);
            if (offset == 0) {
                $('#pwtc-mileage-rider-card-div .leader-search-div').html('<div class="callout small"><i class="fa fa-spinner fa-pulse"></i> searching...</div>');
            }
            else {
                $('#pwtc-mileage-rider-card-div .leader-search-div ul').append('<li><i class="fa fa-spinner fa-pulse"></i></li>');
            }
        }

	    $(document).on('focusin', function(evt) {
            //console.log('focusin detected on document');
            if (evt.fromLeaderEdit === undefined) {
                $('#pwtc-mileage-rider-card-div .leader-search-div').hide();
            }
        });

        $('#pwtc-mileage-rider-card-div .leaders-div').on('focusin', function(evt) {
            //console.log('focusin detected on leaders-div');
            evt.fromLeaderEdit = 1;
        });

        $('#pwtc-mileage-rider-card-div .leader-search-div').on('focusin', function(evt) {
            //console.log('focusin detected on leader-search-div');
            evt.fromLeaderEdit = 1;
        });

        $('#pwtc-mileage-rider-card-div input[name="leader-pattern"]').on('focus', function() {
            var interval = 500;
            function callback(lastval) {
                var val = $('#pwtc-mileage-rider-card-div input[name="leader-pattern"]').val();
                //console.log('callback: val=' + val + ', lastval=' + lastval);
                if (val != lastval) {
                    fetch_ride_leaders(0, 0);
                    $('#pwtc-mileage-rider-card-div .leader-search-div').show();
                }
                leaderTimeoutID = setTimeout(callback, interval, val);
            };
            leaderTimeoutID = setTimeout(callback, interval, $(this).val());
        });  

        $('#pwtc-mileage-rider-card-div input[name="leader-pattern"]').on('blur', function(evt) {
            clearTimeout(leaderTimeoutID);
            $('#pwtc-mileage-rider-card-div input[name="leader-pattern"]').val('');
        });

        $('#pwtc-mileage-rider-card-div input[name="leader-pattern"]').on('click', function(evt) {
            if ($('#pwtc-mileage-rider-card-div .leader-search-div').is(':hidden')) {
                fetch_ride_leaders(0, 0);
                $('#pwtc-mileage-rider-card-div .leader-search-div').show();
            }
            evt.stopPropagation();		
        });

        $('#pwtc-mileage-rider-card-div input[name="leader-pattern"]').on('keypress', function(evt) {
            var keyPressed = evt.keyCode || evt.which; 
            if (keyPressed === 13) { 
                fetch_ride_leaders(0, 1);
                $('#pwtc-mileage-rider-card-div .leader-search-div').show();
            } 
        });	

        $('#pwtc-mileage-rider-card-div .leaders-div').on('click', function(evt) { 
            if ($('#pwtc-mileage-rider-card-div .leader-search-div').is(':hidden')) {
                fetch_ride_leaders(0, 0);
                $('#pwtc-mileage-rider-card-div .leader-search-div').show();
            }
            $('#pwtc-mileage-rider-card-div input[name="leader-pattern"]').focus();
        });      

        $('#pwtc-mileage-rider-card-div .leaders-div .fa-times').on('click', remove_leader_event);
	    
	    $('#pwtc-mileage-rider-card-div .leader-search-div').on('scroll', function() {            
            if ($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
                var offset = $(this).attr('offset');
                if (offset) {
                    fetch_ride_leaders(parseInt(offset, 10), 0);
                    $(this).removeAttr('offset');
                }
            }
        });

	$('#pwtc-mileage-rider-card-div .download_card').on('click', function(evt) {
            //$('#pwtc-mileage-rider-card-div .download-frm').submit();
        });

        window.addEventListener('beforeunload', function(e) {
            if (is_dirty) {
                e.preventDefault();
                e.returnValue = 'If you leave this page, any data you have entered will not be saved.';
            }
            else {
                delete e['returnValue'];
            }
        });

        var is_dirty = false;
    });
</script>
<div id='pwtc-mileage-rider-card-div'>
    <div class="callout">
        <div class="row column">
        	<label>Current Members</label>
        </div>
        <div class="row column">
                <div class= "leaders-div" style="min-height:40px; border:1px solid; display:flex; flex-wrap:wrap;">
                    <input type="text" name="leader-pattern" placeholder="Enter member name">
                </div>
        </div>
        <div class="row column">
                <div class="leader-search-div" style="border:1px solid; border-top-width: 0 !important; overflow: auto; height: 100px; display:none;">
                </div>
        </div>
        <div class="row column" style="margin-top:15px;">
                <p class="help-text">Membership cards can only be downloaded for active members.</p>
        </div>
    	<div class="errmsg"></div>
    	<div class="row column clearfix">
		<div class="button-group float-left">
			<a class="download_card dark button"><i class="fa fa-download"></i> Membership Cards</a>
		</div>
	</div>
	<form class="download-frm" method="POST">
        	<input type="hidden" name="pwtc_mapdb_download_signup" value="yes"/>
        	<input type="hidden" name="user_id" value=""/>
    	</form> 
    </div>
</div>
<?php 
