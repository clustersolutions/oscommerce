<?php
/*
  $Id:address_book.php 188 2005-09-15 02:25:52 +0200 (Do, 15 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  require('includes/classes/address_book.php');

  class osC_Account_Address_book extends osC_Template {

/* Private variables */

    var $_module = 'address_book',
        $_group = 'account',
        $_page_title = HEADING_TITLE_ADDRESS_BOOK,
        $_page_contents = 'address_book.php';

/* Class constructor */

    function osC_Account_Address_book() {
      global $osC_Services, $breadcrumb, $osC_Customer;

      if ($osC_Services->isStarted('breadcrumb')) {
        $breadcrumb->add(NAVBAR_TITLE_ADDRESS_BOOK, tep_href_link(FILENAME_ACCOUNT, $this->_module, 'SSL'));
      }

      if ($osC_Customer->hasDefaultAddress() === false) {
        $this->_page_title = HEADING_TITLE_ADDRESS_BOOK_ADD_ENTRY;
        $this->_page_contents = 'address_book_process.php';

        $this->addJavascriptPhpFilename('includes/form_check.js.php');
      } elseif (isset($_GET['new'])) {
        if ($osC_Services->isStarted('breadcrumb')) {
          $breadcrumb->add(NAVBAR_TITLE_ADDRESS_BOOK_ADD_ENTRY, tep_href_link(FILENAME_ACCOUNT, $this->_module . '&new', 'SSL'));
        }

        $this->_page_title = HEADING_TITLE_ADDRESS_BOOK_ADD_ENTRY;
        $this->_page_contents = 'address_book_process.php';

        $this->addJavascriptPhpFilename('includes/form_check.js.php');
      } elseif (isset($_GET['edit']) && is_numeric($_GET[$this->_module])) {
        if ($osC_Services->isStarted('breadcrumb')) {
          $breadcrumb->add(NAVBAR_TITLE_ADDRESS_BOOK_EDIT_ENTRY, tep_href_link(FILENAME_ACCOUNT, $this->_module . '=' . $_GET[$this->_module] . '&edit', 'SSL'));
        }

        $this->_page_title = HEADING_TITLE_ADDRESS_BOOK_EDIT_ENTRY;
        $this->_page_contents = 'address_book_process.php';

        $this->addJavascriptPhpFilename('includes/form_check.js.php');
      } elseif (isset($_GET['delete']) && is_numeric($_GET[$this->_module])) {
        if ($osC_Services->isStarted('breadcrumb')) {
          $breadcrumb->add(NAVBAR_TITLE_ADDRESS_BOOK_DELETE_ENTRY, tep_href_link(FILENAME_ACCOUNT, $this->_module . '=' . $_GET[$this->_module] . '&delete', 'SSL'));
        }

        $this->_page_title = HEADING_TITLE_ADDRESS_BOOK_DELETE_ENTRY;
        $this->_page_contents = 'address_book_delete.php';
      }

      if (isset($_GET['new']) && ($_GET['new'] == 'save')) {
        if (osC_AddressBook::numberOfEntries() >= MAX_ADDRESS_BOOK_ENTRIES) {
          $messageStack->add('address_book', ERROR_ADDRESS_BOOK_FULL);

          $this->_page_title = HEADING_TITLE_ADDRESS_BOOK;
          $this->_page_contents = 'address_book.php';
        } else {
          $this->_process();
        }
      } elseif (isset($_GET['edit']) && ($_GET['edit'] == 'save')) {
        $this->_process($_GET[$this->_module]);
      } elseif (isset($_GET['delete']) && ($_GET['delete'] == 'confirm') && is_numeric($_GET[$this->_module])) {
        $this->_delete($_GET[$this->_module]);
      }
    }

/* Private methods */

    function _process($id = '') {
      global $messageStack, $osC_Database, $osC_Customer, $entry_state_has_zones;

      $data = array();

      if (ACCOUNT_GENDER >= 0) {
        if (isset($_POST['gender']) && (($_POST['gender'] == 'm') || ($_POST['gender'] == 'f'))) {
          $data['gender'] = $_POST['gender'];
        } else {
          $messageStack->add('address_book', ENTRY_GENDER_ERROR);
        }
      }

      if (isset($_POST['firstname']) && (strlen(trim($_POST['firstname'])) >= ACCOUNT_FIRST_NAME)) {
        $data['firstname'] = $_POST['firstname'];
      } else {
        $messageStack->add('address_book', ENTRY_FIRST_NAME_ERROR);
      }

      if (isset($_POST['lastname']) && (strlen(trim($_POST['lastname'])) >= ACCOUNT_LAST_NAME)) {
        $data['lastname'] = $_POST['lastname'];
      } else {
        $messageStack->add('address_book', ENTRY_LAST_NAME_ERROR);
      }

      if (ACCOUNT_COMPANY >= 0) {
        if (isset($_POST['company']) && (strlen(trim($_POST['company'])) >= ACCOUNT_COMPANY)) {
          $data['company'] = $_POST['company'];
        } else {
          $messageStack->add('address_book', ENTRY_COMPANY_ERROR);
        }
      }

      if (isset($_POST['street_address']) && (strlen(trim($_POST['street_address'])) >= ACCOUNT_STREET_ADDRESS)) {
        $data['street_address'] = $_POST['street_address'];
      } else {
        $messageStack->add('address_book', ENTRY_STREET_ADDRESS_ERROR);
      }

      if (ACCOUNT_SUBURB >= 0) {
        if (isset($_POST['suburb']) && (strlen(trim($_POST['suburb'])) >= ACCOUNT_SUBURB)) {
          $data['suburb'] = $_POST['suburb'];
        } else {
          $messageStack->add('address_book', ENTRY_SUBURB_ERROR);
        }
      }

      if (isset($_POST['postcode']) && (strlen(trim($_POST['postcode'])) >= ACCOUNT_POST_CODE)) {
        $data['postcode'] = $_POST['postcode'];
      } else {
        $messageStack->add('address_book', ENTRY_POST_CODE_ERROR);
      }

      if (isset($_POST['city']) && (strlen(trim($_POST['city'])) >= ACCOUNT_CITY)) {
        $data['city'] = $_POST['city'];
      } else {
        $messageStack->add('address_book', ENTRY_CITY_ERROR);
      }

      if (ACCOUNT_STATE >= 0) {
        $Qcheck = $osC_Database->query('select zone_id from :table_zones where zone_country_id = :zone_country_id limit 1');
        $Qcheck->bindTable(':table_zones', TABLE_ZONES);
        $Qcheck->bindInt(':zone_country_id', $_POST['country']);
        $Qcheck->execute();

        $entry_state_has_zones = ($Qcheck->numberOfRows() > 0);

        if ($entry_state_has_zones === true) {
          $Qzone = $osC_Database->query('select zone_id from :table_zones where zone_country_id = :zone_country_id and zone_code like :zone_code');
          $Qzone->bindTable(':table_zones', TABLE_ZONES);
          $Qzone->bindInt(':zone_country_id', $_POST['country']);
          $Qzone->bindValue(':zone_code', $_POST['state']);
          $Qzone->execute();

          if ($Qzone->numberOfRows() === 1) {
            $data['zone_id'] = $Qzone->valueInt('zone_id');
          } else {
            $Qzone = $osC_Database->query('select zone_id from :table_zones where zone_country_id = :zone_country_id and zone_name like :zone_name');
            $Qzone->bindTable(':table_zones', TABLE_ZONES);
            $Qzone->bindInt(':zone_country_id', $_POST['country']);
            $Qzone->bindValue(':zone_name', $_POST['state'] . '%');
            $Qzone->execute();

            if ($Qzone->numberOfRows() === 1) {
              $data['zone_id'] = $Qzone->valueInt('zone_id');
            } else {
              $messageStack->add('address_book', ENTRY_STATE_ERROR_SELECT);
            }
          }
        } else {
          if (strlen(trim($_POST['state'])) >= ACCOUNT_STATE) {
            $data['state'] = $_POST['state'];
          } else {
            $messageStack->add('address_book', ENTRY_STATE_ERROR);
          }
        }
      } else {
        if (strlen(trim($_POST['state'])) >= ACCOUNT_STATE) {
          $data['state'] = $_POST['state'];
        } else {
          $messageStack->add('address_book', ENTRY_STATE_ERROR);
        }
      }

      if (isset($_POST['country']) && is_numeric($_POST['country']) && ($_POST['country'] >= 1)) {
        $data['country'] = $_POST['country'];
      } else {
        $messageStack->add('address_book', ENTRY_COUNTRY_ERROR);
      }

      if (ACCOUNT_TELEPHONE >= 0) {
        if (isset($_POST['telephone']) && (strlen(trim($_POST['telephone'])) >= ACCOUNT_TELEPHONE)) {
          $data['telephone'] = $_POST['telephone'];
        } else {
          $messageStack->add('address_book', ENTRY_TELEPHONE_NUMBER_ERROR);
        }
      }

      if (ACCOUNT_FAX >= 0) {
        if (isset($_POST['fax']) && (strlen(trim($_POST['fax'])) >= ACCOUNT_FAX)) {
          $data['fax'] = $_POST['fax'];
        } else {
          $messageStack->add('address_book', ENTRY_FAX_NUMBER_ERROR);
        }
      }

      if ( ($osC_Customer->hasDefaultAddress() === false) || (isset($_POST['primary']) && ($_POST['primary'] == 'on')) ) {
        $data['primary'] = true;
      }

      if ($messageStack->size('address_book') === 0) {
        if (osC_AddressBook::saveEntry($data, $id)) {
          $messageStack->add_session('address_book', SUCCESS_ADDRESS_BOOK_ENTRY_UPDATED, 'success');
        }

        tep_redirect(tep_href_link(FILENAME_ACCOUNT, 'address_book', 'SSL'));
      }
    }

    function _delete($id) {
      global $messageStack, $osC_Customer;

      if ($id != $osC_Customer->getDefaultAddressID()) {
        if (osC_AddressBook::deleteEntry($id)) {
          $messageStack->add_session('address_book', SUCCESS_ADDRESS_BOOK_ENTRY_DELETED, 'success');
        }
      } else {
        $messageStack->add_session('address_book', WARNING_PRIMARY_ADDRESS_DELETION, 'warning');
      }

      tep_redirect(tep_href_link(FILENAME_ACCOUNT, 'address_book', 'SSL'));
    }
  }
?>
