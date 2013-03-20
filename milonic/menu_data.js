fixMozillaZIndex=true; //Fixes Z-Index problem  with Mozilla browsers but causes odd scrolling problem, toggle to see if it helps
_menuCloseDelay=500;
_menuOpenDelay=150;
_subOffsetTop=2;
_subOffsetLeft=-2;




with(submenuStyle=new mm_style()){
styleid=1;
bordercolor="#9C151C";
borderstyle="solid";
borderwidth=2;
fontfamily="Geneva, Verdana, Tahoma, Arial,sans-serif";
fontsize="8pt";
fontstyle="normal";
fontweight="normal";
headercolor="#000000";
imagepadding=8;
offbgcolor="#ffffff";
offcolor="#9C151C";
onbgcolor="#DBF5E3"; // Now pale shade of green; was #E7E6E7 grey
oncolor="#42543D"; // green
outfilter="Blinds( Bands=1,direction=up, duration=0.3)";
overfilter="Blinds( Bands=1,direction=down, duration=0.3)";
padding=4;
pagecolor="black";
separatorcolor="#ffffff";
separatorsize=1;
subimagepadding=8;
}

with(menuStyle=new mm_style()){
bordercolor="#9C151C"; // plum red
borderstyle="solid";
borderwidth=2;
fontfamily="Geneva, Tahoma, Arial, sans-serif";
fontsize="10pt";
fontstyle="normal";
fontweight="bold";
headerbgcolor="#ffffff";
headercolor="#000000";
imagepadding=6;
offbgcolor="#ffffff";
offcolor="#9C151C"; // plum
onbgcolor="#FFFFFF";
oncolor="#42543D"; // green #006633
outfilter="Fade(Overlap=1.00)";
padding=6;
}

with(milonic=new menuname("Main Menu")){
alwaysvisible=1;
orientation="horizontal";
screenposition="center";
top=240;
style=menuStyle;
aI("text=&nbsp;Home&nbsp;&nbsp;&nbsp;&nbsp;;url=/index.php;");
aI("text=About&nbsp;&nbsp;&nbsp;&nbsp;;url=/about.php;");
aI("text=Join&nbsp;&nbsp;&nbsp;&nbsp;;url=/join.php;");
aI("text=Training&nbsp;&nbsp;&nbsp;&nbsp;;url=/mediationtraining.php;");
aI("showmenu=Jobs;text=Jobs&nbsp;&nbsp;&nbsp;&nbsp;;");
aI("text=Careers&nbsp;&nbsp;&nbsp;&nbsp;;url=/mediationcareers.php;");
aI("text=Extras&nbsp;&nbsp;&nbsp;&nbsp;;url=/mediationroleplay.php;");
/* aI("text=Forum&nbsp;&nbsp;&nbsp;&nbsp;url=/forum/mediationforum.php;"); I ABANDONED THIS FORUM B/C IT WASN'T PATRONIZED */
aI("showmenu=Registry;text=Registry&nbsp;&nbsp;&nbsp;;");
aI("showmenu=Search;text=Search&nbsp;;");
}

with(milonic=new menuname("Careers")){
style=submenuStyle;
align="left";
aI("text=Become a Mediator;url=/mediationcareers.php;");
}

with(milonic=new menuname("Jobs")){
style=submenuStyle;
align="left";
aI("text=Mediation Jobs;url=/mediationjobs.php;");
aI("text=Arbitration Jobs;url=/arbitrationjobs.php;");
}

with(milonic=new menuname("Extras")){
style=submenuStyle;
align="left";
aI("text=Mediation Role-Play;url=/mediationroleplay.php;");
}

with(milonic=new menuname("Registry")){
style=submenuStyle;
align="left";
aI("text=Create Trainer Profile;url=/addtrainer.php;");
aI("text=Edit/Delete Trainer Profile;url=/edittrainer.php;");
aI("text=Add Training Event;url=/addevent.php;");
aI("text=Edit/Delete Training Event;url=/editevent.php;");
aI("text=Remind Me of My Username/Password;url=/userpassreminder.php;");
}

with(milonic=new menuname("Search")){
style=submenuStyle;
align="left";
aI("text=Power Search;url=/powersearch.php;");
aI("text=Simple Search;url=/simplesearch.php;");
}

with(milonic=new menuname("Links")){
style=submenuStyle;
aI("text=Apache Web Server;url=http://www.apache.org/;");
aI("text=MySQL Database Server;url=http://ww.mysql.com/;");
aI("text=PHP - Development;url=http://www.php.net/;");
aI("text=phpBB Web Forum System;url=http://www.phpbb.net/;");
aI("showmenu=Anti Spam;text=Anti Spam Tools;");
}

with(milonic=new menuname("Anti Spam")){
style=submenuStyle;
aI("text=Spam Cop;url=http://www.spamcop.net/;");
aI("text=Mime Defang;url=http://www.mimedefang.org/;");
aI("text=Spam Assassin;url=http://www.spamassassin.org/;");
}

with(milonic=new menuname("MyMilonic")){
style=submenuStyle;
aI("text=Login;url=http://www.milonic.com/login.php;");
aI("text=Licenses;url=http://www.milonic.com/mylicenses.php;");
aI("text=Invoices;url=http://www.milonic.com/myinvoices.php;");
aI("text=Make Support Request;url=http://www.milonic.com/reqsupport.php;");
aI("text=View Support Requests;url=http://www.milonic.com/mysupport.php;");
aI("text=Your Details;url=http://www.milonic.com/mydetails.php;");
}

drawMenus();

