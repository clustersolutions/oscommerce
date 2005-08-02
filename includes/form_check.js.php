<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/
?>
<script language="javascript"><!--
var form = "";
var submitted = false;
var error = false;
var error_message = "";

function check_input(field_name, field_size, message) {
  if (form.elements[field_name] && (form.elements[field_name].type != "hidden")) {
    var field_value = form.elements[field_name].value;

    if (field_value == '' || field_value.length < field_size) {
      error_message = error_message + "* " + message + "\n";
      error = true;
    }
  }
}

function check_radio(field_name, message) {
  var isChecked = false;

  if (form.elements[field_name] && (form.elements[field_name].type != "hidden")) {
    var radio = form.elements[field_name];

    for (var i=0; i<radio.length; i++) {
      if (radio[i].checked == true) {
        isChecked = true;
        break;
      }
    }

    if (isChecked == false) {
      error_message = error_message + "* " + message + "\n";
      error = true;
    }
  }
}

function check_select(field_name, field_default, message) {
  if (form.elements[field_name] && (form.elements[field_name].type != "hidden")) {
    var field_value = form.elements[field_name].value;

    if (field_value == field_default) {
      error_message = error_message + "* " + message + "\n";
      error = true;
    }
  }
}

function check_password(field_name_1, field_name_2, field_size, message_1, message_2) {
  if (form.elements[field_name_1] && (form.elements[field_name_1].type != "hidden")) {
    var password = form.elements[field_name_1].value;
    var confirmation = form.elements[field_name_2].value;

    if (password == '' || password.length < field_size) {
      error_message = error_message + "* " + message_1 + "\n";
      error = true;
    } else if (password != confirmation) {
      error_message = error_message + "* " + message_2 + "\n";
      error = true;
    }
  }
}

function check_password_new(field_name_1, field_name_2, field_name_3, field_size, message_1, message_2, message_3) {
  if (form.elements[field_name_1] && (form.elements[field_name_1].type != "hidden")) {
    var password_current = form.elements[field_name_1].value;
    var password_new = form.elements[field_name_2].value;
    var password_confirmation = form.elements[field_name_3].value;

    if (password_current == '' || password_current.length < field_size) {
      error_message = error_message + "* " + message_1 + "\n";
      error = true;
    } else if (password_new == '' || password_new.length < field_size) {
      error_message = error_message + "* " + message_2 + "\n";
      error = true;
    } else if (password_new != password_confirmation) {
      error_message = error_message + "* " + message_3 + "\n";
      error = true;
    }
  }
}

function check_form(form_name) {
  if (submitted == true) {
    alert("<?php echo JS_ERROR_SUBMITTED; ?>");
    return false;
  }

  error = false;
  form = form_name;
  error_message = "<?php echo JS_ERROR; ?>";

<?php
  if (ACCOUNT_GENDER > 0) {
    echo '  check_radio("gender", "' . ENTRY_GENDER_ERROR . '");' . "\n";
  }
?>

  check_input("firstname", <?php echo ACCOUNT_FIRST_NAME; ?>, "<?php echo ENTRY_FIRST_NAME_ERROR; ?>");
  check_input("lastname", <?php echo ACCOUNT_LAST_NAME; ?>, "<?php echo ENTRY_LAST_NAME_ERROR; ?>");
  check_input("email_address", <?php echo ACCOUNT_EMAIL_ADDRESS; ?>, "<?php echo ENTRY_EMAIL_ADDRESS_ERROR; ?>");

<?php
  if (ACCOUNT_COMPANY > 0) {
    echo '  check_input("company", ' . ACCOUNT_COMPANY . ', "' . ENTRY_COMPANY_ERROR . '");' . "\n";
  }
?>

  check_input("street_address", <?php echo ACCOUNT_STREET_ADDRESS; ?>, "<?php echo ENTRY_STREET_ADDRESS_ERROR; ?>");

<?php
  if (ACCOUNT_SUBURB > 0) {
    echo '  check_input("suburb", ' . ACCOUNT_SUBURB . ', "' . ENTRY_SUBURB_ERROR . '");' . "\n";
  }
?>

  check_input("postcode", <?php echo ACCOUNT_POST_CODE; ?>, "<?php echo ENTRY_POST_CODE_ERROR; ?>");
  check_input("city", <?php echo ACCOUNT_CITY; ?>, "<?php echo ENTRY_CITY_ERROR; ?>");

<?php
  if (ACCOUNT_STATE > 0) {
    echo '  check_input("state", ' . ACCOUNT_STATE . ', "' . ENTRY_STATE_ERROR . '");' . "\n";
  }
?>

  check_select("country", "", "<?php echo ENTRY_COUNTRY_ERROR; ?>");

<?php
  if (ACCOUNT_TELEPHONE > 0) {
    echo '  check_input("telephone", ' . ACCOUNT_TELEPHONE . ', "' . ENTRY_TELEPHONE_NUMBER_ERROR . '");' . "\n";
  }

  if (ACCOUNT_FAX > 0) {
    echo '  check_input("fax", ' . ACCOUNT_FAX . ', "' . ENTRY_FAX_NUMBER_ERROR . '");' . "\n";
  }
?>

  check_password("password", "confirmation", <?php echo ACCOUNT_PASSWORD; ?>, "<?php echo ENTRY_PASSWORD_ERROR; ?>", "<?php echo ENTRY_PASSWORD_ERROR_NOT_MATCHING; ?>");
  check_password_new("password_current", "password_new", "password_confirmation", <?php echo ACCOUNT_PASSWORD; ?>, "<?php echo ENTRY_PASSWORD_ERROR; ?>", "<?php echo ENTRY_PASSWORD_NEW_ERROR; ?>", "<?php echo ENTRY_PASSWORD_NEW_ERROR_NOT_MATCHING; ?>");

  if (error == true) {
    alert(error_message);
    return false;
  } else {
    submitted = true;
    return true;
  }
}
//--></script>
