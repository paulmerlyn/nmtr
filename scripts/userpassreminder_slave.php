<?php
/*
userpassreminder_slave.php is the slave script of userpassreminder.php. It takes the form input (an email address) and looks up the trainer's TrainerID in trainers_table and then uses that ID to retrieve his/her Username and Password in userpass_table. If it can't relate the email address submitted to a TrainerID, then it issues a message on the screen. If it can relate the email address to a TrainerID, it issues a "success" message on the screen and sends an email to that address with the username/password.
*/

// Start a session
session_start();

ob_start(); // Used in conjunction with ob_flush() [see www.tutorialized.com/view/tutorial/PHP-redirect-page/27316], this allows me to postpone issuing output to the screen until after the header has been sent.

// Short variable names
$TrainerEmail = $_POST['TrainerEmail'];
$RemindMe = $_POST['RemindMe'];

// Sanitize variables to prevent cross-site scripting
$TrainerEmail = htmlspecialchars($TrainerEmail);

if (!get_magic_quotes_gpc())
{
	$TrainerEmail = addslashes($TrainerEmail);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Username/Password Reminder | Slave</title>
<meta NAME="description" CONTENT="Slave script for reminder of a forgotten username/password">
<link href="/mediationtraining.css" rel="stylesheet" type="text/css">
</head>

<body style="margin: 0; padding: 0; height: 100%;">
<?php

// User has clicked the 'RemindMe' button in userpassreminder.php to submit his/her email address, so now process the form data.
$db = mysql_connect('localhost', 'paulme6_merlyn', 'fePhaCj64mkik')
or die('Could not connect: ' . mysql_error());
mysql_select_db('paulme6_medtrainers') or die('Could not select database: ' . mysql_error());

// Count the number of rows in trainers_table whose EntityEmail column matches the $TrainerEmail submitted via the form. The count should be either zero (no matches) or 1 (one match). It would unlikely be more than one because each trainer should ordinarily only sign up for inclusion in the Registry once with one email address. However, it's possible that the same value of EntityEmail appears in more than one row of trainers_table. If the matching EntityEmail is not unique to a single row, then I can't ascertain a unique username-password pair for that email address and so I have to deny the user's attempt to obtain a reminder.
$matchfound = false; // Initialize to false
$query = "SELECT COUNT(*), TrainerID FROM trainers_table WHERE EntityEmail = '".$TrainerEmail."'";
$result = mysql_query($query) or die('The SELECT count of trainers_table failed i.e. '.$query.' failed: ' . mysql_error());
$row = mysql_fetch_row($result);
$thecount = $row[0];
$theTrainerID = $row[1];
if ($thecount == 1) $matchfound = true;
	
if ($matchfound)
	{
	// Now retrieve the Username/Password from userpass_table that corresponds to the uniquely matching $theTrainerID.
	$query = "SELECT Username, Password FROM userpass_table WHERE AssignedtoTrainerID = ".$theTrainerID;
	$result = mysql_query($query) or die('The SELECT of Username and Password from userpass_table failed i.e. '.$query.' failed: ' . mysql_error());
	$row = mysql_fetch_assoc($result);
	?>
	<div style="text-align: center; position: absolute; top: 30%; left: 50%; height: 140px; margin-top: 0px; width: 480px; margin-left: -240px; margin-right: auto; border: 2px solid #444444; border-color: #9C151C; padding: 15px; padding-bottom: 35px;">
	<h4 class="forms">A username and password were successfully retrieved for this email address.<br clear="all"><br />The username and password have now been sent to you at:<br /><br /><br /><kbd style="text-transform: lowercase; font-size: 14px; font-weight: normal;"><?=$TrainerEmail; ?></kbd></h4><br />
	<form method="post" action="/index.php">
	<input type="submit" value="Continue" class="buttonstyle">
	</form>
	</div>
	<?php
	/* Create and send an HTML email to trainer with username and password reminder/confirmation. I'm using a Mail package that readily handles MIME and email attachments. In order to run it, I needed to first install Mail on the server (see http://pear.php.net/manual/en/package.mail.mail.php) and Mail_mime (see http://pear.php.net/manual/en/package.mail.mail-mime.example.php) via cPanel's PEAR gateway, and then include() them (see below).
	*/
	require('Mail.php');
	require('Mail/mime.php');

$messageHTML = "<html><body><table cellspacing='10'><tr><td style='font-family: Arial, Helvetica, sans-serif'>Hello</td></tr>";
$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>This is an automated response to your request for a reminder of your username and password in order to access your trainer and event details in the National Mediation Training Registry.</td></tr>";
$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>Your username is <kbd>".$row['Username']."</kbd>, and your password is <kbd>".$row['Password']."</kbd>.</td></tr>";
$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>You may log in any time to edit your trainer profile and add upcoming training events. Simply visit <a href='http://www.mediationtrainings.org'>mediationtrainings.org</a> and click on 'Registry' in the main menu.</td></tr>";
$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>If you have any questions or need additional support, please contact me at:
info@mediationtrainings.org</td></tr>";
$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>Sincerely</td></tr>";
$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>Paul Merlyn<br />";
$messageHTML .= "Administrator<br />";
$messageHTML .= "National Mediation Training Registry</td></tr></body></html>";

$messageText = "Hello\n\nThis is an automated response to your request for a reminder of your username and password in order to access your trainer and event details in the National Mediation Training Registry.\n\n";
$messageText .= "Your username is ".$row['Username'].", and your password is ".$row['Password'].".";
$messageText .= "You may log in any time to edit your trainer profile and add upcoming training events. Simply visit <a href='http://www.mediationtrainings.org'>mediationtrainings.org</a> and click on 'Registry' in the main menu.";
$messageText .= "If you have any questions or need additional support, please contact me at:
info@mediationtrainings.org\n\n";
$messageText .= "Sincerely\n
Paul Merlyn\n
Administrator\n
National Mediation Training Registry";

$sendto = $TrainerEmail;
$crlf = "\n";
$hdrs = array(
              'From'    => 'Paul Merlyn <info@mediationtrainings.org>',
   	          'Subject' => 'National Mediation Training Registry',
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
else // No unique match
	{
	?>
	<div style="text-align: center; position: absolute; top: 30%; left: 50%; height: 180px; margin-top: 0px; width: 500px; margin-left: -250px; margin-right: auto; border: 2px solid #444444; border-color: #9C151C; padding: 15px; padding-bottom: 35px;">
	<h4 class="forms">None of our records uniquely matched the email address provided</h4>
	<kbd style="text-transform: lowercase; font-size: 14px; font-weight: normal;"><?=$TrainerEmail; ?></kbd><br /><br /><span class="basictext">You may have used a different address when you first created your trainer profile.</span><br clear="all"><br /><span class="basictext">Please <a href="/userpassreminder.php">try again</a> or contact the Administrator at <script type='text/javascript'> var a = new Array('r','.','@','ort','supp','mediation','trainings','o','g');document.write("<a href='mailto:" + a[4]+a[3]+a[2]+a[5]+a[6]+a[1]+a[7]+a[0]+a[8]+ "'>"+a[4]+a[3]+a[2]+a[5]+a[6]+a[1]+a[7]+a[0]+a[8]+"</a>");</script> for assistance.</span><br /><br /><br />
	<form style="display: inline;" method="post" action="/userpassreminder.php">
	<input type="submit" value="Try Again" class="buttonstyle">
	</form>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<form style="display: inline;" method="post" action="/index.php">
	<input type="submit" value="Abort/Cancel" class="buttonstyle">
	</form>
	</div>
	<?php
	}

ob_end_flush();
?>

</body>
</html>
