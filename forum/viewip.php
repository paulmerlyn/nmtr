<?php

if(!defined('SEO-BOARD'))
{
  die($lang['fatal_error']);
}

if (($user_id != 1) && (is_user_moderator(-1, $user_id) == 0))
  die($lang['access_denied']);
  
$title = $forumtitle.' &raquo; '.$lang['view_IP'];
require('forumheader.php');

$user_list = array();
$result = mysql_query("SELECT DISTINCT u.user_id, u.user_name FROM {$dbpref}posts p LEFT JOIN {$dbpref}users u ON p.post_author_id=u.user_id WHERE p.post_author_ip='$ip'");
while ($row = mysql_fetch_row($result))
{
  if ($row[0] == 0)
    array_push($user_list, $lang['guest']);
  else
    array_push($user_list, get_member_link($row[0], $row[1]));
}
if (count($user_list) == 0)
  $user_list = $lang['no_users_IP'];
else
  $user_list = implode('<br>', $user_list);
print eval(get_template('userviewip'));  
?>
