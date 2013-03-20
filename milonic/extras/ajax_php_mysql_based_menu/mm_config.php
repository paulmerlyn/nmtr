<?php

/*
   This is the config file for setting up database driven menus
   The settings here are pretty much self explanitory and should be straight forward
   for somebody who has a little knowldge of PHP and MySQL
*/

$dbHost = 'localhost';                 // Where is the MySQL Server located
$dbName = 'mysql_db';                  // The name of the database
$dbUser = 'mysql_user';                // MySQL username
$dbPasswd = 'mysql_password';          // MySQL password
$sendErrorReports=true;                // Send MySQL error messages via email
$adminEmail = "you@yoursite.com";      // Email address of user who should get MySQL error reports
$table_prefix = 'mm_ajax_';            // Prefix of menu tables


/// The following is changed if the name or location of the menu code files have been changed.
$menuIncFiles=array();
$menuIncFiles[]="../../milonic_menu_code.js";
$menuIncFiles[]="../../modules/mm_browserapi.js";


$menuVars=array();
$menuVars["menuCloseDelay"]=500;
$menuVars["menuOpenDelay"]=150;
$menuVars["subOffsetTop"]=0;
$menuVars["subOffsetLeft"]=0;



?>