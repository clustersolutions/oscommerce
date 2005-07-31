<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class osC_Account_Address_book {

/* Private variables */

    var $_module = 'address_book', 
        $_page_title = HEADING_TITLE_ADDRESS_BOOK,
        $_page_contents = 'address_book.php';

/* Class constructor */

    function osC_Account_Address_book() {
      global $osC_Template, $osC_Services, $breadcrumb, $osC_Customer;

      if ($osC_Services->isStarted('breadcrumb')) {
        $breadcrumb->add(NAVBAR_TITLE_ADDRESS_BOOK, tep_href_link(FILENAME_ACCOUNT, $this->_module, 'SSL'));
      }

      if ($osC_Customer->hasDefaultAddress() === false) {
        $this->_page_title = HEADING_TITLE_ADDRESS_BOOK_ADD_ENTRY;
        $this->_page_contents = 'address_book_process.php';

        $osC_Template->addJavascriptPhpFilename('includes/form_check.js.php');
      } elseif (isset($_GET['new'])) {
        if ($osC_Services->isStarted('breadcrumb')) {
          $breadcrumb->add(NAVBAR_TITLE_ADDRESS_BOOK_ADD_ENTRY, tep_href_link(FILENAME_ACCOUNT, $this->_module . '&new', 'SSL'));
        }

        $this->_page_title = HEADING_TITLE_ADDRESS_BOOK_ADD_ENTRY;
        $this->_page_contents = 'address_book_process.php';

        $osC_Template->addJavascriptPhpFilename('includes/form_check.js.php');
      } elseif (isset($_GET['edit']) && is_numeric($_GET[$this->_module])) {
        if ($osC_Services->isStarted('breadcrumb')) {
          $breadcrumb->add(NAVBAR_TITLE_ADDRESS_BOOK_EDIT_ENTRY, tep_href_link(FILENAME_ACCOUNT, $this->_module . '=' . $_GET[$this->_module] . '&edit', 'SSL'));
        }

        $this->_page_title = HEADING_TITLE_ADDRESS_BOOK_EDIT_ENTRY;
        $this->_page_contents = 'address_book_process.php';

        $osC_Template->addJavascriptPhpFilename('includes/form_check.js.php');
      } elseif (isset($_GET['delete']) && is_numeric($_GET[$this->_module])) {
        if ($osC_Services->isStarted('breadcrumb')) {
          $breadcrumb->add(NAVBAR_TITLE_ADDRESS_BOOK_DELETE_ENTRY, tep_href_link(FILENAME_ACCOUNT, $this->_module . '=' . $_GET[$this->_module] . '&delete', 'SSL'));
        }

        $this->_page_title = HEADING_TITLE_ADDRESS_BOOK_DELETE_ENTRY;
        $this->_page_contents = 'address_book_delete.php';
      }

      if (isset($_GET['new']) && ($_GET['new'] == 'save')) {
        if (tep_count_customer_address_book_entries() >= MAX_ADDRESS_BOOK_ENTRIES) {
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

/* Public methods */

    function getPageTitle() {
      return $this->_page_title;
    }

    function getPageContentsFilename() {
      return $this->_page_contents;
    }

/* Private methods */

    function _process($id = '') {
      global $messageStack, $osC_Database, $osC_Customer, $entry_state_has_zones;

      if (ACCOUNT_GENDER > 0) {
        if (!isset($_POST['gender']) || (($_POST['gender'] != 'm') && ($_POST['gender'] != 'f'))) {
          $messageStack->add('address_book', ENTRY_GENDER_ERROR);
        }
      }

      if (!isset($_POST['firstname']) || (strlen(trim($_POST['firstname'])) < ACCOUNT_FIRST_NAME)) {
        $messageStack->add('address_book', ENTRY_FIRST_NAME_ERROR);
      }

      if (!isset($_POST['lastname']) || (strlen(trim($_POST['lastname'])) < ACCOUNT_LAST_NAME)) {
        $messageStack->add('address_book', ENTRY_LAST_NAME_ERROR);
      }

      if (ACCOUNT_COMPANY > 0) {
        if (!isset($_POST['company']) || (strlen(trim($_POST['company'])) < ACCOUNT_COMPANY)) {
          $messageStack->add('address_book', ENTRY_COMPANY_ERROR);
        }
      }

      if (!isset($_POST['street_address']) || (strlen(trim($_POST['street_address'])) < ACCOUNT_STREET_ADDRESS)) {
        $messageStack->add('address_book', ENTRY_STREET_ADDRESS_ERROR);
      }

      if (ACCOUNT_SUBURB > 0) {
        if (!isset($_POST['suburb']) || (strlen(trim($_POST['suburb'])) < ACCOUNT_SUBURB)) {
          $messageStack->add('address_book', ENTRY_SUBURB_ERROR);
        }
      }

      if (!isset($_POST['postcode']) || (strlen(trim($_POST['postcode'])) < ACCOUNT_POST_CODE)) {
        $messageStack->add('address_book', ENTRY_POST_CODE_ERROR);
      }

      if (!isset($_POST['city']) || (strlen(trim($_POST['city'])) < ACCOUNT_CITY)) {
        $messageStack->add('address_book', ENTRY_CITY_ERROR);
      }

      if (ACCOUNT_STATE > 0) {
        $zone_id = 0;

        $Qcheck = $osC_Database->query('select zone_id from :table_zones where zone_country_id = :zone_country_id limit 1');
        $Qcheck->bindTable(':table_zones', TABLE_ZONES);
        $Qcheck->bindInt(':zone_country_id', $_POST['country']);
        $Qcheck->execute();

        $entry_state_has_zones = ($Qcheck->numberOfRows() > 0);

        if ($entry_state_has_zones === true) {
          $Qzone = $osC_Database->query('select zone_id from :table_zones where zone_country_id = :zone_country_id and zone_code like :zone_code');
          $Qzone->bindTable(':table_zones', TABLE_ZONES);
          $Qzone->bindInt(':zone_country_id', $_POST['country']);
          $Qzone->bindValue(':zone_code', trim($_POST['state']));
          $Qzone->execute();

          if ($Qzone->numberOfRows() === 1) {
            $zone_id = $Qzone->valueInt('zone_id');
          } else {
            $Qzone = $osC_Database->query('select zone_id from :table_zones where zone_country_id = :zone_country_id and zone_name like :zone_name');
            $Qzone->bindTable(':table_zones', TABLE_ZONES);
            $Qzone->bindInt(':zone_country_id', $_POST['country']);
            $Qzone->bindValue(':zone_name', trim($_POST['state']) . '%');
            $Qzone->execute();

            if ($Qzone->numberOfRows() === 1) {
              $zone_id = $Qzone->valueInt('zone_id');
            } else {
              $messageStack->add('address_book', ENTRY_STATE_ERROR_SELECT);
            }
          }
        } else {
          if (strlen(trim($_POST['state'])) < ACCOUNT_STATE) {
            $messageStack->add('address_book', ENTRY_STATE_ERROR);
          }
        }
      }

      if ( (is_numeric($_POST['country']) === false) || ($_POST['country'] < 1) ) {
        $messageStack->add('address_book', ENTRY_COUNTRY_ERROR);
      }

      if (ACCOUNT_TELEPHONE > 0) {
        if (!isset($_POST['telephone']) || (strlen(trim($_POST['telephone'])) < ACCOUNT_TELEPHONE)) {
          $messageStack->add('address_book', ENTRY_TELEPHONE_NUMBER_ERROR);
        }
      }

      if (ACCOUNT_FAX > 0) {
        if (!isset($_POST['fax']) || (strlen(trim($_POST['fax'])) < ACCOUNT_FAX)) {
          $messageStack->add('address_book', ENTRY_FAX_NUMBER_ERROR);
        }
      }

      if ($messageStack->size('address_book') === 0) {
        if (is_numeric($id)) {
          $Qab = $osC_Database->query('update :table_address_book set customers_id = :customers_id, entry_gender = :entry_gender, entry_company = :entry_company, entry_firstname = :entry_firstname, entry_lastname = :entry_lastname, entry_street_address = :entry_street_address, entry_suburb = :entry_suburb, entry_postcode = :entry_postcode, entry_city = :entry_city, entry_state = :entry_state, entry_country_id = :entry_country_id, entry_zone_id = :entry_zone_id, entry_telephone = :entry_telephone, entry_fax = :entry_fax where address_book_id = :address_book_id and customers_id = :customers_id');
          $Qab->bindInt(':address_book_id', $id);
          $Qab->bindInt(':customers_id', $osC_Customer->id);
        } else {
          $Qab = $osC_Database->query('insert into :table_address_book (customers_id, entry_gender, entry_company, entry_firstname, entry_lastname, entry_street_address, entry_suburb, entry_postcode, entry_city, entry_state, entry_country_id, entry_zone_id, entry_telephone, entry_fax) values (:customers_id, :entry_gender, :entry_company, :entry_firstname, :entry_lastname, :entry_street_address, :entry_suburb, :entry_postcode, :entry_city, :entry_state, :entry_country_id, :entry_zone_id, :entry_telephone, :entry_fax)');
        }
        $Qab->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
        $Qab->bindInt(':customers_id', $osC_Customer->id);
        $Qab->bindValue(':entry_gender', (((ACCOUNT_GENDER > -1) && isset($_POST['gender']) && (($_POST['gender'] == 'm') || ($_POST['gender'] == 'f'))) ? $_POST['gender'] : ''));
        $Qab->bindValue(':entry_company', ((ACCOUNT_COMPANY > -1) ? trim($_POST['company']) : ''));
        $Qab->bindValue(':entry_firstname', trim($_POST['firstname']));
        $Qab->bindValue(':entry_lastname', trim($_POST['lastname']));
        $Qab->bindValue(':entry_street_address', trim($_POST['street_address']));
        $Qab->bindValue(':entry_suburb', ((ACCOUNT_SUBURB > -1) ? trim($_POST['suburb']) : ''));
        $Qab->bindValue(':entry_postcode', trim($_POST['postcode']));
        $Qab->bindValue(':entry_city', trim($_POST['city']));
        $Qab->bindValue(':entry_state', ((ACCOUNT_STATE > -1) ? (($zone_id > 0) ? '' : trim($_POST['state'])) : ''));
        $Qab->bindInt(':entry_country_id', $_POST['country']);
        $Qab->bindInt(':entry_zone_id', ((ACCOUNT_STATE > -1) ? (($zone_id > 0) ? $zone_id : 0) : ''));
        $Qab->bindValue(':entry_telephone', ((ACCOUNT_TELEPHONE > -1) ? trim($_POST['telephone']) : ''));
        $Qab->bindValue(':entry_fax', ((ACCOUNT_FAX > -1) ? trim($_POST['fax']) : ''));
        $Qab->execute();

        if ( ($osC_Customer->hasDefaultAddress() === false) || (isset($_POST['primary']) && ($_POST['primary'] == 'on')) ) {
          if (is_numeric($id) === false) {
            $id = $osC_Database->nextID();
          }

          $Qupdate = $osC_Database->query('update :table_customers set customers_default_address_id = :customers_default_address_id where customers_id = :customers_id');
          $Qupdate->bindTable(':table_customers', TABLE_CUSTOMERS);
          $Qupdate->bindInt(':customers_default_address_id', $id);
          $Qupdate->bindInt(':customers_id', $osC_Customer->id);
          $Qupdate->execute();

          if ($Qupdate->affectedRows() === 1) {
            $osC_Customer->setCountryID($_POST['country']);
            $osC_Customer->setZoneID(($zone_id > 0) ? (int)$zone_id : '0');
            $osC_Customer->setDefaultAddressID($id);
          }
        }

        $messageStack->add_session('address_book', SUCCESS_ADDRESS_BOOK_ENTRY_UPDATED, 'success');

        tep_redirect(tep_href_link(FILENAME_ACCOUNT, 'address_book', 'SSL'));
      }
    }

    function _delete($id) {
      global $messageStack, $osC_Database, $osC_Customer;

      if ($id != $osC_Customer->default_address_id) {
        $Qdelete = $osC_Database->query('delete from :table_address_book where address_book_id = :address_book_id and customers_id = :customers_id');
        $Qdelete->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
        $Qdelete->bindInt(':address_book_id', $id);
        $Qdelete->bindInt(':customers_id', $osC_Customer->id);
        $Qdelete->execute();

        if ($Qdelete->affectedRows() === 1) {
          $messageStack->add_session('address_book', SUCCESS_ADDRESS_BOOK_ENTRY_DELETED, 'success');
        }
      } else {
        $messageStack->add_session('address_book', WARNING_PRIMARY_ADDRESS_DELETION, 'warning');
      }

      tep_redirect(tep_href_link(FILENAME_ACCOUNT, 'address_book', 'SSL'));
    }
  }
?>
