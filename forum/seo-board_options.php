<?php

$dbhost = 'localhost';
$dbname = 'paulme6_medtrainers';
$dbuser = 'paulme6_merlyn';
$dbpass = 'fePhaCj64mkik';
$dbpref = 'seo_board_';

$adminfile = 'admin.php';
$adminuser = 'admin';
$adminpass = 'Eustacia';
$adminemail = 'paul@mediationtrainings.org';

//don't change this AFTER installing the board. You can change it before installing.
$shaprefix = ''; // I reset this to an empty string rather than keep it at its default value below for the benefit of simpler harmonization of registered Forum participants with the usernames/passwords assigned to trainers in the Registry.
//$shaprefix = 'This is the default sha hashing prefix.';

$forumhome = 'http://www.mediationtrainings.org/index.php'; //link to forum home
$forumdir = 'http://www.mediationtrainings.org/forum/'; //forum dir without the script file, must end with a '/'
$forumscript = 'http://www.mediationtrainings.org/forum/mediationforum.php'; //forum dir + script file

$cookiedomain = '';
$cookiename = 'seo-board';
$cookiepath = '';
$cookiesecure = FALSE;

$enablegzip = 1; // 0 - no; 1 - yes

$visittimeout = 600;
$usereditposttimeout = 1200;

$forumtimezone = 2; // default forum zone

$registermode = 1; // 1 - register user immediately; 0 - send an e-mail with generated password

$forumtitle = 'Forum of the National Mediation Training Registry';

$lang = 'eng';

$invalidusernames = array('Anonymous', 'Guest');

$maxwordlength = 60;
$maxpostlength = 20000;

$postsperpage = 30;
$topicsperpage = 20;
$searchresultsperpage = 30;

$searchexpiretime = 600;

$showforumstats = 0; //0 - no; 1 - yes
$showlastposts = 1;  //0 - no; 1 - yes
$shownumlastposts = 30;
//exclude forums from showing their topics in last discussions
//array(forumexcludeid1, forumexcludeid2..); or empty -> array();
$lastpostsexclude = array();

$datetimeformat = 'g:i a T \o\n F j, Y'; // This format is 3:29 pm GMT on November 5, 2010
$dateformat = 'd M Y';
$shortdateformat = 'M Y';
//if you use long months (January), then change $engmonths to long English months for multi-lang support
$engmonths = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');

$antispam = 5;

$modrewrite = 1; // 0- no mod_rewrite; 1- search engine friendly urls, requires .htaccess ...

//special forum options
//private forums, only for a selected subset of users
// = array(private_forum_id => array(allowuserid1, allowuserid2..));
$privateforums = array();
//only admin can post in read only forums
// = array(readonly_forum_id, readonly_forum_id2);
$readonlyforums = array();
//do everything except creating NEW topics (only admin can do it!)
// = array(postonly_forum_id, postonly_forum_id2..);
$postonlyforums = array();
// by default guests cannot post
// = array(guestcanpost_forum_id1, guestcanpost_forum_id2..);
$guestscanpostforums = array(1,2);
// allow posting of HTML tags in messages in these forums
// = array(allowedhtmlforumid1,...);
$allowhtmlforums = array();
// forums that are sorted by topic creation time (newest on top)
// = array(forumid1, forumid2..);
$articleforums = array ();

//forum moderators
// = array(forum => array(moderatorID => 'ModeratorName', moderatorID2 => 'ModeratorName2'));
$moderators = array();

$showmoderators = 0; //0-no 1-yes

//ADDED IN 1.04
//rss feed options
//exclude some forums from the rss/atom feeds
// = array (exclude_forum_id1, exclude_forum_id2...);
$feedexcludeforums = array();

//how many topics to include in the feeds
$feednumtopics = 10;

//ADDED IN 1.1.0
$signaturesandavatars = 1; // 0 - sigs and avatars disabled; 1 - sigs and avatars enabled

$maxsignaturesize = 350; // maximal signature size in chars
$maxavatarsize = 20000; // maximal avatar size in bytes
$maxavatarheight = 80; // maximal avatar height in pixels
$maxavatarwidth = 80;  // maximal avatar width in pixels

$avatardirectory = './avatars/'; //directory where the avatars will be uploaded. Must end with a '/'

$nofollow = 1; //0 - outgoing links normal; 1 - outgoing links with nofollow
?>
