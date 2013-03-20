<?php
/*
This arbitration jobs page (sister to mediationjobs.php) requires a small micropayment "donation" from the page visitor. It exploits PayPal's new micropayment fee schedule set up on my personal (sole prop) name@yahoo.com PayPal account. The PHP code tests whether the visitor made a Completed donation via PayPal (or, alternatively, was referred from the MediationCareer.org server). If he/she has, it's assumed that he/she made the requisite donation and is then shown the full list of mediation jobs. If the PHP code isn't able to detect the Completed status in the returned URL query string, then the page is shown with a large black screen div that requests a donation via a PayPal "Buy Now" button.
*/

// Start a session
session_start();

// I use this technique (courtesy: http://celestial-star.net/tutorials/49-check-referrer/) so that I can see the arbitrationjobs.php page myself without the overlay screen and without my having to make a donation! It works by permitting the page to show if the $_SERVER['HTTP_REFERER']'s path is the jobspagestest.html private page).
$referer = $_SERVER['HTTP_REFERER'];
$refererArray = parse_url($referer); //Otherwise, parse $referer (into an associative array) so we can test $refererArray['path'] below. If it is 'jobspagestest.html', then this would be grounds to not show the overlay screen.
$referringPath = $refererArray['path'];

// Code for detecting whether the visitor to this page did remit a donation via PayPal. (The code used is based on code originally written for file = licenseofframp.php on NRmedlic when checking whether an AlphaTrust esignature was properly made for New Resolution Launch Platform licensees.) Do this by examining the query string appended by PayPal to the www.mediationtrainings.org/arbitrationjobs.php URL when PayPal directs the user back to arbitrationjobs.php. Basically, if the query string (e.g. http://www.mediationtrainings.org/arbitrationjobs.php?tx=8AE45613H9211944R&st=Completed&amt=0.15&cc=USD&cm=&item_number=ArbJobs&sig=P9yU0ceABy0j8nOgBbHlYPyknLs5EJBFGUapPMX34aJBO52VLZWFC1Umlor0bIbs37yawkbCmEHHpuas0%2fYxdbUWw7xtLOHZ6dkszsp2PekFeK%2bcs1Ab03vwP%2fRhKh2dyvT%2bgljF3XUzFJsgbDA6L6JRykUOPjHug8S%2b4Pfh0fo%3d) doesn't contain an 'st=Completed', the visitor failed to pay (e.g. he/she hit the 'Cancel' button while inside PayPal).
$queryString = $_SERVER['QUERY_STRING']; // courtesy http://www.webmasterworld.com/forum88/221.htm
$queryArray = explode('&', $queryString);

$validDonationFlag = 0; // // Initialize to false (i.e. assume it's not valid).

foreach ($queryArray as $item)
	{
	if ($item == 'st=Completed')
		{
		$validDonationFlag = 1; // Set this flag to true (i.e. 1) once we conclude the donation was valid (i.e. the query string contains an "st=Completed")
		}
	};
	
// As a second check of whether the visitor should see the page without the overlay screen, so the $validDonationFlag to true if the visitor is me via the jobspagestest.html page.
if ($referringPath == '/jobspagestest.html') $validDonationFlag = 1;

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><!-- InstanceBegin template="/Templates/MasterTemplate.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<!-- InstanceBeginEditable name="doctitle" -->
<title>Arbitration Jobs</title>
<meta NAME="description" CONTENT="Listing of arbitration jobs and arbitrator jobs, updated daily.">
<meta NAME="keywords" CONTENT="arbitration jobs, arbitrator jobs, conflict resolution, jobs for arbitrators">
<!-- InstanceEndEditable -->
<link href="/mediationtraining.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="/milonic/milonic_src.js" type="text/javascript"></SCRIPT>
<script language="JavaScript" type="text/javascript">
if(ns4)_d.write("<scr"+"ipt language='JavaScript' src='/milonic/mmenuns4.js'><\/scr"+"ipt>");
else _d.write("<scr"+"ipt language='JavaScript' src='/milonic/mmenudom.js'><\/scr"+"ipt>"); </script>
<SCRIPT language='JavaScript' src="/milonic/menu_data.js" type='text/javascript'></SCRIPT>
<link rel="shortcut icon" href="/favicon.ico">
<!-- InstanceBeginEditable name="head" -->
<script>
// Javascript for display of overlay screen is courtesy: http://celestial-star.net/tutorials/49-check-referrer/
function clicker(){
	var thediv=document.getElementById('displaybox');
	if(thediv.style.display == "none"){
		thediv.style.display = "";
		thediv.innerHTML = "<table width='100%' height='100%'><tr><td align='center' valign='middle' width='100%' height='100%'><img alt='arbitration jobs donation' width='256' height='173' style='position: relative; bottom: 20px;' src='/images/mediationjobsdonation.jpg'><p>The Arbitration Jobs board currently lists approximately 10-20 recently posted jobs in the arbitration field. In order to view this page, please make a donation &mdash; minimum $1.00, please &mdash; to the National Mediation Training Registry. Donations are an important source of financial support and help to defray operating costs.</p><p>Good luck as you explore jobs in the rapidly advancing field of arbitration, and thanks for supporting Alternative Dispute Resolution through your donation!</p><form action='https://www.paypal.com/cgi-bin/webscr' method='post'><input type='hidden' name='cmd' value='_s-xclick'><input type='hidden' name='hosted_button_id' value='NDB65TKBQAP3U'><input type='hidden' name='page_style' value='NMTR'><input type='image' src='https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif' border='0' name='submit' alt='PayPal - The safer, easier way to pay online!'><img alt='' border='0' src='https://www.paypal.com/en_US/i/scr/pixel.gif' width='1' height='1'></form></td></tr></table>";
	}else{
		thediv.style.display = "none";
		thediv.innerHTML = '';
	}
	return false;
}
// Note that the "<input type='hidden' name='page_style' value='NMTR'>" above forces an override of the MediationCareer.org banner (the default primary banner on the Custom Payment Page for this particular PayPal account), replacing that default image with a different (non-Primary) image for the National Mediation Training Registry.
</script>
<!-- InstanceEndEditable -->
</head>

<body>
<div id="main">
<div id="relwrapper">
<!-- InstanceBeginEditable name="Content" -->
<?php
//Check if a valid payment donation was made from PayPal.
if($validDonationFlag != 1)
	{
    //The visitor did not successfully complete a payment donation, so show the overlay screen by calling the clicker() JS function on page load. Otherwise (implicitly), do nothing i.e. don't call clicker().
	echo '<script>window.onload=function() { clicker(); }</script>'; // Ref. www.onlinetools.org/articles/unobtrusivejavascript/chapter4.html regarding unobtrusive call to onload event handler.
	} 
?>
<div id="displaybox" style="display: none;"></div> <!-- This is a placeholder for the overlay screen to be filled (via innnerHTML) via the clicker() function iff a Completed donation was not made via PayPal. -->

<h1>Arbitration Jobs</h1>
<p>A compilation of recently posted arbitrator jobs and associated jobs in the arbitration field:</p>
<div style="margin-left: 50px;">
<?php
/*
Jobs data is sourced from simplyhired.com. Log into https://www.jobamatic.com/a/jbb/partner-dashboard-name-pricing, and click the 'Advanced' tab to access the ReST-based API. I implemented the API with help from Paul Reinheimer's Web APIs book b/c jobamatic.com site has minimal  documentation.
   Note that if my IP address changes from its current value of 64.175.41.86 (assigned by inmotionhosting.com), the call to function callAPIQuick() may not work. So I've instead obtained the current value of the IP address dynamicallyas an input parameter to that call. (I may be able to get my server's IP address dynamically, thereby making the code more robust to IP address changes.)
*/
$ipaddress = $_SERVER['SERVER_ADDR'];
//echo '$ipaddress is: '.$ipaddress.'<br />'; 
function callAPIQuick($endpoint, $pshid, $ssty, $cflg, $jbd, $clip)
{
  $url = $endpoint . "?pshid=$pshid&ssty=$ssty&cflg=$cflg&jbd=$jbd&clip=$clip";
  $response = @file_get_contents($url);
  return $response;
}

// The search parameters (i.e. query string 'q' [including OR and NOT keywords], number of results 'ws', and results ranking 'sb' are defined under the 'Advanced' tab after logging into jobamatic (see https://www.jobamatic.com/a/jbb/partner-dashboard-advanced-xml-api). You'll also find there other necessary parameters such as my developer account code=23226, IP address, et al.
$response = callAPIQuick('http://api.simplyhired.com/a/jobs-api/xml-v2/q-arbitration+OR+arbitrator+AND+NOT+%28+%22postdoctoral+fellow%22+OR+%22events+manager%22+OR+engineering+OR+%22quality+engineer%22+OR+programmer+OR+physician+OR+chief+OR+anesthesiology+OR+developer+OR+data+OR+architect+OR+seeking+OR+retired+OR+dentist+OR+%22technical+expert%22+OR+%22site+operation%22+OR+%22validation+manager%22+OR+%22product+analyst%22+OR+cardiology+OR+%22Mill+Manager%22+OR+voip+OR+sdlc+OR+%22Customer+Care+Representative%22+OR+Creative+OR+%22Graduate+assistantship%22+OR+%22Doctoral+Fellow%22+OR+%22mediate+risk%22+%29+AND+NOT+%28C+OR+C%2B%2B+OR+java+OR+platform+OR+unix+OR+UNIX+OR+engineer+OR+developer+OR+engineering+OR+MySQL+OR+SQL+OR+CRM+OR+MRO+OR+Technology+OR+Game+OR+scripting+OR+architect+OR+estrogen+OR+molecular+OR+mortgage+OR+debt+AND+mediation+OR+perl+OR+technical+OR+SOA+OR+code%29+AND+NOT+%28title%3A%28+mitigation+OR+programmer+OR+developer+OR+engineering+OR+%22events+manager%22+OR+%22Customer+Service+Representative%22+OR+Servicing+OR+unit+OR+architect+OR+assistantship+OR+Pyk2+OR+estrogen+OR+fellow+OR+%22business+coordinator%22+%22medical+assistant%22+underwriting+OR+underwriter+OR+creative+OR+Maintenance+OR+servicing%29+%29+AND+NOT+%28searchableCompanyName%3A%28cancer+OR+Magnolias+OR+Warner+OR+Nokia+OR+IBM+OR+%22Bank+of+America%22+OR+Walmart+OR+HP+OR+JPMorgan+OR+ALSTOM+OR+%22HR+Dynamics%22+OR+%22Fegs+Health%22+OR+AT&T+OR+At-tech+OR+Scipher+OR+%22Occupational+Health%22+OR+Sinai%29+%29/ws-50/sb-rd', '23226', '2', 'r', 'mediationjobs.jobamatic.com', $ipaddress);
if ($response)
{
//	$xml = simplexml_load_string($response); 
	$xmlObject = new SimpleXMLElement($response);

//  print_r($xml);
}else
{
  echo "Error loading feed"; 
}

foreach($xmlObject->rs->r AS $key=>$value)
	{
	$jobtitle = $value->jt; // The $jobtitle as is may not be in a consistent title-case for all job postings, so I must convert it to make it consistent as follows.
	$jobtitle = strtolower($jobtitle); // First make it all lower-case...
	$jobtitle = ucwords($jobtitle); // ... then make initial capitals upper-case.
	
	// Further perfect by ensuring that the first letter after a slash gets capitalized (it remains lower-case otherwise).
	$jobtitlearray = explode('/', $jobtitle); // Explode the string around the slash character.
	for ($count=0; $count < sizeof($jobtitlearray); $count++) // Loop for as many elements as we have in the array
		{
		$jobtitlearray[$count] = ucfirst($jobtitlearray[$count]); // Create an initial capital for each of the exploded particles (array elements).
		}
	$jobtitle = implode('/', $jobtitlearray); // Join up the array elements to reconstitute a string.

	// Further perfect by ensuring that the first letter after an open parenthesis "(" gets capitalized (it remains lower-case otherwise).
	$jobtitlearray = explode('(', $jobtitle); // Explode the string around the parenthesis character.
	for ($count=0; $count < sizeof($jobtitlearray); $count++) // Loop for as many elements as we have in the array
		{
		$jobtitlearray[$count] = ucfirst($jobtitlearray[$count]); // Create an initial capital for each of the exploded particles (array elements).
		}
	$jobtitle = implode('(', $jobtitlearray); // Join up the array elements to reconstitute a string.
	
	$joblocation = $value->loc; // Nicer variable name

	// Miscellaneous tidy-ups:
	$jobtitle = str_replace('--', '-', $jobtitle);
	$jobtitle = str_replace('-', '&ndash;', $jobtitle);
	$jobtitle = str_replace('Bbb', 'BBB', $jobtitle);
	$jobtitle = str_replace('Er', 'ER', $jobtitle);
	$jobtitle = str_replace('s.', 'S.', $jobtitle);
	$jobtitle = str_replace('Mba', 'MBA', $jobtitle);
	$jobtitle = str_replace('Adr', 'ADR', $jobtitle);
	$jobtitle = str_replace('And', '&amp;', $jobtitle);
	$jobtitle = str_replace('Ip ', 'IP ', $jobtitle);
	$jobtitle = str_replace('Ii', 'II', $jobtitle);
	$jobtitle = str_replace(' Ii ', ' II ', $jobtitle);
	$jobtitle = str_replace('/Ii ', '/II ', $jobtitle);
	$jobtitle = str_replace('Ii/', 'II/', $jobtitle);
	$jobtitle = str_replace(' Iii ', ' III ', $jobtitle);
	$jobtitle = str_replace('/Iii ', '/III ', $jobtitle);
	$jobtitle = str_replace('Iii/', 'III/', $jobtitle);
	$jobtitle = str_replace(' Iv ', ' IV ', $jobtitle);
	$jobtitle = str_replace('/Iv ', '/IV ', $jobtitle);
	$jobtitle = str_replace('Iv/', 'IV/', $jobtitle);
	$jobtitle = str_replace(' /', '/', $jobtitle);
	$jobtitle = str_replace('/ ', '/', $jobtitle);
	$jobtitle = str_replace(' A ', ' a ', $jobtitle);
	$jobtitle = str_replace(' For ', ' for ', $jobtitle);
	$jobtitle = str_replace(' The ', ' the ', $jobtitle);
	$jobtitle = str_replace(' In ', ' in ', $jobtitle);
	$jobtitle = str_replace(' On ', ' on ', $jobtitle);
	$jobtitle = str_replace(' To ', ' to ', $jobtitle);
	$jobtitle = str_replace(' With ', ' with ', $jobtitle);
	$jobtitle = str_replace(' Of ', ' of ', $jobtitle);
	$jobtitle = str_replace('monday', 'mMnday', $jobtitle);
	$jobtitle = str_replace('tuesday', 'Tuesday', $jobtitle);
	$jobtitle = str_replace('wednesday', 'Wednesday', $jobtitle);
	$jobtitle = str_replace('thursday', 'Thursday', $jobtitle);
	$jobtitle = str_replace('friday', 'Friday', $jobtitle);
	$jobtitle = str_replace('Tx', 'TX', $jobtitle);
	$jobtitle = str_replace('Ohio Us', 'Ohio', $jobtitle);
	$jobtitle = str_replace('Fl ', 'FL ', $jobtitle);
	$jobtitle = str_replace('La ', 'LA ', $jobtitle);
	$joblocation = str_replace(', Dc', ', DC', $joblocation);
	$joblocation = str_replace('Mcc', 'McC', $joblocation);

	echo '<div class="dirhead">'.$jobtitle.'</div>';
	echo '<div class="dirlist"><span class="smallcaps">Arbitration Job Location:</span> '.$joblocation.'</div>';
	if ($value->cn != '') echo '<div class="dirlist"><span class="smallcaps">Company Name:</span> '.$value->cn.'</div>';
	$excerpt = $value->e;
	$excerpt = ucfirst($excerpt);
	echo '<div class="dirlist" style="margin-right: 50px;"><span class="smallcaps">Excerpt from job description:</span> '.$excerpt;
	foreach ($value->src[0]->attributes() as $a => $b)
		{
//	    echo $a,' is: "',$b,"\"<br />";
	    $theurl = $b;
		}
	echo ' <a target="secondwindow" href="'.$theurl.'">[job details]</a></div>';
//	foreach ($value->loc[0]->attributes() as $a => $b)
//		{
//		echo $b," | ";
//		if ($b->attributeName != '') echo $b->attributeName;
//		}
    $datetimeposted = $value->dp; // The xml object's <dp> element provides the date in a datetime format of, for example, 2010-07-23T10:20:15Z. We need to convert this into something more user-friendly. (I use method courtesy http://www.hawkee.com/snippet/393/)
	$dateposted = substr($datetimeposted, 0, 10); // Just obtain the date portion i.e. the first 10 characters of the string.
	list($year, $month, $day) = split("-", $dateposted); // For example of 2010-07-23, will assign 2010 to $year, 07 to $month, and 23 to $day.
    $reformatteddate = date('F j, Y', mktime(0, 0, 0, $month, $day, $year));
 
	echo '<div class="dirlist"><span class="smallcaps">Date Job Posted:</span> '.$reformatteddate.'</div>';
	}
?>
</div>
<!-- InstanceEndEditable -->

<div id="footer">
<?php
require ("/home/paulme6/public_html/medtrainings/ssi/footer.php");
?>
</div>

</div>
</div>

<div id="topbar"><a href="/index.php"><img src="/images/mediationtraininglogo.jpg" alt="mediation training logo" border="0"></a></div>

<hr id="horzmenuline" color="#9C151C" width="0" noshade />

<!-- Place this tag where you want the +1 button to render (Note: I have two +1 buttons on each page.) -->
<span id="plusone"><g:plusone size="small" count="false" href="<?php
function curPageURL() // Courtesy: http://www.webcheatsheet.com/PHP/get_current_page_url.php
	{
	$pageURL = 'http';
	if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80")
		{
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} 
	else
		{
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
	return $pageURL;
	}
echo curPageURL();
?>
"></g:plusone></span>
<!--  Place this tag after the last plusone tag -->
<script type="text/javascript">
  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>

<!-- InstanceBeginEditable name="EditRegion4" --><!-- InstanceEndEditable -->
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
<!-- InstanceEnd --></html>
