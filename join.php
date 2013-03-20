<?php
ob_start();
session_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><!-- InstanceBegin template="/Templates/MasterTemplateFlexImage.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<!-- InstanceBeginEditable name="doctitle" -->
<title>Join the National Mediation Training Registry</title>
<meta NAME="description" CONTENT="Join the National Mediation Training Registry">
<meta NAME="keywords" CONTENT="">
<!-- InstanceEndEditable -->
<link href="/mediationtraining.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="/milonic/milonic_src.js" type="text/javascript"></SCRIPT>
<script language="JavaScript" type="text/javascript">
if(ns4)_d.write("<scr"+"ipt language='JavaScript' src='/milonic/mmenuns4.js'><\/scr"+"ipt>");
else _d.write("<scr"+"ipt language='JavaScript' src='/milonic/mmenudom.js'><\/scr"+"ipt>"); </script>
<SCRIPT language='JavaScript' src="/milonic/menu_data.js" type='text/javascript'></SCRIPT>
<link rel="shortcut icon" href="/favicon.ico">
<!-- InstanceBeginEditable name="head" -->
<!-- Start: The following javascripts pertain to Trio Solutions's glossary and image preview -->
<script language='JavaScript' type='text/javascript' src='TSScript/yahoo.js'></script>
<script language='JavaScript' type='text/javascript' src='TSScript/event.js'></script>
<script language='JavaScript' type='text/javascript' src='TSScript/dom.js'></script>
<script language='JavaScript' type='text/javascript' src='TSScript/dragdrop.js'></script>
<script language='JavaScript' type='text/javascript' src='TSScript/animation.js'></script>
<script language='JavaScript' type='text/javascript' src='TSScript/container.js'></script>
<script language='JavaScript' type='text/javascript' src='TSScript/TSPreviewImage/TSPreviewImage.js'></script>
<script language='JavaScript' type='text/javascript' src='TSScript/TSGlossary/TSGlossary.js'></script>
<link href='TSScript/TSContainer.css' rel='stylesheet' type='text/css'>
<link rel='stylesheet' type='text/css' href='TSScript/TSGlossary/TSGlossary.css' />
<!-- End: Trio Solutions's glossary and image preview -->
<!-- InstanceEndEditable -->
</head>

<body>
<div id="main">
<div id="relwrapper">
<!-- InstanceBeginEditable name="Content" -->
<?php
// Set the monthly fee as a session variable. The value is required in other pages/scripts (notably, offramp.php and ipn.php), so it's a good idea to set it here once and then know that any changes to its value (which should also be made by editing the value of the session variable here) will automatically flow through to any other page. ALSO, if changing the value, remember to change it in the PayPal payment buttons on the PayPal site (I'm using a PayPal-hosted button on this page, whereas the buttons used on the New Resolution Platform page are not hosted) and then copy/paste the TWO instances of the button on this join.php page.
$_SESSION['MonthlySubscription'] = 19.95; // Set the monthly subscription fee once here (USD)
?>
<div style="text-align: center; margin-top: 20px; margin-bottom: 30px;">
<!-- See field definitions and sample code at https://www.paypalobjects.com/en_US/ebook/subscriptions/html.html. Note I decided not to use a PayPal hosted button b/c it's too inflexible. -->
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_xclick-subscriptions">
<input type="hidden" name="business" value="ZGV4J85ZEA8B8"> <!-- Merchant account ID or email address -->
<!-- Including a page_style field ensures that my PayPal account (which is shared by the MediationCareer.org and NMTR sites) displays the NMTR banner (not the MediationCareer.org banner) when a user visits the PayPal payment page. -->
<input type="hidden" name="page_style" value="NMTR">
<input type="hidden" name="item_name" value="NMTR_Monthly_Subscription">
<input type="hidden" name="item_number" value="">
<!-- a3, p3, and t3 pertain to the full subscription fee; a1, a2, p1, p2, t1, and t2 pertain to amounts for prior trial periods -->
<input type="hidden" name="a3" value="<?=$_SESSION['MonthlySubscription']; ?>"><!-- Thus, the monthly subscription fee gets posted as the value of the PayPal button's 'amount' field. -->
<input type="hidden" name="p3" value="1"><!-- p3 is the absolute value of the period (i.e payment interval). The interval is 1 month, so p3 is "1" -->
<input type="hidden" name="t3" value="M"><!-- t3 is the unit of the payment interval -- M for month, Y for year, etc. The interval is 1 month, so t3 is "M" -->
<input type="hidden" name="src" value="1"><!-- Set to 1 to ensure subscription occurs indefinitely -->
<input type="hidden" name="sra" value="1"><!-- Set to 1 to ensure a failed attempt to collect a payment gets retried up to two times more -->
<input type="hidden" name="no_note" value="1">
<!-- Deny buyers the opportunity to write a text note with their order -->
<input type="hidden" name="currency_code" value="USD">
<input type="hidden" name="return" value="http://www.mediationtrainings.org/offramp.php">
<input type="hidden" name="cancel_return" value="http://www.mediationtrainings.org/join.php">
 <input type="hidden" name="custom" value="join"> <!-- The value of the 'custom' field is used by offramp.php to distinguish between payments for joining NMTR (first-timers; value = "join") vs people who had previously been listed on the NMTR for free and who now are paying to "activate" their accounts (value of the preexisting trainer's TrainerID from trainers_table) in order to remain listed in the NMTR. -->
<input type="image" src="http://www.mediationtrainings.org/images/JoinButton.png" border="0" width="175" height="79" name="submit" alt="PayPal - The safer, easier way to pay online!">
</form>
</div>

<h1>Why It Pays to Join the National Mediation Training Registry?</h1>
<h2>Reason #1: Visibility Matters</h2>
<p>Did you know, more than <a href="#Link608170C0" id="Link608170C0" style="cursor:help" onMouseOver="javascript:createPreviewImage('TSPreviewImagePanelID608170', 'Google searches for \'mediation training\' (last month, U.S. only)', 'images/MediationTrainingSearches.png', 'Link608170C0',748,510, true)">18,000 people <span style="font-size: 10px;">[click]</span></a> searched on Google for &ldquo;<a href="#Link617778C0" id="Link617778C0" style="cursor:help" onMouseOver="javascript:createPreviewImage('TSPreviewImagePanelID617778', 'Google searches for \'mediation training\' (last month, U.S. only)', 'images/MediationTrainingSearches.png', 'Link617778C0',748,510, true)">mediation training</a>&rdquo; last month? An additional <a href="#Link405939C0" id="Link405939C0" style="cursor:help" onMouseOver="javascript:createPreviewImage('TSPreviewImagePanelID405939', 'Google searches for \'mediator training\' (last month, U.S. only)', 'images/MediatorTrainingSearches.png', 'Link405939C0',748,510, true)">6600 people <span style="font-size: 10px;">[click]</span></a> searched for &ldquo;<a href="#Link990932C0" id="Link990932C0" style="cursor:help" onMouseOver="javascript:createPreviewImage('TSPreviewImagePanelID990932', 'Google searches for \'mediator training\' (last month, U.S. only)', 'images/MediatorTrainingSearches.png', 'Link990932C0',748,510, true)">mediator training</a>&rdquo;. When they do, do they find <em>your</em> web site?</p>

<p>They find ours. They find it on every major search engine &mdash; and usually we&rsquo;re in first place. That means the National Mediation Training Registry is likely the first (and often, the last) click for those 24,000+ searchers (18,000 + 6600) every month. But don&rsquo;t take our word for it. Look for yourself.</p>

<table style="margin-left: 50px; margin-right: 50px; margin-top: 30px; margin-bottom: 30px; width: 90%; font-family: Geneva, Arial, Helvetica, sans-serif; font-size: 14px; border-collapse: collapse;" class="basictext" frame="hsides">
<tr style="font-weight: bold;">
<td>
Search Term</td>
<td colspan="5" style="text-align: right;">
# Google searches last month (U.S. only)</td>
</tr>
<tr>
<td>
mediation training
</td>
<td colspan="5" style="text-align: center;">
18,100
</td>
</tr>
<tr>
<td colspan="6" style="height: 6px; border-top: 1px dashed black"></td>
</tr>
<tr>
<td colspan="2" style="font-weight: bold;">
</td>
<td style="font-weight: bold; width: 80px; text-align: center;">
Google
</td>
<td style="font-weight: bold; width: 80px; text-align: center;">
Bing
</td>
<td style="font-weight: bold; width: 80px; text-align: center;">
Ask.com</td>
<td style="font-weight: bold; width: 80px; text-align: center;">
AoL.com
</td>
</tr>
<tr>
<td colspan="2">
National Mediation Training Registry&rsquo;s search ranking
</td>
<td style="text-align: center;">
  <a href="#Link191494C0" id="Link191494C0" style="cursor:help" onMouseOver="javascript:createPreviewImage('TSPreviewImagePanelID191494', 'Number 1 on Google for \'mediation training\'', 'images/MediationTrainingGoogle.png', 'Link191494C0',748,510)">1<sup>st</sup>&nbsp;<span style="font-size: 10px;">[click]</span></a>
</td>
<td style="text-align: center;">
3<sup>rd</sup>
</td>
<td style="text-align: center;">
1<sup>st</sup>
</td>
<td style="text-align: center;">
1<sup>st</sup>
</td>
</tr>
</table>
<br>

<table style="margin-left: 50px; margin-right: 50px; margin-top: 30px; margin-bottom: 30px; width: 90%; font-family: Geneva, Arial, Helvetica, sans-serif; font-size: 14px; border-collapse: collapse;" class="basictext" frame="hsides">
<tr style="font-weight: bold;">
<td>
Search Term</td>
<td colspan="5" style="text-align: right;">
# Google searches last month (U.S. only)</td>
</tr>
<tr style="height: 30px; vertical-align: top;">
<td>
mediator training
</td>
<td colspan="5" style="text-align: center;">
6600
</td>
</tr>
<tr>
<td colspan="6" style="height: 6px; border-top: 1px dashed black"></td>
</tr>
<tr>
<td colspan="2" style="font-weight: bold;">
</td>
<td style="font-weight: bold; width: 80px; text-align: center;">
Google
</td>
<td style="font-weight: bold; width: 80px; text-align: center;">
Bing
</td>
<td style="font-weight: bold; width: 80px; text-align: center;">
Ask.com</td>
<td style="font-weight: bold; width: 80px; text-align: center;">
AoL.com
</td>
</tr>
<tr>
<td colspan="2">
National Mediation Training Registry&rsquo;s search ranking
</td>
<td style="text-align: center;"><a href="#Link292467C0" id="Link292467C0" style="cursor:help" onMouseOver="javascript:createPreviewImage('TSPreviewImagePanelID292467', 'Google search results for \'mediator training\'', 'images/MediatorTrainingGoogle.png', 'Link292467C0',748,510)">2<sup>nd</sup>&nbsp;<span style="font-size: 10px;">[click]</span></a>
</td>
<td style="text-align: center;">8<sup>th</sup>
</td>
<td style="text-align: center;">
2<sup>nd</sup>
</td>
<td style="text-align: center;">
3<sup>rd</sup>
</td>
</tr>
</table>

<h2>Reason #2: It&rsquo;s a Financial No-Brainer</h2>
<p>How much do you charge for a training program? If it&rsquo;s $500, your inclusion in the  National Mediation Training Registry will more than pay for itself by serving up just <u>one trainee</u> in the next <u>two years</u>. If it&rsquo;s $1000, you&rsquo;ll be ahead financially with just one extra trainee in the next <u>four years</u>! Top ranking on Google for $<?=$_SESSION['MonthlySubscription']; ?> per month is a financial no-brainer!</p>

<h2>Reason #3: 64oz of Google Juice</h2>
<p>What is <em>Google juice</em>? (Click <a href="http://google.about.com/od/g/g/google_juice.htm" target="_blank">here</a> for the about.com definition.) Without getting technical, it&rsquo;s the boost in search engine visibility that your own web site gets when a highly ranked site such as ours links to your site. And because our site ranks #1 in Google, you&rsquo;re getting a turbo-charged boost from an extremely high-quality source. Listing in the Registry is like taking your own web site out to dinner and a movie!</p>

<p>Inclusion in the Registry costs just $<?=$_SESSION['MonthlySubscription']; ?> per month. There&rsquo;s no long-term contract, and whereas payments aren&rsquo;t refundable, you can cancel any time. Simply send a cancellation request to <script type='text/javascript'> var a = new Array('r','.','@med','port','sup','iation','trainings','o','g');document.write("<a href='mailto:"+a[4]+a[3]+a[2]+a[5]+a[6]+a[1]+a[7]+a[0]+a[8]+"'>"+a[4]+a[3]+a[2]+a[5]+a[6]+a[1]+a[7]+a[0]+a[8]+"</a>");</script> or click the <u>Unsubscribe</u> link (at the foot of every page).</p>
<div style="text-align: center; margin-top: 30px;">
<!-- See field definitions and sample code at https://www.paypalobjects.com/en_US/ebook/subscriptions/html.html. Note I decided not to use a PayPal hosted button b/c it's too inflexible. -->
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_xclick-subscriptions">
<input type="hidden" name="business" value="ZGV4J85ZEA8B8"> <!-- Merchant account ID or email address -->
<!-- Including a page_style field ensures that my PayPal account (which is shared by the MediationCareer.org and NMTR sites) displays the NMTR banner (not the MediationCareer.org banner) when a user visits the PayPal payment page. -->
<input type="hidden" name="page_style" value="NMTR">
<input type="hidden" name="item_name" value="NMTR_Monthly_Subscription">
<input type="hidden" name="item_number" value="">
<!-- a3, p3, and t3 pertain to the full subscription fee; a1, a2, p1, p2, t1, and t2 pertain to amounts for prior trial periods -->
<input type="hidden" name="a3" value="<?=$_SESSION['MonthlySubscription']; ?>"><!-- Thus, the monthly subscription fee gets posted as the value of the PayPal button's 'amount' field. -->
<input type="hidden" name="p3" value="1"><!-- p3 is the absolute value of the period (i.e payment interval). The interval is 1 month, so p3 is "1" -->
<input type="hidden" name="t3" value="M"><!-- t3 is the unit of the payment interval -- M for month, Y for year, etc. The interval is 1 month, so t3 is "M" -->
<input type="hidden" name="src" value="1"><!-- Set to 1 to ensure subscription occurs indefinitely -->
<input type="hidden" name="sra" value="1"><!-- Set to 1 to ensure a failed attempt to collect a payment gets retried up to two times more -->
<input type="hidden" name="no_note" value="1">
<!-- Deny buyers the opportunity to write a text note with their order -->
<input type="hidden" name="currency_code" value="USD">
<input type="hidden" name="return" value="http://www.mediationtrainings.org/offramp.php">
<input type="hidden" name="cancel_return" value="http://www.mediationtrainings.org/join.php">
 <input type="hidden" name="custom" value="join"> <!-- The value of the 'custom' field is used by offramp.php to distinguish between payments for joining NMTR (first-timers; value = "join") vs people who had previously been listed on the NMTR for free and who now are paying to "activate" their accounts (value of the preexisting trainer's TrainerID from trainers_table) in order to remain listed in the NMTR. -->
<input type="image" src="http://www.mediationtrainings.org/images/JoinButton.png" border="0" width="175" height="79" name="submit" alt="PayPal - The safer, easier way to pay online!">
</form>
</div>
<!-- InstanceEndEditable -->

<div id="footer">
<?php
require ("/home/paulme6/public_html/medtrainings/ssi/footer.php");
?>
</div>

</div>
</div>

<div id="topbar"><!-- InstanceBeginEditable name="EditRegion5" --><a href="/index.php"><img src="/images/JoinTrainerDirectory.jpg" alt="mediation training logo" border="0"></a><!-- InstanceEndEditable --></div>

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

<!-- InstanceBeginEditable name="EditRegion4" -->
<?php
ob_flush();
?>
<script type="text/jscript">
/* An Ajax-based preload method (which I really don't fully utilize compared to the full version) courtesy: http://perishablepress.com/press/2009/12/28/3-ways-preload-images-css-javascript-ajax/ */
window.onload = function() {
	setTimeout(function() {
		// preload image
		new Image().src = "http://www.mediationtrainings.org/images/MediationTrainingSearches.png";
		new Image().src = "http://www.mediationtrainings.org/images/MediatorTrainingSearches.png";
		new Image().src = "http://www.mediationtrainings.org/images/MediationTrainingGoogle.png";
		new Image().src = "http://www.mediationtrainings.org/images/MediatorTrainingGoogle.png";
	}, 1000);
};
</script>
<!-- InstanceEndEditable -->
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
<!-- InstanceEnd -->
</html>
