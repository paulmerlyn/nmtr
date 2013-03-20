<?php
// Start a session
session_start();

ob_start(); // Used in conjunction with ob_flush() [see www.tutorialized.com/view/tutorial/PHP-redirect-page/27316], this allows me to postpone issuing output to the screen until after the header has been sent.

// Create short variable names
$Title = $_POST['Title'];
$FirstName = $_POST['FirstName'];
$LastName = $_POST['LastName'];
$UseFormalTitle = $_POST['UseFormalTitle'];
$EntityName = $_POST['EntityName'];
$Email = $_POST['Email'];
$SendNow = $_POST['SendNow'];

// Create the hashcode, which will be passed as a string in a URL when a prospect clicks the "unsubscribe me" link in his/her email solicitation message.
$hashcode = 'polo'; // This secret key gets used to hash the prospect's email address. If you change the value of this key, make sure you change it also in scripts/unsubscriber.php.
$hashcode = $hashcode.$Email;
$hashcode = sha1($hashcode);
$hashcode = substr($hashcode, 0, 12); // Truncate, allowing only first 12 characters.

/*
Begin PHP form validation.
*/

// Create a session variable for the PHP form validation flag, and initialize it to 'false' i.e. assume it's valid.
$_SESSION['phpinvalidflag'] = false;

// Create session variables to hold inline error messages, and initialize them to blank.
$_SESSION['MsgTitle'] = null;
$_SESSION['MsgFirstName'] = null;
$_SESSION['MsgLastName'] = null;
$_SESSION['MsgEmail'] = null;

// Seek to validate $Title
$illegalCharSet = '[0-9~%\^\*_@\+`\|\$:";<>\?#!=]+'; // Exclude everything except A-Z, a-z, period, hyphen, apostrophe, &, slash, space, comma, and parentheses.
$reqdCharSet = "[A-Za-z]{2,}";  // At least two letters
if ($Title != '') // Since it's an optional field, only bother to validate if it's not empty.
	{
	if (ereg($illegalCharSet, $Title) || !ereg($reqdCharSet, $Title))
		{
		$_SESSION['MsgTitle'] = "<span class='errorphp'>Please enter a valid title (e.g. Dr. or Ms.) or leave blank.<br></span>";
		$_SESSION['phpinvalidflag'] = true; 
		};
	}

// Seek to validate $FirstName
$illegalCharSet = '[0-9~%@\^\*_\+`\|\$:";<>\?#&+=!,\(\)]+'; // Exclude everything except letters and dash and period and apostrophe.
$reqdCharSet = "[A-Za-z]{2,}";  // At least two letters
if ($FirstName != '') // Since it's an optional field, only bother to validate if it's not empty.
	{
	if (ereg($illegalCharSet, $FirstName) || !ereg($reqdCharSet, $FirstName))
		{
		$_SESSION['MsgFirstName'] = "<span class='errorphp'>Please enter a valid first name or leave blank.<br></span>";
		$_SESSION['phpinvalidflag'] = true; 
		};
	}

// Seek to validate $LastName
$illegalCharSet = '[0-9~%@\^\*_\+`\|\$:";<>\?#&+=!,\(\)]+'; // Exclude everything except letters and dash and period and apostrophe.
$reqdCharSet = "[A-Za-z]{2,}";  // At least two letters
if ($LastName != '') // Since it's an optional field, only bother to validate if it's not empty.
	{
	if (ereg($illegalCharSet, $LastName) || !ereg($reqdCharSet, $LastName))
		{
		$_SESSION['MsgLastName'] = "<span class='errorphp'>Please enter a valid surname or leave blank.<br></span>";
		$_SESSION['phpinvalidflag'] = true; 
		};
	}

// Seek to validate $Email
$reqdCharSet = '^[A-Za-z0-9\-_.]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.]+$';  // Simple validation from Welling/Thomson book, p125.
//if (!ereg($reqdCharSet, $Email)) // This is a required field.
//	{
//	$_SESSION['MsgEmail'] = '<span class="errorphp"><br>phpPlease check format. Use only alphanumerics (A-Z, a-z, 0-9), dash (-), period (.), @, and underscore (_) characters.<br></span>';
//	$_SESSION['phpinvalidflag'] = true; 
//	};

//Now go back to the previous page (admininvitetrainer.php) and show any PHP inline validation error messages if the $_SESSION['phpinvalidflag'] has been set to true. ... otherwise, proceed to update the database with the user's form data.
if ($_SESSION['phpinvalidflag'])
	{
	?>
	<script type='text/javascript' language='javascript'>history.back();</script>
	<noscript>
	<?php
	if (isset($_SERVER['HTTP_REFERER']))
		header("Location: ".$_SERVER['HTTP_REFERER']); // Go back to previous page. (Similar to echoing the Javascript statement: history.go(-1) or history.back() except I think $_SERVER['HTTP_REFERER'] reloads the page, which causes freshly entered values in the adminaddtrainer.php form to get overwritten by values retrieved from the DB. So the javascript 'history.back()' method is more suitable. However, if Javascript is enabled, php form validation is moot. And if Javascript is disabled, then the javascript 'history.back()' method won't work anyway.
	?>
	</noscript>
	<?php
	ob_flush();
	}

// End of PHP form validation
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>admininviterainer Slave Script</title>
<link href="/mediationtraining.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php
/* Prevent cross-site scripting via htmlspecialchars on these user-entry form field */
$EntityName = htmlspecialchars($EntityName, ENT_COMPAT);
$Title = htmlspecialchars($Title, ENT_COMPAT);
$FirstName = htmlspecialchars($FirstName, ENT_COMPAT);
$LastName = htmlspecialchars($LastName, ENT_COMPAT);

if (!get_magic_quotes_gpc())
{
	$Title = addslashes($Title);
	$FirstName = addslashes($FirstName);
	$LastName = addslashes($LastName);
	$EntityName = addslashes($EntityName);
}	

/* Manipulate user data prior to insertion into DB */
if ($UseFormalTitle != 1) $UseFormalTitle = 0;

/*
Store details of the trainer who will be invited to list in the directory via email in trainerinvitees_table.
*/

// Connect to mysql
$db = mysql_connect('localhost', 'paulme6_merlyn', 'fePhaCj64mkik')
or die('Could not connect: ' . mysql_error());
mysql_select_db('paulme6_medtrainers') or die('Could not select database: ' . mysql_error());

$query = "INSERT INTO trainerinvitees_table set Email = '".$Email."', Title = '".$Title."', FirstName = '".$FirstName."', LastName = '".$LastName."', EntityName = '".$EntityName."', UseFormalTitle = '".$UseFormalTitle."'";
// Append to the query for an update of the DofLastInvitn only if the SendNow check box was checked
if ($SendNow == 1)
	{
	$query .= ", DofLastInvtn = CURDATE()";
	}
$result = mysql_query($query) or die('Query (insert into trainerinvitees_table) failed: ' . mysql_error().' and the query string was: '.$query);


/*
If the 'Send Now' box was checked in the admininvitetrainer.php HTML form, create and send an HTML email to the trainer, inviting him/her to add his/her training entity to the directory. For the HTML email, I'm using a Mail package that readily handles MIME and email attachments. In order to run it, I needed to first install Mail on the server (see http://pear.php.net/manual/en/package.mail.mail.php) and Mail_mime (see http://pear.php.net/manual/en/package.mail.mail-mime.example.php) via cPanel's PEAR gateway, and then include() them (see below). 
*/

if ($SendNow == 1)
	{
	require_once('Mail.php');
	require_once('Mail/mime.php');

	// Formulate appropriate form of name personalization (i.e. $name) based on value of $UseFormalTitle, etc.
	if ($UseFormalTitle == 1 && $Title != '' && $LastName != '') 
		{
		$name = $Title.' '.$LastName; // Use a formal title (e.g. Dr. Thomas) if applicable.
		$name = stripslashes($name); // Converts from O\'Reilly to O'Reilly
		}
	else if ($FirstName != '') $name = $FirstName;
	else $name = 'ADR Professional';

	$messageHTML = "<html><body><table cellspacing='16'><tr><td style='font-family: Arial, Helvetica, sans-serif'>Hello ".$name."</td></tr>";
	$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>Did you know, almost 25,000 people typed <kbd>mediation training</kbd> or <kbd>mediator training</kbd> into Google last month? And did you know that the National Mediation Training Registry ranks 1st and 2nd respectively on Google for those search terms?</td></tr>";
	$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>If you&rsquo;re a mediation trainer and you&rsquo;re not in the Registry, you&rsquo;re missing out on the easiest and cheapest way to attract people to your training events.</td></tr>";
	$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>I invite you to include your training services in the National Mediation Training Registry. Joining takes just a minute and will bring your services to the attention of tens of thousands of  prospective clients who are <em>actively searching</em> right now for mediation training.</td></tr>";
	$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>To get registered, click <a href='http://www.mediationtrainings.org/join.php'>here</a>. Or simply point your browser to <a href='http://www.mediationtrainings.org'>www.mediationtrainings.org</a> and click the &lsquo;Join the Registry&rsquo; icon.</td></tr>";
	$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>Together, we can bring conflict resolution skills to more people and help prospective trainees connect with quality providers such as ";
	if ($EntityName != '') $messageHTML .= stripslashes($EntityName); else $messageHTML .= 'yourself';
	$messageHTML .= ".</td></tr>";
	$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>Sincerely</td></tr>";
	$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>Paul Merlyn<br />";
	$messageHTML .= "Administrator<br />";
	$messageHTML .= "<strong>National Mediation Training Registry</strong><br />";
	$messageHTML .= "<em>Learning to change the course of conflict</em><br />";
	$messageHTML .= "www.mediationtrainings.org<br />";
	$messageHTML .= "paul@mediationtrainings.org<br />";
	$messageHTML .= "415.378.7003 t<br />";
	$messageHTML .= "415.366.3005 f</td></tr>";
	$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif; font-size: 12px;'>If you don&rsquo;t want to receive further communications from the National Mediation Training Registry, click <a href='http://www.mediationtrainings.org/scripts/unsubscriber.php?code=".$hashcode."'>here</a>. Thank you, and please pardon the intrusion!</td></tr></body></html>";

	$messageText = "Hello ".$name."\n\nDid you know, almost 25,000 people typed 'mediation training' or 'mediator training' into Google last month? And did you know that the National Mediation Training Registry ranks 1st and 2nd respectively on Google for those search terms?\n\n";
	$messageText .= "If you're a mediation trainer and you're not in the Registry, you're missing out on the easiest and least expensive way to bring clients to your training events.\n\n";
	$messageText .= "I invite you to list in the National Mediation Training Registry. Joining takes just a minute and will bring your services to the attention of tens of thousands of prospective clients who are actively searching right now for mediation training.\n\n";
	$messageText .= "To get started, simply point your browser to http://www.mediationtrainings.org and click the Join the Registry icon.\n\n";
	$messageText .= "Together, we can bring conflict resolution skills to more people and help prospective trainees connect with quality providers such as ";
	if ($EntityName != '') $messageText .= $EntityName; else $messageText .= 'yourself';
	$messageText .= ".\n\n";
	$messageText .= "Sincerely\n
Paul Merlyn\n
Administrator\n
National Mediation Training Registry\n
Learning to change the course of conflict\n
www.mediationtrainings.org\n
paul@mediationtrainings.org";

	$sendto = $FirstName.' '.$LastName.' <'.$Email.'>'; // This line was previously just: $sendto = $Email;
	$crlf = "\n";
	$hdrs = array(
	              'From'    => 'Paul Merlyn <paul@mediationtrainings.org>',
    	          'Subject' => 'Invitation from the National Mediation Training Registry',
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
<div style="text-align: center"> <!-- This div provides centering for older browsers incl. NS4 and IE5. (See http://theodorakis.net/tablecentertest.html#intro.) Use of margin-left: auto and margin-right: auto in the style of the table itself (see below) takes care of centering in newer browsers. -->
<form method="post" action="unwind.php">
<table cellpadding="0" cellspacing="0" style="margin-top: 50px; margin-left: auto; margin-right: auto; position: relative; left: -7px;">
<tr>
<td style="text-align: left;">
<?php
if ($SendNow == 1)
	{
?>
<p class='basictext' style='margin-left: 50px; margin-right: 50px; margin-top: 40px; font-size: 14px;'>An email invitation has been successfully sent to trainer <?=$name; ?> (<?=$Email; ?>).</p>
<?php
	}
else
	{
?>
<p class='basictext' style='margin-left: 50px; margin-right: 50px; margin-top: 40px; font-size: 14px;'>No email was sent just yet, but trainer <?=$name; ?> (<?=$Email; ?>) has been added to the trainerinvitees_table and will receive an email with the next trainerinviterXX.php cron job.</p>
<?php
	}
?>
<p class='basictext' style='margin-left: 50px; margin-right: 50px; margin-bottom: 40px; font-size: 14px;'>Please click <a target='_self' style='font-size: 14px;' href='/index.php'>here</a> to go to the mediationtrainings.org home page. Or click <a style='font-size: 14px;' href='/admininvitetrainer.php'>here</a> to invite additional trainers to list in the directory.</p>
</td>
<tr>
<td style="text-align: center;">
<input type='button' name='Continue' class='buttonstyle' style="text-align: center;" value='Continue' onclick='javascript: window.location = "/admininvitetrainer.php";'> <!-- This is not a submit button and functions independenly of the action='unwind.php' form -->
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" name="Logout" class="buttonstyle" style="text-align: center;" value="Log Out">
</td>
</tr>
</table>
</form>
</div>

<?php
ob_end_flush();
?>
</body>
</html>