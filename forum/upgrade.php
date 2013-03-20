<?php

require ('seo-board_options.php');
include ('./lang/eng.php');

error_reporting(E_ALL);
set_magic_quotes_runtime(0);

@mysql_connect($dbhost, $dbuser, $dbpass) or die ($lang['db_error']);
@mysql_select_db($dbname) or die ($lang['db_error']);

$q[] = "ALTER TABLE {$dbpref}users ADD COLUMN user_signature text NOT NULL DEFAULT '';";
$q[] = "ALTER TABLE {$dbpref}users ADD COLUMN user_signature_status tinyint(2) unsigned NOT NULL DEFAULT '5';";
$q[] = "ALTER TABLE {$dbpref}users ADD COLUMN user_view_signatures tinyint(1) unsigned NOT NULL DEFAULT '1';";
$q[] = "ALTER TABLE {$dbpref}users ADD COLUMN user_avatar varchar(150) NOT NULL DEFAULT '';";
$q[] = "ALTER TABLE {$dbpref}users ADD COLUMN user_view_avatars tinyint(1) unsigned NOT NULL DEFAULT '1';";

foreach ($q as $query)
{
  mysql_query($query) or die (mysql_error());
}

print 'Upgrade To SEO-Board 1.1.0RC3 Completed';
?>
