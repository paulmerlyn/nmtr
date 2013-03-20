<?php
/*
Each of these 51 (incl DC) state pages displays details of all upcoming mediation training events. Each page is accessed when a user clicks the respective state name link on mediationtraining.php.
*/
$thestate = 'Arizona'; // Use of these state name variables makes the code below more modular.
$stateshort = 'AZ';

// Connect to DB
$db = mysql_connect('localhost', 'paulme6_merlyn', 'fePhaCj64mkik')
or die('Could not connect: ' . mysql_error());
mysql_select_db('paulme6_medtrainers') or die('Could not select database: ' . mysql_error());

// Construct query to obtain from events_table all the EventID values of current (i.e. pending) events. Join events_table to trainers_table (via TrainerID) because I want to order the results by MemberLevel (i.e. get all the 'Friend'-level IDs first, then have the events pertaining to Associate-level trainers last.
$query = "SELECT events_table.EventID, events_table.TrainerID, events_table.EndDate, events_table.EventState, trainers_table.MemberLevel FROM events_table, trainers_table WHERE events_table.EventState = '".$stateshort."' AND events_table.StartDate >= CURDATE() AND trainers_table.TrainerID = events_table.TrainerID ORDER BY trainers_table.MemberLevel DESC";
$result = mysql_query($query) or die('The SELECT for EventID values in the state of '.$thestate.' has failed i.e. '.$query.' failed: ' . mysql_error());

// Loop through the result of the query, placing all the EventID values into an array.
$EventIDsArray = array(); // Initialize the array
while ($row = mysql_fetch_assoc($result))
	{
	array_push($EventIDsArray, $row['EventID']);
	};
	
// Construct query to obtain event details for all the EventID values in $EventIDsArray(). Note the need to join with trainers_table (via TrainerID) in order to obtain the trainer's EntityName, EntityHomePage, and MemberLevel.
$query = "SELECT events_table.TrainerID, events_table.TrainingType, events_table.NofHours, events_table.NofDays, events_table.EventType, events_table.EventCity, events_table.EventState, events_table.CostStd, events_table.EbirdAvailable, events_table.CostEbird, events_table.StartDate, DATE_FORMAT(events_table.StartDate, '%M %e, %Y') AS StartDateFormatted, events_table.EndDate, DATE_FORMAT(events_table.EndDate, '%M %e, %Y') AS EndDateFormatted, events_table.EbirdDeadline, DATE_FORMAT(events_table.EbirdDeadline, '%M %e, %Y') as EbirdDeadlineFormatted, events_table.EventOverview, events_table.CertBody, events_table.RegContact, events_table.RegURL, events_table.RegEmail, events_table.RegTel, trainers_table.TrainerID, trainers_table.EntityName, trainers_table.EntityHomePage, trainers_table.MemberLevel FROM events_table, trainers_table WHERE (";
// If $EventIDsArray has at least one id value (i.e. isn't empty), obtain whichever check-boxes were checked in simplesearch.php or powersearch.php from the POST array.
$noresults = false; // Initialize
if (empty($EventIDsArray))
	{
	$noresults = true;
	}
else
	{
	foreach($EventIDsArray as $id)
		{
		$query .= ' events_table.EventID = '.$id." OR";
		}
	// Lop off from the end of $query the extraneous " OR".
	$posn = strrpos($query, ' OR');	// Find position of last instance of " OR" needle.
	$query = substr($query, 0, $posn);
	$query .= ") AND trainers_table.TrainerID = events_table.TrainerID ORDER BY trainers_table.MemberLevel DESC";
	$result = mysql_query($query) or die('The SELECT for event details from events_table and trainers_table failed i.e. '.$query.' failed: ' . mysql_error());
	};
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><!-- InstanceBegin template="/Templates/MasterTemplate.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<!-- InstanceBeginEditable name="doctitle" -->
<title>Mediation Training in <?=$thestate; ?></title>
<meta NAME="description" CONTENT="A comprehensive list of mediation training events in <?=$thestate; ?>">
<meta NAME="keywords" CONTENT="mediation training, <?=$thestate; ?>, <?=$stateshort; ?>">
<!-- InstanceEndEditable -->
<link href="/mediationtraining.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="/milonic/milonic_src.js" type="text/javascript"></SCRIPT>
<script language="JavaScript" type="text/javascript">
if(ns4)_d.write("<scr"+"ipt language='JavaScript' src='/milonic/mmenuns4.js'><\/scr"+"ipt>");
else _d.write("<scr"+"ipt language='JavaScript' src='/milonic/mmenudom.js'><\/scr"+"ipt>"); </script>
<SCRIPT language='JavaScript' src="/milonic/menu_data.js" type='text/javascript'></SCRIPT>
<link rel="shortcut icon" href="/favicon.ico">
<!-- InstanceBeginEditable name="head" --><!-- InstanceEndEditable -->
</head>

<body>
<div id="main">
<div id="relwrapper">
<!-- InstanceBeginEditable name="Content" -->
<div id="spacefillerforQSbar"></div> <!-- Only include this spacefillerforQSbar on pages where I show the Quick-Search Bar. -->

<h1>Upcoming Mediation Training Events in <?=$thestate; ?></h1>

<?php
// Unless $noresults == true, present the details of each training event whose EventID was obtained as pertaining to this particular state.
if (!$noresults) while ($row = mysql_fetch_assoc($result))
	{
	switch ($row['TrainingType'])
		{
		case '0': // Note the need to test the case for '0' (string), not 0 (integer). For a non-numeric TrainingType such as "Non-Violent Communication", that TrainingType would case as 0 (integer) because "Non-Violent Communication" == 0 in a weakly typed language like PHP.
			$thestring = 'Basic Mediation';
			break;
		case '1':
			$thestring = 'Divorce Mediation';
			break;
		case '2':
			$thestring = 'Family Mediation';
			break;
		case '3':
			$thestring = 'Workplace Mediation';
			break;
		case '4':
			$thestring = 'Marital Mediation';
			break;
		case '5':
			$thestring = 'Elder Mediation';
			break;
		default:
			$thestring = $row['TrainingType'];
			break;
		}
	?>
	<div class="dirhead"><?=$thestring; ?></div>
	<div class="dirlist"><span class="smallcaps">Duration:</span> <?=$row['NofHours']; ?>&nbsp;hours over <?=$row['NofDays'] ;?>&nbsp;days</div>
	<div class="dirlist"><span class="smallcaps">When:</span> <?php echo $row['StartDateFormatted'].'&nbsp;&nbsp;to&nbsp;&nbsp;'.$row['EndDateFormatted']; ?></div>
	<?php
	$thestring = $row['EntityName'];
	if ($row['MemberLevel'] == 'Friend' && $row['EntityHomePage'] != '') $thestring .= "&nbsp;|&nbsp;<a target='secondwindow' href='".$row['EntityHomePage']."'>more info</a>";
	?>
	<div class="dirlist"><span class="smallcaps">Trainer:</span> <?=$thestring; ?></div>
	<?php
	switch ($row['EventType'])
		{
		case 'online':
			$thestring = '<span class="smallcaps">Format:</span> online';
			break;
		case 'classroom':
			$thestring = '<span class="smallcaps">Location:</span>&nbsp;'.$row['EventCity'].', '.$row['EventState'];
			break;
		default: 
			echo 'Error: Unrecognized EventType. Please contact Administrator';
			exit;
		}
	?>
	<div class="dirlist"><?=$thestring; ?></div>
	<?php
	$thestring = '<span class="smallcaps">Cost:</span>&nbsp;$'.$row['CostStd'];
	if ($row['EbirdAvailable'] AND $row['EbirdDeadline'] >= date(Y-m-d))
		{
		$thestring .= '&nbsp;&nbsp;(Early-bird rate of $'.$row['CostEbird'].' for registration before '.$row['EbirdDeadlineFormatted'].')';
		}
	?>
	<div class="dirlist"><?=$thestring; ?></div>
	<?php
	if ($row['EventOverview'] != '') echo '<div class="dirlist"><span class="smallcaps">Overview:</span>&nbsp;'.$row['EventOverview'].'</div>';
	if ($row['CertBody'] != '') echo '<div class="dirlist"><span class="smallcaps">Certification:</span>&nbsp;'.$row['CertBody'].'</div>';
	if ($row['RegContact'] != '' OR $row['RegURL'] != '' OR $row['RegEmail'] != '' OR $row['RegTel'] != '')
		{
		$thestring = '<span class="smallcaps">Registration:</span> ';
		if ($row['RegContact'] != '') $thestring .= "&nbsp;".$row['RegContact'];
		if ($row['RegURL'] != '' && $row['MemberLevel'] == 'Friend') $thestring .= "&nbsp;|&nbsp;<a target='secondwindow' href='".$row['RegURL']."'>web</a>";
		if ($row['RegEmail'] != '') $thestring .= "&nbsp;|&nbsp;<a href='mailto:".$row['RegEmail']."'>".$row['RegEmail']."</a>";
		if ($row['RegTel'] != '') $thestring .= "&nbsp;|&nbsp;tel.&nbsp;".$row['RegTel'];
		?>
		<div class="dirlist"><?=$thestring; ?></div>
		<?php
		}
	}
else
	{
	?>
	<div class="dirlist">No training events exist in the Registry for this state.</div>
	<?php
	}
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
<input type="submit" name="LocateTrainers" class="buttonstylebig" value="Find Trainers">
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
<input type="submit" name="LocateEvents" class="buttonstylebig" value="Find Training Events">
<div class="greytextsmall">Select training type</div>
<div id="telzipeventinstrns" class="greytextsmall" style="display: inline; float: left; clear: both; position: relative; left: 115px; bottom: 19px;">Enter area<br />or zip code</div>
</form>
</div>

<br clear="all" />
<!-- Note that I provide an id for the TelZipEvent span so I can hide them (i.e. set display to 'none') when the EventType drop-down menu gets changed to 'online'. -->
<span style="position: relative; bottom: 12px;" id="TelZipTrainerSpan"><?php if ($_SESSION['MsgTelZipTrainer'] != null) { echo $_SESSION['MsgTelZipTrainer']; $_SESSION['MsgTelZipTrainer']=null; } ?></span>
<span style="position: relative; bottom: 12px; clear: both; float: right;" id="TelZipEventSpan"><?php if ($_SESSION['MsgTelZipEvent'] != null) { echo $_SESSION['MsgTelZipEvent']; $_SESSION['MsgTelZipEvent']=null; } ?></span>

</div> <!-- End of quicksearchbar div -->
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
<!-- Horizontal line under Quick Search bar -->
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
