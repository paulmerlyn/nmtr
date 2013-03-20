<?php
if(!defined('SEO-BOARD'))
{
  die($lang['fatal_error']);
}

if ($user_id != 0)
  die($lang['fatal_error']);

$title = $forumtitle.' &raquo; '.$lang['forgot_pass'];
require('forumheader.php');

$errormessage = null;

if (isset($_POST['forgotpass']))
{//change pass/send email
  $result = mysql_query("SELECT user_id, user_email FROM {$dbpref}users WHERE user_name='$username'");
  if (mysql_num_rows($result) == 0)
  {
    $errormessage = get_error($lang['user_not_exist']);
    print eval(get_template('userforgotpass'));
  }
  else
  {
    list($uid, $useremail) = mysql_fetch_row($result);
    $newpass = substr(md5(rand()), 0, 6);
	$newpasssha1 = sha1($shaprefix.$newpass);    
    mysql_query("UPDATE {$dbpref}users SET user_newpassword='$newpasssha1' WHERE user_id='$uid'");
    $changepasslink = $forumscript.'?a=forgotpass&id='.$uid.'&newpass='.$newpass;
    require ('./emails/sendnewpass_email.php');
    require ('./code/mailer.php');
    send_email($email_subject, $email_body, $useremail, $adminemail, $adminemail);
    $message = $lang['new_email_sent'];
    print eval(get_template('message'));
  }
}
else
{
  if (isset($id) && isset($newpass))
  {
    $newpass = sha1($shaprefix.$newpass);
    $result = mysql_query("SELECT user_id FROM {$dbpref}users WHERE user_id='$id' and user_newpassword='$newpass'");
    if ((mysql_num_rows($result) != 1) || (mysql_result($result, 0) != $id))
    {
      $message = $lang['inv_change_pass_url'];
    }
    else
    {
      $message = $lang['new_pass_set'];
      mysql_query("UPDATE {$dbpref}users SET user_pass='$newpass', user_newpassword='' WHERE user_id='$id'");
    }
    print eval(get_template('message'));
  }
  else
  {
    $username = null;
    print eval(get_template('userforgotpass'));
  }
}


?>
