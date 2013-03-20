<?php

if(!defined('SEO-BOARD'))
{
  die($lang['fatal_error']);
}

if (!isset($t) || !is_numeric($t))
  die($lang['fatal_error']);
  
$result = mysql_query("SELECT forum_id, topic_title FROM {$dbpref}topics WHERE topic_id='$t'");
if (mysql_num_rows($result)!=1)
  die($lang['no_such_topic']);

list ($f, $topic_title) = mysql_fetch_row($result);
$errormessage = null;

if (($user_id != 1) && (is_user_moderator($f, $user_id) == 0))
  die($lang['access_denied']);
  
$title = $forumtitle.' &raquo; '.$lang['edit_topic'];
$forum_path = get_forum_path($f, $lang['edit_topic']);
require('forumheader.php');

if(isset($newtitle) && (strlen($newtitle) == 0))
  $errormessage = get_error($lang['title_empty']);

if(!isset($newtitle) || !is_null($errormessage))
{
  print eval(get_template('useredittopic'));
}
else
{
  mysql_query("UPDATE {$dbpref}topics SET topic_title='$newtitle' WHERE topic_id='$t'");
  show_message($lang['edit_title_success']);
}

?>
