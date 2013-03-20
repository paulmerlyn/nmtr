<?php
/*
simplesearch_slave.php is slave to simple searches performed either on index.php or on simplesearch.php. Simple search entails two principal search stategies:
(1) User sought Trainers and entered either a zip code (5 digits) or a tel area code (3 digits), and then clicked the 'LocateTrainers' button. (Note: Technically, name = 'LocateTrainers' is a hidden field name, not a submit button name. I ended up using a hidden field instead of simply naming the submit button b/c IE faltered, failing to pass the name of the submit button to simplesearch_slave.php. Clues into this failing can be found at http://www.webdeveloper.com/forum/showthread.php?t=143073 and http://www.dev-archive.net/articles/forms/multiple-submit-buttons.html)
(2) User sought Training Events and entered either 'online' or 'classroom-plus-zip-or-area-code', and then clicked the 'LocateEvents' button.  (Note: Technically, name = 'LocateEvents' is a hidden field name, not a submit button name. I ended up using a hidden field instead of simply naming the submit button b/c IE faltered, failing to pass the name of the submit button to simplesearch_slave.php. Clues into this failing can be found at http://www.webdeveloper.com/forum/showthread.php?t=143073 and http://www.dev-archive.net/articles/forms/multiple-submit-buttons.html)

simplesearch_slave.php simply validates the user-submitted zip code or area code and/or training type (classroom vs online), manages the issuance of a PHP error message on either index.php or simplesearch.php, and, presuming the submission was validated, passes the validated data to simplesearch.php for presentation to the user. 
*/

// Start a session
session_start();

ob_start(); // Used in conjunction with ob_flush() [see www.tutorialized.com/view/tutorial/PHP-redirect-page/27316], this allows me to postpone issuing output to the screen until after the header has been sent.
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Simple Search Slave Script</title>
</head>
<?php
// Unset the session variables that, when set, are sent to simplesearch.php for DB querying and display of user's search results.
unset($_SESSION['SSareacode']);
unset($_SESSION['SSzipcode']);
unset($_SESSION['SSeventtype']);
unset($_SESSION['SSforTrainers']);
unset($_SESSION['SSforEvents']);


// Connect to mysql
$db = mysql_connect('localhost', 'paulme6_merlyn', 'fePhaCj64mkik')
or die('Could not connect: ' . mysql_error());
mysql_select_db('paulme6_medtrainers') or die('Could not select database: ' . mysql_error());

// Create short variable names
$TelZipTrainer = $_POST['TelZipTrainer'];
$LocateTrainers = $_POST['LocateTrainers'];

$EventType = $_POST['EventType'];
$TelZipEvent = $_POST['TelZipEvent'];
$LocateEvents = $_POST['LocateEvents'];

/* Prevent cross-site scripting via htmlspecialchars on these user-entry form field */
$TelZipTrainer = htmlspecialchars($TelZipTrainer, ENT_COMPAT);
$TelZipEvent = htmlspecialchars($TelZipEvent, ENT_COMPAT);


// Determine which button (LocateTrainers or LocateEvents) was clicked and slave accordingly...

if (isset($LocateTrainers))
{
unset($LocateTrainers);
$_SESSION['SSforTrainers'] = true; // Set this session variable for use by simplesearch.php, indicating that user is seeking a search for trainers rather than for training events.

/* Validate whether user entered a legitimate zip code or area code */

// Create a session variable for the PHP form validation flag, and initialize it to 'false' i.e. assume it's valid.
$_SESSION['phpinvalidflag'] = false;

// Create session variables to hold inline error messages, and initialize them to blank.
$_SESSION['MsgTelZipTrainer'] = null;

// Seek to validate $TelZipTrainer
$illegalCharSet = '[A-Za-z~%@\^\*_\+`\|\$:";<>\?\.#&+=!,\(\)\-]+'; // Exclude everything except numbers.
switch (strlen($TelZipTrainer))
	{
	case 5: // Probable zip code
		$reqdCharSet = '[[:digit:]]{5}';  // Five numerics.
		if (ereg($illegalCharSet, $TelZipTrainer) || !ereg($reqdCharSet, $TelZipTrainer))
			{
			$_SESSION['phpinvalidflag'] = true;
			$_SESSION['MsgTelZipTrainer'] = '<span class="errorphp">Please enter a valid zip code (5 digits).<br></span>';
			};
		break;

	case 3: // Probable tel area code
		$reqdCharSet = '[[:digit:]]{3}';  // Three numerics.
		if (ereg($illegalCharSet, $TelZipTrainer) || !ereg($reqdCharSet, $TelZipTrainer))
			{
			$_SESSION['phpinvalidflag'] = true; 
			$_SESSION['MsgTelZipTrainer'] = '<span class="errorphp">Please enter a valid telephone area code (3 digits).<br></span>';
			};
		break;

	case 0: // Erroneous empty submission
		$_SESSION['phpinvalidflag'] = true; 
		$_SESSION['MsgTelZipTrainer'] = '<span class="errorphp">You entered a blank (empty) telephone area code or zip code. Please try again.<br></span>';
		break;

	default: // Value entered by user for $TelZipTrainer was neither three- nor five-characters.
		$_SESSION['phpinvalidflag'] = true; 
		$_SESSION['MsgTelZipTrainer'] = '<span class="errorphp">Please enter a valid telephone area code (3 digits) or zip code (5 digits).<br></span>';
		break;
	}

//Now go back to the previous page (which will be either index.php or simplesearch.php) and show any PHP inline validation error messages if the $_SESSION['phpinvalidflag'] has been set to true. ... otherwise, proceed to obtain search results for the user.
if ($_SESSION['phpinvalidflag'])
	{
	?>
	<script type='text/javascript' language='javascript'>history.back(); </script>
	<noscript>
	<?php
	if (isset($_SERVER['HTTP_REFERER']))
		header("Location: ".$_SERVER['HTTP_REFERER']); // Go back to previous page. (Similar to echoing the Javascript statement: history.go(-1) or history.back() except I think $_SERVER['HTTP_REFERER'] reloads the page.
	?>
	</noscript>
	<?php
	ob_flush();
	exit;
	}

// If we got this far, then the user's zip code/area code was valid. Save the validated user data as session variable(s) and pass it to simplesearch.php for DB processing and subsequent display of search results.

switch (strlen($TelZipTrainer))
	{
	case 5: // Validated zip code
		$_SESSION['SSzipcode'] = $TelZipTrainer;
		break;
	case 3: // Validated tel area code
		$_SESSION['SSareacode'] = $TelZipTrainer;
		break;
	default:
		echo 'An invalid zip code or telephone area code has been presented for use in a potential select query. Please contact the site Administrator.';
		ob_flush();
		exit; 
	}

}


if (isset($LocateEvents))
{
unset($LocateEvents);
$_SESSION['SSforEvents'] = true; // Set this session variable for use by simplesearch.php, indicating that user is seeking a search for training events rather than for trainers.

// Create a session variable for the PHP form validation flag, and initialize it to 'false' i.e. assume it's valid.
$_SESSION['phpinvalidflag'] = false;

/* Conduct form validation iff $EventType == 'classroom'. (If $EventType == 'online', there's no zip or area code input to validate.) */
if ($EventType == 'classroom')
	{
	/* Validate whether user entered a legitimate zip code or area code */

	// Create session variables to hold inline error messages, and initialize them to blank.
	$_SESSION['MsgTelZipEvent'] = null;

	// Seek to validate $TelZipEvent
	$illegalCharSet = '[A-Za-z~%@\^\*_\+`\|\$:";<>\?\.#&+=!,\(\)\-]+'; // Exclude everything except numbers.
	switch (strlen($TelZipEvent))
		{
		case 5: // Probable zip code
			$reqdCharSet = '[[:digit:]]{5}';  // Five numerics.
			if (ereg($illegalCharSet, $TelZipEvent) || !ereg($reqdCharSet, $TelZipEvent))
				{
				$_SESSION['phpinvalidflag'] = true;
				$_SESSION['MsgTelZipEvent'] = '<span class="errorphp">Please enter a valid zip code (5 digits).<br></span>';
				};
			break;

		case 3: // Probable tel area code
			$reqdCharSet = '[[:digit:]]{3}';  // Three numerics.
			if (ereg($illegalCharSet, $TelZipEvent) || !ereg($reqdCharSet, $TelZipEvent))
				{
				$_SESSION['phpinvalidflag'] = true; 
				$_SESSION['MsgTelZipEvent'] = '<span class="errorphp">Please enter a valid telephone area code (3 digits).<br></span>';
				};
			break;

		case 0: // Erroneous empty submission
			$_SESSION['phpinvalidflag'] = true; 
			$_SESSION['MsgTelZipEvent'] = '<span class="errorphp">You entered a blank (empty) telephone area code or zip code. Please try again.<br></span>';
			break;

		default: // Value entered by user for $TelZipEvent was neither three- nor five-characters.
			$_SESSION['phpinvalidflag'] = true; 
			$_SESSION['MsgTelZipEvent'] = '<span class="errorphp">Please enter a valid telephone area code (3 digits) or zip code (5 digits).<br></span>';
			break;
		}

	//Now go back to the previous page (which will be either index.php or simplesearch.php) and show any PHP inline validation error messages if the $_SESSION['phpinvalidflag'] has been set to true. ... otherwise, proceed to obtain search results for the user.
	if ($_SESSION['phpinvalidflag'])
		{
		?>
		<script type='text/javascript' language='javascript'>history.back(); </script>
		<noscript>
		<?php
		if (isset($_SERVER['HTTP_REFERER']))
			header("Location: ".$_SERVER['HTTP_REFERER']); // Go back to previous page. (Similar to echoing the Javascript statement: history.go(-1) or history.back() except I think $_SERVER['HTTP_REFERER'] reloads the page.
		?>
		</noscript>
		<?php
		ob_flush();
		exit;
		}

	} // End of if clause for form validation when EventType == 'classroom'
else // EventType == 'online' i.e. the user selected 'online' in the Simple Search bar's dropdown menu on index.php or simplesearch.php
	{
	$_SESSION['SSeventtype'] = 'online'; // Set this session variable for use by simplesearch.php. (No form validation necessary.)
	};
	
// If we got this far, then either the user selected EventType == 'classroom' and entered a valid zip code/area code, or the user selected EventType == 'online'. We dealt with the latter case just above by setting $_SESSION['SSeventtype'] to 'online'. Now deal with the former case below, saving the validated user data as session variable(s).

if ($EventType == 'classroom') 
	{
	switch (strlen($TelZipEvent))
		{
		case 5: // Validated zip code
			$_SESSION['SSzipcode'] = $TelZipEvent;
			unset($_SESSION['SSareacode']);
			break;
		case 3: // Validated tel area code
			$_SESSION['SSareacode'] = $TelZipEvent;
			unset($_SESSION['SSzipcode']);
			break;
		default:
			echo 'An invalid zip code or telephone area code has been presented for use in a potential select query. Please contact the site Administrator.';
			ob_flush();
			exit; 
		}
	}

}

// Now go to the simple search results page simplesearch.php for display of the search results.
if (!$_SESSION['phpinvalidflag'])
	{
	?>
	<script type='text/javascript' language='javascript'>document.location = '/simplesearch.php'; </script>
	<noscript>
	<?php
	if (isset($_SERVER['HTTP_REFERER']))
		header("Location: /simplesearch.php"); // Go back to previous page. (Similar to echoing the Javascript statement: history.go(-1) or history.back() except I think $_SERVER['HTTP_REFERER'] reloads the page.
	?>
	</noscript>
	<?php
	ob_flush();
	exit;
	}
?>

<body>

</body>
</html>
