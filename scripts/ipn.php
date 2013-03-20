<?php
/*
Process the IPN. The bulk of this code draws from Chapter 10 of Paul Reinheimer's excellent book 'Professional Web APIs with PHP'
*/

//Step 0. Log the server superglobal and any posted transaction details in alltxlog.txt, which is dump for logging all transaction details.
ob_start();
echo date("D M j G:i:s T Y") . "\n"; 
print_r($_POST);
print_r($_SERVER);
$body = ob_get_clean();
$datadump = $body; // Store all this $_SERVER and $_POST data in a variable for later reuse.
file_put_contents("/home/paulme6/public_html/medtrainings/logs/IPNlogs/alltxlog.txt", $body, FILE_APPEND);

/* Send test email to confirm that ipn.php is being called. */
$message = "This message is invoked upon the execution of ipn.php. It's being sent to you as the webmaster.";
// In case any of our lines are larger than 70 characters, we should use wordwrap()
$message = wordwrap($message, 70);
mail('paulmerlyn@yahoo.com', 'Test Message for MedTrainings ipn.php', $message);

//Step 1. Verify IPN With PayPal. (Only proceed to Step 1.5 if () returns a true result.) 
$result=verifyIPN($_POST); 
if ($result == 0) // Test whether a 'false' (i.e. 0) is returned by 
	{
	$subject = "FAKE IPN RECEIVED";
	$address = "paul@mediationtrainings.org";
	$headers = 
	"From: ipn_processor@mediationtrainings.org\r\n" .
	"Reply-To: donotreply@mediationtrainings.org\r\n" .
	"X-Mailer: PHP/" . phpversion();
	mail($address, $subject, $body, $headers);
	exit; // Exit the script. No point in continuing without having a valid IPN.
	}
else if($result != 1) // I don't really see this clause getting executed. I think $result will be either 0 (above) or 1, and nothing else.
	{
	$subject = "Unable to Verify IPN";
	$body = "ipn.php was unable to contact PayPal in order to validate the IPN. If the incoming payment notification from PayPal pertains to a valid payment, the admittance of a trainer to the National Mediation Training Registry will need to be manually processed\n $result\n $body";
	$address = "paulmerlyn@yahoo.com";
	$headers = 
	"From: ipn_processor@mediationtrainings.org\r\n" .
	"Reply-To: donotreply@mediationtrainings.org\r\n" .
       "X-Mailer: PHP/" . phpversion();
	mail($address, $subject, $body, $headers);
	exit; // Exit the script. No point in continuing without having a valid IPN.
	}
  
// Step 0.5. The PayPal account that issues these IPN notifications also handles other products such as $1 donations to view the mediationjobs.php and arbitrationjobs.php pages for which transactions the kind of checking and email message sending isn't appropriate. Don't bother to go any further (exit instead) if the "item_name" isn't a Natl Med Traing Reg subscription. 
if ($_POST['transaction_subject'] != 'NMTR_Monthly_Subscription') exit;

// Step 1.0. Examine txn_type. If it's a cancellation, send a notification email to myself and a "sorry to see you go" email to the cancelling trainer. If it's a failed attempt at applying a subscription payment post-sign up, send a message to just myself so I can send the trainer a message (perhaps his/her credit card has expired).
switch ($_POST['txn_type']) 
	{
   	case "subscr_cancel":
		goodBye($_POST['first_name'], $_POST['payer_email']); // Send the trainer a "sorry to see you go" email
		// Also send myself a notification so I can manually set this trainer's Approved flag to 0 if necessary.
		$body = "Hello NMTR Support\n
This ipn.php-generated message is to let you know that a trainer has just cancelled his/her subscription to the National Mediation Training Registry.\n
The item name is ".$_POST['item_name'].". The name on the PayPal account is ".$_POST['address_name'].". The email address is ".$_POST['payer_email'].". The phone number is ".$_POST['contact_phone'].". The transaction ID (which relates to the original transaction) is ".$_POST['txn_id'].".\n\n";
		$body .= $datadump;  // Append all the $_SERVER and $_POST data
		$subject = "IPN Received (Cancellation of NMTR Subscription)";
		$address = "paul@mediationtrainings.org";
		$headers = 
		"From: ipn_processor@mediationtrainings.org\r\n" .
		"Reply-To: donotreply@mediationtrainings.org\r\n" .
		"X-Mailer: PHP/" . phpversion();
		mail($address, $subject, $body, $headers);
		
		// Also need to set the trainer's Approved column to 0 in trainers_table if we can detect it.
		
		exit; // Exit script here b/c there's no point in proceeding.
		break;
	case "subscr_failed":
		// Also send myself a notification so I can manually set this trainer's Approved flag to 0 if necessary.
		$body = "Hello NMTR Support\n
This ipn.php-generated message is to let you know that an attempt to charge a monthly subscription for an ongoing presence in the National Mediation Training Registry was not successfully completed by PayPal. You should investigate and contact the customer. His/her credit card may be out of date. At your discetion, you may also want to manually turn off (i.e. set Approved = 0) the listing for this trainer.\n
The item name is ".$_POST['item_name'].". The name on the PayPal account is ".$_POST['address_name'].". The email address is ".$_POST['payer_email'].". The phone number is ".$_POST['contact_phone'].". The transaction ID (which relates to the original transaction) is ".$_POST['txn_id'].".\n\n";
		$body .= $datadump;  // Append all the $_SERVER and $_POST data
		$subject = "IPN Received (Subscription Charge Failure for NMTR)";
		$address = "paul@mediationtrainings.org";
		$headers = 
		"From: ipn_processor@mediationtrainings.org\r\n" .
		"Reply-To: donotreply@mediationtrainings.org\r\n" .
		"X-Mailer: PHP/" . phpversion();
		mail($address, $subject, $body, $headers);
		exit;
		break;
	}
		
//Step 1.5. The IPN was valid (i.e. it came from PayPal) though it could still have been sourced from someone seeking a fraudulent transaction. Now check payment_status as "Completed", "Pending", "Failed", or default, and proceed accordingly...
switch ($_POST['payment_status']) 
	{
   	case "Completed":
		if ($_POST['test_ipn'] == 1) // The posted information came from a sandbox account/purchaser.
			{	
			paymentCompletedThankYou('Paul', 'paulmerlyn@yahoo.com'); // Send the 'Thank you' acknowledgement to myself b/c Sandbox accounts can't accept external email.
			}
		else // The posted information came from the live PayPal system, not from a sandbox PayPal account.
			{
			paymentCompletedThankYou($_POST['first_name'], $_POST['payer_email']); // Send the purchaser a message to say his/her payment is completed.
			};
		break; // Proceed to confirm product information.
	case "Pending":
		if ($_POST['test_ipn'] == 1) // The posted information came from a sandbox account/purchaser.
			{	
			paymentPendingThankYou('Paul', 'paulmerlyn@yahoo.com'); // Send the 'Thank you' acknowledgement to myself b/c Sandbox accounts can't accept external email.
			}
		else // The posted information came from the live PayPal system, not from a sandbox PayPal account.
			{
			paymentPendingThankYou($_POST['first_name'], $_POST['payer_email']); // Send the purchaser a message to say his/her payment is pending.
			};
		break; // Proceed to confirm product information.
	case "Failed":
		if ($_POST['test_ipn'] == 1) // The posted information came from a sandbox account/purchaser.
			{	
			paymentFailed('Paul', 'paulmerlyn@yahoo.com'); // Send the 'Thank you' acknowledgement to myself b/c Sandbox accounts can't accept external email.
			}
		else // The posted information came from the live PayPal system, not from a sandbox PayPal account.
			{
			paymentFailed($_POST['first_name'], $_POST['payer_email']); // Send the purchaser a message to say his/her payment failed.
			};
		$body = "Hello NMTR Support\n
An IPN from PayPal was posted to ipn.php (which generated this email message). You can view full details of the IPN post both below and in /logs/IPNlogs/alltxlog.txt.\n
The IPN had a payment_status of Failed - presumably because the payer either cancelled a Pending transaction him/herself before it could attain a payment_status of Completed, or because the payer's funding source (e.g. eCheck) had insufficient funds such that PayPal was unable to obtain the funds to clear the transaction.\n
The item name is ".$_POST['item_name'].". The name on the PayPal account is ".$_POST['address_name'].". The email address is ".$_POST['payer_email'].". The phone number is ".$_POST['contact_phone'].". The transaction ID (which relates to the original transaction) is ".$_POST['txn_id'].".\n\n";
		$body .= $datadump;  // Append all the $_SERVER and $_POST data
		$subject = "Valid IPN Received (Failed)";
		$address = "support@mediationtrainings.org";
		$headers = 
		"From: ipn_processor@mediationtrainings.org\r\n" .
		"Reply-To: donotreply@mediationtrainings.org\r\n" .
		"X-Mailer: PHP/" . phpversion();
		mail($address, $subject, $body, $headers);
		exit; // Exit script here b/c there's no point in confirming product information when the payment_status is Failed.
	case "Refunded":
		if ($_POST['test_ipn'] == 1) // The posted information came from a sandbox account/purchaser.
			{	
			paymentRefunded('Paul', 'paulmerlyn@yahoo.com'); // Send the 'Thank you' acknowledgement to myself b/c Sandbox accounts can't accept external email.
			}
		else // The posted information came from the live PayPal system, not from a sandbox PayPal account.
			{
			paymentRefunded($_POST['first_name'], $_POST['payer_email']); // Send the purchaser a message to say his/her payment failed.
			};
		$body = "Hello NMTR Support\n
An IPN from PayPal was posted to ipn.php (which generated this email message). You can view full details of the IPN post both below and in /logs/IPNlogs/alltxlog.txt.\n
The IPN had a payment_status of Refunded. The item name is ".$_POST['item_name'].". The refunded amount is ".$_POST['mc_gross'].". The name on the PayPal account is ".$_POST['address_name'].". The email address is ".$_POST['payer_email'].". The phone number is ".$_POST['contact_phone'].". The transaction ID (which relates to the original transaction) is ".$_POST['txn_id'].".\n\n";
		$body .= $datadump;  // Append all the $_SERVER and $_POST data
		$subject = "Refund Issued";
		$address = "support@mediationtrainings.org";
		$headers = 
		"From: ipn_processor@mediationtrainings.org\r\n" .
		"Reply-To: donotreply@mediationtrainings.org\r\n" .
		"X-Mailer: PHP/" . phpversion();
		mail($address, $subject, $body, $headers);
		exit; // Exit script here b/c there's no point in confirming product information when the payment_status is Refunded.
	default:
		$body = "Hello NMTR\nAn IPN from PayPal was received by ipn.php (which generated this email message). The IPN had a payment_status of neither Completed nor Pending nor Failed nor Refunded. You should confirm this transaction against your records.";
		$body .= $post;
		$subject = "Neither Completed nor Pending nor Failed nor Refunded IPN Received";
		$address = "paulmerlyn@yahoo.com";
		$headers = 
		"From: ipn_processor@mediationtrainings.org\r\n" .
		"Reply-To: donotreply@mediationtrainings.org\r\n" .
		"X-Mailer: PHP/" . phpversion();
		mail($address, $subject, $body, $headers);
		exit; // Exit script here b/c there's no point in confirming product information with this indeterminate payment_status.
	}

/* 
Step 2. Confirm Product Information for both Completed and Pending payment_status. This helps prevent fraud. Perform this verification using the confirmProduct() function, which I originally defined for use in ipn.php.
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


// Call the confirmProduct() function with the amount due and the amount tendered as input data.
$confirmationresult = confirmProduct($_SESSION['MonthlySubscription'], $mc_gross); // $_SESSION['MonthlySubscription'] is defined once in join.php.
if ($confirmationresult != 1)
	{
	$subject = "Fraud Alert: Product/Price Mismatch Detected by ipn.php";
	$address = "support@mediationtrainings.org";
	$headers = 
	"From: ipn_processor@mediationtrainings.org\r\n" .
	"Reply-To: donotreply@mediationtrainings.org\r\n" .
	"X-Mailer: PHP/" . phpversion();
	mail($address, $subject, $body, $headers);
	
	// Send prospective licensee a note to say the amount tendered doesn't match the correct price for the license sought
	if ($_POST['test_ipn'] == 1) // The posted information came from a sandbox account/purchaser.
		{	
		priceProductMismatch('Paul', 'paulmerlyn@yahoo.com'); //  Send email to myself b/c Sandbox accounts can't accept external email.
		}
	else // The posted information came from the live PayPal system, not from a sandbox PayPal account.
		{
		priceProductMismatch($_POST['first_name'], $_POST['payer_email']); // Send message to the prospective licensee.
		};

	exit; // Exit the script.
	}
 
// Step 3. Proceed with the order
// Send a notification email that a valid IPN has been received (and one that has passed the confirmProduct() function as a legitimate purchase).
$subject = "Valid IPN Received (".$_POST['payment_status'].")";
$address = "paul@mediationtrainings.org";
$headers = 
"From: ipn_processor@mediationtrainings.org\r\n" .
"Reply-To: donotreply@mediationtrainings.org\r\n" .
"X-Mailer: PHP/" . phpversion();
mail($address, $subject, $body, $headers); // If the parser gets to this line, $body will still have the value given to it at the beginning of this script i.e. a print_r of the $_SERVER[] and $_POST[] array contents.
// Log essential details in an easy-to-read validipnlog.txt file.
file_put_contents("/home/paulme6/public_html/medtrainings/logs/IPNlogs/validipnlog.txt", "Valid IPN received at: ".date(r)." with payment_status = ".$_POST['payment_status'].", address_name = ".$_POST['address_name'].", payer_id = ".$_POST['payer_id'].", payer_email = ".$_POST['payer_email'].", contact_phone = ".$_POST['contact_phone'].", mc_gross = ".$_POST['mc_gross'].", txn_id = ".$_POST['txn_id'].", txn_type = ".$_POST['txn_type'].", and item_name = ".$_POST['item_name']."\n\n", FILE_APPEND);

exit;


function verifyIPN($data) 
{ 
$postdata = "";
$response = array();
$postdata = 'cmd=_notify-validate'; 
foreach($data as $i=>$v) 
	{ 
	$postdata .= $i . "=" . urlencode(stripslashes($v)) . "&"; 
	}
$fp=@fsockopen("ssl://www.paypal.com" ,"443",$errnum,$errstr,30); // Change from www.paypal to www.sandbox.paypal.com if sandbox rather than production version.
if(!$fp) 
	{ 
	return "$errnum: $errstr";
	}
else 
	{ 
	fputs($fp, "POST /cgi-bin/webscr HTTP/1.1\r\n"); 
	fputs($fp, "Host: www.paypal.com\r\n");  // Change from www.paypal to www.sandbox.paypal.com if sandbox rather than production version.
	fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n"); 
	fputs($fp, "Content-length: ".strlen($postdata)."\r\n"); 
	fputs($fp, "Connection: close\r\n\r\n"); 
	fputs($fp, $postdata . "\r\n\r\n"); 
	while(!feof($fp)) { $response[]=@fgets($fp, 1024); }  
	fclose($fp); 
	}
	$response = implode("\n", $response);
	if(eregi("VERIFIED",$response)) 
	{
	return true;
	}
	else
	{
	// Save details of all IPNs that were not verified in log file = nonverifiedIPNs.txt for investigating potential fraud attempts.
	file_put_contents("/home/paulme6/public_html/medtrainings/logs/IPNlogs/nonverifiedIPNs.txt", "Failed, $response", FILE_APPEND); 
	return false;
	}
}

/*
The processOrder function is currently empty and unused because ipn.php isn't actually in the order-processing path. I actually use data provided by Payment Data Transfer (PDT) within onramp.php to support verification in the order processing workflow.
*/
function processOrder($data)
{
}

function paymentCompletedThankYou($fname, $address)
{
$body = "Hello ".$fname."\n\n";
$subject = "Subscription to the National Mediation Training Registry";
$body .= "It is my pleasure to welcome you as a member of the National Mediation Training Registry.\n
We&rsquo;ve now received confirmation from PayPal, our payment processor, that your payment has cleared.\n
If you have any questions, please contact our support desk at:\n
support@mediationtrainings.org.\n\n";
$body .= "Sincerely\n
Paul R. Merlyn
Administrator
National Mediation Training Registry
paul@mediationtrainings.org\n";
$headers = 
"From: paul@mediationtrainings.org\r\n" .
"Reply-To: support@mediationtrainings.org\r\n" .
"X-Mailer: PHP/" . phpversion();
mail($address, $subject, $body, $headers);
return;
}

function paymentPendingThankYou($fname, $address)
{
$body = "Hello ".$fname."\n\n";
$subject = "License Renewal - Payment Pending";
$body .= "It is my pleasure to welcome you as a member of the National Mediation Training Registry.\n
PayPal, our payment processor, has notified us that your payment is expected to clear in the next 1-5
business days. You will receive another email from us when the funds have cleared. We&rsquo;ll also contact
you if we encounter a problem with your payment.\n
If you have any questions, please contact our support desk at:\n
support@mediationtrainings.org.\n\n";
$body .= "Sincerely\n
Paul R. Merlyn
Administrator
National Mediation Training Registry
paul@mediationtrainings.org\n";
$headers = 
"From: paul@mediationtrainings.org\r\n" .
"Reply-To: support@mediationtrainings.org\r\n" .
"X-Mailer: PHP/" . phpversion();
mail($address, $subject, $body, $headers);
return;
}

function paymentFailed($fname, $address)
{
$subject = "Failed Payment";
$body = "Hello ".$fname."\n
This is an automated courtesy note to let you know that unfortunately your recent payment to the 
National Mediation Training Registry has failed. We've received notification from PayPal 
that PayPal was unable to draw sufficient funds for the transaction. The problem may be 
an out-of-date credit or debit card or insufficient funds in your bank account.\n
We will contact you again shortly to help you resolve this problem.
In the meantime, your inclusion in the Registry may be temporarily suspended until the problem 
is resolved.\n
If you have any questions, please don't hesitate to contact our support desk at:\n
support@mediationtrainings.org.\n
Sincerely\n
Paul R. Merlyn
Administrator
National Mediation Training Registry
paul@mediationtrainings.org\n";
$headers = 
"From: paul@mediationtrainings.org\r\n" .
"Reply-To: support@mediationtrainings.org\r\n" .
"X-Mailer: PHP/" . phpversion();
mail($address, $subject, $body, $headers);
return;
}

function paymentRefunded($fname, $address)
{
$refundamount = substr($_POST['mc_gross'],1);  // This string manipulation omits the minus sign E.g. if $_POST['mc_gross'] is -24.95, then $refundamount will be '24.95'.
$subject = "Payment Refunded";
$body = "Hello ".$fname."\n
This is a courtesy note to let you know that we have issued a refund of your payment. 
Depending on the original method of payment, the refund ($".$refundamount.") may take 0-5 
business days before the transaction is complete and the funds are restored to your account.\n
If you have any questions, please don't hesitate to contact our support desk at:\n
support@mediationtrainings.org.\n
Please let us know if we can be of further service to you in the future.\n
Sincerely\n
Paul R. Merlyn
Administrator
National Mediation Training Registry
paul@mediationtrainings.org\n";
$headers = 
"From: paul@mediationtrainings.org\r\n" .
"Reply-To: support@mediationtrainings.org\r\n" .
"X-Mailer: PHP/" . phpversion();
mail($address, $subject, $body, $headers);
return;
}

function priceProductMismatch($fname, $address)
{
$subject = "Subscription Price Mismatch";
$body = "Hello ".$fname."\n
Re. ".$_POST['item_name']."\n
This is a courtesy note to let you know that, after further processing your recent attempt to 
purchase the above license, we've detected a price discrepancy in the payment transaction. Our 
customer service desk will contact you shortly to resolve this problem.\n
If you have any questions in the meantime, please don't hesitate to contact our support desk at:\n
support@mediationtrainings.org.\n
Sincerely\n
Paul R. Merlyn
Administrator
National Mediation Training Registrypaul@mediationtrainings.org\n";
$headers = 
"From: paul@mediationtrainings.org\r\n" .
"Reply-To: support@mediationtrainings.org\r\n" .
"X-Mailer: PHP/" . phpversion();
mail($address, $subject, $body, $headers);
return;
}

function goodBye($fname, $address)
{
$subject = "Your NMTR Subscription Has Been Cancelled";
$body = "Hello ".$fname."\n
This is a courtesy note to let you know that we've cancelled your listing in the National
Mediation Training Registry. No additional charges will be made.\n
Thank you for your past support -- we are sorry to see you go!\n
If you've cancelled in error and wish to remain visible to tens of thousands of people who type
'mediation training' into Google every month, please contact me to help reinstate your
membership. Alternatively, just visit the National Mediation Training Registry and click one 
of the 'Join' links to create a new Trainer Profile.\n
Sincerely\n
Paul R. Merlyn
Administrator
National Mediation Training Registry
paul@mediationtrainings.org\n";
$headers = 
"From: paul@mediationtrainings.org\r\n" .
"Reply-To: support@mediationtrainings.org\r\n" .
"X-Mailer: PHP/" . phpversion();
mail($address, $subject, $body, $headers);
return;
}
?>