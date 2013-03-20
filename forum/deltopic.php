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

if (($user_id != 1) && (is_user_moderator($f, $user_id) == 0))
  die($lang['access_denied']);

$title = $forumtitle.' &raquo; '.$lang['del_topic'];
$forum_path = get_forum_path($f, $lang['del_topic']);
require('forumheader.php');

if (!isset($confirm))
{
  $message = sprintf($lang['confirm_del_topic'], $topic_title);;
  $confirmed_link = '<a href="'.$forumscript.'?a=deltopic&amp;t='.$t.'&amp;confirm=1">'.$lang['confirm_action'].'</a>';
  print eval(get_template('confirm'));
}
else
{
  //get posters' IDs
  $result = mysql_query("SELECT DISTINCT post_author_id FROM {$dbpref}posts WHERE topic_id='$t'");
  $members = array();
  while($row = mysql_fetch_row($result))
    array_push($members, $row[0]);
    
  //delete posts
  mysql_query("DELETE FROM {$dbpref}posts WHERE topic_id='$t'");

  //delete topic
  mysql_query("DELETE FROM {$dbpref}topics WHERE topic_id='$t'");

  //fix members' stats
  foreach ($members as $m_id)
  {
    $user_numposts = mysql_result(mysql_query("SELECT COUNT(*) FROM {$dbpref}posts WHERE post_author_id='$m_id'"), 0);
    mysql_query("UPDATE {$dbpref}users SET user_numposts='$user_numposts' WHERE user_id='$m_id'") or die(mysql_query());
  }
  
  //fix forum stats
  fix_forum_stats($f);
  
  show_message($lang['del_topic_success']);  
}
?>
