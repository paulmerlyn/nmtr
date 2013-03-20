<?php
/*
adminaddeventA.php is Part 2 of adminaddevent.php. Together, these allow an administrator (user) to add an event for a designated trainer. adminaddevent.php retrieves and displays trainer(s) that match selection criteria supplied by the user. The user then selects the trainer for whom he/she wants to add an event by clicking a check-box. On submit of this check-box form element, control passes to adminaddeventA.php, which provides the management user-interface for entering details of the event (for storage into the events_table). The back-end slave processing of adminaddevent.php and adminaddeventA.php is performed by adminaddevent_slave.php.
*/
// Start a session
session_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Add Event | Administrator of Mediation Training Registry</title>
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
<form method="post" action="/scripts/unwind.php">
<input type="submit" name="Logout" class="submitLinkSmall" value="Log Out">
</form>
</div>
			
<h1 style="margin-top: 15px; font-size: 22px;">mediationtrainings.org Administrator</h1>
<?php
require('ssi/adminmenu.php'); // Include the navigation menu.

// Create short variable names
$_SESSION['selectedID'] = $_POST['selectedID']; // Save the selected ID in a session variable for reuse by adminaddevent_slave.php

// Determine the trainer's EntityName for the selected TrainerID for use in the heading on this page.
$db = mysql_connect('localhost', 'paulme6_merlyn', 'fePhaCj64mkik')
or die('Could not connect: ' . mysql_error());
mysql_select_db('paulme6_medtrainers') or die('Could not select database: ' . mysql_error());

$query = "SELECT EntityName FROM trainers_table WHERE TrainerID = ".$_SESSION['selectedID'];
$result = mysql_query($query) or die('The SELECT EntityName from trainers_table failed i.e. '.$query.' failed: ' . mysql_error());
$line = mysql_fetch_assoc($result);
$EntityName = $line['EntityName'];
$_SESSION['EntityName'] = $EntityName; // Also store the EntityName as a session variable for use by adminaddevent_slave.php.


if  (empty($Authentication) && $_SESSION['Authenticated'] != 'true')
	{
	// Visitor needs to authenticate
	?>
	<div style="position: absolute; left: 240px; top: 100px; width: 250px; padding: 15px; border: 2px solid #9C151C; border-color: #9C151C;">
	<h4 class="forms">Please authenticate yourself:</h4>
	<br>
	<form method="post" action="adminaddevent.php">
	<table border="0" width="280">
	<tr>
	<td align="center"><input type="password" name="Authentication" maxlength="40" size="20"></td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
	<td align="center"><input type="submit" value="Authenticate"></td>
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
			<div style="margin-left: auto; margin-right: auto; margin-top: 0px; width: 750px; padding: 15px; border: 2px solid #9C151C; border-color: #9C151C;">
			<h1 style="text-align: left;">Add a training event to the database for trainer <?=$line['EntityName']; ?>:</h1>
			<br>
			
			<form name="AddEvent" method="post" action="/scripts/adminaddevent_slave.php">
			
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
			<td colspan="3" align="left"><div style="position: relative; bottom: 10px;"><a name="OtherTypeAnchor" class="plain"><input type="text" name="OtherType" id="OtherType" maxlength="50" size="30" onSelect="disableAll();" onKeyDown="if (this.value != '') { document.getElementById('BasicMed').checked = false; document.getElementById('BasicMed').disabled = true; document.getElementById('DivorceMed').checked = false; document.getElementById('DivorceMed').disabled = true; document.getElementById('WorkplaceMed').checked = false; document.getElementById('WorkplaceMed').disabled = true; document.getElementById('FamilyMed').checked = false; document.getElementById('FamilyMed').disabled = true; document.getElementById('MaritalMed').checked = false; document.getElementById('MaritalMed').disabled = true; document.getElementById('ElderMed').checked = false; document.getElementById('ElderMed').disabled = true; };" onFocus="this.style.background='#FFFF99'; if (this.value != '') { document.getElementById('BasicMed').checked = false; document.getElementById('BasicMed').disabled = true; document.getElementById('DivorceMed').checked = false; document.getElementById('DivorceMed').disabled = true; document.getElementById('WorkplaceMed').checked = false; document.getElementById('WorkplaceMed').disabled = true; document.getElementById('FamilyMed').checked = false; document.getElementById('FamilyMed').disabled = true; document.getElementById('MaritalMed').checked = false; document.getElementById('MaritalMed').disabled = true; document.getElementById('ElderMed').checked = false; document.getElementById('ElderMed').disabled = true; } else { document.getElementById('BasicMed').disabled = false; document.getElementById('DivorceMed').disabled = false; document.getElementById('WorkplaceMed').disabled = false; document.getElementById('FamilyMed').disabled = false; document.getElementById('MaritalMed').disabled = false; document.getElementById('ElderMed').disabled = false; };" onBlur="this.style.background='white'; document.getElementById('BasicMed').disabled = false; document.getElementById('WorkplaceMed').disabled = false; document.getElementById('DivorceMed').disabled = false; document.getElementById('FamilyMed').disabled = false; document.getElementById('MaritalMed').disabled = false; document.getElementById('ElderMed').disabled = false;"></a>&nbsp;&nbsp;<label>Other (please specify)</label></div></td>
			</tr>
			<tr>
			<td height="30">&nbsp;</td>
			<td align="left" colspan="3"><div class="greytextsmall" style="position: relative; bottom: 10px;">Either check one of the boxes, or complete the &lsquo;Other&rsquo; text field to indicate the type of training.</div><div class="error" id="TrainingTypeError" style="position: relative; bottom: 12px;">Please select at least one check-box or fill in the &lsquo;Other&rsquo; text field.<br></div><?php if ($_SESSION['MsgTrainingType'] != null) { echo $_SESSION['MsgTrainingType']; $_SESSION['MsgTrainingType']=null; } ?></td>
			</tr>
			<tr>
			<td height="50" align="left"><label style="position: relative; bottom: 16px;">Start Date</label><span class="redsup" style="position: relative; bottom: 16px;">&nbsp;&nbsp;<b>*</b></span></td>
			<td align="left" valign="top">
			<input type="text" name="StartDate" id="StartDate" maxlength="10" size="10" value="<?=date('m/d/Y');?>" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkStartDateOnly();">
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
			<label>End Date</label><span class="redsup">&nbsp;&nbsp;<b>*</b></span>&nbsp;&nbsp;<input type="text" name="EndDate" id="EndDate" maxlength="10" size="10" value="<?=date('m/d/Y');?>" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkEndDateOnly();">
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
			<input type="text" name="NofHours" id="NofHours" maxlength="4" size="3" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkNofHoursOnly();"><span class="redsup">&nbsp;&nbsp;<b>*</b></span>&nbsp;&nbsp;<label>hours</label>
			&nbsp;&nbsp;<label>over a period of</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="NofDays" id="NofDays" maxlength="4" size="3" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkNofDaysOnly();">
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
			<td height="40" align="left"><label for="EventType">Event Type</label><span class="redsup">&nbsp;&nbsp;<b>*</b></span></td>
			<td align="left" colspan="3">
			<select name="EventType" id="EventType" size="1" onchange="if (this.selectedIndex == 0) document.getElementById('classroom_city_state').style.display = 'inline'; else document.getElementById('classroom_city_state').style.display = 'none';">
			<option value="classroom" selected>Classroom</option>
			<option value="online">Online</option>
			</select>
			<span id="classroom_city_state">
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label>City</label>&nbsp;&nbsp;<a name="EventCityAnchor" class="plain"><input type="text" name="EventCity" id="EventCity" maxlength="24" size="20" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkEventCityOnly();"></a><span class="redsup">&nbsp;&nbsp;<b>*</b></span>
			<label for="EventState">State</label>&nbsp;&nbsp;
			<select name="EventState" id="EventState" size="1" onchange="hideAllErrors(); return checkEventStateOnly();">
			<?php
			$statesArray = array(array('&lt;&nbsp; Select a State &nbsp;&gt;',null), array('Alabama','AL'),	array('Alaska','AK'), array('Arizona','AZ'), array('Arkansas','AR'),	array('California','CA'), array('Colorado','CO'), array('Connecticut','CT'), array('Delaware','DE'), array('District of Columbia','DC'), array('Florida','FL'), array('Georgia','GA'), array('Hawaii','HI'), array('Idaho','ID'), array('Illinois','IL'), array('Indiana','IN'), array('Iowa','IA'), array('Kansas','KS'), array('Kentucky','KY'), array('Louisiana','LA'), array('Maine','ME'), array('Maryland','MD'), array('Massachusetts','MA'), array('Michigan','MI'), array('Minnesota','MN'), array('Mississippi','MS'), array('Missouri','MO'), array('Montana','MT'), array('Nebraska','NE'), array('Nevada','NV'), array('New Hampshire','NH'), array('New Jersey','NJ'), array('New Mexico','NM'), array('New York','NY'), array('North Carolina','NC'), array('North Dakota','ND'), array('Ohio','OH'), array('Oklahoma','OK'), array('Oregon','OR'), array('Pennsylvania','PA'), array('Rhode Island','RI'), array('South Carolina','SC'), array('South Dakota','SD'), array('Tennessee','TN'), array('Texas','TX'), array('Utah','UT'), array('Vermont','VT'), array('Virginia','VA'), array('Washington','WA'), array('Washington, D.C.','DC'), array('West Virginia','WV'), array('Wisconsin','WI'), array('Wyoming','WY'));
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
			<label for="EventAreaCode">Telephone Area Code</label>&nbsp;&nbsp;<a name="EventAreaCodeAnchor" class="plain"><input type="text" name="EventAreaCode" id="EventAreaCode" maxlength="3" size="3" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkEventAreaCodeOnly();"></a><span class="redsup">&nbsp;&nbsp;<b>*</b></span>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<label>Zip Code</label>&nbsp;&nbsp;<a name="EventZipCodeAnchor" class="plain"><input type="text" name="EventZipCode" id="EventZipCode" maxlength="5" size="5" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkEventZipCodeOnly();"></a><span class="redsup">&nbsp;&nbsp;<b>*</b></span>
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
			<label>$</label>&nbsp;<input type="text" name="CostStd" id="CostStd" maxlength="8" size="6" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkCostStdOnly();"><span class="redsup">&nbsp;&nbsp;<b>*</b></span>
			<div class="error" id="CostStdError"><br>Enter the cost or fee ($) for this training. Use only digits e.g. 1045 for $1045.<br></div>
			<?php if ($_SESSION['MsgCostStd'] != null) { echo $_SESSION['MsgCostStd']; $_SESSION['MsgCostStd']=null; } ?>
			</td>
			</tr>
			<tr height="40">
			<td align="left" valign="top"><label for="EarlyBird" style="position:relative; top: 16px;">Early Bird</label></td>
			<td align="left">
			<a name="EarlyBirdAvailable" class="plain"></a><input type="checkbox" name="EarlyBirdAvailable" id="EarlyBirdAvailable" value="true" onChange="if (this.checked) { document.getElementById('CostEbird').disabled = false; document.getElementById('EbirdDeadline').disabled = false; document.getElementById('CostEbird').focus(); } else { document.getElementById('CostEbird').disabled = true; document.getElementById('EbirdDeadline').disabled = true; };"><label>&nbsp;&nbsp;Early-Bird Rate Available?</label><br>
			</td>
			<td colspan="2"><label>$</label>&nbsp;<input type="text" name="CostEbird" id="CostEbird" maxlength="8" size="4" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkCostEbirdOnly();" disabled>
			&nbsp;&nbsp;<label>Early-Bird Rate</label>
			&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="EbirdDeadline" id="EbirdDeadline" maxlength="10" size="10" value="<?=date('m/d/Y');?>" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkEbirdDeadlineOnly();" disabled>
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
			<td colspan="3" valign="top" align="left"><a name="EventOverviewAnchor" class="plain"><textarea name="EventOverview" id="EventOverview" rows="8" cols="80" wrap="soft" style="overflow:auto; height: 180px; width: 540px;" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkEventOverviewOnly('EventOverview', 'EventOverviewError', 'EventOverviewAnchor');" onKeyUp="charCount('EventOverview','sBann1EventOverview','{CHAR} chars'); toCount('EventOverview','sBann2EventOverview','{CHAR} chars',600);"></textarea></a>
			<div class="greytextsmall">Single paragraph. Maximum: 600 characters. Count: [<span id="sBann1EventOverview" class="greytextsmall" style="font-weight:bold;">0 chars</span>]&nbsp;&nbsp;Remaining: [<span id="sBann2EventOverview" class="greytextsmall" style="font-weight:bold;">600 chars</span>]</div>
			<div class="error" id="EventOverviewError">Please remove any newline characters ('return' or 'enter'). To remove them, use the [delete] or [backspace] keys.<br>Also, please ensure your text is less than 600 characters (approx. 100 words) in length.<br></div><?php if ($_SESSION['MsgEventOverview'] != null) { echo $_SESSION['MsgEventOverview']; $_SESSION['MsgEventOverview']=null; };?></td>
			</tr>
			<tr height="20">
			<td height="40" align="left"><label for="RegContact">Registration Contact</label></td>
			<td align="left" width="175"><a name="RegContactAnchor" class="plain"><input type="text" name="RegContact" id="RegContact" maxlength="30" size="20" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkRegContactOnly();"></a></td>
			<td align="right" colspan="2">
			<label for="RegTel1">Telephone</label>&nbsp;&nbsp;<a name="RegTel1Anchor" class="plain"><input type="text" name="RegTel1" id="RegTel1" maxlength="3" size="3" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white';"></a>&nbsp;&nbsp;&nbsp;<input type="text" name="RegTel2" id="RegTel2" maxlength="3" size="3" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white';">&nbsp;&ndash;&nbsp;<input type="text" name="RegTel3" id="RegTel3" maxlength="4" size="4" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkRegTelOnly();">
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
			<td align="left" colspan="2"><a name="RegURLAnchor" class="plain"><input type="text" name="RegURL" id="RegURL" maxlength="150" size="30" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkRegURLOnly();"></a>
			<div class="greytextsmall">Example: www.janedoemediation.com/april_workshop.htm</div>
			<div class="error" id="RegURLError">Please check the format of your web address.<br></div><?php if ($_SESSION['MsgRegURL'] != null) { echo $_SESSION['MsgRegURL']; $_SESSION['MsgRegURL']=null; } ?>
			</td>
			<td align="right"><label for="RegEmail">Email</label>&nbsp;&nbsp;<a name="RegEmailAnchor" class="plain"><input type="text" name="RegEmail" id="RegEmail" maxlength="60" size="24" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkRegEmailOnly();"></a>
			<div class="greytextsmall" style="text-align: right;">Example: jane@janedoemediation.com</div>
			<div class="error" id="RegEmailError">Please check your format. Use only alphanumerics (A-Z, a-z, 0-9), dash (-), period (.), @, and underscore (_) characters.<br></div><?php if ($_SESSION['MsgRegEmail'] != null) { echo $_SESSION['MsgRegEmail']; $_SESSION['MsgRegEmail']=null; } ?>
			</td>
			<tr height="20">
			<td height="60" align="left" valign="bottom"><label for="CertBody" style="position: relative; bottom: 18px;">Certification Body</label></td>
			<td align="left" colspan="3" valign="bottom">
			<a name="CertBodyAnchor" class="plain"></a>
			<input type="text" name="CertBody" id="CertBody" maxlength="100" size="60" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkCertBodyOnly();">
			<div class="greytextsmall" style="position: relative; bottom: 0px;">Example: American Arbitration Association (or leave blank if none).</div><div class="error" id="CertBodyError">Please use only alphanumerics (A-Z, a-z, 0-9), dash (-), slash (/), period (.), apostrophe ('), &, and space characters.<br></div>
			<?php if ($_SESSION['MsgCertBody'] != null) { echo $_SESSION['MsgCertBody']; $_SESSION['MsgCertBody']=null; } ?>
			</td>
			</tr>
			<tr valign="bottom" height="50">
			<td colspan="4" align="center"><input type="submit" name="AdminAddEvent" class="buttonstyle" style="margin-left: 0px;" value="Add Event" onClick="return checkForm();"></td>
			</tr>
			</table>
			
			</form>

			</div>

			<?php
		}
	else
		{
		// Authentication is denied.
		echo "<p class='basictext' style='position: absolute; left: 150px; margin-right: 50px; margin-top: 180px; font-size: 14px;'>Authentication is denied. Use your browser&rsquo;s Back button or ";
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