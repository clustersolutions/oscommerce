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

function flagCheckboxes(element) {
  var elementForm = element.form;
  var i = 0;

  for (i = 0; i < elementForm.length; i++) {
    if (elementForm[i].type == 'checkbox') {
      elementForm[i].checked = element.checked;
    }
  }
}

// Returns array with x,y page scroll values
// Core code from - quirksmode.org
function getPageScroll() {
  var yScroll;

  if (self.pageYOffset) {
    yScroll = self.pageYOffset;
  } else if (document.documentElement && document.documentElement.scrollTop) { // Explorer 6 Strict
    yScroll = document.documentElement.scrollTop;
  } else if (document.body) { // all other Explorers
    yScroll = document.body.scrollTop;
  }

  arrayPageScroll = new Array('', yScroll);

  return arrayPageScroll;
}

// Returns array with page width, height and window width, height
// Core code from - quirksmode.org
// Edit for Firefox by pHaez
function getPageSize() {
  var xScroll, yScroll;

  if (window.innerHeight && window.scrollMaxY) {
    xScroll = document.body.scrollWidth;
    yScroll = window.innerHeight + window.scrollMaxY;
  } else if (document.body.scrollHeight > document.body.offsetHeight) { // all but Explorer Mac
    xScroll = document.body.scrollWidth;
    yScroll = document.body.scrollHeight;
  } else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari
    xScroll = document.body.offsetWidth;
    yScroll = document.body.offsetHeight;
  }

  var windowWidth, windowHeight;

  if (self.innerHeight) { // all except Explorer
    windowWidth = self.innerWidth;
    windowHeight = self.innerHeight;
  } else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
    windowWidth = document.documentElement.clientWidth;
    windowHeight = document.documentElement.clientHeight;
  } else if (document.body) { // other Explorers
    windowWidth = document.body.clientWidth;
    windowHeight = document.body.clientHeight;
  }

// for small pages with total height less then height of the viewport
  if (yScroll < windowHeight) {
    pageHeight = windowHeight;
  } else {
    pageHeight = yScroll;
  }

// for small pages with total width less then width of the viewport
  if (xScroll < windowWidth) {
    pageWidth = windowWidth;
  } else {
    pageWidth = xScroll;
  }

  arrayPageSize = new Array(pageWidth, pageHeight, windowWidth, windowHeight);

  return arrayPageSize;
}
