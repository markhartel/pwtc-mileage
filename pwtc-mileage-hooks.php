<?php

/*
Returns an array of arrays that contains the membership list. 
The interor array contains a membership record structured thus:
array[0] - member ID (string)
array[1] - first name (string)
array[2] - last name (string)
array[3] - expiration date (string with PHP date format 'Y-m-d')
*/
function pwtc_mileage_fetch_membership() {
    $users = get_users();
    $users_array = array();
    foreach ( $users as $item ) {
        $firstname = $item->user_firstname;
        $lastname = $item->user_lastname;
        $memberid = get_field('rider_number', 'user_' . $item->ID);
        $expirdate = get_field('expir_date', 'user_' . $item->ID);
        array_push($users_array, array($memberid, $firstname, $lastname, $expirdate));
    }
    return $users_array;
    //return array();
}

/*
Returns a string that contains the member ID of the logged on user.
(Return a null if the user is not logged on or his member ID is not set.)
*/
function pwtc_mileage_get_member_id() {
    $id = null;
    $current_user = wp_get_current_user();
    if ( 0 == $current_user->ID ) {
        $id = null;
    } else {
        $id = get_field('rider_number', 'user_' . $current_user->ID);
    }
    return $id;
    //return null;
}

/*
Returns an array of arrays that contains the posted rides without ridesheets. 
The interor array contains a posted ride record structured thus:
array[0] - post ID (string)
array[1] - title (string)
array[2] - start date (string with PHP date format 'Y-m-d')
array[3] - post guid (url string)
*/
function pwtc_mileage_fetch_posts($select_sql, $lookback_date) {
    global $wpdb;
    $ride_post_type = 'rideevent';
    $ride_date_metakey = 'start_date';
    $sql_part1 = 'select p.ID, p.post_title, m.meta_value as start_date, p.guid' . 
        ' from ' . $wpdb->posts . ' as p inner join ' . $wpdb->postmeta . 
        ' as m on p.ID = m.post_id where p.post_type = %s and p.post_status = \'publish\'' . 
        ' and m.meta_key = %s and (cast(m.meta_value as date) ';
    $sql_part2 = ') and p.ID not in (' . $select_sql . ')' . ' order by m.meta_value';
    $sql_stmt = null;
    if ($lookback_date != null) {
        $sql_stmt = $wpdb->prepare(
            $sql_part1 . 'between %s and curdate()' . $sql_part2, 
            $ride_post_type, $ride_date_metakey, $lookback_date);
    }
    else {
        $sql_stmt = $wpdb->prepare(
            $sql_part1 . '< curdate()' . $sql_part2, 
            $ride_post_type, $ride_date_metakey);
    }
    $results = $wpdb->get_results($sql_stmt, ARRAY_N);
    return $results;
    //return array();
}

/*
Returns the guid of the post. 
(Return a null if the post cannot be found.)
*/
function pwtc_mileage_fetch_post_guid($post_id) {
    $post = get_post($post_id);
    $guid = null;
    if ($post !== null) {
        $guid = $post->guid;
    }
    return $guid;
    //return null;
}

/*
Returns an array of arrays that contains the ride leaders of the posted ride. 
The interor array contains a posted ride record structured thus:
array[0] - rider ID (string)
array[1] - full name (string)
*/
function pwtc_mileage_fetch_ride_leaders($post_id) {
    $leaders = get_field('ride_leader', $post_id);
    $leaders_array = array();
    if ($leaders) {
        foreach ($leaders as $leader) {
            $name = $leader->post_title;
            $riderid = get_field('rider_number', $leader->ID);
            array_push($leaders_array, array($riderid, $name));
        }
    }
    return $leaders_array;
    //return array();   
}
