<?php

class PwtcMileage_DB {

	const MEMBER_TABLE = 'pwtc_membership';					// club membership list table
	const RIDE_TABLE = 'pwtc_club_rides';					// club ride list table
	const MILEAGE_TABLE = 'pwtc_ride_mileage';				// club ride mileage table
	const LEADER_TABLE = 'pwtc_ride_leaders';				// club ride leader table
	const JOBS_TABLE = 'pwtc_running_jobs';					// currently running jobs table

	const LT_MILES_VIEW = 'pwtc_lt_miles_vw';				// lifetime mileage view
	const YTD_MILES_VIEW = 'pwtc_ytd_miles_vw';				// year-to-date mileage view
	const LY_MILES_VIEW = 'pwtc_ly_miles_vw';				// last year's mileage view
	const LY_LT_MILES_VIEW = 'pwtc_ly_lt_miles_vw';			// last year's lifetime mileage view
	const YBL_LT_MILES_VIEW = 'pwtc_ybl_lt_miles_vw';		// year before last's lifetime mileage view
	const LY_LT_ACHVMNT_VIEW = 'pwtc_ly_lt_achvmnt_vw';		// last year's lifetime achiviement view
	const YTD_RIDES_LED_VIEW = 'pwtc_ytd_rides_led_vw';		// year-to-date rides led list view
	const LY_RIDES_LED_VIEW = 'pwtc_ly_rides_led_vw';		// last year's rides led list view
	const YTD_LED_VIEW = 'pwtc_ytd_led_vw';					// year-to-date number of rides led view 
	const LY_LED_VIEW = 'pwtc_ly_led_vw';					// last year's number of rides led view
	const YTD_RIDES_VIEW = 'pwtc_ytd_rides_vw';				// year-to-date rides ridden list view
	const LY_RIDES_VIEW = 'pwtc_ly_rides_vw';				// last year's rides ridden list view

	public static function get_new_ride_id() {
    	global $wpdb;
		return $wpdb->insert_id;
	}

	public static function get_num_rides_before_date($maxdate) {
    	global $wpdb;
		$ride_table = $wpdb->prefix . self::RIDE_TABLE;
		$num_rides = $wpdb->get_var($wpdb->prepare('select count(*) from ' . $ride_table . 
			' where date <= %s', $maxdate));
		return $num_rides;
	}

	public static function fetch_ly_lt_achvmnt($outtype, $sort) {
    	global $wpdb;
    	$results = $wpdb->get_results(
			'select member_id, concat(first_name, \' \', last_name), mileage, achievement from ' . 
			self::LY_LT_ACHVMNT_VIEW . ' order by ' . $sort, $outtype);
		return $results;
	}

	public static function meta_ly_lt_achvmnt() {
		$thisyear = date('Y', current_time('timestamp'));
    	$lastyear = intval($thisyear) - 1;
		$meta = array(
			'header' => array('Member ID', 'Name', 'Mileage', 'Achievement'),
			'title' => '' . $lastyear . ' Lifetime Mileage Achievement',
			'date_idx' => -1,
			'id_idx' => 0
		);
		return $meta;
	}

	public static function fetch_ytd_miles($outtype, $sort, $min = 0) {
    	global $wpdb;
		$where = '';
		if ($min > 0) {
			$where = ' where mileage >= ' . $min . ' ';
		}
    	$results = $wpdb->get_results(
			'select member_id, concat(first_name, \' \', last_name), mileage from ' . 
			self::YTD_MILES_VIEW . $where . ' order by ' . $sort , $outtype);
		return $results;
	}

	public static function meta_ytd_miles() {
		$meta = array(
			'header' => array('Member ID', 'Name', 'Mileage'),
			'title' => 'Year-to-date Rider Mileage',
			'date_idx' => -1,
			'id_idx' => 0
		);
		return $meta;
	}

	public static function fetch_ly_miles($outtype, $sort, $min = 0) {
    	global $wpdb;
		$where = '';
		if ($min > 0) {
			$where = ' where mileage >= ' . $min . ' ';
		}
    	$results = $wpdb->get_results(
			'select member_id, concat(first_name, \' \', last_name), mileage from ' . 
			self::LY_MILES_VIEW . $where . ' order by ' . $sort , $outtype);
		return $results;
	}

	public static function meta_ly_miles() {
		$meta = array(
			'header' => array('Member ID', 'Name', 'Mileage'),
			'title' => 'Last Year\'s Rider Mileage',
			'date_idx' => -1,
			'id_idx' => 0
		);
		return $meta;
	}

	public static function fetch_lt_miles($outtype, $sort, $min = 0) {
    	global $wpdb;
		$where = '';
		if ($min > 0) {
			$where = ' where mileage >= ' . $min . ' ';
		}
    	$results = $wpdb->get_results(
			'select member_id, concat(first_name, \' \', last_name), mileage from ' . 
			self::LT_MILES_VIEW . $where . ' order by ' . $sort , $outtype);
		return $results;
	}

	public static function meta_lt_miles() {
		$meta = array(
			'header' => array('Member ID', 'Name', 'Mileage'),
			'title' => 'Lifetime Rider Mileage',
			'date_idx' => -1,
			'id_idx' => 0
		);
		return $meta;
	}

	public static function fetch_ytd_led($outtype, $sort, $min = 0) {
    	global $wpdb;
		$where = '';
		if ($min > 0) {
			$where = ' where rides_led >= ' . $min . ' ';
		}
    	$results = $wpdb->get_results(
			'select member_id, concat(first_name, \' \', last_name), rides_led from ' . 
			self::YTD_LED_VIEW . $where . ' order by ' . $sort , $outtype);
		return $results;
	}

	public static function meta_ytd_led() {
		$meta = array(
			'header' => array('Member ID', 'Name', 'Rides Led'),
			'title' => 'Year-to-date Number of Rides Led',
			'date_idx' => -1,
			'id_idx' => 0
		);
		return $meta;
	}

	public static function fetch_ly_led($outtype, $sort, $min = 0) {
    	global $wpdb;
		$where = '';
		if ($min > 0) {
			$where = ' where rides_led >= ' . $min . ' ';
		}
    	$results = $wpdb->get_results(
			'select member_id, concat(first_name, \' \', last_name), rides_led from ' . 
			self::LY_LED_VIEW . $where . ' order by ' . $sort , $outtype);
		return $results;
	}

	public static function meta_ly_led() {
		$meta = array(
			'header' => array('Member ID', 'Name', 'Rides Led'),
			'title' => 'Last year\'s Number of Rides Led',
			'date_idx' => -1,
			'id_idx' => 0
		);
		return $meta;
	}

	public static function fetch_ytd_rides($outtype, $memberid) {
    	global $wpdb;
    	$results = $wpdb->get_results($wpdb->prepare(
			'select title, date, mileage from ' . 
			self::YTD_RIDES_VIEW . ' where member_id = %s', $memberid), $outtype);
		return $results;
	}

	public static function meta_ytd_rides($name = '') {
		$meta = array(
			'header' => array('Title', 'Date', 'Mileage'),
			'title' => 'Year-to-date Rides by ' . $name,
			'date_idx' => 1,
			'id_idx' => -1
		);
		return $meta;
	}

	public static function fetch_ly_rides($outtype, $memberid) {
    	global $wpdb;
    	$results = $wpdb->get_results($wpdb->prepare(
			'select title, date, mileage from ' . 
			self::LY_RIDES_VIEW . ' where member_id = %s', $memberid), $outtype);
		return $results;
	}

	public static function meta_ly_rides($name = '') {
		$meta = array(
			'header' => array('Title', 'Date', 'Mileage'),
			'title' => 'Last Year\'s Rides by ' . $name,
			'date_idx' => 1,
			'id_idx' => -1
		);
		return $meta;
	}

	public static function fetch_ytd_rides_led($outtype, $memberid) {
    	global $wpdb;
    	$results = $wpdb->get_results($wpdb->prepare(
			'select title, date from ' . 
			self::YTD_RIDES_LED_VIEW . ' where member_id = %s', $memberid), $outtype);
		return $results;
	}

	public static function meta_ytd_rides_led($name = '') {
		$meta = array(
			'header' => array('Title', 'Date'),
			'title' => 'Year-to-date Rides Led by ' . $name,
			'date_idx' => 1,
			'id_idx' => -1
		);
		return $meta;
	}

	public static function fetch_ly_rides_led($outtype, $memberid) {
    	global $wpdb;
    	$results = $wpdb->get_results($wpdb->prepare(
			'select title, date from ' . 
			self::LY_RIDES_LED_VIEW . ' where member_id = %s', $memberid), $outtype);
		return $results;
	}

	public static function meta_ly_rides_led($name = '') {
		$meta = array(
			'header' => array('Title', 'Date'),
			'title' => 'Last Year\'s Rides Led by ' . $name,
			'date_idx' => 1,
			'id_idx' => -1
		);
		return $meta;
	}

	public static function fetch_club_rides($title, $fromdate, $todate) {
    	global $wpdb;
		$ride_table = $wpdb->prefix . self::RIDE_TABLE;
    	$results = $wpdb->get_results($wpdb->prepare('select * from ' . $ride_table . 
			' where title like %s and date between cast(%s as date) and cast(%s as date) order by date', 
			$title . '%', $fromdate, $todate), ARRAY_A);
		return $results;
	}

/*
	public static function fetch_posts_without_rides($outtype) {
    	global $wpdb;
		$plugin_options = PwtcMileage::get_plugin_options();
		$ride_table = $wpdb->prefix . self::RIDE_TABLE;
    	$results = $wpdb->get_results($wpdb->prepare(
			'select p.ID, p.post_title, m.meta_value as start_date' . 
			' from ' . $wpdb->posts . ' as p inner join ' . $wpdb->postmeta . 
			' as m on p.ID = m.post_id where p.post_type = %s and p.post_status = \'publish\'' . 
			' and m.meta_key = %s and (cast(m.meta_value as date) < curdate())' . 
			' and p.ID not in (select post_id from ' . $ride_table . ' where post_id <> 0)' . 
			' order by m.meta_value', 
			$plugin_options['ride_post_type'], $plugin_options['ride_date_metakey']), $outtype);
		return $results;
	}
*/

	public static function fetch_posts_without_rides() {
		global $wpdb;
		// TODO: use lookback date from plugin options
		$plugin_options = PwtcMileage::get_plugin_options();
		$lookback_date = null;
		$ride_table = $wpdb->prefix . self::RIDE_TABLE;
		$results = pwtc_mileage_fetch_posts(
			'select post_id from ' . $ride_table . ' where post_id <> 0',
			$lookback_date);
		return $results;
	}

	public static function meta_posts_without_rides() {
		$meta = array(
			'header' => array('Post ID', 'Title', 'Start Date'),
			'title' => 'Posted Rides without Ride Sheets',
			'date_idx' => 2,
			'id_idx' => 0
		);
		return $meta;
	}

	public static function fetch_ride_mileage($rideid) {
    	global $wpdb;
		$mileage_table = $wpdb->prefix . self::MILEAGE_TABLE;
		$member_table = $wpdb->prefix . self::MEMBER_TABLE;
    	$results = $wpdb->get_results($wpdb->prepare('select' . 
			' c.member_id, c.first_name, c.last_name, m.mileage' . 
			' from ' . $member_table . ' as c inner join ' . $mileage_table . ' as m' . 
			' on c.member_id = m.member_id where m.ride_id = %d order by c.last_name, c.first_name', 
			$rideid), ARRAY_A);
		return $results;
	}

	public static function fetch_ride_leaders($rideid) {
    	global $wpdb;
		$leader_table = $wpdb->prefix . self::LEADER_TABLE;
		$member_table = $wpdb->prefix . self::MEMBER_TABLE;
    	$results = $wpdb->get_results($wpdb->prepare('select' . 
			' c.member_id, c.first_name, c.last_name' . 
			' from ' . $member_table . ' as c inner join ' . $leader_table . ' as l' . 
			' on c.member_id = l.member_id where l.ride_id = %d order by c.last_name, c.first_name', 
			$rideid), ARRAY_A);
		return $results;
	}

	public static function fetch_ride_has_mileage($rideid) {
    	global $wpdb;
		$mileage_table = $wpdb->prefix . self::MILEAGE_TABLE;
		$results = $wpdb->get_var($wpdb->prepare('select count(*) from ' . $mileage_table . 
			' where ride_id = %d', $rideid));
		return $results;
	}

	public static function fetch_ride_has_leaders($rideid) {
    	global $wpdb;
		$leader_table = $wpdb->prefix . self::LEADER_TABLE;
		$results = $wpdb->get_var($wpdb->prepare('select count(*) from ' . $leader_table . 
			' where ride_id = %d', $rideid));
		return $results;
	}

	public static function fetch_member_has_mileage($memberid) {
    	global $wpdb;
		$mileage_table = $wpdb->prefix . self::MILEAGE_TABLE;
		$results = $wpdb->get_var($wpdb->prepare('select count(*) from ' . $mileage_table . 
			' where member_id = %s', $memberid));
		return $results;
	}

	public static function fetch_member_has_leaders($memberid) {
    	global $wpdb;
		$leader_table = $wpdb->prefix . self::LEADER_TABLE;
		$results = $wpdb->get_var($wpdb->prepare('select count(*) from ' . $leader_table . 
			' where member_id = %s', $memberid));
		return $results;
	}

	public static function delete_ride($rideid) {
    	global $wpdb;
		$ride_table = $wpdb->prefix . self::RIDE_TABLE;
		$status = $wpdb->query($wpdb->prepare('delete from ' . $ride_table . 
			' where ID = %d', $rideid));
		return $status;
	}

	public static function delete_ride_leader($rideid, $memberid) {
    	global $wpdb;
		$leader_table = $wpdb->prefix . self::LEADER_TABLE;
		$status = $wpdb->query($wpdb->prepare('delete from ' . $leader_table . 
			' where member_id = %s and ride_id = %d', $memberid, $rideid));
		return $status;
	}

	public static function delete_ride_mileage($rideid, $memberid) {
    	global $wpdb;
		$mileage_table = $wpdb->prefix . self::MILEAGE_TABLE;
		$status = $wpdb->query($wpdb->prepare('delete from ' . $mileage_table . 
			' where member_id = %s and ride_id = %d', $memberid, $rideid));
		return $status;
	}

	public static function insert_ride($title, $startdate) {
    	global $wpdb;
		$ride_table = $wpdb->prefix . self::RIDE_TABLE;
		$status = $wpdb->query($wpdb->prepare('insert into ' . $ride_table .
			' (title, date) values (%s, %s)', $title, $startdate));
		return $status;
	}

	public static function insert_ride_with_postid($title, $startdate, $postid) {
    	global $wpdb;
		$ride_table = $wpdb->prefix . self::RIDE_TABLE;
		/*
		$status = $wpdb->query($wpdb->prepare('insert into ' . $ride_table .
			' (title, date, post_id) values (%s, %s, %d)', $title, $startdate, $postid));
		*/
		$status = $wpdb->insert($ride_table,
			array( 
				'title' => $title, 
				'date' => $startdate,
				'post_id' => $postid
			), 
			array( 
				'%s', 
				'%s',
				'%d' 
			)
		);			
		return $status;
	}

	public static function insert_ride_leader($rideid, $memberid) {
    	global $wpdb;
		$leader_table = $wpdb->prefix . self::LEADER_TABLE;
		$status = $wpdb->query($wpdb->prepare('insert into ' . $leader_table . 
			' (member_id, ride_id, rides_led) values (%s, %d, 1)' . 
			' on duplicate key update rides_led = 1', $memberid, $rideid));
		return $status;
	}

	public static function insert_ride_mileage($rideid, $memberid, $mileage) {
    	global $wpdb;
		$mileage_table = $wpdb->prefix . self::MILEAGE_TABLE;
		$status = $wpdb->query($wpdb->prepare('insert into ' . $mileage_table . 
			' (member_id, ride_id, mileage) values (%s, %d, %d)' . 
			' on duplicate key update mileage = %d', 
			$memberid, $rideid, $mileage, $mileage));
		return $status;
	}

	public static function fetch_riders($lastname, $firstname, $memberid = '') {
    	global $wpdb;
		$member_table = $wpdb->prefix . self::MEMBER_TABLE;
    	$results = $wpdb->get_results($wpdb->prepare('select * from ' . $member_table . 
			' where first_name like %s and last_name like %s and member_id like %s' . 
			' order by last_name, first_name', 
            $firstname . "%", $lastname . "%", $memberid . "%"), ARRAY_A);
		return $results;
	}

	public static function fetch_rider($memberid) {
    	global $wpdb;
		$member_table = $wpdb->prefix . self::MEMBER_TABLE;
    	$results = $wpdb->get_results($wpdb->prepare('select * from ' . $member_table . 
			' where member_id = %s', $memberid), ARRAY_A);
		return $results;
	}

	public static function insert_rider($memberid, $lastname, $firstname, $expdate) {
    	global $wpdb;
		$member_table = $wpdb->prefix . self::MEMBER_TABLE;
		$status = $wpdb->query($wpdb->prepare('insert into ' . $member_table .
			' (member_id, last_name, first_name, expir_date) values (%s, %s, %s, %s)' . 
			' on duplicate key update last_name = %s, first_name = %s, expir_date = %s',
			$memberid, $lastname, $firstname, $expdate, $lastname, $firstname, $expdate));
		return $status;
	}

	public static function delete_rider($memberid) {
    	global $wpdb;
		$member_table = $wpdb->prefix . self::MEMBER_TABLE;
		$status = $wpdb->query($wpdb->prepare('delete from ' . $member_table . 
			' where member_id = %s', $memberid));
		return $status;
	}

	public static function rollup_ridesheets($rideid, $maxdate) {
    	global $wpdb;
		$member_table = $wpdb->prefix . self::MEMBER_TABLE;
		$ride_table = $wpdb->prefix . self::RIDE_TABLE;
		$mileage_table = $wpdb->prefix . self::MILEAGE_TABLE;
		$leader_table = $wpdb->prefix . self::LEADER_TABLE;

		$status1 = $wpdb->query($wpdb->prepare('insert into ' . $mileage_table .
				'  (member_id, ride_id, mileage) select c.member_id, %d, SUM(m.mileage)' . 
				' from ((' . $mileage_table . ' as m inner join ' . $member_table . 
				' as c on c.member_id = m.member_id) inner join ' . $ride_table . 
				' as r on m.ride_id = r.ID) where r.ID <> %d and r.date <= %s' . 
				' group by m.member_id', $rideid, $rideid, $maxdate));

		$status2 = $wpdb->query($wpdb->prepare('insert into ' . $leader_table .
				'  (member_id, ride_id, rides_led) select c.member_id, %d, SUM(l.rides_led)' . 
				' from ((' . $leader_table . ' as l inner join ' . $member_table . 
				' as c on c.member_id = l.member_id) inner join ' . $ride_table . 
				' as r on l.ride_id = r.ID) where r.ID <> %d and r.date <= %s' . 
				' group by l.member_id', $rideid, $rideid, $maxdate));

		$status3 = $wpdb->query($wpdb->prepare('delete from ' . $mileage_table . 
				' where ride_id in (select ID from ' . $ride_table . 
				' where ID <> %d and date <= %s)', $rideid, $maxdate));

		$status4 = $wpdb->query($wpdb->prepare('delete from ' . $leader_table . 
				' where ride_id in (select ID from ' . $ride_table . 
				' where ID <> %d and date <= %s)', $rideid, $maxdate));

		$status5 = $wpdb->query($wpdb->prepare('delete from ' . $ride_table . 
				' where ID <> %d and date <= %s', $rideid, $maxdate));	

		$status = array(
			'm_inserts' => $status1,
			'l_inserts' => $status2,
			'm_deletes' => $status3,
			'l_deletes' => $status4,
			'r_deletes' => $status5
		);
		return $status;
	}

	public static function delete_database_for_restore() {
    	global $wpdb;
		$member_table = $wpdb->prefix . self::MEMBER_TABLE;
		$ride_table = $wpdb->prefix . self::RIDE_TABLE;
		$mileage_table = $wpdb->prefix . self::MILEAGE_TABLE;
		$leader_table = $wpdb->prefix . self::LEADER_TABLE;
		$errcnt = 0;
		$status = $wpdb->query('delete from ' . $leader_table);
		if (false === $status or 0 === $status) {
			$errcnt++;
		}
		$status = $wpdb->query('delete from ' . $mileage_table);
		if (false === $status or 0 === $status) {
			$errcnt++;
		}
		$status = $wpdb->query('delete from ' . $ride_table);
		if (false === $status or 0 === $status) {
			$errcnt++;
		}
		$status = $wpdb->query('delete from ' . $member_table);
		if (false === $status or 0 === $status) {
			$errcnt++;
		}
		return $errcnt;
	}

	public static function fetch_members_for_export() {
    	global $wpdb;
		$member_table = $wpdb->prefix . self::MEMBER_TABLE;
    	$results = $wpdb->get_results(
			'select member_id, first_name, last_name, expir_date from ' . $member_table . 
			' order by last_name, first_name', ARRAY_N);
		return $results;
	}

	public static function insert_members_for_restore($data) {
    	global $wpdb;
		error_log('enter insert_members_for_restore');		
		$member_table = $wpdb->prefix . self::MEMBER_TABLE;
		//error_log('members file contents');
		$errcnt = 0;
		foreach ( $data as $item ) {
			//error_log($item[0] . ',' . $item[1] . ',' . $item[2] . ',' . $item[3]);
			$status = $wpdb->query($wpdb->prepare('insert into ' . $member_table .
				' (member_id, first_name, last_name, expir_date) values (%s, %s, %s, %s)',
				$item[0], $item[1], $item[2], $item[3]));
			if (false === $status or 0 === $status) {
				$errcnt++;
			}
		}
		error_log('exit insert_members_for_restore');		
		return $errcnt;
	}

	public static function load_members_for_restore($filename) {
    	global $wpdb;
		$member_table = $wpdb->prefix . self::MEMBER_TABLE;
		$status = $wpdb->query("LOAD DATA LOCAL INFILE '" . $filename . "'" . 
			" INTO TABLE " . $member_table . " FIELDS TERMINATED BY ','" . 
			" OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\n'" . 
			" (member_id, first_name, last_name, expir_date)");
		return $status;
	}

	public static function fetch_rides_for_export() {
    	global $wpdb;
		$ride_table = $wpdb->prefix . self::RIDE_TABLE;
    	$results = $wpdb->get_results(
			'select ID, title, date, post_id from ' . $ride_table . 
			' order by date', ARRAY_N);
		return $results;
	}

	public static function insert_rides_for_restore($data) {
    	global $wpdb;
		$ride_table = $wpdb->prefix . self::RIDE_TABLE;
		//error_log('rides file contents');
		$errcnt = 0;
		foreach ( $data as $item ) {
			//error_log($item[0] . ',' . $item[1] . ',' . $item[2] . ',' . $item[3]);
			$status = $wpdb->query($wpdb->prepare('insert into ' . $ride_table .
				' (ID, title, date, post_id) values (%d, %s, %s, %d)', 
				intval($item[0]), $item[1], $item[2], intval($item[3])));
			if (false === $status or 0 === $status) {
				$errcnt++;
			}
		}
		return $errcnt;
	}

	public static function load_rides_for_restore($filename) {
    	global $wpdb;
		$ride_table = $wpdb->prefix . self::RIDE_TABLE;
		$status = $wpdb->query("LOAD DATA LOCAL INFILE '" . $filename . "'" . 
			" INTO TABLE " . $ride_table . " FIELDS TERMINATED BY ','" . 
			" OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\n'" . 
			" (ID, title, date, post_id)");
		return $status;
	}

	public static function fetch_mileage_for_export() {
    	global $wpdb;
		$mileage_table = $wpdb->prefix . self::MILEAGE_TABLE;
    	$results = $wpdb->get_results(
			'select ride_id, member_id, mileage from ' . $mileage_table . 
			' order by ride_id', ARRAY_N);
		return $results;
	}

	public static function insert_mileage_for_restore($data) {
    	global $wpdb;
		$mileage_table = $wpdb->prefix . self::MILEAGE_TABLE;
		//error_log('mileage file contents');
		$errcnt = 0;
		foreach ( $data as $item ) {
			//error_log($item[0] . ',' . $item[1] . ',' . $item[2]);
			$status = $wpdb->query($wpdb->prepare('insert into ' . $mileage_table . 
				' (ride_id, member_id, mileage) values (%d, %s, %d)', 
				intval($item[0]), $item[1], intval($item[2])));
			if (false === $status or 0 === $status) {
				$errcnt++;
			}
		}
		return $errcnt;
	}

	public static function load_mileage_for_restore($filename) {
    	global $wpdb;
		$mileage_table = $wpdb->prefix . self::MILEAGE_TABLE;
		$status = $wpdb->query("LOAD DATA LOCAL INFILE '" . $filename . "'" . 
			" INTO TABLE " . $mileage_table . " FIELDS TERMINATED BY ','" . 
			" OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\n'" . 
			" (ride_id, member_id, mileage)");
		return $status;
	}

	public static function fetch_leaders_for_export() {
    	global $wpdb;
		$leader_table = $wpdb->prefix . self::LEADER_TABLE;
    	$results = $wpdb->get_results(
			'select ride_id, member_id, rides_led from ' . $leader_table . 
			' order by ride_id', ARRAY_N);
		return $results;
	}

	public static function insert_leaders_for_restore($data) {
    	global $wpdb;
		$leader_table = $wpdb->prefix . self::LEADER_TABLE;
		//error_log('leaders file contents');
		$errcnt = 0;
		foreach ( $data as $item ) {
			//error_log($item[0] . ',' . $item[1] . ',' . $item[2]);
			$status = $wpdb->query($wpdb->prepare('insert into ' . $leader_table . 
				' (ride_id, member_id, rides_led) values (%d, %s, %d)', 
				intval($item[0]), $item[1], intval($item[2])));
			if (false === $status or 0 === $status) {
				$errcnt++;
			}
		}
		return $errcnt;
	}

	public static function load_leaders_for_restore($filename) {
    	global $wpdb;
		$leader_table = $wpdb->prefix . self::LEADER_TABLE;
		$status = $wpdb->query("LOAD DATA LOCAL INFILE '" . $filename . "'" . 
			" INTO TABLE " . $leader_table . " FIELDS TERMINATED BY ','" . 
			" OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\n'" . 
			" (ride_id, member_id, rides_led)");
		return $status;
	}

	public static function job_get_status($jobid) {
    	global $wpdb;
		$jobs_table = $wpdb->prefix . self::JOBS_TABLE;
		$result = $wpdb->get_row($wpdb->prepare('select * from ' . $jobs_table . 
			' where job_id = %s', $jobid), ARRAY_A);
		return $result;
	}

	public static function job_set_status($jobid, $status, $errmsg = '') {
    	global $wpdb;
		$jobs_table = $wpdb->prefix . self::JOBS_TABLE;
		$t = time();
		$status = $wpdb->query($wpdb->prepare('insert into ' . $jobs_table .
			' (job_id, status, timestamp, error_msg) values (%s, %s, %d, %s)' . 
			' on duplicate key update status = %s, timestamp = %d, error_msg = %s',
			$jobid, $status, $t, $errmsg, $status, $t, $errmsg));
		return $status;
	}

	public static function num_running_jobs() {
    	global $wpdb;
		$jobs_table = $wpdb->prefix . self::JOBS_TABLE;
		$results = $wpdb->get_var('select count(*) from ' . $jobs_table . 
			' where status = \'triggered\' or status = \'started\'');
		return $results;
	}

	public static function max_job_timestamp() {
    	global $wpdb;
		$jobs_table = $wpdb->prefix . self::JOBS_TABLE;
		$results = $wpdb->get_var('select max(timestamp) from ' . $jobs_table . 
			' where status = \'triggered\' or status = \'started\'');
		return $results;
	}

	public static function job_remove($jobid) {
    	global $wpdb;
		$jobs_table = $wpdb->prefix . self::JOBS_TABLE;
		$status = $wpdb->query($wpdb->prepare('delete from ' . $jobs_table . 
			' where job_id = %s', $jobid));
		return $status;
	}

	public static function job_remove_failed() {
    	global $wpdb;
		$jobs_table = $wpdb->prefix . self::JOBS_TABLE;
		$status = $wpdb->query($wpdb->prepare('delete from ' . $jobs_table . 
			' where status = %s', 'failed'));
		return $status;
	} 

	public static function job_remove_running() {
    	global $wpdb;
		$jobs_table = $wpdb->prefix . self::JOBS_TABLE;
		$status = $wpdb->query('delete from ' . $jobs_table . 
			' where status = \'triggered\' or status = \'started\'');
		return $status;
	} 

	public static function create_db_tables( ) {
		global $wpdb;
		$member_table = $wpdb->prefix . self::MEMBER_TABLE;
		$ride_table = $wpdb->prefix . self::RIDE_TABLE;
		$mileage_table = $wpdb->prefix . self::MILEAGE_TABLE;
		$leader_table = $wpdb->prefix . self::LEADER_TABLE;
		$jobs_table = $wpdb->prefix . self::JOBS_TABLE;
		
		$result = $wpdb->query('create table if not exists ' . $member_table . 
			' (member_id VARCHAR(5) NOT NULL,' .
			' last_name TEXT NOT NULL,' . 
			' first_name TEXT NOT NULL,' . 
			' expir_date DATE NOT NULL,' . 
			' constraint pk_' . $member_table . ' PRIMARY KEY (member_id))');
		if (false === $result) {
			error_log( 'Could not create table ' . $member_table . ': ' . $wpdb->last_error);
		}

		$result = $wpdb->query('create table if not exists ' . $ride_table .
			' (ID BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,' .
			' title TEXT NOT NULL,' .
			' date DATE NOT NULL,' . 
			' post_id BIGINT UNSIGNED NOT NULL DEFAULT 0,' . 
			' constraint pk_' . $ride_table . ' PRIMARY KEY (ID))');
		if (false === $result) {
			error_log( 'Could not create table ' . $ride_table . ': ' . $wpdb->last_error);
		}

		$result = $wpdb->query('create table if not exists ' . $mileage_table . 
			' (member_id VARCHAR(5) NOT NULL,' . 
			' ride_id BIGINT UNSIGNED NOT NULL,' . 
			' mileage INT UNSIGNED NOT NULL,' . 
			' constraint pk_' . $mileage_table . ' PRIMARY KEY (member_id, ride_id),' . 
			' constraint fk_' . $mileage_table . '_member_id FOREIGN KEY (member_id) REFERENCES ' . $member_table . ' (member_id),' . 
			' constraint fk_' . $mileage_table . '_ride_id FOREIGN KEY (ride_id) REFERENCES ' . $ride_table . ' (ID))');
		if (false === $result) {
			error_log( 'Could not create table ' . $mileage_table . ': ' . $wpdb->last_error);
		}

		$result = $wpdb->query('create table if not exists ' . $leader_table . 
			' (member_id VARCHAR(5) NOT NULL,' . 
			' ride_id BIGINT UNSIGNED NOT NULL,' . 
			' rides_led INT UNSIGNED NOT NULL,' . 
			' constraint pk_' . $leader_table . ' PRIMARY KEY (member_id, ride_id),' . 
			' constraint fk_' . $leader_table . '_member_id FOREIGN KEY (member_id) REFERENCES ' . $member_table . ' (member_id),' . 
			' constraint fk_' . $leader_table . '_ride_id FOREIGN KEY (ride_id) REFERENCES ' . 
			$ride_table . ' (ID))');
		if (false === $result) {
			error_log( 'Could not create table ' . $leader_table . ': ' . $wpdb->last_error);
		}

		$result = $wpdb->query('create table if not exists ' . $jobs_table . 
			' (job_id VARCHAR(20) NOT NULL,' .
			' status TEXT NOT NULL,' . 
			' timestamp BIGINT UNSIGNED NOT NULL,' . 
			' error_msg TEXT,' . 
			' constraint pk_' . $jobs_table . ' PRIMARY KEY (job_id))');
		if (false === $result) {
			error_log( 'Could not create table ' . $jobs_table . ': ' . $wpdb->last_error);
		}
	}

	public static function create_db_views( ) {
		global $wpdb;
		$member_table = $wpdb->prefix . self::MEMBER_TABLE;
		$ride_table = $wpdb->prefix . self::RIDE_TABLE;
		$mileage_table = $wpdb->prefix . self::MILEAGE_TABLE;
		$leader_table = $wpdb->prefix . self::LEADER_TABLE;

		$result = $wpdb->query('create or replace view ' . self::LT_MILES_VIEW . 
			' (member_id, first_name, last_name, mileage)' . 
			' as select c.member_id, c.first_name, c.last_name, SUM(m.mileage)' . 
			' from ' . $member_table . ' as c inner join ' . $mileage_table . ' as m on c.member_id = m.member_id' . 
			' group by m.member_id');
		if (false === $result) {
			error_log( 'Could not create view ' . self::LT_MILES_VIEW . ': ' . $wpdb->last_error);
		}

		$result = $wpdb->query('create or replace view ' . self::YTD_MILES_VIEW . 
			' (member_id, first_name, last_name, mileage)' . 
			' as select c.member_id, c.first_name, c.last_name, SUM(m.mileage)' . 
			' from ((' . $mileage_table . ' as m inner join ' . $member_table . ' as c on c.member_id = m.member_id)' . 
			' inner join ' . $ride_table . ' as r on m.ride_id = r.ID)' . 
			' where r.date >= DATE_FORMAT(CURDATE(), \'%Y-01-01\')' . 
			' group by m.member_id');
		if (false === $result) {
			error_log( 'Could not create view ' . self::YTD_MILES_VIEW . ': ' . $wpdb->last_error);
		}

		$result = $wpdb->query('create or replace view ' . self::LY_MILES_VIEW . 
			' (member_id, first_name, last_name, mileage)' . 
			' as select c.member_id, c.first_name, c.last_name, SUM(m.mileage)' . 
			' from ((' . $mileage_table . ' as m inner join ' . $member_table . ' as c on c.member_id = m.member_id)' . 
			' inner join ' . $ride_table . ' as r on m.ride_id = r.ID)' . 
			' where r.date between DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 YEAR), \'%Y-01-01\')' . 
			' and DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 YEAR), \'%Y-12-31\')' . 
			' group by m.member_id');
		if (false === $result) {
			error_log( 'Could not create view ' . self::LY_MILES_VIEW . ': ' . $wpdb->last_error);
		}

		$result = $wpdb->query('create or replace view ' . self::LY_LT_MILES_VIEW . 
			' (member_id, first_name, last_name, mileage)' . 
			' as select c.member_id, c.first_name, c.last_name, SUM(m.mileage)' . 
			' from ((' . $mileage_table . ' as m inner join ' . $member_table . ' as c on c.member_id = m.member_id)' . 
			' inner join ' . $ride_table . ' as r on m.ride_id = r.ID)' . 
			' where r.date < DATE_FORMAT(CURDATE(), \'%Y-01-01\')' . 
			' group by m.member_id');
		if (false === $result) {
			error_log( 'Could not create view ' . self::LY_LT_MILES_VIEW . ': ' . $wpdb->last_error);
		}

		$result = $wpdb->query('create or replace view ' . self::YBL_LT_MILES_VIEW . 
			' (member_id, first_name, last_name, mileage)' . 
			' as select c.member_id, c.first_name, c.last_name, SUM(m.mileage)' . 
			' from ((' . $mileage_table . ' as m inner join ' . $member_table . ' as c on c.member_id = m.member_id)' . 
			' inner join ' . $ride_table . ' as r on m.ride_id = r.ID)' . 
			' where r.date < DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 YEAR), \'%Y-01-01\')' . 
			' group by m.member_id');
		if (false === $result) {
			error_log( 'Could not create view ' . self::YBL_LT_MILES_VIEW . ': ' . $wpdb->last_error);
		}

		$result = $wpdb->query('create or replace view ' . self::LY_LT_ACHVMNT_VIEW . 
			' (member_id, first_name, last_name, mileage, achievement)' . 
			' as select a.member_id, a.first_name, a.last_name, a.mileage, concat(floor(a.mileage/10000),\'0K\')' . 
			' from ' . self::LY_LT_MILES_VIEW . ' as a inner join ' . self::YBL_LT_MILES_VIEW . ' as b on a.member_id = b.member_id' . 
			' where floor(a.mileage/10000) > floor(b.mileage/10000)');
		if (false === $result) {
			error_log( 'Could not create view ' . self::LY_LT_ACHVMNT_VIEW . ': ' . $wpdb->last_error);
		}

		$result = $wpdb->query('create or replace view ' . self::YTD_RIDES_LED_VIEW . 
			' (title, date, member_id)' . 
			' as select r.title, r.date, l.member_id' . 
			' from ' . $ride_table . ' as r inner join ' . $leader_table . ' as l on r.ID = l.ride_id' . 
			' where r.date >= DATE_FORMAT(CURDATE(), \'%Y-01-01\')' . 
			' order by r.date');
		if (false === $result) {
			error_log( 'Could not create view ' . self::YTD_RIDES_LED_VIEW . ': ' . $wpdb->last_error);
		}

		$result = $wpdb->query('create or replace view ' . self::LY_RIDES_LED_VIEW . 
			' (title, date, member_id)' . 
			' as select r.title, r.date, l.member_id' .
			' from ' . $ride_table . ' as r inner join ' . $leader_table . ' as l on r.ID = l.ride_id' . 
			' where r.date between DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 YEAR), \'%Y-01-01\')' . 
			' and DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 YEAR), \'%Y-12-31\')' . 
			' order by r.date');
		if (false === $result) {
			error_log( 'Could not create view ' . self::LY_RIDES_LED_VIEW . ': ' . $wpdb->last_error);
		}

		$result = $wpdb->query('create or replace view ' . self::YTD_LED_VIEW . 
			' (member_id, first_name, last_name, rides_led)' . 
			' as select c.member_id, c.first_name, c.last_name, SUM(l.rides_led)' . 
			' from ((' . $leader_table . ' as l inner join ' . $member_table . ' as c on c.member_id = l.member_id)' . 
			' inner join ' . $ride_table . ' as r on l.ride_id = r.ID)' . 
			' where r.date >= DATE_FORMAT(CURDATE(), \'%Y-01-01\')' . 
			' group by l.member_id');
		if (false === $result) {
			error_log( 'Could not create view ' . self::YTD_LED_VIEW . ': ' . $wpdb->last_error);
		}

		$result = $wpdb->query('create or replace view ' . self::LY_LED_VIEW . 
			'(member_id, first_name, last_name, rides_led)' . 
			' as select c.member_id, c.first_name, c.last_name, SUM(l.rides_led)' . 
			' from ((' . $leader_table . ' as l inner join ' . $member_table . ' as c on c.member_id = l.member_id)' . 
			' inner join ' . $ride_table . ' as r on l.ride_id = r.ID)' . 
			' where r.date between DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 YEAR), \'%Y-01-01\')' . 
			' and DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 YEAR), \'%Y-12-31\')' . 
			' group by l.member_id');
		if (false === $result) {
			error_log( 'Could not create view ' . self::LY_LED_VIEW . ': ' . $wpdb->last_error);
		}

		$result = $wpdb->query('create or replace view ' . self::YTD_RIDES_VIEW . 
			' (title, date, mileage, member_id)' . 
			' as select r.title, r.date, m.mileage, m.member_id' . 
			' from ' . $ride_table . ' as r inner join ' . $mileage_table . ' as m on r.ID = m.ride_id' . 
			' where r.date >= DATE_FORMAT(CURDATE(), \'%Y-01-01\')' . 
			' order by r.date'); 
		if (false === $result) {
			error_log( 'Could not create view ' . self::YTD_RIDES_VIEW . ': ' . $wpdb->last_error);
		}

		$result = $wpdb->query('create or replace view ' . self::LY_RIDES_VIEW . 
			' (title, date, mileage, member_id)' . 
			' as select r.title, r.date, m.mileage, m.member_id' . 
			' from ' . $ride_table . ' as r inner join ' . $mileage_table . ' as m on r.ID = m.ride_id' . 
			' where r.date between DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 YEAR), \'%Y-01-01\')' . 
			' and DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 YEAR), \'%Y-12-31\')' . 
			' order by r.date');
		if (false === $result) {
			error_log( 'Could not create view ' . self::LY_RIDES_VIEW . ': ' . $wpdb->last_error);
		}
	}

	public static function drop_db_tables( ) {
		global $wpdb;
		$member_table = $wpdb->prefix . self::MEMBER_TABLE;
		$ride_table = $wpdb->prefix . self::RIDE_TABLE;
		$mileage_table = $wpdb->prefix . self::MILEAGE_TABLE;
		$leader_table = $wpdb->prefix . self::LEADER_TABLE;
		$jobs_table = $wpdb->prefix . self::JOBS_TABLE;

		$result = $wpdb->query('drop table if exists ' . $leader_table . ', ' . $mileage_table . ', ' . $ride_table . ', ' . $member_table . ', ' . $jobs_table);
		if (false === $result) {
			error_log( 'Could not drop tables: ' . $wpdb->last_error);
		}
	}

	public static function drop_db_views( ) {
		$result = $wpdb->query('drop view if exists ' . self::LY_RIDES_VIEW . ', ' . self::YTD_RIDES_VIEW . ', ' . self::LY_LED_VIEW . 
			', ' . self::YTD_LED_VIEW . ', ' . self::LY_RIDES_LED_VIEW . ', ' . self::YTD_RIDES_LED_VIEW . ', ' . self::LY_LT_ACHVMNT_VIEW . 
			', ' . self::YBL_LT_MILES_VIEW . ', ' . self::LY_LT_MILES_VIEW . ', ' . self::LY_MILES_VIEW . ', ' . self::YTD_MILES_VIEW . 
			', ' . self::LT_MILES_VIEW);
		if (false === $result) {
			error_log( 'Could not drop views: ' . $wpdb->last_error);
		}
	}
   
}
