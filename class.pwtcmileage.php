<?php

class PwtcMileage {
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

		// Register admin menu creation callback
		add_action( 'admin_menu', 
			array( 'PwtcMileage', 'plugin_menu' ) );

		// Register script and style enqueue callbacks
		add_action( 'wp_enqueue_scripts', 
			array( 'PwtcMileage', 'load_report_scripts' ) );
		add_action( 'admin_enqueue_scripts', 
			array( 'PwtcMileage', 'load_admin_scripts' ) );

		// Register shortcode callbacks
		add_shortcode('pwtc_achievement_last_year', 
			array( 'PwtcMileage', 'shortcode_ly_lt_achvmnt'));
		add_shortcode('pwtc_mileage_year_to_date', 
			array( 'PwtcMileage', 'shortcode_ytd_miles'));
		add_shortcode('pwtc_mileage_last_year', 
			array( 'PwtcMileage', 'shortcode_ly_miles'));
		add_shortcode('pwtc_mileage_lifetime', 
			array( 'PwtcMileage', 'shortcode_lt_miles'));
		add_shortcode('pwtc_rides_led_year_to_date', 
			array( 'PwtcMileage', 'shortcode_ytd_led'));
		add_shortcode('pwtc_rides_led_last_year', 
			array( 'PwtcMileage', 'shortcode_ly_led'));
		add_shortcode('pwtc_rides_year_to_date', 
			array( 'PwtcMileage', 'shortcode_ytd_rides'));
		add_shortcode('pwtc_rides_last_year', 
			array( 'PwtcMileage', 'shortcode_ly_rides'));
		add_shortcode('pwtc_led_rides_year_to_date', 
			array( 'PwtcMileage', 'shortcode_ytd_led_rides'));
		add_shortcode('pwtc_led_rides_last_year', 
			array( 'PwtcMileage', 'shortcode_ly_led_rides'));
		add_shortcode('pwtc_posted_rides_wo_sheets', 
			array( 'PwtcMileage', 'shortcode_rides_wo_sheets'));

		// Register ajax callbacks
		add_action( 'wp_ajax_pwtc_mileage_lookup_posts', 
			array( 'PwtcMileage', 'lookup_posts_callback') );
		add_action( 'wp_ajax_pwtc_mileage_lookup_rides', 
			array( 'PwtcMileage', 'lookup_rides_callback') );
		add_action( 'wp_ajax_pwtc_mileage_create_ride', 
			array( 'PwtcMileage', 'create_ride_callback') );
		add_action( 'wp_ajax_pwtc_mileage_create_ride_from_event', 
			array( 'PwtcMileage', 'create_ride_from_event_callback') );
		add_action( 'wp_ajax_pwtc_mileage_remove_ride', 
			array( 'PwtcMileage', 'remove_ride_callback') );
		add_action( 'wp_ajax_pwtc_mileage_lookup_ridesheet', 
			array( 'PwtcMileage', 'lookup_ridesheet_callback') );
		add_action( 'wp_ajax_pwtc_mileage_lookup_riders', 
			array( 'PwtcMileage', 'lookup_riders_callback') );
		add_action( 'wp_ajax_pwtc_mileage_create_rider', 
			array( 'PwtcMileage', 'create_rider_callback') );
		add_action( 'wp_ajax_pwtc_mileage_remove_rider', 
			array( 'PwtcMileage', 'remove_rider_callback') );
		add_action( 'wp_ajax_pwtc_mileage_remove_leader', 
			array( 'PwtcMileage', 'remove_leader_callback') );
		add_action( 'wp_ajax_pwtc_mileage_remove_mileage', 
			array( 'PwtcMileage', 'remove_mileage_callback') );
		add_action( 'wp_ajax_pwtc_mileage_add_leader', 
			array( 'PwtcMileage', 'add_leader_callback') );
		add_action( 'wp_ajax_pwtc_mileage_add_mileage', 
			array( 'PwtcMileage', 'add_mileage_callback') );
		add_action( 'wp_ajax_pwtc_mileage_generate_report', 
			array( 'PwtcMileage', 'generate_report_callback') );

		// Register background action task callbacks 
		add_action( 'pwtc_mileage_consolidation', 
			array( 'PwtcMileage', 'consolidation_callback') );  
		add_action( 'pwtc_mileage_member_sync', 
			array( 'PwtcMileage', 'member_sync_callback') );  
		add_action( 'pwtc_mileage_cvs_restore', 
			array( 'PwtcMileage', 'cvs_restore_callback') );  
	}

	/*************************************************************/
	/* Script and style enqueue callback functions
	/*************************************************************/

	public static function load_report_scripts() {
        wp_enqueue_style('pwtc_mileage_report_css', 
			PWTC_MILEAGE__PLUGIN_URL . 'reports-style.css' );
	}

	public static function load_admin_scripts($hook) {
		if (!strpos($hook, "pwtc_mileage")) {
            return;
        }
        wp_enqueue_style('pwtc_mileage_admin_css', 
			PWTC_MILEAGE__PLUGIN_URL . 'admin-style.css');
        wp_enqueue_style('pwtc_mileage_datepicker_css', 
			PWTC_MILEAGE__PLUGIN_URL . 'datepicker.css');
		wp_enqueue_style('wp-jquery-ui-dialog');
		wp_enqueue_script('jquery-ui-datepicker');   
		wp_enqueue_script('pwtc_mileage_admin_js', 
			PWTC_MILEAGE__PLUGIN_URL . 'admin-scripts.js',
			array('jquery-ui-dialog'), 1.1, true);
		wp_enqueue_script('pwtc_mileage_dateformatter_js', 
			PWTC_MILEAGE__PLUGIN_URL . 'php-date-formatter.min.js', 
			array('jquery'), 1.1, true);
	}

	/*************************************************************/
	/* Ajax callback functions
	/*************************************************************/

	public static function lookup_posts_callback() {
		$posts = self::fetch_posts_without_rides(ARRAY_A);
		$response = array('posts' => $posts);
    	echo wp_json_encode($response);
		wp_die();
	}

	public static function lookup_rides_callback() {
		$startdate = $_POST['startdate'];	
		$enddate = $_POST['enddate'];	
		$title = $_POST['title'];	
		$rides = self::fetch_club_rides($title, $startdate, $enddate);
		$response = array(
			'rides' => $rides);
    	echo wp_json_encode($response);
		wp_die();
	}

	public static function create_ride_callback() {
    	global $wpdb;
		$startdate = $_POST['startdate'];	
		$title = $_POST['title'];	
		$status = self::insert_ride($title, $startdate);
		if (false === $status or 0 === $status) {
			$response = array(
				'error' => 'Could not insert ridesheet into database.'
			);
    		echo wp_json_encode($response);
		}
		else {
			$ride_id = $wpdb->insert_id;
			$leaders = self::fetch_ride_leaders($ride_id);
			$mileage = self::fetch_ride_mileage($ride_id);
			$response = array(
				'ride_id' => $ride_id,
				'title' => $title,
				'startdate' => $startdate, 
				'leaders' => $leaders,
				'mileage' => $mileage);
    		echo wp_json_encode($response);
		}
		wp_die();
	}

	public static function create_ride_from_event_callback() {
    	global $wpdb;
		$startdate = $_POST['startdate'];	
		$title = $_POST['title'];	
		$postid = $_POST['post_id'];	
		$status = self::insert_ride_with_postid($title, $startdate, intval($postid));
		if (false === $status or 0 === $status) {
			$response = array(
				'error' => 'Could not insert ridesheet into database.'
			);
    		echo wp_json_encode($response);
		}
		else {
			$ride_id = $wpdb->insert_id;
			$leaders = self::fetch_ride_leaders($ride_id);
			$mileage = self::fetch_ride_mileage($ride_id);
			$response = array(
				'ride_id' => $ride_id,
				'title' => $title,
				'startdate' => $startdate, 
				'leaders' => $leaders,
				'mileage' => $mileage);
    		echo wp_json_encode($response);
		}
		wp_die();
	}

	public static function remove_ride_callback() {
		$startdate = $_POST['startdate'];
		$enddate = $_POST['enddate'];	
		$title = $_POST['title'];	
		$rideid = $_POST['ride_id'];
		$mcnt = self::fetch_ride_has_mileage(intval($rideid));
		$lcnt = self::fetch_ride_has_leaders(intval($rideid));
		if ($mcnt > 0 or $lcnt > 0) {
			$response = array(
				'error' => 'Cannot delete a ridesheet that has riders.'
			);
    		echo wp_json_encode($response);
		}
		else {
			$status = self::delete_ride(intval($rideid));
			if (false === $status or 0 === $status) {
				$response = array(
					'error' => 'Could not delete ridesheet from database.'
				);
    			echo wp_json_encode($response);
			}
			else {
				$rides = self::fetch_club_rides($title, $startdate, $enddate);
				$response = array(
					'rides' => $rides);
    			echo wp_json_encode($response);
			}
		}
		wp_die();	
	}

	public static function lookup_ridesheet_callback() {
		$rideid = $_POST['ride_id'];
		$startdate = $_POST['startdate'];
		$title = $_POST['title'];
		$leaders = self::fetch_ride_leaders(intval($rideid));
		$mileage = self::fetch_ride_mileage(intval($rideid));
		$response = array(
			'startdate' => $startdate,
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
		$memberid = '';
    	if (isset($_POST['memberid'])) {
			$memberid = $_POST['memberid'];
		}
		$members = self::fetch_riders($lastname, $firstname, $memberid);	
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
		$expdate = $_POST['exp_date'];
		$mode = $_POST['mode'];
		$lookupfirst = '';
		$lookuplast = strtolower(substr($lastname, 0, 1));
		$no_overwrite = false;
		if ($mode == 'insert') {
			if (count(self::fetch_rider($memberid)) > 0) {
				$no_overwrite = true;
			}
		}
		if ($no_overwrite) {
			$response = array(
				'error' => 'Member ID ' . $memberid . ' already exists.'
			);
    		echo wp_json_encode($response);
		}
		else {
			$status = self::insert_rider($memberid, $lastname, $firstname, $expdate);	
			if (false === $status or 0 === $status) {
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
				'error' => 'Cannot delete a rider that is entered on a ridesheet.'
			);
    		echo wp_json_encode($response);
		}
		else {
			$status = self::delete_rider($memberid);	
			if (false === $status or 0 === $status) {
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
		if (false === $status or 0 === $status) {
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
		if (false === $status or 0 === $status) {
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

	//TODO: Disable this check if option is set.
	public static function check_expir_date($memberid) {
		$rider = self::fetch_rider($memberid);
		$errormsg = null;
		if (count($rider) > 0) {
			$r = $rider[0];
			if (strtotime($r['expir_date']) < strtotime(date('Y-m-d', current_time('timestamp')))) {
				$errormsg = 'The membership of ' . $r['first_name'] . ' ' . $r['last_name'] .
					' (' . $r['member_id'] . ') has expired.';
			}
		}
		else {
			$errormsg = 'Could not find rider ' + $memberid . ' in database.';
		}
		return $errormsg;
	}

	public static function add_leader_callback() {
		$rideid = $_POST['ride_id'];
		$memberid = $_POST['member_id'];
		$error = self::check_expir_date($memberid);
		if ($error != null) {
			$response = array(
				'error' => $error
			);
    		echo wp_json_encode($response);
		}
		else {
			$status = self::insert_ride_leader(intval($rideid), $memberid);
			if (false === $status or 0 === $status) {
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
		}
		wp_die();
	}

	public static function add_mileage_callback() {
		$rideid = $_POST['ride_id'];
		$memberid = $_POST['member_id'];
		$mileage = $_POST['mileage'];
		$error = self::check_expir_date($memberid);
		if ($error != null) {
			$response = array(
				'error' => $error
			);
    		echo wp_json_encode($response);
		}
		else {
			$status = self::insert_ride_mileage(intval($rideid), $memberid, intval($mileage));
			if (false === $status or 0 === $status) {
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
		}
		wp_die();
	}

	public static function generate_report_callback() {
		$reportid = $_POST['report_id'];
		$plugin_options = self::get_plugin_options();
		$error = null;
		$data = array();
		$meta = null;
		switch ($reportid) {
			case "ytd_miles":
			case "ly_miles":
			case "lt_miles":
			case "ly_lt_achvmnt":
				$sort = $_POST['sort'];
				switch ($reportid) {			
					case "ytd_miles":
						$meta = self::meta_ytd_miles();
						$data = self::fetch_ytd_miles(ARRAY_N, $sort);
						break;
					case "ly_miles":
						$meta = self::meta_ly_miles();
						$data = self::fetch_ly_miles(ARRAY_N, $sort);
						break;
					case "lt_miles":
						$meta = self::meta_lt_miles();
						$data = self::fetch_lt_miles(ARRAY_N, $sort);
						break;
					case "ly_lt_achvmnt":
						$meta = self::meta_ly_lt_achvmnt();
						$data = self::fetch_ly_lt_achvmnt(ARRAY_N, $sort);
						break;
				}
				break;
			case "ytd_led":
			case "ly_led":
				$sort = $_POST['sort'];
				$sort = $_POST['sort'];
				switch ($reportid) {			
					case "ytd_led":
						$meta = self::meta_ytd_led();
						$data = self::fetch_ytd_led(ARRAY_N, $sort);
						break;
					case "ly_led":
						$meta = self::meta_ly_led();
						$data = self::fetch_ly_led(ARRAY_N, $sort);
						break;
				}
				break;
			case "ytd_rides":
			case "ly_rides":
			case "ytd_rides_led":
			case "ly_rides_led":
				$memberid = $_POST['member_id'];
				$name = $_POST['name'];
				switch ($reportid) {			
					case "ytd_rides":
						$meta = self::meta_ytd_rides($name);
						$data = self::fetch_ytd_rides(ARRAY_N, $memberid);
						break;
					case "ly_rides":
						$meta = self::meta_ly_rides($name);
						$data = self::fetch_ly_rides(ARRAY_N, $memberid);
						break;
					case "ytd_rides_led":
						$meta = self::meta_ytd_rides_led($name);
						$data = self::fetch_ytd_rides_led(ARRAY_N, $memberid);
						break;
					case "ly_rides_led":
						$meta = self::meta_ly_rides_led($name);
						$data = self::fetch_ly_rides_led(ARRAY_N, $memberid);
						break;
				}
				break;
			default:
				$error = 'Report type ' . $reportid . ' not found.';
		}
		if (null === $error) {
			if ($meta['date_idx'] >= 0) {
				$i = $meta['date_idx'];
				foreach( $data as $key => $row ):
					$data[$key][$i] = date($plugin_options['date_display_format'], strtotime($row[$i]));
				endforeach;					
			}
			$response = array(
				'title' => $meta['title'],
				'header' => $meta['header'],
				'data' => $data
			);
			echo wp_json_encode($response);			
		}
		else {
			$response = array(
				'error' => $error
			);
			echo wp_json_encode($response);
		}
		wp_die();
	}

	/*************************************************************/
	/* Background action task callbacks
	/*************************************************************/

	public static function consolidation_callback() {
    	global $wpdb;
		error_log( 'Consolidation process started.');
		self::job_set_status('consolidation', 'started');

		$thisyear = date('Y', current_time('timestamp'));
    	$yearbeforelast = intval($thisyear) - 2;
		$title = 'Totals Through ' . $yearbeforelast;
		$maxdate = '' . $yearbeforelast . '-12-31';

		$ride_table = $wpdb->prefix . self::RIDE_TABLE;
		$num_rides = $wpdb->get_var($wpdb->prepare('select count(*) from ' . $ride_table . 
			' where date <= %s', $maxdate));
		if ($num_rides == 0) {
			self::job_set_status('consolidation', 'failed', 
				'no ridesheets were found for ' . $yearbeforelast);
		}
		else if ($num_rides == 1) {
			self::job_set_status('consolidation', 'failed', 
				'' . $yearbeforelast . ' ridesheets are already consolidated');
		}
		else {
			$status = self::insert_ride($title, $maxdate);
			if (false === $status or 0 === $status) {
				self::job_set_status('consolidation', 'failed', 'could not insert new ridesheet');
			}
			else {
				$rideid = $wpdb->insert_id;
				if (isset($rideid) and is_int($rideid)) {
					$status = self::rollup_ridesheets($rideid, $maxdate);
					error_log(var_export($status, true));
					self::job_remove('consolidation');
				}
				else {
					self::job_set_status('consolidation', 'failed', 'new ridesheet ID is invalid');
				}
			}
		}	
	}

	public static function write_export_csv_file($fp, $data) {
		foreach ($data as $item) {
    		fputcsv($fp, $item);
		}		
	}

	public static function read_export_csv_file($fp) {
		$data = array();
		// TODO: handle blank lines in csv file
		while (($item = fgetcsv($fp)) !== FALSE) {
			array_push($data, $item);
		}
		return $data;		
	}

	public static function member_sync_callback() {
		error_log( 'Membership Sync process started.');
		self::job_set_status('member_sync', 'started');
		$members = pwtc_mileage_fetch_membership();
		if (count($members) == 0) {
			self::job_set_status('member_sync', 'failed', 'no members in membership list');
		}
		else {
    		foreach ( $members as $item ) {
       			$memberid = $item[0];
				$firstname = $item[1];
        		$lastname = $item[2];
        		$expirdate = $item[3];
				$status = self::insert_rider($memberid, $lastname, $firstname, $expirdate);	
				if (false === $status or 0 === $status) {
					error_log('Could not insert or update ' . $memberid);
				}
			}
			self::job_remove('member_sync');	
		}
	}

	public static function cvs_restore_callback() {
		error_log( 'CVS restore process started.');
		self::job_set_status('cvs_restore', 'started');
		$upload_dir = wp_upload_dir();
		$plugin_upload_dir = $upload_dir['basedir'] . '/pwtc_mileage';
		$members_file = $plugin_upload_dir . '/' . self::MEMBER_TABLE . '.csv';
		$rides_file = $plugin_upload_dir . '/' . self::RIDE_TABLE . '.csv';
		$mileage_file = $plugin_upload_dir . '/' . self::MILEAGE_TABLE . '.csv';
		$leaders_file = $plugin_upload_dir . '/' . self::LEADER_TABLE . '.csv';
		if (!file_exists($members_file)) {
			self::job_set_status('cvs_restore', 'failed', 'members upload file does not exist');
		}
		else if (!file_exists($rides_file)) {
			self::job_set_status('cvs_restore', 'failed', 'rides upload file does not exist');
		}
		else if (!file_exists($mileage_file)) {
			self::job_set_status('cvs_restore', 'failed', 'mileage upload file does not exist');
		}
		else if (!file_exists($leaders_file)) {
			self::job_set_status('cvs_restore', 'failed', 'leaders upload file does not exist');
		}
		else {
			$use_load = true;
			if ($use_load) {
				$plugin_upload_url = $upload_dir['baseurl'] . '/pwtc_mileage';
				$members_url = $plugin_upload_url . '/' . self::MEMBER_TABLE . '.csv';
				$rides_url = $plugin_upload_url . '/' . self::RIDE_TABLE . '.csv';
				$mileage_url = $plugin_upload_url . '/' . self::MILEAGE_TABLE . '.csv';
				$leaders_url = $plugin_upload_url . '/' . self::LEADER_TABLE . '.csv';
				error_log('members_url: ' . $members_url);
				error_log('rides_url: ' . $rides_url);
				error_log('mileage_url: ' . $mileage_url);
				error_log('leaders_url: ' . $leaders_url);

				self::delete_database_for_restore();
				self::load_members_for_restore($members_url);
				self::load_rides_for_restore($rides_url);
				self::load_mileage_for_restore($mileage_url);
				self::load_leaders_for_restore($leaders_url);

				unlink($members_file);
				unlink($rides_file);
				unlink($mileage_file);
				unlink($leaders_file);

				self::job_remove('cvs_restore');
			}
			else {
				$members_a = null;
				$members_fc = 0;
				if (($fp = fopen($members_file, 'r')) !== FALSE) {
					$members_a = self::read_export_csv_file($fp);
					error_log( 'members count: ' . count($members_a));
					$members_fc = self::field_count_check($members_a, 4);	
					fclose($fp);
				}
				$rides_a = null;
				$rides_fc = 0;
				if (($fp = fopen($rides_file, 'r')) !== FALSE) {
					$rides_a = self::read_export_csv_file($fp);
					error_log( 'rides count: ' . count($rides_a));	
					$rides_fc = self::field_count_check($rides_a, 4);	
					fclose($fp);
				}
				$mileage_a = null;
				$mileage_fc = 0;
				if (($fp = fopen($mileage_file, 'r')) !== FALSE) {
					$mileage_a = self::read_export_csv_file($fp);
					error_log( 'mileage count: ' . count($mileage_a));	
					$mileage_fc = self::field_count_check($mileage_a, 3);	
					fclose($fp);
				}
				$leaders_a = null;
				$leaders_fc = 0;
				if (($fp = fopen($leaders_file, 'r')) !== FALSE) {
					$leaders_a = self::read_export_csv_file($fp);
					error_log( 'leaders count: ' . count($leaders_a));	
					$leaders_fc = self::field_count_check($leaders_a, 3);	
					fclose($fp);
				}

				if ($members_a == null) {
					self::job_set_status('cvs_restore', 'failed', 
						'members upload file cannot be opened for read');
				}
				else if ($members_fc > 0) {
					self::job_set_status('cvs_restore', 'failed', 
						'members upload file contains invalid entries');
				}
				else if ($rides_a == null) {
					self::job_set_status('cvs_restore', 'failed', 
						'rides upload file cannot be opened for read');
				}
				else if ($rides_fc > 0) {
					self::job_set_status('cvs_restore', 'failed', 
						'rides upload file contains invalid entries');
				}
				else if ($mileage_a == null) {
					self::job_set_status('cvs_restore', 'failed', 
						'mileage upload file cannot be opened for read');
				}
				else if ($mileage_fc > 0) {
					self::job_set_status('cvs_restore', 'failed', 
						'mileage upload file contains invalid entries');
				}
				else if ($leaders_a == null) {
					self::job_set_status('cvs_restore', 'failed', 
						'leaders upload file cannot be opened for read');
				}
					else if ($leaders_fc > 0) {
					self::job_set_status('cvs_restore', 'failed', 
						'leaders upload file contains invalid entries');
				}
				else {
					$errs_detected = false;
				
					self::delete_database_for_restore();
				
					if (self::insert_members_for_restore($members_a) > 0) {
						self::job_set_status('cvs_restore', 'failed', 
							'members table insert errors detected');
						$errs_detected = true;
					}
					error_log('after insert members');
				
					if (self::insert_rides_for_restore($rides_a) > 0) {
						self::job_set_status('cvs_restore', 'failed', 
							'rides table insert errors detected');
						$errs_detected = true;
					}
				
					if (self::insert_mileage_for_restore($mileage_a) > 0) {
						self::job_set_status('cvs_restore', 'failed', 
							'mileage table insert errors detected');
						$errs_detected = true;
					}

					if (self::insert_leaders_for_restore($leaders_a) > 0) {
						self::job_set_status('cvs_restore', 'failed', 
							'leaders table insert errors detected');
						$errs_detected = true;
					}

					unlink($members_file);
					unlink($rides_file);
					unlink($mileage_file);
					unlink($leaders_file);

					if (!$errs_detected) {
						self::job_remove('cvs_restore');
					}
				}
			}
		}	
	}

	public static function field_count_check($data, $num) {
		$count = 0;
		foreach ( $data as $item ) {	
			if (count($item) != $num) {
				$count++;
			}	
		}
		return $count;
	}

	/*************************************************************/
	/* Admin menu and pages creation functions
	/*************************************************************/

	public static function plugin_menu() {
		$plugin_options = self::get_plugin_options();

    	$page_title = $plugin_options['plugin_menu_label'];
    	$menu_title = $plugin_options['plugin_menu_label'];
    	$capability = 'manage_options';
    	$parent_menu_slug = 'pwtc_mileage_menu';
    	$function = array( 'PwtcMileage', 'plugin_menu_page');
    	$icon_url = '';
    	$position = $plugin_options['plugin_menu_location'];
		add_menu_page($page_title, $menu_title, $capability, $parent_menu_slug, $function, $icon_url, $position);

    	$page_title = 'View Reports';
    	$menu_title = 'View Reports';
    	$menu_slug = 'pwtc_mileage_generate_reports';
    	$capability = 'edit_posts';
    	$function = array( 'PwtcMileage', 'page_generate_reports');
		add_submenu_page($parent_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);

    	$page_title = 'Manage Riders';
    	$menu_title = 'Manage Riders';
    	$menu_slug = 'pwtc_mileage_manage_riders';
    	$capability = 'edit_published_pages';
    	$function = array( 'PwtcMileage', 'page_manage_riders');
		add_submenu_page($parent_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);

    	$page_title = 'Manage Ride Sheets';
    	$menu_title = 'Manage Ride Sheets';
    	$menu_slug = 'pwtc_mileage_manage_ride_sheets';
    	$capability = 'edit_published_pages';
    	$function = array( 'PwtcMileage', 'page_manage_ride_sheets');
		add_submenu_page($parent_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);

    	$page_title = 'Database Operations';
    	$menu_title = 'Database Ops';
    	$menu_slug = 'pwtc_mileage_manage_year_end';
    	$capability = 'manage_options';
    	$function = array( 'PwtcMileage', 'page_manage_year_end');
		$page = add_submenu_page($parent_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
		add_action('load-' . $page, array('PwtcMileage','download_csv'));

		remove_submenu_page($parent_menu_slug, $parent_menu_slug);

		$page_title = 'Plugin Settings';
    	$menu_title = 'Settings';
    	$menu_slug = 'pwtc_mileage_settings';
    	$capability = 'manage_options';
    	$function = array( 'PwtcMileage', 'page_manage_settings');
		add_submenu_page($parent_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
	}

	public static function plugin_menu_page() {
	}

	public static function page_manage_ride_sheets() {
		$plugin_options = self::get_plugin_options();
		$running_jobs = self::num_running_jobs();
		include('admin-man-ridesheets.php');
	}

	public static function page_generate_reports() {
		$plugin_options = self::get_plugin_options();
		$running_jobs = self::num_running_jobs();
		include('admin-gen-reports.php');
	}

	public static function page_manage_riders() {
		$plugin_options = self::get_plugin_options();
		$running_jobs = self::num_running_jobs();
		include('admin-man-riders.php');
	}

	public static function download_csv() {
		if (isset($_POST['export_members'])) {
			$today = date('Y-m-d', current_time('timestamp'));
			header('Content-Description: File Transfer');
			header("Content-type: text/csv");
			header("Content-Disposition: attachment; filename=members_{$today}.csv");
			$fh = fopen('php://output', 'w');
			self::write_export_csv_file($fh, self::fetch_members_for_export());
			fclose($fh);
			die;
		}
		else if (isset($_POST['export_rides'])) {
			$today = date('Y-m-d', current_time('timestamp'));
			header('Content-Description: File Transfer');
			header("Content-type: text/csv");
			header("Content-Disposition: attachment; filename=rides_{$today}.csv");
			$fh = fopen('php://output', 'w');
			self::write_export_csv_file($fh, self::fetch_rides_for_export());
			fclose($fh);
			die;
		}
		else if (isset($_POST['export_mileage'])) {
			$today = date('Y-m-d', current_time('timestamp'));
			header('Content-Description: File Transfer');
			header("Content-type: text/csv");
			header("Content-Disposition: attachment; filename=mileage_{$today}.csv");
			$fh = fopen('php://output', 'w');
			self::write_export_csv_file($fh, self::fetch_mileage_for_export());
			fclose($fh);
			die;
		}
		else if (isset($_POST['export_leaders'])) {
			$today = date('Y-m-d', current_time('timestamp'));
			header('Content-Description: File Transfer');
			header("Content-type: text/csv");
			header("Content-Disposition: attachment; filename=leaders_{$today}.csv");
			$fh = fopen('php://output', 'w');
			self::write_export_csv_file($fh, self::fetch_leaders_for_export());
			fclose($fh);
			die;
		}
	}

	public static function page_manage_year_end() {
		$plugin_options = self::get_plugin_options();
    	if (isset($_POST['consolidate'])) {
			self::job_set_status('consolidation', 'triggered');
			wp_schedule_single_event(time(), 'pwtc_mileage_consolidation');
		}

    	if (isset($_POST['member_sync'])) {
			self::job_set_status('member_sync', 'triggered');
			wp_schedule_single_event(time(), 'pwtc_mileage_member_sync');
		}
		if (isset($_POST['restore'])) {
			self::job_set_status('cvs_restore', 'triggered');
			$files = array(
				self::generate_file_record(
					'members_file', 'members', 'members_', self::MEMBER_TABLE),
				self::generate_file_record(
					'rides_file', 'rides', 'rides_', self::RIDE_TABLE),
				self::generate_file_record(
					'mileage_file', 'mileage', 'mileage_', self::MILEAGE_TABLE),
				self::generate_file_record(
					'leaders_file', 'leaders', 'leaders_', self::LEADER_TABLE)
			);
			$error = self::validate_uploaded_files($files);
			if ($error) {
				self::job_set_status('cvs_restore', 'failed', $error);
			}
			else {
				$error = self::move_uploaded_files($files);
				if ($error) {
					self::job_set_status('cvs_restore', 'failed', $error);
				}
				else {
					wp_schedule_single_event(time(), 'pwtc_mileage_cvs_restore');
				}
			}
		}
    	if (isset($_POST['clear_errs'])) {
			self::job_remove_failed();
		}
		$job_status_s = self::job_get_status('member_sync');
		$job_status_b = self::job_get_status('backup');
		$job_status_c = self::job_get_status('consolidation');
		$job_status_r = self::job_get_status('cvs_restore');
		include('admin-man-yearend.php');
	}

	public static function generate_file_record($id, $label, $prefix, $tblname) {
		return array(
			'id' => $id,
			'label' => $label,
			'pattern' => '/' . $prefix . '\d{4}-\d{2}-\d{2}\.csv' . '/',
			'tblname' => $tblname
		);
	}

	public static function validate_uploaded_files($files) {
		$errmsg = null;
		// TODO: validate that $_FILES[$file['id']] exists
    	foreach ( $files as $file ) {
			if ($_FILES[$file['id']]['size'] == 0) {
				$errmsg = $file['label'] . ' file empty or not selected';
				break;
			}
			else if ($_FILES[$file['id']]['error'] != UPLOAD_ERR_OK) {
				$errmsg = $file['label'] . ' file upload error code ' . $_FILES[$file]['error'];
				break;
			}
			else if (preg_match($file['pattern'], $_FILES[$file['id']]['name']) !== 1) {
				$errmsg = $file['label'] . ' file name pattern mismatch';
				break;
			}
		}

		return $errmsg;
	}

	public static function move_uploaded_files($files) {
		$errmsg = null;
		$upload_dir = wp_upload_dir();
		$plugin_upload_dir = $upload_dir['basedir'] . '/pwtc_mileage';
		//error_log('plugin_upload_dir: ' . $plugin_upload_dir);
		if (!file_exists($plugin_upload_dir)) {
    		wp_mkdir_p($plugin_upload_dir);
		}
		foreach ( $files as $file ) {
			$uploadfile = $plugin_upload_dir . '/' . $file['tblname'] . '.csv';
			//error_log('moved file: ' . $uploadfile);
			if (!move_uploaded_file($_FILES[$file['id']]['tmp_name'], $uploadfile)) {
				$errmsg = $file['label'] . ' file upload could not be moved';
				break;
			}
		}
		return $errmsg;
	}

	public static function page_manage_settings() {
		$plugin_options = self::get_plugin_options();
		$form_submitted = false;
    	if (isset($_POST['ride_post_type'])) {
			$plugin_options['ride_post_type'] = $_POST['ride_post_type'];
			$form_submitted = true;
    	} 
    	if (isset($_POST['ride_date_metakey'])) {
			$plugin_options['ride_date_metakey'] = $_POST['ride_date_metakey'];
			$form_submitted = true;
    	} 
    	if (isset($_POST['ride_date_format'])) {
			$plugin_options['ride_date_format'] = $_POST['ride_date_format'];
			$form_submitted = true;
    	} 
    	if (isset($_POST['date_display_format'])) {
			$plugin_options['date_display_format'] = $_POST['date_display_format'];
 			$form_submitted = true;
	   	} 
    	if (isset($_POST['db_backup_location'])) {
			$plugin_options['db_backup_location'] = $_POST['db_backup_location'];
			$form_submitted = true;
    	} 
    	if (isset($_POST['plugin_menu_label'])) {
			$plugin_options['plugin_menu_label'] = $_POST['plugin_menu_label'];
			$form_submitted = true;
    	} 
    	if (isset($_POST['plugin_menu_location'])) {
			$plugin_options['plugin_menu_location'] = intval($_POST['plugin_menu_location']);
			$form_submitted = true;
    	} 
		if ($form_submitted) {
			if (isset($_POST['drop_db_on_delete'])) {
				$plugin_options['drop_db_on_delete'] = true;
			}
			else {
				$plugin_options['drop_db_on_delete'] = false;
			}
			self::update_plugin_options($plugin_options);
			$plugin_options = self::get_plugin_options();			
		}
		include('admin-man-settings.php');
	}

	/*************************************************************/
	/* Shortcode utility functions
	/*************************************************************/

	public static function get_rider_name($id) {
		$rider = self::fetch_rider($id);
		$name = '';
		if (count($rider) > 0) {
			$r = $rider[0];
			$name = $r['first_name'] . ' ' . $r['last_name'];
		}
		else {
			$name = $id;
		}
		return $name;
	}

	public static function shortcode_build_errmsg($errmsg) {
		$out = '<div class="pwtc-mileage-report"><div class="report-caption">';
		$out .= $errmsg;
		$out .= '</div></div>';
		return $out;	
	}

	public static function shortcode_build_table($meta, $data, $atts) {
		$plugin_options = self::get_plugin_options();
		$hide_id = true;
		if ($atts['show_id'] == 'on') {
			$hide_id = false;
		}
		$id = null;
		if ($meta['id_idx'] >= 0 and $atts['highlight_user'] == 'on') {
			$id = pwtc_mileage_get_member_id();
		}
		$out = '<div class="pwtc-mileage-report">';
		if ($atts['caption'] == 'on') {
			$out .= '<div class="report-caption">' . $meta['title'] . '</div>';
		}
		$out .= '<table><tr>';
		$i = 0;
		foreach( $meta['header'] as $item ):
			if ($meta['id_idx'] === $i) {
				if (!$hide_id) {
					$out .= '<th>' . $item . '</th>';						
				}
			} 
			else {
				$out .= '<th>' . $item . '</th>';
			}
			$i++;
		endforeach;	
		$out .= '</tr>';
		foreach( $data as $row ):
			$outrow = '';
			$i = 0;
			$highlight = false;
			foreach( $row as $item ):
				if ($meta['date_idx'] == $i) {
					$fmtdate = date($plugin_options['date_display_format'], strtotime($item));
					$outrow .= '<td>' . $fmtdate . '</td>';
				}
				else if ($meta['id_idx'] === $i) {
					if ($id !== null and $id == $item) {
						$highlight = true;
					}
					if (!$hide_id) {
						$outrow .= '<td>' . $item . '</td>';						
					}
				}
				else {
					$outrow .= '<td>' . $item . '</td>';
				}
				$i++;
			endforeach;	
			if ($highlight) {
				$out .= '<tr class="highlight">' . $outrow . '</tr>';
			}
			else {
				$out .= '<tr>' . $outrow . '</tr>';
			}
		endforeach;
		$out .= '</table></div>';
		return $out;
	}

	public static function normalize_atts($atts) {
    	$a = shortcode_atts(array(
        		'show_id' => 'off',
       			'highlight_user' => 'on',
				'sort_by' => 'off',
				'sort_order' => 'asc',
				'minimum' => 0,
				'caption' => 'on'
			), $atts);
		return $a;
	}

	public static function build_mileage_sort($atts) {
		$order = 'asc';
		if ($atts['sort_order'] == 'desc') {
			$order = 'desc';
		}
		$sort = 'mileage ' . $order;
		if ($atts['sort_by'] == 'name') {
			$sort = 'last_name ' . $order . ', first_name ' . $order;
		}
		return $sort;
	}

	public static function build_rides_led_sort($atts) {
		$order = 'asc';
		if ($atts['sort_order'] == 'desc') {
			$order = 'desc';
		}
		$sort = 'rides_led ' . $order;
		if ($atts['sort_by'] == 'name') {
			$sort = 'last_name ' . $order . ', first_name ' . $order;
		}
		return $sort;
	}

	public static function get_minimum_val($atts) {
		$min = 0;
		if ($atts['minimum'] > 0) {
			$min = $atts['minimum'];
		}
		return $min;
	}

	/*************************************************************/
	/* Shortcode report generation functions
	/*************************************************************/

	public static function shortcode_ly_lt_achvmnt($atts) {
		$a = self::normalize_atts($atts);
		$sort = self::build_mileage_sort($a);
		$meta = self::meta_ly_lt_achvmnt();
		$data = self::fetch_ly_lt_achvmnt(ARRAY_N, $sort);
		$out = self::shortcode_build_table($meta, $data, $a);
		return $out;
	}

	public static function shortcode_ytd_miles($atts) {
		$a = self::normalize_atts($atts);
		$sort = self::build_mileage_sort($a);
		$min = self::get_minimum_val($a);
		$meta = self::meta_ytd_miles();
		$data = self::fetch_ytd_miles(ARRAY_N, $sort, $min);
		$out = self::shortcode_build_table($meta, $data, $a);
		return $out;
	}

	public static function shortcode_ly_miles($atts) {
		$a = self::normalize_atts($atts);
		$sort = self::build_mileage_sort($a);
		$min = self::get_minimum_val($a);
		$meta = self::meta_ly_miles();
		$data = self::fetch_ly_miles(ARRAY_N, $sort, $min);
		$out = self::shortcode_build_table($meta, $data, $a);
		return $out;
	}

	public static function shortcode_lt_miles($atts) {
		$a = self::normalize_atts($atts);
		$sort = self::build_mileage_sort($a);
		$min = self::get_minimum_val($a);
		$meta = self::meta_lt_miles();
		$data = self::fetch_lt_miles(ARRAY_N, $sort, $min);
		$out = self::shortcode_build_table($meta, $data, $a);
		return $out;
	}

	public static function shortcode_ytd_led($atts) {
		$a = self::normalize_atts($atts);
		$sort = self::build_rides_led_sort($a);
		$min = self::get_minimum_val($a);
		$meta = self::meta_ytd_led();
		$data = self::fetch_ytd_led(ARRAY_N, $sort, $min);
		$out = self::shortcode_build_table($meta, $data, $a);
		return $out;
	}

	public static function shortcode_ly_led($atts) {
		$a = self::normalize_atts($atts);
		$sort = self::build_rides_led_sort($a);
		$min = self::get_minimum_val($a);
		$meta = self::meta_ly_led();
		$data = self::fetch_ly_led(ARRAY_N, $sort, $min);
		$out = self::shortcode_build_table($meta, $data, $a);
		return $out;
	}

	public static function shortcode_ytd_rides($atts) {
		$member_id = pwtc_mileage_get_member_id();
		$out = '';
		if ($member_id === null) {
			$out = self::shortcode_build_errmsg('This report requires a valid logged in rider!');
		}
		else {
			$name = self::get_rider_name($member_id);
			$a = self::normalize_atts($atts);
			$meta = self::meta_ytd_rides($name);
			$data = self::fetch_ytd_rides(ARRAY_N, $member_id);
			$out = self::shortcode_build_table($meta, $data, $a);
		}
		return $out;
	}

	public static function shortcode_ly_rides($atts) {
		$member_id = pwtc_mileage_get_member_id();
		$out = '';
		if ($member_id === null) {
			$out = self::shortcode_build_errmsg('This report requires a valid logged in rider!');
		}
		else {
			$name = self::get_rider_name($member_id);
			$a = self::normalize_atts($atts);
			$meta = self::meta_ly_rides($name);
			$data = self::fetch_ly_rides(ARRAY_N, $member_id);
			$out = self::shortcode_build_table($meta, $data, $a);
		}
		return $out;
	}

	public static function shortcode_ytd_led_rides($atts) {
		$member_id = pwtc_mileage_get_member_id();
		$out = '';
		if ($member_id === null) {
			$out = self::shortcode_build_errmsg('This report requires a valid logged in rider!');
		}
		else {
			$name = self::get_rider_name($member_id);
			$a = self::normalize_atts($atts);
			$meta = self::meta_ytd_rides_led($name);
			$data = self::fetch_ytd_rides_led(ARRAY_N, $member_id);
			$out = self::shortcode_build_table($meta, $data, $a);
		}
		return $out;
	}

	public static function shortcode_ly_led_rides($atts) {
		$member_id = pwtc_mileage_get_member_id();
		if ($member_id === null) {
			$out = self::shortcode_build_errmsg('This report requires a valid logged in rider!');
		}
		else {
			$name = self::get_rider_name($member_id);
			$a = self::normalize_atts($atts);
			$meta = self::meta_ly_rides_led($name);
			$data = self::fetch_ly_rides_led(ARRAY_N, $member_id);
			$out = self::shortcode_build_table($meta, $data, $a);
			return $out;
		}
	}

	public static function shortcode_rides_wo_sheets($atts) {
		$a = self::normalize_atts($atts);
		$meta = self::meta_posts_without_rides();
		$data = self::fetch_posts_without_rides(ARRAY_N);
		$out = self::shortcode_build_table($meta, $data, $a);
		return $out;
	}

	/*************************************************************/
	/* Database access functions
	/*************************************************************/

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

	public static function fetch_posts_without_rides($outtype) {
    	global $wpdb;
		$plugin_options = self::get_plugin_options();
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

	public static function meta_posts_without_rides() {
		$meta = array(
			'header' => array('Ride ID', 'Title', 'Start Date'),
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
		$status = $wpdb->query($wpdb->prepare('insert into ' . $jobs_table .
			' (job_id, status, timestamp, error_msg) values (%s, %s, now(), %s)' . 
			' on duplicate key update status = %s, timestamp = now(), error_msg = %s',
			$jobid, $status, $errmsg, $status, $errmsg));
		return $status;
	}

	public static function num_running_jobs() {
    	global $wpdb;
		$jobs_table = $wpdb->prefix . self::JOBS_TABLE;
		$results = $wpdb->get_var('select count(*) from ' . $jobs_table . 
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

	/*************************************************************/
	/* Plugin options access functions
	/*************************************************************/

	public static function create_default_plugin_options() {
		$data = array(
			'ride_post_type' => 'ride',
			'ride_date_metakey' => 'date',
			'ride_date_format' => 'Y-m-d',
			'date_display_format' => 'D M j, Y',
			'drop_db_on_delete' => false,
			'db_backup_location' => '',
			'plugin_menu_label' => 'Rider Mileage',
			'plugin_menu_location' => 50);
		add_option('pwtc_mileage_options', $data);
	}

	public static function get_plugin_options() {
		return get_option('pwtc_mileage_options');
	}

	public static function delete_plugin_options() {
		delete_option('pwtc_mileage_options');
	}

	public static function update_plugin_options($data) {
		update_option('pwtc_mileage_options', $data);
	}

	/*************************************************************/
	/* Plugin installation and removal functions
	/*************************************************************/

	public static function plugin_activation() {
		error_log( 'PWTC Mileage plugin activated' );
		if ( version_compare( $GLOBALS['wp_version'], PWTC_MILEAGE__MINIMUM_WP_VERSION, '<' ) ) {
			//TODO: Implement version check fail abort
		}
		self::create_db_tables();
		self::create_db_views();
		if (self::get_plugin_options() === false) {
			self::create_default_plugin_options();
		}
	}

	public static function plugin_deactivation( ) {
		error_log( 'PWTC Mileage plugin deactivated' );
	}

	public static function plugin_uninstall() {
		error_log( 'PWTC Mileage plugin uninstall' );	
		$plugin_options = self::get_plugin_options();
		if ($plugin_options['drop_db_on_delete']) {
			self::drop_db_views();	
			self::drop_db_tables();				
		}
		self::delete_plugin_options();
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
			' timestamp DATETIME NOT NULL,' . 
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