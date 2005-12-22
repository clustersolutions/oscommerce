<?php
/*
  $Id:edit.php 188 2005-09-15 02:25:52 +0200 (Do, 15 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  require('includes/classes/account.php');

  class osC_Account_Edit extends osC_Template {

/* Private variables */

    var $_module = 'edit',
        $_group = 'account',
        $_page_title = HEADING_TITLE_ACCOUNT_EDIT,
        $_page_contents = 'account_edit.php';

/* Class constructor */

    function osC_Account_Edit() {
      global $osC_Services, $breadcrumb;

      $this->addJavascriptPhpFilename('includes/form_check.js.php');

      if ($osC_Services->isStarted('breadcrumb')) {
        $breadcrumb->add(NAVBAR_TITLE_EDIT_ACCOUNT, tep_href_link(FILENAME_ACCOUNT, $this->_module, 'SSL'));
      }

      if ($_GET[$this->_module] == 'save') {
        $this->_process();
      }
    }

/* Private methods */

    function _process() {
      global $messageStack, $osC_Database, $osC_Customer;

      $data = array();

      if (ACCOUNT_GENDER >= 0) {
        if (isset($_POST['gender']) && (($_POST['gender'] == 'm') || ($_POST['gender'] == 'f'))) {
          $data['gender'] = $_POST['gender'];
        } else {
          $messageStack->add('account_edit', ENTRY_GENDER_ERROR);
        }
      }

      if (isset($_POST['firstname']) && (strlen(trim($_POST['firstname'])) >= ACCOUNT_FIRST_NAME)) {
        $data['firstname'] = $_POST['firstname'];
      } else {
        $messageStack->add('account_edit', ENTRY_FIRST_NAME_ERROR);
      }

      if (isset($_POST['lastname']) && (strlen(trim($_POST['lastname'])) >= ACCOUNT_LAST_NAME)) {
        $data['lastname'] = $_POST['lastname'];
      } else {
        $messageStack->add('account_edit', ENTRY_LAST_NAME_ERROR);
      }

      if (ACCOUNT_DATE_OF_BIRTH > -1) {
        if (isset($_POST['dob_days']) && isset($_POST['dob_months']) && isset($_POST['dob_years']) && checkdate($_POST['dob_months'], $_POST['dob_days'], $_POST['dob_years'])) {
          $data['dob'] = mktime(0, 0, 0, $_POST['dob_months'], $_POST['dob_days'], $_POST['dob_years']);
        } else {
          $messageStack->add('account_edit', ENTRY_DATE_OF_BIRTH_ERROR);
        }
      }

      if (isset($_POST['email_address']) && (strlen(trim($_POST['email_address'])) >= ACCOUNT_EMAIL_ADDRESS)) {
        if (tep_validate_email($_POST['email_address'])) {
          if (osC_Account::checkDuplicateEntry($_POST['email_address']) === false) {
            $data['email_address'] = $_POST['email_address'];
          } else {
            $messageStack->add('account_edit', ENTRY_EMAIL_ADDRESS_ERROR_EXISTS);
          }
        } else {
          $messageStack->add('account_edit', ENTRY_EMAIL_ADDRESS_CHECK_ERROR);
        }
      } else {
        $messageStack->add('account_edit', ENTRY_EMAIL_ADDRESS_ERROR);
      }

      if ($messageStack->size('account_edit') === 0) {
        if (osC_Account::saveEntry($data)) {
// reset the session variables
          if (ACCOUNT_GENDER > -1) {
            $osC_Customer->setGender($data['gender']);
          }
          $osC_Customer->setFirstName(trim($data['firstname']));
          $osC_Customer->setLastName(trim($data['lastname']));
          $osC_Customer->setEmailAddress($data['email_address']);

          $messageStack->add_session('account', SUCCESS_ACCOUNT_UPDATED, 'success');
        }

        tep_redirect(tep_href_link(FILENAME_ACCOUNT, '', 'SSL'));
      }
    }
  }
?>
