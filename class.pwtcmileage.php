<?php

class PwtcMileage {

	// TODO: add plugin prefix to capacity names.
	const VIEW_MILEAGE_CAP = 'view_mileage';
	const EDIT_MILEAGE_CAP = 'edit_mileage';
	const EDIT_RIDERS_CAP = 'edit_riders';
	const DB_OPS_CAP = 'mileage_db_ops';

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

		// Register script and style enqueue callbacks
		add_action( 'wp_enqueue_scripts', 
			array( 'PwtcMileage', 'load_report_scripts' ) );

		// Register shortcode callbacks
		add_shortcode('pwtc_rider_name', 
			array( 'PwtcMileage', 'shortcode_rider_name'));
		add_shortcode('pwtc_rider_mileage', 
			array( 'PwtcMileage', 'shortcode_rider_mileage'));
		add_shortcode('pwtc_rider_led', 
			array( 'PwtcMileage', 'shortcode_rider_led'));
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

	/*************************************************************/
	/* Background action task callbacks
	/*************************************************************/

	// TODO: validate permissions!
	public static function consolidation_callback() {
		error_log( 'Consolidation process started.');
		PwtcMileage_DB::job_set_status('consolidation', 'started');

		$thisyear = date('Y', current_time('timestamp'));
    	$yearbeforelast = intval($thisyear) - 2;
		$title = 'Totals Through ' . $yearbeforelast;
		$maxdate = '' . $yearbeforelast . '-12-31';

		$num_rides = PwtcMileage_DB::get_num_rides_before_date($maxdate);	
		if ($num_rides == 0) {
			PwtcMileage_DB::job_set_status('consolidation', 'failed', 
				'no ridesheets were found for ' . $yearbeforelast);
		}
		else if ($num_rides == 1) {
			PwtcMileage_DB::job_set_status('consolidation', 'failed', 
				'' . $yearbeforelast . ' ridesheets are already consolidated');
		}
		else {
			$status = PwtcMileage_DB::insert_ride($title, $maxdate);
			if (false === $status or 0 === $status) {
				PwtcMileage_DB::job_set_status('consolidation', 'failed', 'could not insert new ridesheet');
			}
			else {
				$rideid = PwtcMileage_DB::get_new_ride_id();
				if (isset($rideid) and is_int($rideid)) {
					$status = PwtcMileage_DB::rollup_ridesheets($rideid, $maxdate);
					error_log(var_export($status, true));
					PwtcMileage_DB::job_remove('consolidation');
				}
				else {
					PwtcMileage_DB::job_set_status('consolidation', 'failed', 'new ridesheet ID is invalid');
				}
			}
		}	
	}

	// TODO: validate permissions!
	public static function member_sync_callback() {
		error_log( 'Membership Sync process started.');
		PwtcMileage_DB::job_set_status('member_sync', 'started');
		$members = pwtc_mileage_fetch_membership();
		if (count($members) == 0) {
			PwtcMileage_DB::job_set_status('member_sync', 'failed', 'no members in membership list');
		}
		else {
			$val_fail_count = 0;
			$ins_fail_count = 0;
			$ins_succ_count = 0;
			$dup_rec_count = 0;
			$riders = PwtcMileage_DB::fetch_members_for_export();
			$hashmap = array();
			foreach ( $riders as $item ) {
				$hashmap[$item[0]] = $item;
			}
    		foreach ( $members as $item ) {
				$memberid = trim($item[0]);
				$firstname = trim($item[1]);
				$lastname = trim($item[2]);
				$expirdate = trim($item[3]);
				if (!self::validate_member_id_str($memberid)) {
					$val_fail_count++;
				}
				else if (!self::validate_member_name_str($lastname)) {
					$val_fail_count++;
				}
				else if (!self::validate_member_name_str($firstname)) {
					$val_fail_count++;
				}
				else if (!self::validate_date_str($expirdate)) {
					$val_fail_count++;
				}
				else {
					$insert = false;
					if (array_key_exists($memberid, $hashmap)) {
						$rider = $hashmap[$memberid];
						if ($firstname != $rider[1] or $lastname != $rider[2] or $expirdate != $rider[3]) {
							$insert = true;
						}
						else {
							$dup_rec_count++;
						}
					}
					else {
						$insert = true;
					}

					if ($insert) {
						$status = PwtcMileage_DB::insert_rider($memberid, $lastname, $firstname, $expirdate);	
						if (false === $status or 0 === $status) {
							$ins_fail_count++;
						}
						else {
							$ins_succ_count++;
						}
					}
				}
			}
			error_log('val_fail_count: ' . $val_fail_count);
			error_log('ins_fail_count: ' . $ins_fail_count);
			error_log('ins_succ_count: ' . $ins_succ_count);
			error_log('dup_rec_count: ' . $dup_rec_count);
			if ($ins_fail_count > 0) {
				PwtcMileage_DB::job_set_status('member_sync', 'failed', 
					'member database insert failed ' . $ins_fail_count . ' times');
			}
			else if ($val_fail_count > 0) {
				PwtcMileage_DB::job_set_status('member_sync', 'failed', 
					'member validation failed ' . $val_fail_count . ' times');
			}
			else {
				PwtcMileage_DB::job_remove('member_sync');
			}	
		}
	}

	// TODO: validate permissions!
	public static function cvs_restore_callback() {
		error_log( 'CVS restore process started.');
		PwtcMileage_DB::job_set_status('cvs_restore', 'started');
		$upload_dir = wp_upload_dir();
		$plugin_upload_dir = $upload_dir['basedir'] . '/pwtc_mileage';
		$members_file = $plugin_upload_dir . '/' . PwtcMileage_DB::MEMBER_TABLE . '.csv';
		$rides_file = $plugin_upload_dir . '/' . PwtcMileage_DB::RIDE_TABLE . '.csv';
		$mileage_file = $plugin_upload_dir . '/' . PwtcMileage_DB::MILEAGE_TABLE . '.csv';
		$leaders_file = $plugin_upload_dir . '/' . PwtcMileage_DB::LEADER_TABLE . '.csv';
		if (!file_exists($members_file)) {
			PwtcMileage_DB::job_set_status('cvs_restore', 'failed', 'members upload file does not exist');
		}
		else if (!file_exists($rides_file)) {
			PwtcMileage_DB::job_set_status('cvs_restore', 'failed', 'rides upload file does not exist');
		}
		else if (!file_exists($mileage_file)) {
			PwtcMileage_DB::job_set_status('cvs_restore', 'failed', 'mileage upload file does not exist');
		}
		else if (!file_exists($leaders_file)) {
			PwtcMileage_DB::job_set_status('cvs_restore', 'failed', 'leaders upload file does not exist');
		}
		else {
			$plugin_upload_url = $upload_dir['baseurl'] . '/pwtc_mileage';
			$members_url = $plugin_upload_url . '/' . PwtcMileage_DB::MEMBER_TABLE . '.csv';
			$rides_url = $plugin_upload_url . '/' . PwtcMileage_DB::RIDE_TABLE . '.csv';
			$mileage_url = $plugin_upload_url . '/' . PwtcMileage_DB::MILEAGE_TABLE . '.csv';
			$leaders_url = $plugin_upload_url . '/' . PwtcMileage_DB::LEADER_TABLE . '.csv';
			error_log('members_url: ' . $members_url);
			error_log('rides_url: ' . $rides_url);
			error_log('mileage_url: ' . $mileage_url);
			error_log('leaders_url: ' . $leaders_url);

			PwtcMileage_DB::delete_database_for_restore();
			PwtcMileage_DB::load_members_for_restore($members_url);
			PwtcMileage_DB::load_rides_for_restore($rides_url);
			PwtcMileage_DB::load_mileage_for_restore($mileage_url);
			PwtcMileage_DB::load_leaders_for_restore($leaders_url);

			unlink($members_file);
			unlink($rides_file);
			unlink($mileage_file);
			unlink($leaders_file);

			PwtcMileage_DB::job_remove('cvs_restore');
		}	
	}

	/*************************************************************/
	/* Shortcode utility functions
	/*************************************************************/

	public static function get_rider_name($id) {
		$rider = PwtcMileage_DB::fetch_rider($id);
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

	public static function shortcode_build_table($meta, $data, $atts, $content = null) {
		$plugin_options = self::get_plugin_options();
		$hide_id = true;
		if ($atts['show_id'] == 'on') {
			$hide_id = false;
		}
		$id = null;
		if ($meta['id_idx'] >= 0 and $atts['highlight_user'] == 'on') {
			$id = pwtc_mileage_get_member_id();
		}
		$out = '<div>';  
		if (count($data) > 0) {
			$out .= '<table class="pwtc-mileage-rwd-table">';
			if (empty($content)) {
				if ($atts['caption'] == 'on') {
					$out .= '<caption>' . $meta['title'] . '</caption>';
				}
			}
			else {
				$out .= '<caption>' . do_shortcode($content) . '</caption>';
			}
			$out .= '<tr>';
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
					$label = $meta['header'][$i];
					$lbl_attr = 'data-th="' . $label . '"';
					if ($meta['date_idx'] == $i) {
						$fmtdate = date('D M j Y', strtotime($item));
						$outrow .= '<td ' . $lbl_attr . '>' . $fmtdate . '</td>';
					}
					else if ($meta['id_idx'] === $i) {
						if ($id !== null and $id == $item) {
							$highlight = true;
						}
						if (!$hide_id) {
							$outrow .= '<td ' . $lbl_attr . '>' . $item . '</td>';						
						}
					}
					else {
						if (0 === strpos($item, 'http://') or 0 === strpos($item, 'https://')) {
							$outrow .= '<td ' . $lbl_attr . '><a href="' . $item . 
								'" target="_blank">View</a></td>';
						}
						else {
							$outrow .= '<td ' . $lbl_attr . '>' . $item . '</td>';
						}
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
			$out .= '</table>';
		}
		else {
			$out .= '<span class="pwtc-mileage-empty-tbl">No records found!</span>';
		}
		$out .= '</div>';
		return $out;
	}

	public static function normalize_atts($atts) {
    	$a = shortcode_atts(array(
        		'show_id' => 'off',
       			'highlight_user' => 'on',
				'sort_by' => 'off',
				'sort_order' => 'asc',
				'minimum' => 1,
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

	public static function shortcode_rider_name($atts) {
		$out = '';
		$id = pwtc_mileage_get_member_id();
		if ($id != null) {
			$result = PwtcMileage_DB::fetch_rider($id);
			if (count($result) > 0) {
				$out .= '<span>' . $result[0]['first_name'] . ' ' . 
					$result[0]['last_name'] . '</span>';
			}
		}
		else {
			$out .= '<span>Unknown Rider</span>';			
		}
		return $out;
	}

	public static function shortcode_rider_mileage($atts) {
    	$a = shortcode_atts(array('type' => 'year2date'), $atts);
		$out = '';
		$id = pwtc_mileage_get_member_id();
		if ($id != null) {
			if ($a['type'] == 'year2date') {
				$out .= '<span>' . PwtcMileage_DB::get_ytd_rider_mileage($id) . '</span>';
			}
			else if ($a['type'] == 'lastyear') {
				$out .= '<span>' . PwtcMileage_DB::get_ly_rider_mileage($id) . '</span>';
			}
			else if ($a['type'] == 'lifetime') {
				$out .= '<span>' . PwtcMileage_DB::get_lt_rider_mileage($id) . '</span>';
			}
		}
		else {
			$out .= '<span>0</span>';			
		}
		return $out;
	}

	public static function shortcode_rider_led($atts) {
    	$a = shortcode_atts(array('type' => 'year2date'), $atts);
		$out = '';
		$id = pwtc_mileage_get_member_id();
		if ($id != null) {
			if ($a['type'] == 'year2date') {
				$out .= '<span>' . PwtcMileage_DB::get_ytd_rider_led($id) . '</span>';
			}
			else if ($a['type'] == 'lastyear') {
				$out .= '<span>' . PwtcMileage_DB::get_ly_rider_led($id) . '</span>';
			}
		}
		else {
			$out .= '<span>0</span>';						
		}
		return $out;
	}

	public static function shortcode_ly_lt_achvmnt($atts, $content = null) {
		$a = self::normalize_atts($atts);
		$sort = self::build_mileage_sort($a);
		$meta = PwtcMileage_DB::meta_ly_lt_achvmnt();
		$data = PwtcMileage_DB::fetch_ly_lt_achvmnt(ARRAY_N, $sort);
		$out = self::shortcode_build_table($meta, $data, $a, $content);
		return $out;
	}

	public static function shortcode_ytd_miles($atts, $content = null) {
		$a = self::normalize_atts($atts);
		$sort = self::build_mileage_sort($a);
		$min = self::get_minimum_val($a);
		$meta = PwtcMileage_DB::meta_ytd_miles();
		$data = PwtcMileage_DB::fetch_ytd_miles(ARRAY_N, $sort, $min);
		$out = self::shortcode_build_table($meta, $data, $a, $content);
		return $out;
	}

	public static function shortcode_ly_miles($atts, $content = null) {
		$a = self::normalize_atts($atts);
		$sort = self::build_mileage_sort($a);
		$min = self::get_minimum_val($a);
		$meta = PwtcMileage_DB::meta_ly_miles();
		$data = PwtcMileage_DB::fetch_ly_miles(ARRAY_N, $sort, $min);
		$out = self::shortcode_build_table($meta, $data, $a, $content);
		return $out;
	}

	public static function shortcode_lt_miles($atts, $content = null) {
		$a = self::normalize_atts($atts);
		$sort = self::build_mileage_sort($a);
		$min = self::get_minimum_val($a);
		$meta = PwtcMileage_DB::meta_lt_miles();
		$data = PwtcMileage_DB::fetch_lt_miles(ARRAY_N, $sort, $min);
		$out = self::shortcode_build_table($meta, $data, $a, $content);
		return $out;
	}

	public static function shortcode_ytd_led($atts, $content = null) {
		$a = self::normalize_atts($atts);
		$sort = self::build_rides_led_sort($a);
		$min = self::get_minimum_val($a);
		$meta = PwtcMileage_DB::meta_ytd_led();
		$data = PwtcMileage_DB::fetch_ytd_led(ARRAY_N, $sort, $min);
		$out = self::shortcode_build_table($meta, $data, $a, $content);
		return $out;
	}

	public static function shortcode_ly_led($atts, $content = null) {
		$a = self::normalize_atts($atts);
		$sort = self::build_rides_led_sort($a);
		$min = self::get_minimum_val($a);
		$meta = PwtcMileage_DB::meta_ly_led();
		$data = PwtcMileage_DB::fetch_ly_led(ARRAY_N, $sort, $min);
		$out = self::shortcode_build_table($meta, $data, $a, $content);
		return $out;
	}

	public static function shortcode_ytd_rides($atts, $content = null) {
		$member_id = pwtc_mileage_get_member_id();
		$out = '';
		if ($member_id === null) {
			$out = '';
		}
		else {
			$name = self::get_rider_name($member_id);
			$a = self::normalize_atts($atts);
			$meta = PwtcMileage_DB::meta_ytd_rides($name);
			$data = PwtcMileage_DB::fetch_ytd_rides(ARRAY_N, $member_id);
			$out = self::shortcode_build_table($meta, $data, $a, $content);
		}
		return $out;
	}

	public static function shortcode_ly_rides($atts, $content = null) {
		$member_id = pwtc_mileage_get_member_id();
		$out = '';
		if ($member_id === null) {
			$out = '';
		}
		else {
			$name = self::get_rider_name($member_id);
			$a = self::normalize_atts($atts);
			$meta = PwtcMileage_DB::meta_ly_rides($name);
			$data = PwtcMileage_DB::fetch_ly_rides(ARRAY_N, $member_id);
			$out = self::shortcode_build_table($meta, $data, $a, $content);
		}
		return $out;
	}

	public static function shortcode_ytd_led_rides($atts, $content = null) {
		$member_id = pwtc_mileage_get_member_id();
		$out = '';
		if ($member_id === null) {
			$out = '';
		}
		else {
			$name = self::get_rider_name($member_id);
			$a = self::normalize_atts($atts);
			$meta = PwtcMileage_DB::meta_ytd_rides_led($name);
			$data = PwtcMileage_DB::fetch_ytd_rides_led(ARRAY_N, $member_id);
			$out = self::shortcode_build_table($meta, $data, $a, $content);
		}
		return $out;
	}

	public static function shortcode_ly_led_rides($atts, $content = null) {
		$member_id = pwtc_mileage_get_member_id();
		if ($member_id === null) {
			$out = '';
		}
		else {
			$name = self::get_rider_name($member_id);
			$a = self::normalize_atts($atts);
			$meta = PwtcMileage_DB::meta_ly_rides_led($name);
			$data = PwtcMileage_DB::fetch_ly_rides_led(ARRAY_N, $member_id);
			$out = self::shortcode_build_table($meta, $data, $a, $content);
			return $out;
		}
	}

	public static function shortcode_rides_wo_sheets($atts, $content = null) {
		$a = self::normalize_atts($atts);
		$meta = PwtcMileage_DB::meta_posts_without_rides2();
		$data = PwtcMileage_DB::fetch_posts_without_rides2();
		$out = self::shortcode_build_table($meta, $data, $a, $content);
		return $out;
	}

	/*************************************************************/
	/* User input validation functions
	/*************************************************************/

	public static function validate_date_str($datestr) {
		$ok = true;
		if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $datestr) !== 1) {
			$ok = false;
		}
		return $ok;
	}

	public static function validate_member_id_str($memberid) {
		$ok = true;
		if (preg_match('/^\d{5}$/', $memberid) !== 1) {
			$ok = false;
		}
		return $ok;
	}

	public static function validate_member_name_str($name) {
		$ok = true;
		if (preg_match('/^[A-Za-z].*/', $name) !== 1) {
			$ok = false;
		}
		return $ok;
	}

	// TODO: allow ride titles to also start with a digit.
	public static function validate_ride_title_str($title) {
		$ok = true;
		if (preg_match('/^[A-Za-z].*/', $title) !== 1) {
			$ok = false;
		}
		return $ok;
	}

	public static function validate_label_str($label) {
		$ok = true;
		if (preg_match('/^[A-Za-z].*/', $label) !== 1) {
			$ok = false;
		}
		return $ok;
	}

	public static function validate_mileage_str($mileage) {
		$ok = true;
		if (!is_numeric($mileage)) {
			$ok = false;
		}
		else if (intval($mileage) < 0) {
			$ok = false;
		}
		return $ok;
	}

	public static function validate_number_str($number) {
		$ok = true;
		if (!is_numeric($number)) {
			$ok = false;
		}
		else if (intval($number) < 0) {
			$ok = false;
		}
		return $ok;
	}


	/*************************************************************/
	/* Plugin options access functions
	/*************************************************************/

	// TODO: add an option to specify a "grace period" for the member expiration date.
	public static function create_default_plugin_options() {
		$data = array(
			'drop_db_on_delete' => false,
			'plugin_menu_label' => 'Rider Mileage',
			'plugin_menu_location' => 50,
			'ride_lookback_date' => '',
			'disable_expir_check' => false,
			'disable_delete_confirm' => false,
			'show_ride_ids' => false,
			'db_lock_time_limit' => 60);
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

	public static function add_caps_admin_role() {
		$admin = get_role('administrator');
		$admin->add_cap(self::VIEW_MILEAGE_CAP);
		$admin->add_cap(self::EDIT_MILEAGE_CAP);
		$admin->add_cap(self::EDIT_RIDERS_CAP);
		$admin->add_cap(self::DB_OPS_CAP);
		error_log('PWTC Mileage plugin added capabilities to administrator role');
	}

	public static function remove_caps_admin_role() {
		$admin = get_role('administrator');
		$admin->remove_cap(self::VIEW_MILEAGE_CAP);
		$admin->remove_cap(self::EDIT_MILEAGE_CAP);
		$admin->remove_cap(self::EDIT_RIDERS_CAP);
		$admin->remove_cap(self::DB_OPS_CAP);
		error_log('PWTC Mileage plugin removed capabilities from administrator role');
	}

	public static function create_stat_role() {
		$stat = get_role('statistician');
		if ($stat === null) {
			$subscriber = get_role('subscriber');
			$stat = add_role('statistician', 'Statistician', $subscriber->capabilities);
			error_log('PWTC Mileage plugin added statistician role');
		}
		if ($stat !== null) {
			$stat->add_cap(self::VIEW_MILEAGE_CAP);
			$stat->add_cap(self::EDIT_MILEAGE_CAP);
			$stat->add_cap(self::EDIT_RIDERS_CAP);
			$stat->add_cap(self::DB_OPS_CAP);
			error_log('PWTC Mileage plugin added capabilities to statistician role');
		}
	}

	public static function remove_stat_role() {
		$users = get_users(array('role' => 'statistician'));
		if (count($users) > 0) {
			$stat = get_role('statistician');
			$stat->remove_cap(self::VIEW_MILEAGE_CAP);
			$stat->remove_cap(self::EDIT_MILEAGE_CAP);
			$stat->remove_cap(self::EDIT_RIDERS_CAP);
			$stat->remove_cap(self::DB_OPS_CAP);
			error_log('PWTC Mileage plugin removed capabilities from statistician role');
		}
		else {
			$stat = get_role('statistician');
			if ($stat !== null) {
				remove_role('statistician');
				error_log('PWTC Mileage plugin removed statistician role');
			}
		}
	}

	/*************************************************************/
	/* Plugin installation and removal functions
	/*************************************************************/

	public static function plugin_activation() {
		error_log( 'PWTC Mileage plugin activated' );
		if ( version_compare( $GLOBALS['wp_version'], PWTC_MILEAGE__MINIMUM_WP_VERSION, '<' ) ) {
			//TODO: Implement version check fail abort
		}
		PwtcMileage_DB::create_db_tables();
		PwtcMileage_DB::create_db_views();
		if (self::get_plugin_options() === false) {
			//self::delete_plugin_options();
			self::create_default_plugin_options();
		}
		self::add_caps_admin_role();
		self::create_stat_role();
	}

	public static function plugin_deactivation( ) {
		error_log( 'PWTC Mileage plugin deactivated' );
		//self::delete_plugin_options();
		self::remove_caps_admin_role();
		self::remove_stat_role();
	}

	public static function plugin_uninstall() {
		error_log( 'PWTC Mileage plugin uninstall' );	
		$plugin_options = self::get_plugin_options();
		if ($plugin_options['drop_db_on_delete']) {
			PwtcMileage_DB::drop_db_views();	
			PwtcMileage_DB::drop_db_tables();				
		}
		self::delete_plugin_options();
	}

}