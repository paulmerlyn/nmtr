<?php

	include("mm_config.php");  // This file contains all of the user editable parameters
	include("mm_phpmenu.php"); // This is the file containing all of the PHP functions

error_reporting(_ALL);


	if (!mysql_pconnect($dbHost,$dbUser,$dbPasswd)) 
	{
		die("Couldn't connect to the database server $MySQLServer<br><br>");
	}
	else
	{
		mysql_select_db($dbName);
	}
	
	
	

	$query="select * from ".$table_prefix."menus where name = \"$_REQUEST[name]\"";
	//echo $query;
	$mqry=runquery($query);
	
	while ($mar = mysql_fetch_array($mqry))
	{
		$sysCtr=0;
		$mmMenu=new mMenu();	
		foreach ($mar as $k => $v) 
		{
			if($sysCtr==1)$sysCtr=0; else $sysCtr=1;
			if($sysCtr==0)
			{
				if(($k!="menuid" && $k!="projectid" && $k!="name") && $v)
				{
					if($k=="styleid")
					{
						$k="style";
						$query="select * from ".$table_prefix."styles where styleid=$mar[styleid]";
						//echo $query;
//						echo $ar['name'];
						$ar=doquery($query);

						$v=$ar['name'];
					}
		
					$mmMenu->$k=$v;
				}
			}
		}	
	
		
		$sysCtr=0;
		$query="select * from ".$table_prefix."items where menuid=$mar[menuid]";
		$iqry=runquery($query);
		while ($iar = mysql_fetch_array($iqry))
		{
			$mmItem = new mItem();
			$andy=0;
			foreach ($iar as $k => $v) 
			{
				if($sysCtr==1)$sysCtr=0; else $sysCtr=1;
	
				if($sysCtr==0)
				{
					if(($k!="menuid" && $k!="itemid") && $v)
					{
						$mmItem->addItemElement($k, $v);
						
					}
	
				}
				
				
			}	
			$mmMenu->addItemFromItem($mmItem);
		}
	
		
				
	$mmMenu->createMenu($mar['name']);
	}
	 
	echo $menuData;

	
?>