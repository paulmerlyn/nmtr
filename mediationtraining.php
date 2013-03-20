<?php
// Start a session
session_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><!-- InstanceBegin template="/Templates/MasterTemplate.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<!-- InstanceBeginEditable name="doctitle" -->
<title>Mediation Training Events by State</title>
<meta NAME="description" CONTENT="Gateway to a list of mediation training events by state.">
<meta NAME="keywords" CONTENT="mediation training events,Alabama,Alaska,Arizona,Arkansas,California,Colorado,Connecticut,Delaware,District of Columbia,Florida,Georgia,Hawaii,Idaho,Illinois,Indiana,Iowa,Kansas,Kentucky,Louisiana,Maine,Maryland,Massachusetts,Michigan,Minnesota,Mississippi,Missouri,Montana,Nebraska,Nevada,New Hampshire,New Jersey,New Mexico,New York,North Carolina,North Dakota,Ohio,Oklahoma,Oregon,Pennsylvania,Rhode Island,South Carolina,South Dakota,Tennessee,Texas,Utah,Vermont,Virginia,Washington,West Virginia,Wisconsin,Wyoming">
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

<h1>Mediation Training Events by State</h1>

<table width="800">
<?php
$statesArray = array(array('Alabama','AL'),	array('Alaska','AK'), array('Arizona','AZ'), array('Arkansas','AR'),	array('California','CA'), array('Colorado','CO'), array('Connecticut','CT'), array('Delaware','DE'), array('District of Columbia','DC'), array('Florida','FL'), array('Georgia','GA'), array('Hawaii','HI'), array('Idaho','ID'), array('Illinois','IL'), array('Indiana','IN'), array('Iowa','IA'), array('Kansas','KS'), array('Kentucky','KY'), array('Louisiana','LA'), array('Maine','ME'), array('Maryland','MD'), array('Massachusetts','MA'), array('Michigan','MI'), array('Minnesota','MN'), array('Mississippi','MS'), array('Missouri','MO'), array('Montana','MT'), array('Nebraska','NE'), array('Nevada','NV'), array('New Hampshire','NH'), array('New Jersey','NJ'), array('New Mexico','NM'), array('New York','NY'), array('North Carolina','NC'), array('North Dakota','ND'), array('Ohio','OH'), array('Oklahoma','OK'), array('Oregon','OR'), array('Pennsylvania','PA'), array('Rhode Island','RI'), array('South Carolina','SC'), array('South Dakota','SD'), array('Tennessee','TN'), array('Texas','TX'), array('Utah','UT'), array('Vermont','VT'), array('Virginia','VA'), array('Washington','WA'), array('West Virginia','WV'), array('Wisconsin','WI'), array('Wyoming','WY'));
$rowflag = 'odd'; // Initialize the row flag to 'odd' for the 1st row (which has five state names). Switch it to 'even' for 2nd, 4th, 6th rows, etc. (which have three state names), then back to 'odd' for 3rd, 5th, 7th, etc. rows.
for ($i=0; $i<51; $i++)
	{
	if ($rowflag == 'odd' && $i%8 == 0) echo '<tr height="35" valign="middle"><td width="100"></td>';
	if ($rowflag == 'even' && ($i-3)%8 == 0) echo '<tr height="35" valign="middle">';
?>
	<td align="center"><a href="mediation-training-<?=strtolower($statesArray[$i][1]); ?>.php"><?=$statesArray[$i][0]; ?></a></td>
<?php
	if ($rowflag == 'odd' && ($i-2)%8 == 0) 
		{
		echo '<td width="100"></td></tr>';
		$rowflag = 'even'; // Switch the flag over now to indicate an even row.
		}
	if ($rowflag == 'even' && ($i+1)%8 == 0) 
		{
		echo '</tr>';
		$rowflag = 'odd'; // Switch the flag back again to indicate an odd row.
		}
	}
?>
</table>

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
