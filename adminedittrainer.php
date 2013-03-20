<?php
/*
adminedittrainer.php allows an administrator (user) to search for a particular trainer (via a filter form) and then identify that trainer from among a table of trainer's who match the search criteria. The processing of this script is performed by adminedittrainer_slave.php. Once the trainer of interest has been identified, the editing of that trainer's profile takes place in adminedittrainerA.php.
*/
// Start a session
session_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Edit Trainer | Administrator of Mediation Training Registry</title>
<link href="/mediationtraining.css" rel="stylesheet" type="text/css">
</head>

<body>
<?php
$db = mysql_connect('localhost', 'paulme6_merlyn', 'fePhaCj64mkik')
or die('Could not connect: ' . mysql_error());
mysql_select_db('paulme6_medtrainers') or die('Could not select database: ' . mysql_error());
?>

<div style="text-align: center; min-height: 100%; margin-bottom: -20px; margin-left: auto; margin-right: auto; ">

<div style="margin-top: 10px; text-align: center; padding: 0px;">
<form method="post" action="/scripts/unwind.php">
<input type="submit" name="Logout" class="submitLinkSmall" value="Log Out">
</form>
</div>
			
<h1 style="margin-top: 15px; font-size: 22px;">mediationtrainings.org Administrator</h1>
<?php
require('ssi/adminmenu.php'); // Include the navigation menu.

// Create short variable names
$Authentication = $_POST['Authentication'];

if  (empty($Authentication) && $_SESSION['Authenticated'] != 'true')
	{
	// Visitor needs to authenticate
	?>
	<div style="text-align: center; width: 250px; margin: auto; padding: 15px; border: 2px solid #9C151C; border-color: #9C151C;">
	<h4 class="forms">Please authenticate yourself:</h4>
	<br>
	<form method="post" action="/adminedittrainer.php">
	<table border="0" width="280" align="center">
	<tr>
	<td align="center"><input type="password" name="Authentication" maxlength="40" size="20"></td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
	<td align="center"><input type="submit" class="buttonstyle" value="Authenticate"></td>
	</tr>
	</table>
	</form>
	</div>
	<?php
	exit;
	}
else
	{
	// See if the password entered by the user in the POST array is correct. (Note that PHP comparisons are case-sensitive [unlike MySQL query matches] and sha1 returns a lower-case result.) If it is correct or if the $_SESSION['Authenticated'] session variable was set for a previously established authentication, proceed to show either the client vs demo selection form or proceed straight to the main screen.
	if ((sha1($Authentication) == 'dc6a59aab127063fd353585bf716c7f7c34d2aa0') || $_SESSION['Authenticated'] == 'true')
		{
		$_SESSION['Authenticated'] = 'true';
			?>
			<div style="margin-left: auto; margin-right: auto; margin-top: 0px; width: 500px; padding: 15px; border: 2px solid #9C151C; border-color: #9C151C;">
			<h1 style="text-align: left;">To edit a trainer&rsquo;s profile, first find the trainer:</h1>
			<br>
			
			<form method="post" name="IdentifyTrainer" id="IdentifyTrainer" action="/scripts/adminedittrainer_slave.php">
			
			<table width="530">
			<tr>
			<td width="120" align="left" valign="middle"><label style="position: relative; bottom: 6px;">Organization Name</label></td>
			<td align="left"><input type="text" name="EntityName" id="EntityName" maxlength="50" size="20"><div class="greytextsmall">Enter all or part of the training organization&rsquo;s name</div></td>
			</tr>
			<tr>
			<td align="left" valign="middle"><label style="position: relative; bottom: 6px;">Trainer&rsquo;s Name</label></td>
			<td align="left"><input type="text" name="TrainerName" id="TrainerName" maxlength="30" size="20"><div class="greytextsmall">Enter all or part of the trainer&rsquo;s name</div></td>
			</tr>
			<tr>
			<td align="left" valign="middle"><label style="position: relative; bottom: 6px;">Username</label></td>
			<td align="left"><input type="text" name="Username" id="Username" maxlength="20" size="20"><div class="greytextsmall">Enter all or part of the trainer&rsquo;s username</div></td>
			</tr>
			<tr>
			<td align="left" valign="middle"><label style="position: relative; bottom: 6px;">Domain Name</label></td>
			<td align="left"><input type="text" name="DomainName" id="DomainName" maxlength="60" size="20"><div class="greytextsmall">Enter all or part of the entity&rsquo;s domain name</div></td>
			</tr>
			<tr>
			<td align="left" valign="middle"><label style="position: relative; bottom: 6px;">City</label></td>
			<td align="left"><input type="text" name="City" id="City" maxlength="20" size="20"><div class="greytextsmall">Enter all or  first few characters of the trainer&rsquo;s city</div></td>
			</tr>
			<tr>
			<td align="left" width="120" valign="top"><label>State</label></td>
			<td align="left">
			<select name="State" id="State" size="1">
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
			</tr>
			<tr>
			<td align="left" valign="middle"><label style="position: relative; bottom: 6px;">Zip</label></td>
			<td align="left"><input type="text" name="Zip" id="Zip" maxlength="5" size="5"><div class="greytextsmall">Exact match required when specifying zip. Use 5-digit code.</div></td>
			</tr>
			<tr>
			<td align="left" valign="middle"><label style="position: relative; bottom: 6px;">Phone Area Code</label></td>
			<td align="left"><input type="text" name="AreaCode" id="AreaCode" maxlength="3" size="3"><div class="greytextsmall">Use 3-digit area code.</div></td>
			</tr>
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr>
			<td colspan="2" align="left"><input type="submit" name="FindTrainer" class="buttonstyle" style="margin-left: 120px;" value="Find Trainer"></td>
			</tr>
			</table>
			</form>

			</div>

			<?php
			if (isset($_SESSION['FindTrainerReturn']))
			{
				unset($_SESSION['FindTrainerReturn']);
				$TrainerIDmatchesArray = $_SESSION['TrainerIDmatchesArray'];  // Load the session variable (whose value was determined in adminedittrainer_slave.php) into $TrainerIDmatchesArray for continued use inside adminedittrainer.php.
				$NofMatches = count($TrainerIDmatchesArray);  // Note: $NofMatches will be 0 when $TrainerIDmatchesArray is null from adminedittrainer_slave.php.
				switch ($NofMatches)
				{

				case 0:
					echo '<script type="text/javascript" language="javascript">alert("No trainers match your search criteria. Please try again.");</script>';
					break;
				default: // Display details of all potential trainer matches.
					?>
					<form method="post" action="adminedittrainerA.php">
					
					<script type="text/javascript" language="javascript">
					// Chrome browser detection courtesy http://davidwalsh.name/detecting-google-chrome-javascript
					var is_chrome = navigator.userAgent.toLowerCase().indexOf('chrome') > -1;
					if (is_chrome) document.write('<br>')
					</script>
					
					<div style="position: relative; left: 150px; top: 60px; width: 1000px; padding: 10px; border: 2px solid #9C151C; border-color: #9C151C;">
					<h4 class="forms" style="margin-left: 0px;">Select trainer from the database:</h4>
					<table id="matchingtrainerstable" width="940" cellspacing="0" cellpadding="6" border="0" style="font-size: 10px; font-family: Arial, Helvetica, sans-serif; padding: 0px;">
					<thead>
					<tr>
					<th align="left">&nbsp;</th>
					<th align="left" width="140">EntityName</th>
					<th align="left">City</th>
					<th align="left">State</th>
					<th align="left">TrainerID</th>
					<th align="left">Username</th>
					<th align="left">Password</th>
					<th align="left" width="180">Entity Home Page</th>
					<th align="left">EntityEmail</th>
					<th align="left">Telephone</th>
					<th width="140" align="left">Trainer Name</th>
					</tr>
					</thead>
					<tbody>
					<?php
					// Connect to DB to select details of trainers whose TrainerIDs are in the $TrainerIDmatchesArray.
					$db = mysql_connect('localhost', 'paulme6_merlyn', 'fePhaCj64mkik')
					or die('Could not connect: ' . mysql_error());
					mysql_select_db('paulme6_medtrainers') or die('Could not select database: ' . mysql_error());
					$i = 0;
					$query = "SELECT EntityName, City, State, TrainerID, Username, Password, EntityHomePage, EntityEmail, Telephone, TrainerName1 FROM trainers_table WHERE (TrainerID=".$TrainerIDmatchesArray[$i].")";
					$i=1;
					while ($i < $NofMatches)
					{
						$query .= " OR (TrainerID=".$TrainerIDmatchesArray[$i].")";
						$i++;
					}
					$result = mysql_query($query) or die('Query (select EntityName, City, State, TrainerID, Username, etc. from trainers_table for user-selected trainer search criteria) has failed: ' . mysql_error());
					while ($row = mysql_fetch_assoc($result))
					{
						$TrainerIDmatchesArray[$i] = $row['TrainerID']; // Irrelevant line of code, I think!
						echo '<tr>';
						echo '<td valign="top"><input type="checkbox" name="selectedID" value="'.$row['TrainerID'].'" onclick="if (this.checked) this.form.submit();"></td>';
						echo '<td align="left" valign="top">'.$row['EntityName'].'</td>';
						echo '<td align="left" valign="top">'.$row['City'].'</td>';
						echo '<td valign="top">'.$row['State'].'</td>';
						echo '<td valign="top">'.$row['TrainerID'].'</td>';
						echo '<td align="left" valign="top">'.$row['Username'].'</td>'; 
						echo '<td align="left" valign="top">'.$row['Password'].'</td>';
						if (strstr($row['EntityHomePage'], 'http://')) $thestring = str_replace('http://', '', $row['EntityHomePage']); else $thestring = $row['EntityHomePage'];
						if (strstr($row['EntityHomePage'], 'https://')) $thestring = str_replace('https://', '', $row['EntityHomePage']);
						echo '<td align="left" valign="top">'.$thestring.'</td>';
						echo '<td align="left" valign="top">'.$row['EntityEmail'].'</td>';
						echo '<td align="left" valign="top">'.$row['Telephone'].'</td>';
						echo '<td align="left" valign="top">'.$row['TrainerName1'].'</td>';
						echo '</tr>';
					}
					?>
					</tbody>
					</table>
					</div>
					<script type="text/javascript" language="javascript">
					location.hash = "matchingtrainerstable";
					</script>
					</form>
					<?php
					// Closing connection
					mysql_close($db);
					break;
				}
			}


		}
	else
		{
		// Authentication is denied.
		echo "<p class='basictext' style='position: absolute; left: 50%; margin-left: -260px; margin-top: 80px; font-size: 14px;'>Authentication is denied. Use your browser&rsquo;s Back button or ";
		// Include a 'Back' button for redisplaying the Authentication form.
		if (isset($_SERVER['HTTP_REFERER'])) // Antivirus software sometimes blocks HTTP_REFERER.
			{
			echo "<a style='font-size: 14px;' href='".$_SERVER['HTTP_REFERER']."'>click here</a> to try again.</p>";
			}
		else
			{
			echo "<a style='font-size: 14px;' href='javascript:history.back()'>click here</a> to try again.</p>";
			}
		}
	}
exit;
?>	
</div>
</body>
</html>