<?php

if(!defined('SEO-BOARD'))
{
  die($lang['fatal_error']);
}

require ('smilies/smilies.php');

if (!isset($m) || !is_numeric($m))
  die($lang['fatal_error']);

$result = mysql_query("SELECT user_name, user_regdate, user_bio, user_bio_status, user_email, user_email_public, user_allowviewonline, user_numposts, user_lasttimereadpost FROM {$dbpref}users WHERE user_id='$m'");
if (mysql_num_rows($result)!=1)
{
  $title = $forumtitle.' &raquo; '.$lang['member_profile'];
  require('forumheader.php');
  show_error($lang['no_such_user']);
}
else
{
  list($member_name, $member_regdate, $member_bio, $member_bio_status, $member_email, $member_email_public, $member_allowviewonline, $member_numposts, $member_lasttimereadpost) = mysql_fetch_row($result);
  $title = $forumtitle.' &raquo; '.$lang['member_profile'].' &raquo; '.$member_name;
  require('forumheader.php');

  if ($member_email_public == 0)
    $member_email = null;
    
  $member_bio = format_html($member_bio);
  
  if (($member_bio_status & 8) != 0)
    $member_bio = str_replace($sm_search, $sm_replace, $member_bio);

  if (($member_bio_status & 2) != 0)
    $member_bio = format_bbcodes($member_bio);
  
  $member_bio = fix_tabs_spaces($member_bio);
  

  $ppd = $member_numposts/(ceil((time()-$member_regdate)/86400));
  $member_numposts = sprintf($lang['posts_per_day'], $member_numposts, $ppd);
  
  $member_regdate = format_datetime($member_regdate, $user_timezone);
  $find_member_topics = '<a href="'.$forumscript.'?a=search&amp;s=&amp;postsby=&amp;postauthor='.htmlentities(urlencode($member_name)).'">'.sprintf($lang['member_topics'],$member_name).'</a>';
  print eval(get_template('memberprofile'));  
}
?>
