<?php
/*
addtrainer.php allows a trainer to add details of his/her Trainer Profile to the trainers_table. (He/she will already have a TrainerID and Username and Password after having joined the Registry via join.php.)
	This script first has to determine whether the user should be presented with a username/password login screen. The Trainer will (i) either come to addtrainer.php after having clicked the "CreateTrainerProfileNow" button in offramp.php (the landing page after having paid a subscription fee via PayPal), in which case his/her Username/Password will have been stored as session variables $_SESSION['Username'] and $_SESSION['Password'] in offramp.php ready for use by addtrainer.php, rendering login unncessary; or (ii) he/she will come to addtrainer.php via the Registry menu, in which case he/she will need to manually type in the Username/Password that he/she was given (on-screen and via email) within offramp.php when they previously subscribed to the Registry (unless the $_SESSION['Username'] and $_SESSION['Password'] session variables still exist).
*/

// Start a session
session_start();

ob_start();

// Connect to DB
$db = mysql_connect('localhost', 'paulme6_merlyn', 'fePhaCj64mkik')
or die('Could not connect: ' . mysql_error());
mysql_select_db('paulme6_medtrainers') or die('Could not select database: ' . mysql_error());
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Add Trainer | National Mediation Training Registry</title>
<meta NAME="description" CONTENT="Form to add a mediation trainer to the National Mediation Training Registry">
<meta NAME="keywords" CONTENT="add trainer, National Mediation Training Registry">
<link href="/mediationtraining.css" rel="stylesheet" type="text/css">
<script type="text/javascript" language="javascript" src="/scripts/emailaddresschecker.js"></script>
<script type="text/javascript" src="/scripts/characterCounter.js"></script>
<script>
function showHideRow(rowID, elemID)
{
var row = document.getElementById(rowID);
if (row.style.display == '') 
	{
	document.getElementById(elemID).value = '';
	row.style.display = 'none';
	}
else row.style.display = '';
}

/* Begin JS form validation functions. */
function checkEntityNameOnly()
{
// Validate EntityName field.
var entityNameValue = document.getElementById("EntityName").value;
illegalCharSet = /[^A-Za-z'0-9!,\/\-\.\& ]+/; // Exclude everything except A-Z, a-z, !, apostrophe ('), 0-9, ampersand, comma, period, slash, hyphen, space.
reqdCharSet = /^[A-Za-z0-9!][a-zA-Z0-9!'\.]+/;  // Names of form initial capital (e.g. San Jose-Gilroy) followed by potentially a period (e.g. N. Chicago) or lower case. May include dashes, slashes, or spaces. Also supports names like "D'Angelo".
if (illegalCharSet.test(entityNameValue) || !(reqdCharSet.test(entityNameValue)))
	{
	document.getElementById("EntityNameError").style.display = "inline";
	anchordestn = "#EntityNameAnchor";
	return false;
	} 
else
	{
	return true;
	}
}

function checkStreetAddressOnly()
{
// Validate StreetAddress (optional) field.
var streetAddressValue = document.getElementById("StreetAddress").value;
illegalCharSet = /[^A-Za-z'0-9,\-\.\# ]+/; // Exclude everything except A-Z, a-z, apostrophe ('), 0-9, #, comma, period, hyphen, space.
if (document.getElementById('StreetAddress').value != null && document.getElementById('StreetAddress').value != '')
	{
	if (illegalCharSet.test(streetAddressValue))
		{
		document.getElementById("StreetAddressError").style.display = "inline";
		anchordestn = "#StreetAddressAnchor";
		return false;
		} 
	else
		{
		return true;
		}
	}
}

function checkCityOnly()
{
// Validate City field.
var cityValue = document.getElementById("City").value;
var cityLength = cityValue.length;
illegalCharSet = /[^A-Za-z'\-\.\(\) ]+/; // Exclude everything except A-Z, a-z, apostrophe ('), period, hyphen, space, or parentheses.
reqdCharSet = /^[A-Z][a-z'\.]+[(\-|\/| |)[A-Za-z\-\.\/\(\) ]+/;  // Names of form initial capital (e.g. San Jose-Gilroy) followed by potentially a period (e.g. N. Chicago) or lower case. May include dashes, slashes, or spaces. Also may have ( and ) for "(Test)" appendage. Also supports names like "D'Angelo".
if (cityLength < 3 || illegalCharSet.test(cityValue) || !(reqdCharSet.test(cityValue)))
	{
	document.getElementById("CityError").style.display = "inline";
	anchordestn = "#CityAnchor";
	return false;
	} 
else
	{
	return true;
	}
}

function checkStateOnly()
{
// Validate State selection.
if (document.getElementById('State').selectedIndex == 0)
	{
	document.getElementById("StateError").style.display = "inline";
	return false;
	} 
else
	{
	return true;
	}
}

function checkZipOnly()
{
// Validate Zip field.
var zipValue = document.getElementById("Zip").value;
reqdCharSet = /^(\d{5})$/;  
if (!reqdCharSet.test(zipValue))
	{
	document.getElementById("ZipError").style.display = "inline";
	anchordestn = "#ZipAnchor";
	return false;
	} 
else
	{
	return true;
	}
}

function checkTelephoneOnly()
{
// Validate Telephone (Tel1, Tel2, Tel3 fields).
var tel1Value = document.getElementById("Tel1").value;
var tel2Value = document.getElementById("Tel2").value;
var tel3Value = document.getElementById("Tel3").value;
var tel1Length = tel1Value.length;
var tel2Length = tel2Value.length;
var tel3Length = tel3Value.length;
illegalCharSet = /[^0-9]+/; // Reject everything that contains one or more non-digits.
if (illegalCharSet.test(tel1Value) || illegalCharSet.test(tel2Value) || illegalCharSet.test(tel3Value) || tel1Length!=3 || tel2Length!=3 || tel3Length!=4)
	{
	document.getElementById("TelephoneError").style.display = "inline";
	anchordestn = "#TelephoneAnchor";
	return false;
	} 
else
	{
	return true;
	}
}

function checkTrainingsOfferedOnly()
{
if (document.getElementById('BasicMed').checked == false && document.getElementById('DivorceMed').checked == false && document.getElementById('FamilyMed').checked == false && document.getElementById('WorkplaceMed').checked == false && document.getElementById('MaritalMed').checked == false && document.getElementById('OtherMed').checked == false)
	{
	document.getElementById("TrainingsOfferedError").style.display = "inline";
	anchordestn = "#TrainingsOfferedAnchor";
	return false;
	}
else
	{
	return true;
	}
}

function checkEntityHomePageOnly()
{
// Validate HomePage field. Note: also admits "home page" URLs of form www.mediate.com/janedoe
reqdCharSet = /^(http:\/\/|https:\/\/)?(([\w|\-])+\.)+(com|edu|org|us|net|biz|gov|mobi|mil|ca|uk|hk|asia|tv|jobs|bz|cc|co)+/;
var entityHomePageValue = document.getElementById("EntityHomePage").value;
var entityHomePageLength = entityHomePageValue.length;
if (entityHomePageValue != null && entityHomePageValue  != '') // Since EntityHomePage is optional, only validate if it's non-blank.
	{
	if (!reqdCharSet.test(entityHomePageValue))
		{
		document.getElementById("EntityHomePageError").style.display = "inline";
		anchordestn = "#EntityHomePageAnchor";
		return false;
		} 
	else
		{
		return true;
		}
	}
} 

function checkEntityEmailOnly()
{
// Validate EntityEmail field (a required field).
var entityEmailValue = document.getElementById("EntityEmail").value;
var entityEmailLength = entityEmailValue.length;
	if (entityEmailValue > 60 || !(emailCheck(entityEmailValue,'noalert'))) // emailCheck() is function in emailaddresscheker.js. This field is reqd i.e. blank causes a rejection as invalid.
	{
	document.getElementById("EntityEmailError").style.display = "inline";
	anchordestn = "#EntityEmailAnchor";
	return false;
	}
else
	{
	return true;
	}
} 

function checkOverviewOnly()
{
// Validate Overview field.
var overviewValue = document.getElementById("Overview").value;
overviewValue = overviewValue.replace(/^\s*|\s*$/g,''); // PHP inside adminaddtrainer_slave.php will trim white space from beginning and end of the string, so there's no need to "catch" any such white space during Javascript validation. Better to avoid annoying user by just trimming it here inside admintrainer.php then JS-validating what remains of the overviewValue string. (Note: JS doesn't have a trim() function analogous to PHP, so I build one using JS's replace() method.
var overviewLength = overviewValue.length;
illegalCharSet1 = /[\r\n\t\f\v]+/; // Reject special characters return, newline, tab, form feed, vertical tab.

if (illegalCharSet1.test(overviewValue) || (overviewLength>450)) // Check the validity of the user's Overview text.
	{
	document.getElementById("OverviewError").style.display = "inline";
	anchordestn = "#OverviewAnchor";
	return false;
	} 
else
	{
	return true;
	}
}

function checkTrainerNameOnly(TrainerName, TrainerNameError, TrainerNameAnchor)
{
// Validate TrainerName#N fields (multiple).
var trainerNameValue = document.getElementById(TrainerName).value;
illegalCharSet = /[^A-Za-z'0-9,\/\-\.\& ]+/; // Exclude everything except A-Z, a-z, apostrophe ('), 0-9, ampersand, comma, period, slash, hyphen, space.
if (illegalCharSet.test(trainerNameValue))
	{
	document.getElementById(TrainerNameError).style.display = "inline";
	anchordestn = "#" + TrainerNameAnchor;
	return false;
	} 
else
	{
	return true;
	}
}

function checkTrainerBioOnly(TrainerBio, TrainerBioError, TrainerBioAnchor)
{
// Validate TrainerBio#N fields.
var trainerBioValue = document.getElementById(TrainerBio).value;
trainerBioValue = trainerBioValue.replace(/^\s*|\s*$/g,''); // PHP inside adminaddtrainer_slave.php will trim white space from beginning and end of the string, so there's no need to "catch" any such white space during Javascript validation. Better to avoid annoying user by just trimming it here inside adminaddtrainer.php then JS-validating what remains of the overviewValue string. (Note: JS doesn't have a trim() function analogous to PHP, so I build one using JS's replace() method.
var trainerBioLength = trainerBioValue.length;
illegalCharSet1 = /[\r\n\t\f\v]+/; // Reject special characters return, newline, tab, form feed, vertical tab.

if (illegalCharSet1.test(trainerBioValue) || (trainerBioLength>900)) // Check the validity of the user's TrainerBio text.
	{
	document.getElementById(TrainerBioError).style.display = "inline";
	anchordestn = "#" + TrainerBioAnchor;
	return false;
	} 
else
	{
	return true;
	}
}

function checkBackLinkPageOnly()
{
// Validate Back Link field. Note: also admits "home page" URLs of form www.mediate.com/janedoe
if (document.getElementById('MemberLevelFriend').checked) // Since BackLinkPage value is only relevant when MemberLevelFriend radio button is checked, only validate if that element is checked.
	{
	reqdCharSet = /^(http:\/\/|https:\/\/)?((\w)+\.)+(com|edu|org|us|net|biz|gov|mobi|mil|ca|uk|hk|asia|tv|jobs|bz)+/;
	var backLinkPageValue = document.getElementById("BackLinkPage").value;
	var backLinkPageLength = backLinkPageValue.length;
	if (!reqdCharSet.test(backLinkPageValue))
		{
		document.getElementById("BackLinkPageError").style.display = "inline";
		anchordestn = "#BackLinkPageAnchor";
		return false;
		} 
	else
		{
		return true;
		}
	}
else
	{
	return true;
	};
} 

function checkForm() // checkForm() gets called when the user clicks the submit button.
{
hideAllErrors();
if (!checkEntityNameOnly()) { document.getElementById("AnyValidationError").style.display = "inline"; return false; };
if (document.getElementById('StreetAddress').value != null && document.getElementById('StreetAddress').value != '')
	{
	if (!checkStreetAddressOnly()) { document.getElementById("AnyValidationError").style.display = "inline"; return false; };
	}
if (!checkCityOnly()) { document.getElementById("AnyValidationError").style.display = "inline"; return false; };
if (!checkStateOnly()) { document.getElementById("AnyValidationError").style.display = "inline"; return false; };
if (!checkZipOnly()) { document.getElementById("AnyValidationError").style.display = "inline"; return false; };
if (!checkTelephoneOnly()) { document.getElementById("AnyValidationError").style.display = "inline"; return false; };
if (!checkTrainingsOfferedOnly()) { document.getElementById("AnyValidationError").style.display = "inline"; return false; };
if (document.getElementById('EntityHomePage').value != null && document.getElementById('EntityHomePage').value != '')
	{
	if (!checkEntityHomePageOnly()) { document.getElementById("AnyValidationError").style.display = "inline"; return false; };
	}
if (!checkEntityEmailOnly()) { document.getElementById("AnyValidationError").style.display = "inline"; return false; };
if (document.getElementById('Overview').value != null && document.getElementById('Overview').value != '')
	{
	if (!checkOverviewOnly()) { document.getElementById("AnyValidationError").style.display = "inline"; return false; };
	}
if (document.getElementById('TrainerName1').value != null && document.getElementById('TrainerName1').value != '')
	{
	if (!checkTrainerNameOnly('TrainerName1', 'TrainerName1Error', 'TrainerName1Anchor')) { document.getElementById("AnyValidationError").style.display = "inline"; return false; };
	}
if (document.getElementById('Trainer1Bio').value != null && document.getElementById('Trainer1Bio').value != '')
	{
	if (!checkTrainerBioOnly('Trainer1Bio', 'Trainer1BioError', 'Trainer1BioAnchor')) { document.getElementById("AnyValidationError").style.display = "inline"; return false; };
	}
if (document.getElementById('TrainerName2').value != null && document.getElementById('TrainerName2').value != '')
	{
	if (!checkTrainerNameOnly('TrainerName2', 'TrainerName2Error', 'TrainerName2Anchor')) { document.getElementById("AnyValidationError").style.display = "inline"; return false; };
	}
if (document.getElementById('Trainer2Bio').value != null && document.getElementById('Trainer2Bio').value != '')
	{
	if (!checkTrainerBioOnly('Trainer2Bio', 'Trainer2BioError', 'Trainer2BioAnchor')) { document.getElementById("AnyValidationError").style.display = "inline"; return false; };
	}
if (document.getElementById('TrainerName3').value != null && document.getElementById('TrainerName3').value != '')
	{
	if (!checkTrainerNameOnly('TrainerName3', 'TrainerName3Error', 'TrainerName3Anchor')) { document.getElementById("AnyValidationError").style.display = "inline"; return false; };
	}
if (document.getElementById('Trainer3Bio').value != null && document.getElementById('Trainer3Bio').value != '')
	{
	if (!checkTrainerBioOnly('Trainer3Bio', 'Trainer3BioError', 'Trainer3BioAnchor')) { document.getElementById("AnyValidationError").style.display = "inline"; return false; };
	}
if (document.getElementById('TrainerName4').value != null && document.getElementById('TrainerName4').value != '')
	{
	if (!checkTrainerNameOnly('TrainerName4', 'TrainerName4Error', 'TrainerName4Anchor')) { document.getElementById("AnyValidationError").style.display = "inline"; return false; };
	}
if (document.getElementById('Trainer4Bio').value != null && document.getElementById('Trainer4Bio').value != '')
	{
	if (!checkTrainerBioOnly('Trainer4Bio', 'Trainer4BioError', 'Trainer4BioAnchor')) { document.getElementById("AnyValidationError").style.display = "inline"; return false; };
	}
if (document.getElementById('TrainerName5').value != null && document.getElementById('TrainerName5').value != '')
	{
	if (!checkTrainerNameOnly('TrainerName5', 'TrainerName5Error', 'TrainerName5Anchor')) { document.getElementById("AnyValidationError").style.display = "inline"; return false; };
	}
if (document.getElementById('Trainer5Bio').value != null && document.getElementById('Trainer5Bio').value != '')
	{
	if (!checkTrainerBioOnly('Trainer5Bio', 'Trainer5BioError', 'Trainer5BioAnchor')) { document.getElementById("AnyValidationError").style.display = "inline"; return false; };
	}
if (document.getElementById('TrainerName6').value != null && document.getElementById('TrainerName6').value != '')
	{
	if (!checkTrainerNameOnly('TrainerName6', 'TrainerName6Error', 'TrainerName6Anchor')) { document.getElementById("AnyValidationError").style.display = "inline"; return false; };
	}
if (document.getElementById('Trainer6Bio').value != null && document.getElementById('Trainer6Bio').value != '')
	{
	if (!checkTrainerBioOnly('Trainer6Bio', 'Trainer6BioError', 'Trainer6BioAnchor')) { document.getElementById("AnyValidationError").style.display = "inline"; return false; };
	}
if (!checkBackLinkPageOnly()) { document.getElementById("AnyValidationError").style.display = "inline"; return false; };
return true; // All elements passed their validity checks, so return a true.
} // End of checkForm()

/* This function hideAllErrors() is called by checkForm() and by onblur event. */
function hideAllErrors()
{
document.getElementById("EntityNameError").style.display = "none";
document.getElementById("StreetAddressError").style.display = "none";
document.getElementById("CityError").style.display = "none";
document.getElementById("StateError").style.display = "none";
document.getElementById("ZipError").style.display = "none";
document.getElementById("TelephoneError").style.display = "none";
document.getElementById("TrainingsOfferedError").style.display = "none";
document.getElementById("EntityHomePageError").style.display = "none";
document.getElementById("EntityEmailError").style.display = "none";
document.getElementById("OverviewError").style.display = "none";
document.getElementById("CityError").style.display = "none";
document.getElementById("StateError").style.display = "none";
document.getElementById("ZipError").style.display = "none";
document.getElementById("TrainerName1Error").style.display = "none";
document.getElementById("Trainer1BioError").style.display = "none";
document.getElementById("TrainerName2Error").style.display = "none";
document.getElementById("Trainer2BioError").style.display = "none";
document.getElementById("TrainerName3Error").style.display = "none";
document.getElementById("Trainer3BioError").style.display = "none";
document.getElementById("TrainerName4Error").style.display = "none";
document.getElementById("Trainer4BioError").style.display = "none";
document.getElementById("TrainerName5Error").style.display = "none";
document.getElementById("Trainer5BioError").style.display = "none";
document.getElementById("TrainerName6Error").style.display = "none";
document.getElementById("Trainer6BioError").style.display = "none";
document.getElementById("BackLinkPageError").style.display = "none";
document.getElementById("AnyValidationError").style.display = "none";
return true;
}
</script>
</head>

<body>
<div style="text-align: center; min-height: 100%; margin-bottom: -20px; margin-left: auto; margin-right: auto; ">

<div style="margin-top: 10px; text-align: center; padding: 0px;">
<form method="post" action="/index.php">
<input type="submit" class="submitLinkSmall" value="Home">
</form>
</div>

<h1 style="margin-top: 15px; font-size: 22px; margin-left: auto; margin-right: auto; text-align: center;">Add Trainer to National Mediation Training Registry</h1>

<?php
/* If $_SESSION['Username'] and $_SESSION['Password'] are not empty, see whether they constitute a valid username-password pair. If they don't, set their values to NULL. */
if (!empty($_SESSION['Username']) && !empty($_SESSION['Password']))
	{
	$query = "SELECT COUNT(*) AS TheCount FROM trainers_table WHERE Username = '".$_SESSION['Username']."' AND Password = '".$_SESSION['Password']."'";
	$result = mysql_query($query) or die('The SELECT COUNT from trainser_table for Username and Password failed i.e. '.$query.' failed: ' . mysql_error());
	$row = mysql_fetch_assoc($result);
	if ($row['TheCount'] != 1) // The session variables don't hold a legitimate username-password pair
		{
		unset($_SESSION['Username']);
		unset($_SESSION['Password']);
		$_SESSION['LoggedIn'] = 'false';
		}
	else $_SESSION['LoggedIn'] = 'true'; // Set this flag.
	}

/* Examine the manually entered Username and Password if the LogIn button in addtrainer.php was clicked. */
$LogIn = $_POST['LogIn'];
if (isset($LogIn))
	{
	unset($LogIn);
	$Username = $_POST['Username']; // Use short names for user's manually entered username and password.
	$Password = $_POST['Password'];
	
	// Examine userpass_table to be sure such a username-password pair exists
	$db = mysql_connect('localhost', 'paulme6_merlyn', 'fePhaCj64mkik')
	or die('Could not connect: ' . mysql_error());
	mysql_select_db('paulme6_medtrainers') or die('Could not select database: ' . mysql_error());

	$query = "SELECT COUNT(*) AS TheCount, EntityName FROM trainers_table WHERE Username = '".$Username."' AND Password = '".$Password."'";
	$result = mysql_query($query) or die('Query (count of matching username-password pairs in trainers_table) failed: ' . mysql_error().' and the query string was: '.$query);
	$row = mysql_fetch_assoc($result);
	$count = $row['TheCount'];

	if ($count == 0) // username-password pair entered is invalid. Display message
		{
		echo "<br><p class='basictext' style='margin-left: 50px; margin-right: 50px; margin-top: 40px; font-size: 14px;'>Incorrect username or password. Please use your browser&rsquo;s Back button or <a href='addtrainer.php' style='font-size: 14px;'>click here</a> to try again.</p>";
		$Username = null;
		$Password = null;
		$_SESSION['LoggedIn'] = 'false';
		exit;
		}
	else if ($count > 1) // This condition should never arise.
		{
		$message = "A spurious condition has arisen in file addtrainer.php on the mediationtrainings.org server. This note is being sent to you as the webmaster.";
		// In case any of our lines are larger than 70 characters, we should use wordwrap()
		$message = wordwrap($message, 70);
		mail('paulmerlyn@yahoo.com', 'Spurious Condition Alert in addtrainer.php', $message);
		echo "<br><p class='basictext' style='margin-left: 50px; margin-right: 50px; margin-top: 40px; font-size: 14px;'>A spurious condition has been detected by the server. Our webmaster has been notified. We apologize for the inconvenience. Please use your browser&rsquo;s Back button or <a href='addtrainer.php' style='font-size: 14px;'>click here</a> to try again.</p>";
		$_SESSION['LoggedIn'] = 'false';
		exit;
		}
	else // $count == 1 so there is one match and the username-password pair is legitimate. But before allowing the user to create a Trainer Profile, make sure one doesn't already exist. This safeguards against an existing subscriber who inadvertently logs into the addtrainer.php (rather than edittrainer.php) page and then overwrites it. Determine whether the Trainer Profile already exists by examining the EntityName column in trainers_table (a required field when creating a Trainer Profile).
		{
		if (empty($row['EntityName']))
			{
			$_SESSION['LoggedIn'] = 'true';
			$_SESSION['Username'] = $Username; // Store $Username and $Password as session variables for use by addtrainer_slave.php
			$_SESSION['Password'] = $Password;
			}
		else
			{
			echo "<br><p class='basictext' style='margin-left: 100px; margin-right: 100px; margin-top: 40px; font-size: 14px; text-align: left;'>Your Trainer Profile already exists. To edit or delete it, <a href='edittrainer.php' style='font-size: 14px;'>click here</a> or select &ldquo;Edit/Delete Trainer Profile&rdquo; under <b>Registry</b> on the main menu. You may also want to add a training event. Click <a href='addevent.php' style='font-size: 14px;'>here</a> to do that, or select &ldquo;Add Training Event&rdquo; under <b>Registry</b> on the main menu.</p>";
			$Username = null;
			$Password = null;
			$_SESSION['LoggedIn'] = 'false';
			exit;
			}
		}
	}

/* If there aren't session variable values for $_SESSION['Username'] and $_SESSION['Password'] or if $_SESSION['LoggedIn'] isn't true, the user needs to be shown the Log In screen. */
if  ($_SESSION['LoggedIn'] != 'true' || empty($_SESSION['Username']) || empty($_SESSION['Password']))
	{
	// Visitor needs to log in manually.
	?>
	<div style="text-align: center; width: 280px; margin-left: auto; margin-right: auto; margin-top: 30px; padding: 15px; border: 2px solid #444444; border-color: #9C151C;">
	<h4 class="forms">Please log in to create your Trainer Profile</h4>
	<div style="padding: 10px; width: 250px;">
	<span class="basictext">(Forgotten your username and password? You&rsquo;ll find it in the email confirmation sent when you joined the Registry. You can also <a href="/userpassreminder.php">request a reminder</a>.)</span></div><br>
<br>
	<form method="post" action="/addtrainer.php">
	<table border="0" width="280" style="margin-left:0px;">
	<tr>
	<td width="60"><label for="Username">Username:&nbsp;</label></td>
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
	<div style="margin-top: 30px; margin-bottom: 20px; font-family: Geneva, Arial, Helvetica, sans-serif; font-size: 15px;">No username/password? <a href="/join.php">Join</a> the National Mediation Training Registry.</div>
	<?php
	}
	
/* If user is successfully logged in, display the main Add Trainer form */
if ($_SESSION['LoggedIn'] == 'true')
	{
?>
	<div style="margin-left: auto; margin-right: auto; margin-top: 30px; width: 750px; padding: 15px; border: 2px solid #444444; border-color: #9C151C;">

	<form method="post" action="/scripts/addtrainer_slave.php">
			
	<table width="750">
	<tr>
	<td align="left" width="150" valign="top"><label for="EntityName" style="position:relative; top: 8px;">Organization Name</label></td>
	<td colspan="3" align="left"><a name="EntityNameAnchor" class="plain"><input type="text" class="textfieldsmall" name="EntityName" id="EntityName" value="<?=$_SESSION['EntityName'];?>" maxlength="60" size="50" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkEntityNameOnly();"></a><span class="redsup">&nbsp;&nbsp;<b>*</b> (required)</span><div class="greytextsmall">Example: Law and Mediation Office of Jane Doe & Associates</div><div class="error" id="EntityNameError">Please use only alphanumerics (A-Z, a-z, 0-9), dash (-), slash (/), period (.), apostrophe ('), &, and space characters.<br></div><?php if ($_SESSION['MsgEntityName'] != null) { echo $_SESSION['MsgEntityName']; $_SESSION['MsgEntityName']=null; } ?></td>
	</tr>
	<tr height="40">
	<td align="left" valign="top"><label for="StreetAddress" style="position:relative; top: 10px;">Street Address</label></td>
	<td colspan="3" align="left"><a name="StreetAddressAnchor" class="plain"><input type="text" class="textfieldsmall" name="StreetAddress" id="StreetAddress" value="<?=$_SESSION['StreetAddress'];?>" maxlength="100" size="50" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkStreetAddressOnly();"></a><div class="error" id="StreetAddressError"><br>Please use only alphanumerics (A-Z, a-z, 0-9), dash (-), pound (#), period (.), apostrophe ('), comma (,) and space.<br>Examples: <i>92 N. Lincoln Way</i> or </i><i>8708 Oak St., Suite #3</i><br></div><?php if ($_SESSION['MsgStreetAddress'] != null) { echo $_SESSION['MsgStreetAddress']; $_SESSION['MsgStreetAddress']=null; } ?></td>
	</tr>
	<tr height="20">
	<td align="left" valign="top"><label for="City" style="position:relative; top: 10px;">City</label></td>
	<td align="left" width="175"><a name="CityAnchor" class="plain"><input type="text" class="textfieldsmall" name="City" id="City" value="<?=$_SESSION['City'];?>" maxlength="30" size="20" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkCityOnly();"></a><span class="redsup">&nbsp;&nbsp;<b>*</b></span></td>
	<td width="250">
	<label for="State">State</label>&nbsp;&nbsp;
	<select class="smallredoutline" name="State" id="State" size="1" onchange="hideAllErrors(); return checkStateOnly();">
<?php
	$statesArray = array(array('&lt;&nbsp; Select a State &nbsp;&gt;',null), array('Alabama','AL'),	array('Alaska','AK'), array('Arizona','AZ'), array('Arkansas','AR'),	array('California','CA'), array('Colorado','CO'), array('Connecticut','CT'), array('Delaware','DE'), array('District of Columbia','DC'), array('Florida','FL'), array('Georgia','GA'), array('Hawaii','HI'), array('Idaho','ID'), array('Illinois','IL'), array('Indiana','IN'), array('Iowa','IA'), array('Kansas','KS'), array('Kentucky','KY'), array('Louisiana','LA'), array('Maine','ME'), array('Maryland','MD'), array('Massachusetts','MA'), array('Michigan','MI'), array('Minnesota','MN'), array('Mississippi','MS'), array('Missouri','MO'), array('Montana','MT'), array('Nebraska','NE'), array('Nevada','NV'), array('New Hampshire','NH'), array('New Jersey','NJ'), array('New Mexico','NM'), array('New York','NY'), array('North Carolina','NC'), array('North Dakota','ND'), array('Ohio','OH'), array('Oklahoma','OK'), array('Oregon','OR'), array('Pennsylvania','PA'), array('Rhode Island','RI'), array('South Carolina','SC'), array('South Dakota','SD'), array('Tennessee','TN'), array('Texas','TX'), array('Utah','UT'), array('Vermont','VT'), array('Virginia','VA'), array('Washington','WA'), array('Washington, D.C.','DC'), array('West Virginia','WV'), array('Wisconsin','WI'), array('Wyoming','WY'));
	for ($i=0; $i<53; $i++)
		{
		$optiontag = '<option value="'.$statesArray[$i][1].'" ';
		if ($_SESSION['State'] == $statesArray[$i][1]) $optiontag = $optiontag.'selected';
		$optiontag = $optiontag.'>'.$statesArray[$i][0]."</option>\n";
		echo $optiontag;
		}
?>
	</select><span class="redsup">&nbsp;&nbsp;<b>*</b></span>
	</td>
	<td width="175" align="left">
	<label>Zip</label>&nbsp;&nbsp;<a name="ZipAnchor" class="plain"><input type="text" class="textfieldsmall" name="Zip" id="Zip" value="<?=$_SESSION['Zip'];?>" maxlength="5" size="5" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkZipOnly();"></a><span class="redsup">&nbsp;&nbsp;<b>*</b></span>
	</td>
	</tr>
	<tr><!-- This row is specially created to hold city, state, and zip errors. --><td>&nbsp;</td>
	<td colspan="3" valign="top" align="left">
	<div class="error" id="CityError">Please enter a valid city. Use only letters (A-Z, a-z), dash (-), apostrophe ('), and space characters here.<br>Use initial capital (upper-case) letters. Examples: <i>Springfield</i> or <i>South Bend</i>.<br></div><?php if ($_SESSION['MsgCity'] != null) { echo $_SESSION['MsgCity']; $_SESSION['MsgCity']=null; } ?>
	<div class="error" id="StateError">Please select a state from the drop-down menu.<br></div>
<?php if ($_SESSION['MsgState'] != null) { echo $_SESSION['MsgState']; $_SESSION['MsgState']=null; } ?>
<div class="error" id="ZipError">Please enter a valid zip code. Use a five-digit format.<br></div><?php if ($_SESSION['MsgZip'] != null) { echo $_SESSION['MsgZip']; $_SESSION['MsgZip']=null; } ?>
	</td>
	</tr>
	<tr height="40">
	<td align="left"><label for="Tel1">Telephone</label></td>
	<td colspan="3" align="left"><a name="TelephoneAnchor" class="plain"><input type="text" class="textfieldsmall" name="Tel1" id="Tel1" value="<?=$_SESSION['Tel1'];?>" maxlength="3" size="3" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white';"></a>&nbsp;&nbsp;&nbsp;<input type="text" class="textfieldsmall" name="Tel2" id="Tel2" value="<?=$_SESSION['Tel2'];?>" maxlength="3" size="3" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white';">&nbsp;&ndash;&nbsp;
	<input type="text" class="textfieldsmall" name="Tel3" id="Tel3" value="<?=$_SESSION['Tel3'];?>" maxlength="4" size="4" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkTelephoneOnly();">
	<span class="redsup">&nbsp;&nbsp;<b>*</b></span>
	<div class="error" id="TelephoneError"><br>Please enter a valid telephone number. Use only numbers (0-9).<br></div><?php if ($_SESSION['MsgTelephone'] != null) { echo $_SESSION['MsgTelephone']; $_SESSION['MsgTelephone']=null; } ?></td>
	</tr>
	<tr height="60">
	<td align="left" valign="top"><label for="TrainingsOffered" style="position:relative; top: 10px;">Training(s) Offered<span class="redsup">&nbsp;&nbsp;<b>*</b></span></label></td>
	<td align="left">
	<a name="TrainingsOfferedAnchor" class="plain"><input type="checkbox" name="BasicMed" id="BasicMed" value="true" <?php if ($_SESSION['BasicMed'] == true) echo 'checked'; ?>></a><label>&nbsp;&nbsp;Basic Mediation</label><br>
	<input type="checkbox" name="WorkplaceMed" id="WorkplaceMed" value="true" <?php if ($_SESSION['WorkplaceMed'] == true) echo 'checked'; ?>><label>&nbsp;&nbsp;Workplace Mediation</label>
	</td>
	<td align="left">
	<div style="position: relative; left: 42px; width: 180px;">
	<input type="checkbox" name="DivorceMed" id="DivorceMed" value="true" <?php if ($_SESSION['DivorceMed'] == true) echo 'checked'; ?>><label>&nbsp;&nbsp;Divorce Mediation</label><br>
	<input type="checkbox" name="MaritalMed" id="MaritalMed" value="true" <?php if ($_SESSION['MaritalMed'] == true) echo 'checked'; ?>><label>&nbsp;&nbsp;Marital Mediation</label>
	</div>
	</td>
	<td align="left">
	<input type="checkbox" name="FamilyMed" id="FamilyMed" value="true" <?php if ($_SESSION['FamilyMed'] == true) echo 'checked'; ?>><label>&nbsp;&nbsp;Family Mediation</label><br>
	<input type="checkbox" name="OtherMed" id="OtherMed" value="true" <?php if ($_SESSION['OtherMed'] == true) echo 'checked'; ?>><label>&nbsp;&nbsp;Other</label>
	</td>
	</tr>
	<tr>
	<td>&nbsp;</td>
	<td align="left" colspan="3"><div class="greytextsmall">Please check at least one of the boxes to indicate the trainings you offer.</div><div class="error" id="TrainingsOfferedError">Please select at least one check-box.<br></div><?php if ($_SESSION['MsgTrainingsOffered'] != null) { echo $_SESSION['MsgTrainingsOffered']; $_SESSION['MsgTrainingsOffered']=null; } ?></td>
	</tr>
	<tr height="40">
<td align="left" valign="top"><label for="EntityHomePage" style="position:relative; top: 8px;">Organization Web Site</label></td>
	<td colspan="3" align="left"><a name="EntityHomePageAnchor" class="plain"><input type="text" class="textfieldsmall" name="EntityHomePage" id="EntityHomePage" value="<?=$_SESSION['EntityHomePage'];?>" maxlength="150" size="50" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkEntityHomePageOnly();"></a>
	<div class="greytextsmall">Example: www.janedoemediation.com or http://www.janedoemediation</div>
	<div class="error" id="EntityHomePageError">Please check format of your web address<br></div><?php if ($_SESSION['MsgEntityHomePage'] != null) { echo $_SESSION['MsgEntityHomePage']; $_SESSION['MsgEntityHomePage']=null; } ?>
	</td>
	</tr>
	<tr height="50">
	<td align="left" valign="top"><label for="EntityEmail" style="position:relative; top: 8px;">Organization Email</label></td>
	<td colspan="3" align="left"><a name="EntityEmailAnchor" class="plain"><input type="text" class="textfieldsmall" name="EntityEmail" id="EntityEmail" value="<?=$_SESSION['EntityEmail'];?>" maxlength="60" size="50" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkEntityEmailOnly();"></a>
	<span class="redsup">&nbsp;&nbsp;<b>*</b></span>
	<div class="greytextsmall">Example:jane@janedoemediation.com</div>
	<div class="error" id="EntityEmailError">Please check your format. Use only alphanumerics (A-Z, a-z, 0-9), dash (-), period (.), @, and underscore (_) characters.<br></div><?php if ($_SESSION['MsgEntityEmail'] != null) { echo $_SESSION['MsgEntityEmail']; $_SESSION['MsgEntityEmail']=null; } ?>
	</td>
	</tr>
	<tr height="150">
	<td valign="top" align="left"><label for="Overview" style="position:relative; top: 14px;">Organization Overview</label></td>
	<td colspan="3" valign="top" align="left"><a name="OverviewAnchor" class="plain"><textarea name="Overview" id="Overview" rows="5" cols="80" wrap="soft" style="overflow:auto; height: 90px; width: 540px;" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkOverviewOnly();" onKeyUp="charCount('Overview','sBann1','{CHAR} chars'); toCount('Overview','sBann2','{CHAR} chars',450);"><?=$_SESSION['Overview'];?></textarea></a><div class="greytextsmall">Please describe your organization as a provider of mediation training. Use a single paragraph (no line breaks).<br>Maximum = 450 chars. Character Count: [<span id="sBann1" class="greytextsmall" style="font-weight:bold;">0 chars</span>]&nbsp;&nbsp;Remaining: [<span id="sBann2" class="greytextsmall" style="font-weight:bold;">450 chars</span>]</div><div class="error" id="OverviewError">Please remove any newline characters ('return' or 'enter'). To remove them, use the [delete] or [backspace] keys.<br>Also, please ensure your text is less than 450 characters (approx. 70 words) in length.<br></div><?php if ($_SESSION['MsgOverview'] != null) { echo $_SESSION['MsgOverview']; $_SESSION['MsgOverview']=null; };?></td>
	</tr>
	<tr height="40">
	<td align="left" valign="top">
	<label for="TrainerName1" style="position:relative; top: 10px;">Trainer Name #1</label></td>
	<td colspan="3" align="left"><a name="TrainerName1Anchor" class="plain"><input type="text" class="textfieldsmall" name="TrainerName1" id="TrainerName1" value="<?=$_SESSION['TrainerName1'];?>" maxlength="30" size="30" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkTrainerNameOnly('TrainerName1', 'TrainerName1Error', 'TrainerName1Anchor');"></a>
			&nbsp;&nbsp;&nbsp;<input type="button" name="Logout" class="submitLinkSmall" value="+ Add/Remove Bio" onclick="showHideRow('Trainer1BioRow', 'Trainer1Bio');">
			&nbsp;&nbsp;&nbsp;<input type="button" name="Logout" class="submitLinkSmall" value="+ Add/Remove Trainer" onclick="showHideRow('Trainer2NameRow', 'TrainerName2');">
<div class="error" id="TrainerName1Error"><br>Please use only letters (A-Z, a-z), dash (-), period (.), apostrophe ('), and space characters.<br></div><?php if ($_SESSION['MsgTrainerName1'] != null) { echo $_SESSION['MsgTrainerName1']; $_SESSION['MsgTrainerName1']=null; } ?>		
	</td>
	</tr>
	<tr id="Trainer1BioRow" style="display: none;">
	<td valign="top" align="left"><label for="Trainer1Bio" style="position: relative; top: 10px;">Trainer #1 Bio</label></td>
	<td colspan="3" valign="top" align="left"><a name="Trainer1BioAnchor" class="plain"><textarea name="Trainer1Bio" id="Trainer1Bio" rows="8" cols="80" wrap="soft" style="overflow:auto; height: 180px; width: 540px;" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkTrainerBioOnly('Trainer1Bio', 'Trainer1BioError', 'Trainer1BioAnchor');" onKeyUp="charCount('Trainer1Bio','sBann1TrainBio1','{CHAR} chars'); toCount('Trainer1Bio','sBann2TrainBio1','{CHAR} chars',900);"><?=$_SESSION['Trainer1Bio'];?></textarea></a>
	<div class="greytextsmall">Single paragraph. Maximum: 900 characters. Count: [<span id="sBann1TrainBio1" class="greytextsmall" style="font-weight:bold;">0 chars</span>]&nbsp;&nbsp;Remaining: [<span id="sBann2TrainBio1" class="greytextsmall" style="font-weight:bold;">900 chars</span>]</div>
	<div class="error" id="Trainer1BioError">Please remove any newline characters ('return' or 'enter'). To remove them, use the [delete] or [backspace] keys.<br>Also, please ensure your text is less than 900 characters (approx. 130 words) in length.<br></div><?php if ($_SESSION['MsgTrainer1Bio'] != null) { echo $_SESSION['MsgTrainer1Bio']; $_SESSION['MsgTrainer1Bio']=null; };?></td>
	</tr>
	<tr height="60" id="Trainer2NameRow" style="display: none;">
	<td align="left" valign="top">
	<label for="TrainerName2" style="position:relative; top: 18px;">Trainer Name #2</label></td>
	<td colspan="3" align="left"><a name="TrainerName2Anchor" class="plain"><input type="text" class="textfieldsmall" name="TrainerName2" id="TrainerName2" value="<?=$_SESSION['TrainerName2'];?>" maxlength="30" size="30" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkTrainerNameOnly('TrainerName2', 'TrainerName2Error', 'TrainerName2Anchor');"></a>
			&nbsp;&nbsp;&nbsp;<input type="button" name="Logout" class="submitLinkSmall" value="+ Add/Remove Bio" onclick="showHideRow('Trainer2BioRow', 'Trainer2Bio');">
			&nbsp;&nbsp;&nbsp;<input type="button" name="Logout" class="submitLinkSmall" value="+ Add/Remove Trainer" onclick="showHideRow('Trainer3NameRow', 'TrainerName3');">
	<div class="error" id="TrainerName2Error"><br>Please use only letters (A-Z, a-z), dash (-), period (.), apostrophe ('), and space characters.<br></div><?php if ($_SESSION['MsgTrainerName2'] != null) { echo $_SESSION['MsgTrainerName2']; $_SESSION['MsgTrainerName2']=null; } ?>
	</td>
	</tr>
	<tr id="Trainer2BioRow" style="display: none;">
	<td valign="top" align="left"><label for="Trainer2Bio" style="position: relative; top: 10px;">Trainer #2 Bio</label></td>
	<td colspan="3" valign="top" align="left"><a name="Trainer2BioAnchor" class="plain"><textarea name="Trainer2Bio" id="Trainer2Bio" rows="8" cols="80" wrap="soft" style="overflow:auto; height: 180px; width: 540px;" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkTrainerBioOnly('Trainer2Bio', 'Trainer2BioError', 'Trainer2BioAnchor');" onKeyUp="charCount('Trainer2Bio','sBann1TrainBio2','{CHAR} chars'); toCount('Trainer2Bio','sBann2TrainBio2','{CHAR} chars',900);"><?=$_SESSION['Trainer2Bio'];?></textarea></a>
	<div class="greytextsmall">Single paragraph. Maximum: 900 characters. Count: [<span id="sBann1TrainBio2" class="greytextsmall" style="font-weight:bold;">0 chars</span>]&nbsp;&nbsp;Remaining: [<span id="sBann2TrainBio2" class="greytextsmall" style="font-weight:bold;">900 chars</span>]</div>
	<div class="error" id="Trainer2BioError">Please remove any newline characters ('return' or 'enter'). To remove them, use the [delete] or [backspace] keys.<br>Also, please ensure your text is less than 900 characters (approx. 130 words) in length.<br></div><?php if ($_SESSION['MsgTrainer2Bio'] != null) { echo $_SESSION['MsgTrainer2Bio']; $_SESSION['MsgTrainer2Bio']=null; };?></td>
	</tr>
	<tr height="60" id="Trainer3NameRow" style="display: none;">
	<td align="left" valign="top">
	<label for="TrainerName3" style="position:relative; top: 18px;">Trainer Name #3</label></td>
<td colspan="3" align="left"><a name="TrainerName3Anchor" class="plain"><input type="text" class="textfieldsmall" name="TrainerName3" id="TrainerName3" value="<?=$_SESSION['TrainerName3'];?>" maxlength="30" size="30" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkTrainerNameOnly('TrainerName3', 'TrainerName3Error', 'TrainerName3Anchor');"></a>
			&nbsp;&nbsp;&nbsp;<input type="button" name="Logout" class="submitLinkSmall" value="+ Add/Remove Bio" onclick="showHideRow('Trainer3BioRow', 'Trainer3Bio');">
			&nbsp;&nbsp;&nbsp;<input type="button" name="Logout" class="submitLinkSmall" value="+ Add/Remove Trainer" onclick="showHideRow('Trainer4NameRow', 'TrainerName4');">
	<div class="error" id="TrainerName3Error"><br>Please use only letters (A-Z, a-z), dash (-), period (.), apostrophe ('), and space characters.<br></div><?php if ($_SESSION['MsgTrainerName3'] != null) { echo $_SESSION['MsgTrainerName3']; $_SESSION['MsgTrainerName3']=null; } ?>
	</td>
	</tr>
	<tr id="Trainer3BioRow" style="display: none;">
	<td valign="top" align="left"><label for="Trainer3Bio" style="position: relative; top: 10px;">Trainer #3 Bio</label></td>
	<td colspan="3" valign="top" align="left"><a name="Trainer3BioAnchor" class="plain"><textarea name="Trainer3Bio" id="Trainer3Bio" rows="8" cols="80" wrap="soft" style="overflow:auto; height: 180px; width: 540px;" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkTrainerBioOnly('Trainer3Bio', 'Trainer3BioError', 'Trainer3BioAnchor');" onKeyUp="charCount('Trainer3Bio','sBann1TrainBio3','{CHAR} chars'); toCount('Trainer3Bio','sBann2TrainBio3','{CHAR} chars',900);"><?=$_SESSION['Trainer3Bio'];?></textarea></a>
	<div class="greytextsmall">Single paragraph Maximum: 900 characters. Count: [<span id="sBann1TrainBio3" class="greytextsmall" style="font-weight:bold;">0 chars</span>]&nbsp;&nbsp;Remaining: [<span id="sBann2TrainBio3" class="greytextsmall" style="font-weight:bold;">900 chars</span>]</div>
	<div class="error" id="Trainer3BioError">Please remove any newline characters ('return' or 'enter'). To remove them, use the [delete] or [backspace] keys.<br>Also, please ensure your text is less than 900 characters (approx. 130 words) in length.<br></div><?php if ($_SESSION['MsgTrainer3Bio'] != null) { echo $_SESSION['MsgTrainer3Bio']; $_SESSION['MsgTrainer3Bio']=null; };?></td>
	</tr>
	<tr height="60" id="Trainer4NameRow" style="display: none;">
	<td align="left" valign="top">
	<label for="TrainerName4" style="position:relative; top: 18px;">Trainer Name #4</label></td>
	<td colspan="3" align="left"><a name="TrainerName4Anchor" class="plain"><input type="text" class="textfieldsmall" name="TrainerName4" id="TrainerName4" value="<?=$_SESSION['TrainerName4'];?>" maxlength="30" size="30" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkTrainerNameOnly('TrainerName4', 'TrainerName4Error', 'TrainerName4Anchor');"></a>
			&nbsp;&nbsp;&nbsp;<input type="button" name="Logout" class="submitLinkSmall" value="+ Add/Remove Bio" onclick="showHideRow('Trainer4BioRow', 'Trainer4Bio');">
			&nbsp;&nbsp;&nbsp;<input type="button" name="Logout" class="submitLinkSmall" value="+ Add/Remove Trainer" onclick="showHideRow('Trainer5NameRow', 'TrainerName5');">
	<div class="error" id="TrainerName4Error"><br>Please use only letters (A-Z, a-z), dash (-), period (.), apostrophe ('), and space characters.<br></div><?php if ($_SESSION['MsgTrainerName4'] != null) { echo $_SESSION['MsgTrainerName4']; $_SESSION['MsgTrainerName4']=null; } ?>
	</td>
	</tr>
	<tr id="Trainer4BioRow" style="display: none;">
	<td valign="top" align="left"><label for="Trainer4Bio" style="position: relative; top: 10px;">Trainer #4 Bio</label></td>
	<td colspan="3" valign="top" align="left"><a name="Trainer4BioAnchor" class="plain"><textarea name="Trainer4Bio" id="Trainer4Bio" rows="8" cols="80" wrap="soft" style="overflow:auto; height: 180px; width: 540px;" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkTrainerBioOnly('Trainer4Bio', 'Trainer4BioError', 'Trainer4BioAnchor');" onKeyUp="charCount('Trainer4Bio','sBann1TrainBio4','{CHAR} chars'); toCount('Trainer4Bio','sBann2TrainBio4','{CHAR} chars',900);"><?=$_SESSION['Trainer4Bio'];?></textarea></a>
	<div class="greytextsmall">Single paragraph. Maximum: 900 characters. Count: [<span id="sBann1TrainBio4" class="greytextsmall" style="font-weight:bold;">0 chars</span>]&nbsp;&nbsp;Remaining: [<span id="sBann2TrainBio4" class="greytextsmall" style="font-weight:bold;">900 chars</span>]</div>
<div class="error" id="Trainer4BioError">Please remove any newline characters ('return' or 'enter'). To remove them, use the [delete] or [backspace] keys.<br>Also, please ensure your text is less than 900 characters (approx. 130 words) in length.<br></div><?php if ($_SESSION['MsgTrainer4Bio'] != null) { echo $_SESSION['MsgTrainer4Bio']; $_SESSION['MsgTrainer4Bio']=null; };?></td>
	</tr>
	<tr height="60" id="Trainer5NameRow" style="display: none;">
	<td align="left" valign="top">
	<label for="TrainerName5" style="position:relative; top: 18px;">Trainer Name #5</label></td>
	<td colspan="3" align="left"><a name="TrainerName5Anchor" class="plain"><input type="text" class="textfieldsmall" name="TrainerName5" id="TrainerName5" value="<?=$_SESSION['TrainerName5'];?>" maxlength="30" size="30" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkTrainerNameOnly('TrainerName5', 'TrainerName5Error', 'TrainerName5Anchor');"></a>
			&nbsp;&nbsp;&nbsp;<input type="button" name="Logout" class="submitLinkSmall" value="+ Add/Remove Bio" onclick="showHideRow('Trainer5BioRow', 'Trainer5Bio');">
			&nbsp;&nbsp;&nbsp;<input type="button" name="Logout" class="submitLinkSmall" value="+ Add/Remove Trainer" onclick="showHideRow('Trainer6NameRow', 'TrainerName6');">
<div class="error" id="TrainerName5Error"><br>Please use only letters (A-Z, a-z), dash (-), period (.), apostrophe ('), and space characters.<br></div><?php if ($_SESSION['MsgTrainerName5'] != null) { echo $_SESSION['MsgTrainerName5']; $_SESSION['MsgTrainerName5']=null; } ?>
	</td>
	</tr>
	<tr id="Trainer5BioRow" style="display: none;">
	<td valign="top" align="left"><label for="Trainer5Bio" style="position: relative; top: 10px;">Trainer #5 Bio</label></td>
	<td colspan="3" valign="top" align="left"><a name="Trainer5BioAnchor" class="plain"><textarea name="Trainer5Bio" id="Trainer5Bio" rows="8" cols="80" wrap="soft" style="overflow:auto; height: 180px; width: 540px;" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkTrainerBioOnly('Trainer5Bio', 'Trainer5BioError', 'Trainer5BioAnchor');" onKeyUp="charCount('Trainer5Bio','sBann1TrainBio5','{CHAR} chars'); toCount('Trainer5Bio','sBann2TrainBio5','{CHAR} chars',900);"><?=$_SESSION['Trainer5Bio'];?></textarea></a>
	<div class="greytextsmall">Single paragraph. Maximum: 900 characters. Count: [<span id="sBann1TrainBio5" class="greytextsmall" style="font-weight:bold;">0 chars</span>]&nbsp;&nbsp;Remaining: [<span id="sBann2TrainBio5" class="greytextsmall" style="font-weight:bold;">900 chars</span>]</div>
<div class="error" id="Trainer5BioError">Please remove any newline characters ('return' or 'enter'). To remove them, use the [delete] or [backspace] keys.<br>Also, please ensure your text is less than 900 characters (approx. 130 words) in length.<br></div><?php if ($_SESSION['MsgTrainer5Bio'] != null) { echo $_SESSION['MsgTrainer5Bio']; $_SESSION['MsgTrainer5Bio']=null; };?></td>
	</tr>
	<tr height="60" id="Trainer6NameRow" style="display: none;">
	<td align="left" valign="top">
<label for="TrainerName6" style="position:relative; top: 18px;">Trainer Name #6</label></td>
	<td colspan="3" align="left"><a name="TrainerName6Anchor" class="plain"><input type="text" class="textfieldsmall" name="TrainerName6" id="TrainerName6" value="<?=$_SESSION['TrainerName6'];?>" maxlength="30" size="30" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkTrainerNameOnly('TrainerName6', 'TrainerName6Error', 'TrainerName6Anchor');"></a>
			&nbsp;&nbsp;&nbsp;<input type="button" name="Logout" class="submitLinkSmall" value="+ Add/Remove Bio" onclick="showHideRow('Trainer6BioRow', 'Trainer6Bio');">
<div class="error" id="TrainerName6Error"><br>Please use only letters (A-Z, a-z), dash (-), period (.), apostrophe ('), and space characters.<br></div><?php if ($_SESSION['MsgTrainerName6'] != null) { echo $_SESSION['MsgTrainerName6']; $_SESSION['MsgTrainerName6']=null; } ?>
	</td>
	</tr>
	<tr id="Trainer6BioRow" style="display: none;">
	<td valign="top" align="left"><label for="Trainer6Bio" style="position: relative; top: 10px;">Trainer #6 Bio</label></td>
	<td colspan="3" valign="top" align="left"><a name="Trainer6BioAnchor" class="plain"><textarea name="Trainer6Bio" id="Trainer6Bio" rows="8" cols="80" wrap="soft" style="overflow:auto; height: 180px; width: 540px;" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkTrainerBioOnly('Trainer6Bio', 'Trainer6BioError', 'Trainer6BioAnchor');" onKeyUp="charCount('Trainer6Bio','sBann1TrainBio6','{CHAR} chars'); toCount('Trainer6Bio','sBann2TrainBio6','{CHAR} chars',900);"><?=$_SESSION['Trainer6Bio'];?></textarea></a>
<div class="greytextsmall">Single paragraph. Maximum: 900 characters. Count: [<span id="sBann1TrainBio6" class="greytextsmall" style="font-weight:bold;">0 chars</span>]&nbsp;&nbsp;Remaining: [<span id="sBann2TrainBio6" class="greytextsmall" style="font-weight:bold;">900 chars</span>]</div>
	<div class="error" id="Trainer6BioError">Please remove any newline characters ('return' or 'enter'). To remove them, use the [delete] or [backspace] keys.<br>Also, please ensure your text is less than 900 characters (approx. 130 words) in length.<br></div><?php if ($_SESSION['MsgTrainer6Bio'] != null) { echo $_SESSION['MsgTrainer6Bio']; $_SESSION['MsgTrainer6Bio']=null; };?></td>
	</tr>
	<tr>
	<td align="left" valign="top"><br /><label for="FeaturedTrainer" style="position:relative; top: 0px;">Featured Trainer?<span class="redsup">&nbsp;&nbsp;<b>*</b></span></label></td>
	<td colspan="3" align="left">
	<br /><input type="radio" name="MemberLevel" id="MemberLevelAssociate" value="Associate" onClick="if (this.checked) document.getElementById('BackLinkPage').value = ''; document.getElementById('BackLinkPage').disabled = true;" <?php if ($_SESSION['MemberLevel'] == 'Associate') echo 'checked'; ?>><label for="MemberLevel">No</label><br />
	<input type="radio" name="MemberLevel" id="MemberLevelFriend" value="Friend" onClick="if (this.checked) { document.getElementById('BackLinkPage').disabled = false; document.getElementById('BackLinkPage').value = 'Example: www.mysite.com/resources.html';};" <?php if ($_SESSION['MemberLevel'] == 'Friend') echo 'checked'; ?>><label for="MemberLevel">Yes</label>&nbsp;&nbsp;<span class="basictext">I&rsquo;ve added a link to the National Mediation Training Registry on the following page of my own site:<br /></span>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a name="BackLinkPageAnchor" class="plain"></a><input type="text" class="textfieldsmall" name="BackLinkPage" id="BackLinkPage" value="<?php if ($_SESSION['BackLinkPage'] != '') echo $_SESSION['BackLinkPage']; else echo 'Example: www.mysite.com/resources.html'; ?>" maxlength="150" size="40" onFocus="this.style.background='#FFFF99';" onBlur="this.style.background='white'; hideAllErrors(); return checkBackLinkPageOnly();">
	<div class="error" id="BackLinkPageError">Please check the format of this web address.<br></div><?php if ($_SESSION['MsgBackLinkPage'] != null) { echo $_SESSION['MsgBackLinkPage']; $_SESSION['MsgBackLinkPage']=null; } ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="CheckBackLink" class="buttonstyle" value="Check Back Link"><br /><br />
	<div class="basictext"><strong>Featured Trainer benefits:</strong> It&rsquo;s <em>free,</em> and your trainer profile will appear in <strong>bold</strong>, ahead of other trainers when users search the Registry. You&rsquo;ll receive a bio link and active links back to your site from our home page, trainer pages, and training events pages, driving traffic to your site and boosting your search-engine ranking. Plus, whenever you post a new training event, an email will be sent to local trainees who&rsquo;ve requested email notifications of upcoming training events.</div>
	<br />
	<div class="basictext"><strong>How to add a link to the National Mediation Training Registry:</strong> Simply copy and paste the following code snippet into any page of your site:</div>
	<textarea style="width: 540px; height: 30px; margin-top: 6px; margin-left: 0px; border: 1px solid #333333; font-family: 'Courier New'; text-align: left;" onClick="this.focus(); this.select();">Member of the &lt;a href=&quot;http://www.mediationtrainings.org&quot;&gt;National Mediation Training Registry&lt;/a&gt;</textarea>
	</td>
	</tr>
	<tr>
	<td>&nbsp;</td>
	<td colspan="3" align="center" height="60px" valign="bottom">
	<img style="position: relative; right: 50px;" src="/scripts/captcha/CaptchaSecurityImages.php?width=100&height=40&characters=5" /><br>
	</td>
	</tr>
	<tr height="40">
	<td>&nbsp;</td>
	<td colspan="3" align="center" height="70px" valign="middle">
	<div style="position: relative; right: 50px;"><label>Security Code:</label><br>
	<!-- For this form, I've used the following implementation of CAPTCHA: www.white-hat-web-design.co.uk/articles/php-captcha.php rather than Securimage CAPTCHA (which I used in the New Resolution Mediation's public site's contact forms (e.g. c_schedmediationform.shtml). -->
	<input id="security_code" name="security_code" type="text" class="textfieldsmall" size="5" />
	</div>
	</td>
	</tr>
	<tr valign="bottom" height="50">
	<td colspan="4" align="center"><input type="submit" name="AddTrainer" class="buttonstyle" style="margin-left: 0px;" value="Add Trainer" onClick="return checkForm();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="Abort" class="buttonstyle" value="Abort/Cancel"></td>
	</tr>
	<tr><td colspan="4" align="center"><span class="error" id="AnyValidationError">Oops! It looks like you made an error or missed one of the required fields. Please scroll up and fix any error(s) identified next to the relevant field(s).<br></span><?php if ($_SESSION['MsgAnyValidation'] != null) { echo $_SESSION['MsgAnyValidation']; $_SESSION['MsgAnyValidation']=null; };?></td></tr>
	</table>
			
	</form>
	
	</div>
<?php
	}
?>	
</div>
</body>
</html>
<?php
ob_flush();
exit;
?>