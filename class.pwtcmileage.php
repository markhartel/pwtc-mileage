<?php

class PwtcMileage {
	const MEMBER_TABLE = 'pwtc_membership';
	const RIDE_TABLE = 'pwtc_club_rides';
	const MILEAGE_TABLE = 'pwtc_ride_mileage';
	const LEADER_TABLE = 'pwtc_ride_leaders';

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
		add_shortcode('pwtc_mileage_report', array( 'PwtcMileage', 'report_shortcode'));
    }

	public static function plugin_menu() {
		add_menu_page('PWTC Mileage', 'PWTC Mileage', 'manage_options', 'pwtc_mileage_menu', array( 'PwtcMileage', 'plugin_menu_page'));
		add_submenu_page('pwtc_mileage_menu', 'Generate Reports', 'Generate Reports', 'manage_options', 'pwtc_mileage_generate_reports', array('PwtcMileage', 'plugin_menu_page'));
		add_submenu_page('pwtc_mileage_menu', 'Manage Riders', 'Manage Riders', 'manage_options', 'pwtc_mileage_manage_riders', array('PwtcMileage', 'plugin_menu_page'));
		add_submenu_page('pwtc_mileage_menu', 'Manage Ride Sheets', 'Manage Ride Sheets', 'manage_options', 'pwtc_mileage_manage_ride_sheets', array('PwtcMileage', 'plugin_menu_page'));

		remove_submenu_page('pwtc_mileage_menu', 'pwtc_mileage_menu');
		add_submenu_page('pwtc_mileage_menu', 'Settings', 'Settings', 'manage_options', 'pwtc_mileage_settings', array( 'PwtcMileage', 'plugin_menu_page'));
	}

	public static function plugin_menu_page() {
    	if (!current_user_can('manage_options')) {
        	return;
    	}
    	?>
    	<div class="wrap">
			<h1><?= esc_html(get_admin_page_title()); ?></h1>
			<p>This is a test.</p>
    	</div>
    	<?php
	}

	public static function report_shortcode() {
		$out = '<div>Output from pwtc_mileage_report shortcode</div>';
		return $out;
	}

	public static function plugin_activation() {
		error_log( 'PWTC Mileage plugin activated' );
		if ( version_compare( $GLOBALS['wp_version'], PWTC_MILEAGE__MINIMUM_WP_VERSION, '<' ) ) {
			$message = '<strong>'.sprintf('PWTC Mileage %s requires WordPress %s or higher.', PWTC_MILEAGE__VERSION, PWTC_MILEAGE__MINIMUM_WP_VERSION ).'</strong> '.sprintf('Please <a href="%1$s">upgrade WordPress</a> to a current version.', 'https://codex.wordpress.org/Upgrading_WordPress');
			PwtcMileage::bail_on_activation( $message );
		}
		self::create_db_tables();
	}

	public static function create_db_tables( ) {
		global $wpdb;
		$member_table = $wpdb->prefix . self::MEMBER_TABLE;
		$result = $wpdb->query('create table if not exists ' . $member_table . 
			' (member_id VARCHAR(5) NOT NULL,' .
			' last_name TEXT NOT NULL,' . 
			' first_name TEXT NOT NULL,' . 
			' expir_date DATE NOT NULL,' . 
			' constraint pk_' . $member_table . ' PRIMARY KEY (member_id))');
		if (false === $result) {
			error_log( 'Could not create table ' . $member_table . ': ' . $wpdb->last_error);
		}
	}

	public static function create_db_views( ) {
	}

	public static function drop_db_tables( ) {
	}

	public static function drop_db_views( ) {
	}

	public static function plugin_deactivation( ) {
		error_log( 'PWTC Mileage plugin deactivated' );
	}

	public static function plugin_uninstall() {
		error_log( 'PWTC Mileage plugin uninstall' );		
	}

	private static function bail_on_activation( $message, $deactivate = true ) {
?>
<!doctype html>
<html>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<style>
* {
	text-align: center;
	margin: 0;
	padding: 0;
	font-family: "Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;
}
p {
	margin-top: 1em;
	font-size: 18px;
}
</style>
<body>
<p><?php echo $message; ?></p>
</body>
</html>
<?php
		if ( $deactivate ) {
			$plugins = get_option( 'active_plugins' );
			$pwtcmileage = plugin_basename( PWTC_MILEAGE__PLUGIN_DIR . 'pwtc-mileage.php' );
			$update  = false;
			foreach ( $plugins as $i => $plugin ) {
				if ( $plugin === $pwtcmileage ) {
					$plugins[$i] = false;
					$update = true;
				}
			}

			if ( $update ) {
				update_option( 'active_plugins', array_filter( $plugins ) );
			}
		}
		exit;
	}
}