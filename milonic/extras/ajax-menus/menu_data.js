fixMozillaZIndex=true; //Fixes Z-Index problem  with Mozilla browsers but causes odd scrolling problem, toggle to see if it helps
_menuCloseDelay=500;
_menuOpenDelay=150;
_subOffsetTop=2;
_subOffsetLeft=-2;



/* This is the "Loading Menu" feature. Normal menu style and menu properties are used for this */
with(ajaxLoadingStyle=new mm_style()){
	bordercolor="#999999";
	borderstyle="solid";
	borderwidth=1;
	fontfamily="Verdana, Tahoma, Arial";
	fontsize="10px"
	padding=5
	offcolor="#000000"
	offbgcolor="ffffe1"
	overfilter="Shadow(color=#777777', Direction=135, Strength=3)";
	
}

with(milonic=new menuname("ajaxLoading")){
	style=ajaxLoadingStyle;
	top="offset=10"
	left="offset=10"
	aI("text=Menu Loading, Please Wait...;disabled=true;")
}
/* Code to handle the above menu is stored in mm_browserapi.js and mmenudom.js */



with(menuStyle=new mm_style()){
bordercolor="#999999";
borderstyle="solid";
borderwidth=1;
fontfamily="Verdana, Tahoma, Arial";
fontsize="75%";
fontstyle="normal";
headerbgcolor="#ffffff";
headercolor="#000000";
offbgcolor="#eeeeee";
offcolor="#000000";
onbgcolor="#ddffdd";
oncolor="#000099";
outfilter="randomdissolve(duration=0.3)";
overfilter="Fade(duration=0.2);Alpha(opacity=90);Shadow(color=#777777', Direction=135, Strength=3)";
padding=4;
pagebgcolor="#82B6D7";
pagecolor="black";
separatorcolor="#999999";
separatorsize=1;
subimage="arrow.gif";
subimagepadding=2;
}

with(milonic=new menuname("Main Menu")){
alwaysvisible=1;
left=10;
orientation="horizontal";
style=menuStyle;
top=10;
aI("text=Home;url=http://www.milonic.com/;");
aI("showmenu=ajax:samples-menu.txt;text=Menu Samples;");
aI("showmenu=ajax:milonic-menu.txt;text=Milonic;");
aI("showmenu=ajax:partners-menu.txt;text=Partners;");
aI("showmenu=ajax:links-menu.txt;text=Links;");
aI("showmenu=ajax:my-milonic-menu.txt;text=My Milonic;");
}

drawMenus();

