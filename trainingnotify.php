<?php
// Start a session (session variables $_SESSION['phpinvalidflag'], $_SESSION['MsgTraineeAreaCode'], and $_SESSION['MsgTraineeEmail'] is set within trainingnotify_slave.php)
session_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Notification of Upcoming Mediation Trainings</title>
<meta NAME="description" CONTENT="Form to receive automatic email notifications of mediation trainings">
<meta NAME="keywords" CONTENT="email notification, mediation trainings">
<link href="/mediationtraining.css" rel="stylesheet" type="text/css">
</head>

<body style="text-align: left;">
<h2>Notification of Upcoming Trainings</h2>
<p>Enter your telephone area code to receive email notification of upcoming mediation trainings in your area:</p>
<div class="basictext" style="margin-left: 50px;">Note: Your email address remains private; one-click unsubscribe any time.</div>

<div style="text-align: center; margin-top: 25px;">

<div style="display: inline; float: left; margin-left: 50px;">
<form method="post" action="/scripts/trainingnotify_slave.php">
<input type="text" class="textfieldsmall" name="TraineeAreaCode" id="TraineeAreaCode" maxlength="3" size="3" style="width: 50px; text-align: center;">&nbsp;&nbsp;
<input type="text" class="textfieldsmall" name="TraineeEmail" id="TraineeEmail" maxlength="40" size="25" style="width: 200px;">&nbsp;&nbsp;&nbsp;
<input type="submit" name="Notify" class="buttonstyle" value="Notify Me">
<div class="greytextsmall">Area code</div>
<div class="greytextsmall" style="display: inline; float: left; clear: both; position: relative; left: 70px; bottom: 18px;">Email address</div>
</form>
</div>

<div style="display: block; text-align: left; margin-left: 50px; padding: 0px 0px 0px 0px;"><br clear="all"/><br /><?php if ($_SESSION['MsgTraineeEmail'] != null) { echo $_SESSION['MsgTraineeEmail']; $_SESSION['MsgTraineeEmail']=null; } ?><?php if ($_SESSION['MsgTraineeAreaCode'] != null) { echo $_SESSION['MsgTraineeAreaCode']; $_SESSION['MsgTraineeAreaCode']=null; } ?><br /></div>

</div>
</body>
</html>
