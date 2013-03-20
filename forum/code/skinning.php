<?php

function get_template($template_name)
{
  $tn = './skin/'.$template_name.'.htm';
  if (file_exists($tn))
  {
    $t = file_get_contents($tn);
    return "return <<<TMP\r\n<!-- START: $template_name -->\r\n{$t}\r\n<!-- END: $template_name -->\r\nTMP;\r\n";
  }
  else
    die("FATAL ERROR: File not found $template_name");
}

function show_error($errormsg)
{
  global $lang;
  print eval(get_template('error'));
}

function get_error($errormsg)
{
  global $lang;
  return eval(get_template('error'));
}

function show_error_back($errormsg)
{
  global $lang;
  print eval(get_template('errorback'));
}

function show_message($message)
{
  global $forumhome, $forumtitle;
  print eval(get_template('message'));
}

function get_forum_url($forum_id, $pagenum)
{
  global $forumscript, $forumdir, $modrewrite;
  
  if ($pagenum==1)
    return ($modrewrite == 0) ? $forumscript.'?a=vforum&f='.$forum_id : $forumdir."forum$forum_id.htm";
  else
    return ($modrewrite == 0) ? $forumscript.'?a=vforum&f='.$forum_id.'&p='.$pagenum : $forumdir."forum$forum_id-$pagenum.htm";
}

function get_forum_link($forum_id, $forum_name, $cssclass = null, $pagenum = 1)
{
  if (is_null($cssclass))
    return '<a href="'.format_html(get_forum_url($forum_id, $pagenum)).'">'.$forum_name.'</a>';
  else
    return '<a class='.$cssclass.' href="'.format_html(get_forum_url($forum_id, $pagenum)).'">'.$forum_name.'</a>';
}

function get_forum_pages($forum_id, $curpage, $numtopics)
{
  global $topicsperpage, $lang;
  
  if ($numtopics == 0)
    return null;
    
  $fp = array ('<div class="pagination">', $lang['pages'], ': ');
    
  if ($curpage!=1)
    array_push($fp, get_forum_link($forum_id, $lang['first'], null, 1), '&nbsp;&nbsp;', get_forum_link($forum_id, $lang['prev'], null, $curpage-1), '&nbsp;');
  
  $lastpage = ceil($numtopics/$topicsperpage);
  
  $startpage = $curpage - 3;
  $endpage = $curpage + 3;
  if ($startpage < 1)
  {
    $endpage += (1 - $startpage);
    $startpage = 1; 
  }
  if ($endpage > $lastpage)
    $endpage = $lastpage;

  if ($startpage > 1)
    array_push($fp, '&nbsp;&nbsp;...');
      
  for($i=$startpage;$i<=$endpage;++$i)
  {
    array_push($fp, ($i!=$startpage) ? ' , ' : ' ');
      
    if ($i==$curpage)
      array_push($fp, '<b>', $i, '</b>');
    else
      array_push($fp, get_forum_link($forum_id, $i, null, $i));
  }
  
  if ($endpage < $lastpage)
    array_push($fp, ' ... ');
    
  if ($curpage != $lastpage)
    array_push($fp, '&nbsp;&nbsp;', get_forum_link($forum_id, $lang['next'], null, $curpage+1), '&nbsp;&nbsp;', get_forum_link($forum_id, $lang['last'], null, $lastpage));

  array_push($fp, '</div>');
  
  return implode('', $fp);
}

function get_topic_url($topic_id, $pagenum)
{
  global $forumscript, $forumdir, $modrewrite;
  
  if ($pagenum==1)
    return ($modrewrite == 0) ? $forumscript.'?a=vtopic&t='.$topic_id : $forumdir."article$topic_id.htm";
  else
    return ($modrewrite == 0) ? $forumscript.'?a=vtopic&t='.$topic_id.'&p='.$pagenum : $forumdir."article$topic_id-$pagenum.htm";
}

//get link to topic without pagination
function get_topic_link2($topic_id, $topic_title, $cssclass = null, $pagenum = 1)
{
  if (is_null($cssclass))
    return '<a href="'.format_html(get_topic_url($topic_id, $pagenum)).'">'.trunc_url_title($topic_title).'</a>';
  else
    return '<a class='.$cssclass.' href="'.format_html(get_topic_url($topic_id, $pagenum)).'">'.trunc_url_title($topic_title).'</a>';
}

//Get link to topic with pagination
function get_topic_link($topic_id, $topic_title, $numposts)
{
  global $postsperpage;
  
  $tp = array ('<a class=topiclink href="', format_html(get_topic_url($topic_id, 1)),'">',format_html(trunc_url_title($topic_title)),'</a>');

  $lastpage = ceil($numposts/$postsperpage);
  
  if ($lastpage > 1)
    array_push($tp, '<sup>');

  if ($lastpage > 7)
  {
    array_push($tp, ' . ', get_topic_link2($topic_id, 2, 'topiclink', 2));
    array_push($tp, ' . ', get_topic_link2($topic_id, 3, 'topiclink', 3));
    array_push($tp, ' . ', get_topic_link2($topic_id, 4, 'topiclink', 4), ' . ');
    array_push($tp, ' . ', get_topic_link2($topic_id, $lastpage-2, 'topiclink', $lastpage-2));
    array_push($tp, ' . ', get_topic_link2($topic_id, $lastpage-1, 'topiclink', $lastpage-1));
    array_push($tp, ' . ', get_topic_link2($topic_id, $lastpage, 'topiclink', $lastpage));
  }
  else
  {
    for($i=2;$i<=$lastpage;++$i)
    {
      array_push($tp, ' . ', get_topic_link2($topic_id, $i, 'topiclink', $i));
    }
  }
  if ($lastpage > 1)
    array_push($tp, ' .</sup>');
  
  return implode('', $tp);
}

//topic pagination
function get_topic_pages($topic_id, $curpage, $numposts)
{
  global $postsperpage, $lang;
  
  if ($numposts==0)
    return null;

  $tp = array ('<div class="pagination">', $lang['pages'], ': ');
  
  if ($curpage!=1)
    array_push($tp, get_topic_link2($topic_id, $lang['first'], null, 1), '&nbsp;&nbsp;', get_topic_link2($topic_id, $lang['prev'], null, $curpage-1), '&nbsp;');

  $lastpage = ceil($numposts/$postsperpage);

  $startpage = $curpage - 3;
  $endpage = $curpage + 3;
  if ($startpage < 1)
  {
    $endpage += (1 - $startpage);
    $startpage = 1; 
  }
  if ($endpage > $lastpage)
    $endpage = $lastpage;
  
  if ($startpage > 1)
    array_push($tp, '&nbsp;&nbsp;...');

  for($i=$startpage;$i<=$endpage;++$i)
  {
    array_push($tp, ($i!=$startpage) ? ' , ' : ' ');
      
    if ($i==$curpage)
      array_push($tp, '<b>', $i, '</b>');
    else
      array_push($tp, get_topic_link2($topic_id, $i, null, $i));
  }

  if ($endpage < $lastpage)
    array_push($tp, ' ... ');
    
  if ($curpage != $lastpage)
    array_push($tp, '&nbsp;&nbsp;', get_topic_link2($topic_id, $lang['next'], null, $curpage+1), '&nbsp;&nbsp;', get_topic_link2($topic_id, $lang['last'], null, $lastpage));
 
  array_push($tp, '</div>');
  
  return implode('', $tp);
}

function get_search_results_link($search_url, $link_title, $pagenum)
{
  return '<a href="'.$search_url.'&amp;p='.$pagenum.'">'.$link_title.'</a>';
}

//search results pagination
function get_search_results_pages($search_url, $curpage, $numresults)
{
  global $lang, $searchresultsperpage;

  if ($numresults==0)
    return null;
  
  $sp = array ('<div class="pagination">', $lang['pages'], ': ');

  if ($curpage!=1)
    array_push($sp, get_search_results_link($search_url, $lang['first'], 1), '&nbsp;&nbsp;', get_search_results_link($search_url, $lang['prev'], $curpage-1), '&nbsp;');
  
  $lastpage = ceil($numresults/$searchresultsperpage);

  $startpage = $curpage - 3;
  $endpage = $curpage + 3;
  if ($startpage < 1)
  {
    $endpage += (1 - $startpage);
    $startpage = 1; 
  }
  if ($endpage > $lastpage)
    $endpage = $lastpage;
  
  if ($startpage > 1)
    array_push($sp, '&nbsp;&nbsp;...');
  
  for($i=$startpage;$i<=$endpage;++$i)
  {
    array_push($sp, ($i!=$startpage) ? ' , ' : ' ');
      
    if ($i==$curpage)
      array_push($sp, '<b>', $i, '</b>');
    else
      array_push($sp, get_search_results_link($search_url, $i, $i));
  }
  
  if ($endpage < $lastpage)
    array_push($sp, ' ... ');
    
  if ($curpage != $lastpage)
    array_push($sp, '&nbsp;&nbsp;', get_search_results_link($search_url, $lang['next'], $curpage+1), '&nbsp;&nbsp;', get_search_results_link($search_url, $lang['last'], $lastpage));
 
  array_push($sp, '</div>');
  
  return implode('', $sp);
}

//function get_forums_tree($forums_array, $forum_link, $parent = 0)
function get_forums_tree($forum_link, $parent = 0)
{
  global $f_rows, $f_lookup_by_id, $f_lookup_by_parent;
  $return = null;
  
  if (!isset($f_lookup_by_parent[$parent])) 
    return null;
    
  $f_index = $f_lookup_by_parent[$parent];
  
  while ((isset($f_rows[$f_index])) && ($f_rows[$f_index][1] == $parent))
  {
    $forum_id = $f_rows[$f_index][0];
    $return .= '<ul>'."<li><a href='{$forum_link}{$forum_id}'>{$f_rows[$f_index][3]}</a></li>".get_forums_tree($forum_link, $forum_id).'</ul>';
    ++$f_index;
  }
  return $return;
}

function get_forums_order($parent)
{
  global $f_rows, $f_lookup_by_id, $f_lookup_by_parent;
  $return = null;

  if (!isset($f_lookup_by_parent[$parent])) 
    return null;
    
  $f_index = $f_lookup_by_parent[$parent];
  
  while ((isset($f_rows[$f_index])) && ($f_rows[$f_index][1] == $parent))
  {
    $forum_id = $f_rows[$f_index][0];
    $return .= '<ul>'."<li><input class=inputtextbox size=1 type=text name=o{$forum_id} value={$f_rows[$f_index][2]}>&nbsp;{$f_rows[$f_index][3]}</li>".get_forums_order($forum_id).'</ul>';
    ++$f_index;
  }
  return $return;      
}

//if $select == -1 then select all
function select_forums($select = 0, $parent = 0, $ident = '')
{
  global $f_rows, $f_lookup_by_id, $f_lookup_by_parent, $user_id;
  $return = null;
  
  if (!isset($f_lookup_by_parent[$parent])) return null;
  $f_index = $f_lookup_by_parent[$parent];
  
  while ((isset($f_rows[$f_index])) && ($f_rows[$f_index][1] == $parent))
  {
    $forum_id = $f_rows[$f_index][0];
    if (forum_visible($user_id, $forum_id) == 0)
    {
      ++$f_index;
      continue;
    }
    if (($forum_id != $select) && ($select != -1))
      $selected = null;
    else
      $selected = ' selected=\'selected\'';
    $return .= '<option value="'.$forum_id.'"'.$selected.'>'.$ident.format_html($f_rows[$f_index][3])."</option>\n".
    select_forums($select, $forum_id, $ident.'&nbsp;&nbsp;&nbsp;&nbsp;');
    ++$f_index;
  }
  return $return;
}

//generate forum path and validate forum_id
function get_forum_path($forum_id, $topic_title = null)
{
  global $f_rows, $f_lookup_by_id, $lang, $forumhome, $forumtitle;
  if ($forum_id == -1)
    return '<div class=forumpath><a href="'.$forumhome.'">'.$forumtitle.'</a>&nbsp;/&nbsp;'.$topic_title.'</div>';
  if (!isset($f_lookup_by_id[$forum_id]))
    die($lang['fatal_error']);
  else
    $f_index = $f_lookup_by_id[$forum_id];
  if (is_null($topic_title))
  {
    $forum_path = $f_rows[$f_index][3];
  }
  else
  {
    $topic_title = format_html($topic_title);
    $forum_path = get_forum_link($forum_id, $f_rows[$f_index][3]).'&nbsp;/&nbsp;'.$topic_title;
  }
  while (($forum_id = $f_rows[$f_index][1]) != 0)
  {
    $f_index = $f_lookup_by_id[$forum_id];
    $forum_path = get_forum_link($forum_id, $f_rows[$f_index][3]).'&nbsp;/&nbsp;'.$forum_path;
  }
  return '<div class=forumpath><a href="'.$forumhome.'">'.$forumtitle.'</a>&nbsp;/&nbsp;'.$forum_path.'</div>';
}

function get_timezone_select($tz)
{
  global $timezones;
  $return = '<select name=\'usertimezone\'>';
  foreach ($timezones as $zone => $zonename)
  {
    if ($zone == $tz)
      $return .= "<option value='$zone' selected>$zonename</option>";
    else
      $return .= "<option value='$zone'>$zonename</option>";
  }
  return $return.'</select>';
}

function format_datetime($time, $timezone = 0)
{
  global $months, $datetimeformat, $engmonths;
  $time += ($timezone * 3600);
  $dt = gmdate($datetimeformat, $time);
  if (isset($months))
    $dt = str_replace($engmonths, $months, $dt);
  return $dt;
}

function format_date($time, $timezone = 0)
{
  global $months, $dateformat, $engmonths;
  $time += ($timezone * 3600);
  $d = gmdate($dateformat, $time);
  if (isset($months))
    $d = str_replace($engmonths, $months, $d);
  return $d;
}

function format_shortdate($time, $timezone = 0)
{
  global $months, $shortdateformat, $engmonths;
  $time += ($timezone * 3600);
  $d = gmdate($shortdateformat, $time);
  if (isset($months))
    $d = str_replace($engmonths, $months, $d);
  return $d;
}

function get_user_type($user_id, $forum_id)
{
  global $moderators, $lang;
  if ($user_id == 0)
    return null;
  if ($user_id == 1) 
    return $lang['admin'];
  if (isset($moderators[$forum_id][$user_id]))
    return $lang['moderator'];
  return $lang['member'];
}

function get_forum_moderators($forum_id)
{
  global $moderators, $lang;
  if (!isset($moderators[$forum_id]))  
    return '<span style="float:right;padding-left: 10px;"><i>'.$lang['mods'].': '.$lang['none'].'</i></span>';
  else
    return '<span style="float:right;padding-left: 10px;"><i>'.$lang['mods'].': '.implode(', ', $moderators[$forum_id]).'</i></span>';
}

function get_member_link($member_id, $member_name)
{
  global $forumscript;
  return '<form action="'.$forumscript.'?a=member&amp;m='.$member_id.'" method=post class=formstyle style="max-width:20px;"><input type=submit name=submit value="'.$member_name.'" class=memberbutton></form>';
}

function get_view_ip_link($ip)
{
  global $forumscript;
  return '<a href="'.$forumscript.'?a=viewip&amp;ip='.$ip.'">'.$ip.'</a>';
}

?>
