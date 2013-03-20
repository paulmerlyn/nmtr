<?php

function send_email($email_subject, $email_body, $send_to, $send_from, $errors_to)
{
  mail($send_to, $email_subject, str_replace("\r\n", "\n", $email_body), "From: $send_from\r\nReply-To: $send_from\r\nErrors-To: $errors_to\r\nX-Mailer: PHP ver. ".phpversion());
}

?>
