<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class osC_Account_Password {

/* Private variables */

    var $_module = 'password',
        $_page_title = HEADING_TITLE_ACCOUNT_PASSWORD,
        $_page_contents = 'account_password.php';

/* Class constructor */

    function osC_Account_Password() {
      global $osC_Template, $osC_Services, $breadcrumb;

      $osC_Template->addJavascriptPhpFilename('includes/form_check.js.php');

      if ($osC_Services->isStarted('breadcrumb')) {
        $breadcrumb->add(NAVBAR_TITLE_EDIT_PASSWORD, tep_href_link(FILENAME_ACCOUNT, $this->_module, 'SSL'));
      }

      if ($_GET[$this->_module] == 'save') {
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
      global $messageStack, $osC_Database, $osC_Customer;

      if (!isset($_POST['password_current']) || (strlen(trim($_POST['password_current'])) < ACCOUNT_PASSWORD)) {
        $messageStack->add('account_password', ENTRY_PASSWORD_CURRENT_ERROR);
      } elseif (!isset($_POST['password_new']) || (strlen(trim($_POST['password_new'])) < ACCOUNT_PASSWORD)) {
        $messageStack->add('account_password', ENTRY_PASSWORD_NEW_ERROR);
      } elseif (!isset($_POST['password_confirmation']) || (trim($_POST['password_new']) != trim($_POST['password_confirmation']))) {
        $messageStack->add('account_password', ENTRY_PASSWORD_NEW_ERROR_NOT_MATCHING);
      }

      if ($messageStack->size('account_password') === 0) {
        $Qcheck = $osC_Database->query('select customers_password from :table_customers where customers_id = :customers_id');
        $Qcheck->bindTable(':table_customers', TABLE_CUSTOMERS);
        $Qcheck->bindInt(':customers_id', $osC_Customer->id);
        $Qcheck->execute();

        if (tep_validate_password(trim($_POST['password_current']), $Qcheck->value('customers_password'))) {
          $Qupdate = $osC_Database->query('update :table_customers set customers_password = :customers_password where customers_id = :customers_id');
          $Qupdate->bindTable(':table_customers', TABLE_CUSTOMERS);
          $Qupdate->bindValue(':customers_password', tep_encrypt_password(trim($_POST['password_new'])));
          $Qupdate->bindInt(':customers_id', $osC_Customer->id);
          $Qupdate->execute();

          $Qupdate = $osC_Database->query('update :table_customers_info set customers_info_date_account_last_modified = now() where customers_info_id = :customers_info_id');
          $Qupdate->bindTable(':table_customers_info', TABLE_CUSTOMERS_INFO);
          $Qupdate->bindInt(':customers_info_id', $osC_Customer->id);
          $Qupdate->execute();

          $messageStack->add_session('account', SUCCESS_PASSWORD_UPDATED, 'success');

          tep_redirect(tep_href_link(FILENAME_ACCOUNT, '', 'SSL'));
        } else {
          $messageStack->add('account_password', ERROR_CURRENT_PASSWORD_NOT_MATCHING);
        }
      }
    }
  }
?>
