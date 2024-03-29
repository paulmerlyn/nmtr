// Ref. http://javascript.internet.com/forms/character-counter.html
/* This script and many more are available free online at
The JavaScript Source!! http://javascript.internet.com
Created by: Steve | http://jsmadeeasy.com/ */
function getObject(obj) {
  var theObj;
  if(document.all) {
    if(typeof obj=="string") {
      return document.all(obj);
    } else {
      return obj.style;
    }
  }
  if(document.getElementById) {
    if(typeof obj=="string") {
      return document.getElementById(obj);
    } else {
      return obj.style;
    }
  }
  return null;
}

function toCount(entrance,exit,text,characters) {
  var entranceObj=getObject(entrance);
  var exitObj=getObject(exit);
  var length=characters - entranceObj.value.length;
  if(length <= 0) {
    length=0;
    text='<span class="disable"> '+text+' </span>';
    entranceObj.value=entranceObj.value.substr(0,characters);
  }
  exitObj.innerHTML = text.replace("{CHAR}",length);
}

function charCount(entrance,exit,text) {
  var entranceObj=getObject(entrance);
  var exitObj=getObject(exit);
  var length=entranceObj.value.length;
  exitObj.innerHTML = text.replace("{CHAR}",length);
}
