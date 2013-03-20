<?php
/*
adhocdirectmailer1.php sends a direct mail message to addressees from the database. In this instance, the addressees are Associate-level members in the trainers_table.
*/

// Connect to my mysql database.
$db = mysql_connect('localhost', 'paulme6_merlyn', 'fePhaCj64mkik')
or die('Could not connect: ' . mysql_error());
mysql_select_db('paulme6_medtrainers') or die('Could not select database: ' . mysql_error());

// Query the trainers_table table to select TrainerName1, Username, Password, and EntityEmail for trainers where MemberLevel == 'Associate'.
$query = "SELECT TrainerName1, Username, Password, EntityEmail FROM trainers_table WHERE MemberLevel = 'Associate'";
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
		$dearline = 'Dear Associate Member';
		}
	else
		{
		$dearline = 'Dear '.$line['TrainerName1'];
		}
	
	// Formulate body text for HTML version
	$messageHTML = "<html><body><table cellspacing='16'><tr><td style='font-family: Arial, Helvetica, sans-serif'>$dearline</td></tr>";
$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>As a member of the National Mediation Training Registry, I want to share with you the exciting news that the Registry now ranks in FIRST PLACE on Google for the search term &ldquo;mediation training&rdquo;. Users in the United States typed that search into Google approximately 13,000 times last month, and we ranked first in the search results!</td></tr>";
$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>Now that the Registry is attracting such high traffic, I strongly recommend you take advantage of the benefits of becoming a Featured Trainer i.e. change your membership from &lsquo;Associate-level&rsquo; to &lsquo;Friend-level&rsquo;.</td></tr>";
$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>It&rsquo;s easy and free to become a Friend of the Registry. You just have to include a link back to the National Mediation Training Registry site from your own site. First, simply place the following snippet into the source code of any page on your site:</td></tr>";
$messageHTML .= "<tr><td style='font-family: Courier, serif'>Member of the &lt;a href=&quot;http://www.mediationtrainings.org&quot;&gt;National Mediation Training Registry&lt;/a&gt;</td></tr>";
$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>Then log in to your Trainer Profile <a href='http://www.mediationtrainings.org/edittrainer.php'>here</a> (or click <b>Registry</b> on the main menu on any page of the NMTR site). Once logged in, click the radio button for 'Featured Trainer'. (Log in with username = '".$line['Username']."', and password = '".$line['Password']."'.)</td></tr>";
$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>In addition to driving significantly more traffic to your own site, as a Featured Trainer you will profit from these extra benefits:</td></tr>";
$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>* A <b>bold</b> trainer profile ahead of other trainers when users search the Registry.</td></tr>";
$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>* Active links to your bio and site from our home page, trainer pages, and training events pages, boosting your own site&rsquo;s search-engine ranking.</td></tr>";
$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>* Whenever you post a new training event, emails are sent to local trainees who&rsquo;ve requested notifications of upcoming training events in their area.</td></tr>";
$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>So please take advantage of these benefits by becoming a Friend of the National Mediation Training Registry today. We&rsquo;re thrilled to find ourselves at the pinnacle of Mt. Google! And together with your increased involvement, we can further our goal of changing the course of conflict.</td></tr>";
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
	$messageText = $dearline."\n\nAs a member of the National Mediation Training Registry, I want to share with you the exciting news that the Registry now ranks in FIRST PLACE on Google for the search term &ldquo;mediation training&rdquo;. Users in the United States typed that search into Google approximately 13,000 times last month, and we ranked first in the search results!\n\n";
$messageText .= "Now that the Registry is attracting such high traffic, I strongly recommend you take advantage of the benefits of becoming a Featured Trainer i.e. change your membership from 'Associate-level' to 'Friend-level'.\n\n";
$messageText .= "It's easy and free to become a Friend of the Registry. You just have to include a link back to the National Mediation Training Registry site from your own site. First, simply place the following snippet into the source code of any page on your site:\n\n";
$messageText .= "Member of the <a href='http://www.mediationtrainings.org'>National Mediation Training Registry<a>\n\n";
$messageText .= "Then log in to your Trainer Profile at www.mediationtrainings.org/edittrainer.php (or click 'Registry' on the main menu on any page of the NMTR site). Once logged in, click the radio button for 'Featured Trainer'. (Log in with username = '".$line['$Username']."', and password = '".$line['Password']."'.)\n\n";
$messageText .= "In addition to driving significantly more traffic to your own site, as a Featured Trainer you will profit from these extra benefits:\n\n";
$messageText .= "* A bold trainer profile ahead of other trainers when users search the Registry.\n\n";
$messageText .= "* Active links to your bio and site from our home page, trainer pages, and training events pages, boosting your own site&rsquo;s search-engine ranking.\n\n";
$messageText .= "* Whenever you post a new training event, emails are sent to local trainees who&rsquo;ve requested notifications of upcoming training events in their area.\n\n";
$messageText .= "So please take advantage of these benefits by becoming a Friend of the National Mediation Training Registry today. We're thrilled to find ourselves at the pinnacle of Mt. Google! And together with your increased involvement, we can further our goal of changing the course of conflict.\n\n";
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
<title>Ad Hoc Direct Mailer 2 Script</title>
</head>

<body>
<p>Your script has finished!</p>
</body>
</html>
