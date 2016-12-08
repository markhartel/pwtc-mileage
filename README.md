# PWTC Mileage 
A Wordpress plugin to record, manage and report the mileage of riders in the [Portland Wheelmen Touring Club](http://pwtc.com).

## Installation
TBD

## Administration Pages
TBD
### View Reports
TBD
### Manage Riders
TBD
### Manage Ride Sheets
TBD
### Year-End Ops
TBD
### Settings
TBD

## Report Shortcodes
TBD
`pwtc_achievement_last_year`
`pwtc_mileage_year_to_date`
`pwtc_mileage_last_year`
`pwtc_mileage_lifetime`
`pwtc_rides_led_year_to_date`
`pwtc_rides_led_last_year`
`pwtc_rides_year_to_date`
`pwtc_rides_last_year`
`pwtc_led_rides_year_to_date`
`pwtc_led_rides_last_year`
`pwtc_posted_rides_wo_sheets`

## Package Files
- README.md *(this file)*
- pwtc-mileage.php *(plugin definition file)*
- class.pwtcmileage.php *(PHP class with server-side logic)*
- admin-gen-reports.php *(client-side logic for View Reports admin page)*
- admin-man-riders.php *(client-side logic for Manage Riders admin page)*
- admin-man-ridesheets.php *(client-side logic for Manage Ride Sheets admin page)*
- admin-man-yearend.php *(client-side logic for Year-End Ops admin page)*
- admin-man-settings.php *(client-side logic for Settings admin page)*
- admin-rider-lookup.php *(client-side logic for rider lookup dialog)*
- admin-scripts.js *(javascript utility functions for admin pages)*
- php-date-formatter.js *(javascript utility for PHP-style date formats - from [github](https://github.com/kartik-v/php-date-formatter))*
- php-date-formatter.min.js *(minimized version of above file - from [github](https://github.com/kartik-v/php-date-formatter))*
- admin-style.css *(stylesheet for admin pages)*
- reports-style.css *(stylesheet for report shortcodes)*
- datepicker.css *(stylesheet for Wordpress jQueryUI datepicker - from [github](https://github.com/stuttter/wp-datepicker-styling))*
