<?php

function gen_forum_arrays()
{
  global $dbpref, $f_rows, $f_lookup_by_id, $f_lookup_by_parent, $totaltopics, $totalreplies;
  $f_rows = array();
  $f_lookup_by_id = array();
  $f_lookup_by_parent = array();
  $result = mysql_query("SELECT * FROM {$dbpref}forums ORDER BY forum_parent, forum_order");
  $num_rows = 0;  
  while ($row = mysql_fetch_row($result))
  {
    $f_rows[] = $row;
    $f_lookup_by_id[$row[0]] = $num_rows;
    if (!isset($f_lookup_by_parent[$row[1]]))
      $f_lookup_by_parent[$row[1]] = $num_rows;
    ++$num_rows;
    $totaltopics += $row[5];
    $totalreplies += $row[6];    
  }
}

function array_add_slashes(&$array)
{
  foreach($array as $var => $val)
  {
  	if (is_array($val))
  	{
  	  array_add_slashes($array[$var]);
	}
	else
	{
	  $array[$var] = addslashes($array[$var]);
	}
  }
}

function format_html($text)
{
  global $lang;
  return str_replace('&amp;#', '&#', htmlspecialchars($text, ENT_QUOTES));
}

function trunc_url_title($url, $maxchars = 70)
{
  if (strlen($url) > $maxchars)
    return substr($url, 0, $maxchars - 5).' ... ';
  else
    return $url;
}

function trunc_url($url, $url_title)
{
  global $nofollow;
  if ($nofollow)
    return '<a href="'.$url.'" target="_new" rel="nofollow">'.trunc_url_title($url_title).'</a>';
  else
    return '<a href="'.$url.'" target="_new">'.trunc_url_title($url_title).'</a>';
}

function format_bbcodes($text)
{
  global $lang;
  $search = array(
  '[b]','[/b]','[B]','[/B]','[i]','[/i]','[I]','[/I]','[u]','[/u]','[U]','[/U]');
  $replace = array(
  '<strong>','</strong>','<strong>','</strong>','<em>','</em>','<em>','</em>','<u>','</u>','<u>','</u>');
  
  $text = str_replace($search, $replace, $text);

  $search = array(
  '#\[img\](http|https|ftp)://(.*?)\[/img\]#i',
  '#\[email\](.*?)\[/email\]#i',
  '#\[email=(.*?)\](.*?)\[/email\]#i',  
  '#\[url=(http|https|ftp)://(.+?)\](.+?)\[/url\]#ie',
  '#\[url\](http|https|ftp)://(.+?)\[/url\]#ie',
  '#\[code\]#i',
  '#\[/code\]#i',
  '#\[quote\]#i',
  '#\[quote=(.*?)\]#i',
  '#\[/quote\]#i'
  );
  $replace = array(
  '<img src="\\1://\\2" alt="\\1://\\2">',
  '<a href="mailto:\\1">\\1</a>',
  '<a href="mailto:\\1">\\2</a>',
  'trunc_url(\'\\1://\\2\',\'\\3\')',
  'trunc_url(\'\\1://\\2\',\'\\1://\\2\')',
  '<div class="code"><b>'.$lang['code'].': </b><br><br>',
  '</div>',
  '<div class="quote"><b>'.$lang['quote'].': </b><br>',
  '<div class="quote"><b>'.$lang['quoting'].' \\1</b><br>',
  '</div>'
  );
  
  return preg_replace($search, $replace, $text);
}

function makeURLs($text)
{
  $search = array(
  '#(^|\s)(http|https|ftp)(://[^\s\[]+)#i',
  '#(^|\s)([a-z0-9-_.]+@[a-z0-9-.]+\.[a-z0-9-_.]+)#i'
  );
  $replace = array(
  '\\1[url]\\2\\3[/url]',
  '\\1[email]\\2[/email]'
  );
  return preg_replace($search, $replace, $text);
}

function fix_tabs_spaces($text)
{
  $search = array("\n", "\t", '  ', '  ');
  $replace = array('<br>', '&nbsp; &nbsp; ', '&nbsp; ', ' &nbsp;');

  return str_replace($search, $replace, $text);
}

//check if user_id has the right to see forum_id?
function forum_visible($user_id, $forum_id)
{
  global $privateforums;
  if (isset($privateforums[$forum_id]) && !in_array($user_id, $privateforums[$forum_id]) && ($user_id != 1))
    return 0;
  else
    return 1;
}

//get an array of the forums invisible by a user
function get_invisible_forums($user_id)
{
  global $privateforums;
  $result = array();
  if (($user_id == 1) || (count($privateforums)==0)) 
    return $result;
  foreach ($privateforums as $forum_id => $allowed_users)
  {
    if (!in_array($user_id, $allowed_users))
      array_push($result, $forum_id);
  }
  return $result;
}

function can_user_create_topic($user_id, $forum_id)
{
  global $privateforums, $readonlyforums, $postonlyforums, $guestscanpostforums, $f_rows, $f_lookup_by_id;
  if (!isset($f_lookup_by_id[$forum_id]))
    return 0;
  if($f_rows[$f_lookup_by_id[$forum_id]][1] == 0)
    return 0;   
  if ($user_id == 1)
    return 1;
  if (in_array($forum_id, $postonlyforums))
    return 0;
  if (in_array($forum_id, $readonlyforums))
    return 0;
  if (isset($privateforums[$forum_id]) && !in_array($user_id, $privateforums[$forum_id]))
    return 0;
  if ($user_id == 0)
    return in_array($forum_id, $guestscanpostforums) ? 1 : 0;
  return 1;
}

function can_user_post_reply($user_id, $forum_id, $is_topic_locked)
{
  global $privateforums, $readonlyforums, $postonlyforums, $guestscanpostforums, $f_rows, $f_lookup_by_id, $moderators;
  if (!isset($f_lookup_by_id[$forum_id]))
    return 0;
  if($f_rows[$f_lookup_by_id[$forum_id]][1] == 0)
    return 0;   
  if ($user_id == 1)
    return 1;
  if (in_array($forum_id, $readonlyforums))
    return 0;
  if (isset($privateforums[$forum_id]) && !in_array($user_id, $privateforums[$forum_id]))
    return 0;
  if ($is_topic_locked == 1)
    return (($user_id == 1) || (isset($moderators[$forum_id][$user_id]))) ? 1 : 0;
  if ($user_id == 0)
    return in_array($forum_id, $guestscanpostforums) ? 1 : 0;
  return 1;
}

//if forum_id == -1 then check if user_id is a moderator in any forum
function is_user_moderator($forum_id, $user_id)
{
  global $moderators;
  if ($forum_id == -1)
  {
    foreach($moderators as $forum_id => $mods)
      if (isset($mods[$user_id]))
        return 1;
    return 0;
  }
  else
  {
    if (isset($moderators[$forum_id][$user_id]))
      return 1;
    else
      return 0;
  }
}

//return: null for no error, or error message
function validate_text($text, $checkbbcodes = true)
{
  global $lang, $maxwordlength, $maxpostlength;
  
  if (strlen($text) > $maxpostlength)
    return get_error($lang['text_too_long']);
    
  //check for a word that is too long. reg expr could be improved
  if (preg_match('#\b[0-9A-Za-z_]{'.$maxwordlength.',}\b#s',$text)!=0)
    return get_error($lang['word_too_long']);
  
  if ($checkbbcodes == false)
    return null;  
    
  $stack = array();
  $tags1 = array('b','/b','i','/i','u','/u','url','/url','email','/email','img','/img','code','/code','quote','/quote');
  $tags2 = array('url','/url','email','/email', 'quote', '/quote');
  $ex = explode('[', $text);
  $excount = count($ex);
  if ($excount == 1)
    return null;
  for ($i=0; $i<$excount; ++$i)
  {
    $temp_arr = explode(']', $ex[$i]);
    if (count($temp_arr) == 1)
      continue;
    $temp_arr2 = explode('=', $temp_arr[0]);
    $tag = strtolower($temp_arr2[0]);
    //validate tags without '='
    if ((count($temp_arr2) == 1) && (!in_array($tag, $tags1)))
      continue;
    //validate tags with '=' (example: [url=http://www.seobb.com]..)
    if ((count($temp_arr2) > 1) && (!in_array($tag, $tags2)))
      continue;
    if ($tag{0} != '/')
	{
	  array_push($stack, $tag);
	  continue;
	}
	if (count($stack) == 0)
	  return get_error(sprintf($lang['bbcode_error1'], '['.$tag.']')); //unmatched closing tag
	if (end($stack) != substr($tag, 1))
	  return get_error(sprintf($lang['bbcode_error2'], '[/'.end($stack).']', '['.$tag.']')); //closing the wrong tag example: [b][i][/b]
	array_pop($stack);    
  }
  if (count($stack) != 0)
    return get_error($lang['bbcode_error3']); //unclosed tag(s) left
  
  return null;
}

function fix_forum_stats($forum_id)
{
  global $dbpref;
//UPDATE forum_numtopics, forum_numreplies, forum_lastpost_time, forum_lastposter
  $result = mysql_query("SELECT COUNT(*) FROM {$dbpref}topics WHERE forum_id='$forum_id'");
  $topics = mysql_result($result, 0);
  if ($topics != 0)
  {
    $result = mysql_query("SELECT SUM(topic_numreplies) FROM {$dbpref}topics WHERE forum_id='$forum_id'");
    $replies = mysql_result($result, 0);
  }
  else
    $replies = 0;
  $result = mysql_query("SELECT topic_lastpost_time, topic_lastposter_name FROM {$dbpref}topics WHERE forum_id='$forum_id' ORDER BY topic_lastpost_time DESC LIMIT 1");
  list ($last_post_time, $last_poster_name) = mysql_fetch_row($result);
  mysql_query("UPDATE {$dbpref}forums SET forum_numtopics='$topics',forum_numreplies='$replies', forum_lastpost_time='$last_post_time', forum_lastposter='$last_poster_name' WHERE forum_id = '$forum_id'");
}

function fix_member_stats()
{
  global $dbpref;
  $result = mysql_query("SELECT user_id FROM {$dbpref}users");
  while ($row = mysql_fetch_row($result))
  {
    $user_numposts = mysql_result(mysql_query("SELECT COUNT(*) FROM {$dbpref}posts WHERE post_author_id='{$row[0]}'"), 0);
    mysql_query("UPDATE {$dbpref}users SET user_numposts='$user_numposts' WHERE user_id='{$row[0]}'");
  }
}

?>
