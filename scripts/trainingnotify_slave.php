<?php
/*
trainingnotify_slave is the slave script of trainingnotify.php. The latter is a simple form that allows a user (a trainee or prospective trainee) to enter his/her telephone area code and email address st he/she can receive automatic email notification when a trainer posts a new training event for that area code. (The email notifications are actually issued via /cron/notifytrainees.php.) 
*/

// Start a session
session_start();

ob_start(); // Used in conjunction with ob_flush() [see www.tutorialized.com/view/tutorial/PHP-redirect-page/27316], this allows me to postpone issuing output to the screen until after the header has been sent.

// Create short variable names
$TraineeEmail = $_POST['TraineeEmail'];
$TraineeAreaCode = $_POST['TraineeAreaCode'];
$Notify = $_POST['Notify'];

/* Prevent cross-site scripting via htmlspecialchars on these user-entry form field */
$TraineeEmail = htmlspecialchars($TraineeEmail, ENT_COMPAT);
$TraineeAreaCode = htmlspecialchars($TraineeAreaCode, ENT_COMPAT);

/* Begin PHP form validation */

// Create a session variable for the PHP form validation flag, and initialize it to 'false' i.e. assume it's valid.
$_SESSION['phpinvalidflag'] = false;

// Create session variables to hold inline error messages, and initialize them to blank.
$_SESSION['MsgTraineeAreaCode'] = null;
$_SESSION['MsgTraineeEmail'] = null;

// Seek to validate $TraineeEmail
$reqdCharSet = '^[A-Za-z0-9_\-\.]+@[a-zA-Z0-9_\-]+\.[a-zA-Z0-9_\-\.]+$';  // Simple validation from Welling/Thomson book, p125.
if (!ereg($reqdCharSet, $TraineeEmail))
	{
	$_SESSION['MsgTraineeEmail'] = '<span class="errorphp">Check your email address. Use only letters, numbers, dash (-), period (.), @, and underscore (_) characters.<br></span>';
	$_SESSION['phpinvalidflag'] = true; 
	};

// Seek to validate $TraineeAreaCode
$illegalCharSet = '[A-Za-z~%@\^\*_\+`\|\$:";<>\?\.#&+=!,\(\)\-]+'; // Exclude everything except numbers.
$reqdCharSet = '[[:digit:]]{3}';  // Three numerics.
if (ereg($illegalCharSet, $TraineeAreaCode) || !ereg($reqdCharSet, $TraineeAreaCode))
	{
	$_SESSION['MsgTraineeAreaCode'] = '<span class="errorphp">Please enter a 3-digit telephone area code.<br></span>';
	$_SESSION['phpinvalidflag'] = true; 
	};

//Now open the trainingnotify.php pop-up and show any PHP inline validation error messages if the $_SESSION['phpinvalidflag'] has been set to true. ... otherwise, proceed to insert the trainee notification data into the database with the trainee's form data.
if ($_SESSION['phpinvalidflag'])
	{
	?>
	<script type='text/javascript' language='javascript'>poptasticDIY('/index.php', 300, 700, 250, 250, 300, 300, 'no')</script>
	<noscript>
	<?php
	if (isset($_SERVER['HTTP_REFERER']))
		header("Location: ".$_SERVER['HTTP_REFERER']); // Go back to previous page. (Similar to echoing the Javascript statement: history.go(-1) or history.back() except I think $_SERVER['HTTP_REFERER'] reloads the page, which causes freshly entered values in the addevent.php form to get overwritten by values retrieved from the DB. So the javascript 'history.back()' method is more suitable. However, if Javascript is enabled, php form validation is moot. And if Javascript is disabled, then the javascript 'history.back()' method won't work anyway.
	?>
	</noscript>
	<?php
	ob_flush();
	exit;
	}

// End of PHP form validation


/* If we got this far, we are ready to insert the form data into the traineenotify_table table. */

if (!get_magic_quotes_gpc())
{
	$TraineeAreaCode = addslashes($TraineeAreaCode);
	$TraineeEmail = addslashes($TraineeEmail);
}

// Connect to mysql
$db = mysql_connect('localhost', 'paulme6_merlyn', 'fePhaCj64mkik')
or die('Could not connect: ' . mysql_error());
mysql_select_db('paulme6_medtrainers') or die('Could not select database: ' . mysql_error());

$query = "INSERT INTO traineenotify_table SET TraineeEmail = '".$TraineeEmail."', TraineeAreaCode = '".$TraineeAreaCode."'";
$result = mysql_query($query) or die('The INSERT into traineenotify_table failed i.e. '.$query.' failed: ' . mysql_error());
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>trainingnotify_slave.php Script</title>
<script language="JavaScript" src="/scripts/windowpops.js" type="text/javascript"></script>
<link href="/mediationtraining.css" rel="stylesheet" type="text/css">
</head>

<body style="text-align: left;">
<?php
// Display a confirmation message to the user with a 'Close' window button.
?>
<p style="margin-top: 50px;">Your submission was successful. You&rsquo;ll now receive email updates each time a trainer posts a new training event in the <?=$TraineeAreaCode; ?> area code.</p>
<p>All email notifications have an &lsquo;unsubscribe&rsquo; link through which you can discontinue receipt of updates at any time.</p>
<div style="text-align: center; margin-top: 40px;">
<input type=button class="buttonstyle" onClick="javascript:window.close();" onKeyPress="javascript:window.close();" value="Close">
</div>
</body>
</html>
