# PWTC Mileage 
A Wordpress plugin to record, manage and report the mileage of riders in the [Portland Wheelmen Touring Club](http://pwtc.com).

## Installation
TBD
### Database Schema 
TBD
### Plugin Setup
TBD
### Plugin Uninstall
TBD

## Administration Pages
TBD
### View Reports
TBD
### Manage Riders
TBD
### Manage Ride Sheets
TBD
### Datebase Ops
TBD
### Settings
TBD

## Report Shortcodes
TBD

Argument|Description|Values|Default
--------|-----------|------|-------
caption|Blah, blah, blah|"on" or "off"|"on"
show_id|Blah, blah, blah|"on" or "off"|"off"
highlight_user|Blah, blah, blah|"on" or "off"|"on"
sort_order|Blah, blah, blah|"asc" or "desc"|"asc"

### Mileage Reports
`[pwtc_achievement_last_year]`
`[pwtc_mileage_year_to_date]`
`[pwtc_mileage_last_year]`
`[pwtc_mileage_lifetime]`

### Ride Leader Reports
`[pwtc_rides_led_year_to_date]`
`[pwtc_rides_led_last_year]`

### Individual Ride Reports
`[pwtc_rides_year_to_date]`
`[pwtc_rides_last_year]`
`[pwtc_led_rides_year_to_date]`
`[pwtc_led_rides_last_year]`

### Administrative Reports
`[pwtc_posted_rides_wo_sheets]`

## Package Files
- README.md *(this file)*
- pwtc-mileage.php *(plugin definition file)*
- pwtc-mileage-hooks.php *(plugin membership hooks file)*
- class.pwtcmileage.php *(PHP class with non-admin server-side logic)*
- class.pwtcmileage-db.php *(PHP class with db access logic)*
- class.pwtcmileage-admin.php *(PHP class with admin server-side logic)*
- admin-gen-reports.php *(client-side logic for View Reports admin page)*
- admin-man-riders.php *(client-side logic for Manage Riders admin page)*
- admin-man-ridesheets.php *(client-side logic for Manage Ride Sheets admin page)*
- admin-man-yearend.php *(client-side logic for Year-End Ops admin page)*
- admin-man-settings.php *(client-side logic for Settings admin page)*
- admin-rider-lookup.php *(client-side logic for rider lookup dialog)*
- fpdf.php *(open source pdf generator - from [fpdf.org](http://www.fpdf.org))*
- helvetica.php *(font definition for pdf generator)*
- helveticai.php *(font definition for pdf generator)*
- helveticab.php *(font definition for pdf generator)*
- helveticabi.php *(font definition for pdf generator)*
- dbf_class.php *(open source DBF file reader - from [phpclasses.org](https://www.phpclasses.org/package/1302-PHP-Extract-information-from-a-DBF-database-file.html))*
- admin-scripts.js *(javascript utility functions for admin pages)*
- php-date-formatter.js *(javascript utility for PHP-style date formats - from [github](https://github.com/kartik-v/php-date-formatter))*
- php-date-formatter.min.js *(minimized version of above file - from [github](https://github.com/kartik-v/php-date-formatter))*
- admin-style.css *(stylesheet for admin pages)*
- reports-style.css *(stylesheet for report shortcodes)*
- datepicker.css *(stylesheet for Wordpress jQueryUI datepicker - from [github](https://github.com/stuttter/wp-datepicker-styling))*
- pwtc_logo.png *(image for membership card generation)*
