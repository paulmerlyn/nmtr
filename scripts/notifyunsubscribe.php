<?php
/*
notifyunsubscribe.php is a slave script invoked via addevent_slave.php. The latter, in addition to being the slave for addevent.php, also generates a notification email to trainees who have signed up to receive notifications when a trainer adds a new event in their particular telephone area code. That notification email always include an unsubscribe link so the trainee can stop receiving such notifications. When the trainee clicks the unsubscribe link in a notification email, he/she is taken to notifyunsubscribe.php.
	This script must delete the trainee's record (simply two fields, the email address [primary key] and telephone area code) from the traineenotify_table table in the DB.
*/

$queryString = $_SERVER['QUERY_STRING']; // courtesy http://www.webmasterworld.com/forum88/221.htm
$unsubscribeEmail = $queryString; // Given that the user clicked the following URI to get to this script (i.e. http://www.mediationtrainings.org/scripts/notifyunsubscribe.php?hername@herisp.com, $$queryString will be assigned the email address to be unsubscribed (i.e. hername@herisp.com).

$db = mysql_connect('localhost', 'paulme6_merlyn', 'fePhaCj64mkik')
or die('Could not connect: ' . mysql_error());
mysql_select_db('paulme6_medtrainers') or die('Could not select database: ' . mysql_error());

$query = "DELETE FROM traineenotify_table WHERE TraineeEmail = '".$unsubscribeEmail."'";
$result = mysql_query($query) or die('The DELETE of a row from traineenotify_table has failed i.e. '.$query.' failed: ' . mysql_error());

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta NAME="description" CONTENT="Unsubscribe from receiving mediation training notifications via email.">
<meta NAME="keywords" CONTENT="unsubscribe, mediation training notifications">
<title>Unsubscribe from Email Notification of Mediation Training Events</title>
<link href="/mediationtraining.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="/scripts/windowpops.js" type="text/javascript"></script>
</head>

<body style="text-align: left;">
<p>Thank you for your interest in the <b>National Mediation Training Registry</b>. The following email address has now been removed from our database:</p>
<p style="font-family: 'Courier New', Courier, mono;"><?=$unsubscribeEmail; ?></p>
<p>If you unsubscribed by accident, you may sign up again for updates <a href="/trainingnotify.php" onClick="poptasticDIY('/trainingnotify.php', 320, 700, 250, 250, 300, 300, 'no'); return false;">here</a>. In the meantime, you will no longer receive notification of upcoming trainings in your telephone area code.</p>

<div style="text-align: center; margin-top: 25px;">
<input type=button class="buttonstyle" onClick="javascript:window.close();" onKeyPress="javascript:window.close();" value="Close">
</div>

</body>
</html>
