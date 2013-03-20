<?php

require 'seo-board_options.php';
require 'code/functions.php';
require 'code/skinning.php';
require "lang/$lang.php";

@mysql_connect($dbhost, $dbuser, $dbpass) or exit;
@mysql_select_db($dbname) or exit;

gen_forum_arrays();

//exclude private to the guest user forums + the forums in $feedexcludeforums
$userprivateforums = get_invisible_forums(0);
if (!empty($feedexcludeforums))
  $userprivateforums = array_unique(array_merge($userprivateforums, $feedexcludeforums));


//topic_title, topic_id, topic_lastposter_name,
if (count($userprivateforums)==0)
  $result = mysql_query("SELECT forum_id, topic_id, topic_title, topic_poster_name, topic_lastposter_name, topic_lastpost_time FROM {$dbpref}topics ORDER BY topic_lastpost_time DESC LIMIT $feednumtopics");
else
{
  $where = 'WHERE forum_id<>'.implode(' AND forum_id<>', $userprivateforums);
  $result = mysql_query("SELECT forum_id, topic_id, topic_title, topic_poster_name, topic_lastposter_name, topic_lastpost_time FROM {$dbpref}topics $where ORDER BY topic_lastpost_time DESC LIMIT $feednumtopics");
}

header('Content-Type: text/xml; charset='.$lang['charset']);
print '<' . '?xml version="1.0" encoding="'.$lang['charset'].'"?'.'>';

?>
<rss version="2.0">
<channel>
<title><?=format_html($forumtitle)?></title>
<link><?=format_html($forumhome)?></link>
<description><?=format_html($forumtitle)?></description>
<?php

if (mysql_num_rows($result) != 0)
{
  while($row = mysql_fetch_row($result))
  {
    list($f_id, $id, $title, $author, $lastposter, $lastposttime) = $row;
    list($post_text) = mysql_fetch_row(mysql_query("SELECT post_text FROM {$dbpref}posts WHERE topic_id='$id' ORDER BY post_time DESC LIMIT 1"));
    $f_name = $f_rows[$f_lookup_by_id[$f_id]][3];
?>
<item>
<title><?=format_html($author)?>: <?=format_html($title)?></title>
<link><?=format_html(get_topic_url($id,1))?></link>
<description><?=format_html($lastposter)?>: <?=format_html(strip_tags($post_text))?></description>
<pubDate><?=date("r", $lastposttime)?></pubDate>
<category><?=format_html($f_name)?></category>
</item>
<?
  }
}
?>
</channel>
</rss>
