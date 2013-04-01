<?php
/*
offramp.php is the landing page to which the NMTR subscriber is redirected after he/she has completed a monthly subscription payment on PayPal. He/she will have reached PayPal either by clicking a button in join.php or by clicking a button in activate.php. We can tell which route was taken by examining the value of the name="custom" field, which is set to "join" or the preexisting trainer's TrainerID (from trainers_table) respectively. The join.php page is for trainers who are listing in the Registry for the first time, whereas the activate.php page is for trainers who had previously listed in the Registry for free but who now are being asked to "activate" their accounts i.e. pony up in order to remain in the Registry.
   Note: the Payment Data Transfer (PDT) script code is based on PayPal's sample code for PDT at https://www.paypal.com/us/cgi-bin/webscr?cmd=p/pdn/pdt-codesamples-pop-outside#php. It was originally written for the New Resolution Mediation Platform licensing e-commerce under NRmedlic.
   offramp.php is a simplified version of licenseonramp.php combined with licenseofframp.php from the NRmedlic site.
   offramp.php (i) first determines whether it has been called legitimately by a PayPal GET referral. If it has been, it then checks that the payment amount (passed via PDT) is correct. An on-screen message is displayed accordingly, and (ii) an email is sent as a payment receipt if the amount is correct.
   offramp.php then (iii) obtains a username/password, updates the trainers_table for the new subscriber, and presents the trainer with a username/password confirmation both on-screen and via email confirmation so he/she can log into the addtrainer.php page and other password-protected pages. (In the case of "activate" trainers [cf. "join" trainers], they will already have been assigned username/passwords when they were listing for free, but we can still take this opportunity to remind them of their username/password.)
*/

session_start();

/* Connect to DB */
$db = mysql_connect('localhost', '', '')
or die('Could not connect: ' . mysql_error());
mysql_select_db('') or die('Could not select database: ' . mysql_error());

// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-synch';

$tx_token = $_GET['tx'];
$auth_token = "ZBvHEtfDDcymwuHLEWS10OgswNQYywOvmwI6a0xzlv2VawlT1bCI7FzVgEa"; // REPLACE with the token (obtained from Profile/Web Payments Preferences) for Sandbox implementation.
$req = "tx=$tx_token&at=$auth_token&cmd=_notify-synch";

// post back to PayPal system to validate
$header = "POST /cgi-bin/webscr HTTP/1.0\r\n"; // REMOVE THE PERIOD
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
// If PHP server isn't SSL enabled, instead use: $fp = fsockopen('www.paypal.com', 80, $errno, $errstr, 30);
 $fp = fsockopen('ssl://www.paypal.com', 443, $errno, $errstr, 30); // Change to 'www.sandbox.paypal.com' for Sandbox implementation!

if (!$fp) 
{
// HTTP ERROR
} 
else 
{
$outputstring = $header.$req;
fputs($fp, $outputstring);
// read the body data response from PayPal
$res = '';
$headerdone = false;
while (!feof($fp)) 
	{
	$line = fgets($fp, 1024);
	// echo $line.'<br>'; // Uncomment this if I'm debugging.
	if (strcmp($line, "\r\n") == 0) 
		{
		// read the header
		$headerdone = true;
		}
	else if ($headerdone)
		{
		// header has been read. now read the contents
		$res .= $line;
		}
	}

// parse the data
$lines = explode("\n", $res);
$keyarray = array();
if (strcmp ($lines[0], "SUCCESS") == 0)
	{
	for ($i=1; $i<count($lines);$i++)
		{
		list($key,$val) = explode("=", $lines[$i]);
		$keyarray[urldecode($key)] = urldecode($val);
		}
	/* Check that txn_id has not been previously processed */
	// I haven't yet implemented this. Hopefullly it's not very useful.

	/* Check that receiver_email is your Primary PayPal email */

	/* Check that payment_amount/payment_currency are correct */

	/* Process payment */
	$first_name = $keyarray['first_name'];
	$last_name = $keyarray['last_name']; 
	$item_name = $keyarray['item_name']; 
	$payment_gross = $keyarray['payment_gross']; // Deprecated. Better to use mc_gross instead, which will be the same value.
	$mc_gross = $keyarray['mc_gross'];
	$payment_status = $keyarray['payment_status']; // Completed or Pending
	$payer_status = $keyarray['payer_status']; // Verified or Unverified
	$payer_id = $keyarray['payer_id'];
	$txn_id = $keyarray['txn_id']; 
	$payment_date = $keyarray['payment_date']; 
	$payer_email = $keyarray['payer_email'];
	$mc_currency = $keyarray['mc_currency'];
	$address_street = $keyarray['address_street'];
	$address_city = $keyarray['address_city']; 
	$address_state = $keyarray['address_state']; 
	$address_zip = $keyarray['address_zip'];
	$address_country = $keyarray['address_country'];
	$contact_phone = $keyarray['contact_phone'];
	$custom = $keyarray['custom']; // The custom field will have value = "join" (assigned in buttons in join.php [and join_test.php]) for people who are joining the Registry for the first time, or the value of the preexisting trainer's TrainerID (assigned in buttons in activate.php [and activate_test.php]) for people who had previously been listed in the Registry for free and who are now choosing to pay to continue listing in the Registry i.e. to activate their accounts.
	}
else if (strcmp ($lines[0], "FAIL") == 0) 
	{
	// If this clause is invoked I should really (but haven't yet done so) log for manual investigation
	echo 'You are seeing this message because of either an unauthorized access attempt or a processing error in our Payment Data Transfer script. Please notify our Customer Support desk at support@mediationtrainings.org so we can investigate this problem and issue you a refund if applicable. Thank you.'; exit;
	}
}

fclose ($fp);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>National Mediation Training Registry</title>
<link href="/mediationtraining.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="750" align="center" cellpadding="0" cellspacing="0" border="0">
<tr>
<td width="750" align="center"><img class="graphic" src="/images/JoinTrainerDirectory.jpg" alt="Join Trainer Directory"></td></tr>
<tr>
<td align="left">
<?php
/*
Before thanking the user for his/her payment and providing an opportunity to proceed to receiving his/her username/password, verify that the payment amount tendered is correct. This helps prevent fraud. Perform this verification using the confirmProduct() function, which I originally defined for use in ipn.php.
*/
function confirmProduct($subscriptionfee, $grosspayment)
{
if ($grosspayment < (0.99 * $subscriptionfee) || $grosspayment > (1.01 * $subscriptionfee)) // Is received amount within 1% of the subscription fee?
	{
	$confirmed = 0; // Suspected case of fraud by the buyer (trying to sneak a small payment past me). Set $confirmed to 0 i.e. false.
	}
else
	{
	$confirmed = 1; // Set $confirmed to 1 i.e. true
	};
return $confirmed; // $confirmed will be true if the $ amount tendered is within 1% of the expected amount.
}

/* 
Verify whether the amount tendered is correct using the confirmProduct() function. 
*/
$confirmationresult = confirmProduct($_SESSION['MonthlySubscription'], $mc_gross);

if ($confirmationresult != 1) // i.e. a potentially fraudulent effort to purchase a license cheaply
	{
?>
	<h2 style="margin-top: 40px;">Thank you for your payment</h2>
	<p class="sales" style="margin-top:30px;">Please note that our system has detected a pricing discrepancy with a payment to the National Mediation Training Registry. For that reason, we cannot complete your transaction at this time. Our customer support desk has been informed of the discrepancy and will contact you shortly to resolve the issue. In the event that we&rsquo;re unable to resolve it, you will receive a full refund of your payment.</p>
	<p class="sales" style="margin-top:30px;">You may also contact our support desk at any time if you have questions: 
	  <script type='text/javascript'> var a = new Array('r','.','@med','port','sup','iation','trainings','o','g');document.write("<a href='mailto:"+a[4]+a[3]+a[2]+a[5]+a[6]+a[1]+a[7]+a[0]+a[8]+"'>"+a[4]+a[3]+a[2]+a[5]+a[6]+a[1]+a[7]+a[0]+a[8]+"</a>");</script>.</p>
<?php
	
	// Send myself (Customer Support) a message alerting me to this discrepancy.
	$address = 'paul@mediationtrainings.org';  // When testing, I commented out this line, and uncommented the abertawe address below.
	$subject = "NMTR: Discrepant Payment";
$body = "Hello ".$first_name."\n\n";
$body .= "A payment was apparently remitted for a subscription to the National Mediation Training Registry, but the amount paid was detected as discrepant with respect to the amount due for a monthly subscription. Please investigate and potentially refund the amount paid. Discrepancy was detected in offramp.php. Below is a record of the transaction.\n
Payment by: $first_name $last_name
Monthly subscription amount: $payment_gross $mc_currency
Payer email: $payer_email
Item: $item_name
Payer address: $address_street, $address_city, $address_state $address_zip, $address_country
Payment status: $payment_status
Payer ID: $payer_id
Transaction ID: $txn_id
Payment date: $payment_date\n";
$headers = 
"From: donotreply@mediationtrainings.org\r\n" .
"Reply-To: donotreply@mediationtrainings.org\r\n" .
"Bcc: paulmerlyn@yahoo.com\r\n" .
"X-Mailer: PHP/" . phpversion();
mail($address, $subject, $body, $headers);
	}
else
	{
	/* Determine whether the custom field in the button is of value "join" or a preexisting trainer's TrainerID and process accordingly. */
	if ($custom == 'join')
		{
		/* Draw the first "available" username and password pair from userpass_table. (Code adapted from nrmedlic's admin2.php.) */
		$query = "SELECT Username, Password FROM userpass_table WHERE Available = 1 ORDER BY Username LIMIT 1";
		$result = mysql_query($query) or die('The SELECT Username, Password (where Available = 1) failed i.e. '.$query.' failed: ' . mysql_error());
		$line = mysql_fetch_assoc($result);
		$Username = $line['Username']; // Assign the username
		$Password = $line['Password']; // Assign the password
	
		// Also store the $Username and $Password as session variables for use by addtrainer.php in the case where the user wants to use that script before he/she has killed the session.
		$_SESSION['Username'] = $Username;
		$_SESSION['Password'] = $Password;

		// Insert this username/password pair into trainers_table, also granting an Approved status of 1, a PaidUp status of 1, and capturing DofSub (i.e. the date on which the trainer became a paid subscriber), and the monthly subscription amount as well as other PDT-sourced data (i.e. payer email, payer phone, txn id, and payer name).
		$PayerName = $first_name.' '.$last_name;
		$query = "INSERT INTO trainers_table set Username = '".$Username."', Password = '".$Password."', Approved = 1, PaidUp = 1, AddedByAdmin = 0, DofSub = CURDATE(), MthlySubAmt = ".$mc_gross.", PayerEmail = '".$payer_email."', PayerPhone = '".$contact_phone."', TxnID = '".$txn_id."', PayerName = '".$PayerName."'";
		$result = mysql_query($query) or die('Query (insert into trainers_table) failed: ' . mysql_error().' and the query string was: '.$query);

		// Obtain the TrainerID for the trainer row just inserted into trainers_table and then use it when updating the userpass_table.
		$TrainerID = mysql_insert_id($db);
	
		// Update the Available value (setting it to 0 i.e. unavailable) for the username-password pair that we've now assigned to a trainer.
		$query = "UPDATE userpass_table SET Available = 0, AssignedToTrainerID = ".$TrainerID." WHERE Username = '".$Username."' AND Password = '".$Password."'";
		$result = mysql_query($query) or die('The attempt to update the userpass_table has failed. ' . mysql_error());

?>
		<h2 style="margin-top: 40px;">Welcome</h2>
		<p class="sales" style="margin-top:30px;">Thank you for your payment. And welcome to the National Mediation Training Registry &mdash; the top-ranked web site through which trainees find mediation trainers!</p>
		<p class="sales" style="margin-top:24px;">Your username and password are as follows:</p>
		<p class="sales" style="margin-top:24px; margin-left: 100px;">Username = <kbd><?=$Username; ?></kbd><br>Password = <kbd><?=$Password; ?></kbd></p>
		<p class="sales" style="margin-top:24px;">You may log in any time to create your <b>Trainer Profile</b> by selecting &ldquo;Create Trainer Profile&rdquo; under <b>Registry</b> in the main menu on any page of the web site. Better yet, create your <b>Trainer Profile</b> right now.</p>
		<div style="text-align: center;">
		<form method="post" action="/addtrainer.php">
		<input type="submit" name="CreateTrainerProfileNow" value="Create My Trainer Profile" class="buttonstyle" style="border-color: #9C151C; border-style: solid; border-width: 1px;">
		</form>
		</div>
	
		<p class="sales" style="margin-top:24px;">Once you&rsquo;ve created your Trainer Profile, you&rsquo;ll want to add training events. You&rsquo;ll also find &ldquo;Add Training Event&rdquo; under <b>Registry</b> in the main menu on any page of the web site.</p>

		<p class="sales" style="margin-top:24px;">Details of your transaction are below. We&rsquo;ve also sent a receipt to you at <?=$payer_email; ?>.</p>
<?php
		if ($payment_status == 'Pending')
			{
?>
			<p class="sales" style="margin-top:24px;">If your payment status is <em>pending,</em> the funds should clear in 1&ndash;5 business days. (We&rsquo;ll only contact you again if there&rsquo;s a problem with your payment.)</p>
<?php
			}
?>
		</td></tr></table>
		<hr size="1px" noshade color="#FF9900" class="divider" style="margin-top: 20px;">
		<br>
		<table cellpadding="0" cellspacing="2" border="0" style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin-left: auto; margin-right: auto;">
		<tr>
		<td width="120">
		Payment by:
		</td>
		<td>
		<?php echo $first_name.' '.$last_name; ?>
		</td>
		</tr>
		<tr>
		<td>
		Monthly subscription:
		</td>
		<td>
		<?php echo $payment_gross.' '.$mc_currency; ?>
		</td>
		</tr>
		<tr>
		<td>
		Item:</td>
		<td>
		<?php $TheItemName = str_replace('_', ' ', $item_name); echo $TheItemName; ?><!-- replace the underscores in "NMTR_Monthly_Subscription" with spaces-->
		</td>
		</tr>
		<tr>
		<td>
		Payment status:</td>
		<td>
		<?=$payment_status ;?>
		</td>
		</tr>
		<tr>
		<td>
		Payer ID:
		</td>
		<td>
		<?=$payer_id ;?>
		</td>
		</tr>
		<tr>
		<td>
		Transaction ID:</td>
		<td>
		<?=$txn_id ;?>
		</td>
		</tr>
		<tr>
		<td>
		Payment date:</td>
		<td>
		<?=$payment_date ;?>
		</td>
		</tr>
		</table>
		</td>
		</tr>
		<tr>
		<td>
		<br>
		<hr size="1px" noshade color="#FF9900" class="divider">
		</td>
		</tr>
		<tr>
		<td height="40" align="center" valign="bottom">
		<div class="basictextsmaller">&copy; <?php echo date("Y"); ?> National Mediation Training Registry. All rights reserved.</div>
		</td>
		</tr>
		</table>

<?php
		/* Send the user an email (transaction receipt) with details of the PayPal transaction if $payment_status == 'Pending' or 'Completed'. (Note: I don't want to issue a transaction receipt for other $payment_status values such as 'Refunded' or 'Failed'. */
		if ($payment_status == 'Pending' || $payment_status == 'Completed')
			{
			$address = $payer_email;  // When testing, I commented out this line, and uncommented the abertawe address below.
			// $address = "abertawe@sbcglobal.net";  // For testing purposes, I uncommented this abertawe address
			$subject = "Transaction Receipt";
$body = "Hello ".$first_name."\n\n";
$body .= "Thank you for your subscription to list in the National Mediation Training Registry. Please find below a record of your recent transaction.\n
Payment by: $first_name $last_name
Monthly subscription amount: $payment_gross $mc_currency
Item: $TheItemName
Payment status: $payment_status
Payer ID: $payer_id
Transaction ID: $txn_id
Payment date: $payment_date\n
If you have any questions, please don't hesitate to contact our support desk:
support@mediationtrainings.org.\n
Sincerely\n
Paul R. Merlyn
Administrator
National Mediation Training Registry
paul@mediationtrainings.org\n";
$headers = 
"From: paul@mediationtrainings.org\r\n" .
"Reply-To: paul@mediationtrainings.org\r\n" .
"Bcc: paulmerlyn@yahoo.com\r\n" .
"X-Mailer: PHP/" . phpversion();
mail($address, $subject, $body, $headers);

			// Also send the trainer a second email, this time with his/her username/password
			$address = $payer_email;  // When testing, I commented out this line, and uncommented the abertawe address below.
			// $address = "abertawe@sbcglobal.net";  // For testing purposes, I uncommented this abertawe address
			$subject = "Your Username/Password to Access NMTR";
$body = "Hello ".$first_name."\n\n";
$body .= "Thank you for your subscription to list in the National Mediation Training Registry. Please find below a confirmation of your login.\n
Username = $Username
Password = $Password\n
If you haven't already, visit mediationtrainings.org and select 'Create Trainer Profile' under Registry on the main menu. After you've created your Trainer Profile, you may want to select 'Add Training Event' to add details of any upcoming training events.\n
If you have any questions, please don't hesitate to contact our support desk:
support@mediationtrainings.org.\n
Thanks again and welcome!\n
Sincerely\n
Paul R. Merlyn
Administrator
National Mediation Training Registry
paul@mediationtrainings.org\n";
$headers = 
"From: paul@mediationtrainings.org\r\n" .
"Reply-To: paul@mediationtrainings.org\r\n" .
"Bcc: paulmerlyn@yahoo.com\r\n" .
"X-Mailer: PHP/" . phpversion();
mail($address, $subject, $body, $headers);
			}
		}
	else // $custom has taken the value of an existing trainer's TrainerID
		{
		/* Draw the trainer's username and password pair from trainers_table. */
		$query = "SELECT Username, Password, EntityEmail FROM trainers_table WHERE TrainerID = ".$custom;
		$result = mysql_query($query) or die('The SELECT Username, Password, EntityEmail from trainers_table for a preexisting (activating) trainer failed i.e. '.$query.' failed: ' . mysql_error());
		$line = mysql_fetch_assoc($result);
		$Username = $line['Username']; // Assign the username
		$Password = $line['Password']; // Assign the password
		$EntityEmail = $line['EntityEmail'];
	
		// Update the trainer's row in trainers_table, granting an Approved status of 1, a PaidUp status of 1, and capturing DofSub (i.e. the date on which the trainer became a paid subscriber), and the monthly subscription amount as well as other PDT-sourced data about the payer/transaction.
		$PayerName = $first_name.' '.$last_name;
		$query = "UPDATE trainers_table set Approved = 1, PaidUp = 1, AddedByAdmin = 0, DofSub = CURDATE(), MthlySubAmt = ".$mc_gross.", PayerEmail = '".$payer_email."', PayerPhone = '".$contact_phone."', TxnID = '".$txn_id."', PayerName = '".$PayerName."' WHERE TrainerID = ".$custom;
		$result = mysql_query($query) or die('Query (update of trainers_table) failed: ' . mysql_error().' and the query string was: '.$query);

?>
		<h2 style="margin-top: 40px;">Thank You</h2>
		<p class="sales" style="margin-top:30px;">Thank you for your payment. Your presence in the National Mediation Training Registry has now been activated. If you&rsquo;re happy with your Trainer Profile and training events as currently listed in the Registry, you can ignore the rest of this message.</p>
		<p class="sales" style="margin-top:24px;">As a reminder, your username and password are as follows:</p>
		<p class="sales" style="margin-top:24px; margin-left: 100px;">Username = <kbd><?=$Username; ?></kbd><br>Password = <kbd><?=$Password; ?></kbd></p>
		<p class="sales" style="margin-top:24px;">You may log in any time to edit your <b>Trainer Profile</b> by selecting &ldquo;Edit/Delete Trainer Profile&rdquo; under <b>Registry</b> in the main menu on any page of the web site. You&rsquo;ll also find options to add training events or edit/delete existing training events under <b>Registry</b> too.</p>
		<p class="sales" style="margin-top:24px;">Details of your transaction are below. We&rsquo;ve also sent a receipt to you at <?=$payer_email; ?>.</p>
<?php
		if ($payment_status == 'Pending')
			{
?>
			<p class="sales" style="margin-top:24px;">If your payment status is <em>pending,</em> the funds should clear in 1&ndash;5 business days. (We&rsquo;ll only contact you again if there&rsquo;s a problem with your payment.)</p>
<?php
			}
?>
		</td></tr></table>
		<hr size="1px" noshade color="#FF9900" class="divider" style="margin-top: 20px;">
		<br>
		<table cellpadding="0" cellspacing="2" border="0" style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin-left: auto; margin-right: auto;">
		<tr>
		<td width="120">
		Payment by:
		</td>
		<td>
		<?php echo $first_name.' '.$last_name; ?>
		</td>
		</tr>
		<tr>
		<td>
		Monthly subscription:
		</td>
		<td>
		<?php echo $payment_gross.' '.$mc_currency; ?>
		</td>
		</tr>
		<tr>
		<td>
		Item:</td>
		<td>
		<?php $TheItemName = str_replace('_', ' ', $item_name); echo $TheItemName; ?><!-- replace the underscores in "NMTR_Monthly_Subscription" with spaces-->
		</td>
		</tr>
		<tr>
		<td>
		Payment status:</td>
		<td>
		<?=$payment_status ;?>
		</td>
		</tr>
		<tr>
		<td>
		Payer ID:
		</td>
		<td>
		<?=$payer_id ;?>
		</td>
		</tr>
		<tr>
		<td>
		Transaction ID:</td>
		<td>
		<?=$txn_id ;?>
		</td>
		</tr>
		<tr>
		<td>
		Payment date:</td>
		<td>
		<?=$payment_date ;?>
		</td>
		</tr>
		</table>
		</td>
		</tr>
		<tr>
		<td>
		<br>
		<hr size="1px" noshade color="#FF9900" class="divider">
		</td>
		</tr>
		<tr>
		<td height="40" align="center" valign="bottom">
		<div class="basictextsmaller">&copy; <?php echo date("Y"); ?> National Mediation Training Registry. All rights reserved.</div>
		</td>
		</tr>
		</table>

<?php
		/* Send the user an email (transaction receipt) with details of the PayPal transaction if $payment_status == 'Pending' or 'Completed'. Send it to both $payer_email (i.e. the email address associated with the payer's PayPal account) and to $EntityEmail. (Note: I don't want to issue a transaction receipt for other $payment_status values such as 'Refunded' or 'Failed'. */
		if ($payment_status == 'Pending' || $payment_status == 'Completed')
			{
			$address = $payer_email.','.$EntityEmail;  // When testing, I commented out this line, and uncommented the abertawe address below.
			// $address = "abertawe@sbcglobal.net";  // For testing purposes, I uncommented this abertawe address
			$subject = "Transaction Receipt";
$body = "Hello ".$first_name."\n\n";
$body .= "Thank you for your subscription to list in the National Mediation Training Registry. Please find below a record of your recent transaction.\n
Payment by: $first_name $last_name
Monthly subscription amount: $payment_gross $mc_currency
Item: $TheItemName
TrainerID: $custom
Payment status: $payment_status
Payer ID: $payer_id
Transaction ID: $txn_id
Payment date: $payment_date\n
If you have any questions, please don't hesitate to contact our support desk:
support@mediationtrainings.org.\n
Sincerely\n
Paul R. Merlyn
Administrator
National Mediation Training Registry
paul@mediationtrainings.org\n";
$headers = 
"From: paul@mediationtrainings.org\r\n" .
"Reply-To: paul@mediationtrainings.org\r\n" .
"Bcc: paulmerlyn@yahoo.com\r\n" .
"X-Mailer: PHP/" . phpversion();
mail($address, $subject, $body, $headers);

			// Also send the trainer a second email, this time with his/her username/password
			$address = $payer_email.','.$EntityEmail;  // When testing, I commented out this line, and uncommented the abertawe address below.
			// $address = "abertawe@sbcglobal.net";  // For testing purposes, I uncommented this abertawe address
			$subject = "Username/Password Reminder to Access NMTR";
$body = "Hello ".$first_name."\n\n";
$body .= "Thank you for your subscription to list in the National Mediation Training Registry. Please find below a confirmation of your login.\n
Username = $Username
Password = $Password\n
Remember that you can add and edit training events under the Registry menu item (on the main menu).\n
If you have any questions, please don&rsquo;t hesitate to contact our support desk:
support@mediationtrainings.org.\n
Sincerely\n
Paul R. Merlyn
Administrator
National Mediation Training Registry
paul@mediationtrainings.org\n";
$headers = 
"From: paul@mediationtrainings.org\r\n" .
"Reply-To: paul@mediationtrainings.org\r\n" .
"Bcc: paulmerlyn@yahoo.com\r\n" .
"X-Mailer: PHP/" . phpversion();
mail($address, $subject, $body, $headers);
			}
		}
	}
?>

<!-- Start of StatCounter Code -->
<script type="text/javascript">
var sc_project=5997462; 
var sc_invisible=1; 
var sc_security="a349dfab"; 
</script>
<script type="text/javascript"
src="http://www.statcounter.com/counter/counter.js"></script><noscript><div
class="statcounter"><a href="http://www.statcounter.com/godaddy_website_tonight/"
target="_blank"><img class="statcounter"
src="http://c.statcounter.com/5997462/0/a349dfab/1/"></a></div></noscript>
<!-- End of StatCounter Code -->
</body>
</html>
