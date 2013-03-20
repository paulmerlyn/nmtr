<?php
/*
powersearch.php provides two search functionalities via a radio button. The first is to search for a trainer. The second is to search for a training event. In that way, it echoes the duality of simplesearch.php. However, powersearch.php allows much more specific and diverse search matching for particular trainers or events by entity name, training type, event format, fee, etc.
	All the form (slave) processing for a trainer search is performed within powersearch.php, including display of matching trainers. However, for event searches (similar to how simplesearch.php handles event searches), only a summary table of matching events is displayed inside powersearch.php. The user then selects some or all of those events (via check-boxes) for more detailed display in eventsdisplay.php.
*/
if (isset($_POST['FindTrainers']))
	{
	// Create short names for FindTrainers variables
	$FindTrainers = $_POST['FindTrainers'];
	$BasicMed = $_POST['BasicMed'];
	$WorkplaceMed = $_POST['WorkplaceMed'];
	$DivorceMed = $_POST['DivorceMed'];
	$MaritalMed = $_POST['MaritalMed'];
	$FamilyMed = $_POST['FamilyMed'];
	$ElderMed = $_POST['ElderMed'];
	$OtherType = $_POST['OtherType'];
	$EntityName = $_POST['EntityName'];
	$City = $_POST['City'];
	$State = $_POST['State'];
	$Zip = $_POST['Zip'];
	$TrainerAreaCode = $_POST['TrainerAreaCode'];
	$TrainerName = $_POST['TrainerName'];
	$DomainName = $_POST['DomainName'];

	// Sanitize variables that may be used in a DB query
	$OtherType = htmlentities($OtherType);
	$EntityName = htmlentities($EntityName);
	$City = htmlentities($City);
	$Zip = htmlentities($Zip);
	$TrainerAreaCode = htmlentities($TrainerAreaCode);
	$TrainerName = htmlentities($TrainerName);
	$DomainName = htmlentities($DomainName);

	if (!get_magic_quotes_gpc())
		{
		$OtherType = addslashes($OtherType);
		$EntityName = addslashes($EntityName);
		$City = addslashes($City);
		$Zip = addslashes($Zip);
		$TrainerAreaCode = addslashes($TrainerAreaCode);
		$TrainerName = addslashes($TrainerName);
		$DomainName = addslashes($DomainName);
		}
	}

if (isset($_POST['FindEvents']))
	{
	// Create short names for FindEvents variables
	$FindEvents = $_POST['FindEvents'];
	$BasicMed = $_POST['BasicMed'];
	$WorkplaceMed = $_POST['WorkplaceMed'];
	$DivorceMed = $_POST['DivorceMed'];
	$MaritalMed = $_POST['MaritalMed'];
	$FamilyMed = $_POST['FamilyMed'];
	$ElderMed = $_POST['ElderMed'];
	$OtherType = $_POST['OtherType'];
	$Format = $_POST['Format'];
	$StartPreference = $_POST['StartPreference'];
	$StartDate = $_POST['StartDate'];
	$EndPreference = $_POST['EndPreference'];
	$EndDate = $_POST['EndDate'];
	$CostPreference = $_POST['CostPreference'];
	$MaxCost = $_POST['MaxCost'];
	$EventCity = $_POST['EventCity'];
	$EventState = $_POST['EventState'];
	$EventAreaCode = $_POST['EventAreaCode'];
	$EventZipCode = $_POST['EventZipCode'];
	$EntityName = $_POST['EntityName'];
	$TrainerName = $_POST['TrainerName'];

	// Sanitize variables that take free-form user input and may be used in a DB query. I use htmlspecialchars() rather than the more extensive htmlentities() to avoid messing up the forward slashes in $StartDate and $EndDate values e.g. 06/23/2010 prior to YYYY-MM-DD format conversion below.
	$OtherType = htmlspecialchars($OtherType);
	$StartDate = htmlspecialchars($StartDate);
	$EndDate = htmlspecialchars($EndDate);
	$MaxCost = htmlspecialchars($MaxCost);
	$EventCity = htmlspecialchars($EventCity);
	$EventAreaCode = htmlspecialchars($EventAreaCode);
	$EventZipCode = htmlspecialchars($EventZipCode);
	$EntityName = htmlspecialchars($EntityName);
	$TrainerName = htmlspecialchars($TrainerName);

	// Transform the $StartDate and $EndDate user inputs from HTML form format (i.e. 'MM/DD/YYYY') into the 'YYYY-MM-DD' MySQL format.
	$datearray = explode('/', $StartDate);
	$StartDate = $datearray[2].'-'.$datearray[0].'-'.$datearray[1];

	$datearray = explode('/', $EndDate);
	$EndDate = $datearray[2].'-'.$datearray[0].'-'.$datearray[1];

	if (!get_magic_quotes_gpc())
		{
		$OtherType = addslashes($OtherType);
		$StartDate = addslashes($StartDate);
		$EndDate = addslashes($EndDate);
		$MaxCost = addslashes($MaxCost);
		$EventCity = addslashes($EventCity);
		$EventAreaCode = addslashes($EventAreaCode);
		$EventZipCode = addslashes($EventZipCode);
		$EntityName = addslashes($EntityName);
		$TrainerName = addslashes($TrainerName);
		}
	}

// Connect to DB
$db = mysql_connect('localhost', 'paulme6_merlyn', 'fePhaCj64mkik')
or die('Could not connect: ' . mysql_error());
mysql_select_db('paulme6_medtrainers') or die('Could not select database: ' . mysql_error());

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><!-- InstanceBegin template="/Templates/MasterTemplate.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<!-- InstanceBeginEditable name="doctitle" -->
<title>Power Search for Mediation Trainers and Mediation Training Events</title>
<meta NAME="description" CONTENT="Power search for mediation trainers and mediation training events in the National Mediation Training Registry.">
<meta NAME="keywords" CONTENT="search,mediation trainers,mediation training events,power search">
<!-- InstanceEndEditable -->
<link href="/mediationtraining.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="/milonic/milonic_src.js" type="text/javascript"></SCRIPT>
<script language="JavaScript" type="text/javascript">
if(ns4)_d.write("<scr"+"ipt language='JavaScript' src='/milonic/mmenuns4.js'><\/scr"+"ipt>");
else _d.write("<scr"+"ipt language='JavaScript' src='/milonic/mmenudom.js'><\/scr"+"ipt>"); </script>
<SCRIPT language='JavaScript' src="/milonic/menu_data.js" type='text/javascript'></SCRIPT>
<link rel="shortcut icon" href="/favicon.ico">
<!-- InstanceBeginEditable name="head" -->
<link href="/scripts/tigra_calendar/calendar.css" rel="stylesheet" type="text/css">
<script type="text/javascript" language="JavaScript" src="/scripts/tigra_calendar/calendar_us.js"></script>
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

<script>
function checkStartDateOnly()
{
// Validate StartDate input field.
var startDateValue = document.getElementById("StartDate").value;
var illegalCharSet = /[^0-9\/]+/; // Reject everything that contains one or more characters that is neither a slash (/) nor a digit. Note the need to escape the slash.
var reqdCharSet = /\d{2}\/\d{2}\/\d{4}/;  // Required format is MM/DD/YYYY.
if (illegalCharSet.test(startDateValue)  || !reqdCharSet.test(startDateValue))
	{
	document.getElementById("StartDateError").style.display = "inline";
	return false;
	} 
else
	{
	return true;
	}
}

function checkEndDateOnly()
{
// Validate EndDate input field.
var endDateValue = document.getElementById("EndDate").value;
var illegalCharSet = /[^0-9\/]+/; // Reject everything that contains one or more characters that is neither a slash (/) nor a digit. Note the need to escape the slash.
var reqdCharSet = /\d{2}\/\d{2}\/\d{4}/;  // Required format is MM/DD/YYYY.
if (illegalCharSet.test(endDateValue)  || !reqdCharSet.test(endDateValue))
	{
	document.getElementById("EndDateError").style.display = "inline";
	return false;
	} 
else
	{
	return true;
	}
}

/* This function hideAllErrors() is called by checkForm() and by onblur events. */
function hideAllErrors()
{
document.getElementById("StartDateError").style.display = "none";
document.getElementById("EndDateError").style.display = "none";
return true;
}
</script>
<!-- End: Trio Solutions's glossary and image preview -->
<!-- InstanceEndEditable -->
</head>

<body>
<div id="main">
<div id="relwrapper">
<!-- InstanceBeginEditable name="Content" -->
<h1>Power Search for Mediation Trainers and Training Events</h1>

<div style="margin-left: 0px; text-align: center;"> <!-- Centering div -->

<form>
<label class="big"><a name="searchagain"></a>Search for Mediation Trainers</label>&nbsp;<input type="radio" class="radiobut" name="trainerorevent" onClick="document.getElementById('eventquery').style.display = 'none'; document.getElementById('trainerquery').style.display = 'block';" <?php if (isset($FindTrainers) || !isset($FindEvents)) echo 'checked'; ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="radio" class="radiobut" name="trainerorevent" onClick="document.getElementById('trainerquery').style.display = 'none'; document.getElementById('eventquery').style.display = 'block';" <?php if (isset($FindEvents)) echo 'checked'; ?>>&nbsp;<label class="big">Search for Training Events</label><br />
</form>

<div id="trainerquery" class="<?php if (isset($FindEvents)) echo 'hideit'; else echo 'displayit'; ?>" style="margin-left: auto; margin-right: auto; margin-top: 30px; width: 750px; padding: 15px; border: 2px solid #444444; border-color: #9C151C;">

<h3 style="text-align: left; margin-left: 0px; padding-top: 4px; padding-bottom: 20px;">Limit my search. Show me only those trainers who match the following criteria:</h3>

<form method="post" action="/powersearch.php#trainers">
			
<table width="750">
<tr height="60">
<td align="left" width="150" valign="top"><label for="TrainingType" style="position:relative; top: 20px;">Training Offered</label></td>
<td align="left">
<input type="checkbox" name="BasicMed" id="BasicMed" value="true"><label>&nbsp;&nbsp;Basic Mediation</label><br>
<input type="checkbox" name="WorkplaceMed" id="WorkplaceMed" value="true"><label>&nbsp;&nbsp;Workplace Mediation</label>
</td>
<td align="left">
<div>
<input type="checkbox" name="DivorceMed" id="DivorceMed" value="true"><label>&nbsp;&nbsp;Divorce Mediation</label><br>
<input type="checkbox" name="MaritalMed" id="MaritalMed" value="true"><label>&nbsp;&nbsp;Marital Mediation</label>
</div>
</td>
<td align="left">
<input type="checkbox" name="FamilyMed" id="FamilyMed" value="true"><label>&nbsp;&nbsp;Family Mediation</label><br>
<input type="checkbox" name="ElderMed" id="ElderMed" value="true"><label>&nbsp;&nbsp;Elder Mediation</label>
</td>
</tr>
<tr>
<td align="left">&nbsp;</td>
<td colspan="3" align="left">
<div style="position: relative; bottom: 10px;">
<input type="text" class="textfieldsmall" name="OtherType" id="OtherType" maxlength="50" size="30">&nbsp;&nbsp;<label>Other (please specify)</label>
</div>
<div class="greytextsmall">To search for a particular type of training, check one or more boxes. Enter specialized types in the &lsquo;Other&rsquo; field &mdash; for example, &ldquo;narrative mediation&rdquo;. (To ensure more matches, enter only part of a keyword.) Leave all boxes unchecked/blank to include all training types. </div></td>
</tr>
<tr height="80">
<td align="left" width="150" valign="top"><label for="EntityName" style="position:relative; top: 28px;">Organization Name</label></td>
<td colspan="3" align="left"><input type="text" class="textfieldsmall" name="EntityName" id="EntityName" maxlength="40" size="30" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white';">  
  <div class="greytextsmall">For broadest results, enter only one keyword or keyphrase &mdash; example, &ldquo;Jane Doe&rdquo; rather than &ldquo;Law &amp; Mediation Office of Jane Doe&rdquo;.</div></td></tr>
<tr height="30">
<td align="left" valign="top"><label for="City" style="position:relative; top: 10px;">Location</label></td>
<td align="left" width="175"><input type="text" class="textfieldsmall" name="City" id="City" maxlength="30" size="20" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white';"></td>
<td width="250">
<label for="State">State</label>&nbsp;&nbsp;
<select name="State" class="smallredoutline" id="State" size="1">
<?php
$statesArray = array(array('&lt;&nbsp; Select a State &nbsp;&gt;',null), array('Alabama','AL'),	array('Alaska','AK'), array('Arizona','AZ'), array('Arkansas','AR'),	array('California','CA'), array('Colorado','CO'), array('Connecticut','CT'), array('Delaware','DE'), array('District of Columbia','DC'), array('Florida','FL'), array('Georgia','GA'), array('Hawaii','HI'), array('Idaho','ID'), array('Illinois','IL'), array('Indiana','IN'), array('Iowa','IA'), array('Kansas','KS'), array('Kentucky','KY'), array('Louisiana','LA'), array('Maine','ME'), array('Maryland','MD'), array('Massachusetts','MA'), array('Michigan','MI'), array('Minnesota','MN'), array('Mississippi','MS'), array('Missouri','MO'), array('Montana','MT'), array('Nebraska','NE'), array('Nevada','NV'), array('New Hampshire','NH'), array('New Jersey','NJ'), array('New Mexico','NM'), array('New York','NY'), array('North Carolina','NC'), array('North Dakota','ND'), array('Ohio','OH'), array('Oklahoma','OK'), array('Oregon','OR'), array('Pennsylvania','PA'), array('Rhode Island','RI'), array('South Carolina','SC'), array('South Dakota','SD'), array('Tennessee','TN'), array('Texas','TX'), array('Utah','UT'), array('Vermont','VT'), array('Virginia','VA'), array('Washington','WA'), array('Washington, D.C.','DC'), array('West Virginia','WV'), array('Wisconsin','WI'), array('Wyoming','WY'));
for ($i=0; $i<53; $i++)
	{
	$optiontag = '<option value="'.$statesArray[$i][1].'" ';
	$optiontag = $optiontag.'>'.$statesArray[$i][0]."</option>\n";
	echo $optiontag;
	}
?>
</select>
</td>
<td width="175" align="left">
<label>Zip</label>&nbsp;&nbsp;<input type="text" class="textfieldsmall" name="Zip" id="Zip" maxlength="5" size="5" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white';">
</td>
</tr>
<tr><td>&nbsp;</td>
<td colspan="3" valign="top" align="left">
<div class="greytextsmall">You may limit your search to a particular city (whole or partial name) and/or state and/or zip code (5 digits).</div>
</td>
</tr>
<tr height="100">
<td align="left"><label for="TrainerAreaCode" style="vertical-align: top; position: relative; bottom: 8px;">Telephone Area Code</label></td>
<td colspan="3" align="left"><input type="text" class="textfieldsmall" name="TrainerAreaCode" id="TrainerAreaCode" maxlength="3" size="3" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white';">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label for="TrainerName">Trainer</label>&nbsp;&nbsp;<input type="text" class="textfieldsmall" name="TrainerName" id="TrainerName" maxlength="30" size="16" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white';">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label for="DomainName">Domain Name</label>&nbsp;&nbsp;<input type="text" class="textfieldsmall" name="DomainName" id="DomainName" maxlength="35" size="24" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white';">
<div class="greytextsmall" style="position: relative; top: 6px;">You may also search by  area code (3 digits), trainer name (whole or partial), and/or domain name (e.g. janedoemediation.com).</div>
</td>
</tr>
<tr valign="bottom" height="20">
<td colspan="4" align="center"><input type="submit" name="FindTrainers" class="buttonstyle" style="margin-left: 0px;" value="Find Trainers"></td>
</tr>
</table>
			
</form>
	
<?php
if (isset($FindTrainers))
	{
	unset($FindTrainers); // We can unset this immediately after testing whether it was set, because all the processing for a FindTrainers button submission is done within this PHP code block.
	$query = "SELECT trainers_table.*";
	if ($ElderMed == 'true' || $OtherType != '') $query .= ", events_table.TrainingType, events_table.TrainerID"; // We only need to draw columns from the events_table if the user wants to search for Elder Mediation trainers or trainers of some other specific type.
	$query .= " FROM trainers_table";
	if ($ElderMed == 'true' || $OtherType != '') $query .= ", events_table"; // We only need to draw columns from the events_table if the user wants to search for Elder Mediation trainers or trainers of some other specific type.
	$query .= " WHERE Approved = 1 AND EntityName != ''"; // Omit a trainer if he/she isn't Approved or hasn't yet completed his/her Trainer Profile (i.e. EntityName == '')
	if ($ElderMed == 'true' || $OtherType != '') $query .= " AND events_table.TrainerID = trainers_table.TrainerID"; // We only need this join if the user wants to search for Elder Mediation trainers or trainers of some other specific type.
	if ($BasicMed) $query .= " AND trainers_table.BasicMed = 1";
	if ($DivorceMed) $query .= " AND trainers_table.DivorceMed = 1";
	if ($FamilyMed) $query .= " AND trainers_table.FamilyMed = 1";
	if ($WorkplaceMed) $query .= " AND trainers_table.WorkplaceMed = 1";
	if ($MaritalMed) $query .= " AND trainers_table.MaritalMed = 1";
	if ($ElderMed) $query .= " AND events_table.TrainingType = 5"; // Code #5 pertains to elder mediation in the TrainingType field of events_table.
	if ($OtherType !='') $query .= " AND events_table.TrainingType LIKE '%".$OtherType."%'";
	if ($EntityName != '') $query .= " AND trainers_table.EntityName LIKE '%".$EntityName."%'";
	if ($City != '') $query .= " AND trainers_table.City LIKE '%".$City."%'";
	if ($State != '') $query .= " AND trainers_table.State = '".$State."'";
	if ($Zip != '') $query .= " AND trainers_table.Zip = '".$Zip."'";
	if ($TrainerAreaCode != '') $query .= " AND LEFT(trainers_table.Telephone, 3) = '".$TrainerAreaCode."'";
	if ($TrainerName != '') $query .= " AND (trainers_table.TrainerName1 LIKE '%".$TrainerName."%' OR trainers_table.TrainerName2 LIKE '%".$TrainerName."%' OR trainers_table.TrainerName3 LIKE '%".$TrainerName."%' OR trainers_table.TrainerName4 LIKE '%".$TrainerName."%' OR trainers_table.TrainerName5 LIKE '%".$TrainerName."%' OR trainers_table.TrainerName6 LIKE '%".$TrainerName."%')";
	if ($DomainName != '') $query .= " AND trainers_table.EntityHomePage LIKE '%".$DomainName."%'";
	$query .= " ORDER BY trainers_table.MemberLevel DESC";
	
	// If $query contains a "WHERE AND" phrase, replace it with simply "WHERE ". (Since I added the 'Approved = 1' requirement, I don't think such a situation is now possible anyway.)
	$query = str_replace('WHERE AND', 'WHERE', $query);
	$result = mysql_query($query) or die('The compound SELECT for a mediation trainer power search failed i.e. '.$query.' failed: ' . mysql_error());
	?>
	<div style="text-align: left; margin-top: 36px;">
	<a name="trainers">
	<h3 style="margin-left: 0px;">These trainers match your search criteria: </h3>
	</a>
	<?php

	$AtLeastOneResult = false;
	while ($row = mysql_fetch_assoc($result))
		{
		$AtLeastOneResult = true;
		if ($row['MemberLevel'] == 'Friend')
			{
			?>
			<div class="dirhead"><?=$row['EntityName']; ?></div>
			<?php
			}
		else
			{
			?>
			<div class="dirlist" style="margin-top: 24px;"><?=$row['EntityName']; ?></div>
			<?php
			};
		$thestring = ''; // Need to reset this to blank in case $row['StreetAddress'] is empty.
		if ($row['StreetAddress'] != '') $thestring = $row['StreetAddress'].', ';
		$thestring .= $row['City'].', '.$row['State'].' '.$row['Zip'].'&nbsp;&nbsp;|&nbsp;&nbsp;tel. '.$row['Telephone'];
		?>
		<div class="dirlist"><?=$thestring; ?></div>
		<?php
		if ($row['MemberLevel'] == 'Friend')
			{
			$thestring = '';
			if ($row['EntityHomePage'] != '') 
				{
				// Strip off any http:// or https:// if any to be found in $line['EntityHomePage']
				if (strstr($row['EntityHomePage'], 'http://')) $stripped = str_replace('http://', '', $row['EntityHomePage']); else $stripped = $row['EntityHomePage']; 
				if (strstr($row['EntityHomePage'], 'https://')) $stripped = str_replace('https://', '', $row['EntityHomePage']);
				$thestring .= '<a target="secondwindow" href="'.$row['EntityHomePage'].'">'.$stripped.'</a>&nbsp;&nbsp;|&nbsp;&nbsp;';
				}
			$thestring .= '<a target="secondwindow" href="mailto:'.$row['EntityEmail'].'">'.$row['EntityEmail'].'</a>';
			?>
			<div class="dirlist"><?=$thestring; ?></div>
		<?php
			}
		else // It's an Associate member
			{
			$thestring = '';
			if ($row['EntityHomePage'] != '') 
				{
				// Strip off any http:// or https:// if any to be found in $line['EntityHomePage']
				$thestring .= '<a target="secondwindow" href="'.$row['EntityHomePage'].'">web site</a>&nbsp;&nbsp;|&nbsp;&nbsp;';
				}
			$thestring .= $row['EntityEmail'];
			?>
			<div class="dirlist"><?=$thestring; ?></div>
		<?php
			}
		// Store all the types of mediation training that this trainer offers in an array.
		$thearray = array(); // Initialize the array
		if ($row['BasicMed']) array_push($thearray, 'Basic Mediation'); if ($row['DivorceMed']) array_push($thearray, 'Divorce Mediation'); if ($row['FamilyMed']) array_push($thearray, 'Family Mediation'); if ($row['WorkplaceMed']) array_push($thearray, 'Workplace Mediation'); if ($row['MaritalMed']) array_push($thearray, 'Marital Mediation'); if ($row['OtherMed']) array_push($thearray, 'Other');
		$thestring = implode(' | ', $thearray); 
		?>
		<div class="dirlist"><span class="smallcaps">Mediation Training:</span> <?=$thestring; ?></div>
		<?php
		// Store all TrainerNames for this entity in an associative array (by using index=1 for Trainer1, index=2 for Trainer2, etc.).
		if ($row['MemberLevel'] == 'Friend')
			{
			$thearray = array(); // Initialize the array
			if ($row['TrainerName1'] != '') $thearray[1] = $row['TrainerName1'];
			if ($row['TrainerName2'] != '') $thearray[2] = $row['TrainerName2'];
			if ($row['TrainerName3'] != '') $thearray[3] = $row['TrainerName3'];
			if ($row['TrainerName4'] != '') $thearray[4] = $row['TrainerName4'];
			if ($row['TrainerName5'] != '') $thearray[5] = $row['TrainerName5'];
			if ($row['TrainerName6'] != '') $thearray[6] = $row['TrainerName6'];
			if (count($thearray) > 0)
				{
				// Append 'bio' links to each element of $thearray (e.g. $thearray[X] whenever the corresponding $row['TrainerXBio'] != ''.
				$thebioarray = array();
				$randnum = rand(); // Generate a random integer in order to assign a unique (more than likely, barring a freak coincidence by which the same random number were assigned twice) identifier for the Trio Solutions Link attribute value for the bio links below.
				if ($row['Trainer1Bio'] != '') $thearray[1] .= ' <a href="#Link'.$randnum.'Context" name="Link'.$randnum.'Context" id="Link'.$randnum.'Context" style="cursor:help" onMouseOver="javascript:createGlossary(\'TSGlossaryPanelID'.$randnum.'\', \'Trainer Bio\', \''.addslashes($row['Trainer1Bio']).'\', \'Link'.$randnum.'Context\')">[bio]</a>';
				$randnum = rand(); // Generate a random number again.
				if ($row['Trainer2Bio'] != '') $thearray[2] .= ' <a href="#Link'.$randnum.'Context" name="Link'.$randnum.'Context" id="Link'.$randnum.'Context" style="cursor:help" onMouseOver="javascript:createGlossary(\'TSGlossaryPanelID'.$randnum.'\', \'Trainer Bio\', \''.addslashes($row['Trainer2Bio']).'\', \'Link'.$randnum.'Context\')">[bio]</a>';
				$randnum = rand(); // Generate a random number again.
				if ($row['Trainer3Bio'] != '') $thearray[3] .= ' <a href="#Link'.$randnum.'Context" name="Link'.$randnum.'Context" id="Link'.$randnum.'Context" style="cursor:help" onMouseOver="javascript:createGlossary(\'TSGlossaryPanelID'.$randnum.'\', \'Trainer Bio\', \''.addslashes($row['Trainer3Bio']).'\', \'Link'.$randnum.'Context\')">[bio]</a>';
				$randnum = rand(); // Generate a random number again.
				if ($row['Trainer4Bio'] != '') $thearray[4] .= ' <a href="#Link'.$randnum.'Context" name="Link'.$randnum.'Context" id="Link'.$randnum.'Context" style="cursor:help" onMouseOver="javascript:createGlossary(\'TSGlossaryPanelID'.$randnum.'\', \'Trainer Bio\', \''.addslashes($row['Trainer4Bio']).'\', \'Link'.$randnum.'Context\')">[bio]</a>';
				$randnum = rand(); // Generate a random number again.
				if ($row['Trainer5Bio'] != '') $thearray[5] .= ' <a href="#Link'.$randnum.'Context" name="Link'.$randnum.'Context" id="Link'.$randnum.'Context" style="cursor:help" onMouseOver="javascript:createGlossary(\'TSGlossaryPanelID'.$randnum.'\', \'Trainer Bio\', \''.addslashes($row['Trainer5Bio']).'\', \'Link'.$randnum.'Context\')">[bio]</a>';
				$randnum = rand(); // Generate a random number again.
				if ($row['Trainer6Bio'] != '') $thearray[6] .= ' <a href="#Link'.$randnum.'Context" name="Link'.$randnum.'Context" id="Link'.$randnum.'Context" style="cursor:help" onMouseOver="javascript:createGlossary(\'TSGlossaryPanelID'.$randnum.'\', \'Trainer Bio\', \''.addslashes($row['Trainer6Bio']).'\', \'Link'.$randnum.'Context\')">[bio]</a>';
				$thestring = implode(' | ', $thearray); 
				?>
				<div class="dirlist"><span class="smallcaps">Trainer<?php if (count($thearray) > 1) echo 's:</span> '.$thestring; else echo ':</span> '.$thestring; ?></div>
				<?php
				}
			}
		else // Non-featured (Associate) trainer(s) (who won't be allowed a link to his/her bio
			{
			$thearray = array(); // Initialize the array
			if ($row['TrainerName1'] != '') $thearray[1] = $row['TrainerName1'];
			if ($row['TrainerName2'] != '') $thearray[2] = $row['TrainerName2'];
			if ($row['TrainerName3'] != '') $thearray[3] = $row['TrainerName3'];
			if ($row['TrainerName4'] != '') $thearray[4] = $row['TrainerName4'];
			if ($row['TrainerName5'] != '') $thearray[5] = $row['TrainerName5'];
			if ($row['TrainerName6'] != '') $thearray[6] = $row['TrainerName6'];
			if (count($thearray) > 0)
				{
				$thestring = implode(' | ', $thearray); 
				?>
				<div class="dirlist"><span class="smallcaps">Trainer<?php if (count($thearray) > 1) echo 's:</span> '.$thestring; else echo ':</span> '.$thestring; ?></div>
				<?php
				}
			}
		if ($row['MemberLevel'] == 'Friend' && $row['Overview'] != '')
			{
			$thestring = $row['Overview'];
			?>
			<div class="dirlist"><span class="smallcaps">Overview:</span> <?=$thestring; ?></div>
			<?php
			}

		}

		if (!$AtLeastOneResult) // The results set contained zero rows i.e. no matching results, so display a message to that effect.
			{
			?>
			<div class="dirlist"><br />No trainers exist in the Registry for your search critera. Please revise your search criteria and <a href='/powersearch.php#searchagain'>try again</a>.</div><br />
			<?php
			}
			?>
	</div>
	<?php

	}
?>
</div> <!-- Close of trainerquery div -->

<div id="eventquery" class="<?php if (isset($FindEvents)) echo 'displayit'; else echo 'hideit'; ?>" style="margin-left: auto; margin-right: auto; margin-top: 30px; width: 750px; padding: 15px; border: 2px solid #444444; border-color: #9C151C;">

<h3 style="text-align: left; margin-left: 0px; padding-top: 4px; padding-bottom: 20px;">Limit my search. Show me only training events that match the following criteria:</h3>

<form method="post" id="findevents" name="findevents" action="/powersearch.php#events">
			
<table width="750">
<tr height="60">
<td align="left" width="150" valign="top"><label for="TrainingType" style="position:relative; top: 20px;">Training Type</label></td>
<td align="left">
<input type="checkbox" name="BasicMed" id="BasicMed" value="true"><label>&nbsp;&nbsp;Basic Mediation</label><br>
<input type="checkbox" name="WorkplaceMed" id="WorkplaceMed" value="true"><label>&nbsp;&nbsp;Workplace Mediation</label>
</td>
<td align="left">
<div>
<input type="checkbox" name="DivorceMed" id="DivorceMed" value="true"><label>&nbsp;&nbsp;Divorce Mediation</label><br>
<input type="checkbox" name="MaritalMed" id="MaritalMed" value="true"><label>&nbsp;&nbsp;Marital Mediation</label>
</div>
</td>
<td align="left">
<input type="checkbox" name="FamilyMed" id="FamilyMed" value="true"><label>&nbsp;&nbsp;Family Mediation</label><br>
<input type="checkbox" name="ElderMed" id="ElderMed" value="true"><label>&nbsp;&nbsp;Elder Mediation</label>
</td>
</tr>
<tr height="30">
<td align="left">&nbsp;</td>
<td colspan="3" align="left" valign="top">
<input type="text" class="textfieldsmall" name="OtherType" id="OtherType" maxlength="50" size="30">&nbsp;&nbsp;<label>Other (please specify)</label>
<div class="greytextsmall" style="display: ">To find only particular types of training, check one or more boxes. Enter specialized types in &lsquo;Other&rsquo; field. For example, enter &ldquo;narrative&rdquo; to find trainings that feature narrative mediation.) Leave all boxes unchecked/blank to include all training types in your search results.</div></td>
</tr>
<tr height="50">
<td align="left" width="150" valign="top">
<label for="TrainingType" style="position:relative; top: 16px;">Format</label>
</td>
<td colspan="3" align="left">
<input type="radio" class="radiobut" name="Format" value="nopref" onClick="document.getElementById('locationrow').style.display = 'table-row'; document.getElementById('locationrowinstructions').style.display = 'table-row';" checked>&nbsp;<label>No Preference</label>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="radio" class="radiobut" name="Format" value="classroom" onClick="document.getElementById('locationrow').style.display = 'table-row'; document.getElementById('locationrowinstructions').style.display = 'table-row';">&nbsp;<label>Classroom</label>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="radio" class="radiobut" name="Format" value="online" onClick="document.getElementById('locationrow').style.display = 'none'; document.getElementById('locationrowinstructions').style.display = 'none'">&nbsp;<label>Online/Virtual</label>
</td>
</tr>
<tr>
<td height="30" align="left"><label style="position: relative; bottom: 0px;">Start Date</label></td>
<td align="left">
<select class="smallredoutline" name="StartPreference" id="StartPreference" size="1" onchange="if (this.selectedIndex == 0) { document.getElementById('StartDate').disabled = true; document.getElementById('StartDate').style.display = 'none'; document.getElementById('StartDateCalendar').style.display = 'none'; document.getElementById('CalendarInstructions').style.display = 'none';} else { document.getElementById('StartDate').style.display = 'inline'; document.getElementById('StartDate').disabled = false;  document.getElementById('StartDateCalendar').style.display = 'inline'; document.getElementById('CalendarInstructions').style.display = 'block';}">
<option value="nopref" selected>No Preference</option>
<option value="onorbefore">On or before...</option>
<option value="onorafter">On or after...</option>
</select>
<input type="text" style="display: none;" class="textfieldsmall" name="StartDate" id="StartDate" maxlength="10" size="8" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkStartDateOnly();">
<div id="StartDateCalendar" style="display: none;">
<script language="JavaScript">
	new tcal ({
	'formname': 'findevents',
	'controlname': 'StartDate'
	});
</script>
</div>
<div class="error" id="StartDateError"><br>Date format MM/DD/YYYY.<br></div>
</td>
<td colspan="2" align="left">
<label>End Date</label>&nbsp;&nbsp;
<select class="smallredoutline" name="EndPreference" id="EndPreference" size="1" onchange="if (this.selectedIndex == 0) { document.getElementById('EndDate').disabled = true; document.getElementById('EndDate').style.display = 'none'; document.getElementById('EndDateCalendar').style.display = 'none'; document.getElementById('CalendarInstructions').style.display = 'none';} else { document.getElementById('EndDate').style.display = 'inline'; document.getElementById('EndDate').disabled = false;  document.getElementById('EndDateCalendar').style.display = 'inline'; document.getElementById('CalendarInstructions').style.display = 'block';}">
<option value="nopref" selected>No Preference</option>
<option value="onorbefore">On or before...</option>
<option value="onorafter">On or after...</option>
</select>
<input type="text" style="display: none;" class="textfieldsmall" name="EndDate" id="EndDate" maxlength="10" size="8" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkEndDateOnly();">
<div id="EndDateCalendar" style="display: none;">
<script language="JavaScript">
	new tcal ({
	'formname': 'findevents',
	'controlname': 'EndDate'
	});
</script>
</div>
<div class="error" id="EndDateError"><br>Date must have format MM/DD/YYYY.<br></div>
</td>
</tr>
<tr>
<td>&nbsp;</td>
<td colspan="3" valign="top"><div id="CalendarInstructions" class="greytextsmall" style="display: none; position: relative; bottom: 12px;"><br />Specify dates in format MM/DD/YYYY. Click the calendar icon to ensure correct format.</div></td>
</tr>
<tr height="40">
<td align="left"><label for="MaxCost">Maximum Cost</label></td>
<td align="left" colspan="3">
<select class="smallredoutline" name="CostPreference" id="CostPreference" size="1" onchange="if (this.selectedIndex == 0) { document.getElementById('MaxCost').disabled = true; document.getElementById('setcost').style.display = 'none';} else { document.getElementById('setcost').style.display = 'inline'; document.getElementById('MaxCost').disabled = false; document.getElementById('MaxCost').focus(); }">
<option value="nopref" selected>No Preference</option>
<option value="lessthanorequal">Less than or equal to...</option>
</select>
&nbsp;&nbsp;
<div id="setcost" style="display: none;">
<label>$</label>&nbsp;
<input type="text" name="MaxCost" id="MaxCost" maxlength="8" size="6" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white';">
</div>
</td>
</tr>
<tr height="70" id="locationrow">
<td align="left" valign="middle"><label for="Location" style="position: relative; bottom: 10px; ">Location</label></td>
<td align="left">
<br />
<input type="text" name="EventCity" id="EventCity" class="textfieldsmall" maxlength="24" size="20" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white';">
&nbsp;&nbsp;<label>City</label>
<br ><br />
<input type="text" name="EventAreaCode" id="EventAreaCode" maxlength="3" size="3" class="textfieldsmall" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white';">
&nbsp;&nbsp;<label for="EventAreaCode">Telephone Area Code</label>
</td>
<td align="left" colspan="2">
<br />
<select name="EventState" id="EventState" class="smallredoutline" size="1">
<?php
$statesArray = array(array('&lt;&nbsp; No Preference &nbsp;&gt;',null), array('Alabama','AL'),	array('Alaska','AK'), array('Arizona','AZ'), array('Arkansas','AR'),	array('California','CA'), array('Colorado','CO'), array('Connecticut','CT'), array('Delaware','DE'), array('District of Columbia','DC'), array('Florida','FL'), array('Georgia','GA'), array('Hawaii','HI'), array('Idaho','ID'), array('Illinois','IL'), array('Indiana','IN'), array('Iowa','IA'), array('Kansas','KS'), array('Kentucky','KY'), array('Louisiana','LA'), array('Maine','ME'), array('Maryland','MD'), array('Massachusetts','MA'), array('Michigan','MI'), array('Minnesota','MN'), array('Mississippi','MS'), array('Missouri','MO'), array('Montana','MT'), array('Nebraska','NE'), array('Nevada','NV'), array('New Hampshire','NH'), array('New Jersey','NJ'), array('New Mexico','NM'), array('New York','NY'), array('North Carolina','NC'), array('North Dakota','ND'), array('Ohio','OH'), array('Oklahoma','OK'), array('Oregon','OR'), array('Pennsylvania','PA'), array('Rhode Island','RI'), array('South Carolina','SC'), array('South Dakota','SD'), array('Tennessee','TN'), array('Texas','TX'), array('Utah','UT'), array('Vermont','VT'), array('Virginia','VA'), array('Washington','WA'), array('Washington, D.C.','DC'), array('West Virginia','WV'), array('Wisconsin','WI'), array('Wyoming','WY'));
for ($i=0; $i<53; $i++)
	{
	$optiontag = '<option value="'.$statesArray[$i][1].'" ';
	if ($line['EventState'] == $statesArray[$i][1]) $optiontag = $optiontag.'selected';
	$optiontag = $optiontag.'>'.$statesArray[$i][0]."</option>\n";
	echo $optiontag;
	}
?>
</select>&nbsp;&nbsp;
<label for="EventState">State</label>
<br /><br />
<input type="text" name="EventZipCode" id="EventZipCode" maxlength="5" size="5" class="textfieldsmall" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white';">&nbsp;&nbsp;<label>Zip Code</label>
</td>
</tr>
<tr height="60" valign="top" id="locationrowinstructions">
<td>&nbsp;</td>
<td colspan="3"><div class="greytextsmall">Enter location details to limit your search. Use 3-digit telephone area code, and 5-digit zip code. Leave  fields blank for broadest results.</div></td>
</tr>
<tr>
<td height="20" align="left"><label for="EntityName" style="position: relative; bottom: 6px;">Organization Name</label></td>
<td align="left" colspan="1"><input type="text" name="EntityName" id="EntityName" class="textfieldsmall" maxlength="50" size="20" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white';">
<div class="greytextsmall">Example: Dispute Resolution Center</div>
</td>
<td align="left" colspan="2">
<label for="TrainerName">Trainer Name</label>&nbsp;&nbsp;<input type="text" name="TrainerName" id="TrainerName" class="textfieldsmall" maxlength="30" size="20" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white';">
<div class="greytextsmall" style="margin-left: 87px;">Example: Jane Doe</div>
</td>
</tr>
<tr valign="bottom" height="40">
<td colspan="4" align="center"><input type="submit" name="FindEvents" class="buttonstyle" style="margin-left: 0px;" value="Find Events" onClick="var invalidstartdate; var invalidenddate; if (document.getElementById('StartPreference').value != 'nopref') if (!checkStartDateOnly()) invalidstartdate = true; if (document.getElementById('EndPreference').value != 'nopref') if (!checkEndDateOnly()) invalidenddate = true; if (invalidstartdate || invalidenddate) return false; else return true;"></td>
</tr>
</table>

</form>

<?php
// Build the compound query for seeking events that match the user's power-search criteria.
if (isset($FindEvents))
	{
	unset($FindTrainers); // We can unset this immediately after testing whether it was set, because all the processing for a FindEvents button submission inside powersearch.php is done within this PHP code block (although control will then pass to displayevents.php thereafter when the user has selected certain events of interest for more detailed display).
	$query = "SELECT events_table.*, DATE_FORMAT(events_table.StartDate, '%M %e, %Y') as StartDateFormatted, trainers_table.MemberLevel, trainers_table.TrainerID, trainers_table.EntityName FROM events_table, trainers_table WHERE trainers_table.TrainerID = events_table.TrainerID AND events_table.StartDate >= CURDATE() AND trainers_table.Approved = 1";
	if ($BasicMed || $DivorceMed || $FamilyMed || $WorkplaceMed || $MaritalMed || $ElderMed || $OtherType != '') $query .= " AND (";
	$queryArray = array(); // Initialize array, to be used in conjunction later with an implode() statement, for purpose of creating a complex sequence of 'OR' clauses for the various TrainingType s that the power-searcher might choose.
	if ($BasicMed) array_push($queryArray, "TrainingType = '0'"); // Match to '0' rather than 0 otherwise text training type values sa "Narrative mediation" will match positively with 0.
	if ($DivorceMed) array_push($queryArray, "TrainingType = 1");
	if ($FamilyMed) array_push($queryArray, "TrainingType = 2");
	if ($WorkplaceMed) array_push($queryArray, "TrainingType = 3");
	if ($MaritalMed) array_push($queryArray, "TrainingType = 4");
	if ($ElderMed) array_push($queryArray, "TrainingType = 5");
	if ($OtherType != '') array_push($queryArray, "TrainingType LIKE '%".$OtherType."%'");
	$queryString = implode(' OR ', $queryArray);
	$query .= $queryString;
	if ($BasicMed || $DivorceMed || $FamilyMed || $WorkplaceMed || $MaritalMed || $ElderMed || $OtherType != '') $query .= ")";

	if ($Format == 'classroom') $query .= " AND EventType = 'classroom'";
	if ($Format == 'online') $query .= " AND EventType = 'online'";
	if ($StartPreference == 'onorbefore') $query .= " AND StartDate <= '".$StartDate."'";
	if ($StartPreference == 'onorafter') $query .= " AND StartDate >= '".$StartDate."'";
	if ($EndPreference == 'onorbefore') $query .= " AND EndDate <= '".$EndDate."'";
	if ($EndPreference == 'onorafter') $query .= " AND EndDate >= '".$EndDate."'";
	/* Below I use a conditional statement inside a query to obtain the value of CostEbird if EbirdAvailable and non-expired, or a very large number (I chose one million) if it isn't. I then proceed to use the LEAST comparison function to assess whether the $MaxCost that the user will pay is greater than either the CostStd or (if applicable) the CostEbird. For conditional statement, courtesy: http://lifescaler.com/2008/04/conditional-statements-inside-mysql-queries/  */
	if ($CostPreference == 'lessthanorequal') $query .= " AND ".$MaxCost." > LEAST(CostStd, IF (EbirdAvailable = 1 AND EbirdDeadline > CURDATE(), CostEbird, 1000000))"; 
	if ($EventCity != '') $query .= " AND EventCity LIKE '%".$EventCity."%'";	
	if ($EventState != '' && !is_null($EventState)) $query .= " AND EventState = '".$EventState."'";	
	if ($EventAreaCode != '') $query .= " AND EventAreaCode = '".$EventAreaCode."'";	
	if ($EventZipCode != '') $query .= " AND EventZipCode = '".$EventZipCode."'";	
	if ($EntityName != '') $query .= " AND EntityName LIKE '%".$EntityName."%'";	
	if ($TrainerName != '') $query .= " AND (TrainerName1 LIKE '%".$TrainerName."%' OR TrainerName2 LIKE '%".$TrainerName."%' OR TrainerName3 LIKE '%".$TrainerName."%' OR TrainerName4 LIKE '%".$TrainerName."%' OR TrainerName5 LIKE '%".$TrainerName."%' OR TrainerName6 LIKE '%".$TrainerName."%')";
	$query .= " ORDER BY trainers_table.MemberLevel DESC";
	$result = mysql_query($query) or die('The compound SELECT for a training events power search failed i.e. '.$query.' failed: ' . mysql_error());
	/* To fully implement my use of a 'check-all' box in the summary of events table below using javascript, I also need to separately query the DB to get the count of the number of results returned for my compound query. I now do this before presenting the summary table. */
	$querycount = str_replace("events_table.*, DATE_FORMAT(events_table.StartDate, '%M %e, %Y') as StartDateFormatted, trainers_table.MemberLevel, trainers_table.TrainerID, trainers_table.EntityName", "COUNT(*)", $query);
	$resultcount = mysql_query($querycount) or die('The count of returned training events for a power search failed i.e. '.$query.' failed: ' . mysql_error());
	$row = mysql_fetch_row($resultcount);
	$ResultsCount = $row[0];
	?>
	<div style="text-align: left; margin-top: 36px;">
	<a name="events"></a>
	<?php

	// Only display the summary table column headers and "These training events match your search criteria ..." message if $ResultsCount > 0
	if ($ResultsCount > 0)
		{ 
		?>
		<h3 style="margin-left: 0px; margin-right: 0px; margin-bottom: 24px;">These training events match your search criteria. Using the check-boxes (<input type="checkbox" style="position: relative; top: 1px;" disabled>), select at least one event to view event details.</h3>
		<!-- Initialize the 'noneselected' global javascript value to true i.e. no check-boxes have yet been selected. -->
		<script type="text/javascript" language="javascript">noneselected = true;</script>
		<div class="error" id="noneselectederror">Please select at least one event check-box before requesting details.</div>
		<form method="post" name="EventsMatch" action="eventsdisplay.php#details">
		<table id="matchingeventstable" width="800" cellspacing="0" cellpadding="6" border="0" style="font-size: 10px; font-family: Geneva, Arial, sans-serif; padding: 0px;">
		<thead>
		<tr> <!-- I struggled with and ultimately adapted the brilliant check-all implementation at http://www.shiningstar.net/articles/articles/javascript/checkboxes.asp. It was problematic because it denied me the ability to give every event check-box the same name of format "selectedID[]" (it crashed when I added the []). Use of [] seems to greatly simplify form processing. However, my clever adaptation, which entails use of loop counters for the number of events and corresponding assignment of a unique id to each check-box  works great.  -->
		<th align="left"><input type="checkbox" id="selectAllIDs" name="selectAllIDs" value="checkall" onclick="function checkAll(id, boxcount) { for (i = 1; i <= boxcount; i++) document.getElementById(id+i).checked = true; }; function uncheckAll(id, boxcount) { for (i = 1; i <= boxcount; i++) document.getElementById(id+i).checked = false ; } if (this.checked) { checkAll('Event', <?=$ResultsCount; ?>); } else { uncheckAll('Event', <?=$ResultsCount; ?>); };"><span style="position: relative; bottom: 4px;">&nbsp;[all]</span></th>
		<th align="left" width="140">Training</th>
		<th align="left" width="200">Entity Name</th>
		<th align="left">Hours</th>
		<th align="left" width="100">Start Date</th>
		<th align="left">Cost</th>
		<th align="left">Early Bird</th>
		</tr>
		</thead>
		<tbody>
	<?php
		}
	/* The following adapts code used originally in simplesearch.php */
	// Ordinarily, I'd be able to give each checkbox the same name (e.g. selectedID[]) for easy form processing of a group of checkboxes (see excellent http://www.webcheatsheet.com/PHP/form_processing.php). However, my use of shiningstar.net's (see above) excellent "check-all" box code denies me that possibility. So instead I must give each check-box a unique name and process the form results accordingly.
	$checkboxcounter = 1; // Initialize this checkbox counter, which will control the id value of each Friend event's checkbox.
	$AtLeastOneResult = false;
	while ($row = mysql_fetch_assoc($result))
		{
		$AtLeastOneResult = true;
		?>
		<tr>
		<td><input type="checkbox" id="<?='Event'.$checkboxcounter; ?>" name="selectedID[]" value="<?=$row['EventID']; ?>" onClick="if (this.checked) noneselected = false; else noneselected = true;"></td>
		<td align="left">
		<?php
		$checkboxcounter = $checkboxcounter + 1; // Increment the checkbox counter
		switch ($row['TrainingType'])
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
				echo $row['TrainingType'];
				break;
			}
		?>
		</td>
		<td><?=$row['EntityName']; ?></td>
		<td><?=$row['NofHours']; ?></td>
		<td><?=$row['StartDateFormatted']; ?></td>
		<td><?='$'.$row['CostStd']; ?></td>
		<td>
		<?php
		if ($row['EbirdAvailable'] == 1 AND $row['EbirdDeadline'] >= date('Y-m-d'))
			echo '$'.$row['CostEbird'];
		else
			echo 'N/A';
		?>
		</td>
		</tr>
		<?php
		}

	// Only show the 'Show Me Details' button if $ResultsCount > 0
	if ($ResultsCount > 0)
		{ 
		?>
		<tr><td align="center" height="40" colspan="7"><br /><input type="submit" name="ShowEventDetails" class="buttonstyle" value="Show Me Details" onClick="if ((noneselected) && !(document.getElementById('selectAllIDs').checked)) { document.getElementById('noneselectederror').style.display = 'block'; return false; } else { document.getElementById('noneselectederror').style.display = 'none'; return true; };"></td></tr>
		</tbody>
		</table>
		<?php
		}
		?>
	</form>

	<?php
	if (!$AtLeastOneResult) // The results set contained zero rows i.e. no matching results, so display a message to that effect.
		{
		?>
		<div class="dirlist"><br />No training events exist in the Registry for your search critera. Please revise your search criteria and <a href='/powersearch.php#searchagain'>try again</a>.</div><br />
		<?php
		}
		?>

	</div>
	<?php

	}
?>

</div> <!-- Close of eventquery div -->

</div> <!-- Close of centering div -->

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

<!-- InstanceBeginEditable name="EditRegion4" --><!-- InstanceEndEditable -->
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
