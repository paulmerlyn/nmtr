<?php

require ('seo-board_options.php');
include ('./lang/eng.php');

error_reporting(E_ALL);
set_magic_quotes_runtime(0);

header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: Mon,26 Jul 1997 05:00:00 GMT");

@mysql_connect($dbhost, $dbuser, $dbpass) or die ($lang['db_error']);
@mysql_select_db($dbname) or die ($lang['db_error']);

$q = array();

// forums ain't too many, so we can use char instead of varchar for this table
$q[] = "DROP TABLE IF EXISTS {$dbpref}forums";
$q[] = "CREATE TABLE {$dbpref}forums (
forum_id int(10) unsigned NOT NULL auto_increment,
forum_parent int(10) unsigned NOT NULL DEFAULT '0',
forum_order int(10) unsigned NOT NULL DEFAULT '0',
forum_name char(100) NOT NULL DEFAULT '',
forum_desc char(255) NOT NULL DEFAULT '',
forum_numtopics int(10) unsigned NOT NULL DEFAULT '0',
forum_numreplies int(10) unsigned NOT NULL DEFAULT '0',
forum_lastpost_time int(10) unsigned NOT NULL DEFAULT '0',
forum_lastposter char(30) NOT NULL DEFAULT '',
PRIMARY KEY (forum_id)
) TYPE=MyISAM;";


$q[] = "DROP TABLE IF EXISTS {$dbpref}users";
$q[] = "CREATE TABLE {$dbpref}users (
user_id int(10) unsigned NOT NULL auto_increment,
user_name varchar(20) NOT NULL DEFAULT '',
user_pass char(40) NOT NULL DEFAULT '',
user_regdate int(10) unsigned NOT NULL DEFAULT '0',
user_bio text NOT NULL DEFAULT '',
user_bio_status tinyint(2) unsigned NOT NULL DEFAULT '5',
user_timezone float(3,1) NOT NULL DEFAULT '0.0',
user_email varchar(100) NOT NULL DEFAULT '',
user_email_public tinyint(1) unsigned NOT NULL DEFAULT '0',
user_allowviewonline tinyint(1) unsigned NOT NULL DEFAULT '1',
user_numposts int(10) unsigned NOT NULL DEFAULT '0',
user_lasttimereadpost int(10) unsigned NOT NULL DEFAULT '0',
user_lastsession int(10) unsigned NOT NULL DEFAULT '0',
user_banned tinyint(1) unsigned NOT NULL DEFAULT '0',
user_newpassword char(40) NOT NULL DEFAULT '',
user_signature text NOT NULL DEFAULT '',
user_signature_status tinyint(2) unsigned NOT NULL DEFAULT '5',
user_view_signatures tinyint(1) unsigned NOT NULL DEFAULT '1',
user_avatar varchar(150) NOT NULL DEFAULT '',
user_view_avatars tinyint(1) unsigned NOT NULL DEFAULT '1',
PRIMARY KEY (user_id)
) TYPE=MyISAM;";


$q[] = "DROP TABLE IF EXISTS {$dbpref}topics";
$q[] = "CREATE TABLE {$dbpref}topics(
topic_id int(10) unsigned NOT NULL auto_increment,
topic_title varchar(100) NOT NULL,
topic_poster_id int(10) unsigned NOT NULL DEFAULT '0',
topic_poster_name varchar(30) DEFAULT 'Anonymous' NOT NULL,
topic_lastposter_id int(10) unsigned NOT NULL DEFAULT '0',
topic_lastposter_name varchar(30) DEFAULT 'Anonymous' NOT NULL,
topic_created_time int(10) unsigned NOT NULL DEFAULT '0',
topic_lastpost_time int(10) unsigned NOT NULL DEFAULT '0',
topic_numreplies int(10) unsigned NOT NULL DEFAULT '0',
topic_numviews int(10) unsigned NOT NULL DEFAULT '0',
topic_sticky tinyint(1) unsigned DEFAULT '0',
topic_locked tinyint(1) unsigned DEFAULT '0',
topic_moved tinyint(1) unsigned DEFAULT '0',
forum_id int(10) unsigned NOT NULL DEFAULT '0',
PRIMARY KEY (topic_id),
KEY (forum_id),
KEY (topic_lastpost_time)
) TYPE=MyISAM;";

$q[] = "DROP TABLE IF EXISTS {$dbpref}posts";
$q[] = "CREATE TABLE {$dbpref}posts(
post_id int(10) unsigned NOT NULL auto_increment,
topic_id int(10) unsigned NOT NULL DEFAULT '0',
post_author varchar(30) DEFAULT 'Anonymous' NOT NULL,
post_author_id int(10) unsigned NOT NULL DEFAULT '0',
post_author_ip varchar(15) NOT NULL default 'Unknown',
post_text text NOT NULL DEFAULT '',
post_text_status tinyint(2) unsigned NOT NULL DEFAULT '5',
post_time int(10) unsigned NOT NULL DEFAULT '0',
post_edited tinyint(1) unsigned DEFAULT '0',
post_edited_by varchar(30) DEFAULT '' NOT NULL,
post_edited_time int(10) unsigned NOT NULL DEFAULT '0',
PRIMARY KEY (post_id),
KEY (topic_id),
KEY (post_time),
FULLTEXT (post_text)
) TYPE=MyISAM;";

$q[] = "DROP TABLE IF EXISTS {$dbpref}search";
$q[] = "CREATE TABLE {$dbpref}search(
search_id int(10) unsigned NOT NULL auto_increment,
search_user_id int(10) unsigned NOT NULL DEFAULT '0',
search_time int(10) unsigned NOT NULL DEFAULT '0',
search_results text NOT NULL DEFAULT '',
PRIMARY KEY(search_id)
) TYPE=MyISAM;";

$q[] = "DROP TABLE IF EXISTS {$dbpref}bans";
$q[] = "CREATE TABLE {$dbpref}bans(
ban_id int(10) unsigned NOT NULL auto_increment,
ban_data char(20) NOT NULL DEFAULT '',
PRIMARY KEY(ban_id),
UNIQUE KEY(ban_data)
) TYPE=MyISAM;";

//create tables
foreach ($q as $query)
{
  mysql_query($query) or die (mysql_error());
}

//insert admin account
$now = time();
mysql_query("INSERT INTO {$dbpref}users (user_id, user_name, user_pass, user_email, user_regdate, user_timezone) VALUES (1, '".addslashes($adminuser)."', '".sha1($shaprefix.$adminpass)."','$adminemail','$now','$forumtimezone')") or die (mysql_error());;

echo 'Forum Installed Successfully!';
?>
