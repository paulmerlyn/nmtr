<?php
/*
For maximum ease of access/editing, I've placed the copy of the email marketing messages used by trainerinviterXX.php inside this SSI file. This include file (include'd in trainerinverXX.php where XX is an integer and is generally called via a cron job) simply defines email message content (HTML and plain text) and subject line.
*/

// Start a session
session_start();
 
$subject = 'Reactivate your presence in the National Mediation Training Registry';

// Formulate body text for HTML version:
$messageHTML = "<html><body><table cellspacing='16'><tr><td style='font-family: Arial, Helvetica, sans-serif'>".$_SESSION['dearline']."</td></tr>";
$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>It&rsquo;s easy to reactivate your presence in the National Mediation Training Registry. And doing so is the cheapest way of attracting participants to your training events.</td></tr>";
$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>You may not know this, but almost 25,000 people typed <kbd>mediation training</kbd> or <kbd>mediator training</kbd> into Google last month. And the National Mediation Training Registry ranks 1st and 2nd respectively on Google for those search terms. We bring training providers to the attention of tens of thousands of prospective clients who are <em>actively searching</em> right now for mediation training.</td></tr>";
$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>To reactivate your membership, click <a href='http://www.mediationtrainings.org/activate.php'>here</a>. Or simply type into your browser <kbd>www.mediationtrainings.org/activate</kbd>.</td></tr>";
$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>As a reminder, your username is <kbd>".$_SESSION['Username']."</kbd> and your password is <kbd>".$_SESSION['Password']."</kbd>.</td></tr>";
$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>The National Mediation Training Registry is a mission-driven organization. Together, we can bring conflict resolution skills to more people and help prospective trainees connect with quality organizations such as ";
$messageHTML .= stripslashes($_SESSION['EntityName']);
$messageHTML .= ".</td></tr>";
$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>Sincerely</td></tr>";
$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>Paul Merlyn<br />";
$messageHTML .= "Administrator<br />";
$messageHTML .= "<strong>National Mediation Training Registry</strong><br />";
$messageHTML .= "<em>Learning to change the course of conflict</em><br />";
$messageHTML .= "www.mediationtrainings.org<br />";
$messageHTML .= "paul@mediationtrainings.org<br />";
$messageHTML .= "415.378.7003 t<br />";
$messageHTML .= "415.366.3005 f</td></tr></body></html>";

// Formulate body text for plain text version
$messageText = $_SESSION['dearline']."\n\nIt's easy to reactivate your presence in the National Mediation Training Registry. And doing so is the cheapest way of attracting participants to your training events.\n\n";
$messageText = $_SESSION['dearline']."\n\nYou may not know this, but almost 25,000 people typed 'mediation training' or '>mediator training' into Google last month. And the National Mediation Training Registry ranks 1st and 2nd respectively on Google for those search terms. We bring training providers to the attention of tens of thousands of prospective clients who are actively searching right now for mediation training\n\n";
$messageText .= "To reactivate your membership, simply type into your browser www.mediationtrainings.org/activate.\n\n";
$messageText .= "As a reminder, your username = ".$_SESSION['Username'].", and your password = ".$_SESSION['Password'].".\n\n";
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
?>