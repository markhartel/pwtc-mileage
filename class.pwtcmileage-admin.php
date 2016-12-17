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
		$posts = PwtcMileage_DB::fetch_posts_without_rides(ARRAY_A);
		$response = array('posts' => $posts);
    	echo wp_json_encode($response);
		wp_die();
	}

	public static function lookup_rides_callback() {
		$startdate = $_POST['startdate'];	
		$enddate = $_POST['enddate'];	
		$title = $_POST['title'];	
		$rides = PwtcMileage_DB::fetch_club_rides($title, $startdate, $enddate);
		$response = array(
			'rides' => $rides);
    	echo wp_json_encode($response);
		wp_die();
	}

	public static function create_ride_callback() {
		$startdate = $_POST['startdate'];	
		$title = $_POST['title'];	
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
		wp_die();
	}

	public static function create_ride_from_event_callback() {
		$startdate = $_POST['startdate'];	
		$title = $_POST['title'];	
		$postid = $_POST['post_id'];	
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
		$startdate = $_POST['startdate'];
		$enddate = $_POST['enddate'];	
		$title = $_POST['title'];	
		$rideid = $_POST['ride_id'];
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
				$rides = PwtcMileage_DB::fetch_club_rides($title, $startdate, $enddate);
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
		$lastname = $_POST['lastname'];	
		$firstname = $_POST['firstname'];
		$memberid = '';
    	if (isset($_POST['memberid'])) {
			$memberid = $_POST['memberid'];
		}
		$members = PwtcMileage_DB::fetch_riders($lastname, $firstname, $memberid);	
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
				$members = PwtcMileage_DB::fetch_riders($lookuplast, $lookupfirst);
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
				$members = PwtcMileage_DB::fetch_riders($lastname, $firstname);	
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
		$rideid = $_POST['ride_id'];
		$memberid = $_POST['member_id'];
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

	//TODO: Disable this check if option is set.
	public static function check_expir_date($memberid) {
		$rider = PwtcMileage_DB::fetch_rider($memberid);
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
		wp_die();
	}

	public static function generate_report_callback() {
		$reportid = $_POST['report_id'];
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
		add_submenu_page($parent_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);

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

		$page_title = 'Plugin Settings';
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

	public static function write_export_csv_file($fp, $data) {
		foreach ($data as $item) {
    		fputcsv($fp, $item);
		}		
	}

	public static function download_csv() {
		if (isset($_POST['export_members'])) {
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
			PwtcMileage_DB::job_set_status('consolidation', 'triggered');
			wp_schedule_single_event(time(), 'pwtc_mileage_consolidation');
		}

    	if (isset($_POST['member_sync'])) {
			PwtcMileage_DB::job_set_status('member_sync', 'triggered');
			wp_schedule_single_event(time(), 'pwtc_mileage_member_sync');
		}
		if (isset($_POST['restore'])) {
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
			PwtcMileage_DB::job_remove_failed();
		}
		$job_status_s = PwtcMileage_DB::job_get_status('member_sync');
		$job_status_b = PwtcMileage_DB::job_get_status('backup');
		$job_status_c = PwtcMileage_DB::job_get_status('consolidation');
		$job_status_r = PwtcMileage_DB::job_get_status('cvs_restore');
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
		$plugin_options = PwtcMileage::get_plugin_options();
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
			PwtcMileage::update_plugin_options($plugin_options);
			$plugin_options = PwtcMileage::get_plugin_options();			
		}
		include('admin-man-settings.php');
	}

}