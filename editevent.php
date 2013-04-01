<?php
/*
editevent.php allows a trainer to select one of his/her training events then edit the information for that event as stored in the events_table. It is similar in structure to adminaddevent.php. After the trainer has logged in with a username-password, editevent.php presents the trainer with a list of all his/her training events (ordered chronologically by EndDate) so he/she can select one of them (via a check-box) to be edited or deleted. Once the user has selected an event, it hands over to editeventA.php (where the editing takes place).
*/

// Start a session
session_start();

// Connect to DB
$db = mysql_connect('localhost', 'paulme6_merlyn', '')
or die('Could not connect: ' . mysql_error());
mysql_select_db('paulme6_medtrainers') or die('Could not select database: ' . mysql_error());
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Edit Training Event | National Mediation Training Registry</title>
<meta NAME="description" CONTENT="Form to edit a training event in the National Mediation Training Registry">
<meta NAME="keywords" CONTENT="edit training event, National Mediation Training Registry">
<link href="/mediationtraining.css" rel="stylesheet" type="text/css">
<script>
function FocusFirst()
{
	if (document.forms.length > 0 && document.forms[0].elements.length > 0)
		document.forms[0].elements[0].focus();
};
</script>
</head>

<body>
<div style="text-align: center; min-height: 100%; margin-bottom: -20px; margin-left: auto; margin-right: auto; ">

<div style="margin-top: 10px; text-align: center; padding: 0px;">
<form method="post" action="/index.php">
<input type="submit" class="submitLinkSmall" value="Home">
</form>
</div>

<h1 style="margin-top: 15px; font-size: 22px; margin-left: auto; margin-right: auto; text-align: center;">Edit Training Event</h1>

<?php
$LogIn = $_POST['LogIn'];

// Present the username/password log-in screen unless the trainer has already successfully logged in i.e. $ValidatedLogIn == 1 or has clicked the "Log In" button.
if ($ValidatedLogIn != 1 && !isset($LogIn))
	{
	unset($_SESSION['SelectedEventID']);
?>
	<div style="text-align: center; width: 250px; margin-left: auto; margin-right: auto; margin-top: 30px; padding: 15px; border: 2px solid #9C151C; border-color: #9C151C;">
	<h4 class="forms">Please log in to edit an event in the registry.</h4>
	<div><a style="font-size: 10px;" href="/userpassreminder.php">Forgot your username/password?</a></div><br />
	<form method="post" action="/editevent.php">
	<table border="0" width="280" style="margin-left:0px;">
	<tr>
	<td><label for="Username">Username:&nbsp;</label></td>
	<td><input type="text" class="textfieldsmall" name="Username" id="Username" style="width:125px" maxlength="20" size="20" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'"></td>
	<!-- The style attribute ensures IE/Firefox consistency -->
	</tr>
	<tr>
	<td><label for="Password">Password:&nbsp;</label></td>
	<td><input type="password" class="textfieldsmall" name="Password" id="Password" style="width:125px" maxlength="40" size="20" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'"></td>
	</tr>
	<tr>
	<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
	<td colspan="2" align="center"><input type="submit" name="LogIn" value="Log In" class="buttonstyle">
	</td>
	</tr>
	</table>
	</form>
	</div>
<?php
	}

if (isset($LogIn)) // The user has entered a username/password to log in.
	{
	unset($LogIn);
	$Username = $_POST['Username']; // Use short names for username and password.
	$Password = $_POST['Password'];
	
	// Examine trainers_table to be sure such a username-password pair exists
	$query = "SELECT COUNT(*) FROM trainers_table WHERE Username = '".$Username."' AND Password = '".$Password."'";
	$result = mysql_query($query) or die('Query (count of matching username-password pairs in trainers_table) failed: ' . mysql_error().' and the query string was: '.$query);

	$row = mysql_fetch_row($result); // $row array should have just one item, which holds either '0' or '1'
	$count = $row[0];

	if ($count == 0) // username-password pair entered is invalid. User can now proceed to see the form for entering trainer details.
		{
		echo "<br><p class='basictext' style='margin-left: 50px; margin-right: 50px; margin-top: 40px; font-size: 14px;'>Incorrect username or password. Please use your browser&rsquo;s Back button or <a href='/editevent.php' style='font-size: 14px;'>click here</a> to try again.</p>";
		$Username = null;
		$Password = null;
		exit;
		}
	else if ($count > 1) // This condition should never arise i.e. more than one trainers have the same username-password pair.
		{
		$message = "A spurious condition has arisen in file editevent.php on the mediationtrainings.org server. This note is being sent to you as the webmaster.";
		// In case any of our lines are larger than 70 characters, we should use wordwrap()
		$message = wordwrap($message, 70);
		mail('paulmerlyn@yahoo.com', 'Spurious Condition Alert in editevent.php', $message);
		echo "<br><p class='basictext' style='margin-left: 50px; margin-right: 50px; margin-top: 40px; font-size: 14px;'>A spurious condition has been detected by the server. Our webmaster has been notified. We apologize for the inconvenience. Please use your browser&rsquo;s Back button or <a href='/editevent.php' style='font-size: 14px;'>click here</a> to try again.</p>";
		exit;
		}
	else // $count == 1 so there is one match and the username-password pair is legitimate.
		{
		// Before proceeding to show the main HTML form for editing a Trainer Profile, check whether trainer's payments are in good standing (i.e. PaidUp == 1). If they aren't, (i) set his/her Approved column to 0 in trainers_table (thereby preventing his/her Trainer Profile and events from showing up in the Registry, and (ii) require him/her to activate his/her listing by paying via the activate.php page.
		$query = "SELECT PaidUp, TrainerID FROM trainers_table WHERE Username = '".$Username."' AND Password = '".$Password."'";
		$result = mysql_query($query) or die('Query (select of PaidUp from trainers_table) failed: ' . mysql_error().' and the query string was: '.$query);
		$row = mysql_fetch_assoc($result);
		if ($row['PaidUp'] == 0) 
			{
			$_SESSION['TrainerID'] = $row['TrainerID']; // This session variable is used in activate.php as the value of the name="custom" field in the PayPal button.
			$ValidatedLogIn = 0;
			echo "<br><p class='basictext' style='margin-left: 50px; margin-right: 50px; margin-top: 40px; font-size: 14px;'>Your listing is no longer active in the National Mediation Training Registry. Please <a href='/activate.php' style='font-size: 14px;'>click here</a> to reactivate your Trainer Profile and training events in the Registry.</p>"; 
			
			$query = "UPDATE trainers_table SET Approved = 0 WHERE Username = '".$Username."' AND Password = '".$Password."'";
			$result = mysql_query($query) or die('Query (update of Approved in trainers_table) failed: ' . mysql_error().' and the query string was: '.$query);
			}
		else
			{
			$ValidatedLogIn = 1;
			$_SESSION['Username'] = $Username;
			$_SESSION['Password'] = $Password;
			$TrainerID = $row['TrainerID']; // For use by editevent_slave.php
			}
		}
	}

if ($ValidatedLogIn == 1) // Once the trainer has successfully logged in, show a concise list of the event(s) with a selectable check-box beside each event for that trainer.
	{
	unset($ValidatedLogIn);
	// Query the events_table to find out how many event(s) (if any) exist for the TrainerID associated with the logged-in trainer.
	$query = "SELECT EventID FROM events_table WHERE TrainerID = ".$TrainerID; // ... Retrieve the EventID (s) of all events that are associated with the TrainerID.
	$result = mysql_query($query) or die('Query (select of all EventIDs that associate with the TrainerID) failed: ' . mysql_error());
	$i = 0;
	while ($row = mysql_fetch_assoc($result))
		{
		$EventIDmatchesArray[$i] = $row['EventID'];
		$i += 1;
		}
	$NofMatches = count($EventIDmatchesArray); // Count the number of elements in the array
	
	if ($NofMatches == 0)
		{
		echo "<br><p class='basictext' style='margin-left: 50px; margin-right: 50px; margin-top: 40px; font-size: 14px;'>You cannot edit any events currently because you haven&rsquo;t yet added any events into the Registry. Click <a href='addevent.php' style='font-size: 14px;'>here</a> to add a training event.</p>"; 
		}
	else
		{
		// Display concise information about all the associated events.
		?>
		<form method="post" action="editeventA.php">
					
		<script type="text/javascript" language="javascript">
		// Chrome browser detection courtesy http://davidwalsh.name/detecting-google-chrome-javascript
		var is_chrome = navigator.userAgent.toLowerCase().indexOf('chrome') > -1;
		if (is_chrome) document.write('<br>')
		</script>
					
		<div style="position: relative; left: 150px; top: 60px; width: 1000px; padding: 10px; border: 2px solid #9C151C; border-color: #9C151C;">
		<h4 class="forms" style="margin-left: 0px;">Select the event that you wish to edit:</h4>
		<table id="matchingeventstable" width="940" cellspacing="0" cellpadding="6" border="0" style="font-size: 10px; font-family: Arial, Helvetica, sans-serif; padding: 0px;">
		<thead>
		<tr>
		<th align="left">&nbsp;</th>
		<th align="left" width="60">Event ID</th>
		<th align="left" width="70">Trainer ID</th>
		<th align="left">Start Date</th>
		<th align="left">End Date</th>
		<th align="left">Training</th>
		<th align="left">Event Format</th>
		<th align="left">Event Location</th>
		<th align="center" width="100">Cost (Standard)</th>
		</tr>
		</thead>
		<tbody>
<?php
		// Select details of events whose EventIDs are in the $EventIDmatchesArray.
		$i = 0;
		$query = "SELECT EventID, TrainerID, StartDate, DATE_FORMAT(StartDate, '%M %e, %Y') as StartDateFormatted, EndDate, DATE_FORMAT(EndDate, '%M %e, %Y') as EndDateFormatted, TrainingType, EventType, EventCity, EventState, CostStd FROM events_table WHERE (EventID=".$EventIDmatchesArray[$i];
		$i=1;
		while ($i < $NofMatches)
			{
			$query .= " OR EventID=".$EventIDmatchesArray[$i];
			$i++;
			}
		$query .= ") ORDER BY EndDate DESC";
		$result = mysql_query($query) or die('Query (select EventID, TrainerID, StartDate, etc. from events_table for the logged-in trainer has failed: ' . mysql_error());
		mysql_data_seek($result, 0); // Move the internal pointer back to the first row (i.e. row 0) in $result.
		while ($row = mysql_fetch_assoc($result))
			{
			echo '<tr>';
			echo '<td valign="top"><input type="checkbox" name="SelectedEventID" value="'.$row['EventID'].'" onclick="if (this.checked) this.form.submit();"></td>';
			echo '<td align="center" valign="top">'.$row['EventID'].'</td>';
			echo '<td align="center" valign="top">'.$row['TrainerID'].'</td>';
			echo '<td align="left" valign="top">'.$row['StartDateFormatted'].'</td>';
			echo '<td align="left" valign="top">'.$row['EndDateFormatted'].'</td>';
			// The key code that translates for TrainingType is defined in addevent_slave.php (and consistently so in adminaddevent_slave.php)
			switch ($row['TrainingType'])
				{
				case '0': // Note the need to test the case for '0' (string), not 0 (integer). For a non-numeric TrainingType such as "Non-Violent Communication", that TrainingType would case as 0 (integer) because "Non-Violent Communication" == 0 in a weakly typed language like PHP.
					echo '<td valign="top" align="left">Basic mediation</td>';
					break;
				case '1':
					echo '<td valign="top" align="left">Divorce mediation</td>';
					break;
				case '2':
					echo '<td valign="top" align="left">Family mediation</td>';
					break;
				case '3':
					echo '<td valign="top" align="left">Workplace mediation</td>';
					break;
				case 4:
					echo '<td valign="top" align="left">Marital mediation</td>';
					break;
				case '5':
					echo '<td valign="top" align="left">Elder mediation</td>';
					break;
				default: 
					echo '<td valign="top" align="left">'.$row['TrainingType'].'</td>';
					break; 
				}
			echo '<td align="left" valign="top">'.$row['EventType'].'</td>'; 
			if ($row['EventCity'] != '' && $row['EventState'] != '') echo '<td align="left" valign="top">'.$row['EventCity'].', '.$row['EventState'].'</td>'; else echo '<td align="left" valign="top">&nbsp;</td>';
			echo '<td align="center" valign="top">$'.$row['CostStd'].'</td>';
			echo '</tr>';
		}
?>
		</tbody>
		</table>
		</div>
		<script type="text/javascript" language="javascript">
		location.hash = "matchingeventstable";
		</script>
		</form>
<?php
		}
	}
?>	
</div>
</body>
</html>
