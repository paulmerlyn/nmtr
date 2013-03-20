<?php

$version = '1.1.0';

error_reporting(E_ALL);
set_magic_quotes_runtime(0);

require ('./code/functions.php');

if (!get_magic_quotes_gpc())
{
  array_add_slashes($_GET);
  array_add_slashes($_POST);  
  array_add_slashes($_COOKIE);  
}

foreach ($_GET as $var=>$val)
{
  if (is_array($val))
    $$var = $val;
  else
    $$var = trim($val);
}

foreach ($_POST as $var=>$val)
{
  if (is_array($val))
    $$var = $val;
  else
    $$var = trim($val);
}

foreach ($_COOKIE as $var=>$val)
{
  if (is_array($val))
    $$var = $val;
  else
    $$var = trim($val);
}

require ('seo-board_options.php');
require ('./code/skinning.php');
require ("./lang/$lang.php");

if ($enablegzip == 1)
  ob_start('ob_gzhandler');
else
  ob_start();
  
header('Content-Type: text/html; charset='.$lang['charset']);

@mysql_connect($dbhost, $dbuser, $dbpass) or die ('Error: Cannot connect to database.');
@mysql_select_db($dbname) or die ('Error: Database is missing.');

DEFINE ('SEO-BOARD', true);

$loginerror = 0;
 
if (!isset($a) || 
    !in_array($a, array ('login','logout','register','vforum','vtopic','forgotpass','usercp',
                         'editpost','delpost','toggletopic','movetopic','deltopic','edittopic',
                         'forumjump','member','search', 'viewip')))
  $action = 'board';
else
  $action = $a;
 
$user_id = 0; //guest
$user_name = $lang['guest'];
$user_timezone = $forumtimezone;
$user_lastsession = 0;
$user_view_signatures = 1; //guest users | crawlers see signatures
$user_view_avatars = 1; //guest users | crawlers see avatars

//check for banned IPs
$user_ip = $_SERVER["REMOTE_ADDR"];
$cen = explode('.', $user_ip);
while(count($cen) < 4)
  array_unshift($cen, 0);
$ipmask1 = $cen[0].'.'.$cen[1].'.'.$cen[2].'.+';
$ipmask2 = $cen[0].'.'.$cen[1].'.+';
if(mysql_num_rows(mysql_query("SELECT ban_id FROM {$dbpref}bans WHERE ban_data='$user_ip' OR ban_data='$ipmask1' OR ban_data='$ipmask2'")) != 0)
  die($lang['banned']);

if (isset($_COOKIE[$cookiename]))
{
  list($user_id, $user_pass_sha1) = @unserialize(stripslashes($_COOKIE[$cookiename]));  
  $user_id = addslashes($user_id);
  $user_pass_sha1 = addslashes($user_pass_sha1);

  if (!is_numeric($user_id))
    die($lang['fatal_error']);

  $result = mysql_query("SELECT user_name, user_timezone, user_numposts, user_regdate, user_allowviewonline, user_lasttimereadpost, user_lastsession, user_banned, user_view_signatures, user_view_avatars FROM {$dbpref}users WHERE user_id='$user_id' AND user_pass='$user_pass_sha1'");
  if (mysql_num_rows($result) != 1)
  {
    setcookie($cookiename, '', time() - 10000, $cookiepath, $cookiedomain, $cookiesecure);
    if (isset($_SERVER["HTTP_REFERER"]))
      header("Location: {$_SERVER["HTTP_REFERER"]}");  
    else
      header("Location: $forumhome");  
    exit;
  }
  else
  {
    $user_row = mysql_fetch_row($result);
    list($user_name, $user_timezone, $user_numposts, $user_regdate, $user_allowviewonline, $user_last_time_read_post, $user_lastsession, $user_banned, $user_view_signatures, $user_view_avatars) = $user_row;
    if ($user_banned == 1)
      die($lang['banned']);
    $now = time();
    if (($now - $user_last_time_read_post > $visittimeout) && ($user_lastsession != $user_last_time_read_post))
    {
      $user_lastsession = $user_last_time_read_post;
      mysql_query("UPDATE {$dbpref}users SET user_lastsession='$user_lastsession' WHERE user_id='$user_id'");
    }
  }
  //disallow logged users from these commands
  if (in_array($action, array('forgotpass', 'register', 'login')))
  {
    header("Location: {$forumhome}");
    exit;
  }
}

//log in ? or log out ?
if ($action == 'login')
{//log user in
  if (!isset($username) || !isset($userpass))
    die($lang['fatal_error']);
  $result = mysql_query("SELECT user_id, user_pass FROM {$dbpref}users WHERE user_name='$username' AND user_pass='".sha1($shaprefix.$userpass)."'");
  if (mysql_num_rows($result) != 1)
  {//invalid username and pass
    $loginerror = 1;
    $title = 'Forum of the National Mediation Training Registry';
    $action = 'forumheader';
  }
  else
  {
    list($user_id, $user_pass) = mysql_fetch_row($result);
    if (isset($autolog))
      setcookie($cookiename, serialize(array($user_id, $user_pass)), time() + 31536000, $cookiepath, $cookiedomain, $cookiesecure);
    else
      setcookie($cookiename, serialize(array($user_id, $user_pass)), 0, $cookiepath, $cookiedomain, $cookiesecure);
    if (isset($_SERVER["HTTP_REFERER"]))
      header("Location: {$_SERVER["HTTP_REFERER"]}");  
    else
      header("Location: $forumhome");  
    exit;    
  }
}

if ($action == 'logout')
{//log user out
  if ($user_id != 0)
  {
    $now = time();
    mysql_query("UPDATE {$dbpref}users SET user_lastsession='$now' WHERE user_id='$user_id'");    
  }
  setcookie($cookiename, '', time() - 10000, $cookiepath, $cookiedomain, $cookiesecure);
  header("Location: $forumhome");
  exit;
}

if ($action == 'forumjump')
{
  header('Location: '.get_forum_url($f,1));
  exit;
}

$totaltopics = $totalreplies = $totalforums = $totalusers = 0;
gen_forum_arrays();
$jumptoforum = '<select class=selectbox name=\'f\'>'.select_forums().'</select>';
$forum_path = '';

if ($user_id == 1)
  $admin_panel_link = eval(get_template('adminpanellink'));
else
  $admin_panel_link = null;

require($action.'.php');

print eval(get_template('mainfooter'));

?>
