<?php
/*
This script (originally developed as prospectinviterXX.php for nrmedlic to send email invitations to prospects for the New Resolution Platform) is conventionally executed via a cron job. (It can also be executed manually by simply calling this file in my browser.) Its only purpose is to send an initial email invitation to a number (no more than 50 at the present time -- a limit imposed by my host, InMotion Hosting) of unique mediation trainer prospects in the trainerinvitees_table, unless that prospect has unsubscribed him/herself via unsubscriber.php (in which case his/her record in trainerinvitees_table will have the Unsubscribed column set to 1 (true)). After sending the message to each appropriate prospect, it must also record today's date/time in the DofLastInvtn field.
*/

// Start a session
session_start();
ob_start();

/* Determine the ProspectID's of prospects in the trainerinvitees_table who should receive a marketing solicitation when this script is next run (either manually or via a cron job). Note that we actually calculate the low and high values of the ProspectID range by using an integer multiplier that is obtained from the script's filename (strictly speaking, filepath). So, if the script, for example, were called trainerinviter6.php, then the multiplier ($RangeMultiplier) would be 6. This technique allows me to create one batch of 50 unique ProspectID's in each script file. If I needed to send emails to, say, 350 propsects, I might have seven files: trainerinviter1.php, trainerinviter2.php, trainerinviter3.php, ... , and trainerinviter7.php. */
$theFilepath = __FILE__; // Obtain this script's own file path (and thence filename - e.g. trainerinviter6.php). Note that my previous attempt (where I'd used $_SERVER['REQUEST_URI']) failed because it didn't return the file path when called by a cron script (only when called by typing the URL into a browser).
echo 'The filepath obtained via __FILE__ is: '.$theFilepath."\n";
$theFilename = strrchr($theFilepath, '/');
echo '<br>The filename obtained via strrchr is: '.$theFilename."\n";
$RangeMultiplier = preg_replace( '/[^\d]/', '', $theFilename); // Strip anything that isn't a digit from the REQUEST_URI file path

$ProspectIDLow = '1'; // Baseline
$ProspectIDLow = (int)$ProspectIDLow + ((int)$RangeMultiplier - 1) * 50; // Calculate actual $ProspectIDLow for this particular script. Note use of integer type when performing arithmetic on a string.

$ProspectIDHigh = (int)$ProspectIDLow + 49; // Similarly, calculate actual $ProspectIDHigh for this particular script.
$ProspectIDHigh = (string)$ProspectIDHigh; // Convert back to a string type after performing arithmetic to find upper limit of range

// Include these PEAR mail utilities.
require_once('Mail.php');
require_once('Mail/mime.php');

// Connect to my mysql database.
$db = mysql_connect('localhost', 'paulme6_merlyn', 'fePhaCj64mkik')
or die('Could not connect: ' . mysql_error());
mysql_select_db('paulme6_medtrainers') or die('Could not select database: ' . mysql_error());

echo '<br>$ProspectIDLow is: '.$ProspectIDLow;
echo '<br>$ProspectIDHigh is: '.$ProspectIDHigh;
// Query the trainerinvitees_table table to select various prospect data where NofMsgs is not 99 and Unsubscribed != 1.
$query = "SELECT UseFormalTitle, Title, FirstName, LastName, Email, EntityName FROM trainerinvitees_table WHERE ProspectID >= ".$ProspectIDLow." AND ProspectID <= ".$ProspectIDHigh." AND Unsubscribed != 1";

echo '<br>$query is: '.$query."\n\n";

$result = mysql_query($query) or die('Query (select prospects who have not yet been sent any messages) failed: ' . mysql_error());

while ($row = mysql_fetch_assoc($result))
	{
	echo '<br>Message sent to: '.$row['FirstName'].' '.$row['LastName'].', '.$row['Email'].', '.$row['EntityName']."\n"; // This will either be echoed to the screen (if trainerinviter.php is invoked manually via the browser) or to an email that is sent to me through a setting on the cron jobs section of cpanel (if trainerinviter.php is invoked via a cron job).

	// Formulate appropriate form of address based on value of $row['UseFormalTitle'], etc., for the "dear line" in the message.
	if ($row['UseFormalTitle'] == 1 && $row['Title'] != '' && $row['LastName'] != '') 
		{
		$dearline = 'Dear '.$row['Title'].' '.$row['LastName']; // Use a formal title (e.g. Dr. Thomas) if applicable.
		$dearline = stripslashes($dearline); // Converts from O\'Reilly to O'Reilly
		}	
	else if ($row['FirstName'] != '') $dearline = $row['FirstName'].',';
	else $dearline = 'Dear ADR Professional'; // Very unlikely to see use.
	$_SESSION['dearline'] = $dearline; // for use in SSI file emailsolicitationcontent.php

	// Assign the correct Email to $_SESSION['Email'], a session variable that is used by emailsolicitationcontent.php
	$_SESSION['Email'] = $row['Email'];
			
	// Assign the correct Email to $_SESSION['Email'], a session variable that is used by emailsolicitationcontent.php
	$_SESSION['EntityName'] = $row['EntityName'];

	require('/home/paulme6/public_html/medtrainings/ssi/emailsolicitationcontent.php'); // Important to place this SSI AFTER the definition of the $_SESSION['dearline'] and $_SESSION['Email'] b/c the included file incorporates these session variables. NOTE: I originally used a require_once() rather than a require() statement, but it causes a problem. If I use require(), the values of the session variables inside emailsolicitationcontent.php remain stuck at their values for the first row of results.
			
	$sendto = $row['Email']; /* Comment out this line via // when testing this script, replacing it with the next line instead */
//	$sendto = 'paulmerlyn@yahoo.com';   /* Uncomment this line for test purposes */
	$crlf = "\n";
	$hdrs = array(
	              'From'    => 'National Mediation Training Registry <paul@mediationtrainings.org>',
	              'To'    => $row['FirstName'].' '.$row['LastName'].' <'.$sendto.'>',
				  'Subject' => $subject
	              );

	$mime = new Mail_mime($crlf);
	$mime->setTXTBody($messageText);
	$mime->setHTMLBody($messageHTML);

	//do not ever try to call these lines in reverse order
	$body = $mime->get();
	$hdrs = $mime->headers($hdrs);

	$mail =& Mail::factory('mail');
	$mail->send("$sendto", $hdrs, $body);

	} 

// Finally, update trainerinvitees_table, incrementing DofLastInvtn .

// Disconnect then reconnect to my mysql database (I have found that the cron job issues a "MySQL server has gone away" error message otherwise. Reestablishing a DB connection is my attempt to prevent that.
mysql_close($db);
$db = mysql_connect('localhost', 'paulme6_merlyn', 'fePhaCj64mkik')
or die('Could not connect: ' . mysql_error());
mysql_select_db('paulme6_medtrainers') or die('Could not select database: ' . mysql_error());

$query = "UPDATE trainerinvitees_table SET DofLastInvtn = CURDATE() WHERE ProspectID >= ".$ProspectIDLow." AND ProspectID <= ".$ProspectIDHigh." AND Unsubscribed != 1";

$result = mysql_query($query) or die('Update to trainerinvitees_table for DofLastInvtn failed: ' . mysql_error());

echo '<br>The DofLastInvtn field in the trainerinvitees_table has been updated for each prospect to whom an email was sent<br>'; // This will either be echoed to the screen (if trainerinviter.php is invoked manually via the browser) or to an email that is sent to me through a setting on the cron jobs section of cpanel (if trainerinviter.php is invoked via a cron job).

ob_flush();
exit;
?>