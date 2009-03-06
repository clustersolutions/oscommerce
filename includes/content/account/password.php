<?php
/*
  $Id:password.php 188 2005-09-15 02:25:52 +0200 (Do, 15 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  require('includes/classes/account.php');

  class osC_Account_Password extends osC_Template {

/* Private variables */

    var $_module = 'password',
        $_group = 'account',
        $_page_title,
        $_page_contents = 'account_password.php',
        $_page_image = 'table_background_account.gif';

/* Class constructor */

    function osC_Account_Password() {
      global $osC_Language, $osC_Services, $osC_Breadcrumb;

      $this->_page_title = $osC_Language->get('account_password_heading');

      $this->addJavascriptPhpFilename('includes/form_check.js.php');

      if ($osC_Services->isStarted('breadcrumb')) {
        $osC_Breadcrumb->add($osC_Language->get('breadcrumb_edit_password'), osc_href_link(FILENAME_ACCOUNT, $this->_module, 'SSL'));
      }

      if ($_GET[$this->_module] == 'save') {
        $this->_process();
      }
    }

/* Private methods */

    function _process() {
      global $osC_MessageStack, $osC_Database, $osC_Language;

      if (!isset($_POST['password_current']) || (strlen(trim($_POST['password_current'])) < ACCOUNT_PASSWORD)) {
        $osC_MessageStack->add('account_password', sprintf($osC_Language->get('field_customer_password_current_error'), ACCOUNT_PASSWORD));
      } elseif (!isset($_POST['password_new']) || (strlen(trim($_POST['password_new'])) < ACCOUNT_PASSWORD)) {
        $osC_MessageStack->add('account_password', sprintf($osC_Language->get('field_customer_password_new_error'), ACCOUNT_PASSWORD));
      } elseif (!isset($_POST['password_confirmation']) || (trim($_POST['password_new']) != trim($_POST['password_confirmation']))) {
        $osC_MessageStack->add('account_password', $osC_Language->get('field_customer_password_new_mismatch_with_confirmation_error'));
      }

      if ($osC_MessageStack->size('account_password') === 0) {
        if (osC_Account::checkPassword(trim($_POST['password_current']))) {
          if (osC_Account::savePassword(trim($_POST['password_new']))) {
            $osC_MessageStack->add('account', $osC_Language->get('success_password_updated'), 'success');

            osc_redirect(osc_href_link(FILENAME_ACCOUNT, null, 'SSL'));
          } else {
            $osC_MessageStack->add('account_password', sprintf($osC_Language->get('field_customer_password_new_error'), ACCOUNT_PASSWORD));
          }
        } else {
          $osC_MessageStack->add('account_password', $osC_Language->get('error_current_password_not_matching'));
        }
      }
    }
  }
?>
