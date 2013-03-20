<?php
/*
adhocdirectmailer1.php sends a direct mail message to addressees from the database. In this instance, the addressees are from the trainerinvitees_table, excluding any who already exist in the Registry by dint of being added by an Administrator (i.e. AddedByAdmin field == 1). I also manually deleted from the table any invitees who accepted the invitation (issued via admininvitetrainer.php) by then creating their own trainer profiles.
*/

// Connect to my mysql database.
$db = mysql_connect('localhost', 'paulme6_merlyn', 'fePhaCj64mkik')
or die('Could not connect: ' . mysql_error());
mysql_select_db('paulme6_medtrainers') or die('Could not select database: ' . mysql_error());

// Query the trainerinvitees_table table to select Email, Title, FirstName, LastName, EntityName, UseFormalTitle, and AddedByAdmin for invitees where AddedByAdmin == 0.
$query = "SELECT * FROM trainerinvitees_table WHERE Email = 'paul@cidresolution.com'";
// Uncomment the next line (and comment the one above) if you want to do a mailing to the full database rather than a mere test.
//$query = "SELECT * FROM trainerinvitees_table WHERE AddedByAdmin != 1";
$result = mysql_query($query) or die('Query (select those trainer invitees who should receive the email message) failed: ' . mysql_error());

// Loop through the result set and send a message to each row.
while ($line = mysql_fetch_assoc($result))
	{

	// Create and send an HTML email. For the HTML email, I'm using a Mail package that readily handles MIME and email attachments. In order to run it, I needed to first install Mail on the server (see http://pear.php.net/manual/en/package.mail.mail.php) and Mail_mime (see http://pear.php.net/manual/en/package.mail.mail-mime.example.php) via cPanel's PEAR gateway, and then include() them (see below). 

	require_once('Mail.php');
	require_once('Mail/mime.php');

	// Formulate appropriate form of name personalization (i.e. $name) based on value of $UseFormalTitle, etc.
	if ($line['UseFormalTitle'] == 1 && $line['Title'] != '' && $line['LastName'] != '') $name = $line['Title'].' '.$line['LastName']; // Use a formal title (e.g. Dr. Thomas) if applicable.
	else if ($line['FirstName'] != '') $name = $line['FirstName'];
	else $name = 'Mediation Trainer';
	
	// Formulate body text for HTML version
	$messageHTML = "<html><body><table cellspacing='16'><tr><td style='font-family: Arial, Helvetica, sans-serif'>Hello ".$name."</td></tr>";
$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>Exciting news! Thanks in part to the number of mediation trainers who have joined the Registry, the <a href='http://www.mediationtrainings.org'>National Mediation Training Registry</a> now ranks 1st on Google for the high-volume search term &ldquo;mediation training&rdquo;. It&rsquo;s also on the first page of Google for the even higher volume search term &ldquo;mediation training&rdquo;. In addition, we draw significant referral traffic from Wikipedia.</td></tr>";
$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>If you haven&rsquo;t already, be sure to include your organization in the Registry. Signing up is free and takes just a minute, and it makes your services more visible to tens of thousands of potential trainees.</td></tr>";
$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>As a member of the Registry, you&rsquo;ll be joining other prominent mediation trainers from across the United States who&rsquo;ve already listed in the Registry &ndash; including Northwestern University, Seattle University School of Law, Mediation Works Incorporated, and the Washington DC Center for Dispute Settlement. Just click <a href='http://www.mediationtrainings.org/addtrainer.php'>here</a> to list your training services.</td></tr>";
$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>Perhaps best of all &mdash; and in-keeping with the highest standards of permission-based marketing &mdash; the Registry automatically sends an email to local prospective trainees who&rsquo;ve signed up to receive notifications of upcoming training events in your area code.</td></tr>";
$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>I look forward to welcoming you as the newest member of the Registry. In the meantime, please contact me at: <a href='mailto:paul@mediationtrainings.org'>paul@mediationtrainings.org</a> if you need help or support.</td></tr>";
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
	$messageText = "Hello ".$name."\n\nExciting news! Thanks in part to the number of mediation trainers who have joined the Registry, the National Mediation Training Registry now ranks 1st on Google for the high-volume search term &ldquo;mediation training&rdquo;. We also draw significant referral traffic from Wikipedia.\n\n";
$messageText .= "If you haven't already, be sure to include your organization in the Registry. Signing up is free and takes just a minute, and it makes your services more visible to tens of thousands of potential trainees.\n\n";
$messageText .= "As a member of the Registry, you'll be joining other prominent mediation trainers from across the United States who've already listed in the Registry &ndash; including Northwestern University, Seattle University School of Law, Mediation Works Incorporated, and the Washington DC Center for Dispute Settlement. Just visit www.mediationtrainings.org and click the 'Include Me' icon near the top-left of the page to list your training services.\n\n";
$messageText .= "Perhaps best of all -- and in-keeping with the highest standards of permission-based marketing -- the Registry automatically sends an email to local prospective trainees who&rsquo;ve signed up to receive notifications of upcoming training events in your area code.\n\n";
$messageText .= "I look forward to welcoming you as the newest member of the Registry. In the meantime, please email me (paul@mediationtrainings.org) if you need help or support.\n\n";
$messageText .= "Or, simply visit www.mediationtrainings.org, then click the 'Include Me' icon towards the top-left corner of the page.\n\n";
$messageText .= "If you have any questions or need additional support, please contact me at: info@mediationtrainings.org. In the meantime, welcome to the Registry! Together, we can bring conflict resolution skills to more people and help prospective trainees connect with quality providers such as ";
if ($line['EntityName'] != '') $messageText .= $line['EntityName']; else $messageText .= 'yourself';
$messageText .= ".\n\n";
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

$sendto = $name.' <'.$line['Email'].'>'; /* Comment out this line via // when testing this script, replacing it with the next line instead */
$sendto = $line['Email'];
$crlf = "\n";
$hdrs = array(
	              'From'    => 'Paul Merlyn <paul@mediationtrainings.org>',
    	          'Subject' => 'Register your services with the National Mediation Training Registry',
				  'Bcc' => 'kokomomonkey@gmail.com'
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
<title>Ad Hoc Direct Mailer 1 Script</title>
</head>

<body>
<p>Your script has finished!</p>
</body>
</html>
