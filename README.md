# PWTC Mileage 
This is a Wordpress plugin used to record, manage and report the mileage of riders in the [Portland Wheelmen Touring Club](http://pwtc.com).

## Installation
Download this GitHub distribution as a zip file, login to the pwtc.com Wordpress website as 
admin and upload this zip file as a new plugin. This plugin will be named **PWTC Mileage**,
activate it from the Plugins management page. After activation, this plugin will create
five new tables and twelve new views in the Wordpress database. A **Rider Mileage** menu group
will be added to the admin menu bar and shortcodes created to allow you to add mileage-related 
content to your pages. A new user role named **statistician** will be created to allows 
a user access to the **Rider Mileage** menu group pages.
### Plugin Uninstall
TBD

## Rider Mileage Menu Group Pages
TBD
### Manage Ride Sheets
TBD
### Manage Riders
TBD
### View Reports
TBD
### Datebase Ops
TBD
### Settings
TBD

## Rider Mileage Report Shortcodes
TBD

### Mileage Report Shortcodes
`[pwtc_mileage_year_to_date]` *Display year-to-date mileage for riders*

`[pwtc_mileage_last_year]` *Display last year's mileage for riders*

`[pwtc_mileage_lifetime]` *Display lifetime mileage for riders*

Argument|Description|Values|Default
--------|-----------|------|-------
caption|show table caption|"on" or "off"|"on"
show_id|show rider ids|"on", "off"|"off"
highlight_user|highlight row for logged-in user|"on", "off"|"on"
sort_order|table sort order|"asc", "desc"|"asc"
sort_by|table sort type|"mileage", "name"|"mileage"
minimum|minimum mileage to display|number|1

### Ride Leader Report Shortcodes
`[pwtc_rides_led_year_to_date]` *Display year-to-date number of rides lead by riders*

`[pwtc_rides_led_last_year]` *Display last year's number of rides lead by riders*

Argument|Description|Values|Default
--------|-----------|------|-------
caption|show table caption|"on" or "off"|"on"
show_id|show rider ids|"on", "off"|"off"
highlight_user|highlight row for logged-in user|"on", "off"|"on"
sort_order|table sort order|"asc", "desc"|"asc"
sort_by|table sort type|"rides_led", "name"|"rides_led"
minimum|minimum number of rides led to display|number|1

`[pwtc_posted_rides_wo_sheets]` *Display posted rides that are missing ridesheets*

### Individual Rider Report Shortcodes
`[pwtc_rider_report]`

Argument|Description|Values|Default
--------|-----------|------|-------
type|Blah, blah, blah|"both", "mileage", "leader"|"both"

`[pwtc_rides_year_to_date]`

`[pwtc_rides_last_year]`

`[pwtc_led_rides_year_to_date]`

`[pwtc_led_rides_last_year]`

## Package Files Used By This Plugin
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
