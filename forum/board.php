<?php

if(!defined('SEO-BOARD'))
{
  die($lang['fatal_error']);
}

$title = $forumtitle;
require('forumheader.php');
if (count($f_rows) == 0)
{
  show_message($lang['forums_empty']);
  exit;
}


$forums_html = array();

$c_index = $f_lookup_by_parent[0];
$cell_iterator = 0;

$template_cat = get_template('forumcat');
$template_forum = get_template('forumcell');

while (isset($f_rows[$c_index]) && ($f_rows[$c_index][1] == 0))
{
  $c_id = $f_rows[$c_index][0];
  if (!forum_visible($user_id, $c_id)) 
  { 
    ++$c_index; 
	continue; 
  }
  $forum_link = get_forum_link($c_id, $f_rows[$c_index][3]);
  $forum_desc = (strlen($f_rows[$c_index][4]) != 0) ? ' - '.$f_rows[$c_index][4] : null;
    
  array_push($forums_html, eval($template_cat));
  if (isset($f_lookup_by_parent[$c_id]))
  {
    $f_index = $f_lookup_by_parent[$c_id];
    while (isset($f_rows[$f_index]) && ($f_rows[$f_index][1] == $c_id))
    {
      list($f_id, , , $f_name, $forum_desc, $num_topics, $num_replies, $lastpost_time, $lastposter) = $f_rows[$f_index];
      if (!forum_visible($user_id, $f_id)) 
	  { 
	    ++$f_index; 
		continue; 
	  }
      $forum_link = get_forum_link($f_id, $f_name, 'forumlink');
      $subforums = '';
      $moderated_by = ($showmoderators == 0) ? null : get_forum_moderators($f_id);
      if ($num_topics == 0)
        $lastpost = $lang['no_posts_yet'];
      else
        $lastpost = $lastposter.'<br>'.format_datetime($lastpost_time, $user_timezone);
      $num_subforums = 0;
      if (isset($f_lookup_by_parent[$f_id]))
      {
	    $subf_index = $f_lookup_by_parent[$f_id];
	    $subforums = "<div class=subforums>&raquo;&nbsp;{$lang['sub_forums']}: ";
	    while (true)
	    {	      
	      $subf_id = $f_rows[$subf_index][0];
	      if ($f_visible = forum_visible($user_id, $subf_id))
	      {
            $subforums .= get_forum_link($subf_id, $f_rows[$subf_index][3], 'forumlink');
	        ++$num_subforums;
	      }	      
		  if (isset($f_rows[++$subf_index]) && ($f_rows[$subf_index][1] == $f_id))
		  {
		    if ($num_subforums > 0 && $f_visible)
			  $subforums .= ', &nbsp';
		  }
		  else
		    break;
		}		
      }
      if ($num_subforums == 0) 
        $subforums = null;
      else
        $subforums .= '</div>';
      array_push($forums_html, eval($template_forum));
      ++$f_index;
      $cell_iterator = 1 - $cell_iterator;
    }
  }
  ++$c_index; //next main category
}
$forums_html = implode('',$forums_html);
print eval(get_template('mainforumtable'));
unset($template_cat);
unset($template_forum);
unset($forums_html);

if ($showlastposts == 1)
{
  $cell_iterator = 0;
  //exclude private to the current user forums + the ones in $lastpostsexclude
  $userprivateforums = get_invisible_forums($user_id);
  if (!empty($lastpostsexclude))
    $userprivateforums = array_unique(array_merge($userprivateforums, $lastpostsexclude));
  if (count($userprivateforums)==0)
    $result = mysql_query("SELECT topic_id, topic_title, topic_poster_name, topic_poster_id, topic_lastposter_name, topic_lastposter_id, topic_created_time, topic_lastpost_time, topic_numreplies, topic_numviews, topic_sticky, topic_locked, topic_moved FROM {$dbpref}topics ORDER BY topic_lastpost_time DESC LIMIT $shownumlastposts");
  else
  {
    $where = 'WHERE forum_id<>'.implode(' AND forum_id<>', $userprivateforums);
    $result = mysql_query("SELECT topic_id, topic_title, topic_poster_name, topic_poster_id, topic_lastposter_name, topic_lastposter_id, topic_created_time, topic_lastpost_time, topic_numreplies, topic_numviews, topic_sticky, topic_locked, topic_moved FROM {$dbpref}topics $where ORDER BY topic_lastpost_time DESC LIMIT $shownumlastposts");
  }
  if (mysql_num_rows($result) != 0)
  {
    $topics_html = array();
    $template_topic = get_template('mainlastpostscell');
    while($row = mysql_fetch_row($result))
    {
      list($id, $title, $author, $author_id, $lastposter, $lastposter_id, $createdtime, $lastposttime, $num_replies, $num_views, $sticky, $locked, $moved) = $row;
      $topic_link = get_topic_link($id, $title, $num_replies+1, 'topiclink');

      if (($lastposttime > $user_lastsession) && ($lastposter_id != $user_id) && ($user_id != 0))
        if ($createdtime < $user_lastsession) //created before lastsession
          $topic_link = '<sup>'.$lang['new'].'</sup> '.$topic_link;
        else//created after lastsession
          if ($author_id != $user_id)
            $topic_link = '<sup>'.$lang['new'].'</sup> '.$topic_link;
      
      $started_by = $lang['started_by'].' '.$author;      
      $lastpost = $lastposter.'<br>'.format_datetime($lastposttime, $user_timezone);
      array_push($topics_html, eval($template_topic));
      $cell_iterator = 1 - $cell_iterator;    
    }
    $topics_html = implode('', $topics_html);
    print eval(get_template('mainlastposts'));    
    unset($template_topic);
  }
}

//show board stats?
if ($showforumstats == 1)
{
  $totalusers = mysql_result(mysql_query("SELECT COUNT(*) FROM {$dbpref}users"), 0);
  $post_stats = sprintf($lang['post_stats'], $totaltopics, $totalreplies, $totaltopics+$totalreplies);
  $member_stats = sprintf($lang['member_stats'], $totalusers);
  $notonline = time() - $visittimeout;
  $result = mysql_query("SELECT user_name FROM {$dbpref}users WHERE user_lasttimereadpost>'$notonline' AND user_allowviewonline=1");
  if (mysql_num_rows($result) == 0)
    $members_online = null;
  else
  {
    $members_online = $lang['members_online'].': ';
    $online_users = array();
    while($row = mysql_fetch_row($result))
      array_push($online_users, $row[0]);
    $members_online .= implode(', ', $online_users);
  }
  print eval(get_template('forumstats'));
}
?>

