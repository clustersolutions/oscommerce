/*
  $Id: general.js,v 1.4 2004/04/16 00:02:02 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

function SetFocus(TargetFormName) {
  var target = 0;
  if (TargetFormName != "") {
    for (i=0; i<document.forms.length; i++) {
      if (document.forms[i].name == TargetFormName) {
        target = i;
        break;
      }
    }
  }

  var TargetForm = document.forms[target];

  for (i=0; i<TargetForm.length; i++) {
    if ( (TargetForm.elements[i].type != "image") && (TargetForm.elements[i].type != "hidden") && (TargetForm.elements[i].type != "reset") && (TargetForm.elements[i].type != "submit") ) {
      TargetForm.elements[i].focus();

      if ( (TargetForm.elements[i].type == "text") || (TargetForm.elements[i].type == "password") ) {
        TargetForm.elements[i].select();
      }

      break;
    }
  }
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
