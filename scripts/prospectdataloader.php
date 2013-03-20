<?
/* Source: http://www.webmasterworld.com/databases_sql_mysql/3268434.htm 
Note: an alternative script (recommended by InMotionHoting but not needed or tested by me) is available at: http://www.legend.ws/blog/tips-tricks/csv-php-mysql-import/ 
See http://dev.mysql.com/doc/refman/5.0/en/load-data.html for syntax details on info on reverse process, "SELECT * INTO OUTFILE ... */

/* In addition to uploading new "static" data (i.e. Locale, LocaleShort, StateStates, Population, and MaxLicenses) into locales_table, localedataloader.php also recalculates the "dynamic" data fields in locales_table (i.e. Exclusive, NofLicenses, and Full). */

// THESE ARE THE ONLY TWO LINES YOU'LL PROBABLY NEED TO CHANGE
//Configure file path and table name
$source_file = "/home/paulme6/public_html/medtrainings/dbdata/FMCS_Data_060512.csv";
$destination_table = "trainerinvitees_table";

//call to function
$luinfo = load_file_data($source_file, $destination_table);

// LOAD DATA INFILE
// EMPTY TABLE BEFORE LOAD
function load_file_data($source_file, $destable)
{ 
# first get a mysql connection as per the FAQ
$db = mysql_connect('localhost', 'paulme6_merlyn', 'fePhaCj64mkik')
or die('Could not connect: ' . mysql_error());
mysql_select_db('paulme6_medtrainers') or die('Could not select database: ' . mysql_error());

// Empty table
//$query = "DELETE FROM $destable";
//$result = mysql_query($query);

// do the data import (keep column names in the same order as in the DB table)
//$query = "LOAD DATA LOCAL INFILE \"$source_file\" INTO TABLE $destable";
$query = "LOAD DATA LOCAL INFILE \"$source_file\" INTO TABLE $destable FIELDS TERMINATED BY ',' (`Email`,`FirstName`,`LastName`)";

$result = mysql_query($query) or die("Query (The LOAD DATA INFILE operation failed: \nINPUT FILE: $source_file \nTABLE: $destable \nERROR: ".mysql_error());
if (!$result) {	echo 'No $result was achievable.'; };

echo 'Execution of loader script was successful. Data (i.e. Email, FirstName, and LastName) has been uploaded to the trainerinvitees_table. Examine the database table to check results.';
}

?> 