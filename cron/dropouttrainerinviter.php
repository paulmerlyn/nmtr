<?php
/*
This script (originally developed as prospectinviterXX.php for nrmedlic to send email invitations to prospects for the New Resolution Platform) is conventionally executed via a cron job. (It can also be executed manually by simply calling this file in my browser.) Its only purpose is to send an email invitation to a number (no more than 50 at the present time -- a limit imposed by my host, InMotion Hosting) of unique mediation trainers in the trainers_table who had once been listed but who have since declined to list since I levied a fee for inclusion in the Registry. These trainers are identified as having status Approved = 0 AND PaidUp = 0. After sending the message, the script must also record today's date/time in the DofLastInvtn field.
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

// Query the trainers_table table to select various prospect data where PaidUp = 0 AND Approved = 0.
$query = "SELECT TrainerName1, EntityEmail, EntityName, Username, Password FROM trainers_table WHERE Approved = 0 AND PaidUp = 0";

echo '<br>$query is: '.$query."\n\n";

$result = mysql_query($query) or die('Query (select trainers who have not yet been sent any messages) failed: ' . mysql_error());

echo '<br>This is a list of recipients who were formerly listed in the Registry but now have Approved = 0 AND PaidUp = 0<br>';

while ($row = mysql_fetch_assoc($result))
	{
	echo '<br>Message sent to: '.$row['TrainerName1'].', '.$row['EntityEmail'].', '.$row['EntityName']."\n"; // This will either be echoed to the screen (if dropouttrainerinviter.php is invoked manually via the browser) or to an email that is sent to me through a setting on the cron jobs section of cpanel (if dropouttrainerinviter.php is invoked via a cron job).

	// Formulate appropriate form of address based on value of $row['UseFormalTitle'], etc., for the "dear line" in the message.
	if ($row['TrainerName1'] != '') $dearline = 'Dear '.$row['TrainerName1'];
	else $dearline = 'Dear Training Cordinator/Director of Training';
	$_SESSION['dearline'] = $dearline; // for use in SSI file emailsolicitationcontent.php

	// Assign the correct EntityName to $_SESSION['EntityName'], a session variable that is used by emailreactivationsolicitationcontent.php
	$_SESSION['EntityName'] = $row['EntityName'];

	// Assign the correct Username and Password to $_SESSION['Username'] and $_SESSION['Password'], session variables that are used by emailreactivationsolicitationcontent.php
	$_SESSION['Username'] = $row['Username'];
	$_SESSION['Password'] = $row['Password'];

	require('/home/paulme6/public_html/medtrainings/ssi/emailreactivationsolicitationcontent.php'); // Important to place this SSI AFTER the definition of the $_SESSION['dearline'] and $_SESSION['EntityEmail'] b/c the included file incorporates these session variables. NOTE: I originally used a require_once() rather than a require() statement, but it causes a problem. If I use require(), the values of the session variables inside emailreactivationsolicitationcontent.php remain stuck at their values for the first row of results.
			
	$sendto = $row['EntityEmail']; /* Comment out this line via // when testing this script, replacing it with the next line instead */
//	$sendto = 'paulmerlyn@sbcglobal.net';   /* Uncomment this line for test purposes */
	$crlf = "\n";
	$hdrs = array(
	              'From'    => 'National Mediation Training Registry <paul@mediationtrainings.org>',
	              'To'    => $row['TrainerName1'].' <'.$sendto.'>',
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

// Finally, update trainers_table, incrementing DofLastInvtn .

// Disconnect then reconnect to my mysql database (I have found that the cron job issues a "MySQL server has gone away" error message otherwise. Reestablishing a DB connection is my attempt to prevent that.
mysql_close($db);
$db = mysql_connect('localhost', 'paulme6_merlyn', 'fePhaCj64mkik')
or die('Could not connect: ' . mysql_error());
mysql_select_db('paulme6_medtrainers') or die('Could not select database: ' . mysql_error());

$query = "UPDATE trainers_table SET DofLastInvtn = CURDATE() WHERE Approved = 0 AND PaidUp = 0";

$result = mysql_query($query) or die('Update to trainers_table for DofLastInvtn failed: ' . mysql_error());

echo '<br>The DofLastInvtn field in the trainers_table has been updated for each trainer to whom an email was sent<br>'; // This will either be echoed to the screen (if trainerinviter.php is invoked manually via the browser) or to an email that is sent to me through a setting on the cron jobs section of cpanel (if trainerinviter.php is invoked via a cron job).

ob_flush();
exit;
?>