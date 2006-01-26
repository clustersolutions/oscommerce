function rowOverEffect(object) {
  object.className = 'mouseOver';
}

function rowOutEffect(object) {
  object.className = '';
}

function updateDatePullDownMenu(objForm, fieldName) {
  var pdmDays = fieldName + "_days";
  var pdmMonths = fieldName + "_months";
  var pdmYears = fieldName + "_years";

  time = new Date(objForm[pdmYears].options[objForm[pdmYears].selectedIndex].text, objForm[pdmMonths].options[objForm[pdmMonths].selectedIndex].value, 1);

  time = new Date(time - 86400000);

  var selectedDay = objForm[pdmDays].options[objForm[pdmDays].selectedIndex].text;
  var daysInMonth = time.getDate();

  for (var i=0; i<objForm[pdmDays].length; i++) {
    objForm[pdmDays].options[0] = null;
  }

  for (var i=0; i<daysInMonth; i++) {
    objForm[pdmDays].options[i] = new Option(i+1);
  }

  if (selectedDay <= daysInMonth) {
    objForm[pdmDays].options[selectedDay-1].selected = true;
  } else {
    objForm[pdmDays].options[daysInMonth-1].selected = true;
  }
}

function toggleDivBlocks(group, exempt) {
  if (!document.getElementsByTagName) return null;

  if (!exempt) exempt = "";

  var divs = document.getElementsByTagName("div");

  for(var i=0; i < divs.length; i++) {
    var div = divs[i];
    var id = div.id;

    if ((id != exempt) && (id.indexOf(group) == 0)) {
      hideBlock(id);
    }
  }

  showBlock(exempt);
}

function toggleInfoBox(exempt) {
  if (!exempt || !document.getElementsByTagName) return null;

  var infoBox = "infoBox_" + exempt;

  var divs = document.getElementsByTagName("div");

  for(var i=0; i < divs.length; i++) {
    var div = divs[i];
    var id = div.id;

    if (id.indexOf("infoBox_") == 0) {
      var infoBoxForm = id.substring(8);

      if (document.forms[infoBoxForm]) {
        document.forms[infoBoxForm].reset();
      }

      if (id != infoBox) {
        hideBlock(id);
      }
    }
  }

  showBlock(infoBox);
}

function showBlock(id) {
  if (document.getElementById) {
    itm = document.getElementById(id);
  } else if (document.all){
    itm = document.all[id];
  } else if (document.layers){
    itm = document.layers[id];
  }

  if (itm) {
    itm.style.display = "block";
  }
}

function hideBlock(id) {
  if (document.getElementById) {
    itm = document.getElementById(id);
  } else if (document.all){
    itm = document.all[id];
  } else if (document.layers){
    itm = document.layers[id];
  }

  if (itm) {
    itm.style.display = "none";
  }
}

function toggleClass(removeClass, addClass, cssClass, tagName) {
  if (!document.getElementsByTagName) return null;

  if (!tagName) tagName = "div";

  var tags = document.getElementsByTagName(tagName);

  for(var i=0; i < tags.length; i++) {
    var tag = tags[i];
    var id = tag.id;

    if ((id != addClass) && (id.indexOf(removeClass) == 0)) {
      tag.className = "";
    }
  }

  document.getElementById(addClass).className = cssClass;
}

function selectAllFromPullDownMenu(field) {
  var field = document.getElementById(field);

  for (i=0; i < field.length; i++) {
    field.options[i].selected = true;
  }
}

function resetPullDownMenuSelection(field) {
  var field = document.getElementById(field);

  for (i=0; i < field.length; i++) {
    field.options[i].selected = false;
  }
}

/*@cc_on
@if (@_jscript_version >= 5.5 && @_win32)
// Correctly handle PNG transparency in Win IE 5.5 or higher.
// http://homepage.ntlworld.com/bobosola. Updated 02-March-2004
function correctPNG() {
  for(var i=0; i<document.images.length; i++) {
    var img = document.images[i]
    var imgName = img.src.toUpperCase()
    if (imgName.substring(imgName.length-3, imgName.length) == "PNG") {
      var imgID = (img.id) ? "id='" + img.id + "' " : ""
      var imgClass = (img.className) ? "class='" + img.className + "' " : ""
      var imgTitle = (img.title) ? "title='" + img.title + "' " : "title='" + img.alt + "' "
      var imgStyle = "display:inline-block;" + img.style.cssText
      if (img.align == "left") imgStyle = "float:left;" + imgStyle
      if (img.align == "right") imgStyle = "float:right;" + imgStyle
      if (img.parentElement.href) imgStyle = "cursor:hand;" + imgStyle
      var strNewHTML = "<span " + imgID + imgClass + imgTitle + " style=\"" + "width:" + img.width + "px; height:" + img.height + "px;" + imgStyle + ";" + "filter:progid:DXImageTransform.Microsoft.AlphaImageLoader" + "(src=\'" + img.src + "\', sizingMethod='scale');\"></span>"
      img.outerHTML = strNewHTML
      i = i-1
    }
  }
}
window.attachEvent("onload", correctPNG);
@end @*/
