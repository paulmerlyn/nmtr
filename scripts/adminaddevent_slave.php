<?php
/* 
This script is the slave processor for adminaddevent.php and adminaddeventA.php, which allow the administrator to designate a trainer (in adminaddevent.php) and then add an event for that trainer (in adminaddeventA.php). It effects changes to the events_table. 
*/
// Start a session
session_start();
ob_start(); // Used in conjunction with ob_flush() [see www.tutorialized.com/view/tutorial/PHP-redirect-page/27316], this allows me to postpone issuing output to the screen until after the header has been sent.
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Processing Slave Script for adminaddevent.php and adminaddeventA.php</title>
<link href="/mediationtraining.css" rel="stylesheet" type="text/css">
</head>

<body>
<?php
// Create short variable names
$EntityName = $_POST['EntityName'];
$City = $_POST['City'];
$State = $_POST['State'];
$TrainerID = $_POST['TrainerID'];
$ShowAllTrainers = $_POST['ShowAllTrainers'];
$FindTrainer= $_POST['FindTrainer'];

$AdminAddEvent = $_POST['AdminAddEvent'];
$BasicMed = $_POST['BasicMed'];
$WorkplaceMed = $_POST['WorkplaceMed'];
$DivorceMed = $_POST['DivorceMed'];
$FamilyMed = $_POST['FamilyMed'];
$MaritalMed = $_POST['MaritalMed'];
$ElderMed = $_POST['ElderMed'];
$OtherType = $_POST['OtherType'];
$StartDate = $_POST['StartDate'];
$EndDate = $_POST['EndDate'];
$NofHours = $_POST['NofHours'];
$NofDays = $_POST['NofDays'];
$EventType = $_POST['EventType'];
$EventCity = $_POST['EventCity'];
$EventState = $_POST['EventState'];
$EventAreaCode = $_POST['EventAreaCode'];
$EventZipCode = $_POST['EventZipCode'];
$CostStd = $_POST['CostStd'];
$EarlyBirdAvailable = $_POST['EarlyBirdAvailable'];
$CostEbird = $_POST['CostEbird'];
$EbirdDeadline = $_POST['EbirdDeadline'];
$EventOverview = $_POST['EventOverview'];
$RegContact = $_POST['RegContact'];
$RegTel1 = $_POST['RegTel1'];
$RegTel2 = $_POST['RegTel2'];
$RegTel3 = $_POST['RegTel3'];
$RegURL = $_POST['RegURL'];
$RegEmail = $_POST['RegEmail'];
$CertBody = $_POST['CertBody'];


/*
Begin PHP form validation.
*/

if (isset($AdminAddEvent)) 
{

// Create a session variable for the PHP form validation flag, and initialize it to 'false' i.e. assume it's valid.
$_SESSION['phpinvalidflag'] = false;

// Create session variables to hold inline error messages, and initialize them to blank.
$_SESSION['MsgTrainingType'] = null;
$_SESSION['MsgStartDate'] = null;
$_SESSION['MsgEndDate'] = null;
$_SESSION['MsgNofHours'] = null;
$_SESSION['MsgNofDays'] = null;
$_SESSION['MsgEventCity'] = null;
$_SESSION['MsgEventState'] = null;
$_SESSION['MsgEventAreaCode'] = null;
$_SESSION['MsgEventZipCode'] = null;
$_SESSION['MsgCostStd'] = null;
$_SESSION['MsgEarlyBird'] = null;
$_SESSION['MsgEventOverview'] = null;
$_SESSION['MsgRegContact'] = null;
$_SESSION['MsgRegTel'] = null;
$_SESSION['MsgRegURL'] = null;
$_SESSION['MsgRegEmail'] = null;
$_SESSION['MsgCertBody'] = null;

// Seek to validate Training Type checkboxes and text field (ensure either that at least one check-box is checked or that the text field is non-blank). Invoke an invalid flag if either (i) all check-boxes and the text field are blank, or (ii) the text-field is non-blank and at least one check-box is checked. The latter is an erroneous/invalid situation, only achievable if the user disables Javascript.
if (((is_null($BasicMed) && is_null($DivorceMed) && is_null($FamilyMed) && is_null($WorkplaceMed) && is_null($MaritalMed) && is_null($ElderMed)) && $OtherType == '') || ((!is_null($BasicMed) || !is_null($DivorceMed) || !is_null($FamilyMed) || !is_null($WorkplaceMed) || !is_null($MaritalMed) || !is_null($ElderMed)) && $OtherType != ''))
	{
	$_SESSION['MsgTrainingType'] = '<span class="errorphp">Please select at least one check-box or fill in the &lsquo;Other&rsquo; text field.<br></span>';
	$_SESSION['phpinvalidflag'] = true; 
	}

// Seek to validate $StartDate
$illegalCharSet = '/[^0-9\/]+/'; // Reject everything that contains one or more characters that is neither a slash (/) nor a digit. Note the need to escape the slash. 
$reqdCharSet = '[0-1][0-9]\/[0-3][0-9]\/20[0-9]{2}';  // Required format is MM/DD/YYYY. (Note my choice to use ereg for reqdCharSet (less confusing re slashes than using preg_match.)
if (preg_match($illegalCharSet, $StartDate) || !ereg($reqdCharSet, $StartDate))
	{
		$_SESSION['MsgStartDate'] = '<span class="errorphp"><br>Date format MM/DD/YYYY.<br></span>';
		$_SESSION['phpinvalidflag'] = true; 
	}

// Seek to validate $EndDate
$illegalCharSet = '/[^0-9\/]+/'; // Reject everything that contains one or more characters that is neither a slash (/) nor a digit. Note the need to escape the slash. 
$reqdCharSet = '[0-1][0-9]\/[0-3][0-9]\/20[0-9]{2}';  // Required format is MM/DD/YYYY. (Note my choice to use ereg for reqdCharSet (less confusing re slashes than using preg_match.)
if (preg_match($illegalCharSet, $EndDate) || !ereg($reqdCharSet, $EndDate))
	{
		$_SESSION['MsgStartDate'] = '<span class="errorphp"><br>Date must have format MM/DD/YYYY.<br></span>';
		$_SESSION['phpinvalidflag'] = true; 
	}

// Seek to validate $NofHours
$illegalCharSet = '/[^0-9\.]+/'; // Reject everything that contains one or more characters that is neither a period nor a digit.
$reqdCharSet = '/[0-9]+/';  // At least one numeric.
if (preg_match($illegalCharSet, $NofHours) || !preg_match($reqdCharSet, $NofHours))
	{
		$_SESSION['MsgNofHours'] = '<span class="errorphp">Specify a number of hours. Use only digits e.g. 32 for 32 hours.<br></span>';
		$_SESSION['phpinvalidflag'] = true; 
	}
	
// Seek to validate $NofDays
$illegalCharSet = '/[^0-9\.]+/'; // Reject everything that contains one or more characters that is neither a period nor a digit.
$reqdCharSet = '/[0-9]+/';  // At least one numeric.
if (preg_match($illegalCharSet, $NofDays) || !preg_match($reqdCharSet, $NofDays))
	{
		$_SESSION['MsgNofDays'] = '<span class="errorphp">Specify a number of hours. Use only digits e.g. 32 for 32 hours.<br></span>';
		$_SESSION['phpinvalidflag'] = true; 
	}
	
// Seek to validate $EventCity (but only bother if EventType == 'classroom')
$illegalCharSet = '[0-9~%\^\*_@\+`\|\$:";<>\?#!=]+'; // Exclude everything except A-Z, a-z, period, hyphen, apostrophe, &, slash, space, comma, and parentheses.
$reqdCharSet = "^[A-Z][a-z'\.]+[(-|\/| )]*[A-Za-z,-\.\/\(\) ]+";  // Names of form initial capital (e.g. San Jose-Gilroy) followed by potentially a period (e.g. N. Chicago) or lower case. May include dashes, slashes, or spaces. Also supports names like D'Angelo.
if ($EventType == 'classroom')
	{
	if (ereg($illegalCharSet, $EventCity) || !ereg($reqdCharSet, $EventCity))
		{
		$_SESSION['MsgEventCity'] = "<span class='errorphp'>Please enter a valid city. Use only letters (A-Z, a-z), dash (-), apostrophe ('), and space characters here.<br>Use initial capital (upper-case) letters. Examples: <i>Springfield</i> or <i>South Bend</i>.<br></span>";
		$_SESSION['phpinvalidflag'] = true; 
		};
	}

// Seek to validate $EventState (but only bother if EventType == 'classroom')
if ($EventType == 'classroom')
	{
	if (is_null($EventState) || $EventState == '') // Test for either null or '' (blank). The State field gets assigned a null value in updateprofile.php, but the value deposited into the POST array seems to equate to '' rather than null.
		{
		$_SESSION['MsgEventState'] = '<span class="errorphp">Please select a state from the drop-down menu.<br></span>';
		$_SESSION['phpinvalidflag'] = true; 
		};
	}
	
// Seek to validate $EventAreaCode (but only bother if EventType == 'classroom')
$illegalCharSet = '[A-Za-z~%@\^\*_\+`\|\$:";<>\?\.#&+=!,\(\)\-]+'; // Exclude everything except numbers.
$reqdCharSet = '[[:digit:]]{3}';  // Five numerics.
if ($EventType == 'classroom')
	{
	if (ereg($illegalCharSet, $EventAreaCode) || !ereg($reqdCharSet, $EventAreaCode))
		{
		$_SESSION['MsgEventAreaCode'] = '<span class="errorphp">Please enter the 3-digit telephone area code for the location of your training event.<br></span>';
		$_SESSION['phpinvalidflag'] = true; 
		};
	}

// Seek to validate $EventZipCode (but only bother if EventType == 'classroom')
$illegalCharSet = '[A-Za-z~%@\^\*_\+`\|\$:";<>\?\.#&+=!,\(\)\-]+'; // Exclude everything except numbers.
$reqdCharSet = '[[:digit:]]{5}';  // Five numerics.
if ($EventType == 'classroom')
	{
	if (ereg($illegalCharSet, $EventZipCode) || !ereg($reqdCharSet, $EventZipCode))
		{
		$_SESSION['MsgEventZipCode'] = '<span class="errorphp">Please enter the 5-digit zip code for the location of your training event.<br></span>';
		$_SESSION['phpinvalidflag'] = true; 
		};
	};
	
// Seek to validate $CostStd
$illegalCharSet = '/[^0-9\.]+/'; // Reject everything that contains one or more characters that is neither a period nor a digit.
$reqdCharSet = '/[0-9]+/';  // At least one numeric.
if (preg_match($illegalCharSet, $CostStd) || !preg_match($reqdCharSet, $CostStd))
	{
		$_SESSION['MsgCostStd'] = '<span class="errorphp"><br>Enter the cost or fee ($) for this training. Use only digits e.g. 1045 for $1045.<br></span>';
		$_SESSION['phpinvalidflag'] = true; 
	}
	
// Seek to validate $CostEbird(but only bother if the EarlyBirdAvailable check-box is checked)
$illegalCharSet = '/[^0-9\.]+/'; // Reject everything that contains one or more characters that is neither a period nor a digit.
$reqdCharSet = '/[0-9]+/';  // At least one numeric.
if (!is_null($EarlyBirdAvailable))
	{
	if (preg_match($illegalCharSet, $CostEbird) || !preg_match($reqdCharSet, $CostEbird))
		{
			$_SESSION['MsgCostEbird'] = '<span class="errorphp">Please enter the &ldquo;early-bird&rdquo; fee ($ cost) of this event. Also, click the calendar icon to select the last date (deadline) for registering at this rate.<br></span>';
			$_SESSION['phpinvalidflag'] = true; 
		}
	}
	
// Seek to validate $EbirdDeadline (but only bother if the EarlyBirdAvailable check-box is checked)
$illegalCharSet = '/[^0-9\/]+/'; // Reject everything that contains one or more characters that is neither a slash (/) nor a digit. Note the need to escape the slash. 
$reqdCharSet = '[0-1][0-9]\/[0-3][0-9]\/20[0-9]{2}';  // Required format is MM/DD/YYYY. (Note my choice to use ereg for reqdCharSet (less confusing re slashes than using preg_match.)
if (!is_null($EarlyBirdAvailable))
	{
	if (preg_match($illegalCharSet, $EbirdDeadline) || !ereg($reqdCharSet, $EbirdDeadline))
		{
			$_SESSION['MsgCostEbird'] = '<span class="errorphp">Please enter the &ldquo;early-bird&rdquo; fee ($ cost) of this event. Also, click the calendar icon to select the last date (deadline) for registering at this rate.<br></span>';
			$_SESSION['phpinvalidflag'] = true; 
		}
	}

// Seek to validate $EventOverview
$illegalCharSet = '/[\r\n\t\f\v]/'; // Reject special characters return, newline, tab, form feed, vertical tab.
if ($EventOverview != '') // Since it's an optional field, only bother to validate if it's not empty.
	{
	if (preg_match($illegalCharSet, $EventOverview) || strlen($EventOverview) > 600)
		{
		$_SESSION['MsgEventOverview'] = '<span class="errorphp">Please remove any newline characters (\'return\' or \'enter\'). To remove them, use the [delete] or [backspace] keys.<br>Also, please ensure your text is less than 600 characters (approx. 100 words) in length.<br></span>';
		$_SESSION['phpinvalidflag'] = true; 
		};
	}

// Seek to validate $RegContact
$illegalCharSet = '[~#%\^\*_\+`\|:;<>0-9\$]+'; // Exclude everything except A-Z, a-z, double quote, period, hyphen, apostrophe (single quote), &, ?, =, !, slash, space, comma, and parentheses.
if ($RegContact != '') // Since it's an optional field, only bother to validate if it's not empty.
	{
	if (ereg($illegalCharSet, $RegContact))
		{
		$_SESSION['MsgRegContact'] = "<span class='errorphp'>Enter the name of a contact person for registration of this event, or leave this field blank.<br></span>";
		$_SESSION['phpinvalidflag'] = true; 
		};
	}

// Seek to validate Telephone
$illegalCharSet = '[A-Za-z~%\^\*_\+`\|\$:";<>\?\.#&+=!,\(\)-]+'; // Exclude everything except numbers.
$reqdCharSet = '[[:digit:]]{3,}';  // At least three numerics.
if ($RegTel1 != '' || $RegTel2 != '' || $RegTel3 != '') // Since it's an optional field, only bother to validate if it's not empty.
	{
	if (strlen($RegTel1) < 3 || strlen($RegTel2) < 3 || strlen($RegTel3) < 4 || ereg($illegalCharSet, $RegTel1) || ereg($illegalCharSet, $RegTel2) || ereg($illegalCharSet, $RegTel3) || !ereg($reqdCharSet, $RegTel1) || !ereg($reqdCharSet, $RegTel2) || !ereg($reqdCharSet, $RegTel3))
		{
		$_SESSION['MsgRegTel'] = '<span class="errorphp"><br>Leave blank or enter a valid phone number. Use only digits (0-9).<br></span>';
		$_SESSION['phpinvalidflag'] = true; 
		}
	}

// Seek to validate $RegURL
$reqdCharSet = '^(http:\/\/|https:\/\/)?(([[:alnum:]]|\-)+\.)+(com|edu|org|us|net|biz|gov|mobi|mil|ca|uk|hk|asia|tv|jobs|cc|co)+';
if ($RegURL != '') // Since it's an optional field, only bother to validate if it's not empty.
	{
		if (!ereg($reqdCharSet, $RegURL))
		{
		$_SESSION['MsgRegURL'] = '<span class="errorphp">Please check the format of your web address.<br></span>';
		$_SESSION['phpinvalidflag'] = true; 
		};
	}

// Seek to validate $RegEmail
$reqdCharSet = '^[A-Za-z0-9_\-\.]+@[a-zA-Z0-9_\-]+\.[a-zA-Z0-9_\-\.]+$';  // Simple validation from Welling/Thomson book, p125.
if ($RegEmail != '') // Since it's an optional field, only bother to validate if it's not empty.
	{
	if (!ereg($reqdCharSet, $RegEmail))
		{
		$_SESSION['MsgRegEmail'] = '<span class="errorphp">Please check your format. Use only alphanumerics (A-Z, a-z, 0-9), dash (-), period (.), @, and underscore (_) characters.<br></span>';
		$_SESSION['phpinvalidflag'] = true; 
		};
	}

// Seek to validate $CertBody
$illegalCharSet = '[~#%\^\*_\+`\|:;<>\$]+'; // Exclude everything except A-Z, a-z, double quote, period, hyphen, apostrophe (single quote), &, ?, =, !, slash, space, comma, and parentheses.
if ($CertBody != '') // Since it's an optional field, only bother to validate if it's not empty.
	{
	if (ereg($illegalCharSet, $CertBody))
		{
		$_SESSION['MsgCertBody'] = "<span class='errorphp'>Please use only alphanumerics (A-Z, a-z, 0-9), dash (-), slash (/), period (.), apostrophe ('), &, and space characters.<br></span>";
		$_SESSION['phpinvalidflag'] = true; 
		}
	}

//Now go back to the previous page (adminaddevent.php) and show any PHP inline validation error messages if the $_SESSION['phpinvalidflag'] has been set to true. ... otherwise, proceed to update the database with the user's form data.
if ($_SESSION['phpinvalidflag'])
	{
	?>
	<script type='text/javascript' language='javascript'><!-- history.back(); //--></script>
	<noscript>
	<?php
	if (isset($_SERVER['HTTP_REFERER']))
		header("Location: ".$_SERVER['HTTP_REFERER']); // Go back to previous page. (Similar to echoing the Javascript statement: history.go(-1) or history.back() except I think $_SERVER['HTTP_REFERER'] reloads the page, which causes freshly entered values in the adminaddtrainer.php form to get overwritten by values retrieved from the DB. So the javascript 'history.back()' method is more suitable. However, if Javascript is enabled, php form validation is moot. And if Javascript is disabled, then the javascript 'history.back()' method won't work anyway.
	?>
	</noscript>
	<?php
	ob_flush();
	exit;
	}

} // End of PHP form validation

/* Prevent cross-site scripting via htmlspecialchars on these user-entry form field. Note my decision to do this AFTER PHP form validation because I want to validate the actual characters entered by the user, before any conversions into their respective character codes e.g. before an ampersand & gets converted in "&amp;". */
$OtherType = htmlspecialchars($OtherType, ENT_COMPAT);
$StartDate = htmlspecialchars($StartDate, ENT_COMPAT);
$EndDate = htmlspecialchars($EndDate, ENT_COMPAT);
$NofHours = htmlspecialchars($NofHours, ENT_COMPAT);
$NofDays = htmlspecialchars($NofDays, ENT_COMPAT);
$EventCity = htmlspecialchars($EventCity, ENT_COMPAT);
$EventState = htmlspecialchars($EventState, ENT_COMPAT);
$EventAreaCode = htmlspecialchars($EventAreaCode, ENT_COMPAT);
$EventZipCode = htmlspecialchars($EventZipCode, ENT_COMPAT);
$CostStd = htmlspecialchars($CostStd, ENT_COMPAT);
$EbirdDeadline = htmlspecialchars($EbirdDeadline, ENT_COMPAT);
$EventOverview = htmlspecialchars($EventOverview, ENT_COMPAT);
$RegContact = htmlspecialchars($RegContact, ENT_COMPAT);
$RegTel1 = htmlspecialchars($RegTel1, ENT_COMPAT);
$RegTel2 = htmlspecialchars($RegTel2, ENT_COMPAT);
$RegTel3 = htmlspecialchars($RegTel3, ENT_COMPAT);
$RegURL = htmlspecialchars($RegURL, ENT_COMPAT);
$RegEmail = htmlspecialchars($RegEmail, ENT_COMPAT);
$CertBody = htmlspecialchars($CertBody, ENT_COMPAT);

$db = mysql_connect('localhost', 'paulme6_merlyn', 'fePhaCj64mkik')
or die('Could not connect: ' . mysql_error());
mysql_select_db('paulme6_medtrainers') or die('Could not select database: ' . mysql_error());

if (!isset($AdminAddEvent)) // By-pass if the 'AdminAddEvent' button was clicked in adminaddeventA.php.
{
	if (isset($FindTrainer) || isset($ShowAllTrainers)) // User clicked the 'Find Trainer' button in adminaddevent.php or the 'Show All Trainers' check box in adminaddevent.php. This code should return with details of all trainer(s) who match the user-supplied search criteria.
	{
	// Retrieve all rows from trainers_table that match the search criteria and place them in resource $row.
	$_SESSION['NoWhereClause'] = 1; // Initialize to true. Its value will remain true iff the user left all search parameters blank in adminaddevent.php, in which case this session variable can trigger a notification alert when control passes back to adminaddevent.php.
	if ($ShowAllTrainers == 1) $query = "select TrainerID from trainers_table"; // If 'Show All' check-box was checked in adminaddevent.php the query is to retrieve every TrainerID ... 
	else 
		{
		$query = "select TrainerID from trainers_table where"; // ... else just retrieve IDs that match the user-specified criteria.
		if ($EntityName != '') { $query .= " AND EntityName like '%$EntityName%'"; $_SESSION['NoWhereClause'] = 0; }
		if ($City != '') { $query .= " AND City like '$City%'"; $_SESSION['NoWhereClause'] = 0; }
		if ($State != null) { $query .= " AND State = '$State'"; $_SESSION['NoWhereClause'] = 0; }
		if ($TrainerID != '') { $query .= " AND TrainerID = '$TrainerID'"; $_SESSION['NoWhereClause'] = 0; }
		$query = preg_replace('/\sAND\s/',' ',$query, 1); // Remove the first instance of ' AND ' b/c it screws up the query syntax.
		}
	$_SESSION['TrainerIDmatchesArray'] = null; // Initialize this to empty.
	if (!$_SESSION['NoWhereClause'] || $ShowAllTrainers == 1) // Only perform the mysql query if there is at least one 'where clause' (i.e. if the user entered at least one non-blank item in adminaddevent.php) or the 'ShowAllTrainers' checkbox was checked.
		{
		$result = mysql_query($query) or die('Query (select of trainers who match search criteria) failed: ' . mysql_error());
		$i = 0;
		while ($row = mysql_fetch_assoc($result))
			{
			$TrainerIDmatchesArray[$i] = $row['TrainerID'];
			$i += 1;
			}
		$_SESSION['TrainerIDmatchesArray'] = $TrainerIDmatchesArray; // Store the array of IDs that match the search criteria in a session variable so they can be available for use when adminaddevent_slave.php loads adminaddevent.php.
		}
	$_SESSION['FindTrainerReturn'] = 'true'; // Use to control execution on return of control back to adminaddevent.php.
	unset($FindTrainer);
	unset($ShowAllTrainers);
	};
	
// Now go back to adminaddevent.php via either the HTTP_REFERER or	via javascript history.back
?>
<script language="javascript" type="text/javascript">
history.back();
</script>
<noscript>
<?php
if (isset($_SERVER['HTTP_REFERER'])) // Antivirus software sometimes blocks transmission of HTTP_REFERER.
	{
	header("Location: /adminaddevent.php"); // Go back to previous page. (Alternative to echoing the Javascript statement: history.go(-1) or history.back() in cases where user has Javascript disabled.
	}
	ob_flush();
?>
</noscript>
<?php
exit;
}

if (isset($AdminAddEvent))
{
/*
Manipulate Training Type check-boxes BasicMed, DivorceMed, FamilyMed, WorkplaceMed, MaritalMed, and ElderMed and the 'OtherType' text field.
*/
if ($BasicMed) $TrainingType = 0;
else if ($DivorceMed) $TrainingType = 1;
else if ($FamilyMed) $TrainingType = 2; 
else if ($WorkplaceMed) $TrainingType = 3;
else if ($MaritalMed) $TrainingType = 4;
else if ($ElderMed) $TrainingType = 5;
else if ($OtherType != '' && !is_null($OtherType)) $TrainingType = $OtherType;
else $TrainingType = 'undefined';

/*
Manipulate the $StartDate and $EndDate from HTML form format into the 'YYYY-MM-DD' MySQL format.
*/
$datearray = explode('/', $StartDate);
$StartDate = $datearray[2].'-'.$datearray[0].'-'.$datearray[1];

$datearray = explode('/', $EndDate);
$EndDate = $datearray[2].'-'.$datearray[0].'-'.$datearray[1];

/*
Manipulate the event type data i.e. $EventType, $EventCity, and $EventState. Only use the latter two if $EventType == 'classroom'.
*/
if ($EventType != 'classroom') { $EventCity = ''; $EventState = ''; };

/*
Manipulate the early bird data i.e. $EarlyBirdAvailable, $CostEbird and $EbirdDeadline. Only use these if $EarlyBirdAvailable == true.
*/
if (!$EarlyBirdAvailable)
	{
	$EarlyBirdAvailable = 0;
	$CostEbird = '';
	$EbirdDeadline = NULL;
	}
else
	{
	$EarlyBirdAvailable = 1;
	$datearray = explode('/', $EbirdDeadline);
	$EbirdDeadline = $datearray[2].'-'.$datearray[0].'-'.$datearray[1];
	}

/*
Use PHP's trim function to remove white space from beginning and end of $EventOverview...
*/
$EventOverview = trim($EventOverview); // ... then remove any remaining line breaks from $Overview before inserting it into DB. Note the need for 'if' statements b/c strpos() returns a value of 'false' if the needle (\r or \n) is not in the haystack ($EventOverview).
if (strpos($EventOverview, "\r") !== false) $posr = strpos($EventOverview, "\r"); else $posr = strlen($EventOverview); // If the \r character is in the string then identify its position, else set $posr to a safe maximum such as the length of the string.
if (strpos($EventOverview, "\n") !== false) $posn = strpos($EventOverview, "\n"); else $posn = strlen($EventOverview); // Similarly for \n.
$pos = min($posr, $posn);  // Identify which character occurs first 
$EventOverview = substr($EventOverview,0,$pos); // Retain only that portion of $EventOverview from the beginning to the first occurence of either a \r or a \n.

/*
Manipulate user submission into $RegTel1,2,3 so they comport with following format for storage in database: 415.378.7003.
*/
$RegTel = $RegTel1.'.'.$RegTel2.'.'.$RegTel3;
if ($RegTel == '..') $RegTel = ''; // Tidy up what will get inserted into the events_table in case of a blank phone number.

/*
Manipulate RegURL, using PHP's string functions to prepend 'http://' if $RegURL is non-blank and doesn't already begin with either 'http://' or 'https://'.
*/
if ($RegURL != '' AND !strstr($RegURL, 'http://') AND !strstr($RegURL, 'https://')) $RegURL = 'http://'.$RegURL;

if (!get_magic_quotes_gpc())
{
	$TrainingType = addslashes($TrainingType);
	$StartDate = addslashes($StartDate);
	$EndDate = addslashes($EndDate);
	$NofHours = addslashes($NofHours);
	$NofDays = addslashes($NofDays);
	$EventCity = addslashes($EventCity);
	$EventAreaCode = addslashes($EventAreaCode);
	$EventZipCode = addslashes($EventZipCode);
	$CostStd = addslashes($CostStd);
	$CostEbird= addslashes($CostEbird);
	$EbirdDeadline = addslashes($EbirdDeadline);
	$EventOverview = addslashes($EventOverview);
	$RegContact = addslashes($RegContact);
	$RegTel = addslashes($RegTel);
	$RegURL = addslashes($RegURL);
	$RegEmail = addslashes($RegEmail);
	$CertBody = addslashes($CertBody);
}	

// Formulate the query to insert the event details into the events_table for the trainer whose ID is $_SESSION['selectedID'].
$query = "INSERT INTO events_table set TrainerID = '".$_SESSION['selectedID']."', StartDate = '".$StartDate."', EndDate = '".$EndDate."', TrainingType = '".$TrainingType."', NofHours = '".$NofHours."', NofDays = '".$NofDays."', EventType = '".$EventType."', EventCity = '".$EventCity."', EventState = '".$EventState."', EventAreaCode = '".$EventAreaCode."', EventZipCode = '".$EventZipCode."', CostStd = '".$CostStd."', EbirdAvailable = '".$EarlyBirdAvailable."', CostEbird = '".$CostEbird."', EbirdDeadline = '".$EbirdDeadline."', EventOverview = '".$EventOverview."', RegContact = '".$RegContact."', RegEmail = '".$RegEmail."', RegTel = '".$RegTel."', RegURL = '".$RegURL."', CertBody = '".$CertBody."'";

$result = mysql_query($query) or die('Query (insert into trainers_table) failed: ' . mysql_error().' and the query string was: '.$query);

if (!$result) 
	{
	echo 'An update of events_table could not be completed.';
	}
else
	{
	// The events_table has been updated.
	unset($AdminAddEvent);
?>

<div style="text-align: center"> <!-- This div provides centering for older browsers incl. NS4 and IE5. (See http://theodorakis.net/tablecentertest.html#intro.) Use of margin-left: auto and margin-right: auto in the style of the table itself (see below) takes care of centering in newer browsers. -->
<table cellpadding="0" cellspacing="0" style="margin-top: 50px; margin-left: auto; margin-right: auto; position: relative; left: -7px;">
<tr>
<td style="text-align: left;">
<p class='basictext' style='margin-left: 50px; margin-right: 50px; margin-top: 40px; font-size: 14px;'>An event has been successfully added to the database for <?=$_SESSION['EntityName']; ?>.</p>
<p class='basictext' style='margin-left: 50px; margin-right: 50px; margin-bottom: 40px; font-size: 14px;'>Please click <a target='_self' style='font-size: 14px;' href='/index.php'>here</a> to go to the mediationtrainings.org home page. Or click <a style='font-size: 14px;' href='/adminaddevent.php'>here</a> to add another event.</p>
</td>
<tr>
<td style="text-align: center;">
<form method="post" action="/adminaddevent.php">
<input type='submit' name='Continue' class='buttonstyle' style="text-align: center;" value='Continue'>
</form>
</td>
</tr>
</table>
</div>

<?php
	}

}
ob_end_flush();
?>
</body>
</html>