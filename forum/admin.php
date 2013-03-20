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

header('Content-Type: text/html; charset='.$lang['charset']);

@mysql_connect($dbhost, $dbuser, $dbpass) or die ($lang['db_error']);
@mysql_select_db($dbname) or die ($lang['db_error']);

//login checking

$user_id = 0; //guest

if (!isset($_COOKIE[$cookiename]))
  die('You must be logged as admin to access the admin panel');

list($user_id, $user_pass_sha1) = @unserialize(stripslashes($_COOKIE[$cookiename]));  
$user_id = addslashes($user_id);
$user_pass_sha1 = addslashes($user_pass_sha1);

if ($user_id != 1)
  die('You must be logged as admin to access the admin panel');
if (!is_numeric($user_id))
  die($lang['fatal_error']);
$result = mysql_query("SELECT user_name FROM {$dbpref}users WHERE user_id='$user_id' AND user_pass='$user_pass_sha1'");
if (mysql_num_rows($result) != 1)
  die($lang['fatal_error']);
else
  $user_name = mysql_result($result, 0);
$admin_panel_link = eval(get_template('adminpanellink'));  

//end of login stuff

$forum_path = null;

if (!isset($_GET['a']) || 
    !in_array($_GET['a'], array ('addforum', 'editforum', 'optimize', 'delforum', 'changeorder', 'recountforums', 
    'recountusers', 'banuser', 'unbanuser', 'deluser')))
  $action = 'admin';
else
  $action = $_GET['a'];

ob_start();


gen_forum_arrays();
$jumptoforum = '<select class=selectbox name=\'f\'>'.select_forums().'</select>';

$title = $forumtitle.' Admin Panel';
$navigation =  eval(get_template('mainmembernavigation'));
print eval(get_template('mainheader'));

//administration...
switch ($action)
{
  case 'admin':
    print eval(get_template('adminpanel'));
    break;
  case 'addforum':
  	if (!isset($_POST['addforum']))
  	{
  	  if (!isset($parent)) 
		$parent = 0;
	  if (!isset($forumdesc))
	    $forumdesc = '';
	  if (!isset($forumname))
	    $forumname = '';  	  
  	  $parentselect='<select name=\'parent\'><option value=\'0\'>*'.$lang['create_cat'].'*</option>'.select_forums().'</select>';
      print eval(get_template('adminaddforum'));
      break;
    }
    else
    {
      if (!isset($parent) || !isset($forumdesc) || !isset($forumname))
        die ($lang['fatal_error']);
      if ($forumname == '')
      {
        show_error($lang['forum_empty']);
        $forumdesc = stripslashes($forumdesc);
        $forumname = stripslashes($forumname);
        $parentselect = '<select name=\'parent\'><option value=\'0\'>*'.$lang['create_cat'].'*</option>'.select_forums($parent).'</select>';
        print eval(get_template('adminaddforum'));
        break;
      }
      
      $position = 0;
      $result = mysql_query("SELECT MAX(forum_order) FROM {$dbpref}forums WHERE forum_parent = '$parent'");
      if (mysql_num_rows($result)>0) 
	    $position=mysql_result($result,0);	    
      if ($position == null) 
	    $position = 0; 
	  else 
	    ++$position;
	   
      // add forum to the database
      mysql_query("INSERT INTO {$dbpref}forums (forum_parent, forum_order, forum_name, forum_desc) VALUES ('$parent','$position','$forumname','$forumdesc')");
      
      show_message($lang['forum_added']);
      
    }
    break;
  case 'editforum':
    if (!isset($_POST['editforum']))
    {
      if (!isset($id))
      {
        $forum_tree = get_forums_tree("$adminfile?a=editforum&amp;id=");
        if ($forum_tree == null)
          show_message($lang['noforums_indb']);
        else
          print eval(get_template('adminselecteditforum'));
      }
      else
      {
        if (!is_numeric($id))
          die($lang['fatal_error']);
        $row = mysql_fetch_row(mysql_query("SELECT forum_parent, forum_name, forum_desc FROM {$dbpref}forums WHERE forum_id='$id'"));
        if (!$row)
          show_error($lang['nosuch_forum']);
        else
        {
          $parentselect = '<select name=\'parent\'><option value=\'0\'>*'.$lang['create_cat'].'*</option>'.select_forums($row[0]).'</select>';
          $forumname = format_html($row[1]);
          $forumdesc = format_html($row[2]);
          print eval(get_template('admineditforum'));
        }
	  }
	}
	else
	{
	  if (!isset($id) || !is_numeric($id) || !isset($parent) || !is_numeric($parent) || !isset($forumdesc) || !isset($forumname))
	    die($lang['fatal_error']);
	  if (strlen($forumname) == 0)
	  {
	    show_message($lang['forum_empty']);
        $parentselect = '<select name=\'parent\'><option value=\'0\'>*'.$lang['create_cat'].'*</option>'.select_forums($parent).'</select>';    
	    print eval(get_template('admineditforum'));
	  }
	  else if ($parent == $id)
	  {
	    show_message($lang['own_parent']);
        $parentselect = '<select name=\'parent\'><option value=\'0\'>*'.$lang['create_cat'].'*</option>'.select_forums($parent).'</select>';    
	    print eval(get_template('admineditforum'));
      }
      else
	  {
	    mysql_query("UPDATE {$dbpref}forums SET forum_name='$forumname', forum_desc='$forumdesc', forum_parent='$parent' WHERE forum_id='$id'");
	    show_message($lang['forum_edited']);
	  }
	}
    break;
  case 'optimize':
    mysql_query("OPTIMIZE TABLE {$dbpref}forums, {$dbpref}users, {$dbpref}topics, {$dbpref}posts, {$dbpref}search");
    show_message($lang['tables_optimized']);
    break;
  case 'delforum':
    if (!isset($id))
    {
      $forum_tree = get_forums_tree("$adminfile?a=delforum&amp;id=");
      if ($forum_tree == null)
        show_message($lang['noforums_indb']);
      else
        print eval(get_template('adminselectdelforum'));
    }
    else
    {
      if (!is_numeric($id) || !isset($f_lookup_by_id[$id]))
      {
        show_error($lang['nosuch_forum']);
      }
      else
      if (isset($f_lookup_by_parent[$id]))
      {
        show_error($lang['cant_del_forum']);
        $forum_tree = get_forums_tree("$adminfile?a=delforum&amp;id=");
        print eval(get_template('adminselectdelforum'));
      }
      else
      {
        if (!isset($confirm))
        {
          $forum_title = $f_rows[$f_lookup_by_id[$id]][3];
          $message = sprintf($lang['confirm_del_forum'], $forum_title);;
          $confirmed_link = '<a href="'.$adminfile.'?a=delforum&amp;id='.$id.'&amp;confirm=1">'.$lang['confirm_action'].'</a>';
          print eval(get_template('confirm'));          
        }
        else
        {
          $result = mysql_query("SELECT topic_id FROM {$dbpref}topics WHERE forum_id='$id'");
          while ($row=mysql_fetch_row($result))
          {
            mysql_query("DELETE FROM {$dbpref}posts WHERE topic_id='{$row[0]}'");
            mysql_query("DELETE FROM {$dbpref}topics WHERE topic_id='{$row[0]}'");
          }
          mysql_query("DELETE FROM {$dbpref}forums WHERE forum_id='$id'");
          fix_member_stats();
          show_message($lang['forum_deleted']);
        }
      }
    }
    break;
  case 'changeorder':
    if (!isset($_POST['changeorder']))
    {
      $forums_order = get_forums_order(0);
      print eval(get_template('adminforumorder'));
    }
    else
    {
      $fc = count($f_rows);
      for ($i=0; $i < $fc; ++$i)
      {
        $vname = 'o'.$f_rows[$i][0];
        if (isset($$vname) && is_numeric($$vname))
          mysql_query("UPDATE {$dbpref}forums SET forum_order='".$$vname."' WHERE forum_id='{$f_rows[$i][0]}'");
      }
      show_message($lang['forum_orders_changed']);
    }
    break;
  case 'recountforums':
    $fc = count($f_rows);
    for ($i=0; $i < $fc; ++$i)
      fix_forum_stats($f_rows[$i][0]);
    show_message($lang['forum_stats_recounted']);
    break;
  case 'recountusers':
    fix_member_stats();
    show_message($lang['user_stats_recounted']);
    break;
  case 'banuser':
    if (!isset($_POST['banuser']) || (isset($uie) && (strlen($uie)==0)))
    {
      $ban_title = $lang['ban_user'];
      $ban_operation = 'banuser';
      $uie = null;
      $ban_help = '<br>'.$lang['ban_help'];
      print eval(get_template('adminbans'));
      
      $result = mysql_query("SELECT ban_data FROM {$dbpref}bans");
      if (mysql_num_rows($result) != 0)
      {
        $ban_title = $lang['banned_list'];
        $banned_list = array();
        while ($row = mysql_fetch_row($result))
          array_push($banned_list, $row[0]);
        $result = mysql_query("SELECT user_name FROM {$dbpref}users WHERE user_banned=1");
        while ($row = mysql_fetch_row($result))
          array_push($banned_list, $row[0]);                  
        $banned_list = implode('<br>', $banned_list);
        print eval(get_template('adminbanlist'));
      }
    }
    else
    {
      $result = mysql_query("UPDATE {$dbpref}users SET user_banned=1 WHERE user_name='$uie'");
      if (mysql_affected_rows() > 0)
        show_message(sprintf($lang['user_banned'], $uie));
      else
      {
        if (strpos($uie,'@') === FALSE)
        {
          if (!preg_match("/^[0-9.+]+$/", $uie))
            show_message($lang['invalid_IP']);
          else
          {
            mysql_query("REPLACE INTO {$dbpref}bans (ban_data) VALUES ('$uie')");
            show_message(sprintf($lang['IP_banned'], $uie));
          }
        }
        else
        {
          if (!preg_match('#^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$#',$uie))
            show_message($lang['invalid_email']);
          else
          {
            mysql_query("REPLACE INTO {$dbpref}bans (ban_data) VALUES ('$uie')");
            show_message(sprintf($lang['email_banned'], $uie));
          }
        }
      }
    }
    break;
  case 'unbanuser':
    if (!isset($_POST['unbanuser']) || (isset($uie) && (strlen($uie)==0)))
    {
      $ban_title = $lang['unban_user'];
      $ban_operation = 'unbanuser';
      $uie = null;
      $ban_help = null;
      print eval(get_template('adminbans'));
      
      $result = mysql_query("SELECT ban_data FROM {$dbpref}bans");
      if (mysql_num_rows($result) != 0)
      {
        $ban_title = $lang['banned_list'];
        $banned_list = array();
        while ($row = mysql_fetch_row($result))
          array_push($banned_list, $row[0]);
        $result = mysql_query("SELECT user_name FROM {$dbpref}users WHERE user_banned=1");
        while ($row = mysql_fetch_row($result))
          array_push($banned_list, $row[0]);        
        $banned_list = implode('<br>', $banned_list);
        print eval(get_template('adminbanlist'));
      }
    }
    else
    {
      $result = mysql_query("UPDATE {$dbpref}users SET user_banned=0 WHERE user_name='$uie'");
      if (mysql_affected_rows() > 0)
        show_message(sprintf($lang['user_unbanned'], $uie));
      else
      {
        mysql_query("DELETE FROM {$dbpref}bans WHERE ban_data='$uie'");
        if (mysql_affected_rows() == 0)
          show_message($lang['unban_none']);
        else
          show_message($lang['unban_success']);
      }
    }
    break;
  case 'deluser':
    if (!isset($_POST['deluser']) && !isset($confirm))
    {
      $delusername = null;
      print eval(get_template('admindeluser'));
    }
    else
    {
      if (isset($confirm) && isset($uid))
      {
        mysql_query("DELETE FROM {$dbpref}users WHERE user_id='$uid'") or die(mysql_error());
        mysql_query("UPDATE {$dbpref}topics SET topic_poster_id=0 WHERE topic_poster_id='$uid'")or die(mysql_error());
        mysql_query("UPDATE {$dbpref}topics SET topic_lastposter_id=0 WHERE topic_lastposter_id='$uid'")or die(mysql_error());
        mysql_query("UPDATE {$dbpref}posts SET post_author_id=0 WHERE post_author_id='$uid'")or die(mysql_error());
        show_message($lang['user_deleted']);
      }
      else
      {
        //find the user
        $result = mysql_query("SELECT user_id FROM {$dbpref}users WHERE user_name='$delusername'");
        if (mysql_num_rows($result) == 0)
          show_error_back($lang['no_such_user']);
        else
        {
          $uid = mysql_result($result, 0);
          $forum_title = sprintf($lang['confirm_del_user'], $delusername);
          $message = $forum_title;
          $confirmed_link = '<a href="'.$adminfile.'?a=deluser&amp;uid='.$uid.'&amp;confirm=1">'.$lang['confirm_action'].'</a>';
          print eval(get_template('confirm'));
        }          
      }
    }
    break;
}

print eval(get_template('mainfooter'));

?>
