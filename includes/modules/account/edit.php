<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class osC_Account_Edit {

/* Public variables */

    var $page_contents = 'account_edit.php';

/* Private variables */

    var $_module = 'edit';

/* Class constructor */

    function osC_Account_Edit() {
      global $osC_Services, $breadcrumb;

      if ($osC_Services->isStarted('breadcrumb')) {
        $breadcrumb->add(NAVBAR_TITLE_EDIT_ACCOUNT, tep_href_link(FILENAME_ACCOUNT, $this->_module, 'SSL'));
      }

      if ($_GET[$this->_module] == 'save') {
        $this->_process();
      }
    }

/* Public methods */

    function getPageContentsFile() {
      return $this->page_contents;
    }

/* Private methods */

    function _process() {
      global $messageStack, $osC_Database, $osC_Customer;

      if (ACCOUNT_GENDER > 0) {
        if (!isset($_POST['gender']) || (($_POST['gender'] != 'm') && ($_POST['gender'] != 'f'))) {
          $messageStack->add('account_edit', ENTRY_GENDER_ERROR);
        }
      }

      if (!isset($_POST['firstname']) || (strlen(trim($_POST['firstname'])) < ACCOUNT_FIRST_NAME)) {
        $messageStack->add('account_edit', ENTRY_FIRST_NAME_ERROR);
      }

      if (!isset($_POST['lastname']) || (strlen(trim($_POST['lastname'])) < ACCOUNT_LAST_NAME)) {
        $messageStack->add('account_edit', ENTRY_LAST_NAME_ERROR);
      }

      if (ACCOUNT_DATE_OF_BIRTH > -1) {
        if (isset($_POST['dob_days']) && isset($_POST['dob_months']) && isset($_POST['dob_years']) && checkdate($_POST['dob_months'], $_POST['dob_days'], $_POST['dob_years'])) {
          $dob = mktime(0, 0, 0, $_POST['dob_months'], $_POST['dob_days'], $_POST['dob_years']);
        } else {
          $messageStack->add('account_edit', ENTRY_DATE_OF_BIRTH_ERROR);
        }
      }

      if (!isset($_POST['email_address']) || (strlen(trim($_POST['email_address'])) < ACCOUNT_EMAIL_ADDRESS)) {
        $messageStack->add('account_edit', ENTRY_EMAIL_ADDRESS_ERROR);
      } else {
        if (tep_validate_email($_POST['email_address']) == false) {
          $messageStack->add('account_edit', ENTRY_EMAIL_ADDRESS_CHECK_ERROR);
        } else {
          $Qcheck = $osC_Database->query('select customers_id from :table_customers where customers_email_address = :customers_email_address and customers_id != :customers_id limit 1');
          $Qcheck->bindTable(':table_customers', TABLE_CUSTOMERS);
          $Qcheck->bindValue(':customers_email_address', $_POST['email_address']);
          $Qcheck->bindInt(':customers_id', $osC_Customer->id);
          $Qcheck->execute();

          if ($Qcheck->numberOfRows() > 0) {
            $messageStack->add('account_edit', ENTRY_EMAIL_ADDRESS_ERROR_EXISTS);
          }

          $Qcheck->freeResult();
        }
      }

      if ($messageStack->size('account_edit') === 0) {
        $Qcustomer = $osC_Database->query('update :table_customers set customers_gender = :customers_gender, customers_firstname = :customers_firstname, customers_lastname = :customers_lastname, customers_email_address = :customers_email_address, customers_dob = :customers_dob where customers_id = :customers_id');
        $Qcustomer->bindTable(':table_customers', TABLE_CUSTOMERS);
        $Qcustomer->bindValue(':customers_gender', (((ACCOUNT_GENDER > -1) && isset($_POST['gender']) && (($_POST['gender'] == 'm') || ($_POST['gender'] == 'f'))) ? $_POST['gender'] : ''));
        $Qcustomer->bindValue(':customers_firstname', $_POST['firstname']);
        $Qcustomer->bindValue(':customers_lastname', $_POST['lastname']);
        $Qcustomer->bindValue(':customers_email_address', $_POST['email_address']);
        $Qcustomer->bindValue(':customers_dob', ((ACCOUNT_DATE_OF_BIRTH > -1) ? date('Ymd', $dob) : ''));
        $Qcustomer->bindInt(':customers_id', $osC_Customer->id);
        $Qcustomer->execute();

        $Qupdate = $osC_Database->query('update :table_customers_info set customers_info_date_account_last_modified = now() where customers_info_id = :customers_info_id');
        $Qupdate->bindTable(':table_customers_info', TABLE_CUSTOMERS_INFO);
        $Qupdate->bindInt(':customers_info_id', $osC_Customer->id);
        $Qupdate->execute();

// reset the session variables
        if (ACCOUNT_GENDER > -1) {
          $osC_Customer->setGender($_POST['gender']);
        }
        $osC_Customer->setFirstName(trim($_POST['firstname']));
        $osC_Customer->setLastName(trim($_POST['lastname']));
        $osC_Customer->setFullName();
        $osC_Customer->setEmailAddress(trim($_POST['email_address']));

        $messageStack->add_session('account', SUCCESS_ACCOUNT_UPDATED, 'success');

        tep_redirect(tep_href_link(FILENAME_ACCOUNT, '', 'SSL'));
      }
    }
  }
?>
