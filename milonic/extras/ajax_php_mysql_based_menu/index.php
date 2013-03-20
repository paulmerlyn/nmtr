<html>
<head>
<title>Milonic DHTML Menu from MySQL Database using PHP Functions</title>
<head>
<body>

<?php

	include("mm_config.php");  // This file contains all of the user editable parameters
	include("mm_phpmenu.php"); // This is the file containing all of the PHP functions

	buildMySQLMainMenu(1); // This line builds the menu from MySQL data tables.

?>
<br>
<hr>
<br>
Edit the file <a href=mm_config.php>mm_config.php</a> and change the following parameters:<br>
<b>$dbHost, $dbName, $dbUser, $dbPasswd, $sendErrorReports, $adminEmail, $table_prefix, $pathToCodeFiles</b> to suit your own requirements.
<br><br>
Then, using the sample database schema, insert the sample tables and this page should then show a menu
<br><br>
Here is the database scheme for <a href=ajax_mini_menu.sql>ajax_mini_menu.sql</a>
<br>
<br>
<br>

AJAX MySQL Menus - Very similar to the MySQL Menus but the showmenu command is changed to:<br>
ajax:maj.php?name=Menu Samples;
<br>
<br>
<b>ajax</b> is used to denote that the opening menu will be based on contents of a file called by ajax.<br><br>
<b>maj.php</b> is the name of the file you need to call, this file queries the database and returns a formated menu<br><br>
<b>name</b> is the name of the menu you want to obtain, this is found in the name field under the menus table<br><br>
<br>
<b>Note that you will need to install the file <a href=http://www.milonic.com/mm_browserapi.js>http://www.milonic.com/mm_browserapi.js</a> into the same location as milonic_src.js and mmenudom.js</b>
<br>
<br>
<br>
<br>

<pre>
-- 
-- Table structure for table `mm_ajax_items`
-- 

CREATE TABLE IF NOT EXISTS `mm_ajax_items` (
  `itemid` int(11) NOT NULL auto_increment,
  `menuid` int(11) NOT NULL default '0',
  `text` text,
  `url` varchar(255) default NULL,
  `showmenu` varchar(40) default NULL,
  PRIMARY KEY  (`itemid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;

-- 
-- Dumping data for table `mm_ajax_items`
-- 

INSERT INTO `mm_ajax_items` (`itemid`, `menuid`, `text`, `url`, `showmenu`) VALUES 
(1, 1, 'MILONIC', 'http://www.milonic.com', NULL),
(2, 1, 'Sample Menus', NULL, 'ajax:maj.php?name=Menu Samples'),
(3, 1, 'About Milonic', NULL, 'ajax:maj.php?name=About Milonic'),
(4, 1, 'Partners', NULL, 'ajax:maj.php?name=Partners'),
(5, 1, 'Links', NULL, 'ajax:maj.php?name=Links'),
(6, 2, 'Horizontal Navigational Menu', NULL, NULL),
(7, 2, 'Vertical Navigational Menu', '/menusample2.php', NULL),
(8, 2, 'All Horizontal Menus', '/menusample25.php', NULL),
(9, 2, 'Using the popup function Fixed Position', '/menusample3.php', NULL),
(10, 2, 'Using the popup Positioned by Images', 'menusample24.php', NULL),
(11, 2, 'Image Map Sample', 'menusample4.php', NULL),
(12, 2, 'Multiple Styles', 'menusample5.php', NULL),
(13, 2, 'Menus and Tool Tips', 'menusample6.php', NULL),
(14, 2, 'Multiple Colored Menus', 'menusample7.php', NULL),
(15, 2, 'Menu Items as Headers', 'menusample8.php', NULL),
(16, 2, 'Windows XP Style Menus', 'menusample12.php', NULL),
(17, 2, 'Windows 98 Style Menus', 'menusample13.php', NULL),
(18, 2, 'Relative Positioning (Table Bound)', 'menusample9.php', NULL),
(19, 2, 'Follow Scrolling', 'menusample10.php', NULL),
(20, 2, 'Opening Windows & Frames', 'menusample11.php', NULL),
(21, 2, 'Hiding DIVs when displaying menus', 'menusample14.php', NULL),
(22, 2, 'Activating MouseOver & MouseOut Functions', 'menusample15.php', NULL),
(23, 2, 'Dynamic Dragable Menus', 'menusample22.php', NULL),
(24, 2, 'Positioning with screenposition & offsets', 'menusample23.php', NULL),
(25, 2, '100% Width Span Menu', 'menusample26.php', NULL),
(26, 2, 'Context Right Click Menu', 'menusample27.php', NULL),
(27, 2, 'Static Images Sample', 'menusample16.php', NULL),
(28, 2, 'Rollover/swap Images', 'menusample17.php', NULL),
(29, 2, 'Menus built from images', 'menusample18.php', NULL),
(30, 2, 'Images as Menu Backgrounds', 'menusample19.php', NULL),
(31, 2, 'Background Menu Item Images', 'menusample20.php', NULL),
(32, 3, 'Product Purchasing Page', 'http://www.milonic.com/cbuy.php', NULL),
(33, 3, 'Contact Us', 'http://www.milonic.com/contactus.php', NULL),
(34, 3, 'Newsletter Subscription', 'http://www.milonic.com/newsletter.php', NULL),
(35, 3, 'FAQ', 'http://www.milonic.com/menufaq.php', NULL),
(36, 3, 'Discussion Forum', 'http://www.milonic.com/forum/', NULL),
(37, 3, 'Software License Agreement', 'http://www.milonic.com/license.php', NULL),
(38, 3, 'Privacy Policy', 'http://www.milonic.com/privacy.php', NULL),
(39, 4, '(aq) Web Hosting', 'http://www.a-q.co.uk/', NULL),
(40, 4, 'SMS 2 Email', 'http://www.sms2email.com/', NULL),
(41, 4, 'WebSmith', 'http://www.websmith.com/', NULL),
(42, 5, 'Apache Web Server', 'http://www.apache.org/', NULL),
(43, 5, 'MySQL Database Server', 'http://ww.mysql.com/', NULL),
(44, 5, 'PHP - Development', 'http://www.php.net/', NULL),
(45, 5, 'phpBB Web Forum System', 'http://www.phpbb.net/', NULL),
(46, 5, 'Anti Spam Tools', '', 'ajax:maj.php?name=AntiSpam'),
(47, 6, 'Spam Cop', 'http://www.spamcop.net/', NULL),
(48, 6, 'Mime Defang', 'http://www.mimedefang.org/', NULL),
(49, 6, 'Spam Assassin', 'http://www.spamassassin.org/', NULL);

-- --------------------------------------------------------

-- 
-- Table structure for table `mm_ajax_menus`
-- 

CREATE TABLE IF NOT EXISTS `mm_ajax_menus` (
  `menuid` int(11) NOT NULL auto_increment,
  `projectid` int(11) NOT NULL default '0',
  `styleid` int(11) NOT NULL default '0',
  `name` varchar(40) NOT NULL default '',
  `alwaysvisible` tinyint(1) default NULL,
  `orientation` tinyint(1) default NULL,
  `overflow` varchar(20) default NULL,
  PRIMARY KEY  (`menuid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- 
-- Dumping data for table `mm_ajax_menus`
-- 

INSERT INTO `mm_ajax_menus` (`menuid`, `projectid`, `styleid`, `name`, `alwaysvisible`, `orientation`, `overflow`) VALUES 
(1, 1, 1, 'Main Menu', 1, 1, NULL),
(2, 1, 1, 'Menu Samples', NULL, NULL, 'scroll'),
(3, 1, 1, 'About Milonic', NULL, NULL, NULL),
(4, 1, 1, 'Partners', NULL, NULL, NULL),
(5, 1, 1, 'Links', NULL, NULL, NULL),
(6, 1, 1, 'AntiSpam', NULL, NULL, NULL);

-- --------------------------------------------------------

-- 
-- Table structure for table `mm_ajax_projects`
-- 

CREATE TABLE IF NOT EXISTS `mm_ajax_projects` (
  `projectid` int(11) NOT NULL auto_increment,
  `menuCloseDelay` int(11) NOT NULL default '500',
  `menuOpenDelay` int(11) NOT NULL default '150',
  `subOffsetTop` tinyint(1) NOT NULL default '0',
  `subOffsetLeft` tinyint(1) NOT NULL default '0',
  `name` varchar(100) default NULL,
  PRIMARY KEY  (`projectid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `mm_ajax_projects`
-- 

INSERT INTO `mm_ajax_projects` (`projectid`, `menuCloseDelay`, `menuOpenDelay`, `subOffsetTop`, `subOffsetLeft`, `name`) VALUES 
(1, 500, 150, 2, -3, 'Minimalist Menu');

-- --------------------------------------------------------

-- 
-- Table structure for table `mm_ajax_styles`
-- 

CREATE TABLE IF NOT EXISTS `mm_ajax_styles` (
  `styleid` int(11) NOT NULL auto_increment,
  `name` varchar(40) NOT NULL default '',
  `oncolor` varchar(6) default NULL,
  `onbgcolor` varchar(6) default NULL,
  `offcolor` varchar(6) default NULL,
  `offbgcolor` varchar(6) default NULL,
  `padding` tinyint(4) default NULL,
  `separatorsize` tinyint(4) default NULL,
  `borderwidth` tinyint(4) default NULL,
  `fontfamily` varchar(25) default NULL,
  `fontsize` varchar(6) default NULL,
  PRIMARY KEY  (`styleid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `mm_ajax_styles`
-- 

INSERT INTO `mm_ajax_styles` (`styleid`, `name`, `oncolor`, `onbgcolor`, `offcolor`, `offbgcolor`, `padding`, `separatorsize`, `borderwidth`, `fontfamily`, `fontsize`) VALUES 
(1, 'miniStyle', 'FFFFFF', '4F8EB6', '000000', 'FFFFFF', 3, 1, 1, 'verdana', '10px');

</pre>

</body>
</html>