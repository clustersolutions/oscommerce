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
        $_page_title,
        $_page_contents = 'account_password.php';

/* Class constructor */

    function osC_Account_Password() {
      global $osC_Language, $osC_Services, $breadcrumb;

      $this->_page_title = $osC_Language->get('account_password_heading');

      $this->addJavascriptPhpFilename('includes/form_check.js.php');

      if ($osC_Services->isStarted('breadcrumb')) {
        $breadcrumb->add($osC_Language->get('breadcrumb_edit_password'), tep_href_link(FILENAME_ACCOUNT, $this->_module, 'SSL'));
      }

      if ($_GET[$this->_module] == 'save') {
        $this->_process();
      }
    }

/* Private methods */

    function _process() {
      global $messageStack, $osC_Database, $osC_Language;

      if (!isset($_POST['password_current']) || (strlen(trim($_POST['password_current'])) < ACCOUNT_PASSWORD)) {
        $messageStack->add('account_password', sprintf($osC_Language->get('field_customer_password_current_error'), ACCOUNT_PASSWORD));
      } elseif (!isset($_POST['password_new']) || (strlen(trim($_POST['password_new'])) < ACCOUNT_PASSWORD)) {
        $messageStack->add('account_password', sprintf($osC_Language->get('field_customer_password_new_error'), ACCOUNT_PASSWORD));
      } elseif (!isset($_POST['password_confirmation']) || (trim($_POST['password_new']) != trim($_POST['password_confirmation']))) {
        $messageStack->add('account_password', $osC_Language->get('field_customer_password_new_mismatch_with_confirmation_error'));
      }

      if ($messageStack->size('account_password') === 0) {
        if (osC_Account::checkPassword(trim($_POST['password_current']))) {
          if (osC_Account::savePassword(trim($_POST['password_new']))) {
            $messageStack->add_session('account', $osC_Language->get('success_password_updated'), 'success');

            tep_redirect(tep_href_link(FILENAME_ACCOUNT, '', 'SSL'));
          } else {
            $messageStack->add('account_password', sprintf($osC_Language->get('field_customer_password_new_error'), ACCOUNT_PASSWORD));
          }
        } else {
          $messageStack->add('account_password', $osC_Language->get('error_current_password_not_matching'));
        }
      }
    }
  }
?>
