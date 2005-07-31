<?php
/*
  $Id: password.php 64 2005-03-12 16:36:16Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class osC_Account_Login {

/* Private variables */

    var $_module = 'login',
        $_page_title = HEADING_TITLE_LOGIN,
        $_page_contents = 'login.php';

/* Class constructor */

    function osC_Account_Login() {
      global $osC_Session, $osC_Template, $osC_Services, $breadcrumb;

// redirect the customer to a friendly cookie-must-be-enabled page if cookies are disabled (or the session has not started)
      if ($osC_Session->is_started == false) {
        tep_redirect(tep_href_link(FILENAME_COOKIE_USAGE, '', 'AUTO'));
      }

      if ($osC_Services->isStarted('breadcrumb')) {
        $breadcrumb->add(NAVBAR_TITLE_LOGIN, tep_href_link(FILENAME_ACCOUNT, $this->_module, 'SSL'));
      }

      if ($_GET[$this->_module] == 'process') {
        $this->_process();
      }
    }

/* Public methods */

    function getPageTitle() {
      return $this->_page_title;
    }

    function getPageContentsFilename() {
      return $this->_page_contents;
    }

/* Private methods */

    function _process() {
      global $osC_Database, $osC_Session, $messageStack, $osC_Customer, $cart, $navigation;

// Check if email exists
      $Qcheck = $osC_Database->query('select customers_id, customers_password from :table_customers where customers_email_address = :customers_email_address');
      $Qcheck->bindTable(':table_customers', TABLE_CUSTOMERS);
      $Qcheck->bindValue(':customers_email_address', $_POST['email_address']);
      $Qcheck->execute();

      if ($Qcheck->numberOfRows() < 1) {
        $messageStack->add('login', TEXT_LOGIN_ERROR);
      } else {
// Check that password is good
        if (!tep_validate_password($_POST['password'], $Qcheck->value('customers_password'))) {
          $messageStack->add('login', TEXT_LOGIN_ERROR);
        } else {
          if (SERVICE_SESSION_REGENERATE_ID == 'True') {
            $osC_Session->recreate();
          }

          $osC_Customer->setCustomerData($Qcheck->valueInt('customers_id'));

          $Qupdate = $osC_Database->query('update :table_customers_info set customers_info_date_of_last_logon = now(), customers_info_number_of_logons = customers_info_number_of_logons+1 where customers_info_id = :customers_info_id');
          $Qupdate->bindTable(':table_customers_info', TABLE_CUSTOMERS_INFO);
          $Qupdate->bindInt(':customers_info_id', $osC_Customer->id);
          $Qupdate->execute();

// restore cart contents
          $cart->restore_contents();

          $navigation->remove_current_page();

          if (sizeof($navigation->snapshot) > 0) {
            $origin_href = tep_href_link($navigation->snapshot['page'], tep_array_to_string($navigation->snapshot['get'], array($osC_Session->name)), 'AUTO');
            $navigation->clear_snapshot();

            tep_redirect($origin_href);
          } else {
            tep_redirect(tep_href_link(FILENAME_DEFAULT, '', 'AUTO'));
          }
        }
      }
    }
  }
?>
