<?php
/*
  $Id:password.php 188 2005-09-15 02:25:52 +0200 (Do, 15 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  require('includes/classes/account.php');

  class osC_Account_Password extends osC_Template {

/* Private variables */

    var $_module = 'password',
        $_group = 'account',
        $_page_title = HEADING_TITLE_ACCOUNT_PASSWORD,
        $_page_contents = 'account_password.php';

/* Class constructor */

    function osC_Account_Password() {
      global $osC_Services, $breadcrumb;

      $this->addJavascriptPhpFilename('includes/form_check.js.php');

      if ($osC_Services->isStarted('breadcrumb')) {
        $breadcrumb->add(NAVBAR_TITLE_EDIT_PASSWORD, tep_href_link(FILENAME_ACCOUNT, $this->_module, 'SSL'));
      }

      if ($_GET[$this->_module] == 'save') {
        $this->_process();
      }
    }

/* Private methods */

    function _process() {
      global $messageStack, $osC_Database;

      if (!isset($_POST['password_current']) || (strlen(trim($_POST['password_current'])) < ACCOUNT_PASSWORD)) {
        $messageStack->add('account_password', ENTRY_PASSWORD_CURRENT_ERROR);
      } elseif (!isset($_POST['password_new']) || (strlen(trim($_POST['password_new'])) < ACCOUNT_PASSWORD)) {
        $messageStack->add('account_password', ENTRY_PASSWORD_NEW_ERROR);
      } elseif (!isset($_POST['password_confirmation']) || (trim($_POST['password_new']) != trim($_POST['password_confirmation']))) {
        $messageStack->add('account_password', ENTRY_PASSWORD_NEW_ERROR_NOT_MATCHING);
      }

      if ($messageStack->size('account_password') === 0) {
        if (osC_Account::checkPassword(trim($_POST['password_current']))) {
          if (osC_Account::savePassword(trim($_POST['password_new']))) {
            $messageStack->add_session('account', SUCCESS_PASSWORD_UPDATED, 'success');

            tep_redirect(tep_href_link(FILENAME_ACCOUNT, '', 'SSL'));
          } else {
            $messageStack->add('account_password', ENTRY_PASSWORD_NEW_ERROR);
          }
        } else {
          $messageStack->add('account_password', ERROR_CURRENT_PASSWORD_NOT_MATCHING);
        }
      }
    }
  }
?>
