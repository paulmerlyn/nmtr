<?php
/*
This script is called by adminaddtrainer.php et al. It is called as an action when the user clicks the 'Log Out' submit buttons (disguised as links). It unsets various session variables so that the log-in screen (i.e. where the user offers an authentication password) will display again once this script returns control back to adminaddtrainer.php (et al) (via either the php header statement or the javascript history.back() method. Note the former necessitates use of the ob_start() and ob_flush() so nothing is written to the screen prior to a header being sent.
*/

// Start a session
session_start();
ob_start(); // Used in conjunction with ob_flush() [see www.tutorialized.com/view/tutorial/PHP-redirect-page/27316], this allows me to postpone issuing output to the screen relating to $UploadFile until after the header has been sent.

// Create short variable names
$Logout = $_POST['Logout'];
if (isset($Logout))
	{
	unset($_SESSION['Authenticated']);
	unset($_SESSION['Username']);
	unset($_SESSION['Password']);
	}

// Now go back to adminaddtrainer.php via either the HTTP_REFERER or via javascript history.back
?>
<script language="javascript" type="text/javascript">
window.history.go(-2); // Generally, this wil take the user back to the log-in or authentication page. It's used by addtrainer.php, adminaddtrainer.php, et al.
</script>
<noscript>
<?php
ob_flush();
if (isset($_SERVER['HTTP_REFERER'])) // Antivirus software sometimes blocks transmission of HTTP_REFERER.
	{
	header("Location: /adminaddtrainer.php"); // Go back to previous page. (Alternative to echoing the Javascript statement: history.go(-1) or history.back() in cases where user has Javascript disabled.
	}
	ob_flush();
?>
</noscript>
<?php
exit;
?>