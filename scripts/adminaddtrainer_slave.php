<?php
// Start a session
session_start();

ob_start(); // Used in conjunction with ob_flush() [see www.tutorialized.com/view/tutorial/PHP-redirect-page/27316], this allows me to postpone issuing output to the screen until after the header has been sent.

// Create short variable names
$AdminAddTrainer = $_POST['AdminAddTrainer'];
$EntityName = $_POST['EntityName'];
$StreetAddress = $_POST['StreetAddress'];
$City = $_POST['City'];
$State = $_POST['State'];
$Zip = $_POST['Zip'];
$Tel1 = $_POST['Tel1'];
$Tel2 = $_POST['Tel2'];
$Tel3 = $_POST['Tel3'];
$BasicMed = $_POST['BasicMed'];
$DivorceMed = $_POST['DivorceMed'];
$FamilyMed = $_POST['FamilyMed'];
$WorkplaceMed = $_POST['WorkplaceMed'];
$MaritalMed = $_POST['MaritalMed'];
$OtherMed = $_POST['OtherMed'];
$EntityHomePage = $_POST['EntityHomePage'];
$EntityEmail = $_POST['EntityEmail'];
$Overview = $_POST['Overview'];
$TrainerName1 = $_POST['TrainerName1'];
$Trainer1Bio = $_POST['Trainer1Bio'];
$TrainerName2 = $_POST['TrainerName2'];
$Trainer2Bio = $_POST['Trainer2Bio'];
$TrainerName3 = $_POST['TrainerName3'];
$Trainer3Bio = $_POST['Trainer3Bio'];
$TrainerName4 = $_POST['TrainerName4'];
$Trainer4Bio = $_POST['Trainer4Bio'];
$TrainerName5 = $_POST['TrainerName5'];
$Trainer5Bio = $_POST['Trainer5Bio'];
$TrainerName6 = $_POST['TrainerName6'];
$Trainer6Bio = $_POST['Trainer6Bio'];
$MemberLevel = $_POST['MemberLevel'];

/*
Begin PHP form validation.
*/
if (isset($AdminAddTrainer)) 
{

// Create a session variable for the PHP form validation flag, and initialize it to 'false' i.e. assume it's valid.
$_SESSION['phpinvalidflag'] = false;

// Create session variables to hold inline error messages, and initialize them to blank.
$_SESSION['MsgEntityName'] = null;
$_SESSION['MsgStreetAddress'] = null;
$_SESSION['MsgCity'] = null;
$_SESSION['MsgState'] = null;
$_SESSION['MsgZip'] = null;
$_SESSION['MsgTelephone'] = null;
$_SESSION['MsgTrainingsOffered'] = null;
$_SESSION['MsgEntityHomePage'] = null;
$_SESSION['MsgEntityEmail'] = null;
$_SESSION['MsgOverview'] = null;
$_SESSION['MsgTrainerName1'] = null;
$_SESSION['MsgTrainer1Bio'] = null;
$_SESSION['MsgTrainerName2'] = null;
$_SESSION['MsgTrainer2Bio'] = null;
$_SESSION['MsgTrainerName3'] = null;
$_SESSION['MsgTrainer3Bio'] = null;
$_SESSION['MsgTrainerName4'] = null;
$_SESSION['MsgTrainer4Bio'] = null;
$_SESSION['MsgTrainerName5'] = null;
$_SESSION['MsgTrainer5Bio'] = null;
$_SESSION['MsgTrainerName6'] = null;
$_SESSION['MsgTrainer6Bio'] = null;

// Seek to validate $EntityName
$illegalCharSet = '[~#%\^\*_\+`\|:";<>]+'; // Exclude everything except A-Z, a-z, numbers, period, hyphen, apostrophe (single quote), $, &, ?, =, !, slash, space, comma, period, and parentheses.
$reqdCharSet = "[A-Za-z]{2,}";  // At least two letters
if (ereg($illegalCharSet, $EntityName) || !ereg($reqdCharSet, $EntityName))
	{
	$_SESSION['MsgEntityName'] = "<span class='errorphp'>Please use only alphanumerics (A-Z, a-z, 0-9), dash (-), slash (/), period (.), apostrophe ('), &, and space characters.<br></span>";
	$_SESSION['phpinvalidflag'] = true; 
	};

// Seek to validate $StreetAddress
$illegalCharSet = '[~%\^\*_!@\+`\|\$:";<>\?]+'; // Exclude everything except A-Z, a-z, numbers, period, hyphen, apostrophe (single quote), #, &, =, !, slash, space, comma, and parentheses.
if (ereg($illegalCharSet, $StreetAddress))
	{
	$_SESSION['MsgStreetAddress'] = "<span class='errorphp'><br>Please use only alphanumerics (A-Z, a-z, 0-9), dash (-), pound (#), period (.), apostrophe ('), comma (,) and space.<br>Examples: <i>92 N. Lincoln Way</i> or </i><i>8708 Oak St., Suite #3</i><br></span>";
	$_SESSION['phpinvalidflag'] = true; 
	};

// Seek to validate $City
$illegalCharSet = '[0-9~%\^\*_@\+`\|\$:";<>\?#!=]+'; // Exclude everything except A-Z, a-z, period, hyphen, apostrophe, &, slash, space, comma, and parentheses.
$reqdCharSet = "^[A-Z][a-z'\.]+[(-|\/| )]*[A-Za-z,-\.\/\(\) ]+";  // Names of form initial capital (e.g. San Jose-Gilroy) followed by potentially a period (e.g. N. Chicago) or lower case. May include dashes, slashes, or spaces. Also supports names like D'Angelo.
if (ereg($illegalCharSet, $City) || !ereg($reqdCharSet, $City))
	{
	$_SESSION['MsgCity'] = "<span class='errorphp'>Please enter a valid city. Use only letters (A-Z, a-z), dash (-), apostrophe ('), and space characters here.<br>Use initial capital (upper-case) letters. Examples: <i>Springfield</i> or <i>South Bend</i>.<br></span>";
	$_SESSION['phpinvalidflag'] = true; 
	};

// Seek to validate $State.
if (is_null($State) || $State == '') // Test for either null or '' (blank). The State field gets assigned a null value in updateprofile.php, but the value deposited into the POST array seems to equate to '' rather than null.
	{
	$_SESSION['MsgState'] = '<span class="errorphp">Please select a state from the drop-down menu.<br></span>';
	$_SESSION['phpinvalidflag'] = true; 
	};
	
// Seek to validate $Zip
$illegalCharSet = '[A-Za-z~%@\^\*_\+`\|\$:";<>\?\.#&+=!,\(\)]+'; // Exclude everything except numbers and dash.
$reqdCharSet = '[[:digit:]]{5}';  // Five numerics.
if (ereg($illegalCharSet, $Zip) || !ereg($reqdCharSet, $Zip))
	{
	$_SESSION['MsgZip'] = '<span class="errorphp">Please enter a valid zip code. Use a five-digit format.<br></span>';
	$_SESSION['phpinvalidflag'] = true; 
	};

// Seek to validate Telephone
$illegalCharSet = '[A-Za-z~%\^\*_\+`\|\$:";<>\?\.#&+=!,\(\)-]+'; // Exclude everything except numbers.
$reqdCharSet = '[[:digit:]]{3,}';  // At least three numerics.
if (strlen($Tel1) < 3 || strlen($Tel2) < 3 || strlen($Tel3) < 4 || ereg($illegalCharSet, $Tel1) || ereg($illegalCharSet, $Tel2) || ereg($illegalCharSet, $Tel3) || !ereg($reqdCharSet, $Tel1) || !ereg($reqdCharSet, $Tel2) || !ereg($reqdCharSet, $Tel3))
	{
	$_SESSION['MsgTelephone'] = '<span class="errorphp"><br>Please enter a valid telephone number. Use only numbers (0-9).<br></span>';
	$_SESSION['phpinvalidflag'] = true; 
	}

// Seek to validate trainings offered checkboxes (ensure at least one is checked)
if (is_null($BasicMed) && is_null($DivorceMed) && is_null($FamilyMed) && is_null($WorkplaceMed) && is_null($MaritalMed) && is_null($OtherMed))
	{
	$_SESSION['MsgTrainingsOffered'] = '<span class="errorphp">Please select at least one check-box.<br></span>';
	$_SESSION['phpinvalidflag'] = true; 
	}

// Seek to validate $EntityHomePage
$reqdCharSet = '^(http:\/\/|https:\/\/)?(([[:alnum:]]|\-)+\.)+(com|edu|org|us|net|biz|gov|mobi|mil|ca|uk|hk|asia|tv|jobs|bz|cc|co)+';
if ($EntityHomePage != '') // Since it's an optional field, only bother to validate if it's not empty.
	{
		if (!ereg($reqdCharSet, $EntityHomePage))
		{
		$_SESSION['MsgEntityHomePage'] = '<span class="errorphp">Please check the format of your web address.<br></span>';
		$_SESSION['phpinvalidflag'] = true; 
		};
	}

// Seek to validate $EntityEmail
$reqdCharSet = '^[A-Za-z0-9_\-\.]+@[a-zA-Z0-9_\-]+\.[a-zA-Z0-9_\-\.]+$';  // Simple validation from Welling/Thomson book, p125.
if ($EntityEmail != '') // Since it's an optional field, only bother to validate if it's not empty.
	{
	if (!ereg($reqdCharSet, $EntityEmail))
		{
		$_SESSION['MsgEntityEmail'] = '<span class="errorphp">Please check your format. Use only alphanumerics (A-Z, a-z, 0-9), dash (-), period (.), @, and underscore (_) characters.<br></span>';
		$_SESSION['phpinvalidflag'] = true; 
		};
	}

// Seek to validate $Overview
$illegalCharSet = '/[\r\n\t\f\v]/'; // Reject special characters return, newline, tab, form feed, vertical tab.
if ($Overview != '') // Since it's an optional field, only bother to validate if it's not empty.
	{
	if (preg_match($illegalCharSet, $Overview) || strlen($Overview) > 450)
		{
		$_SESSION['MsgOverview'] = '<span class="errorphp">Please remove any newline characters (\'return\' or \'enter\'). To remove them, use the [delete] or [backspace] keys.<br>Also, please ensure your text is less than 450 characters (approx. 70 words) in length.<br></span>';
		$_SESSION['phpinvalidflag'] = true; 
		};
	}

// Seek to validate $TrainerName1
$illegalCharSet = '[~#%\^\*_\+`\|:;<>0-9\$]+'; // Exclude everything except A-Z, a-z, double quote, period, hyphen, apostrophe (single quote), &, ?, =, !, slash, space, comma, and parentheses.
if ($TrainerName1 != '') // Since it's an optional field, only bother to validate if it's not empty.
	{
	if (ereg($illegalCharSet, $TrainerName1))
		{
		$_SESSION['MsgTrainerName1'] = "<span class='errorphp'>Please use only letters (A-Z, a-z), dash (-), period (.), apostrophe ('), and space characters.<br></span>";
		$_SESSION['phpinvalidflag'] = true; 
		};
	}

// Seek to validate $Trainer1Bio
$illegalCharSet = '/[\r\n\t\f\v]/'; // Reject special characters return, newline, tab, form feed, vertical tab.
if (preg_match($illegalCharSet, $Trainer1Bio) || strlen($Trainer1Bio) > 900)
	{
	$_SESSION['MsgTrainer1Bio'] = '<span class="errorphp">Please remove any newline characters (\'return\' or \'enter\'). To remove them, use the [delete] or [backspace] keys.<br>Also, please ensure your text is less than 900 characters (approx. 130 words) in length.<br></span>';
	$_SESSION['phpinvalidflag'] = true; 
	};

// Seek to validate $TrainerName2
$illegalCharSet = '[~#%\^\*_\+`\|:;<>0-9\$]+'; // Exclude everything except A-Z, a-z, double quote, period, hyphen, apostrophe (single quote), &, ?, =, !, slash, space, comma, and parentheses.
if ($TrainerName2 != '') // Since it's an optional field, only bother to validate if it's not empty.
	{
	if (ereg($illegalCharSet, $TrainerName2))
		{
		$_SESSION['MsgTrainerName2'] = "<span class='errorphp'>Please use only letters (A-Z, a-z), dash (-), period (.), apostrophe ('), and space characters.<br></span>";
		$_SESSION['phpinvalidflag'] = true; 
		};
	}
	
// Seek to validate $Trainer2Bio
$illegalCharSet = '/[\r\n\t\f\v]/'; // Reject special characters return, newline, tab, form feed, vertical tab.
if ($Trainer2Bio != '') // Since it's an optional field, only bother to validate if it's not empty.
	{
	if (preg_match($illegalCharSet, $Trainer2Bio) || strlen($Trainer2Bio) > 450)
		{
		$_SESSION['MsgTrainer2Bio'] = '<span class="errorphp">Please remove any newline characters (\'return\' or \'enter\'). To remove them, use the [delete] or [backspace] keys.<br>Also, please ensure your text is less than 900 characters (approx. 130 words) in length.<br></span>';
		$_SESSION['phpinvalidflag'] = true; 
		};
	}

// Seek to validate $TrainerName3
$illegalCharSet = '[~#%\^\*_\+`\|:;<>0-9\$]+'; // Exclude everything except A-Z, a-z, double quote, period, hyphen, apostrophe (single quote), &, ?, =, !, slash, space, comma, and parentheses.
if ($TrainerName3 != '') // Since it's an optional field, only bother to validate if it's not empty.
	{
	if (ereg($illegalCharSet, $TrainerName3))
		{
		$_SESSION['MsgTrainerName3'] = "<span class='errorphp'>Please use only letters (A-Z, a-z), dash (-), period (.), apostrophe ('), and space characters.<br></span>";
		$_SESSION['phpinvalidflag'] = true; 
		};
	}

// Seek to validate $Trainer3Bio
$illegalCharSet = '/[\r\n\t\f\v]/'; // Reject special characters return, newline, tab, form feed, vertical tab.
if ($Trainer3Bio != '') // Since it's an optional field, only bother to validate if it's not empty.
	{
	if (preg_match($illegalCharSet, $Trainer3Bio) || strlen($Trainer3Bio) > 450)
		{
		$_SESSION['MsgTrainer3Bio'] = '<span class="errorphp">Please remove any newline characters (\'return\' or \'enter\'). To remove them, use the [delete] or [backspace] keys.<br>Also, please ensure your text is less than 900 characters (approx. 130 words) in length.<br></span>';
		$_SESSION['phpinvalidflag'] = true; 
		};
	}
	
// Seek to validate $TrainerName4
$illegalCharSet = '[~#%\^\*_\+`\|:;<>0-9\$]+'; // Exclude everything except A-Z, a-z, double quote, period, hyphen, apostrophe (single quote), &, ?, =, !, slash, space, comma, and parentheses.
if ($TrainerName4 != '') // Since it's an optional field, only bother to validate if it's not empty.
	{
	if (ereg($illegalCharSet, $TrainerName4))
		{
		$_SESSION['MsgTrainerName4'] = "<span class='errorphp'>Please use only letters (A-Z, a-z), dash (-), period (.), apostrophe ('), and space characters.<br></span>";
		$_SESSION['phpinvalidflag'] = true; 
		};
	}

// Seek to validate $Trainer4Bio
$illegalCharSet = '/[\r\n\t\f\v]/'; // Reject special characters return, newline, tab, form feed, vertical tab.
if ($Trainer4Bio != '') // Since it's an optional field, only bother to validate if it's not empty.
	{
	if (preg_match($illegalCharSet, $Trainer4Bio) || strlen($Trainer4Bio) > 450)
		{
		$_SESSION['MsgTrainer4Bio'] = '<span class="errorphp">Please remove any newline characters (\'return\' or \'enter\'). To remove them, use the [delete] or [backspace] keys.<br>Also, please ensure your text is less than 900 characters (approx. 130 words) in length.<br></span>';
		$_SESSION['phpinvalidflag'] = true; 
		};
	}

// Seek to validate $TrainerName5
$illegalCharSet = '[~#%\^\*_\+`\|:;<>0-9\$]+'; // Exclude everything except A-Z, a-z, double quote, period, hyphen, apostrophe (single quote), &, ?, =, !, slash, space, comma, and parentheses.
if ($TrainerName5 != '') // Since it's an optional field, only bother to validate if it's not empty.
	{
	if (ereg($illegalCharSet, $TrainerName5))
		{
		$_SESSION['MsgTrainerName5'] = "<span class='errorphp'>Please use only letters (A-Z, a-z), dash (-), period (.), apostrophe ('), and space characters.<br></span>";
		$_SESSION['phpinvalidflag'] = true; 
		};
	}

// Seek to validate $Trainer5Bio
$illegalCharSet = '/[\r\n\t\f\v]/'; // Reject special characters return, newline, tab, form feed, vertical tab.
if ($Trainer5Bio != '') // Since it's an optional field, only bother to validate if it's not empty.
	{
	if (preg_match($illegalCharSet, $Trainer5Bio) || strlen($Trainer5Bio) > 450)
		{
		$_SESSION['MsgTrainer5Bio'] = '<span class="errorphp">Please remove any newline characters (\'return\' or \'enter\'). To remove them, use the [delete] or [backspace] keys.<br>Also, please ensure your text is less than 900 characters (approx. 130 words) in length.<br></span>';
		$_SESSION['phpinvalidflag'] = true; 
		};
	}

// Seek to validate $TrainerName6
$illegalCharSet = '[~#%\^\*_\+`\|:;<>0-9\$]+'; // Exclude everything except A-Z, a-z, double quote, period, hyphen, apostrophe (single quote), &, ?, =, !, slash, space, comma, and parentheses.
if ($TrainerName6 != '') // Since it's an optional field, only bother to validate if it's not empty.
	{
	if (ereg($illegalCharSet, $TrainerName6))
		{
		$_SESSION['MsgTrainerName6'] = "<span class='errorphp'>Please use only letters (A-Z, a-z), dash (-), period (.), apostrophe ('), and space characters.<br></span>";
		$_SESSION['phpinvalidflag'] = true; 
		};
	}

// Seek to validate $Trainer6Bio
$illegalCharSet = '/[\r\n\t\f\v]/'; // Reject special characters return, newline, tab, form feed, vertical tab.
if ($Trainer6Bio != '') // Since it's an optional field, only bother to validate if it's not empty.
	{
	if (preg_match($illegalCharSet, $Trainer6Bio) || strlen($Trainer6Bio) > 450)
		{
		$_SESSION['MsgTrainer6Bio'] = '<span class="errorphp">Please remove any newline characters (\'return\' or \'enter\'). To remove them, use the [delete] or [backspace] keys.<br>Also, please ensure your text is less than 900 characters (approx. 130 words) in length.<br></span>';
		$_SESSION['phpinvalidflag'] = true; 
		};
	}

//Now go back to the previous page (adminaddtrainer.php) and show any PHP inline validation error messages if the $_SESSION['phpinvalidflag'] has been set to true. ... otherwise, proceed to update the database with the user's form data.
if ($_SESSION['phpinvalidflag'])
	{
	?>
	<script type='text/javascript' language='javascript'>history.back(); </script>
	<noscript>
	<?php
	if (isset($_SERVER['HTTP_REFERER']))
		header("Location: ".$_SERVER['HTTP_REFERER']); // Go back to previous page. (Similar to echoing the Javascript statement: history.go(-1) or history.back() except I think $_SERVER['HTTP_REFERER'] reloads the page, which causes freshly entered values in the adminaddtrainer.php form to get overwritten by values retrieved from the DB. So the javascript 'history.back()' method is more suitable. However, if Javascript is enabled, php form validation is moot. And if Javascript is disabled, then the javascript 'history.back()' method won't work anyway.
	?>
	</noscript>
	<?php
	ob_flush();
	}

} // End of PHP form validation

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>adminaddtrainer Slave Script</title>
<link href="/mediationtraining.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php
/* Prevent cross-site scripting via htmlspecialchars on these user-entry form field */
$EntityName = htmlspecialchars($EntityName, ENT_COMPAT);
$StreetAddress = htmlspecialchars($StreetAddress, ENT_COMPAT);
$City = htmlspecialchars($City, ENT_COMPAT);
$Zip = htmlspecialchars($Zip, ENT_COMPAT);
$Tel1 = htmlspecialchars($Tel1, ENT_COMPAT);
$Tel2 = htmlspecialchars($Tel2, ENT_COMPAT);
$Tel3 = htmlspecialchars($Tel3, ENT_COMPAT);
$EntityHomePage = htmlspecialchars($EntityHomePage, ENT_COMPAT);
$EntityEmail = htmlspecialchars($EntityEmail, ENT_COMPAT);
$Overview = htmlspecialchars($Overview, ENT_COMPAT);
$TrainerName1 = htmlspecialchars($TrainerName1, ENT_COMPAT);
$Trainer1Bio = htmlspecialchars($Trainer1Bio, ENT_COMPAT);
$TrainerName2 = htmlspecialchars($TrainerName2, ENT_COMPAT);
$Trainer2Bio = htmlspecialchars($Trainer2Bio, ENT_COMPAT);
$TrainerName3 = htmlspecialchars($TrainerName3, ENT_COMPAT);
$Trainer3Bio = htmlspecialchars($Trainer3Bio, ENT_COMPAT);
$TrainerName4 = htmlspecialchars($TrainerName4, ENT_COMPAT);
$Trainer4Bio = htmlspecialchars($Trainer4Bio, ENT_COMPAT);
$TrainerName5 = htmlspecialchars($TrainerName5, ENT_COMPAT);
$Trainer5Bio = htmlspecialchars($Trainer5Bio, ENT_COMPAT);
$TrainerName6 = htmlspecialchars($TrainerName6, ENT_COMPAT);
$Trainer6Bio = htmlspecialchars($Trainer6Bio, ENT_COMPAT);


/*
Manipulate user submission into $Tel1,2,3 so they comport with following format for storage in database: 415.378.7003.
*/
$Telephone = $Tel1.'.'.$Tel2.'.'.$Tel3;

/*
Manipulate check-boxes BasicMed, DivorceMed, FamilyMed, WorkplaceMed, MaritalMed, and OtherMed.
*/
if ($BasicMed) $BasicMed = 1; else $BasicMed = 0;
if ($DivorceMed) $DivorceMed = 1; else $DivorceMed = 0;
if ($FamilyMed) $FamilyMed = 1; else $FamilyMed = 0;
if ($WorkplaceMed) $WorkplaceMed = 1; else $WorkplaceMed = 0;
if ($MaritalMed) $MaritalMed = 1; else $MaritalMed = 0;
if ($OtherMed) $OtherMed = 1; else $OtherMed = 0;

/*
Manipulate EntityHomePage, using PHP's string functions to prepend 'http://' if $EntityHomePage is non-blank and doesn't already begin with either 'http://' or 'https://'.
*/
if ($EntityHomePage != '' AND !strstr($EntityHomePage, 'http://') AND !strstr($EntityHomePage, 'https://')) $EntityHomePage = 'http://'.$EntityHomePage;

/*
Use PHP's trim function to remove white space from beginning and end of $Overview ...
*/
$Overview = trim($Overview); // ... then remove any remaining line breaks from $Overview before inserting it into DB. Note the need for 'if' statements b/c strpos() returns a value of 'false' if the needle (\r or \n) is not in the haystack ($Overview).
if (strpos($Overview, "\r") !== false) $posr = strpos($Overview, "\r"); else $posr = strlen($Overview); // If the \r character is in the string then identify its position, else set $posr to a safe maximum such as the length of the string.
if (strpos($Overview, "\n") !== false) $posn = strpos($Overview, "\n"); else $posn = strlen($Overview); // Similarly for \n.
$pos = min($posr, $posn);  // Identify which character occurs first 
$Overview = substr($Overview,0,$pos); // Retain only that portion of $Overview from the beginning to the first occurence of either a \r or a \n.

/*
Use PHP's trim function to remove white space from beginning and end of $Trainer1Bio ...
*/
$Trainer1Bio = trim($Trainer1Bio); // ... then remove any remaining line breaks from $Trainer1Bio before inserting it into DB. Note the need for 'if' statements b/c strpos() returns a value of 'false' if the needle (\r or \n) is not in the haystack ($Trainer1Bio).
if (strpos($Trainer1Bio, "\r") !== false) $posr = strpos($Trainer1Bio, "\r"); else $posr = strlen($Trainer1Bio); // If the \r character is in the string then identify its position, else set $posr to a safe maximum such as the length of the string.
if (strpos($Trainer1Bio, "\n") !== false) $posn = strpos($Trainer1Bio, "\n"); else $posn = strlen($Trainer1Bio); // Similarly for \n.
$pos = min($posr, $posn);  // Identify which character occurs first 
$Trainer1Bio = substr($Trainer1Bio,0,$pos); // Retain only that portion of $Trainer1Bio from the beginning to the first occurence of either a \r or a \n.

/*
Use PHP's trim function to remove white space from beginning and end of $Trainer1Bio ...
*/
$Trainer2Bio = trim($Trainer2Bio); // ... then remove any remaining line breaks from $Trainer2Bio before inserting it into DB. Note the need for 'if' statements b/c strpos() returns a value of 'false' if the needle (\r or \n) is not in the haystack ($Trainer2Bio).
if (strpos($Trainer2Bio, "\r") !== false) $posr = strpos($Trainer2Bio, "\r"); else $posr = strlen($Trainer2Bio); // If the \r character is in the string then identify its position, else set $posr to a safe maximum such as the length of the string.
if (strpos($Trainer2Bio, "\n") !== false) $posn = strpos($Trainer2Bio, "\n"); else $posn = strlen($Trainer2Bio); // Similarly for \n.
$pos = min($posr, $posn);  // Identify which character occurs first 
$Trainer2Bio = substr($Trainer2Bio,0,$pos); // Retain only that portion of $Trainer2Bio from the beginning to the first occurence of either a \r or a \n.

/*
Use PHP's trim function to remove white space from beginning and end of $Trainer3Bio ...
*/
$Trainer3Bio = trim($Trainer3Bio); // ... then remove any remaining line breaks from $Trainer3Bio before inserting it into DB. Note the need for 'if' statements b/c strpos() returns a value of 'false' if the needle (\r or \n) is not in the haystack ($Trainer3Bio).
if (strpos($Trainer3Bio, "\r") !== false) $posr = strpos($Trainer3Bio, "\r"); else $posr = strlen($Trainer3Bio); // If the \r character is in the string then identify its position, else set $posr to a safe maximum such as the length of the string.
if (strpos($Trainer3Bio, "\n") !== false) $posn = strpos($Trainer3Bio, "\n"); else $posn = strlen($Trainer3Bio); // Similarly for \n.
$pos = min($posr, $posn);  // Identify which character occurs first 
$Trainer3Bio = substr($Trainer3Bio,0,$pos); // Retain only that portion of $Trainer3Bio from the beginning to the first occurence of either a \r or a \n.

/*
Use PHP's trim function to remove white space from beginning and end of $Trainer4Bio ...
*/
$Trainer4Bio = trim($Trainer4Bio); // ... then remove any remaining line breaks from $Trainer4Bio before inserting it into DB. Note the need for 'if' statements b/c strpos() returns a value of 'false' if the needle (\r or \n) is not in the haystack ($Trainer4Bio).
if (strpos($Trainer4Bio, "\r") !== false) $posr = strpos($Trainer4Bio, "\r"); else $posr = strlen($Trainer4Bio); // If the \r character is in the string then identify its position, else set $posr to a safe maximum such as the length of the string.
if (strpos($Trainer4Bio, "\n") !== false) $posn = strpos($Trainer4Bio, "\n"); else $posn = strlen($Trainer4Bio); // Similarly for \n.
$pos = min($posr, $posn);  // Identify which character occurs first 
$Trainer4Bio = substr($Trainer4Bio,0,$pos); // Retain only that portion of $Trainer4Bio from the beginning to the first occurence of either a \r or a \n.

/*
Use PHP's trim function to remove white space from beginning and end of $Trainer5Bio ...
*/
$Trainer5Bio = trim($Trainer5Bio); // ... then remove any remaining line breaks from $Trainer5Bio before inserting it into DB. Note the need for 'if' statements b/c strpos() returns a value of 'false' if the needle (\r or \n) is not in the haystack ($Trainer5Bio).
if (strpos($Trainer5Bio, "\r") !== false) $posr = strpos($Trainer5Bio, "\r"); else $posr = strlen($Trainer5Bio); // If the \r character is in the string then identify its position, else set $posr to a safe maximum such as the length of the string.
if (strpos($Trainer5Bio, "\n") !== false) $posn = strpos($Trainer5Bio, "\n"); else $posn = strlen($Trainer5Bio); // Similarly for \n.
$pos = min($posr, $posn);  // Identify which character occurs first 
$Trainer5Bio = substr($Trainer5Bio,0,$pos); // Retain only that portion of $Trainer5Bio from the beginning to the first occurence of either a \r or a \n.

/*
Use PHP's trim function to remove white space from beginning and end of $Trainer6Bio ...
*/
$Trainer6Bio = trim($Trainer6Bio); // ... then remove any remaining line breaks from $Trainer6Bio before inserting it into DB. Note the need for 'if' statements b/c strpos() returns a value of 'false' if the needle (\r or \n) is not in the haystack ($Trainer6Bio).
if (strpos($Trainer6Bio, "\r") !== false) $posr = strpos($Trainer6Bio, "\r"); else $posr = strlen($Trainer6Bio); // If the \r character is in the string then identify its position, else set $posr to a safe maximum such as the length of the string.
if (strpos($Trainer6Bio, "\n") !== false) $posn = strpos($Trainer6Bio, "\n"); else $posn = strlen($Trainer6Bio); // Similarly for \n.
$pos = min($posr, $posn);  // Identify which character occurs first 
$Trainer6Bio = substr($Trainer6Bio,0,$pos); // Retain only that portion of $Trainer6Bio from the beginning to the first occurence of either a \r or a \n.

/*
In readiness to set the trainer's Approved column to true (1) (clearly, the trainer is approved because he/she is being added to the DB by an Administrator)...
*/
$Approved = 1;

/*
Also force the value of the AddedByAdmin column in the trainers_table to 1 because any insertion performed by addtrainer_slave.php will have been an administrator insertion.
*/
$AddedByAdmin = 1;

if (!get_magic_quotes_gpc())
{
	$EntityName = addslashes($EntityName);
	$StreetAddress = addslashes($StreetAddress);
	$City = addslashes($City);
	$Zip = addslashes($Zip);
	$Telephone = addslashes($Telephone);
	$EntityHomePage = addslashes($EntityHomePage);
	$EntityEmail = addslashes($EntityEmail);
	$Overview = addslashes($Overview);
	$TrainerName1 = addslashes($TrainerName1);
	$Trainer1Bio = addslashes($Trainer1Bio);
	$TrainerName2 = addslashes($TrainerName2);
	$Trainer2Bio = addslashes($Trainer2Bio);
	$TrainerName3 = addslashes($TrainerName3);
	$Trainer3Bio = addslashes($Trainer3Bio);
	$TrainerName4 = addslashes($TrainerName4);
	$Trainer4Bio = addslashes($Trainer4Bio);
	$TrainerName5 = addslashes($TrainerName5);
	$Trainer5Bio = addslashes($Trainer5Bio);
	$TrainerName6 = addslashes($TrainerName6);
	$Trainer6Bio = addslashes($Trainer6Bio);
}	

// Connect to mysql
$db = mysql_connect('localhost', 'paulme6_merlyn', 'fePhaCj64mkik')
or die('Could not connect: ' . mysql_error());
mysql_select_db('paulme6_medtrainers') or die('Could not select database: ' . mysql_error());

// Draw the first "available" username and password pair from userpass_table. (Code adapted from nrmedlic's admin2.php.)
$query = "SELECT Username, Password FROM userpass_table WHERE Available = 1 ORDER BY Username LIMIT 1";
$result = mysql_query($query) or die('The SELECT Username, Password (where Available = 1) failed i.e. '.$query.' failed: ' . mysql_error());
$line = mysql_fetch_assoc($result);
$Username = $line['Username']; // Assign the username
$Password = $line['Password']; // Assign the password

// Update the Available value (setting it to 0 i.e. unavailable) for the username-password pair that we've now assigned to a mediator.
$query = "UPDATE userpass_table SET Available = 0 WHERE Username = '".$Username."' AND Password = '".$Password."'";
$result = mysql_query($query) or die('The attempt to update the userpass_table has failed. ' . mysql_error());

// Insert this username/password pair together with the trainer's data obtained via the form in adminaddtrainer.php into the database.
$query = "INSERT INTO trainers_table set Username = '".$Username."', Password = '".$Password."', Approved = '".$Approved."', EntityName = '".$EntityName."', StreetAddress = '".$StreetAddress."', City = '".$City."', State = '".$State."', Zip = '".$Zip."', Telephone = '".$Telephone."', BasicMed = '".$BasicMed."', DivorceMed = '".$DivorceMed."', FamilyMed = '".$FamilyMed."', WorkplaceMed = '".$WorkplaceMed."', MaritalMed = '".$MaritalMed."', OtherMed = '".$OtherMed."', EntityHomePage = '".$EntityHomePage."', EntityEmail = '".$EntityEmail."', Overview = '".$Overview."', TrainerName1 = '".$TrainerName1."', TrainerName2 = '".$TrainerName2."', TrainerName3 = '".$TrainerName3."', TrainerName4 = '".$TrainerName4."', TrainerName5 = '".$TrainerName5."', TrainerName6 = '".$TrainerName6."', Trainer1Bio = '".$Trainer1Bio."', Trainer2Bio = '".$Trainer2Bio."', Trainer3Bio = '".$Trainer3Bio."', Trainer4Bio = '".$Trainer4Bio."', Trainer5Bio = '".$Trainer5Bio."', Trainer6Bio = '".$Trainer6Bio."', MemberLevel = '".$MemberLevel."', AddedByAdmin = ".$AddedByAdmin.", DofAdd = CURDATE()";

$result = mysql_query($query) or die('Query (insert into trainers_table) failed: ' . mysql_error().' and the query string was: '.$query);

// Retrieve the value of the autoincremented column TrainerID that would just have gotten inserted (automatically) into trainers_table.
$TrainerID = mysql_insert_id($db);

// Update the userpass_table with the $TrainerID value just retrieved for the relevant username-password pair.
$query = "UPDATE userpass_table SET AssignedToTrainerID = '".$TrainerID."' WHERE Username = '".$Username."' AND Password = '".$Password."'";
$result = mysql_query($query) or die('Query (insert into trainers_table) failed: ' . mysql_error().' and the query string was: '.$query);

if (!$result) 
	{
	echo 'An update of trainers_table could not be completed.';
	}
else
	{
	// The trainers_table and userpass_table have been updated.
	unset($AdminAddTrainer);
?>
<div style="text-align: center"> <!-- This div provides centering for older browsers incl. NS4 and IE5. (See http://theodorakis.net/tablecentertest.html#intro.) Use of margin-left: auto and margin-right: auto in the style of the table itself (see below) takes care of centering in newer browsers. -->
<form method="post" action="unwind.php">
<table cellpadding="0" cellspacing="0" style="margin-top: 50px; margin-left: auto; margin-right: auto; position: relative; left: -7px;">
<tr>
<td style="text-align: left;">
<p class='basictext' style='margin-left: 50px; margin-right: 50px; margin-top: 40px; font-size: 14px;'>A trainer has been successfully added to the database.</p>
<p class='basictext' style='margin-left: 50px; margin-right: 50px; margin-bottom: 40px; font-size: 14px;'>Please click <a target='_self' style='font-size: 14px;' href='/index.php'>here</a> to go to the mediationtrainings.org home page. Or click <a style='font-size: 14px;' href='/adminaddtrainer.php'>here</a> to perform further administrator tasks.</p>
</td>
<tr>
<td style="text-align: center;">
<input type='button' name='Continue' class='buttonstyle' style="text-align: center;" value='Continue' onclick='javascript: window.location = "/adminaddtrainer.php";'> <!-- This is not a submit button and functions independenly of the action='unwind.php' form -->
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" name="Logout" class="buttonstyle" style="text-align: center;" value="Log Out">
</td>
</tr>
</table>
</form>
</div>

<?php
	}

ob_end_flush();
?>
</body>
</html>