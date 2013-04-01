<?php
/*
addevent.php (and its slave script addevent_slave.php) is similar to adminaddeventA.php (and adminaddevent_slave.php) except the trainer would log into addevent.php using his/her username-password pair rather than an administrator identify a trainer via filters (in adminaddevent.php) then add the event for that trainer.
*/
// Start a session
session_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Add Event | National Mediation Training Registry</title>
<meta NAME="description" CONTENT="Form to add a training event to the National Mediation Training Registry">
<meta NAME="keywords" CONTENT="add training event, National Mediation Training Registry">
<link href="/mediationtraining.css" rel="stylesheet" type="text/css">
<link href="/scripts/tigra_calendar/calendar.css" rel="stylesheet" type="text/css">
<script type="text/javascript" language="JavaScript" src="/scripts/tigra_calendar/calendar_us.js"></script>
<script type="text/javascript" language="javascript" src="/scripts/emailaddresschecker.js"></script>
<script type="text/javascript" src="/scripts/characterCounter.js"></script>
<script>
// Function enableAll() enables all six of the TrainingType check-boxes. It's called whenever a check-box is unchecked by the user.
function enableAll()
{
document.getElementById('BasicMed').disabled = false;
document.getElementById('WorkplaceMed').disabled = false;
document.getElementById('DivorceMed').disabled = false;
document.getElementById('FamilyMed').disabled = false;
document.getElementById('MaritalMed').disabled = false;
document.getElementById('ElderMed').disabled = false;
}

// Function disableAll() disables all six of the TrainingType check-boxes. It's called whenever a check-box is unchecked by the user.
function disableAll()
{
document.getElementById('BasicMed').disabled = true;
document.getElementById('WorkplaceMed').disabled = true;
document.getElementById('DivorceMed').disabled = true;
document.getElementById('FamilyMed').disabled = true;
document.getElementById('MaritalMed').disabled = true;
document.getElementById('ElderMed').disabled = true;
}

/* Begin JS form validation functions. */
function checkTrainingTypeOnly()
{
// Validate that either at least one of the training type check-boxes is checked or that the OtherType text-field is non-blank.
if (!document.getElementById('BasicMed').checked && !document.getElementById('DivorceMed').checked && !document.getElementById('WorkplaceMed').checked && !document.getElementById('FamilyMed').checked && !document.getElementById('MaritalMed').checked && !document.getElementById('ElderMed').checked && document.getElementById('OtherType').value == '')
	{
	document.getElementById("TrainingTypeError").style.display = "inline";
	return false;
	}
else
	{
	return true;
	}
}

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

function checkNofHoursOnly()
{
// Validate NofHours input field.
var nofHoursValue = document.getElementById("NofHours").value;
var illegalCharSet = /[^0-9\.]+/; // Reject everything that contains one or more characters that is neither a period nor a digit.
var reqdCharSet = /[0-9]+/;  // At least one numeric.
if (illegalCharSet.test(nofHoursValue)  || !reqdCharSet.test(nofHoursValue))
	{
	document.getElementById("NofHoursError").style.display = "inline";
	return false;
	} 
else
	{
	return true;
	}
}

function checkNofDaysOnly()
{
// Validate NofDays field.
var nofDaysValue = document.getElementById("NofDays").value;
var illegalCharSet = /[^0-9\.]+/; // Reject everything that contains one or more characters that is neither a period nor a digit.
var reqdCharSet = /[0-9]+/;  // At least one numeric.
if (illegalCharSet.test(nofDaysValue) || !reqdCharSet.test(nofDaysValue))
	{
	document.getElementById("NofDaysError").style.display = "inline";
	return false;
	} 
else
	{
	return true;
	}
}

function checkEventCityOnly()
{
// Validate EventCity field.
var eventCityValue = document.getElementById("EventCity").value;
var eventCityLength = eventCityValue.length;
illegalCharSet = /[^A-Za-z'\-\.\(\) ]+/; // Exclude everything except A-Z, a-z, apostrophe ('), period, hyphen, space, or parentheses.
reqdCharSet = /^[A-Z][a-z'\.]+[(\-|\/| |)[A-Za-z\-\.\/\(\) ]+/;  // Names of form initial capital (e.g. San Jose-Gilroy) followed by potentially a period (e.g. N. Chicago) or lower case. May include dashes, slashes, or spaces. Also may have ( and ) for "(Test)" appendage. Also supports names like "D'Angelo".
if (document.getElementById('EventType').selectedIndex == 0) // EventCity is only a required field if EventType is set to 'Classroom'.
	{
	if (eventCityLength < 3 || illegalCharSet.test(eventCityValue) || !(reqdCharSet.test(eventCityValue)))
		{
		document.getElementById("EventCityError").style.display = "inline";
		anchordestn = "#EventCityAnchor";
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
	}
}

function checkEventStateOnly()
{
// Validate EventState selection.
if (document.getElementById('EventType').selectedIndex == 0) // EventState is only a required field if EventType is set to 'Classroom'.
	{
	if (document.getElementById('EventState').selectedIndex == 0)
		{
		document.getElementById("EventStateError").style.display = "inline";
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
	}
}

function checkEventAreaCodeOnly()
{
// Validate EventAreaCode field.
var eventAreaCodeValue = document.getElementById("EventAreaCode").value;
reqdCharSet = /^(\d{3})$/;  
if (document.getElementById('EventType').selectedIndex == 0) // EventCity is only a required field if EventType is set to 'Classroom'.
	{
	if (!reqdCharSet.test(eventAreaCodeValue))
		{
		document.getElementById("EventAreaCodeError").style.display = "inline";
		anchordestn = "#EventAreaCodeAnchor";
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
	}
}

function checkEventZipCodeOnly()
{
// Validate EventZipCode field.
var eventZipCodeValue = document.getElementById("EventZipCode").value;
reqdCharSet = /^(\d{5})$/;  
if (document.getElementById('EventType').selectedIndex == 0) // EventCity is only a required field if EventType is set to 'Classroom'.
	{
	if (!reqdCharSet.test(eventZipCodeValue))
		{
		document.getElementById("EventZipCodeError").style.display = "inline";
		anchordestn = "#EventZipCodeAnchor";
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
	}
}

function checkCostStdOnly()
{
// Validate NofHours input field.
var costStdValue = document.getElementById("CostStd").value;
var illegalCharSet = /[^0-9\.]+/; // Reject everything that contains one or more characters that is neither a period nor a digit.
var reqdCharSet = /[0-9]+/;  // At least one numeric.
if (illegalCharSet.test(costStdValue)  || !reqdCharSet.test(costStdValue))
	{
	document.getElementById("CostStdError").style.display = "inline";
	return false;
	} 
else
	{
	return true;
	}
}

function checkCostEbirdOnly()
{
// Validate CostEbird field.
var costEbirdValue = document.getElementById("CostEbird").value;
var illegalCharSet = /[^0-9\.]+/; // Reject everything that contains one or more characters that is neither a period nor a digit.
var reqdCharSet = /[0-9]+/;  // At least one numeric.
if (document.getElementById('EarlyBirdAvailable').checked) // CostEbirdOnly is only a required field if EarlyBirdAvailable check-box is checked.
	{
	if (illegalCharSet.test(costEbirdValue)  || !reqdCharSet.test(costEbirdValue))
		{
		document.getElementById("EarlyBirdError").style.display = "inline";
		anchordestn = "#EarlyBirdAvailableAnchor";
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
	}
}

function checkEbirdDeadlineOnly()
{
// Validate EbirdDeadline field.
var ebirdDeadlineValue  = document.getElementById("EbirdDeadline").value;
var illegalCharSet = /[^0-9\/]+/; // Reject everything that contains one or more characters that is neither a slash (/) nor a digit. Note the need to escape the slash.
var reqdCharSet = /\d{2}\/\d{2}\/\d{4}/;  // Required format is MM/DD/YYYY.
if (document.getElementById('EarlyBirdAvailable').checked) // EbirdDeadline is only a required field if EarlyBirdAvailable check-box is checked.
	{
	if (illegalCharSet.test(ebirdDeadlineValue)  || !reqdCharSet.test(ebirdDeadlineValue))
		{
		document.getElementById("EarlyBirdError").style.display = "inline";
		anchordestn = "#EarlyBirdAvailableAnchor";
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
	}
}

function checkEventOverviewOnly()
{
// Validate EventOverview field.
var eventOverviewValue = document.getElementById("EventOverview").value;
eventOverviewValue = eventOverviewValue.replace(/^\s*|\s*$/g,''); // PHP inside adminaddevent_slave.php will trim white space from beginning and end of the string, so there's no need to "catch" any such white space during Javascript validation. Better to avoid annoying user by just trimming it here inside admintrainer.php then JS-validating what remains of the overviewValue string. (Note: JS doesn't have a trim() function analogous to PHP, so I build one using JS's replace() method.
var eventOverviewLength = eventOverviewValue.length;
illegalCharSet1 = /[\r\n\t\f\v]+/; // Reject special characters return, newline, tab, form feed, vertical tab.
if (illegalCharSet1.test(eventOverviewValue) || (eventOverviewLength>600)) // Check the validity of the user's EventOverview text.
	{
	document.getElementById("EventOverviewError").style.display = "inline";
	anchordestn = "#EventOverviewAnchor";
	return false;
	} 
else
	{
	return true;
	}
}

function checkRegContactOnly()
{
// Validate RegContact field.
var regContactValue = document.getElementById('RegContact').value;
illegalCharSet = /[^A-Za-z'0-9,\/\-\.\& ]+/; // Exclude everything except A-Z, a-z, apostrophe ('), 0-9, ampersand, comma, period, slash, hyphen, space.
if (document.getElementById('RegContact').value != '') // Since RegContact is an optional field, only validate if it's non-blank.
	{
	if (illegalCharSet.test(regContactValue))
		{
		document.getElementById('RegContactError').style.display = "inline";
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
	}
}

function checkRegTelOnly()
{
// Validate RegTel (Tel1, Tel2, Tel3 fields).
var regTel1Value = document.getElementById("RegTel1").value;
var regTel2Value = document.getElementById("RegTel2").value;
var regTel3Value = document.getElementById("RegTel3").value;
var regTel1Length = regTel1Value.length;
var regTel2Length = regTel2Value.length;
var regTel3Length = regTel3Value.length;
illegalCharSet = /[^0-9]+/; // Reject everything that contains one or more non-digits.
if (regTel1Length!=0 || regTel2Length!=0 || regTel3Length!=0) // Only bother to validate if any of the three telephone text fields is non-blank.
	{
	if (illegalCharSet.test(regTel1Value) || illegalCharSet.test(regTel2Value) || illegalCharSet.test(regTel3Value) || regTel1Length!=3 || regTel2Length!=3 || regTel3Length!=4)
		{
		document.getElementById("RegTelError").style.display = "inline";
		anchordestn = "#RegTelAnchor";
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
	}
}

function checkRegURLOnly()
{
// Validate RegURL field. Note: also admits "page" URLs of form www.mediate.com/janedoe
reqdCharSet = /^(http:\/\/|https:\/\/)?(([\w|\-])+\.)+(com|edu|org|us|net|biz|gov|mobi|mil|ca|uk|hk|asia|tv|jobs|bz|cc|co)+/;
var regURLValue = document.getElementById("RegURL").value;
var regURLLength = regURLValue.length;
if (regURLValue != null && regURLValue  != '') // Since RegURL is optional, only validate if it's non-blank.
	{
	if (!reqdCharSet.test(regURLValue))
		{
		document.getElementById("RegURLError").style.display = "inline";
		anchordestn = "#RegURLAnchor";
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
	}
} 

function checkRegEmailOnly()
{
// Validate EntityEmail field.
var regEmailValue = document.getElementById("RegEmail").value;
var regEmailLength = regEmailValue.length;
if (regEmailValue != null && regEmailValue  != '') // Since RegEmail is optional, only validate if it's non-blank.
	{
	if (regEmailValue > 60 || !(emailCheck(regEmailValue,'noalert'))) // emailCheck() is function in emailaddresschecker.js. This field is reqd i.e. blank causes a rejection as invalid.
		{
		document.getElementById("RegEmailError").style.display = "inline";
		anchordestn = "#RegEmailAnchor";
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
	}
} 

function checkCertBodyOnly()
{
// Validate CertBody field.
var certBodyValue = document.getElementById('CertBody').value;
illegalCharSet = /[^A-Za-z'0-9,\/\-\.\& ]+/; // Exclude everything except A-Z, a-z, apostrophe ('), 0-9, ampersand, comma, period, slash, hyphen, space.
if (document.getElementById('CertBody').value != '') // Since CertBody is an optional field, only validate if it's non-blank.
	{
	if (illegalCharSet.test(certBodyValue))
		{
		document.getElementById('CertBodyError').style.display = "inline";
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
	}
}

function checkForm() // checkForm() gets called when the user clicks the submit button.
{
hideAllErrors();
if (!checkTrainingTypeOnly()) return false;
if (!checkStartDateOnly()) return false;
if (!checkEndDateOnly()) return false;
if (!checkNofHoursOnly()) return false;
if (!checkNofDaysOnly()) return false;
if (!checkEventCityOnly()) return false;
if (!checkEventStateOnly()) return false;
if (!checkEventAreaCodeOnly()) return false;
if (!checkEventZipCodeOnly()) return false;
if (!checkCostStdOnly()) return false;
if (!checkCostEbirdOnly()) return false;
if (!checkEbirdDeadlineOnly()) return false;
if (!checkRegContactOnly()) return false;
if (!checkRegTelOnly()) return false;
if (!checkRegURLOnly()) return false;
if (!checkRegEmailOnly()) return false;
if (!checkCertBodyOnly()) return false;
return true; // All elements passed their validity checks, so return a true.
} // End of checkForm()

/* This function hideAllErrors() is called by checkForm() and by onblur event. */
function hideAllErrors()
{
document.getElementById("TrainingTypeError").style.display = "none";
document.getElementById("StartDateError").style.display = "none";
document.getElementById("EndDateError").style.display = "none";
document.getElementById("NofHoursError").style.display = "none";
document.getElementById("NofDaysError").style.display = "none";
document.getElementById("EventCityError").style.display = "none";
document.getElementById("EventStateError").style.display = "none";
document.getElementById("EventAreaCodeError").style.display = "none";
document.getElementById("EventZipCodeError").style.display = "none";
document.getElementById("CostStdError").style.display = "none";
document.getElementById("EarlyBirdError").style.display = "none";
document.getElementById("EventOverviewError").style.display = "none";
document.getElementById("RegContactError").style.display = "none";
document.getElementById("RegTelError").style.display = "none";
document.getElementById("RegURLError").style.display = "none";
document.getElementById("RegEmailError").style.display = "none";
document.getElementById("CertBodyError").style.display = "none";
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

<h1 style="margin-top: 15px; font-size: 22px; text-align: center; margin-left: auto; margin-right: auto;">Add Training Event to National Mediation Training Registry</h1>

<?php
$LogIn = $_POST['LogIn'];

// Present the username/password log-in screen unless the trainer has already successfully logged in i.e. $ValidatedLogIn == 1 or has clicked the "Log In" button.
if ($ValidatedLogIn != 1 && !isset($LogIn))
	{
?>
	<div style="text-align: center; width: 250px; margin-left: auto; margin-right: auto; margin-top: 30px; padding: 15px; border: 2px solid #444444; border-color: #9C151C;">
	<h4 class="forms">Please log in to add an event to the registry</h4>
	<div><a style="font-size: 10px;" href="/userpassreminder.php">Forgot your username/password?</a></div><br />
	<form method="post" action="/addevent.php">
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
	<div style="margin-top: 30px; margin-bottom: 20px; font-family: Geneva, Arial, Helvetica, sans-serif; font-size: 15px;">No username/password? <a href="/join.php">Join</a> the National Mediation Training Registry.</div>
<?php
	}

if (isset($LogIn)) // The user has entered a username/password to log in.
	{
	unset($LogIn);
	$Username = $_POST['Username']; // Use short names for username and password.
	$Password = $_POST['Password'];
	
	// Examine trainers_table to be sure such a username-password pair exists
	$db = mysql_connect('localhost', 'paulme6_merlyn', '')
	or die('Could not connect: ' . mysql_error());
	mysql_select_db('paulme6_medtrainers') or die('Could not select database: ' . mysql_error());

	$query = "SELECT COUNT(*) AS TheCount, EntityName FROM trainers_table WHERE Username = '".$Username."' AND Password = '".$Password."'";
	$result = mysql_query($query) or die('Query (count of matching username-password pairs in trainers_table) failed: ' . mysql_error().' and the query string was: '.$query);

	$row = mysql_fetch_assoc($result); // $row array should have just one item, which holds either '0' or '1'
	$count = $row['TheCount'];

	if ($count == 0) // username-password pair entered is invalid. User can now proceed to see the form for entering trainer details.
		{
		echo "<br><p class='basictext' style='margin-left: 50px; margin-right: 50px; margin-top: 40px; font-size: 14px;'>Incorrect username or password. Please use your browser&rsquo;s Back button or <a href='addevent.php' style='font-size: 14px;'>click here</a> to try again.</p>";
		$Username = null;
		$Password = null;
		exit;
		}
	else if ($count > 1) // This condition should never arise.
		{
		$message = "A spurious condition has arisen in file addevent.php on the mediationtrainings.org server. This note is being sent to you as the webmaster.";
		// In case any of our lines are larger than 70 characters, we should use wordwrap()
		$message = wordwrap($message, 70);
		mail('paulmerlyn@yahoo.com', 'Spurious Condition Alert in addevent.php', $message);
		echo "<br><p class='basictext' style='margin-left: 50px; margin-right: 50px; margin-top: 40px; font-size: 14px;'>A spurious condition has been detected by the server. Our webmaster has been notified. We apologize for the inconvenience. Please use your browser&rsquo;s Back button or <a href='addevent.php' style='font-size: 14px;'>click here</a> to try again.</p>";
		exit;
		}
	else if (empty($row['EntityName'])) // The trainer obviously hasn't yet created a Trainer Profile.
		{
		echo "<br><p class='basictext' style='margin-left: 50px; margin-right: 50px; margin-top: 40px; font-size: 14px;'>You&rsquo;ll need to create your Trainer Profile before you can add training events to the Registry. Please <a href='addtrainer.php' style='font-size: 14px;'>click here</a> to create a Trainer Profile.</p>";
		exit;
		}
	else // $count == 1 so there is one match and the username-password pair is legitimate.
		{
		// Before proceeding to show the main HTML form for editing a Trainer Profile, check whether trainer's payments are in good standing (i.e. PaidUp == 1). If they aren't, (i) set his/her Approved column to 0 in trainers_table (thereby preventing his/her Trainer Profile and events from showing up in the Registry, and (ii) require him/her to activate his/her listing by paying via the activate.php page.
		$query = "SELECT PaidUp, TrainerID, EntityName FROM trainers_table WHERE Username = '".$Username."' AND Password = '".$Password."'";
		$result = mysql_query($query) or die('Query (select of PaidUp, TrainerID, EntityName from trainers_table) failed: ' . mysql_error().' and the query string was: '.$query);
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
			$_SESSION['selectedID'] = $row['TrainerID'];
			}
		}
	}

if ($ValidatedLogIn == 1)
	{
?>
	<div style="margin-left: auto; margin-right: auto; margin-top: 0px; width: 750px; padding: 15px; border: 2px solid #444444; border-color: #9C151C;">
	<h1 style="text-align: left;">Add a training event for <?=$row['EntityName']; ?>:</h1>
	<br>
			
	<form name="AddEvent" method="post" action="/scripts/addevent_slave.php">
			
			<table width="750">
			<tr height="60">
			<td align="left" width="150" valign="top"><label for="TrainingType" style="position:relative; top: 10px;">Training Type</label><span class="redsup" style="position: relative; top: 8px;">&nbsp;&nbsp;<b>*</b>&nbsp;(required)</span></td>
			<td align="left">
			<a name="TrainingTypeAnchor" class="plain"><input type="checkbox" name="BasicMed" id="BasicMed" value="true" onClick="if (this.checked) { document.getElementById('WorkplaceMed').disabled = true; document.getElementById('DivorceMed').disabled = true; document.getElementById('MaritalMed').disabled = true; document.getElementById('FamilyMed').disabled = true; document.getElementById('ElderMed').disabled = true; document.getElementById('OtherType').disabled = true; } else { enableAll(); if (!this.checked && !document.getElementById('WorkplaceMed').checked && !document.getElementById('DivorceMed').checked && !document.getElementById('FamilyMed').checked && !document.getElementById('MaritalMed').checked && !document.getElementById('ElderMed').checked) { document.getElementById('OtherType').disabled = false; }; };"></a><label>&nbsp;&nbsp;Basic Mediation</label><br>
			<input type="checkbox" name="WorkplaceMed" id="WorkplaceMed" value="true" onClick="if (this.checked) { document.getElementById('BasicMed').disabled = true; document.getElementById('DivorceMed').disabled = true; document.getElementById('MaritalMed').disabled = true; document.getElementById('FamilyMed').disabled = true; document.getElementById('ElderMed').disabled = true; document.getElementById('OtherType').disabled = true; } else { enableAll(); if (!this.checked && !document.getElementById('BasicMed').checked && !document.getElementById('DivorceMed').checked && !document.getElementById('FamilyMed').checked && !document.getElementById('MaritalMed').checked && !document.getElementById('ElderMed').checked) { document.getElementById('OtherType').disabled = false; }; };"><label>&nbsp;&nbsp;Workplace Mediation</label>
			</td>
			<td align="left">
			<div>
			<input type="checkbox" name="DivorceMed" id="DivorceMed" value="true" onClick="if (this.checked) { document.getElementById('WorkplaceMed').disabled = true; document.getElementById('BasicMed').disabled = true; document.getElementById('MaritalMed').disabled = true; document.getElementById('FamilyMed').disabled = true; document.getElementById('ElderMed').disabled = true; document.getElementById('OtherType').disabled = true; } else { enableAll(); if (!this.checked && !document.getElementById('WorkplaceMed').checked && !document.getElementById('BasicMed').checked && !document.getElementById('FamilyMed').checked && !document.getElementById('MaritalMed').checked && !document.getElementById('ElderMed').checked) { document.getElementById('OtherType').disabled = false; }; };"><label>&nbsp;&nbsp;Divorce Mediation</label><br>
			<input type="checkbox" name="MaritalMed" id="MaritalMed" value="true" onClick="if (this.checked) { document.getElementById('WorkplaceMed').disabled = true; document.getElementById('DivorceMed').disabled = true; document.getElementById('BasicMed').disabled = true; document.getElementById('FamilyMed').disabled = true; document.getElementById('ElderMed').disabled = true; document.getElementById('OtherType').disabled = true; } else { enableAll(); if (!this.checked && !document.getElementById('WorkplaceMed').checked && !document.getElementById('DivorceMed').checked && !document.getElementById('FamilyMed').checked && !document.getElementById('BasicMed').checked && !document.getElementById('ElderMed').checked) { document.getElementById('OtherType').disabled = false; }; };"><label>&nbsp;&nbsp;Marital Mediation</label>
			</div>
			</td>
			<td align="left">
			<input type="checkbox" name="FamilyMed" id="FamilyMed" value="true" onClick="if (this.checked) { document.getElementById('WorkplaceMed').disabled = true; document.getElementById('DivorceMed').disabled = true; document.getElementById('MaritalMed').disabled = true; document.getElementById('BasicMed').disabled = true; document.getElementById('ElderMed').disabled = true; document.getElementById('OtherType').disabled = true; } else { enableAll(); if (!this.checked && !document.getElementById('WorkplaceMed').checked && !document.getElementById('DivorceMed').checked && !document.getElementById('BasicMed').checked && !document.getElementById('MaritalMed').checked && !document.getElementById('ElderMed').checked) { document.getElementById('OtherType').disabled = false; }; };"><label>&nbsp;&nbsp;Family Mediation</label><br>
			<input type="checkbox" name="ElderMed" id="ElderMed" value="true" onClick="if (this.checked) { document.getElementById('WorkplaceMed').disabled = true; document.getElementById('DivorceMed').disabled = true; document.getElementById('MaritalMed').disabled = true; document.getElementById('FamilyMed').disabled = true; document.getElementById('BasicMed').disabled = true; document.getElementById('OtherType').disabled = true; } else { enableAll(); if (!this.checked && !document.getElementById('WorkplaceMed').checked && !document.getElementById('DivorceMed').checked && !document.getElementById('FamilyMed').checked && !document.getElementById('MaritalMed').checked && !document.getElementById('BasicMed').checked) { document.getElementById('OtherType').disabled = false; }; };"><label>&nbsp;&nbsp;Elder Mediation</label>
			</td>
			</tr>
			<tr>
			<td align="left">&nbsp;</td>
			<td colspan="3" align="left"><div style="position: relative; bottom: 10px;"><a name="OtherTypeAnchor" class="plain"><input type="text" class="textfieldsmall" name="OtherType" id="OtherType" maxlength="50" size="30" onSelect="disableAll();" onKeyDown="if (this.value != '') { document.getElementById('BasicMed').checked = false; document.getElementById('BasicMed').disabled = true; document.getElementById('DivorceMed').checked = false; document.getElementById('DivorceMed').disabled = true; document.getElementById('WorkplaceMed').checked = false; document.getElementById('WorkplaceMed').disabled = true; document.getElementById('FamilyMed').checked = false; document.getElementById('FamilyMed').disabled = true; document.getElementById('MaritalMed').checked = false; document.getElementById('MaritalMed').disabled = true; document.getElementById('ElderMed').checked = false; document.getElementById('ElderMed').disabled = true; };" onFocus="this.style.background='#FFFF99'; if (this.value != '') { document.getElementById('BasicMed').checked = false; document.getElementById('BasicMed').disabled = true; document.getElementById('DivorceMed').checked = false; document.getElementById('DivorceMed').disabled = true; document.getElementById('WorkplaceMed').checked = false; document.getElementById('WorkplaceMed').disabled = true; document.getElementById('FamilyMed').checked = false; document.getElementById('FamilyMed').disabled = true; document.getElementById('MaritalMed').checked = false; document.getElementById('MaritalMed').disabled = true; document.getElementById('ElderMed').checked = false; document.getElementById('ElderMed').disabled = true; } else { document.getElementById('BasicMed').disabled = false; document.getElementById('DivorceMed').disabled = false; document.getElementById('WorkplaceMed').disabled = false; document.getElementById('FamilyMed').disabled = false; document.getElementById('MaritalMed').disabled = false; document.getElementById('ElderMed').disabled = false; };" onBlur="this.style.background='white'; document.getElementById('BasicMed').disabled = false; document.getElementById('WorkplaceMed').disabled = false; document.getElementById('DivorceMed').disabled = false; document.getElementById('FamilyMed').disabled = false; document.getElementById('MaritalMed').disabled = false; document.getElementById('ElderMed').disabled = false;"></a>&nbsp;&nbsp;<label>Other (please specify)</label></div></td>
			</tr>
			<tr>
			<td height="30">&nbsp;</td>
			<td align="left" colspan="3"><div class="greytextsmall" style="position: relative; bottom: 10px;">Either check one of the boxes, or complete the &lsquo;Other&rsquo; text field to indicate the type of training.</div><div class="error" id="TrainingTypeError" style="position: relative; bottom: 12px;">Please select at least one check-box or fill in the &lsquo;Other&rsquo; text field.<br></div><?php if ($_SESSION['MsgTrainingType'] != null) { echo $_SESSION['MsgTrainingType']; $_SESSION['MsgTrainingType']=null; } ?></td>
			</tr>
			<tr>
			<td height="50" align="left"><label style="position: relative; bottom: 16px;">Start Date</label><span class="redsup" style="position: relative; bottom: 16px;">&nbsp;&nbsp;<b>*</b></span></td>
			<td align="left" valign="top">
			<input type="text" class="textfieldsmall" name="StartDate" id="StartDate" maxlength="10" size="10" value="<?=date('m/d/Y');?>" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkStartDateOnly();">
			<script language="JavaScript">
			new tcal ({
			'formname': 'AddEvent',
			'controlname': 'StartDate'
			});
			</script>
			<div class="error" id="StartDateError"><br>Date format MM/DD/YYYY.<br></div>
			<?php if ($_SESSION['MsgStartDate'] != null) { echo $_SESSION['MsgStartDate']; $_SESSION['MsgStartDate']=null; } ?>
			</td>
			<td colspan="2" align="left" valign="top">
			<label>End Date</label><span class="redsup">&nbsp;&nbsp;<b>*</b></span>&nbsp;&nbsp;<input type="text" class="textfieldsmall" name="EndDate" id="EndDate" maxlength="10" size="10" value="<?=date('m/d/Y');?>" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkEndDateOnly();">
			<script language="JavaScript">
			new tcal ({
			'formname': 'AddEvent',
			'controlname': 'EndDate'
			});
			</script>
			<div class="error" id="EndDateError"><br>Date must have format MM/DD/YYYY.<br></div>
			<?php if ($_SESSION['MsgEndDate'] != null) { echo $_SESSION['MsgEndDate']; $_SESSION['MsgEndDate']=null; } ?>
			</td>
			</tr>
			<tr>
			<td height="40" align="left"><label>Duration</label><span class="redsup">&nbsp;&nbsp;<b>*</b></span></td>
			<td align="left" colspan="3">
			<input type="text" class="textfieldsmall" name="NofHours" id="NofHours" maxlength="4" size="3" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkNofHoursOnly();"><span class="redsup">&nbsp;&nbsp;<b>*</b></span>&nbsp;&nbsp;<label>hours</label>
			&nbsp;&nbsp;<label>over a period of</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" class="textfieldsmall" name="NofDays" id="NofDays" maxlength="4" size="3" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkNofDaysOnly();">
			<span class="redsup">&nbsp;&nbsp;<b>*</b></span>&nbsp;&nbsp;<label>days</label>
			</td>
			</tr>
			<tr>
			<td>&nbsp;</td>
			<td colspan="3" align="left" valign="top">
			<div class="error" id="NofHoursError">Specify a number of hours. Use only digits e.g. 32 for 32 hours.<br></div>
			<?php if ($_SESSION['MsgNofHours'] != null) { echo $_SESSION['MsgNofHours']; $_SESSION['MsgNofHours']=null; } ?>
			<div class="error" id="NofDaysError">Specify a number of days. Use only digits e.g. 5 for 5 days.<br></div>
			<?php if ($_SESSION['MsgNofDays'] != null) { echo $_SESSION['MsgNofDays']; $_SESSION['MsgNofDays']=null; } ?>
			</td>
			</tr>
			<tr height="20">
			<td height="40" align="left" valign="top"><label for="EventType">Event Type/Location</label><span class="redsup">&nbsp;&nbsp;<b>*</b></span></td>
			<td align="left" colspan="3">
			<select class="smallredoutline" name="EventType" id="EventType" size="1" onchange="if (this.selectedIndex == 0) document.getElementById('classroom_city_state').style.display = 'inline'; else document.getElementById('classroom_city_state').style.display = 'none';">
			<option value="classroom" selected>Classroom</option>
			<option value="online">Online</option>
			</select>
			<span id="classroom_city_state">
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label>City</label>&nbsp;&nbsp;<a name="EventCityAnchor" class="plain"><input type="text" class="textfieldsmall" name="EventCity" id="EventCity" maxlength="24" size="20" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkEventCityOnly();"></a><span class="redsup">&nbsp;&nbsp;<b>*</b></span>
			<label for="EventState">State</label>&nbsp;&nbsp;
			<select class="smallredoutline" name="EventState" id="EventState" size="1" onchange="hideAllErrors(); return checkEventStateOnly();">
			<?php
			$statesArray = array(array('&lt;&nbsp; No Preference &nbsp;&gt;',null), array('Alabama','AL'),	array('Alaska','AK'), array('Arizona','AZ'), array('Arkansas','AR'),	array('California','CA'), array('Colorado','CO'), array('Connecticut','CT'), array('Delaware','DE'), array('District of Columbia','DC'), array('Florida','FL'), array('Georgia','GA'), array('Hawaii','HI'), array('Idaho','ID'), array('Illinois','IL'), array('Indiana','IN'), array('Iowa','IA'), array('Kansas','KS'), array('Kentucky','KY'), array('Louisiana','LA'), array('Maine','ME'), array('Maryland','MD'), array('Massachusetts','MA'), array('Michigan','MI'), array('Minnesota','MN'), array('Mississippi','MS'), array('Missouri','MO'), array('Montana','MT'), array('Nebraska','NE'), array('Nevada','NV'), array('New Hampshire','NH'), array('New Jersey','NJ'), array('New Mexico','NM'), array('New York','NY'), array('North Carolina','NC'), array('North Dakota','ND'), array('Ohio','OH'), array('Oklahoma','OK'), array('Oregon','OR'), array('Pennsylvania','PA'), array('Rhode Island','RI'), array('South Carolina','SC'), array('South Dakota','SD'), array('Tennessee','TN'), array('Texas','TX'), array('Utah','UT'), array('Vermont','VT'), array('Virginia','VA'), array('Washington','WA'), array('Washington, D.C.','DC'), array('West Virginia','WV'), array('Wisconsin','WI'), array('Wyoming','WY'));
			for ($i=0; $i<53; $i++)
				{
				$optiontag = '<option value="'.$statesArray[$i][1].'" ';
				$optiontag = $optiontag.'>'.$statesArray[$i][0]."</option>\n";
				echo $optiontag;
				}
			?>
			</select><span class="redsup">&nbsp;&nbsp;<b>*</b></span>
			<br /><br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<label for="EventAreaCode">Telephone Area Code</label>&nbsp;&nbsp;<a name="EventAreaCodeAnchor" class="plain"><input type="text" class="textfieldsmall" name="EventAreaCode" id="EventAreaCode" maxlength="3" size="3" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkEventAreaCodeOnly();"></a><span class="redsup">&nbsp;&nbsp;<b>*</b></span>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<label>Zip Code</label>&nbsp;&nbsp;<a name="EventZipCodeAnchor" class="plain"><input type="text" class="textfieldsmall" name="EventZipCode" id="EventZipCode" maxlength="5" size="5" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkEventZipCodeOnly();"></a><span class="redsup">&nbsp;&nbsp;<b>*</b></span>
			</span>
			</td>
			</tr>
			<tr><!-- This row is specially created to hold EventCity and EventState errors. -->
			<td>&nbsp;</td>
			<td colspan="3" valign="top" align="left">
			<div class="error" id="EventCityError">Please enter a valid city. Use only letters (A-Z, a-z), dash (-), apostrophe ('), and space characters here.<br>Use initial capital (upper-case) letters. Examples: <i>Springfield</i> or <i>South Bend</i>.<br></div><?php if ($_SESSION['MsgEventCity'] != null) { echo $_SESSION['MsgEventCity']; $_SESSION['MsgEventCity']=null; } ?>
			<div class="error" id="EventStateError">Please select a state from the drop-down menu.<br></div>
			<?php if ($_SESSION['MsgEventState'] != null) { echo $_SESSION['MsgEventState']; $_SESSION['MsgEventState']=null; } ?>
			<div class="error" id="EventAreaCodeError">Please enter the 3-digit telephone area code for the location of your training event.<br></div><?php if ($_SESSION['MsgEventAreaCode'] != null) { echo $_SESSION['MsgEventAreaCode']; $_SESSION['MsgEventAreaCode']=null; } ?>
			<div class="error" id="EventZipCodeError">Please enter the 5-digit zip code for the location of your training event.<br></div><?php if ($_SESSION['MsgEventZipCode'] != null) { echo $_SESSION['MsgEventZipCode']; $_SESSION['MsgEventZipCode']=null; } ?>
			</td>
			</tr>
			<tr height="20">
			<td height="40" align="left"><label for="CostStd">Cost (Standard Rate)</label><span class="redsup">&nbsp;&nbsp;<b>*</b></span></td>
			<td align="left" colspan="3">
			<a name="CostStdAnchor" class="plain"></a>
			<label>$</label>&nbsp;<input type="text" class="textfieldsmall" name="CostStd" id="CostStd" maxlength="8" size="6" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkCostStdOnly();"><span class="redsup">&nbsp;&nbsp;<b>*</b></span>
			<div class="error" id="CostStdError"><br>Enter the cost or fee ($) for this training. Use only digits e.g. 1045 for $1045.<br></div>
			<?php if ($_SESSION['MsgCostStd'] != null) { echo $_SESSION['MsgCostStd']; $_SESSION['MsgCostStd']=null; } ?>
			</td>
			</tr>
			<tr height="40">
			<td align="left" valign="top"><label for="EarlyBird" style="position:relative; top: 16px;">Early Bird</label></td>
			<td align="left">
			<a name="EarlyBirdAvailable" class="plain"></a><input type="checkbox" name="EarlyBirdAvailable" id="EarlyBirdAvailable" value="true" onChange="if (this.checked) { document.getElementById('CostEbird').disabled = false; document.getElementById('EbirdDeadline').disabled = false; document.getElementById('CostEbird').focus(); } else { document.getElementById('CostEbird').disabled = true; document.getElementById('EbirdDeadline').disabled = true; };"><label>&nbsp;Early-Bird Rate Available?</label><br>
			</td>
			<td colspan="2"><label>$</label>&nbsp;<input type="text" class="textfieldsmall" name="CostEbird" id="CostEbird" maxlength="8" size="4" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkCostEbirdOnly();" disabled>
			&nbsp;&nbsp;<label>Early-Bird Rate</label>
			&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" class="textfieldsmall" name="EbirdDeadline" id="EbirdDeadline" maxlength="10" size="10" value="<?=date('m/d/Y');?>" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkEbirdDeadlineOnly();" disabled>
			<script language="JavaScript">
			new tcal ({
			'formname': 'AddEvent',
			'controlname': 'EbirdDeadline'
			});
			</script>
			&nbsp;<label>Deadline</label>
			<div class="error" id="EbirdDeadlineError"><br>Date must have format MM/DD/YYYY.<br></div></td>
			</tr>
			<tr>
			<td>&nbsp;</td>
			<td align="left" colspan="3" valign="top"><div class="greytextsmall">
			If you offer an early-bird discount, check the box then enter the early-bird rate and deadline date to register at this rate.</div><div class="error" id="EarlyBirdError">Please enter the &ldquo;early-bird&rdquo; fee ($ cost) of this event. Also, click the calendar icon to select the last date (deadline) for registering at this rate. Use date format MM/DD/YYYY.<br></div><?php if ($_SESSION['MsgEarlyBird'] != null) { echo $_SESSION['MsgEarlyBird']; $_SESSION['MsgEarlyBird']=null; } ?>
			</td>
			</tr>
			<tr>
			<td valign="top" align="left"><label for="EventOverview" style="position: relative; top: 10px;">Event Overview</label></td>
			<td colspan="3" valign="top" align="left"><a name="EventOverviewAnchor" class="plain"><textarea name="EventOverview" id="EventOverview" rows="8" cols="80" wrap="soft" style="overflow:auto; height: 120px; width: 540px;" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkEventOverviewOnly('EventOverview', 'EventOverviewError', 'EventOverviewAnchor');" onKeyUp="charCount('EventOverview','sBann1EventOverview','{CHAR} chars'); toCount('EventOverview','sBann2EventOverview','{CHAR} chars',600);"></textarea></a>
			<div class="greytextsmall">Single paragraph. Maximum: 600 characters. Count: [<span id="sBann1EventOverview" class="greytextsmall" style="font-weight:bold;">0 chars</span>]&nbsp;&nbsp;Remaining: [<span id="sBann2EventOverview" class="greytextsmall" style="font-weight:bold;">600 chars</span>]</div>
			<div class="error" id="EventOverviewError">Please remove any newline characters ('return' or 'enter'). To remove them, use the [delete] or [backspace] keys.<br>Also, please ensure your text is less than 600 characters (approx. 100 words) in length.<br></div><?php if ($_SESSION['MsgEventOverview'] != null) { echo $_SESSION['MsgEventOverview']; $_SESSION['MsgEventOverview']=null; };?></td>
			</tr>
			<tr height="20">
			<td height="40" align="left"><label for="RegContact">Registration Contact</label></td>
			<td align="left" width="175"><a name="RegContactAnchor" class="plain"><input type="text" class="textfieldsmall" name="RegContact" id="RegContact" maxlength="30" size="20" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkRegContactOnly();"></a></td>
			<td align="right" colspan="2">
			<label for="RegTel1">Telephone</label>&nbsp;&nbsp;<a name="RegTel1Anchor" class="plain"><input type="text" class="textfieldsmall" name="RegTel1" id="RegTel1" maxlength="3" size="3" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white';"></a>&nbsp;&nbsp;&nbsp;<input type="text" class="textfieldsmall" name="RegTel2" id="RegTel2" maxlength="3" size="3" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white';">&nbsp;&ndash;&nbsp;<input type="text" class="textfieldsmall" name="RegTel3" id="RegTel3" maxlength="4" size="4" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkRegTelOnly();">
<div class="error" id="RegTelError"><br>Leave blank or enter a valid phone number. Use only digits (0-9).<br></div><?php if ($_SESSION['MsgRegTel'] != null) { echo $_SESSION['MsgRegTel']; $_SESSION['MsgRegTel']=null; } ?>
			</td>
			</tr>
			<tr>
			<td>&nbsp;</td>
			<td colspan="3" align="left" valign="top">
			<div class="error" id="RegContactError">Enter the name of a contact person for registration of this event, or leave this field blank.<br></div><?php if ($_SESSION['MsgRegContact'] != null) { echo $_SESSION['MsgRegContact']; $_SESSION['MsgRegContact']=null; };?></td>
			</td>
			</tr>
			<tr>
			<td height="40" align="left"><label for="RegURL" style="position: relative; bottom: 6px;">Registration Web Page </label></td>
			<td align="left" colspan="2"><a name="RegURLAnchor" class="plain"><input type="text" class="textfieldsmall" name="RegURL" id="RegURL" maxlength="150" size="30" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkRegURLOnly();"></a>
			<div class="greytextsmall">Example: www.janedoemediation.com/april_workshop.htm</div>
			<div class="error" id="RegURLError">Please check the format of your web address.<br></div><?php if ($_SESSION['MsgRegURL'] != null) { echo $_SESSION['MsgRegURL']; $_SESSION['MsgRegURL']=null; } ?>
			</td>
			<td align="right"><label for="RegEmail">Email</label>&nbsp;&nbsp;<a name="RegEmailAnchor" class="plain"><input type="text" class="textfieldsmall" name="RegEmail" id="RegEmail" maxlength="60" size="24" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkRegEmailOnly();"></a>
			<div class="greytextsmall" style="text-align: right;">Example: jane@janedoemediation.com</div>
			<div class="error" id="RegEmailError">Please check your format. Use only alphanumerics (A-Z, a-z, 0-9), dash (-), period (.), @, and underscore (_) characters.<br></div><?php if ($_SESSION['MsgRegEmail'] != null) { echo $_SESSION['MsgRegEmail']; $_SESSION['MsgRegEmail']=null; } ?>
			</td>
			<tr height="20">
			<td height="60" align="left" valign="bottom"><label for="CertBody" style="position: relative; bottom: 18px;">Certification Body</label></td>
			<td align="left" colspan="3" valign="bottom">
			<a name="CertBodyAnchor" class="plain"></a>
			<input type="text" class="textfieldsmall" name="CertBody" id="CertBody" maxlength="100" size="60" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkCertBodyOnly();">
			<div class="greytextsmall" style="position: relative; bottom: 0px;">Example: American Arbitration Association (or leave blank if none).</div><div class="error" id="CertBodyError">Please use only alphanumerics (A-Z, a-z, 0-9), dash (-), slash (/), period (.), apostrophe ('), &, and space characters.<br></div>
			<?php if ($_SESSION['MsgCertBody'] != null) { echo $_SESSION['MsgCertBody']; $_SESSION['MsgCertBody']=null; } ?>
			</td>
			</tr>
			<tr valign="bottom" height="50">
			<td colspan="4" align="center"><input type="submit" name="AddEvent" class="buttonstyle" style="margin-left: 0px;" value="Add Event" onClick="return checkForm();"></td>
			</tr>
			</table>
			
	  </form>

  </div>

			<?php
	}
exit;
?>	
</div>
</body>
</html>
