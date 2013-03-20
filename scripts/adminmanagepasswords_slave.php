<?php
/* 
This script is the slave processor for adminmanagepasswords.php, which allws the administrator to (i) add new username-password (non-encrypted) pairs to the userpass_table, and (ii) to look up usernames/passwords for a given password/username. It is based predominantly on admin10.php as created for the New Resolution Mediation Launch Platform administrator console.
*/
// Start a session
session_start();
ob_start(); // Used in conjunction with ob_flush() [see www.tutorialized.com/view/tutorial/PHP-redirect-page/27316], this allows me to postpone issuing output to the screen until after the header has been sent.
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Processing Slave Script for adminmanagepasswords.php</title>
<link href="/mediationtraining.css" rel="stylesheet" type="text/css">
</head>

<body>
<?php
// Create short variable names
$Password = $_POST['Password']; // Text field via administrator in admin9.php who is looking up a username for a given (non-encrypted) password.
$Username = $_POST['Username']; // Text field via administrator in admin9.php who is looking up a (non-encrypted) password for a given username.
$UPpairs = $_POST['UPpairs']; // Textarea submission in admin9.php containing new username-password pairs to be added
$LookUpUsername = $_POST['LookUpUsername'];
$LookUpPassword = $_POST['LookUpPassword'];
$SubmitNewUPpairs = $_POST['SubmitNewUPpairs'];

if (!get_magic_quotes_gpc())
{
	$Password = addslashes($Password);
	$Username = addslashes($Username);
	$UPpairs = addslashes($UPpairs);
}

// Prevent cross-site scripting
$Password = htmlspecialchars($Password, ENT_NOQUOTES); 
$Username = htmlspecialchars($Username, ENT_NOQUOTES); 
$UPpairs = htmlspecialchars($UPpairs, ENT_NOQUOTES); 

// Establish a DB connection
$db = mysql_connect('localhost', 'paulme6_merlyn', 'fePhaCj64mkik')
or die('Could not connect: ' . mysql_error());
mysql_select_db('paulme6_medtrainers') or die('Could not select database: ' . mysql_error());

if (isset($SubmitNewUPpairs))
{
unset($SubmitNewUPpairs);

/* Do PHP form manipulation and validation of submitted username-password pairs. To be valid, a username-password pair must be of form "username, password" when entered in admin9.php. Note that spaces have no affect. Multiple username-password pairs must be separated by a [newline] character. Usernames and passwords must be of at least 1 character in length, and they can only contain A-Z, a-z, 0-9, or underscore_ characters. Any username-password pair submission whose username or whose password is a duplicate of a username or password, respectively, that already exists in the database will not be inserted into the database table. */
$UPpairs = trim($UPpairs); // Remove any white space characters at the beginning and at the end of the string.
$UPpairs = str_replace(' ', '', $UPpairs); // Replace all spaces with nothing to tidy up username-password data prior to DB data entry.
$UPpairs = ereg_replace('[[:space:]]', '\n', $UPpairs); // Convert any linebreak characters to '\n'.
while (strstr($UPpairs, '\n\n') != false) // While there's at least one double line break in the string ...
	{
	$UPpairs = str_replace('\n\n', '\n', $UPpairs); // Remove any duplicate line breaks to promote data handling.
	};

// Split $UPpairs string of form doncaster,rovers\nsilver,lining\nred,rum into array form st UPpairsArray[0] = doncaster,rovers; UPpairsArray[1] = silver,lining; and UPpairsArray[2] = red,rum
$UPpairsArray = explode('\n', $UPpairs);
$UsernamesArray = array();
$PasswordsArray = array();
foreach ($UPpairsArray as $item)
	{
	$UParray = split(',', $item);  // $UParray[0] = the_username (e.g. silver), and $UParray[1] = the_password (e.g. lining) for each item in $UPpairsArray.
	array_push($UsernamesArray, $UParray[0]); // $UsernamesArray gets built up st $UsernamesArray[0] = doncaster, $UsernamesArray[1] = silver, etc.
	array_push($PasswordsArray, $UParray[1]); // $PasswordsArray gets built up st $PasswordsArray[0] = rovers, $PasswordsArray[1] = lining, etc.
	}

// Look for illegal characters and required characters in every element of both the $UsernamesArray[] and $PasswordsArray[]. If, say, $PasswordsArray[n] is invalid, then delete it as well as its pair $UsernamesArray[n], and vice versa.
$illegalCharSet = '/[^\w]+/'; // Exclude everything except word character i.e one or more alphanumeric characters or underscore.
$reqdCharSet = '/\w+/';  // Names of form word character i.e one or more alphanumeric characters or underscore.
$NofUPpairs = count($UsernamesArray);
for ($i=0; $i < $NofUPpairs; $i++)
	{
	if (preg_match($illegalCharSet, $UsernamesArray[$i]) || !preg_match($reqdCharSet, $UsernamesArray[$i]) || preg_match($illegalCharSet, $PasswordsArray[$i]) || !preg_match($reqdCharSet, $PasswordsArray[$i]))
		{
		unset($UsernamesArray[$i]); // Note: unsetting an array element still leaves the element's index in the array, even though the element itself no longer exists in the array i.e. the remaining elements retain their original indexes, and the array count is unchanged. The "deleted" element is merely unset.
		unset($PasswordsArray[$i]);
		}
	};
$UsernamesArray = array_values($UsernamesArray); // For neatness, create a new array with the keys reordered, omiting any elements that were unset.
$PasswordsArray = array_values($PasswordsArray);

// Remove duplicates from $UsernamesArray[] and $PasswordsArray[]. Do this by looking at whether the arrays $UsernamesArrayCounts (i.e. array_count_values($UsernamesArray)) and $PasswordsArrayCounts (i.e. array_count_values($PasswordsArray)) contain any values greater than 1. If $UsernamesArrayCounts[n] > 1 then unset $UsernamesArray[n] and $PasswordsArray[n]. Also, if $PasswordsArrayCounts[n] > 1 then unset $UsernamesArray[n] and $PasswordsArray[n].
$UsernamesArrayCounts = array_count_values($UsernamesArray);
$PasswordsArrayCounts = array_count_values($PasswordsArray);
for ($i=0; $i < count($UsernamesArray); $i++)
	{
	if ($UsernamesArrayCounts[$UsernamesArray[$i]] > 1 || $PasswordsArrayCounts[$PasswordsArray[$i]] > 1) // If we detect a $UsernamesArray[n] element whose count is more than 1 ...
		{
		unset($UsernamesArray[$i]); // Delete (i.e. unset) this element because either it or $PasswordsArray[$i] is a duplicate.
		unset($PasswordsArray[$i]); // Delete (i.e. unset) this element because either it or $UsernamesArray[$i] is a duplicate.
		$UsernamesArrayCounts = array_count_values($UsernamesArray); // Recalculate the $UsernamesArrayCounts array for $UsernamesArray post-deletion.
		$PasswordsArrayCounts = array_count_values($PasswordsArray); // Recalculate the $PasswordsArrayCounts array for $PasswordsArray post-deletion.
		}
	}
$UsernamesArray = array_values($UsernamesArray); // For neatness, create a new array with the keys reordered, omiting any elements that were unset.
$PasswordsArray = array_values($PasswordsArray);
	
/*
Having cleansed and validated the username-password pairs submitted by the Administrator, now insert them into userpass_table.
*/
// Note that IGNORE in the $query string ensures that a record (i.e. Username, Password, and Available values) will not be inserted if the new record duplicates any columns in the existing table that have been identified as Unique indexes. (To this end, userpass_table designates Username and Password as unique indexes.) See http://www.tutorialspoint.com/mysql/mysql-handling-duplicates.htm.
$NofUPpairs = count($UsernamesArray); // Recalculate this because it might be less after removal of duplicates above.
if ($NofUPpairs >= 1) // Don't bother to formulate a query if there are no username-password pairs to insert.
	{
	$query = "INSERT IGNORE INTO userpass_table (Username, Password, Available) VALUES ";
	for ($i=0; $i < $NofUPpairs-1; $i++) // Terminate at penultimate $NofUPpairs-1 rather than $NofUPpairs to avoid issue with extra comma being appended to $query and then needing removal.
		{
		$query .= "('".$UsernamesArray[$i]."','".$PasswordsArray[$i]."','1'),";
		};
	$query .= "('".$UsernamesArray[$i]."','".$PasswordsArray[$i]."','1')"; // The final appendage to $query doesn't have a comma at its end.
	$result = mysql_query($query) or die('Either you are not authenticated to insert Username, Password, and Available into the userpass_table or the insertion into the database failed. ' . mysql_error());
	}
	
/*
Also insert each Username-Password pair into the seo_board_users table so that a trainer in the Registry (who already has a username/password to log in to his/her trainer profile and/or add/edit training events) will be able to use that same username/password if he/she wants to (it's optional) log in to the Forum before posting comments, adding discussion topics, etc. Note that table seo_board_users requires its password (column = user_pass) to be encoded via sha1().
      Note alsothat IGNORE in the $query string ensures that a record (i.e. Username, Password, and Available values) will not be inserted if the new record duplicates any columns in the existing table that have been identified as Unique indexes. (To this end, userpass_table designates Username and Password as unique indexes.) See http://www.tutorialspoint.com/mysql/mysql-handling-duplicates.htm.
*/
if ($NofUPpairs >= 1) // Don't bother to formulate a query if there are no username-password pairs to insert.
	{
	$query = "INSERT IGNORE INTO seo_board_users (user_name, user_pass) VALUES ";
	for ($i=0; $i < $NofUPpairs-1; $i++) // Terminate at penultimate $NofUPpairs-1 rather than $NofUPpairs to avoid issue with extra comma being appended to $query and then needing removal.
		{
		$query .= "('".$UsernamesArray[$i]."','".sha1($PasswordsArray[$i])."'),";
		};
	$query .= "('".$UsernamesArray[$i]."','".sha1($PasswordsArray[$i])."')"; // The final appendage to $query doesn't have a comma at its end.
	$result = mysql_query($query) or die('Either you are not authenticated to insert user_name, user_pass into the seo_board_users or the insertion into the database failed. ' . mysql_error());
	}
	
if ($NofUPpairs >= 1)
	{
?>
	<p class='basictext' style='margin-left: 50px; margin-right: 50px; margin-top: 40px; font-size: 14px;'>The following username(s) and password(s) were submitted to the database. (Note: Usernames or passwords that are <em>duplicates</em> of usernames or passwords already stored in the database will not be inserted into the database.)</p><br><br>
	<div style="text-align: center"> <!-- This div provides centering for older browsers incl. NS4 and IE5. (See http://theodorakis.net/tablecentertest.html#intro.) Use of margin-left: auto and margin-right: auto in the style of the table itself (see below) takes care of centering in newer browsers. -->
	<table class="basictext" style="margin-left: auto; margin-right: auto;">
	<tr>
	<th width="120px;" align="left">Username</th>
	<th width="120px;" align="left">Password</th>
	</tr>
<?php
	for ($i=0; $i < $NofUPpairs; $i++)
		{
		echo '<tr>';
		echo '<td align="left"><span style="font-family: Courier, Times, serif;">'.$UsernamesArray[$i].'</span></td>';
		echo '<td align="left"><span style="font-family: Courier, Times, serif;">'.$PasswordsArray[$i].'</span></td>';
		echo '</tr>';
		}
?>
	</table>
	<br><br>
	</div>
<?php
	}
else // i.e. $NofUPpairs == 0
	{
?>
	<p class='basictext' style='margin-left: 50px; margin-right: 50px; margin-top: 40px; font-size: 14px;'>No valid username-password pairs were submitted for insertion into the database.</p><br>
<?php
	}
}

/* Provide the username associated with the password entered by the Administrator in admin9.php */
if (isset($_POST['Password']))
{
$query = "SELECT count(*) FROM userpass_table WHERE Password = '".$Password."'";
$result = mysql_query($query) or die('The SELECT query count of records for the given password failed i.e. '.$query.' failed: ' . mysql_error());
$row = mysql_fetch_row($result); // $row array contains just one value i.e. the value of the count of all records that match the password.
$NofMatches = $row[0]; // $row[0] (the only element in array $row) is the number of records that contained a Username == $Username. This number should either be zero or 1 because there should be no duplicate usernames (or passwords) in the table. So there's either one match or no matches.

// Proceed to either display the associated username for this password, or display a message saying no match could be found for that password.
switch ($NofMatches)
	{
	case 0:
		echo '<p class=\'basictext\' style=\'margin-left: 50px; margin-right: 50px; margin-top: 40px; font-size: 14px;\'>You entered the password <span style="font-family: Courier, Times, serif;">'.$Password.'</span>. Unable to look up the username for this password. No such password found in the database.</p>';
		break;
	case 1: // Now look up the Username for the matching Password.
		$query = "SELECT Username, Password FROM userpass_table WHERE Password = '".$Password."'";
		$result = mysql_query($query) or die('The SELECT for the given Password failed i.e. '.$query.' failed: ' . mysql_error());
		$row = mysql_fetch_assoc($result);
		echo '<br><br><table cellpadding="0" cellspacing="0" style="margin-left: auto; margin-right: auto; position: relative; left: -30px;"><tr style="height: 30px; vertical-align: top;"><td width="120"><span style="font-size: 14px; font-weight: bold; font-family: Arial, Helvetica, sans-serif;">Username</span></td><td><span style="font-size: 14px; font-weight: bold; font-family: Arial, Helvetica, sans-serif;">Password</span></td></tr><tr style="height: 18px;"><td><span style="font-family: Arial, Helvetica, sans-serif; font-size: 12px;">'.$row['Username'].'</span></td><td><span style="font-family: Arial, Helvetica, sans-serif; font-size: 12px;">'.$row['Password'].'</span></td></tr></table>';
		break;
	default:
		echo 'Warning: The userpass_table apparently has duplicate entries as detected by the switch statement in adminmanagepasswords.php.';
		break;
	}
unset($_POST['Password']);
unset($_POST['Username']);
}

/* Provide the password associated with the username entered by the Administrator in admin9.php */
if (isset($_POST['Username']))
{
$query = "SELECT count(*) FROM userpass_table WHERE Username = '".$Username."'";
$result = mysql_query($query) or die('The SELECT query count of records for the given username failed i.e. '.$query.' failed: ' . mysql_error());
$row = mysql_fetch_row($result); // $row array contains just one value i.e. the value of the count of all records that match the Username.
$NofMatches = $row[0]; // $row[0] (the only element in array $row) is the number of records that contained a Username == $Username. This number should either be zero or 1 because there should be no duplicate usernames (or passwords) in the table. So there's either one match or no matches.

// Proceed to either display the associated password for this username, or display a message saying no match could be found for that username.
switch ($NofMatches)
	{
	case 0:
		echo '<p class=\'basictext\' style=\'margin-left: 50px; margin-right: 50px; margin-top: 40px; font-size: 14px;\'>You entered the username <span style="font-family: Courier, Times, serif;">'.$Username.'</span>. Unable to look up the password for this username. No such username found in the database.</p>';
		break;
	case 1: // Now look up the Password for the matching Username.
		$query = "SELECT Username, Password FROM userpass_table WHERE Username = '".$Username."'";
		$result = mysql_query($query) or die('The SELECT for the given Username failed i.e. '.$query.' failed: ' . mysql_error());
		$row = mysql_fetch_assoc($result);
		echo '<br><br><table cellpadding="0" cellspacing="0" style="margin-left: auto; margin-right: auto; position: relative; left: -30px;"><tr style="height: 30px; vertical-align: top;"><td width="120"><span style="font-size: 14px; font-weight: bold; font-family: Arial, Helvetica, sans-serif;">Username</span></td><td><span style="font-size: 14px; font-weight: bold; font-family: Arial, Helvetica, sans-serif;">Password</span></td></tr><tr style="height: 18px;"><td><span style="font-family: Arial, Helvetica, sans-serif; font-size: 12px;">'.$row['Username'].'</span></td><td><span style="font-family: Arial, Helvetica, sans-serif; font-size: 12px;">'.$row['Password'].'</span></td></tr></table>';
		break;
	default:
		echo 'Warning: The userpass_table apparently has duplicate entries as detected by the switch statement in admin10.php.';
		break;
	}
unset($_POST['Password']);
unset($_POST['Username']);
}

?>

<div style="text-align: center"> <!-- This div provides centering for older browsers incl. NS4 and IE5. (See http://theodorakis.net/tablecentertest.html#intro.) Use of margin-left: auto and margin-right: auto in the style of the table itself (see below) takes care of centering in newer browsers. -->
<form method="post" action="/scripts/unwind.php">
<table cellpadding="0" cellspacing="0" style="margin-top: 50px; margin-left: auto; margin-right: auto; position: relative; left: -7px;">
<tr>
<td width="120" style="text-align: left;">
<input type='button' name='Continue' class='buttonstyle' value='Continue' onclick='javascript: window.location = "/adminmanagepasswords.php";'> <!-- This is not a submit button and functions independenly of the action='unwind.php' form -->
</td>
<td>&nbsp;</td>
<td width="120" style="text-align: left;">
<input type="submit" name="Logout" class="buttonstyle" value="Log Out">
</td>
</tr>
</table>
</form>
</div>

<?php
exit;
?>
</body>
</html>