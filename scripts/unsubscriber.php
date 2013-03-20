<?php
/* 
This script gets called when a mediation trainer prospect (from the trainerinvitees_table) clicks the "Remove Me" link in an email solication. The emails are typically sent in bulk via trainerinviter.php (run usually as cron job). The hyperlink will contain a hashed version of the prospect's email address. The hashing uses SHA-1 on a secret key (= white bear, yow, lower case) prepended to the email address. The result is then truncated to just the first 12 characters.
	unsubscriber.php simply goes through all the email addresses in the Email column of trainerinvitees_table to find a match. It then updates the Unsubscribed field for that particular trainer prospect, changing its value from the default of 0 (false) to 1 (true). It also sends me an email alerting me to the prospect's decision to unsubscribe.
*/

// Start a session
ob_start(); // Used in conjunction with ob_flush() [see www.tutorialized.com/view/tutorial/PHP-redirect-page/27316], this allows me to postpone issuing output to the screen until after the header has been sent.
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>National Mediation Training Registry | Removal of Prospect Data</title>
<link href="/mediationtraining.css" rel="stylesheet" type="text/css">
</head>

<body>
<?php
// Connect to mysql and select database.
$db = mysql_connect('localhost', 'paulme6_merlyn', 'fePhaCj64mkik')
or die('Could not connect: ' . mysql_error());
mysql_select_db('paulme6_medtrainers') or die('Could not select database: ' . mysql_error());
	
// First obtain the query string from the URL sought by the prospect who clicked the "Remove Me" hyperlink in his/her email solicitation message.
$queryString = $_SERVER['QUERY_STRING']; // courtesy http://www.webmasterworld.com/forum88/221.htm
$queryStringCode = $_GET['code']; // where the query string created in emailsolicitationcontent.php will have a format such as this: "code=af3uy678jg50"

// Formulate DB query to retrieve all prospects from trainerinvitees_table.
$query = "SELECT ProspectID, Email, FirstName, LastName, EntityName from trainerinvitees_table";
$result = mysql_query($query) or die('Your attempt to select ProspectID and Email has failed. Here is the query: '.$query.mysql_error());
// Now loop through the resultset to find the ProspectID (the key of the trainerinvitees_table) of the trainer prospect whose hash/encoded email address matches the value ($queryStringCode) extracted from the query string above.
 while ($row = mysql_fetch_assoc($result))
	{
	$hashcode = 'polo'; // This secret key gets used to hash the prospect's email address. If you change the value of this key, make sure you change it also in emailsolicitationcontent.php.
	$hashcode = $hashcode.$row['Email'];
	$hashcode = sha1($hashcode);
	$hashcode = substr($hashcode, 0, 12); // Truncate, allowing only first 12 characters.

	if ($hashcode == $queryStringCode)
		{
		// A match has been found. Now proceed to set the Unsubscribed field to 1 (true) for the prospect of corresponding ProspectID.
		$query = "UPDATE trainerinvitees_table SET Unsubscribed = 1 WHERE ProspectID = ".$row['ProspectID'];
		$result = mysql_query($query) or die('Update to trainerinvitees_table for Unsubscribed field failed: ' . mysql_error());
		
		// Display a confirmation message on the prospect's screen.
		echo '<div style="margin-left: 100px; margin-right: 100px; margin-top: 140px; font-family: Geneva, Arial, Helvetica, sans-serif; font-size: 14px;">Thank you. Your request has been received, and sorry to have troubled you! We won&rsquo;t send any additional information to '.$row['Email'].' about the National Mediation Training Registry.</div>';  
		echo '<div style="margin-left: 100px; margin-right: 100px; margin-top: 40px; font-family: Geneva, Arial, Helvetica, sans-serif; font-size: 14px;">Please feel free to contact us in the future if you&rsquo;d like to attract additional trainees to your training events. <a href=\'index.php\' style=\'color: #9C151C; font-weight: bold; text-decoration: none\' onclick=\'javascript: window.location = "/index.php"; return false;\'>Click here</a> to continue.</div>';  
		echo '<div style="margin-left: 100px; margin-right: 100px; margin-top: 60px; font-family: Geneva, Arial, Helvetica, sans-serif; text-align: center;"><img src="../images/mediationtraininglogo.jpg"></div>';  

		// Break out of the while loop (there's no reason to keep looking for other matches b/c the Email field is unique in the trainerinvitees_table.
		break; 
		}
	};
	
	// Send myself an email using PEAR Mail to alert me to this prospect's decision to unsubscribe.
	require_once('Mail.php');
	require_once('Mail/mime.php');
	
	$messageHTML .= "<html><body><table cellspacing='16'><tr><td style='font-family: Arial, Helvetica, sans-serif'><tr><td style='font-family: Arial, Helvetica, sans-serif'>Hello: This is an auto-generated message issued by unsubscriber.php to let you know that the following mediation trainer prospect received an email solicitation and has clicked a link in that message in order not to receive further communications from the National Mediation Training Registry:</td></tr>";
	$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>".$row['FirstName']." ".$row['LastName']."  |  ".$row['Email']."  |  ".$row['EntityName']."</td></tr>";
	$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>Sincerely</td></tr>";
	$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>National Mediation Training Registry (Autoresponder)</td></tr></body></html>";
	$sendto = 'paul@mediationtrainings.org';
	$crlf = "\n";
	$hdrs = array(
	              'From'    => 'NMTR <donotreply@mediationtrainings.org>',
    	          'Subject' => 'NMTR Prospect '.$row['FirstName'].' '.$row['LastName'].' Has Unsubscribed',
				  'Bcc' => 'paulmerlyn@yahoo.com'
	              );

	$mime = new Mail_mime($crlf);
	$mime->setHTMLBody($messageHTML);

	//do not ever try to call these lines in reverse order
	$body = $mime->get();
	$hdrs = $mime->headers($hdrs);

	$mail =& Mail::factory('mail');
	$mail->send("$sendto", $hdrs, $body);

	ob_flush();
?>
</body>
</html>
