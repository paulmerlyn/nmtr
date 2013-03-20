<?php

if(!defined('SEO-BOARD'))
{
  die($lang['fatal_error']);
}

$email_subject = "Mediation Training Forum - Activating Your New Password";
$email_body = <<<BOD
Hello Forum Participant

You have requested a new password in order to log into the Forum of the National Mediation Training Registry.

We cannot retrieve your old password because it is kept encrypted in the database. (Only you ever knew it.) However, we can change your current password so that you may log in again with the new password. Here is the new password we generated for you: $newpass

Please click on the URL below, or copy it into your browser, to change your old password, to $newpass.

$changepasslink

After doing that, you can log in and change your password again.

Thank you for supporting the National Mediation Training Registry!

Sincerely

Paul Merlyn
Registry Administrator

BOD;
?>
