# PWTC Mileage 
This is a Wordpress plugin used to record, manage and report the mileage of riders in the [Portland Wheelmen Touring Club](http://pwtc.com).

## Installation
Download this distribution as a zip file, login to the pwtc.com Wordpress website as 
admin and upload this zip file as a new plugin. This plugin will be named **PWTC Mileage**,
activate it from the Plugins management page. After activation, this plugin will create
five new tables and twelve new views in the Wordpress database. A **Rider Mileage** menu group
will be added to the admin menu bar and shortcodes created to allow you to add mileage-related 
content to your pages. A new user role named **statistician** will be created to allows 
a user access to the **Rider Mileage** menu group pages.
### Plugin Uninstall
Deactivate and then delete the **PWTC Mileage** plugin from the Plugins management page.
Normally, the **PWTC Mileage** database tables and views will **not** be dropped. To
force a drop of these tables and views when the plugin is deleted, select the 
"Drop Tables/Views Upon Plugin Delete" option on the Rider Mileage Settings page.
**Warning:** all mileage data stored in these tables will be lost if you choose this option
and delete the plugin.

## Rider Mileage Admin Pages
This menu group is added to the Wordpress admin menu bar. Users with the **administrator**
and **statistician** roles will have the right to access these menu pages.
### Manage Ride Sheets
This menu page allows a user to create, edit and delete ridesheets. A ridesheet records 
the leaders of a ride and the mileage of the riders. The title and date of the ride is
also recorded.
### Manage Riders
This menu page allows a user to create, edit and delete riders.
### View Reports
This menu page allows a user to view and download rider mileage reports.
### Datebase Ops
This menu page allows a user to perform batch operations on the mileage database.
### Settings
This menu page allows a user to adjust the settings of this plugin. It is only
available to users with the **administrator** role and is located under the **Settings** 
admin menu group as a menu item labeled **Rider Mileage**.

## Rider Mileage Report Shortcodes
These shortcodes allow users to insert rider mileage related content into Wordpress
pages. For example, if you place the following text string into your page content, it will 
render as a table that displays all riders and their mileage (ordered by rider name) that 
have ridden at least 100 miles this year:

`[pwtc_mileage_year_to_date sort_by="name" minimum="100"]`

### Mileage Report Shortcodes
`[pwtc_mileage_year_to_date]` *tabular display of year-to-date mileage for all riders*

`[pwtc_mileage_last_year]` *tabular display of last year's mileage for all riders*

`[pwtc_mileage_lifetime]` *tabular display of lifetime mileage for all riders*

Argument|Description|Values|Default
--------|-----------|------|-------
caption|show the table caption|"on", "off"|"on"
show_id|show the ID of riders|"on", "off"|"off"
highlight_user|highlight the row of the logged-in user|"on", "off"|"on"
sort_order|control the table sort ordering|"asc", "desc"|"asc"
sort_by|sort the table by mileage or name|"mileage", "name"|"mileage"
minimum|minimum mileage to display|number|1

### Ride Leader Report Shortcodes
`[pwtc_rides_led_year_to_date]` *tabular display of year-to-date number of rides led by all riders*

`[pwtc_rides_led_last_year]` *tabular display of last year's number of rides led by all riders*

Argument|Description|Values|Default
--------|-----------|------|-------
caption|show the table caption|"on", "off"|"on"
show_id|show the ID of riders|"on", "off"|"off"
highlight_user|highlight the row of the logged-in user|"on", "off"|"on"
sort_order|control the table sort ordering|"asc", "desc"|"asc"
sort_by|sort the table by number of rides led or name|"rides_led", "name"|"rides_led"
minimum|minimum number of rides led to display|number|1

`[pwtc_posted_rides_wo_sheets]` *tabular display of posted rides that are missing ridesheets*

Argument|Description|Values|Default
--------|-----------|------|-------
caption|show the table caption|"on", "off"|"on"

### Individual Rider Report Shortcodes
`[pwtc_rider_report]` *textual display of mileage and leader info for logged-in user*

Argument|Description|Values|Default
--------|-----------|------|-------
type|display mileage or leader info|"both", "mileage", "leader"|"both"

`[pwtc_rides_year_to_date]` *tabular display of year-to-date rides ridden by logged-in user*

`[pwtc_rides_last_year]` *tabular display of last year's rides ridden by logged-in user*

`[pwtc_led_rides_year_to_date]` *tabular display of year-to-date rides led by logged-in user*

`[pwtc_led_rides_last_year]` *tabular display of last year's rides led by logged-in user*

Argument|Description|Values|Default
--------|-----------|------|-------
caption|show the table caption|"on", "off"|"on"

## Rider Mileage Database Schema
The following tables and views are created by this plugin:

Table `pwtc_membership` is used to contain ...

Table Column|Description|Data Type|Comment
------------|-----------|---------|-------
member_id|rider membership ID|varchar(5)|key
last_name|rider last name|text| 
first_name|rider first name|text| 
expir_date|rider membership expiration date|date| 

Table `pwtc_club_rides` is used to contain ...

Table Column|Description|Data Type|Comment
------------|-----------|---------|-------
ID|club ride ID|bigint(20)|key, auto increment, unsigned
title|club ride title|text| 
date|club ride event date|date| 
post_id|ID of posted ride|bigint(20)|unsigned, default(0)

Table `pwtc_ride_mileage` is used to contain ...

Table Column|Description|Data Type|Comment
------------|-----------|---------|-------
member_id|rider membership ID|varchar(5)|key
ride_id|club ride ID|bigint(20)|key, unsigned
mileage|rider's mileage for this ride|int(10)|unsigned

Table `pwtc_ride_leaders` is used to contain ...

Table Column|Description|Data Type|Comment
------------|-----------|---------|-------
member_id|rider membership ID|varchar(5)|key
ride_id|club ride ID|bigint(20)|key, unsigned
rides_led|rider led this ride|int(10)|unsigned

Table `pwtc_running_jobs` is used to contain ...

Table Column|Description|Data Type|Comment
------------|-----------|---------|-------
job_id|ID of job|varchar(20)|key
status|status of job|text| 
timestamp|job start time|bigint(20)|unsigned
error_msg|job termination message|text| 

The following views are used to generate rider mileage and leader reports:
- `pwtc_lt_miles_vw` *lifetime mileage view*
- `pwtc_ytd_miles_vw` *year-to-date mileage view*
- `pwtc_ly_miles_vw` *last year's mileage view*
- `pwtc_ly_lt_miles_vw` *last year's lifetime mileage view*
- `pwtc_ybl_lt_miles_vw` *year before last's lifetime mileage view*
- `pwtc_ly_lt_achvmnt_vw` *last year's lifetime achiviement view*
- `pwtc_ytd_rides_led_vw` *year-to-date rides led list view*
- `pwtc_ly_rides_led_vw` *last year's rides led list view*
- `pwtc_ytd_led_vw` *year-to-date number of rides led view*
- `pwtc_ly_led_vw` *last year's number of rides led view*
- `pwtc_ytd_rides_vw` *year-to-date rides ridden list view*
- `pwtc_ly_rides_vw` *last year's rides ridden list view*

## Package Files Used By This Plugin
- `README.md` *this file*
- `pwtc-mileage.php` *plugin definition file*
- `pwtc-mileage-hooks.php` *plugin membership hooks file*
- `class.pwtcmileage.php` *PHP class with non-admin server-side logic*
- `class.pwtcmileage-db.php` *PHP class with db access logic*
- `class.pwtcmileage-admin.php` *PHP class with admin server-side logic*
- `admin-gen-reports.php` *client-side logic for View Reports admin page*
- `admin-man-riders.php` *client-side logic for Manage Riders admin page*
- `admin-man-ridesheets.php` *client-side logic for Manage Ride Sheets admin page*
- `admin-man-yearend.php` *client-side logic for Year-End Ops admin page*
- `admin-man-settings.php` *client-side logic for Settings admin page*
- `admin-rider-lookup.php` *client-side logic for rider lookup dialog*
- `fpdf.php` *open source pdf generator - from [fpdf.org](http://www.fpdf.org)*
- `helvetica.php` *font definition for pdf generator*
- `helveticai.php` *font definition for pdf generator*
- `helveticab.php` *font definition for pdf generator*
- `helveticabi.php` *font definition for pdf generator*
- `dbf_class.php` *open source DBF file reader - from [phpclasses.org](https://www.phpclasses.org/package/1302-PHP-Extract-information-from-a-DBF-database-file.html)*
- `admin-scripts.js` *javascript utility functions for admin pages*
- `php-date-formatter.js` *javascript utility for PHP-style date formats - from [github](https://github.com/kartik-v/php-date-formatter)*
- `php-date-formatter.min.js` *minimized version of above file - from [github](https://github.com/kartik-v/php-date-formatter)*
- `admin-style.css` *stylesheet for admin pages*
- `reports-style.css` *stylesheet for report shortcodes*
- `datepicker.css` *stylesheet for Wordpress jQueryUI datepicker - from [github](https://github.com/stuttter/wp-datepicker-styling)*
- `pwtc_logo.png` *image for membership card generation*
