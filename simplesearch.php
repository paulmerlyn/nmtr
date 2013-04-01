<?php
/* 
simplesearch.php has two functions. Firstly, it obtains user search criteria for a simple search of either trainers (user inputs either a zip code or area code) or training events (user inputs trainingtype == classroom with zip or area code, or user inputs trainingtype == online). Data validation is performed by simplesearch_slave.php.
	Secondly, after handing validation over to simplesearch_slave.php, simplesearch.php regains control and obtains and displays the search results. If the user was searching for mediation trainers, the entire results are displayed by simplesearch.php. However, if the user was searching for training events, simplesearch.php only shows a tabular summary of events that match the search criteria. The user would then select one or more of those events (via check-boxes in the table) for detailed display, which takes place in eventsdisplay.php. Thus eventsdisplay.php is an action script of simplesearch.php (and of powersearch.php).
	Note that index.php also performs the first of these two principal functions i.e. gathering simple search user input and handing the data off to simplesearch_slave.php for validation. But simplesearch_slave.php always passes control back to simplesearch.php for display of search results on the simplesearch.php page (unless there was invalid data, in which case the user error message is displayed on either index.php or simplesearch.php according to which was the source of the input).
*/

// Start a session
session_start();

ob_start(); // Used in conjunction with ob_flush() [see www.tutorialized.com/view/tutorial/PHP-redirect-page/27316], this allows me to postpone issuing output to the screen until after the header has been sent.
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><!-- InstanceBegin template="/Templates/MasterTemplate.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<!-- InstanceBeginEditable name="doctitle" -->
<title>Simple Search for Mediation Trainers and Mediation Training Events</title>
<meta NAME="description" CONTENT="Search for mediation trainers and mediation training events in the National Mediation Training Registry.">
<meta NAME="keywords" CONTENT="search, mediation trainers, mediation training events">
<!-- InstanceEndEditable -->
<link href="/mediationtraining.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="/milonic/milonic_src.js" type="text/javascript"></SCRIPT>
<script language="JavaScript" type="text/javascript">
if(ns4)_d.write("<scr"+"ipt language='JavaScript' src='/milonic/mmenuns4.js'><\/scr"+"ipt>");
else _d.write("<scr"+"ipt language='JavaScript' src='/milonic/mmenudom.js'><\/scr"+"ipt>"); </script>
<SCRIPT language='JavaScript' src="/milonic/menu_data.js" type='text/javascript'></SCRIPT>
<link rel="shortcut icon" href="/favicon.ico">
<!-- InstanceBeginEditable name="head" -->
<link href='TSScript/TSContainer.css' rel='stylesheet' type='text/css'>
<link rel='stylesheet' type='text/css' href='TSScript/TSGlossary/TSGlossary.css' />
<!-- Start: The following javascripts pertain to Trio Solutions's glossary and image preview -->
<script language='JavaScript' type='text/javascript' src='TSScript/yahoo.js'></script>
<script language='JavaScript' type='text/javascript' src='TSScript/event.js'></script>
<script language='JavaScript' type='text/javascript' src='TSScript/dom.js'></script>
<script language='JavaScript' type='text/javascript' src='TSScript/dragdrop.js'></script>
<script language='JavaScript' type='text/javascript' src='TSScript/animation.js'></script>
<script language='JavaScript' type='text/javascript' src='TSScript/container.js'></script>
<script language='JavaScript' type='text/javascript' src='TSScript/TSPreviewImage/TSPreviewImage.js'></script>
<link rel='stylesheet' type='text/css' href='TSScript/TSGlossary/TSGlossary.css' />
<script language='JavaScript' type='text/javascript' src='TSScript/TSGlossary/TSGlossary.js'></script>
<!-- End: Trio Solutions's glossary and image preview -->
<!-- InstanceEndEditable -->
</head>

<body>
<div id="main">
<div id="relwrapper">
<!-- InstanceBeginEditable name="Content" -->
<div id="spacefillerforQSbar"></div> <!-- Only include this spacefillerforQSbar on pages where I show the Quick-Search Bar. -->
<?php
// If the user conducts a search at simplesearch.php that produces a validation error in simplesearch_slave.php (i.e. $_SESSION['phpinvalidflag'] = true in simplesearch_slave.php), control will pass back to simplesearch.php. However, in that circumstance, we won't want to display anything except the Quick Search Bar and its error message. To ensure that is what happens, only show the following code block iff $_SESSION['phpinvalidflag'] != true
if ($_SESSION['phpinvalidflag'] != true)
{

// Connect to DB
$db = mysql_connect('localhost', '', '')
or die('Could not connect: ' . mysql_error());
mysql_select_db('') or die('Could not select database: ' . mysql_error());

// Create short variable names for this (already validated by simplesearch_slave.php) user input data
if (isset($_SESSION['SSzipcode'])) $SSzipcode = $_SESSION['SSzipcode']; else unset($SSzipcode);
if (isset($_SESSION['SSareacode'])) $SSareacode = $_SESSION['SSareacode']; else unset($SSareacode);
$SSeventtype = $_SESSION['SSeventtype'];

if (isset($_SESSION['SSforTrainers'])) // User submitted a search for trainers (rather than training events) in index.php or simplesearch.php.
	{
	
	unset($_SESSION['SSforTrainers']);
	
	if (isset($SSzipcode)) // User submitted a zip code rather than an area code
		{
		/* Create an associative array (ordered alphabetically by EntityName) of all trainer details for each Approved trainer whose MemberLevel == 'Friend' in this zip code i.e. merits treatment as a Featured Trainer */
		$query = "SELECT * FROM trainers_table WHERE Zip = '".$SSzipcode."' && MemberLevel = 'Friend' && Approved = 1 ORDER BY EntityName";
		$resultFriends = mysql_query($query) or die('The SELECT * for simple search Friend trainers in this zip code failed i.e. '.$query.' failed: ' . mysql_error());
		
		/* Count the number of such Friend trainers in this zip code. */
		$query = "SELECT COUNT(*) FROM trainers_table WHERE Zip = '".$SSzipcode."' && MemberLevel = 'Friend' && Approved = 1";
		$result = mysql_query($query) or die('select of count(*) for Friend-level members in a zip code failed: ' . mysql_error());
		$row = mysql_fetch_row($result);
		$FriendCount = $row[0];

		/* Create a sister associative array (ordered alphabetically by EntityName) of all trainer details for each Approved trainer whose MemberLevel == 'Associate' in this zip code i.e. merits treatment as a Featured Trainer */
		$query = "SELECT * FROM trainers_table WHERE Zip = '".$SSzipcode."' && MemberLevel = 'Associate' && Approved = 1 ORDER BY EntityName";
		$resultAssociates = mysql_query($query) or die('The SELECT * for simple search Associate trainers in this zip code failed i.e. '.$query.' failed: ' . mysql_error());
		
		/* Count the number of such Associate trainers in this zip code. */
		$query = "SELECT COUNT(*) FROM trainers_table WHERE Zip = '".$SSzipcode."' && MemberLevel = 'Associate' && Approved = 1";
		$result = mysql_query($query) or die('select of count(*) for Associate-level members in a zip code failed: ' . mysql_error());
		$row = mysql_fetch_row($result);
		$AssociateCount = $row[0];
		}

	if (isset($SSareacode)) // User submitted an area code rather than a zip code
		{
		/* Create an associative array (ordered alphabetically by EntityName) of all trainer details for each Approved trainer whose MemberLevel == 'Friend' in this area code i.e. merits treatment as a Featured Trainer */
		$query = "SELECT * FROM trainers_table WHERE LEFT(Telephone, 3) = '".$SSareacode."' && MemberLevel = 'Friend' && Approved = 1 ORDER BY EntityName";
		$resultFriends = mysql_query($query) or die('The SELECT * for simple search Friend trainers in this area code failed i.e. '.$query.' failed: ' . mysql_error());
		
		/* Count the number of such Friend trainers in this area code. */
		$query = "SELECT COUNT(*) FROM trainers_table WHERE LEFT(Telephone, 3) = '".$SSareacode."' && MemberLevel = 'Friend' && Approved = 1";
		$result = mysql_query($query) or die('select of count(*) for Friend-level members in an area codefailed: ' . mysql_error());
		$row = mysql_fetch_row($result);
		$FriendCount = $row[0];

		/* Create a sister associative array (ordered alphabetically by EntityName) of all Approved trainer details for each trainer whose MemberLevel == 'Associate' in this area code i.e. merits treatment as a Featured Trainer */
		$query = "SELECT * FROM trainers_table WHERE LEFT(Telephone, 3) = '".$SSareacode."' && MemberLevel = 'Associate'  && Approved = 1 ORDER BY EntityName";
		$resultAssociates = mysql_query($query) or die('The SELECT * for simple search Associate trainers in this area code failed i.e. '.$query.' failed: ' . mysql_error());
		
		/* Count the number of such Associate trainers in this zip code. */
		$query = "SELECT COUNT(*) FROM trainers_table WHERE LEFT(Telephone, 3) = '".$SSareacode."' && MemberLevel = 'Associate' && Approved = 1";
		$result = mysql_query($query) or die('select of count(*) for Associate-level members in an area code failed: ' . mysql_error());
		$row = mysql_fetch_row($result);
		$AssociateCount = $row[0];
		}

	/* Display the search results by looping through $resultFriends and $resultAssociates */
	// Create title customized to whether user entered a zip code or an area code.
	$h1string = '<h1>Mediation Trainers for ';
	if (isset($SSzipcode))
		{
		$h1string .= 'Zip Code '.$SSzipcode;
		unset($SSzipcode);
		}
	if (isset($SSareacode))
		{
		$h1string .= 'Telephone Area Code '.$SSareacode;
		unset($SSareacode);
		}
	$h1string .= '</h1>';
	echo $h1string;
	while ($rowFriends = mysql_fetch_assoc($resultFriends))
		{
		if ($FriendCount == 0) break; // Break out of the loop for display of featured trainers if there are none.
		?>
		<div class="dirhead"><?=$rowFriends['EntityName']; ?></div>
		<?php
		if ($rowFriends['StreetAddress'] != '') $thestring = $rowFriends['StreetAddress'].', ';
		$thestring .= $rowFriends['City'].', '.$rowFriends['State'].' '.$rowFriends['Zip'].'&nbsp;&nbsp;|&nbsp;&nbsp;tel. '.$rowFriends['Telephone'];
		?>
		<div class="dirlist"><?=$thestring; ?></div>
		<?php
		$thestring = '';
		if ($rowFriends['EntityHomePage'] != '') 
			{
			// Strip off any http:// or https:// if any to be found in $line['EntityHomePage']
			if (strstr($rowFriends['EntityHomePage'], 'http://')) $stripped = str_replace('http://', '', $rowFriends['EntityHomePage']); else $stripped = $rowFriends['EntityHomePage']; 
			if (strstr($rowFriends['EntityHomePage'], 'https://')) $stripped = str_replace('https://', '', $rowFriends['EntityHomePage']);
			$thestring .= '<a target="secondwindow" href="'.$rowFriends['EntityHomePage'].'">'.$stripped.'</a>&nbsp;&nbsp;|&nbsp;&nbsp;';
			}
		$thestring .= '<a target="secondwindow" href="mailto:'.$rowFriends['EntityEmail'].'">'.$rowFriends['EntityEmail'].'</a>';
		?>
		<div class="dirlist"><?=$thestring; ?></div>
		<?php
		// Store all the types of mediation training that this trainer offers in an array.
		$thearray = array(); // Initialize the array
		if ($rowFriends['BasicMed']) array_push($thearray, 'Basic Mediation'); if ($rowFriends['DivorceMed']) array_push($thearray, 'Divorce Mediation'); if ($rowFriends['FamilyMed']) array_push($thearray, 'Family Mediation'); if ($rowFriends['WorkplaceMed']) array_push($thearray, 'Workplace Mediation'); if ($rowFriends['MaritalMed']) array_push($thearray, 'Marital Mediation'); if ($rowFriends['OtherMed']) array_push($thearray, 'Other');
		$thestring = implode(' | ', $thearray); 
		?>
		<div class="dirlist"><span class="smallcaps">Mediation Training:</span> <?=$thestring; ?></div>
		<?php
		// Store all TrainerNames for this entity in an associative array (by using index=1 for Trainer1, index=2 for Trainer2, etc.).
		$thearray = array(); // Initialize the array
		if ($rowFriends['TrainerName1'] != '') $thearray[1] = $rowFriends['TrainerName1'];
		if ($rowFriends['TrainerName2'] != '') $thearray[2] = $rowFriends['TrainerName2'];
		if ($rowFriends['TrainerName3'] != '') $thearray[3] = $rowFriends['TrainerName3'];
		if ($rowFriends['TrainerName4'] != '') $thearray[4] = $rowFriends['TrainerName4'];
		if ($rowFriends['TrainerName5'] != '') $thearray[5] = $rowFriends['TrainerName5'];
		if ($rowFriends['TrainerName6'] != '') $thearray[6] = $rowFriends['TrainerName6'];
		if (count($thearray) > 0)
			{
			// Append 'bio' links to each element of $thearray (e.g. $thearray[X] whenever the corresponding $rowFriends['TrainerXBio'] != ''.
			$thebioarray = array();
			$randnum = rand(); // Generate a random integer in order to assign a unique (more than likely, barring a freak coincidence by which the same random number were assigned twice) identifier for the Trio Solutions Link attribute value for the bio links below.
			if ($rowFriends['Trainer1Bio'] != '') $thearray[1] .= ' <a href="#Link'.$randnum.'Context" name="Link'.$randnum.'Context" id="Link'.$randnum.'Context" style="cursor:help" onMouseOver="javascript:createGlossary(\'TSGlossaryPanelID'.$randnum.'\', \'Trainer Bio\', \''.addslashes($rowFriends['Trainer1Bio']).'\', \'Link'.$randnum.'Context\')">[bio]</a>';
			$randnum = rand(); // Generate a random number again.
			if ($rowFriends['Trainer2Bio'] != '') $thearray[2] .= ' <a href="#Link'.$randnum.'Context" name="Link'.$randnum.'Context" id="Link'.$randnum.'Context" style="cursor:help" onMouseOver="javascript:createGlossary(\'TSGlossaryPanelID'.$randnum.'\', \'Trainer Bio\', \''.addslashes($rowFriends['Trainer2Bio']).'\', \'Link'.$randnum.'Context\')">[bio]</a>';
			$randnum = rand(); // Generate a random number again.
			if ($rowFriends['Trainer3Bio'] != '') $thearray[3] .= ' <a href="#Link'.$randnum.'Context" name="Link'.$randnum.'Context" id="Link'.$randnum.'Context" style="cursor:help" onMouseOver="javascript:createGlossary(\'TSGlossaryPanelID'.$randnum.'\', \'Trainer Bio\', \''.addslashes($rowFriends['Trainer3Bio']).'\', \'Link'.$randnum.'Context\')">[bio]</a>';
			$randnum = rand(); // Generate a random number again.
			if ($rowFriends['Trainer4Bio'] != '') $thearray[4] .= ' <a href="#Link'.$randnum.'Context" name="Link'.$randnum.'Context" id="Link'.$randnum.'Context" style="cursor:help" onMouseOver="javascript:createGlossary(\'TSGlossaryPanelID'.$randnum.'\', \'Trainer Bio\', \''.addslashes($rowFriends['Trainer4Bio']).'\', \'Link'.$randnum.'Context\')">[bio]</a>';
			$randnum = rand(); // Generate a random number again.
			if ($rowFriends['Trainer5Bio'] != '') $thearray[5] .= ' <a href="#Link'.$randnum.'Context" name="Link'.$randnum.'Context" id="Link'.$randnum.'Context" style="cursor:help" onMouseOver="javascript:createGlossary(\'TSGlossaryPanelID'.$randnum.'\', \'Trainer Bio\', \''.addslashes($rowFriends['Trainer5Bio']).'\', \'Link'.$randnum.'Context\')">[bio]</a>';
			$randnum = rand(); // Generate a random number again.
			if ($rowFriends['Trainer6Bio'] != '') $thearray[6] .= ' <a href="#Link'.$randnum.'Context" name="Link'.$randnum.'Context" id="Link'.$randnum.'Context" style="cursor:help" onMouseOver="javascript:createGlossary(\'TSGlossaryPanelID'.$randnum.'\', \'Trainer Bio\', \''.addslashes($rowFriends['Trainer6Bio']).'\', \'Link'.$randnum.'Context\')">[bio]</a>';
			$thestring = implode(' | ', $thearray); 
			?>
			<div class="dirlist"><span class="smallcaps">Trainer<?php if (count($thearray) > 1) echo 's:</span> '.$thestring; else echo ':</span> '.$thestring; ?></div>
			<?php
			}
		if ($rowFriends['Overview'] != '') $thestring = $rowFriends['Overview'];
		?>
		<div class="dirlist"><span class="smallcaps">Overview:</span> <?=$thestring; ?></div>
		<?php
		} // End of while loop for trainer simple search (Friend)

	$OthersTitleAlreadyShown = 0; // Initialize this flag to false.
	while ($rowAssociates = mysql_fetch_assoc($resultAssociates))
		{
		if ($AssociateCount == 0) break; // Break out of the loop for display of non-featured trainers if there are none.
		if ($OthersTitleAlreadyShown == 0 AND $AssociateCount > 0 && $FriendCount > 0) // Only show the "Other Trainers" subtitle (for non-featured trainers) once, and then only show it if there was at least one featured trainer.
			{
			echo '<h2 style="margin-left: 0px;">Other Trainers</h2>';
			$OthersTitleAlreadyShown = 1; // Set this flag to true to prevent redisplay of the "Other Trainers" subtitle with each iteration through the $rowAssociates loop.
			}
		?>
		<div class="dirlist"><?=$rowAssociates['EntityName']; ?></div>
		<?php
		if ($rowAssociates['StreetAddress'] != '') $thestring = $rowAssociates['StreetAddress'].', ';
		$thestring .= $rowAssociates['City'].', '.$rowAssociates['State'].' '.$rowAssociates['Zip'].'&nbsp;&nbsp;|&nbsp;&nbsp;tel. '.$rowAssociates['Telephone'];
		?>
		<div class="dirlist"><?=$thestring; ?></div>
		<?php
		$thestring = '';
		if ($rowAssociates['EntityHomePage'] != '') 
			{
			// Strip off any http:// or https:// if any to be found in $line['EntityHomePage']
			$thestring .= '<a target="secondwindow" href="'.$rowAssociates['EntityHomePage'].'">web site</a>&nbsp;&nbsp;|&nbsp;&nbsp;';
			}
		$thestring .= $rowAssociates['EntityEmail'];
		?>
		<div class="dirlist"><?=$thestring; ?></div>
		<?php
		// Store all the types of mediation training that this trainer offers in an array.
		$thearray = array(); // Initialize the array
		if ($rowAssociates['BasicMed']) array_push($thearray, 'Basic Mediation'); if ($rowAssociates['DivorceMed']) array_push($thearray, 'Divorce Mediation'); if ($rowAssociates['FamilyMed']) array_push($thearray, 'Family Mediation'); if ($rowAssociates['WorkplaceMed']) array_push($thearray, 'Workplace Mediation'); if ($rowAssociates['MaritalMed']) array_push($thearray, 'Marital Mediation'); if ($rowAssociates['OtherMed']) array_push($thearray, 'Other');
		$thestring = implode(' | ', $thearray); 
		?>
		<div class="dirlist"><span class="smallcaps">Mediation Training: </span><?=$thestring; ?></div>
		<?php
		// Store all TrainerNames for this entity in an associative array (by using index=1 for Trainer1, index=2 for Trainer2, etc.).
		$thearray = array(); // Initialize the array
		if ($rowAssociates['TrainerName1'] != '') $thearray[1] = $rowAssociates['TrainerName1'];
		if ($rowAssociates['TrainerName2'] != '') $thearray[2] = $rowAssociates['TrainerName2'];
		if ($rowAssociates['TrainerName3'] != '') $thearray[3] = $rowAssociates['TrainerName3'];
		if ($rowAssociates['TrainerName4'] != '') $thearray[4] = $rowAssociates['TrainerName4'];
		if ($rowAssociates['TrainerName5'] != '') $thearray[5] = $rowAssociates['TrainerName5'];
		if ($rowAssociates['TrainerName6'] != '') $thearray[6] = $rowAssociates['TrainerName6'];
		if (count($thearray) > 0)
			{
			$thestring = implode(' | ', $thearray); 
			?>
			<div class="dirlist"><span class="smallcaps">Trainer<?php if (count($thearray) > 1) echo 's:</span> '.$thestring; else echo ':</span> '.$thestring; ?></div>
			<?php
			}
		?>
		<br /><br />
		<?php
		} // End of while loop for trainer simple search (Associate)
	
	if ($FriendCount == 0 && $AssociateCount == 0) 	
		{
		?>
		<div class="dirlist">No mediation trainers have yet listed in the Registry for the zip code or area code entered.</div>
		<?php
		}

	} // End of if statement for SS for trainers

if (isset($_SESSION['SSforEvents'])) // User submitted a search for training events (rather than trainers) in index.php or simplesearch.php.
	{
	unset($_SESSION['SSforEvents']);
	
	if ($SSeventtype == 'online') // User submitted a search request for online training events (i.e. zip code/area code is now irrelevant)
		{
		/* Create an associative array (ordered alphabetically by EntityName) of all training event details whose sponsoring entity has MemberLevel == 'Friend' and Approved == 1 and whose EventType == 'online'. Note that the event's StartDate should actually be EARLIER THAN OR EQUAL TO (rather than later than) the current date in the case of online events. (The opposite is true for classroom events!) */
		$query = "SELECT trainers_table.EntityName AS EntityName, trainers_table.MemberLevel, events_table.*, DATE_FORMAT(events_table.StartDate, '%M %e, %Y') as StartDateFormatted, DATE_FORMAT(events_table.EbirdDeadline, '%M %e, %Y') as EbirdDeadlineFormatted FROM trainers_table, events_table WHERE trainers_table.TrainerID = events_table.TrainerID AND events_table.EventType = 'online' AND trainers_table.MemberLevel = 'Friend' AND trainers_table.Approved = 1 AND events_table.StartDate <= CURDATE() ORDER BY EntityName";
		$resultFriends = mysql_query($query) or die('The SELECT * for simple search for Friend online training events i.e. '.$query.' failed: ' . mysql_error());

		/* Count the number of online training events from Friend trainers. */
		$query = "SELECT COUNT(*) FROM trainers_table, events_table WHERE trainers_table.TrainerID = events_table.TrainerID AND events_table.EventType = 'online' AND trainers_table.MemberLevel = 'Friend' AND trainers_table.MemberLevel = 'Friend' AND trainers_table.Approved = 1 AND events_table.StartDate <= CURDATE()";
		$result = mysql_query($query) or die('select of count(*) for Friend-level online training events failed: ' . mysql_error());
		$row = mysql_fetch_row($result);
		$FriendCount = $row[0];

		/* Create a sister associative array (ordered alphabetically by EntityName) of all training event details whose sponsoring entity has MemberLevel == 'Associate' and Approved == 1 and whose EventType == 'online'. Note that the event's StartDate should actually be EARLIER THAN OR EQUAL TO (rather than later than) the current date in the case of online events. (The opposite is true for classroom events!) */
		$query = "SELECT trainers_table.EntityName AS EntityName, trainers_table.MemberLevel, events_table.*, DATE_FORMAT(events_table.StartDate, '%M %e, %Y') as StartDateFormatted, DATE_FORMAT(events_table.EbirdDeadline, '%M %e, %Y') as EbirdDeadlineFormatted FROM trainers_table, events_table WHERE trainers_table.TrainerID = events_table.TrainerID AND events_table.EventType = 'online' AND trainers_table.MemberLevel = 'Associate' AND trainers_table.Approved = 1 AND events_table.StartDate <= CURDATE() ORDER BY EntityName";
		$resultAssociates = mysql_query($query) or die('The SELECT * for simple search for Associate online training events i.e. '.$query.' failed: ' . mysql_error());

		/* Count the number of online training events from Associate trainers. */
		$query = "SELECT COUNT(*) FROM trainers_table, events_table WHERE trainers_table.TrainerID = events_table.TrainerID AND events_table.EventType = 'online' AND trainers_table.MemberLevel = 'Associate' AND trainers_table.Approved = 1 AND events_table.StartDate <= CURDATE()";
		$result = mysql_query($query) or die('select of count(*) for Associate-level online training events failed: ' . mysql_error());
		$row = mysql_fetch_row($result);
		$AssociateCount = $row[0];
		}
		
	elseif (isset($SSzipcode)) // User submitted a zip code rather than an area code
		{
		/* Create an associative array (ordered alphabetically by EntityName) of all training event details whose sponsoring entity has MemberLevel == 'Friend' and Approved = 1 and whose EventZipCode == $SSzipcode */
		$query = "SELECT trainers_table.EntityName AS EntityName, trainers_table.MemberLevel, events_table.*, DATE_FORMAT(events_table.StartDate, '%M %e, %Y') as StartDateFormatted, DATE_FORMAT(events_table.EbirdDeadline, '%M %e, %Y') as EbirdDeadlineFormatted FROM trainers_table, events_table WHERE trainers_table.TrainerID = events_table.TrainerID AND events_table.EventZipCode = '".$SSzipcode."' AND trainers_table.MemberLevel = 'Friend' AND trainers_table.Approved = 1 AND events_table.StartDate > CURDATE() ORDER BY EntityName";
		$resultFriends = mysql_query($query) or die('The SELECT for simple search training events by zip code for Friend trainers failed i.e. '.$query.' failed: ' . mysql_error());
		
		/* Count the number of such Friend events in this zip code. */
		$query = "SELECT COUNT(*) FROM trainers_table, events_table WHERE trainers_table.TrainerID = events_table.TrainerID AND events_table.EventZipCode = '".$SSzipcode."' AND trainers_table.MemberLevel = 'Friend' AND trainers_table.Approved = 1 AND events_table.StartDate > CURDATE()";
		$result = mysql_query($query) or die('select of count(*) for Friend-level members in a zip code failed: ' . mysql_error());
		$row = mysql_fetch_row($result);
		$FriendCount = $row[0];

		/* Create a sister associative array (ordered alphabetically by EntityName) of all training event details whose sponsoring entity has MemberLevel == 'Associate' and Approved = 1 and whose EventZipCode == $SSzipcode */
		$query = "SELECT trainers_table.EntityName AS EntityName, trainers_table.MemberLevel, events_table.*, DATE_FORMAT(events_table.StartDate, '%M %e, %Y') as StartDateFormatted, DATE_FORMAT(events_table.EbirdDeadline, '%M %e, %Y') as EbirdDeadlineFormatted FROM trainers_table, events_table WHERE trainers_table.TrainerID = events_table.TrainerID AND events_table.EventZipCode = '".$SSzipcode."' AND trainers_table.MemberLevel = 'Associate' AND trainers_table.Approved = 1 AND events_table.StartDate > CURDATE() ORDER BY EntityName";
		$resultAssociates = mysql_query($query) or die('The SELECT for simple search training events by zip code for Associate trainers failed i.e. '.$query.' failed: ' . mysql_error());
		
		/* Count the number of such Associate events in this zip code. */
		$query = "SELECT COUNT(*) FROM trainers_table, events_table WHERE trainers_table.TrainerID = events_table.TrainerID AND events_table.EventZipCode = '".$SSzipcode."' AND trainers_table.MemberLevel = 'Associate' AND trainers_table.Approved = 1 AND events_table.StartDate > CURDATE()";
		$result = mysql_query($query) or die('select of count(*) for Associate-level members in a zip code failed: ' . mysql_error());
		$row = mysql_fetch_row($result);
		$AssociateCount = $row[0];
		}

	elseif (isset($SSareacode)) // User submitted an area code rather than a zip code
		{
		/* Create an associative array (ordered alphabetically by EntityName) of all training event details whose sponsoring entity has MemberLevel == 'Friend' and Approved = 1 and whose EventAreaCode == $SSareacode */
		$query = "SELECT trainers_table.EntityName AS EntityName, trainers_table.MemberLevel, events_table.*, DATE_FORMAT(events_table.StartDate, '%M %e, %Y') as StartDateFormatted, DATE_FORMAT(events_table.EbirdDeadline, '%M %e, %Y') as EbirdDeadlineFormatted FROM trainers_table, events_table WHERE trainers_table.TrainerID = events_table.TrainerID AND events_table.EventAreaCode = '".$SSareacode."' AND trainers_table.MemberLevel = 'Friend' AND trainers_table.Approved = 1 AND events_table.StartDate > CURDATE() ORDER BY EntityName";
		$resultFriends = mysql_query($query) or die('The SELECT for simple search training events by area code for Friend trainers failed i.e. '.$query.' failed: ' . mysql_error());
		
		/* Count the number of such Friend events in this area code. */
		$query = "SELECT COUNT(*) FROM trainers_table, events_table WHERE trainers_table.TrainerID = events_table.TrainerID AND events_table.EventAreaCode = '".$SSareacode."' AND trainers_table.MemberLevel = 'Friend' AND trainers_table.Approved = 1 AND events_table.StartDate > CURDATE()";
		$result = mysql_query($query) or die('select of count(*) for Friend-level members in an area code failed: ' . mysql_error());
		$row = mysql_fetch_row($result);
		$FriendCount = $row[0];

		/* Create a sister associative array (ordered alphabetically by EntityName) of all training event details whose sponsoring entity has MemberLevel == 'Associate' and Approved = 1 and whose EventAreaCode == $SSareacode */
		$query = "SELECT trainers_table.EntityName AS EntityName, trainers_table.MemberLevel, events_table.*, DATE_FORMAT(events_table.StartDate, '%M %e, %Y') as StartDateFormatted, DATE_FORMAT(events_table.EbirdDeadline, '%M %e, %Y') as EbirdDeadlineFormatted FROM trainers_table, events_table WHERE trainers_table.TrainerID = events_table.TrainerID AND events_table.EventAreaCode = '".$SSareacode."' AND trainers_table.MemberLevel = 'Associate' AND trainers_table.Approved = 1 AND events_table.StartDate > CURDATE() ORDER BY EntityName";
		$resultAssociates = mysql_query($query) or die('The SELECT for simple search training events by area code for Associate trainers failed i.e. '.$query.' failed: ' . mysql_error());
		
		/* Count the number of such Associate events in this area code. */
		$query = "SELECT COUNT(*) FROM trainers_table, events_table WHERE trainers_table.TrainerID = events_table.TrainerID AND events_table.EventAreaCode = '".$SSareacode."' AND trainers_table.MemberLevel = 'Associate' AND trainers_table.Approved = 1 AND events_table.StartDate > CURDATE()";
		$result = mysql_query($query) or die('select of count(*) for Associate-level members in an area code failed: ' . mysql_error());
		$row = mysql_fetch_row($result);
		$AssociateCount = $row[0];
		}

	else // This state should never get invoked!
		{
		echo 'An unrecognized state has arisen within simplesearch.php. Please contact the <a href="mailto:paul@mediationtrainings.org">Administrator</a> at paul@mediationtrainings.org. Thank you.';
		unset($_SESSION['SSforTrainers']);
		unset($_SESSION['SSforEvents']);
		unset($_SESSION['SSzipcode']);
		unset($_SESSION['SSareacode']);
		unset($_SESSION['SSeventtype']);
		}

	// Create title customized to whether user entered a zip code or an area code.
	if (isset($SSeventtype))
		{
		$h1string = '<h1 style="margin-bottom: 0px;">Online Training Events</h1>';
		unset($SSeventtype);
		}
	elseif (isset($SSzipcode))
		{
		$h1string = '<h1 style="margin-bottom: 0px;">Training Events for Zip Code '.$SSzipcode.'</h1>';
		unset($SSzipcode);
		}
	elseif (isset($SSareacode))
		{
		$h1string = '<h1 style="margin-bottom: 0px;">Training Events for Telephone Area Code '.$SSareacode.'</h1>';
		unset($SSareacode);
		}
	echo $h1string;

	/* Unless both 	$FriendCount and $AssociateCount are zero (i.e. no events to display), display summary details of search results in a table by looping through $resultFriends and $resultAssociates. User can select some or all event(s) in the table by checking a check-box and then see further information about those events. */
	if ($FriendCount != 0 OR $AssociateCount != 0) 	
	{
	?>
	<!-- Initialize the 'noneselected' global javascript value to true i.e. no check-boxes have yet been selected. -->
	<script type="text/javascript" language="javascript">noneselected = true;</script>
	<div class="error" id="noneselectederror">Please select at least one event check-box before requesting details.</div>
	<form method="post" name="EventsMatch" action="eventsdisplay.php#details">
	<h3 style="margin-left: 0px; padding-top: 0px;">Use the check-boxes (<input type="checkbox" style="position: relative; top: 1px;" disabled>) to select and view details of one or more training events:</h3><br />
	<table id="matchingeventstable" width="800" cellspacing="0" cellpadding="6" border="0" style="font-size: 10px; font-family: Geneva, Arial, sans-serif; padding: 0px;">
	<thead>
	<tr> <!-- I struggled with and ultimately adapted the brilliant check-all implementation at http://www.shiningstar.net/articles/articles/javascript/checkboxes.asp. It was problematic because it denied me the ability to give every event check-box the same name of format "selectedID[]" (it crashed when I added the []). Use of [] seems to greatly simplify form processing. However, my clever adaptation, which entails use of loop counters for the number of "Friend" events and "Associate" events and corresponding assignment of a unique id to each Friend check-box and each Associate check-box, works great.  -->
	<th align="left"><input type="checkbox" id="selectAllIDs" name="selectAllIDs" value="checkall" onclick="function checkAll(id, boxcount) { for (i = 1; i <= boxcount; i++) document.getElementById(id+i).checked = true; }; function uncheckAll(id, boxcount) { for (i = 1; i <= boxcount; i++) document.getElementById(id+i).checked = false ; } if (this.checked) { checkAll('FriendEvent', <?=$FriendCount; ?>); checkAll('AssociateEvent', <?=$AssociateCount; ?>); } else { uncheckAll('FriendEvent', <?=$FriendCount; ?>); uncheckAll('AssociateEvent', <?=$AssociateCount; ?>); };"><span style="position: relative; bottom: 4px;">&nbsp;[all]</span></th>
	<th align="left" width="140">Training</th>
	<th align="left">Entity Name</th>
	<th align="left">Hours</th>
	<th align="left">Start Date</th>
	<th align="left">Cost</th>
	<th align="left">Early Bird</th>
	</tr>
	</thead>
	<tbody>
	<?php
	// Ordinarily, I'd be able to give each checkbox the same name (e.g. selectedID[]) for easy form processing of a group of checkboxes (see excellent http://www.webcheatsheet.com/PHP/form_processing.php). However, my use of shiningstar.net's (see above) excellent "check-all" box code denies me that possibility. So instead I must give each check-box a unique name and process the form results accordingly.
	$checkboxcounter = 1; // Initialize this checkbox counter, which will control the id value of each Friend event's checkbox.
	while ($rowFriends = mysql_fetch_assoc($resultFriends))
		{
		if ($FriendCount == 0) break; // Break out of the loop for display of featured trainers' events if there are none.
		?>
		<tr>
		<td><input type="checkbox" id="<?='FriendEvent'.$checkboxcounter; ?>" name="selectedID[]" value="<?=$rowFriends['EventID']; ?>" onClick="if (this.checked) noneselected = false; else noneselected = true;"></td>
		<td align="left">
		<?php
		$checkboxcounter = $checkboxcounter + 1; // Increment the checkbox counter
		if ($rowFriends['EventType'] == 'online') echo 'online'; else switch ($rowFriends['TrainingType'])
			{
			case '0':
				echo 'Basic training';
				break;
			case 1:
				echo 'Divorce mediation';
				break;
			case 2:
				echo 'Family mediation';
				break;
			case 3:
				echo 'Workplace mediation';
				break;
			case 4:
				echo 'Marital mediation';
				break;
			case 5:
				echo 'Elder mediation';
				break;
			default: // This is the 'Other' training type entered as a text-field (e.g. Non-violent communication)
				echo $rowFriends['TrainingType'];
				break;
			}
		?>
		</td>
		<td><?=$rowFriends['EntityName']; ?></td>
		<td><?=$rowFriends['NofHours']; ?></td>
		<td><?php if ($rowFriends['EventType'] == 'online') echo 'All dates'; else echo $rowFriends['StartDateFormatted']; ?></td>
		<td><?='$'.$rowFriends['CostStd']; ?></td>
		<td>
		<?php
		if ($rowFriends['EbirdAvailable'] == 1 AND $rowFriends['EbirdDeadline'] >= date('Y-m-d'))
			echo '$'.$rowFriends['CostEbird'];
		else
			echo 'N/A';
		?>
		</td>
		</tr>
		<?php
		}

	$checkboxcounter = 1; // Re-initialize the checkbox counter, which will control the id value of each Associate event's checkbox.
	while ($rowAssociates = mysql_fetch_assoc($resultAssociates))
		{
		if ($AssociateCount == 0) break; // Break out of the loop for display of non-featured trainers' events if there are none.
		?>
		<tr>
		<td><input type="checkbox" id="<?='AssociateEvent'.$checkboxcounter; ?>" name="selectedID[]" value="<?=$rowAssociates['EventID']; ?>"  onClick="if (this.checked) noneselected = false; else noneselected = true;"></td>
		<td align="left">
		<?php
		$checkboxcounter = $checkboxcounter + 1; // Increment the checkbox counter
		if ($rowAssociates['EventType'] == 'online') echo 'online'; else switch ($rowAssociates['TrainingType'])
			{
			case 0:
				echo 'Basic training';
				break;
			case 1:
				echo 'Divorce mediation';
				break;
			case 2:
				echo 'Family mediation';
				break;
			case 3:
				echo 'Workplace mediation';
				break;
			case 4:
				echo 'Marital mediation';
				break;
			case 5:
				echo 'Elder mediation';
				break;
			default: // This is the 'Other' training type entered as a text-field (e.g. Non-violent communication)
				echo $rowAssociates['TrainingType'];
				break;
			}
		?>
		</td>
		<td><?=$rowAssociates['EntityName']; ?></td>
		<td><?=$rowAssociates['NofHours']; ?></td>
		<td><?php if ($rowAssociates['EventType'] == 'online') echo 'All dates'; else echo $rowAssociates['StartDateFormatted']; ?></td>
		<td><?='$'.$rowAssociates['CostStd']; ?></td>
		<td>
		<?php
		if ($rowAssociates['EbirdAvailable'] == 1 AND $rowAssociates['EbirdDeadline'] >= date('Y-m-d'))
			echo '$'.$rowAssociates['CostEbird'];
		else
			echo 'N/A';
		?>
		</td>
		</tr>
		<?php
		}

	?>		
	<tr><td align="center" height="40" colspan="7"><br /><input type="submit" name="ShowEventDetails" class="buttonstylebig" value="Show Me Details" onClick="if ((noneselected) && !(document.getElementById('selectAllIDs').checked)) { document.getElementById('noneselectederror').style.display = 'block'; return false; } else { document.getElementById('noneselectederror').style.display = 'none'; return true; };"></td></tr>
	</tbody>
	</table>
	</form>

	<?php	
	}
	if ($FriendCount == 0 && $AssociateCount == 0) 	
		{
		?>
		<div class="dirlist"><br />No training events exist in the Registry for your search critera. Please try another <a href='/simplesearch.php'>Simple Search</a> or try a <a href='/powersearch.php'>Power Search</a> instead.</div><br />
		<?php
		}

	}

} // End of if statement for only processing major code block when $_SESSION['phpinvalidflag'] is not true.
?>
<div id="quicksearchbar">

<div  style="display: inline; float: left; position: relative; bottom: 5px;">
<form>
<input type="radio" class="radiobut" name="searchtype" onClick="javascript: window.location.href='/simplesearch.php';" checked>&nbsp;<label class="big">Simple Search</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br />
<input type="radio" class="radiobut" name="searchtype" onClick="javascript: window.location.href='/powersearch.php';">&nbsp;<label class="big">Power Search</label><br />
</form>
</div>

<div  style="display: inline; float: left;">
<form method="post" action="/scripts/simplesearch_slave.php">
<input type="text" class="textfield" name="TelZipTrainer" id="TelZipTrainer" maxlength="5" size="5" style="width: 50px;">&nbsp;&nbsp;
<input type="hidden" name="LocateTrainers" value="true"><!-- IE falters with passing the value of a submit button. (See http://www.dev-archive.net/articles/forms/multiple-submit-buttons.html and http://www.webdeveloper.com/forum/showthread.php?t=143073). I sidestep the issue by using a hidden field (LocateTrainers) to convey (within simplesearch_slave.php) that this form was submitted. -->
<input type="submit" class="buttonstylebig" value="Find Trainers">
<div class="greytextsmall">Enter area<br />or zip code</div>
</form>
</div>

<div  style="display: inline; float: right;">
<form method="post" action="/scripts/simplesearch_slave.php">
<select class="bigredoutline" name="EventType" id="EventType" size="1" onchange="if (this.selectedIndex == 0) { document.getElementById('TelZipEvent').style.display = 'inline'; document.getElementById('telzipeventinstrns').display = 'inline'; } else { document.getElementById('TelZipEvent').style.display = 'none'; document.getElementById('telzipeventinstrns').style.display = 'none'; document.getElementById('TelZipTrainerSpan').style.display = 'none'; document.getElementById('TelZipEventSpan').style.display = 'none'; }">
<option value="classroom" selected>Classroom</option>
<option value="online">Online</option>
</select>&nbsp;&nbsp;
<input type="text" class="textfield" name="TelZipEvent" id="TelZipEvent" maxlength="5" size="5" style="width: 50px;">&nbsp;&nbsp;
<input type="hidden" name="LocateEvents" value="true"><!-- IE falters with passing the value of a submit button. (See http://www.dev-archive.net/articles/forms/multiple-submit-buttons.html and http://www.webdeveloper.com/forum/showthread.php?t=143073). I sidestep the issue by using a hidden field (LocateEvents) to convey (within simplesearch_slave.php) that this form was submitted. -->
<input type="submit" class="buttonstylebig" value="Find Training Events">
<div class="greytextsmall">Select training type</div>
<div id="telzipeventinstrns" class="greytextsmall" style="display: inline; float: left; clear: both; position: relative; left: 115px; bottom: 19px;">Enter area<br />or zip code</div>
</form>
</div>

<br clear="all" />
<!-- Note that I provide an id for the TelZipEvent span so I can hide them (i.e. set display to 'none') when the EventType drop-down menu gets changed to 'online'. -->
<span style="position: relative; bottom: 12px;" id="TelZipTrainerSpan"><?php if ($_SESSION['MsgTelZipTrainer'] != null) { echo $_SESSION['MsgTelZipTrainer']; $_SESSION['MsgTelZipTrainer']=null; } ?></span>
<span style="position: relative; bottom: 12px; clear: both; float: right;" id="TelZipEventSpan"><?php if ($_SESSION['MsgTelZipEvent'] != null) { echo $_SESSION['MsgTelZipEvent']; $_SESSION['MsgTelZipEvent']=null; } ?></span>

</div>
<?php
ob_flush();
?>
 <!-- End of quicksearchbar div -->

<!-- InstanceEndEditable -->

<div id="footer">
<?php
require ("/home/paulme6/public_html/medtrainings/ssi/footer.php");
?>
</div>

</div>
</div>

<div id="topbar"><a href="/index.php"><img src="/images/mediationtraininglogo.jpg" alt="mediation training logo" border="0"></a></div>

<hr id="horzmenuline" color="#9C151C" width="0" noshade />

<!-- Place this tag where you want the +1 button to render (Note: I have two +1 buttons on each page.) -->
<span id="plusone"><g:plusone size="small" count="false" href="<?php
function curPageURL() // Courtesy: http://www.webcheatsheet.com/PHP/get_current_page_url.php
	{
	$pageURL = 'http';
	if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80")
		{
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} 
	else
		{
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
	return $pageURL;
	}
echo curPageURL();
?>
"></g:plusone></span>
<!--  Place this tag after the last plusone tag -->
<script type="text/javascript">
  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>

<!-- InstanceBeginEditable name="EditRegion4" -->
<hr id="horzline" color="#9C151C" width="100%" noshade />
<!-- InstanceEndEditable -->
<!-- Start of StatCounter Code -->
<script type="text/javascript">
var sc_project=5997462; 
var sc_invisible=1; 
var sc_security="a349dfab"; 
</script>
<script type="text/javascript"
src="http://www.statcounter.com/counter/counter.js"></script><noscript><div
class="statcounter"><a href="http://www.statcounter.com/godaddy_website_tonight/"
target="_blank"><img class="statcounter"
src="http://c.statcounter.com/5997462/0/a349dfab/1/"></a></div></noscript>
<!-- End of StatCounter Code -->
</body>
<!-- InstanceEnd --></html>
