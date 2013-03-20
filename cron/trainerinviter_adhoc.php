<?php
/*
This script (originally developed as prospectinviterXX.php for nrmedlic to send email invitations to prospects for the New Resolution Platform) as a cron job, has been adapted for ad hoc manual execution instead. Its only purpose is to send an initial email invitation to a number (no more than 50 at the present time -- a limit imposed by my host, InMotion Hosting) of unique mediation trainer prospects in the trainerinvitees_table, unless that prospect has unsubscribed him/herself via unsubscriber.php (in which case his/her record in trainerinvitees_table will have the Unsubscribed column set to 1 (true)). After sending the message to each appropriate prospect, it must also record today's date/time in the DofLastInvtn field.
*/

// Start a session
session_start();
ob_start();

// Include these PEAR mail utilities.
require_once('Mail.php');
require_once('Mail/mime.php');

// Connect to my mysql database.
$db = mysql_connect('localhost', 'paulme6_merlyn', 'fePhaCj64mkik')
or die('Could not connect: ' . mysql_error());
mysql_select_db('paulme6_medtrainers') or die('Could not select database: ' . mysql_error());

// Query the trainerinvitees_table table to select various prospect data where NofMsgs is not 99 and Unsubscribed != 1.
// IF EDITING THIS QUERY, REMEMBER TO EDIT THE UPDATE QUERY BELOW AS WELL !! 
$query = "SELECT UseFormalTitle, Title, FirstName, LastName, Email, EntityName FROM trainerinvitees_table WHERE `DofLastInvtn` IS NULL AND ProspectID >= 243 AND Unsubscribed != 1";

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
			
	$sendto = $row['FirstName'].' '.$row['LastName'].' <'.$row['Email'].'>'; /* Comment out this line via // when testing this script, replacing it with the next line instead */
//	$sendto = 'paulmerlyn@yahoo.com';   /* Uncomment this line for test purposes */
	$crlf = "\n";
	$hdrs = array(
	              'From'    => 'National Mediation Training Registry <paul@mediationtrainings.org>',
	              'To'    => $row['FirstName'].' '.$row['LastName'].' <'.$sendto.'>',
				  'Subject' => $subject,
				  'Bcc' => 'paul@mediationtrainings.org'
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

$query = "UPDATE trainerinvitees_table SET DofLastInvtn = CURDATE() WHERE DofLastInvtn IS NULL AND ProspectID >= 243 AND Unsubscribed != 1";

$result = mysql_query($query) or die('Update to trainerinvitees_table for DofLastInvtn failed: ' . mysql_error());

echo '<br>The DofLastInvtn field in the trainerinvitees_table has been updated for each prospect to whom an email was sent<br>'; // This will either be echoed to the screen (if trainerinviter.php is invoked manually via the browser) or to an email that is sent to me through a setting on the cron jobs section of cpanel (if trainerinviter.php is invoked via a cron job).

ob_flush();
exit;
?>