<?php
/*
userpassreminder.php allows a user (i.e. a trainer who had previously registered with the Registry) to enter his/her email address in order to obtain an email reminder of his/her username/password. The trainer must enter the email address that is stored in trainers_table for this to work (i.e. his/her email address of record). After the user enters his/her email address via a form, form-handling is performed by /scripts/userpassreminder_slave.php. From that email address, the slave can look up the trainer's TrainerID in trainers_table and then use that ID to retrieve his/her Username and Password in userpass_table.
*/

// Start a session
session_start();

ob_start(); // Used in conjunction with ob_flush() [see www.tutorialized.com/view/tutorial/PHP-redirect-page/27316], this allows me to postpone issuing output to the screen until after the header has been sent.
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Username/Password Reminder</title>
<meta NAME="description" CONTENT="Form to request a reminder of a forgotten username/password for mediation trainers in the National Mediation Training Registry">
<meta NAME="keywords" CONTENT="forgotten username,password,mediation trainer, Training Registry">
<link href="/mediationtraining.css" rel="stylesheet" type="text/css">
</head>

<body style="margin: 0; padding: 0; height: 100%;">
<div style="text-align: center; position: absolute; top: 30%; left: 50%; height: 200px; margin-top: 0px; width: 450px; margin-left: -225px; margin-right: auto; border: 2px solid #444444; border-color: #9C151C; padding: 15px; padding-bottom: 35px;">
<h4 class="forms">Please enter the email address associated with your Trainer Profile.</h4>
<div style="padding: 10px; width: 425px;"><span class="basictext">(We&rsquo;ll attempt to match your email address to your username and password and then send them to this email address.)</span></div>
<br><br>
<form method="post" action="/scripts/userpassreminder_slave.php">
<input type="text" class="textfield" name="TrainerEmail" id="TrainerEmail" maxlength="50" size="40" style="width: 350px;">
<br clear="all" /><br />
<input type="submit" name="RemindMe" value="Remind Me" class="buttonstyle">
</form>
</div>
</body>
</html>
