<?php
/*
  $Id:password.php 188 2005-09-15 02:25:52 +0200 (Do, 15 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  require('includes/classes/account.php');

  class osC_Account_Password_forgotten extends osC_Template {

/* Private variables */

    var $_module = 'password_forgotten',
        $_group = 'account',
        $_page_title = HEADING_TITLE_PASSWORD_FORGOTTEN,
        $_page_contents = 'password_forgotten.php';

/* Class constructor */

    function osC_Account_Password_forgotten() {
      global $osC_Services, $breadcrumb;

      $this->addJavascriptPhpFilename('includes/form_check.js.php');

      if ($osC_Services->isStarted('breadcrumb')) {
        $breadcrumb->add(NAVBAR_TITLE_PASSWORD_FORGOTTEN, tep_href_link(FILENAME_ACCOUNT, $this->_module, 'SSL'));
      }

      if ($_GET[$this->_module] == 'process') {
        $this->_process();
      }
    }

/* Private methods */

    function _process() {
      global $messageStack, $osC_Database;

      $Qcheck = $osC_Database->query('select customers_id, customers_firstname, customers_lastname, customers_password from :table_customers where customers_email_address = :customers_email_address limit 1');
      $Qcheck->bindTable(':table_customers', TABLE_CUSTOMERS);
      $Qcheck->bindValue(':customers_email_address', $_POST['email_address']);
      $Qcheck->execute();

      if ($Qcheck->numberOfRows() === 1) {
        $password = tep_create_random_value(ACCOUNT_PASSWORD);

        if (osC_Account::savePassword($password, $Qcheck->valueInt('customers_id'))) {
          tep_mail($Qcheck->valueProtected('customers_firstname') . ' ' . $Qcheck->valueProtected('customers_lastname'), $_POST['email_address'], EMAIL_PASSWORD_REMINDER_SUBJECT, sprintf(EMAIL_PASSWORD_REMINDER_BODY, $password), STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);

          $messageStack->add_session('login', SUCCESS_PASSWORD_FORGOTTEN_SENT, 'success');
        }

        tep_redirect(tep_href_link(FILENAME_ACCOUNT, 'login', 'SSL'));
      } else {
        $messageStack->add('password_forgotten', ERROR_PASSWORD_FORGOTTEN_NO_EMAIL_ADDRESS_FOUND);
      }
    }
  }
?>
