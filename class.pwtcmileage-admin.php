<?php

class PwtcMileage_Admin {

    private static $initiated = false;

	public static function init() {
		if ( ! self::$initiated ) {
			self::init_hooks();
		}
	}

	private static function init_hooks() {
		self::$initiated = true;

		// Register admin menu creation callback
		add_action( 'admin_menu', 
			array( 'PwtcMileage_Admin', 'plugin_menu' ) );

		// Register script and style enqueue callbacks
		add_action( 'admin_enqueue_scripts', 
			array( 'PwtcMileage_Admin', 'load_admin_scripts' ) );

		// Register ajax callbacks
		add_action( 'wp_ajax_pwtc_mileage_lookup_posts', 
			array( 'PwtcMileage_Admin', 'lookup_posts_callback') );
		add_action( 'wp_ajax_pwtc_mileage_lookup_rides', 
			array( 'PwtcMileage_Admin', 'lookup_rides_callback') );
		add_action( 'wp_ajax_pwtc_mileage_create_ride', 
			array( 'PwtcMileage_Admin', 'create_ride_callback') );
		add_action( 'wp_ajax_pwtc_mileage_create_ride_from_event', 
			array( 'PwtcMileage_Admin', 'create_ride_from_event_callback') );
		add_action( 'wp_ajax_pwtc_mileage_remove_ride', 
			array( 'PwtcMileage_Admin', 'remove_ride_callback') );
		add_action( 'wp_ajax_pwtc_mileage_lookup_ridesheet', 
			array( 'PwtcMileage_Admin', 'lookup_ridesheet_callback') );
		add_action( 'wp_ajax_pwtc_mileage_lookup_riders', 
			array( 'PwtcMileage_Admin', 'lookup_riders_callback') );
		add_action( 'wp_ajax_pwtc_mileage_create_rider', 
			array( 'PwtcMileage_Admin', 'create_rider_callback') );
		add_action( 'wp_ajax_pwtc_mileage_remove_rider', 
			array( 'PwtcMileage_Admin', 'remove_rider_callback') );
		add_action( 'wp_ajax_pwtc_mileage_get_rider', 
			array( 'PwtcMileage_Admin', 'get_rider_callback') );
		add_action( 'wp_ajax_pwtc_mileage_remove_leader', 
			array( 'PwtcMileage_Admin', 'remove_leader_callback') );
		add_action( 'wp_ajax_pwtc_mileage_remove_mileage', 
			array( 'PwtcMileage_Admin', 'remove_mileage_callback') );
		add_action( 'wp_ajax_pwtc_mileage_add_leader', 
			array( 'PwtcMileage_Admin', 'add_leader_callback') );
		add_action( 'wp_ajax_pwtc_mileage_add_mileage', 
			array( 'PwtcMileage_Admin', 'add_mileage_callback') );
		add_action( 'wp_ajax_pwtc_mileage_generate_report', 
			array( 'PwtcMileage_Admin', 'generate_report_callback') );
    }    

	/*************************************************************/
	/* Script and style enqueue callback functions
	/*************************************************************/

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
		$posts = PwtcMileage_DB::fetch_posts_without_rides();
		$response = array('posts' => $posts);
    	echo wp_json_encode($response);
		wp_die();
	}

	public static function lookup_rides_callback() {
		$startdate = trim($_POST['startdate']);	
		$enddate = trim($_POST['enddate']);	
		$title = sanitize_text_field($_POST['title']);	
		if (!PwtcMileage::validate_date_str($startdate)) {
			$response = array(
				'error' => 'Start date entry "' . $startdate . '" is invalid.'
			);
			echo wp_json_encode($response);
		}
		else if (!PwtcMileage::validate_date_str($enddate)) {
			$response = array(
				'error' => 'End date entry "' . $enddate . '" is invalid.'
			);
			echo wp_json_encode($response);
		}
		else {
			$rides = PwtcMileage_DB::fetch_club_rides($title, $startdate, $enddate);
			$response = array(
				'title' => $title,
				'startdate' => $startdate,
				'enddate' => $enddate,
				'rides' => $rides);
    		echo wp_json_encode($response);
		}
		wp_die();
	}

	public static function create_ride_callback() {
		$startdate = trim($_POST['startdate']);	
		$title = sanitize_text_field($_POST['title']);	
		if (!PwtcMileage::validate_ride_title_str($title)) {
			$response = array(
				'error' => 'Title entry "' . $title . '" is invalid, must start with a letter.'
			);
			echo wp_json_encode($response);
		}
		else if (!PwtcMileage::validate_date_str($startdate)) {
			$response = array(
				'error' => 'Start date entry "' . $startdate . '" is invalid.'
			);
			echo wp_json_encode($response);
		}
		else {
			$status = PwtcMileage_DB::insert_ride($title, $startdate);
			if (false === $status or 0 === $status) {
				$response = array(
					'error' => 'Could not insert ridesheet into database.'
				);
				echo wp_json_encode($response);
			}
			else {
				$ride_id = PwtcMileage_DB::get_new_ride_id();
				$leaders = PwtcMileage_DB::fetch_ride_leaders($ride_id);
				$mileage = PwtcMileage_DB::fetch_ride_mileage($ride_id);
				$response = array(
					'ride_id' => $ride_id,
					'title' => $title,
					'startdate' => $startdate, 
					'leaders' => $leaders,
					'mileage' => $mileage);
				echo wp_json_encode($response);
			}
		}
		wp_die();
	}

	public static function create_ride_from_event_callback() {
		$startdate = trim($_POST['startdate']);	
		$title = sanitize_text_field($_POST['title']);	
		$postid = trim($_POST['post_id']);	
		$status = PwtcMileage_DB::insert_ride_with_postid($title, $startdate, intval($postid));
		if (false === $status or 0 === $status) {
			$response = array(
				'error' => 'Could not insert ridesheet into database.'
			);
    		echo wp_json_encode($response);
		}
		else {
			$ride_id = PwtcMileage_DB::get_new_ride_id();
			$leaders = PwtcMileage_DB::fetch_ride_leaders($ride_id);
			$mileage = PwtcMileage_DB::fetch_ride_mileage($ride_id);
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
		$rideid = trim($_POST['ride_id']);
		$mcnt = PwtcMileage_DB::fetch_ride_has_mileage(intval($rideid));
		$lcnt = PwtcMileage_DB::fetch_ride_has_leaders(intval($rideid));
		if ($mcnt > 0 or $lcnt > 0) {
			$response = array(
				'error' => 'Cannot delete a ridesheet that has riders.'
			);
    		echo wp_json_encode($response);
		}
		else {
			$status = PwtcMileage_DB::delete_ride(intval($rideid));
			if (false === $status or 0 === $status) {
				$response = array(
					'error' => 'Could not delete ridesheet from database.'
				);
    			echo wp_json_encode($response);
			}
			else {
				$response = array(
					'ride_id' => $rideid);
    			echo wp_json_encode($response);
			}
		}
		wp_die();	
	}

	public static function lookup_ridesheet_callback() {
		$rideid = trim($_POST['ride_id']);
		$title = '';
		$startdate = '';
		$results = PwtcMileage_DB::fetch_ride(intval($rideid));
		if (count($results) > 0) {
			$title = $results[0]['title'];
			$startdate = $results[0]['date'];
		}
		$leaders = PwtcMileage_DB::fetch_ride_leaders(intval($rideid));
		$mileage = PwtcMileage_DB::fetch_ride_mileage(intval($rideid));
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
		$lastname = sanitize_text_field($_POST['lastname']);	
		$firstname = sanitize_text_field($_POST['firstname']);
		$memberid = '';
    	if (isset($_POST['memberid'])) {
			$memberid = sanitize_text_field($_POST['memberid']);
		}
		$members = null;
		if ($memberid == '' and $firstname == '' and $lastname == '') {
			$members = array();
		}
		else {
			$members = PwtcMileage_DB::fetch_riders($lastname, $firstname, $memberid);
		}	
		$response = array(
			'memberid' => $memberid,
			'lastname' => $lastname,
			'firstname' => $firstname,
			'members' => $members);
    	echo wp_json_encode($response);
		wp_die();
	}

	public static function create_rider_callback() {
		$memberid = sanitize_text_field($_POST['member_id']);	
		$nonce = $_POST['nonce'];	
		$lastname = sanitize_text_field($_POST['lastname']);	
		$firstname = sanitize_text_field($_POST['firstname']);
		$expdate = sanitize_text_field($_POST['exp_date']);
		$mode = $_POST['mode'];
		if (!wp_verify_nonce($nonce, 'pwtc_mileage_create_rider')) {
			$response = array(
				'error' => 'Access security check failed.'
			);
			echo wp_json_encode($response);
		}
		else if (!PwtcMileage::validate_member_id_str($memberid)) {
			$response = array(
				'error' => 'Member ID entry "' . $memberid . '" is invalid, must be a 5 digit number.'
			);
			echo wp_json_encode($response);
		}
		else if (!PwtcMileage::validate_member_name_str($lastname)) {
			$response = array(
				'error' => 'Last name entry "' . $lastname . '" is invalid, must start with a letter.'
			);
			echo wp_json_encode($response);
		}
		else if (!PwtcMileage::validate_member_name_str($firstname)) {
			$response = array(
				'error' => 'First name entry "' . $firstname . '" is invalid, must start with a letter.'
			);
			echo wp_json_encode($response);
		}
		else if (!PwtcMileage::validate_date_str($expdate)) {
			$response = array(
				'error' => 'Expiration date entry "' . $expdate . '" is invalid.'
			);
			echo wp_json_encode($response);
		}
		else {
			$no_overwrite = false;
			if ($mode == 'insert') {
				if (count(PwtcMileage_DB::fetch_rider($memberid)) > 0) {
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
				$status = PwtcMileage_DB::insert_rider($memberid, $lastname, $firstname, $expdate);	
				if (false === $status or 0 === $status) {
					$response = array(
						'error' => 'Could not insert rider into database.'
					);
					echo wp_json_encode($response);
				}
				else {
					$response = array(
						'member_id' => $memberid,
						'lastname' => $lastname,
						'firstname' => $firstname,
						'exp_date' => $expdate);
					echo wp_json_encode($response);
				}
			}
		}
		wp_die();
	}

	public static function remove_rider_callback() {
		$memberid = sanitize_text_field($_POST['member_id']);	
		$nonce = $_POST['nonce'];	
		if (!wp_verify_nonce($nonce, 'pwtc_mileage_remove_rider')) {
			$response = array(
				'error' => 'Access security check failed.'
			);
			echo wp_json_encode($response);
		}
		else {
			$mcnt = PwtcMileage_DB::fetch_member_has_mileage($memberid);
			$lcnt = PwtcMileage_DB::fetch_member_has_leaders($memberid);
			if ($mcnt > 0 or $lcnt > 0) {
				$response = array(
					'error' => 'Cannot delete a rider that is entered on a ridesheet.'
				);
				echo wp_json_encode($response);
			}
			else {
				$status = PwtcMileage_DB::delete_rider($memberid);	
				if (false === $status or 0 === $status) {
					$response = array(
						'error' => 'Could not delete rider from database.'
					);
					echo wp_json_encode($response);
				}
				else {
					$response = array('member_id' => $memberid);
					echo wp_json_encode($response);
				}
			}
		}
		wp_die();
	}

	public static function get_rider_callback() {
		$memberid = sanitize_text_field($_POST['member_id']);	
		$result = PwtcMileage_DB::fetch_rider($memberid);
		if (count($result) == 0) {
			$response = array(
				'error' => 'Could not find rider ' . $memberid . '.'
			);
    		echo wp_json_encode($response);			
		}
		else {
			$response = array(
				'member_id' => $result[0]['member_id'],
				'lastname' => $result[0]['last_name'],
				'firstname' => $result[0]['first_name'],
				'exp_date' => $result[0]['expir_date']);
    		echo wp_json_encode($response);						
		}	
		wp_die();
	}

	public static function remove_leader_callback() {
		$rideid = trim($_POST['ride_id']);
		$memberid = trim($_POST['member_id']);
		$status = PwtcMileage_DB::delete_ride_leader(intval($rideid), $memberid);
		if (false === $status or 0 === $status) {
			$response = array(
				'error' => 'Could not delete ride leader from database.'
			);
    		echo wp_json_encode($response);
		}
		else {
			$leaders = PwtcMileage_DB::fetch_ride_leaders(intval($rideid));
			$response = array(
				'ride_id' => $rideid,
				'leaders' => $leaders
			);
    		echo wp_json_encode($response);
		}
		wp_die();
	}

	public static function remove_mileage_callback() {
		$rideid = trim($_POST['ride_id']);
		$memberid = trim($_POST['member_id']);
		$status = PwtcMileage_DB::delete_ride_mileage(intval($rideid), $memberid);
		if (false === $status or 0 === $status) {
			$response = array(
				'error' => 'Could not delete ride mileage from database.'
			);
    		echo wp_json_encode($response);
		}
		else {
			$mileage = PwtcMileage_DB::fetch_ride_mileage(intval($rideid));
			$response = array(
				'ride_id' => $rideid,
				'mileage' => $mileage
			);
    		echo wp_json_encode($response);
		}
		wp_die();
	}

	public static function check_expir_date($memberid) {
		$errormsg = null;
		$plugin_options = PwtcMileage::get_plugin_options();
		if (!$plugin_options['disable_expir_check']) {
			$rider = PwtcMileage_DB::fetch_rider($memberid);
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
		}
		return $errormsg;
	}

	public static function add_leader_callback() {
		$rideid = trim($_POST['ride_id']);
		$memberid = trim($_POST['member_id']);
		$error = self::check_expir_date($memberid);
		if ($error != null) {
			$response = array(
				'error' => $error
			);
    		echo wp_json_encode($response);
		}
		else {
			$status = PwtcMileage_DB::insert_ride_leader(intval($rideid), $memberid);
			if (false === $status or 0 === $status) {
				$response = array(
					'error' => 'Could not insert ride leader into database.'
				);
    			echo wp_json_encode($response);
			}
			else {
				$leaders = PwtcMileage_DB::fetch_ride_leaders(intval($rideid));
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
		$rideid = trim($_POST['ride_id']);
		$memberid = trim($_POST['member_id']);
		$mileage = trim($_POST['mileage']);
		$error = self::check_expir_date($memberid);
		if ($error != null) {
			$response = array(
				'error' => $error
			);
    		echo wp_json_encode($response);
		}
		else {
			if (!PwtcMileage::validate_mileage_str($mileage)) {
				$response = array(
					'error' => 'Mileage entry "' . $mileage . '" is invalid, must be a non-negative number.'
				);
    			echo wp_json_encode($response);
			}
			else {
				$status = PwtcMileage_DB::insert_ride_mileage(intval($rideid), $memberid, intval($mileage));
				if (false === $status or 0 === $status) {
					$response = array(
						'error' => 'Could not insert ride mileage into database.'
					);
    				echo wp_json_encode($response);
				}
				else {
					$mileage = PwtcMileage_DB::fetch_ride_mileage(intval($rideid));
					$response = array(
						'ride_id' => $rideid,
						'mileage' => $mileage
					);
    				echo wp_json_encode($response);
				}
			}
		}
		wp_die();
	}

	public static function generate_report() {
		$reportid = trim($_POST['report_id']);
		$plugin_options = PwtcMileage::get_plugin_options();
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
						$meta = PwtcMileage_DB::meta_ytd_miles();
						$data = PwtcMileage_DB::fetch_ytd_miles(ARRAY_N, $sort);
						break;
					case "ly_miles":
						$meta = PwtcMileage_DB::meta_ly_miles();
						$data = PwtcMileage_DB::fetch_ly_miles(ARRAY_N, $sort);
						break;
					case "lt_miles":
						$meta = PwtcMileage_DB::meta_lt_miles();
						$data = PwtcMileage_DB::fetch_lt_miles(ARRAY_N, $sort);
						break;
					case "ly_lt_achvmnt":
						$meta = PwtcMileage_DB::meta_ly_lt_achvmnt();
						$data = PwtcMileage_DB::fetch_ly_lt_achvmnt(ARRAY_N, $sort);
						break;
				}
				break;
			case "ytd_led":
			case "ly_led":
				$sort = $_POST['sort'];
				$sort = $_POST['sort'];
				switch ($reportid) {			
					case "ytd_led":
						$meta = PwtcMileage_DB::meta_ytd_led();
						$data = PwtcMileage_DB::fetch_ytd_led(ARRAY_N, $sort);
						break;
					case "ly_led":
						$meta = PwtcMileage_DB::meta_ly_led();
						$data = PwtcMileage_DB::fetch_ly_led(ARRAY_N, $sort);
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
						$meta = PwtcMileage_DB::meta_ytd_rides($name);
						$data = PwtcMileage_DB::fetch_ytd_rides(ARRAY_N, $memberid);
						break;
					case "ly_rides":
						$meta = PwtcMileage_DB::meta_ly_rides($name);
						$data = PwtcMileage_DB::fetch_ly_rides(ARRAY_N, $memberid);
						break;
					case "ytd_rides_led":
						$meta = PwtcMileage_DB::meta_ytd_rides_led($name);
						$data = PwtcMileage_DB::fetch_ytd_rides_led(ARRAY_N, $memberid);
						break;
					case "ly_rides_led":
						$meta = PwtcMileage_DB::meta_ly_rides_led($name);
						$data = PwtcMileage_DB::fetch_ly_rides_led(ARRAY_N, $memberid);
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
					$data[$key][$i] = date('D M j Y', strtotime($row[$i]));
				endforeach;					
			}
			$response = array(
				'report_id' => $reportid,
				'title' => $meta['title'],
				'header' => $meta['header'],
				'data' => $data
			);
			return $response;			
		}
		else {
			$response = array(
				'report_id' => $reportid,
				'error' => $error
			);
			return $response;
		}
	}

	public static function generate_report_callback() {
		echo wp_json_encode(self::generate_report());
		wp_die();
	}

	/*************************************************************/
	/* Admin menu and pages creation functions
	/*************************************************************/

	public static function plugin_menu() {
		$plugin_options = PwtcMileage::get_plugin_options();

    	$page_title = $plugin_options['plugin_menu_label'];
    	$menu_title = $plugin_options['plugin_menu_label'];
    	$capability = 'manage_options';
    	$parent_menu_slug = 'pwtc_mileage_menu';
    	$function = array( 'PwtcMileage_Admin', 'plugin_menu_page');
    	$icon_url = '';
    	$position = $plugin_options['plugin_menu_location'];
		add_menu_page($page_title, $menu_title, $capability, $parent_menu_slug, $function, $icon_url, $position);

    	$page_title = 'View Reports';
    	$menu_title = 'View Reports';
    	$menu_slug = 'pwtc_mileage_generate_reports';
    	$capability = 'edit_posts';
    	$function = array( 'PwtcMileage_Admin', 'page_generate_reports');
		$page = add_submenu_page($parent_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
		add_action('load-' . $page, array('PwtcMileage_Admin','download_report_pdf'));
		add_action('load-' . $page, array('PwtcMileage_Admin','download_report_csv'));

    	$page_title = 'Manage Riders';
    	$menu_title = 'Manage Riders';
    	$menu_slug = 'pwtc_mileage_manage_riders';
    	$capability = 'edit_published_pages';
    	$function = array( 'PwtcMileage_Admin', 'page_manage_riders');
		add_submenu_page($parent_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);

    	$page_title = 'Manage Ride Sheets';
    	$menu_title = 'Manage Ride Sheets';
    	$menu_slug = 'pwtc_mileage_manage_ride_sheets';
    	$capability = 'edit_published_pages';
    	$function = array( 'PwtcMileage_Admin', 'page_manage_ride_sheets');
		add_submenu_page($parent_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);

    	$page_title = 'Database Operations';
    	$menu_title = 'Database Ops';
    	$menu_slug = 'pwtc_mileage_manage_year_end';
    	$capability = 'manage_options';
    	$function = array( 'PwtcMileage_Admin', 'page_manage_year_end');
		$page = add_submenu_page($parent_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
		add_action('load-' . $page, array('PwtcMileage_Admin','download_csv'));

		remove_submenu_page($parent_menu_slug, $parent_menu_slug);

		$page_title = 'Settings';
    	$menu_title = 'Settings';
    	$menu_slug = 'pwtc_mileage_settings';
    	$capability = 'manage_options';
    	$function = array( 'PwtcMileage_Admin', 'page_manage_settings');
		add_submenu_page($parent_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
	}

	public static function plugin_menu_page() {
	}

	public static function page_manage_ride_sheets() {
		$plugin_options = PwtcMileage::get_plugin_options();
		$running_jobs = PwtcMileage_DB::num_running_jobs();
		include('admin-man-ridesheets.php');
	}

	public static function page_generate_reports() {
		$plugin_options = PwtcMileage::get_plugin_options();
		$running_jobs = PwtcMileage_DB::num_running_jobs();
		include('admin-gen-reports.php');
	}

	public static function page_manage_riders() {
		$plugin_options = PwtcMileage::get_plugin_options();
		$running_jobs = PwtcMileage_DB::num_running_jobs();
		include('admin-man-riders.php');
	}

	public static function write_export_csv_file($fp, $data, $header = null) {
		if ($header != null) {
			fputcsv($fp, $header);
		}
		foreach ($data as $item) {
    		fputcsv($fp, $item);
		}		
	}

	public static function write_export_pdf_file($pdf, $data, $header, $title) {
		$rows_per_page = 40;
		$table_width = 190;
		$tcell_width = $table_width/count($header);
		$pdf->SetAutoPageBreak(false);
		$pdf->SetFont('Arial', '', 14);
		if (count($data) == 0) {
			$pdf->AddPage();
			$pdf->SetTextColor(0);
			$pdf->SetFont('','B');
			$pdf->Write(5, $title);
			$pdf->Ln();
			$pdf->Ln();
			$pdf->SetFont('','I');
			$pdf->Write(5, 'table is empty');
		}
		else {
			$row_count = 9999;
			$page_count = 0;
			$fill = false;
			foreach ( $data as $datum ) {
				if ($row_count > $rows_per_page) {
					if ($page_count > 0) {
						$pdf->Cell($table_width,0,'','T');
					}
					$pdf->AddPage();
					if ($page_count == 0) {
						$pdf->SetTextColor(0);
						$pdf->SetFont('','B');
						$pdf->Write(5, $title);
						$pdf->Ln();
						$pdf->Ln();
					}
					$pdf->SetFillColor(255,0,0);
					$pdf->SetTextColor(255);
					$pdf->SetDrawColor(128,0,0);
					$pdf->SetLineWidth(.3);
					$pdf->SetFont('','B');
					foreach ( $header as $item ) {
						$pdf->Cell($tcell_width,7,$item,1,0,'C',true);
					}
					$pdf->Ln();
					$pdf->SetFillColor(224,235,255);
					$pdf->SetTextColor(0);
					$pdf->SetFont('');
					$row_count = 0;
					$page_count++;
				}
				foreach ( $datum as $col ) {
					$pdf->Cell($tcell_width,6,$col,'LR',0,'C',$fill);
				}
				$pdf->Ln();
				$fill = !$fill;
				$row_count++;
			}
			$pdf->Cell($table_width,0,'','T');
		}
	}

	public static function download_report_csv() {
		if (isset($_POST['export_csv'])) {
			$response = self::generate_report();
			$today = date('Y-m-d', current_time('timestamp'));
			$report_id = $response['report_id'];
			header('Content-Description: File Transfer');
			header("Content-type: text/csv");
			header("Content-Disposition: attachment; filename=rpt_{$report_id}_{$today}.csv");
			$fh = fopen('php://output', 'w');
			self::write_export_csv_file($fh, $response['data'], $response['header']);
			fclose($fh);
			die;
		}
	}

	public static function download_report_pdf() {
		if (isset($_POST['export_pdf'])) {
			$response = self::generate_report();
			$today = date('Y-m-d', current_time('timestamp'));
			$report_id = $response['report_id'];
			header('Content-Description: File Transfer');
			header("Content-type: application/pdf");
			header("Content-Disposition: attachment; filename=rpt_{$report_id}_{$today}.pdf");
			require('fpdf.php');	
			$pdf = new FPDF();
			self::write_export_pdf_file($pdf, $response['data'], $response['header'], $response['title']);
			$pdf->Output();
			die;
		}
	}

	public static function download_csv() {
		if (isset($_POST['export_members'])) {
			if (!isset($_POST['_wpnonce']) or
				!wp_verify_nonce($_POST['_wpnonce'], 'pwtc_mileage_export')) {
     			die('Nonce security check failed!'); 
			}			
			$today = date('Y-m-d', current_time('timestamp'));
			header('Content-Description: File Transfer');
			header("Content-type: text/csv");
			header("Content-Disposition: attachment; filename=members_{$today}.csv");
			$fh = fopen('php://output', 'w');
			self::write_export_csv_file($fh, PwtcMileage_DB::fetch_members_for_export());
			fclose($fh);
			die;
		}
		else if (isset($_POST['export_rides'])) {
			if (!isset($_POST['_wpnonce']) or
				!wp_verify_nonce($_POST['_wpnonce'], 'pwtc_mileage_export')) {
     			die('Nonce security check failed!'); 
			}			
			$today = date('Y-m-d', current_time('timestamp'));
			header('Content-Description: File Transfer');
			header("Content-type: text/csv");
			header("Content-Disposition: attachment; filename=rides_{$today}.csv");
			$fh = fopen('php://output', 'w');
			self::write_export_csv_file($fh, PwtcMileage_DB::fetch_rides_for_export());
			fclose($fh);
			die;
		}
		else if (isset($_POST['export_mileage'])) {
			if (!isset($_POST['_wpnonce']) or
				!wp_verify_nonce($_POST['_wpnonce'], 'pwtc_mileage_export')) {
     			die('Nonce security check failed!'); 
			}			
			$today = date('Y-m-d', current_time('timestamp'));
			header('Content-Description: File Transfer');
			header("Content-type: text/csv");
			header("Content-Disposition: attachment; filename=mileage_{$today}.csv");
			$fh = fopen('php://output', 'w');
			self::write_export_csv_file($fh, PwtcMileage_DB::fetch_mileage_for_export());
			fclose($fh);
			die;
		}
		else if (isset($_POST['export_leaders'])) {
			if (!isset($_POST['_wpnonce']) or
				!wp_verify_nonce($_POST['_wpnonce'], 'pwtc_mileage_export')) {
     			die('Nonce security check failed!'); 
			}			
			$today = date('Y-m-d', current_time('timestamp'));
			header('Content-Description: File Transfer');
			header("Content-type: text/csv");
			header("Content-Disposition: attachment; filename=leaders_{$today}.csv");
			$fh = fopen('php://output', 'w');
			self::write_export_csv_file($fh, PwtcMileage_DB::fetch_leaders_for_export());
			fclose($fh);
			die;
		}
	}

	public static function page_manage_year_end() {
		$plugin_options = PwtcMileage::get_plugin_options();
    	if (isset($_POST['consolidate'])) {
			if (!isset($_POST['_wpnonce']) or
				!wp_verify_nonce($_POST['_wpnonce'], 'pwtc_mileage_consolidate')) {
     			die('Nonce security check failed!'); 
			}			
			PwtcMileage_DB::job_set_status('consolidation', 'triggered');
			wp_schedule_single_event(time(), 'pwtc_mileage_consolidation');
		}

    	if (isset($_POST['member_sync'])) {
			if (!isset($_POST['_wpnonce']) or
				!wp_verify_nonce($_POST['_wpnonce'], 'pwtc_mileage_member_sync')) {
     			die('Nonce security check failed!'); 
			}			
			PwtcMileage_DB::job_set_status('member_sync', 'triggered');
			wp_schedule_single_event(time(), 'pwtc_mileage_member_sync');
		}
		if (isset($_POST['restore'])) {
			if (!isset($_POST['_wpnonce']) or
				!wp_verify_nonce($_POST['_wpnonce'], 'pwtc_mileage_restore')) {
     			die('Nonce security check failed!'); 
			}			
			PwtcMileage_DB::job_set_status('cvs_restore', 'triggered');
			$files = array(
				self::generate_file_record(
					'members_file', 'members', 'members_', PwtcMileage_DB::MEMBER_TABLE),
				self::generate_file_record(
					'rides_file', 'rides', 'rides_', PwtcMileage_DB::RIDE_TABLE),
				self::generate_file_record(
					'mileage_file', 'mileage', 'mileage_', PwtcMileage_DB::MILEAGE_TABLE),
				self::generate_file_record(
					'leaders_file', 'leaders', 'leaders_', PwtcMileage_DB::LEADER_TABLE)
			);
			$error = self::validate_uploaded_files($files);
			if ($error) {
				PwtcMileage_DB::job_set_status('cvs_restore', 'failed', $error);
			}
			else {
				$error = self::move_uploaded_files($files);
				if ($error) {
					PwtcMileage_DB::job_set_status('cvs_restore', 'failed', $error);
				}
				else {
					wp_schedule_single_event(time(), 'pwtc_mileage_cvs_restore');
				}
			}
		}
    	if (isset($_POST['clear_errs'])) {
			if (!isset($_POST['_wpnonce']) or
				!wp_verify_nonce($_POST['_wpnonce'], 'pwtc_mileage_clear_errs')) {
     			die('Nonce security check failed!'); 
			}			
			PwtcMileage_DB::job_remove_failed();
		}
    	if (isset($_POST['clear_lock'])) {
			if (!isset($_POST['_wpnonce']) or
				!wp_verify_nonce($_POST['_wpnonce'], 'pwtc_mileage_clear_lock')) {
     			die('Nonce security check failed!'); 
			}			
			PwtcMileage_DB::job_remove_running();
		}
		$job_status_s = PwtcMileage_DB::job_get_status('member_sync');
		$job_status_b = PwtcMileage_DB::job_get_status('backup');
		$job_status_c = PwtcMileage_DB::job_get_status('consolidation');
		$job_status_r = PwtcMileage_DB::job_get_status('cvs_restore');
		$max_timestamp = PwtcMileage_DB::max_job_timestamp();
		$show_clear_lock = false;
		if ($max_timestamp !== null && time()-$max_timestamp > $plugin_options['db_lock_time_limit']) {
			$show_clear_lock = true;
		}
		$thisyear = date('Y', current_time('timestamp'));
    	$yearbeforelast = intval($thisyear) - 2;
		$maxdate = '' . $yearbeforelast . '-12-31';
		$rides_to_consolidate = PwtcMileage_DB::get_num_rides_before_date($maxdate);

		$member_count = PwtcMileage_DB::count_members();
		$ride_count = PwtcMileage_DB::count_rides();
		$mileage_count = PwtcMileage_DB::count_mileage();
		$leader_count = PwtcMileage_DB::count_leaders();

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
		if (isset($_POST['_wpnonce'])) {
			if (!wp_verify_nonce($_POST['_wpnonce'], 'pwtc_mileage_settings')) {
     			die('Nonce security check failed!'); 
			}			
		}
		$plugin_options = PwtcMileage::get_plugin_options();
		$form_submitted = false;
		$error_msgs = array();
    	if (isset($_POST['plugin_menu_label'])) {
			$form_submitted = true;
			$entry = sanitize_text_field($_POST['plugin_menu_label']);
			if (!PwtcMileage::validate_label_str($entry)) {
				array_push($error_msgs,
					'Plugin Menu Label field must contain a valid string.');
			}
			else {
				$plugin_options['plugin_menu_label'] = $entry;
			}
    	} 
    	if (isset($_POST['plugin_menu_location'])) {
			$form_submitted = true;
			$entry = sanitize_text_field($_POST['plugin_menu_location']);
			if (!PwtcMileage::validate_number_str($entry)) {
				array_push($error_msgs,
					'Plugin Menu Location field must contain a non-negative number.');
			}
			else {
				$plugin_options['plugin_menu_location'] = intval($entry);
			}
    	} 
    	if (isset($_POST['db_lock_time_limit'])) {
			$form_submitted = true;
			$entry = sanitize_text_field($_POST['db_lock_time_limit']);
			if (!PwtcMileage::validate_number_str($entry)) {
				array_push($error_msgs,
					'DB Batch Job Lock Time Limit field must contain a non-negative number.');
			}
			else {
				$plugin_options['db_lock_time_limit'] = intval($entry);
			}
    	} 
    	if (isset($_POST['ride_lookback_date'])) {
			$form_submitted = true;
			$entry = sanitize_text_field($_POST['ride_lookback_date']);
			if ($entry != '' and !PwtcMileage::validate_date_str($entry)) {
				array_push($error_msgs,
					'Posted Ride Maximum Lookback Date field must contain a valid date.');
			}
			else {
				$plugin_options['ride_lookback_date'] = $entry;
			}
    	} 
		if ($form_submitted) {
			if (isset($_POST['drop_db_on_delete'])) {
				$plugin_options['drop_db_on_delete'] = true;
			}
			else {
				$plugin_options['drop_db_on_delete'] = false;
			}
			if (isset($_POST['disable_expir_check'])) {
				$plugin_options['disable_expir_check'] = true;
			}
			else {
				$plugin_options['disable_expir_check'] = false;
			}
			if (isset($_POST['disable_delete_confirm'])) {
				$plugin_options['disable_delete_confirm'] = true;
			}
			else {
				$plugin_options['disable_delete_confirm'] = false;
			}
			if (isset($_POST['show_ride_ids'])) {
				$plugin_options['show_ride_ids'] = true;
			}
			else {
				$plugin_options['show_ride_ids'] = false;
			}
			PwtcMileage::update_plugin_options($plugin_options);
			$plugin_options = PwtcMileage::get_plugin_options();			
		}
		include('admin-man-settings.php');
	}

}