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
        $_page_title,
        $_page_contents = 'password_forgotten.php';

/* Class constructor */

    function osC_Account_Password_forgotten() {
      global $osC_Language, $osC_Services, $breadcrumb;

      $this->_page_title = $osC_Language->get('password_forgotten_heading');

      $this->addJavascriptPhpFilename('includes/form_check.js.php');

      if ($osC_Services->isStarted('breadcrumb')) {
        $breadcrumb->add($osC_Language->get('breadcrumb_password_forgotten'), osc_href_link(FILENAME_ACCOUNT, $this->_module, 'SSL'));
      }

      if ($_GET[$this->_module] == 'process') {
        $this->_process();
      }
    }

/* Private methods */

    function _process() {
      global $messageStack, $osC_Database, $osC_Language;

      $Qcheck = $osC_Database->query('select customers_id, customers_firstname, customers_lastname, customers_gender, customers_email_address, customers_password from :table_customers where customers_email_address = :customers_email_address limit 1');
      $Qcheck->bindTable(':table_customers', TABLE_CUSTOMERS);
      $Qcheck->bindValue(':customers_email_address', $_POST['email_address']);
      $Qcheck->execute();

      if ($Qcheck->numberOfRows() === 1) {
        $password = osc_create_random_string(ACCOUNT_PASSWORD);

        if (osC_Account::savePassword($password, $Qcheck->valueInt('customers_id'))) {
          if (ACCOUNT_GENDER > -1) {
             if ($data['gender'] == 'm') {
               $email_text = sprintf($osC_Language->get('email_addressing_gender_male'), $Qcheck->valueProtected('customers_lastname')) . "\n\n";
             } else {
               $email_text = sprintf($osC_Language->get('email_addressing_gender_female'), $Qcheck->valueProtected('customers_lastname')) . "\n\n";
             }
          } else {
            $email_text = sprintf($osC_Language->get('email_addressing_gender_unknown'), $Qcheck->valueProtected('customers_firstname') . ' ' . $Qcheck->valueProtected('customers_lastname')) . "\n\n";
          }

          $email_text .= sprintf($osC_Language->get('email_password_reminder_body'), getenv('REMOTE_ADDR'), STORE_NAME, $password, STORE_OWNER_EMAIL_ADDRESS);

          osc_email($Qcheck->valueProtected('customers_firstname') . ' ' . $Qcheck->valueProtected('customers_lastname'), $Qcheck->valueProtected('customers_email_address'), sprintf($osC_Language->get('email_password_reminder_subject'), STORE_NAME), $email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);

          $messageStack->add_session('login', $osC_Language->get('success_password_forgotten_sent'), 'success');
        }

        osc_redirect(osc_href_link(FILENAME_ACCOUNT, 'login', 'SSL'));
      } else {
        $messageStack->add('password_forgotten', $osC_Language->get('error_password_forgotten_no_email_address_found'));
      }
    }
  }
?>
