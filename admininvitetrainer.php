<?php
/*
admininvitetrainer.php allows an administrator (user) to invite a trainer to list in the directory. After authentication of the administrator and submission of the trainer details form (principally, trainer name and email address), it hands over to form action script admininvitetrainer_slave.php, which sends the email invitation.
*/

// Start a session
session_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Invite Trainer | Administrator of Mediation Training Registry</title>
<link href="/mediationtraining.css" rel="stylesheet" type="text/css">
<script type="text/javascript" language="javascript" src="/scripts/emailaddresschecker.js"></script>
<script>
function FocusFirst()
{
	if (document.forms.length > 0 && document.forms[0].elements.length > 0)
		document.forms[0].elements[0].focus();
};

function checkEmailOnly()
{
// Validate EntityEmail field.
var entityEmailValue = document.getElementById("EntityEmail").value;
var entityEmailLength = entityEmailValue.length;
if (entityEmailValue != null && entityEmailValue  != '') // Since EntityEmail is optional, only validate if it's non-blank.
	{
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
} 

function checkForm() // checkForm() gets called when the user clicks the submit button.
{
hideAllErrors();
if (!checkEmailOnly()) return false;
return true; // All element(s) passed their validity checks, so return a true.
} // End of checkForm()

/* This function hideAllErrors() is called by checkForm() and by onblur event. */
function hideAllErrors()
{
document.getElementById("EmailError").style.display = "none";
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
$Authentication = $_POST['Authentication'];

if  (empty($Authentication) && $_SESSION['Authenticated'] != 'true')
	{
	// Visitor needs to authenticate
	?>
	<div style="text-align: center; width: 250px; margin: auto; padding: 15px; border: 2px solid #9C151C; border-color: #9C151C;">
	<h4 class="forms">Please authenticate yourself:</h4>
	<br>
	<form method="post" action="/admininvitetrainer.php">
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
			<h1 style="text-align: left;">To invite a trainer (via email) to list in the Registry:</h1>
			<br>
			
			<form method="post" action="/scripts/admininvitetrainer_slave.php">
			
			<table width="530">
			<tr height="30">
			<td width="120" align="left"><label for="Title">Title/Address</label></td>
			<td align="left" width="40"><a name="TitleAnchor" class="plain"><input type="text" name="Title" id="Title" maxlength="10" size="3"  style="width: 30px;" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white';"></a></td>
			<td width="185">
			<label for="FirstName">First Name</label>&nbsp;
			<a name="FirstNameAnchor" class="plain"><input type="text" name="FirstName" id="FirstName" maxlength="20" size="8" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white';"></a>
			</td>
			<td width="185" align="left">
			<label>Last Name</label>&nbsp;<a name="LastNameAnchor" class="plain"><input type="text" name="LastName" id="LastName" maxlength="30" size="9" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white';"></a>
			</td>
			</tr>
			<tr><!-- This row is specially created to hold Title, FirstName, and LastName errors. -->
			<td>&nbsp;</td>
			<td colspan="3" valign="top" align="left">
			<?php if ($_SESSION['MsgTitle'] != null) { echo $_SESSION['MsgTitle']; $_SESSION['MsgTitle']=null; } ?>
			<?php if ($_SESSION['MsgFirstName'] != null) { echo $_SESSION['MsgFirstName']; $_SESSION['MsgFirstName']=null; } ?>
			<?php if ($_SESSION['MsgLastName'] != null) { echo $_SESSION['MsgLastName']; $_SESSION['MsgLastName']=null; } ?>
			</td>
			</tr>
			<tr height="40">
			<td align="left" valign="top" style="position: relative; top: 4px;"><label>Use Formal Title</label></td>
			<td align="left" colspan="3"><input type="checkbox" name="UseFormalTitle" value="1">
			<div class="greytextsmall">Check to use formal title in invitation. Example: <em>Dr. Saunders</em>, rather than <em>Bob</em></div></td>
			</tr>
			<tr height="60">
			<td width="120" align="left"><label>Organization Name</label><div class="greytext">(Informal &ndash; log only)</div></td>
			<td align="left" colspan="3"><input type="text" name="EntityName" id="EntityName" maxlength="50" size="30"><div class="greytextsmall">This form of the organization&rsquo;s name is not stored in the trainers_table</div></td>
			</tr>
			<tr height="50">
			<td align="left"><label for="Email">Trainer&rsquo;s Email</label></td>
			<td align="left" colspan="3"><a name="EmailAnchor" class="plain"><input type="text" name="Email" id="Email" maxlength="60" size="30" onFocus="this.style.background='#FFFF99'" onBlur="this.style.background='white'; hideAllErrors(); return checkEmailOnly();"></a><span class="redsup">&nbsp;&nbsp;<b>*</b> (required)</span>
			<div class="error" id="EntityEmailError">Please check your format. Use only alphanumerics (A-Z, a-z, 0-9), dash (-), period (.), @, and underscore (_) characters.<br></div><?php if ($_SESSION['MsgEmail'] != null) { echo $_SESSION['MsgEmail']; $_SESSION['MsgEmail']=null; } ?>
			</td>
			</tr>
			<tr height="40">
			<td align="left" valign="top" style="position: relative; top: 4px;"><label>Send Email Now?</label></td>
			<td align="left" colspan="3"><input type="checkbox" name="SendNow" value="1">
			<div class="greytextsmall">Check to send a marketing message to this prospect now.</div></td>
			</tr>
			<tr>
			<td align="left" colspan="4"><input type="submit" name="AdminInviteTrainer" class="buttonstyle" style="margin-left: 120px;" value="Invite Trainer"></td>
			</tr>
			</table>
			</form>

			</div>

			<?php
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
?>	
</div>
</body>
</html>