<?php
/*
  $Id:edit.php 188 2005-09-15 02:25:52 +0200 (Do, 15 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  require('includes/classes/account.php');

  class osC_Account_Create extends osC_Template {

/* Private variables */

    var $_module = 'create',
        $_group = 'account',
        $_page_title = HEADING_TITLE_CREATE,
        $_page_contents = 'create.php';

/* Class constructor */

    function osC_Account_Create() {
      global $osC_Services, $breadcrumb;

      if ($_GET[$this->_module] == 'success') {
        if ($osC_Services->isStarted('breadcrumb')) {
          $breadcrumb->add(NAVBAR_TITLE_CREATE);
        }

        $this->_page_title = HEADING_TITLE_CREATE_SUCCESS;
        $this->_page_contents = 'create_success.php';
      } else {
        if ($osC_Services->isStarted('breadcrumb')) {
          $breadcrumb->add(NAVBAR_TITLE_CREATE, tep_href_link(FILENAME_ACCOUNT, $this->_module, 'SSL'));
        }

        $this->addJavascriptPhpFilename('includes/form_check.js.php');
      }

      if ($_GET[$this->_module] == 'save') {
        $this->_process();
      }
    }

/* Private methods */

    function _process() {
      global $messageStack, $osC_Database, $osC_Customer;

      $data = array();

      if (DISPLAY_PRIVACY_CONDITIONS == 'true') {
        if ( (isset($_POST['privacy_conditions']) === false) || (isset($_POST['privacy_conditions']) && ($_POST['privacy_conditions'] != '1')) ) {
          $messageStack->add($this->_module, ERROR_PRIVACY_STATEMENT_NOT_ACCEPTED);
        }
      }

      if (ACCOUNT_GENDER >= 0) {
        if (isset($_POST['gender']) && (($_POST['gender'] == 'm') || ($_POST['gender'] == 'f'))) {
          $data['gender'] = $_POST['gender'];
        } else {
          $messageStack->add($this->_module, ENTRY_GENDER_ERROR);
        }
      }

      if (isset($_POST['firstname']) && (strlen(trim($_POST['firstname'])) >= ACCOUNT_FIRST_NAME)) {
        $data['firstname'] = $_POST['firstname'];
      } else {
        $messageStack->add($this->_module, ENTRY_FIRST_NAME_ERROR);
      }

      if (isset($_POST['lastname']) && (strlen(trim($_POST['lastname'])) >= ACCOUNT_LAST_NAME)) {
        $data['lastname'] = $_POST['lastname'];
      } else {
        $messageStack->add($this->_module, ENTRY_LAST_NAME_ERROR);
      }

      if (ACCOUNT_DATE_OF_BIRTH > -1) {
        if (isset($_POST['dob_days']) && isset($_POST['dob_months']) && isset($_POST['dob_years']) && checkdate($_POST['dob_months'], $_POST['dob_days'], $_POST['dob_years'])) {
          $data['dob'] = mktime(0, 0, 0, $_POST['dob_months'], $_POST['dob_days'], $_POST['dob_years']);
        } else {
          $messageStack->add($this->_module, ENTRY_DATE_OF_BIRTH_ERROR);
        }
      }

      if (isset($_POST['email_address']) && (strlen(trim($_POST['email_address'])) >= ACCOUNT_EMAIL_ADDRESS)) {
        if (tep_validate_email($_POST['email_address'])) {
          if (osC_Account::checkDuplicateEntry($_POST['email_address']) === false) {
            $data['email_address'] = $_POST['email_address'];
          } else {
            $messageStack->add($this->_module, ENTRY_EMAIL_ADDRESS_ERROR_EXISTS);
          }
        } else {
          $messageStack->add($this->_module, ENTRY_EMAIL_ADDRESS_CHECK_ERROR);
        }
      } else {
        $messageStack->add($this->_module, ENTRY_EMAIL_ADDRESS_ERROR);
      }

      if ( (isset($_POST['password']) === false) || (isset($_POST['password']) && (strlen(trim($_POST['password'])) < ACCOUNT_PASSWORD)) ) {
        $messageStack->add($this->_module, ENTRY_PASSWORD_ERROR);
      } elseif ( (isset($_POST['confirmation']) === false) || (isset($_POST['confirmation']) && (trim($_POST['password']) != trim($_POST['confirmation']))) ) {
        $messageStack->add($this->_module, ENTRY_PASSWORD_ERROR_NOT_MATCHING);
      } else {
        $data['password'] = $_POST['password'];
      }

      if ($messageStack->size($this->_module) === 0) {
        if (osC_Account::createEntry($data)) {
          $messageStack->add_session('create', SUCCESS_ACCOUNT_UPDATED, 'success');
        }

        tep_redirect(tep_href_link(FILENAME_ACCOUNT, 'create=success', 'SSL'));
      }
    }
  }
?>
