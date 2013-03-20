<?php
/*
adhocdirectmailer3.php sends a direct mail message to addressees from the database. In this instance, the addressees are all members in the trainers_table who were NOT Administrator-added (i.e. only members for which AddedByAdmin == 0).
Note:  I had 20 Friends out of 51 not-added-by-admin listings before running this mailer on 6/2/11.
*/

// Connect to my mysql database.
$db = mysql_connect('localhost', 'paulme6_merlyn', 'fePhaCj64mkik')
or die('Could not connect: ' . mysql_error());
mysql_select_db('paulme6_medtrainers') or die('Could not select database: ' . mysql_error());

// Query the trainers_table table to select TrainerName1, Username, Password, and EntityEmail for trainers where AddedByAdmin == 0.
$query = "SELECT TrainerName1, Username, Password, EntityEmail, MemberLevel FROM trainers_table WHERE AddedByAdmin = 0";
$result = mysql_query($query) or die('Query (select those trainer invitees who should receive the email message) failed: ' . mysql_error());

// Loop through the result set and send a message to each row.
while ($line = mysql_fetch_assoc($result))
	{

	// Create and send an HTML email. For the HTML email, I'm using a Mail package that readily handles MIME and email attachments. In order to run it, I needed to first install Mail on the server (see http://pear.php.net/manual/en/package.mail.mail.php) and Mail_mime (see http://pear.php.net/manual/en/package.mail.mail-mime.example.php) via cPanel's PEAR gateway, and then include() them (see below). 

	require_once('Mail.php');
	require_once('Mail/mime.php');

	// Formulate appropriate form for the dearline based on value of $TrainerName1 (which may be blank or null in the trainers_table)
	if ($line['TrainerName1'] == '' || is_null($line['TrainerName1']))
		{
		$dearline = 'Dear Member';
		}
	else
		{
		$dearline = 'Dear '.$line['TrainerName1'];
		}
	
	// Formulate body text for HTML version
	$messageHTML = "<html><body><table cellspacing='16'><tr><td style='font-family: Arial, Helvetica, sans-serif'>$dearline</td></tr>";
$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>As Administrator of the National Mediation Training Registry, I&rsquo;d like to remind you to update or add any training events in the Registry. Many site visitors search the NMTR site directly for training <em>events</em> rather than for trainers, so you&rsquo;ll attract greater awareness by posting event details. To add/update training events, click <b>Registry</b> on the main menu on any page of the NMTR site and login with your username = ".$line['Username']." and password = ".$line['Password'].".</td></tr>";
if ($line['MemberLevel'] == 'Associate') $messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>Besides that tip, if you haven&rsquo;t already, be sure to upgrade from Associate to Friend. It&rsquo;s free and provides several benefits:</td></tr>";
if ($line['MemberLevel'] == 'Associate') $messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>(1) Your trainer profile will appear in <strong>bold</strong>, above any Associate-level trainer when users search the Registry. (2) You&rsquo;ll receive a bio link and active links back to your site from our home page, trainer pages, and training events pages, driving traffic to your site and boosting your own site&rsquo;s search-engine ranking. (3) Whenever you post a new training event, an email will be sent to local trainees who&rsquo;ve requested email notifications of upcoming training events on our site.</td></tr>";
if ($line['MemberLevel'] == 'Associate') $messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>It&rsquo;s easy and free to become a Friend of the Registry. You just have to include a link back to the National Mediation Training Registry site from your own site. First, simply place the following snippet into the source code of any page on your site:</td></tr>";
if ($line['MemberLevel'] == 'Associate') $messageHTML .= "<tr><td style='font-family: Courier, serif'>Member of the &lt;a href=&quot;http://www.mediationtrainings.org&quot;&gt;National Mediation Training Registry&lt;/a&gt;</td></tr>";
if ($line['MemberLevel'] == 'Associate') $messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>Then log in to your Trainer Profile <a href='http://www.mediationtrainings.org/edittrainer.php'>here</a> (or click <b>Registry</b> on the main menu on any page of the NMTR site). Once logged in, click the radio button for 'Featured Trainer'. (Log in with your username = '".$line['Username']."', and password = '".$line['Password']."'.)</td></tr>";
$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>Finally, an update on our search ranking. Google currently ranks us 2nd and 3rd respectively for high-volume search terms &lsquo;mediation training&rsquo; and &lsquo;mediator training&rsquo;, garnering 26,000 searches in the U.S. and more than 2000 unique visitors to the Registry each month. That&rsquo;s a phenomenal achievement in a very crowded marketplace. We are clearly making a difference!</td></tr>";
$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>Sincerely</td></tr>";
$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>Paul Merlyn<br />";
$messageHTML .= "Administrator<br />";
$messageHTML .= "<strong>National Mediation Training Registry</strong><br />";
$messageHTML .= "<em>Learning to change the course of conflict</em><br />";
$messageHTML .= "844 California Street | San Francisco | CA 94108<br />";
$messageHTML .= "<a href='http://www.mediationtrainings.org'>www.mediationtrainings.org</a><br />";
$messageHTML .= "<a href='mailto:paul@mediationtrainings.org'>paul@mediationtrainings.org</a><br />";
$messageHTML .= "T. 415.378.7003<br />";
$messageHTML .= "F. 415.366.3005</td></tr></body></html>";

	// Formulate body text for plain text version
	$messageText = $dearline."\n\nAs Administrator of the National Mediation Training Registry, I'd like to remind you to update or add any training events in the Registry. Many site visitors search the NMTR site directly for training events rather than for trainers, so you&rsquo;ll attract greater awareness by posting event details. To add/update training events, click 'Registry' on the main menu on any page of the NMTR site and login with your username = ".$line['Username']." and password = ".$line['Password']."\n\n";
if ($line['MemberLevel'] == 'Associate') $messageText .= "Besides that tip, if you haven't already, be sure to upgrade from Associate to Friend. It's free and provides several benefits:\n\n";
if ($line['MemberLevel'] == 'Associate') $messageText .= "(1) Your trainer profile will appear in bold, above any Associate-level trainer when users search the Registry. (2) You'll receive a bio link and active links back to your site from our home page, trainer pages, and training events pages, driving traffic to your site and boosting your own site's search-engine ranking. (3) Whenever you post a new training event, an email will be sent to local trainees who've requested email notifications of upcoming training events on our site.\n\n";
if ($line['MemberLevel'] == 'Associate') $messageText .= "Then log in to your Trainer Profile at www.mediationtrainings.org/edittrainer.php (or click 'Registry' on the main menu on any page of the NMTR site). Once logged in, click the radio button for 'Featured Trainer'. (Log in with your username = '".$line['$Username']."', and password = '".$line['Password']."'.)\n\n";
if ($line['MemberLevel'] == 'Associate') $messageText .= "It's easy and free to become a Friend of the Registry. You just have to include a link back to the National Mediation Training Registry site from your own site. You'll find instructions when you log into your Trainer Profile.\n\n";
$messageText .= "Finally, an update on our search ranking. Google currently ranks us 2nd and 3rd respectively for high-volume search terms 'mediation training' and 'mediator training', garnering 26,000 searches in the U.S. and more than 2000 unique visitors to the Registry each month. That's a phenomenal achievement in a very crowded marketplace. We are clearly making a difference!\n\n";
$messageText .= "Sincerely\n
Paul Merlyn\n
Administrator\n
National Mediation Training Registry\n
Learning to change the course of conflict\n
844 California Street | San Francisco | CA 94108\n
paul@mediationtrainings.org
www.mediationtrainings.org\n
T. 415.378.7003\n;
F. 415.366.3005";

$sendto = $line['TrainerName1'].' <'.$line['EntityEmail'].'>'; /* Comment out this line via // when testing this script, replacing it with the next line instead */
// $sendto = 'paulmerlyn@yahoo.com';   /* Uncomment this line for test purposes */
$crlf = "\n";
$hdrs = array(
	              'From'    => 'Paul Merlyn <paul@mediationtrainings.org>',
    	          'Subject' => 'Become a Friend of the National Mediation Training Registry',
				  'Bcc' => 'paulmerlyn@yahoo.com'
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


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Ad Hoc Direct Mailer 3 Script</title>
</head>

<body>
<p>Your script has finished!</p>
</body>
</html>
