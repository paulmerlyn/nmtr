<?php
/*
For maximum ease of access/editing, I've placed the copy of the email marketing messages used by trainerinviterXX.php inside this SSI file. This include file (include'd in trainerinverXX.php where XX is an integer and is generally called via a cron job) simply defines email message content (HTML and plain text) and subject line.
*/

// Start a session
session_start();
 
// Create the hashcode, which will be passed as a string in a URL when a prospect clicks the "unsubscribe me" link in his/her email solicitation message.
$hashcode = 'polo'; // This secret key gets used to hash the prospect's email address. If you change the value of this key, make sure you change it also in scripts/unsubscriber.php.
$hashcode = $hashcode.$_SESSION['Email'];
$hashcode = sha1($hashcode);
$hashcode = substr($hashcode, 0, 12); // Truncate, allowing only first 12 characters.

$subject = 'Invitation from the National Mediation Training Registry';

// Formulate body text for HTML version:
$messageHTML = "<html><body><table cellspacing='16'><tr><td style='font-family: Arial, Helvetica, sans-serif'>".$_SESSION['dearline']."</td></tr>";
$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>Did you know, almost 25,000 people typed <kbd>mediation training</kbd> or <kbd>mediator training</kbd> into Google last month? And did you know that the National Mediation Training Registry ranks 1st and 2nd respectively on Google for those search terms?</td></tr>";
$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>If you&rsquo;re a mediation trainer and you&rsquo;re not in the Registry, you&rsquo;re missing out on the easiest and cheapest way to attract people to your training events.</td></tr>";
$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>I invite you to include your training services in the National Mediation Training Registry. Joining takes just a minute and will bring your services to the attention of tens of thousands of  prospective clients who are <em>actively searching</em> right now for mediation training.</td></tr>";
$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>To get registered, click <a href='http://www.mediationtrainings.org/join.php'>here</a>. Or simply point your browser to <a href='http://www.mediationtrainings.org'>www.mediationtrainings.org</a> and click the &lsquo;Join the Registry&rsquo; icon.</td></tr>";
$messageHTML .= "<tr><td style='font-family: Arial, Helvetica, sans-serif'>The National Mediation Training Registry is a mission-driven organization. Together, we can bring conflict resolution skills to more people and help prospective trainees connect with quality organizations such as ";
if ($_SESSION['EntityName'] != '') $messageHTML .= stripslashes($_SESSION['EntityName']); else $messageHTML .= 'yours';
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

// Formulate body text for plain text version
$messageText = $_SESSION['dearline']."\n\nDid you know, almost 25,000 people typed 'mediation training' or 'mediator training' into Google last month? And did you know that the National Mediation Training Registry ranks 1st and 2nd respectively on Google for those search terms?\n\n";
$messageText .= "If you're a mediation trainer and you're not in the Registry, you're missing out on the easiest and cheapest way to bring clients to your training events.\n\n";
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
?>