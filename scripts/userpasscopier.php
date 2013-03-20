<?php
/*
userpasscopier.php was created for one-time use only. Its purpose is to effect a batch copying of all the Username - Password pairs from userpass_table (the DB table used to record usernames and passwords of mediation trainers) into the user_name and user_pass pairs in the seo_board_users table (used to record usernames and passwords of individuals [who may or may not be trainers in the Registry] who have become registered (optional) participants in the Forum.
*/

// Connect to mysql
$db = mysql_connect('localhost', 'paulme6_merlyn', 'fePhaCj64mkik')
or die('Could not connect: ' . mysql_error());
mysql_select_db('paulme6_medtrainers') or die('Could not select database: ' . mysql_error());

// Obtain all the Username and Password values from userpass_table
$query = "SELECT Username, Password FROM userpass_table";
$result = mysql_query($query) or die('The SELECT Username, Password from userpass_table failed i.e. '.$query.' failed: ' . mysql_error());

// Insert each Username-Password pair into the seo_board_users table. Note that IGNORE in the $query string ensures that a record (i.e. Username, Password, and Available values) will not be inserted if the new record duplicates any columns in the existing table that have been identified as Unique indexes. (To this end, userpass_table designates Username and Password as unique indexes.) See http://www.tutorialspoint.com/mysql/mysql-handling-duplicates.htm.

while ($row = mysql_fetch_assoc($result))
	{
	$query = "INSERT IGNORE INTO seo_board_users (user_name, user_pass) VALUES ('".$row['Username']."', '".sha1($row['Password'])."')";
//	$query = "INSERT INTO seo_board_users SET user_name = '".$row['Username']."', SET user_pass = '".sha1($row['Password'])."'";
	$result1 = mysql_query($query) or die('The INSERT into seo_board_users failed i.e. '.$query.' failed: ' . mysql_error());
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Username/Password Copier</title>
</head>

<body>
The script has completed.
</body>
</html>
