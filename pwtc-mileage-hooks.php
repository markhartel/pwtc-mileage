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
