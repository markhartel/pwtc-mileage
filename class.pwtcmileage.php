<?php

class PwtcMileage {
	const MEMBER_TABLE = 'pwtc_membership';
	const RIDE_TABLE = 'pwtc_club_rides';
	const MILEAGE_TABLE = 'pwtc_ride_mileage';
	const LEADER_TABLE = 'pwtc_ride_leaders';

	const LT_MILES_VIEW = 'pwtc_lt_miles_vw';
	const YTD_MILES_VIEW = 'pwtc_ytd_miles_vw';
	const LY_MILES_VIEW = 'pwtc_ly_miles_vw';
	const LY_LT_MILES_VIEW = 'pwtc_ly_lt_miles_vw';
	const YBL_LT_MILES_VIEW = 'pwtc_ybl_lt_miles_vw';
	const LY_LT_ACHVMNT_VIEW = 'pwtc_ly_lt_achvmnt_vw';
	const YTD_RIDES_LED_VIEW = 'pwtc_ytd_rides_led_vw';
	const LY_RIDES_LED_VIEW = 'pwtc_ly_rides_led_vw';
	const YTD_LED_VIEW = 'pwtc_ytd_led_vw';
	const LY_LED_VIEW = 'pwtc_ly_led_vw';
	const YTD_RIDES_VIEW = 'pwtc_ytd_rides_vw';
	const LY_RIDES_VIEW = 'pwtc_ly_rides_vw';

    private static $initiated = false;

	public static function init() {
		if ( ! self::$initiated ) {
			self::init_hooks();
		}
	}

	/**
	 * Initializes WordPress hooks
	 */
	private static function init_hooks() {
		self::$initiated = true;
		add_action( 'admin_menu', array( 'PwtcMileage', 'plugin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( 'PwtcMileage', 'load_admin_scripts' ) );
		/*
		[pwtc_mileage_year_to_date orderby="mileage/name" minimum="500"]
		[pwtc_mileage_lifetime orderby="mileage/name" minimum="500"]
		[pwtc_mileage_last_year orderby="mileage/name" minimum="500"]
		[pwtc_rides_led_year_to_date orderby="number/name" minimum="12"]
		[pwtc_rides_led_last_year orderby="number/name" minimum="12"]
		[pwtc_achievement_last_year]
		*/
		add_shortcode('pwtc_achievement_last_year', array( 'PwtcMileage', 'shortcode_ly_lt_achvmnt'));
		/*
		add_shortcode('pwtc_mileage_year_to_date', array( 'PwtcMileage', 'shortcode_ytd_mileage'));
		*/
		add_action( 'wp_ajax_pwtc_mileage_lookup_rides', array( 'PwtcMileage', 'lookup_rides_callback') );
		add_action( 'wp_ajax_pwtc_mileage_create_ride', array( 'PwtcMileage', 'create_ride_callback') );
		add_action( 'wp_ajax_pwtc_mileage_create_ride_from_event', array( 'PwtcMileage', 'create_ride_from_event_callback') );
		add_action( 'wp_ajax_pwtc_mileage_remove_ride', array( 'PwtcMileage', 'remove_ride_callback') );
		add_action( 'wp_ajax_pwtc_mileage_lookup_ridesheet', array( 'PwtcMileage', 'lookup_ridesheet_callback') );
		add_action( 'wp_ajax_pwtc_mileage_lookup_riders', array( 'PwtcMileage', 'lookup_riders_callback') );
		add_action( 'wp_ajax_pwtc_mileage_create_rider', array( 'PwtcMileage', 'create_rider_callback') );
		add_action( 'wp_ajax_pwtc_mileage_remove_rider', array( 'PwtcMileage', 'remove_rider_callback') );
		add_action( 'wp_ajax_pwtc_mileage_remove_leader', array( 'PwtcMileage', 'remove_leader_callback') );
		add_action( 'wp_ajax_pwtc_mileage_remove_mileage', array( 'PwtcMileage', 'remove_mileage_callback') );
		add_action( 'wp_ajax_pwtc_mileage_add_leader', array( 'PwtcMileage', 'add_leader_callback') );
		add_action( 'wp_ajax_pwtc_mileage_add_mileage', array( 'PwtcMileage', 'add_mileage_callback') );
		add_action( 'wp_ajax_pwtc_mileage_generate_report', array( 'PwtcMileage', 'generate_report_callback') );
    }

	public static function load_admin_scripts($hook) {
		if (!strpos($hook, "pwtc_mileage")) {
            return;
        }
        wp_enqueue_style( 'pwtc_mileage_admin_css', PWTC_MILEAGE__PLUGIN_URL . 'admin-style.css' );
		wp_enqueue_script( 'pwtc_mileage_admin_js', PWTC_MILEAGE__PLUGIN_URL . 'admin-scripts.js', array('jquery'), 1.1, true);
	}

	public static function lookup_rides_callback() {
		$startdate = $_POST['startdate'];	
		$date = date("D M j, Y", strtotime($startdate));
		$rides = self::fetch_club_rides($startdate);
		$ridecal = self::fetch_sched_rides($startdate);
		$response = array(
			'startdate' => $startdate, 
			'date' => $date, 
			'rides' => $rides,
			'ridecal' => $ridecal);
    	echo wp_json_encode($response);
		wp_die();
	}

	public static function create_ride_callback() {
		$startdate = $_POST['startdate'];	
		$title = $_POST['title'];	
		$status = self::insert_ride($title, $startdate);
		if (false === $status) {
			$response = array(
				'error' => 'Could not insert ride into database.'
			);
    		echo wp_json_encode($response);
		}
		else {
			$rides = self::fetch_club_rides($startdate);
			$ridecal = self::fetch_sched_rides($startdate);
			$response = array(
				'startdate' => $startdate, 
				'rides' => $rides,
				'ridecal' => $ridecal);
    		echo wp_json_encode($response);
		}
		wp_die();
	}

	public static function create_ride_from_event_callback() {
		$startdate = $_POST['startdate'];	
		$title = $_POST['title'];	
		$postid = $_POST['post_id'];	
		$status = self::insert_ride_with_postid($title, $startdate, intval($postid));
		if (false === $status) {
			$response = array(
				'error' => 'Could not insert ride into database.'
			);
    		echo wp_json_encode($response);
		}
		else {
			$rides = self::fetch_club_rides($startdate);
			$ridecal = self::fetch_sched_rides($startdate);
			$response = array(
				'startdate' => $startdate, 
				'rides' => $rides,
				'ridecal' => $ridecal);
    		echo wp_json_encode($response);
		}
		wp_die();
	}

	public static function remove_ride_callback() {
		$startdate = $_POST['startdate'];
		$date = date("D M j, Y", strtotime($startdate));
		$rideid = $_POST['ride_id'];
		$mcnt = self::fetch_ride_has_mileage(intval($rideid));
		$lcnt = self::fetch_ride_has_leaders(intval($rideid));
		if ($mcnt > 0 or $lcnt > 0) {
			$response = array(
				'error' => 'Cannot remove a ride with a ride sheet.'
			);
    		echo wp_json_encode($response);
		}
		else {
			$status = self::delete_ride(intval($rideid));
			if (false === $status) {
				$response = array(
					'error' => 'Could not delete ride from database.'
				);
    			echo wp_json_encode($response);
			}
			else {
				$rides = self::fetch_club_rides($startdate);
				$ridecal = self::fetch_sched_rides($startdate);
				$response = array(
					'startdate' => $startdate, 
					'date' => $date, 
					'rides' => $rides,
					'ridecal' => $ridecal);
    			echo wp_json_encode($response);
			}
		}
		wp_die();	
	}

	public static function lookup_ridesheet_callback() {
		$rideid = $_POST['ride_id'];
		$startdate = $_POST['startdate'];
		$date = date("D M j, Y", strtotime($startdate));
		$title = $_POST['title'];
		$leaders = self::fetch_ride_leaders(intval($rideid));
		$mileage = self::fetch_ride_mileage(intval($rideid));
		$response = array(
			'startdate' => $startdate,
			'date' => $date,
			'ride_id' => $rideid,
			'title' => $title,
			'leaders' => $leaders,
			'mileage' => $mileage);
    	echo wp_json_encode($response);
		wp_die();
	}

	public static function lookup_riders_callback() {
		$lastname = $_POST['lastname'];	
		$firstname = $_POST['firstname'];
		$members = self::fetch_riders($lastname, $firstname);	
		$response = array(
			'lastname' => $lastname,
			'firstname' => $firstname,
			'members' => $members);
    	echo wp_json_encode($response);
		wp_die();
	}

	public static function create_rider_callback() {
		$memberid = $_POST['member_id'];	
		$lastname = $_POST['lastname'];	
		$firstname = $_POST['firstname'];
		$lookupfirst = $_POST['lookup_first'];
		$lookuplast = $_POST['lookup_last'];
		$status = self::insert_rider($memberid, $lastname, $firstname);	
		if (false === $status) {
			$response = array(
				'error' => 'Could not insert rider into database.'
			);
    		echo wp_json_encode($response);
		}
		else if (0 === $status) {
			$response = array(
				'error' => 'Could not insert rider into database.'
			);
    		echo wp_json_encode($response);
		}
		else {
			$members = self::fetch_riders($lookuplast, $lookupfirst);
			$response = array(
				'lastname' => $lookuplast,
				'firstname' => $lookupfirst,
				'members' => $members);
    		echo wp_json_encode($response);
		}
		wp_die();
	}

	public static function remove_rider_callback() {
		$memberid = $_POST['member_id'];	
		$lastname = $_POST['lastname'];	
		$firstname = $_POST['firstname'];
		$mcnt = self::fetch_member_has_mileage($memberid);
		$lcnt = self::fetch_member_has_leaders($memberid);
		if ($mcnt > 0 or $lcnt > 0) {
			$response = array(
				'error' => 'Cannot remove a rider with a ride sheet.'
			);
    		echo wp_json_encode($response);
		}
		else {
			$status = self::delete_rider($memberid);	
			if (false === $status) {
				$response = array(
					'error' => 'Could not delete rider from database.'
				);
    			echo wp_json_encode($response);
			}
			else if (0 === $status) {
				$response = array(
					'error' => 'Could not delete rider from database.'
				);
    			echo wp_json_encode($response);
			}
			else {
				$members = self::fetch_riders($lastname, $firstname);	
				$response = array(
					'lastname' => $lastname,
					'firstname' => $firstname,
					'members' => $members);
   				echo wp_json_encode($response);
			}
		}
		wp_die();
	}

	public static function remove_leader_callback() {
		$rideid = $_POST['ride_id'];
		$memberid = $_POST['member_id'];
		$status = self::delete_ride_leader(intval($rideid), $memberid);
		if (false === $status) {
			$response = array(
				'error' => 'Could not delete ride leader from database.'
			);
    		echo wp_json_encode($response);
		}
		else {
			$leaders = self::fetch_ride_leaders(intval($rideid));
			$response = array(
				'ride_id' => $rideid,
				'leaders' => $leaders
			);
    		echo wp_json_encode($response);
		}
		wp_die();
	}

	public static function remove_mileage_callback() {
		$rideid = $_POST['ride_id'];
		$memberid = $_POST['member_id'];
		$status = self::delete_ride_mileage(intval($rideid), $memberid);
		if (false === $status) {
			$response = array(
				'error' => 'Could not delete ride mileage from database.'
			);
    		echo wp_json_encode($response);
		}
		else {
			$mileage = self::fetch_ride_mileage(intval($rideid));
			$response = array(
				'ride_id' => $rideid,
				'mileage' => $mileage
			);
    		echo wp_json_encode($response);
		}
		wp_die();
	}

	public static function add_leader_callback() {
		$rideid = $_POST['ride_id'];
		$memberid = $_POST['member_id'];
		$status = self::insert_ride_leader(intval($rideid), $memberid);
		if (false === $status) {
			$response = array(
				'error' => 'Could not insert ride leader into database.'
			);
    		echo wp_json_encode($response);
		}
		else {
			$leaders = self::fetch_ride_leaders(intval($rideid));
			$response = array(
				'ride_id' => $rideid,
				'leaders' => $leaders
			);
    		echo wp_json_encode($response);
		}
		wp_die();
	}

	public static function add_mileage_callback() {
		$rideid = $_POST['ride_id'];
		$memberid = $_POST['member_id'];
		$mileage = $_POST['mileage'];
		$status = self::insert_ride_mileage(intval($rideid), $memberid, intval($mileage));
		if (false === $status) {
			$response = array(
				'error' => 'Could not insert ride mileage into database.'
			);
    		echo wp_json_encode($response);
		}
		else {
			$mileage = self::fetch_ride_mileage(intval($rideid));
			$response = array(
				'ride_id' => $rideid,
				'mileage' => $mileage
			);
    		echo wp_json_encode($response);
		}
		wp_die();
	}

	public static function generate_report_callback() {
		$reportid = $_POST['report_id'];
		switch ($reportid) {
			case "ytd_miles":
				$sort = $_POST['sort'];
				$header = self::get_ytd_miles_header();
				$data = self::fetch_ytd_miles(ARRAY_N, $sort);
				$response = array(
					'title' => 'Year-to-date Rider Mileage',
					'header' => $header,
					'data' => $data
				);
				echo wp_json_encode($response);
				break;
			case "ytd_led":
				$sort = $_POST['sort'];
				$header = self::get_ytd_led_header();
				$data = self::fetch_ytd_led(ARRAY_N, $sort);
				$response = array(
					'title' => 'Year-to-date Number of Rides Led',
					'header' => $header,
					'data' => $data
				);
				echo wp_json_encode($response);
				break;
			case "tbd":
				break;
			default:
				$response = array(
					'error' => 'Report type ' . $reportid . ' not found.'
				);
				echo wp_json_encode($response);
		}
		wp_die();
	}

	public static function plugin_menu() {
		add_menu_page('PWTC Mileage', 'PWTC Mileage', 'manage_options', 'pwtc_mileage_menu', array( 'PwtcMileage', 'plugin_menu_page'));
		add_submenu_page('pwtc_mileage_menu', 'Generate Reports', 'Generate Reports', 'manage_options', 'pwtc_mileage_generate_reports', array('PwtcMileage', 'page_generate_reports'));
		add_submenu_page('pwtc_mileage_menu', 'Manage Riders', 'Manage Riders', 'manage_options', 'pwtc_mileage_manage_riders', array('PwtcMileage', 'page_manage_riders'));
		add_submenu_page('pwtc_mileage_menu', 'Manage Ride Sheets', 'Manage Ride Sheets', 'manage_options', 'pwtc_mileage_manage_ride_sheets', array('PwtcMileage', 'page_manage_ride_sheets'));
		add_submenu_page('pwtc_mileage_menu', 'Manage Year End', 'Manage Year End', 'manage_options', 'pwtc_mileage_manage_year_end', array('PwtcMileage', 'page_manage_year_end'));
		remove_submenu_page('pwtc_mileage_menu', 'pwtc_mileage_menu');
		add_submenu_page('pwtc_mileage_menu', 'Settings', 'Settings', 'manage_options', 'pwtc_mileage_settings', array( 'PwtcMileage', 'page_manage_settings'));
	}

	public static function plugin_menu_page() {
	}

	public static function page_manage_ride_sheets() {
		include('admin-man-ridesheets.php');
	}

	public static function page_generate_reports() {
		include('admin-gen-reports.php');
	}

	public static function page_manage_riders() {
		include('admin-man-riders.php');
	}

	public static function page_manage_year_end() {
		include('admin-man-yearend.php');
	}

	public static function page_manage_settings() {
		include('admin-man-settings.php');
	}

	public static function shortcode_ly_lt_achvmnt() {
		$out = '';
		$thisyear = date('Y');
    	$lastyear = intval($thisyear) - 1;
		$results = self::fetch_ly_lt_achvmnt();
		$out .= '<div><table><tr>';
		$out .= '<th>Name</th>';
		$out .= '<th>' . $lastyear . ' Lifetime Mileage</th>';
		$out .= '<th>' . $lastyear . ' Achievement</th>';
		$out .= '</tr>';
		foreach( $results as $row ):
			$out .= '<tr>';
			$out .= '<td>' . $row['first_name'] . ' ' . $row['last_name'] . '</td>';
			$out .= '<td>' . $row['mileage'] . '</td>';
			$out .= '<td>' . $row['achievement'] . '</td>';
			$out .= '</tr>';
		endforeach;
		$out .= '</table></div>';
		return $out;
	}

/*
	public static function shortcode_ytd_mileage($atts = [], $content = null, $tag = '') {
		$my_atts = convert_mileage_atts($atts, $tag);
		$out = '';
		$thisyear = date('Y');
		$results = self::fetch_ytd_mileage($my_atts['orderby'], $my_atts['minimum']);
		$out .= '<div><table><tr>';
		$out .= '<th>Name</th>';
		$out .= '<th>' . $thisyear . ' YTD Mileage</th>';
		$out .= '</tr>';
		foreach( $results as $row ):
			$out .= '<tr>';
			$out .= '<td>' . $row['first_name'] . ' ' . $row['last_name'] . '</td>';
			$out .= '<td>' . $row['mileage'] . '</td>';
			$out .= '</tr>';
		endforeach;
		$out .= '</table></div>';
		return $out;
	}

	public static function fetch_ytd_mileage($orderby, $minimum) {
    	global $wpdb;
    	$results = $wpdb->get_results('select * from ' . self::YTD_MILES_VIEW . 
			' where mileage >= ' . $minimum . ' order by ' . $orderby, ARRAY_A);
		return $results;
	}

*/
	public static function fetch_ly_lt_achvmnt() {
    	global $wpdb;
    	$results = $wpdb->get_results('select * from ' . self::LY_LT_ACHVMNT_VIEW . 
			' order by mileage', ARRAY_A);
		return $results;
	}

	public static function fetch_ytd_miles($outtype, $sort) {
    	global $wpdb;
    	$results = $wpdb->get_results(
			'select member_id, concat(first_name, \' \', last_name), mileage from ' . 
			self::YTD_MILES_VIEW . ' order by ' . $sort , $outtype);
		return $results;
	}

	public static function get_ytd_miles_header() {
		$header = array('Member ID', 'Name', 'Mileage');
		return $header;
	}

	public static function fetch_ytd_led($outtype, $sort) {
    	global $wpdb;
    	$results = $wpdb->get_results(
			'select member_id, concat(first_name, \' \', last_name), rides_led from ' . 
			self::YTD_LED_VIEW . ' order by ' . $sort , $outtype);
		return $results;
	}

	public static function get_ytd_led_header() {
		$header = array('Member ID', 'Name', 'Rides Led');
		return $header;
	}

	public static function fetch_club_rides($date) {
    	global $wpdb;
		$ride_table = $wpdb->prefix . self::RIDE_TABLE;
    	$results = $wpdb->get_results($wpdb->prepare('select * from ' . $ride_table . 
			' where date = %s', $date), ARRAY_A);
		return $results;
	}

	public static function fetch_sched_rides($date) {
    	global $wpdb;
    	$results = $wpdb->get_results($wpdb->prepare('select p.ID, p.post_title' . 
			' from ' . $wpdb->posts . ' as p inner join ' . $wpdb->postmeta . 
			' as m on p.ID = m.post_id' . 
			' where p.post_type = %s and p.post_status = \'publish\'' . 
			' and m.meta_key = %s and cast(m.meta_value as date) = %s', 
			'rideevent', 'start_date', $date), ARRAY_A);
		return $results;
	}

	public static function fetch_ride_mileage($rideid) {
    	global $wpdb;
		$mileage_table = $wpdb->prefix . self::MILEAGE_TABLE;
		$member_table = $wpdb->prefix . self::MEMBER_TABLE;
    	$results = $wpdb->get_results($wpdb->prepare('select' . 
			' c.member_id, c.first_name, c.last_name, m.mileage' . 
			' from ' . $member_table . ' as c inner join ' . $mileage_table . ' as m' . 
			' on c.member_id = m.member_id where m.ride_id = %d', $rideid), ARRAY_A);
		return $results;
	}

	public static function fetch_ride_leaders($rideid) {
    	global $wpdb;
		$leader_table = $wpdb->prefix . self::LEADER_TABLE;
		$member_table = $wpdb->prefix . self::MEMBER_TABLE;
    	$results = $wpdb->get_results($wpdb->prepare('select' . 
			' c.member_id, c.first_name, c.last_name' . 
			' from ' . $member_table . ' as c inner join ' . $leader_table . ' as l' . 
			' on c.member_id = l.member_id where l.ride_id = %d', $rideid), ARRAY_A);
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
		$status = $wpdb->query($wpdb->prepare('insert into ' . $ride_table .
			' (title, date, post_id) values (%s, %s, %d)', $title, $startdate, $postid));
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

	public static function fetch_riders($lastname, $firstname) {
    	global $wpdb;
		$member_table = $wpdb->prefix . self::MEMBER_TABLE;
    	$results = $wpdb->get_results($wpdb->prepare('select * from ' . $member_table . 
			' where first_name like %s and last_name like %s order by last_name, first_name', 
            $firstname . "%", $lastname . "%"), ARRAY_A);
		return $results;
	}

	public static function insert_rider($memberid, $lastname, $firstname) {
    	global $wpdb;
		$member_table = $wpdb->prefix . self::MEMBER_TABLE;
		/*
		$status = $wpdb->query($wpdb->prepare('insert into ' . $member_table .
			' (member_id, last_name, first_name, expir_date) values (%s, %s, %s, curdate())', 
			$memberid, $lastname, $firstname));
		*/
		$status = $wpdb->query($wpdb->prepare('insert into ' . $member_table .
			' (member_id, last_name, first_name, expir_date) values (%s, %s, %s, curdate())' . 
			' on duplicate key update last_name = %s, first_name = %s, expir_date = curdate()',
			$memberid, $lastname, $firstname, $lastname, $firstname));
		return $status;
	}

	public static function delete_rider($memberid) {
    	global $wpdb;
		$member_table = $wpdb->prefix . self::MEMBER_TABLE;
		$status = $wpdb->query($wpdb->prepare('delete from ' . $member_table . 
			' where member_id = %s', $memberid));
		return $status;
	}

/*
	public static function convert_mileage_atts($atts, $tag) {
    	// normalize attribute keys, lowercase
    	$atts = array_change_key_case((array)$atts, CASE_LOWER);
 
    	// override default attributes with user attributes
    	$atts = shortcode_atts([
        	'orderby' => 'mileage',
			'minimum' => '1'], $atts, $tag);

	}
*/

	public static function plugin_activation() {
		error_log( 'PWTC Mileage plugin activated' );
		if ( version_compare( $GLOBALS['wp_version'], PWTC_MILEAGE__MINIMUM_WP_VERSION, '<' ) ) {
			//TODO: Implement version check fail abort
		}
		self::create_db_tables();
		self::create_db_views();
	}

	public static function plugin_deactivation( ) {
		error_log( 'PWTC Mileage plugin deactivated' );
	}

	public static function plugin_uninstall() {
		error_log( 'PWTC Mileage plugin uninstall' );	
		self::drop_db_views();	
		self::drop_db_tables();	
	}

	public static function create_db_tables( ) {
		global $wpdb;
		$member_table = $wpdb->prefix . self::MEMBER_TABLE;
		$ride_table = $wpdb->prefix . self::RIDE_TABLE;
		$mileage_table = $wpdb->prefix . self::MILEAGE_TABLE;
		$leader_table = $wpdb->prefix . self::LEADER_TABLE;
		
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
			' post_id BIGINT UNSIGNED,' . 
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

		$result = $wpdb->query('drop table if exists ' . $leader_table . ', ' . $mileage_table . ', ' . $ride_table . ', ' . $member_table);
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