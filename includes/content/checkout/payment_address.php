<?php
/*
  $Id:payment_address.php 188 2005-09-15 02:25:52 +0200 (Do, 15 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class osC_Checkout_Payment_address extends osC_Template {

/* Private variables */

    var $_module = 'payment_address',
        $_group = 'checkout',
        $_page_title,
        $_page_contents = 'checkout_payment_address.php';

/* Class constructor */

    function osC_Checkout_Payment_address() {
      global $osC_Session, $osC_Customer, $osC_Services, $osC_Language, $osC_NavigationHistory, $breadcrumb;

      $this->_page_title = $osC_Language->get('payment_address_heading');

      if ($osC_Customer->isLoggedOn() === false) {
        $osC_NavigationHistory->setSnapshot();

        tep_redirect(tep_href_link(FILENAME_ACCOUNT, 'login', 'SSL'));
      }

      if ($_SESSION['cart']->count_contents() < 1) {
        tep_redirect(tep_href_link(FILENAME_CHECKOUT, '', 'SSL'));
      }

      $this->addJavascriptFilename('templates/' . $this->_template . '/javascript/checkout_payment_address.js');
      $this->addJavascriptPhpFilename('includes/form_check.js.php');

// if no billing destination address was selected, use their own address as default
      if (isset($_SESSION['billto']) == false) {
        $_SESSION['billto'] = $osC_Customer->getDefaultAddressID();
      }

      if ($osC_Services->isStarted('breadcrumb')) {
        $breadcrumb->add($osC_Language->get('breadcrumb_checkout_payment'), tep_href_link(FILENAME_CHECKOUT, 'payment', 'SSL'));
        $breadcrumb->add($osC_Language->get('breadcrumb_checkout_payment_address'), tep_href_link(FILENAME_CHECKOUT, $this->_module, 'SSL'));
      }

      if (($_GET[$this->_module] == 'process')) {
        $this->_process();
      }
    }

    function &getListing() {
      global $osC_Database, $osC_Customer;

      $Qaddresses = $osC_Database->query('select address_book_id, entry_firstname as firstname, entry_lastname as lastname, entry_company as company, entry_street_address as street_address, entry_suburb as suburb, entry_city as city, entry_postcode as postcode, entry_state as state, entry_zone_id as zone_id, entry_country_id as country_id from :table_address_book where customers_id = :customers_id');
      $Qaddresses->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
      $Qaddresses->bindInt(':customers_id', $osC_Customer->getID());
      $Qaddresses->execute();

      return $Qaddresses;
    }

/* Private methods */

    function _process() {
      global $osC_Database, $osC_Session, $osC_Language, $osC_Customer, $messageStack;

// process a new billing address
      if (($osC_Customer->hasDefaultAddress() === false) || (tep_not_null($_POST['firstname']) && tep_not_null($_POST['lastname']) && tep_not_null($_POST['street_address'])) ) {
        if (ACCOUNT_GENDER > 0) {
          if (!isset($_POST['gender']) || (($_POST['gender'] != 'm') && ($_POST['gender'] != 'f'))) {
            $messageStack->add('checkout_address', $osC_Language->get('field_customer_gender_error'));
          }
        }

        if (!isset($_POST['firstname']) || (strlen(trim($_POST['firstname'])) < ACCOUNT_FIRST_NAME)) {
          $messageStack->add('checkout_address', sprintf($osC_Language->get('field_customer_first_name_error'), ACCOUNT_FIRST_NAME));
        }

        if (!isset($_POST['lastname']) || (strlen(trim($_POST['lastname'])) < ACCOUNT_LAST_NAME)) {
          $messageStack->add('checkout_address', sprintf($osC_Language->get('field_customer_last_name_error'), ACCOUNT_LAST_NAME));
        }

        if (ACCOUNT_COMPANY > 0) {
          if (!isset($_POST['company']) || (strlen(trim($_POST['company'])) < ACCOUNT_COMPANY)) {
            $messageStack->add('checkout_address', sprintf($osC_Language->get('field_customer_company_error'), ACCOUNT_COMPANY));
          }
        }

        if (!isset($_POST['street_address']) || (strlen(trim($_POST['street_address'])) < ACCOUNT_STREET_ADDRESS)) {
          $messageStack->add('checkout_address', sprintf($osC_Language->get('field_customer_street_address_error'), ACCOUNT_STREET_ADDRESS));
        }

        if (ACCOUNT_SUBURB > 0) {
          if (!isset($_POST['suburb']) || (strlen(trim($_POST['suburb'])) < ACCOUNT_SUBURB)) {
            $messageStack->add('checkout_address', sprintf($osC_Language->get('field_customer_suburb_error'), ACCOUNT_SUBURB));
          }
        }

        if (!isset($_POST['postcode']) || (strlen(trim($_POST['postcode'])) < ACCOUNT_POST_CODE)) {
          $messageStack->add('checkout_address', sprintf($osC_Language->get('field_customer_post_code_error'), ACCOUNT_POST_CODE));
        }

        if (!isset($_POST['city']) || (strlen(trim($_POST['city'])) < ACCOUNT_CITY)) {
          $messageStack->add('checkout_address', sprintf($osC_Language->get('field_customer_city_error'), ACCOUNT_CITY));
        }

        if (ACCOUNT_STATE > 0) {
          $zone_id = 0;

          $Qcheck = $osC_Database->query('select zone_id from :table_zones where zone_country_id = :zone_country_id limit 1');
          $Qcheck->bindTable(':table_zones', TABLE_ZONES);
          $Qcheck->bindInt(':zone_country_id', $_POST['country']);
          $Qcheck->execute();

          $entry_state_has_zones = ($Qcheck->numberOfRows() > 0);

          $Qcheck->freeResult();

          if ($entry_state_has_zones === true) {
            $Qzone = $osC_Database->query('select zone_id from :table_zones where zone_country_id = :zone_country_id and zone_code like :zone_code');
            $Qzone->bindTable(':table_zones', TABLE_ZONES);
            $Qzone->bindInt(':zone_country_id', $_POST['country']);
            $Qzone->bindValue(':zone_code', $_POST['state']);
            $Qzone->execute();

            if ($Qzone->numberOfRows() === 1) {
              $zone_id = $Qzone->valueInt('zone_id');
            } else {
              $Qzone = $osC_Database->query('select zone_id from :table_zones where zone_country_id = :zone_country_id and zone_name like :zone_name');
              $Qzone->bindTable(':table_zones', TABLE_ZONES);
              $Qzone->bindInt(':zone_country_id', $_POST['country']);
              $Qzone->bindValue(':zone_name', $_POST['state'] . '%');
              $Qzone->execute();

              if ($Qzone->numberOfRows() === 1) {
                $zone_id = $Qzone->valueInt('zone_id');
              } else {
                $messageStack->add('checkout_address', $osC_Language->get('field_customer_state_select_pull_down_error'));
              }
            }

            $Qzone->freeResult();
          } else {
            if (strlen(trim($_POST['state'])) < ACCOUNT_STATE) {
              $messageStack->add('checkout_address', sprintf($osC_Language->get('field_customer_state_error'), ACCOUNT_STATE));
            }
          }
        }

        if ( (is_numeric($_POST['country']) === false) || ($_POST['country'] < 1) ) {
          $messageStack->add('checkout_address', $osC_Language->get('field_customer_country_error'));
        }

        if (ACCOUNT_TELEPHONE > 0) {
          if (!isset($_POST['telephone']) || (strlen(trim($_POST['telephone'])) < ACCOUNT_TELEPHONE)) {
            $messageStack->add('checkout_address', sprintf($osC_Language->get('field_customer_telephone_number_error'), ACCOUNT_TELEPHONE));
          }
        }

        if (ACCOUNT_FAX > 0) {
          if (!isset($_POST['fax']) || (strlen(trim($_POST['fax'])) < ACCOUNT_FAX)) {
            $messageStack->add('checkout_address', sprintf($osC_Language->get('field_customer_fax_number_error'), ACCOUNT_FAX));
          }
        }

        if ($messageStack->size('checkout_address') === 0) {
          $Qab = $osC_Database->query('insert into :table_address_book (customers_id, entry_gender, entry_company, entry_firstname, entry_lastname, entry_street_address, entry_suburb, entry_postcode, entry_city, entry_state, entry_country_id, entry_zone_id, entry_telephone, entry_fax) values (:customers_id, :entry_gender, :entry_company, :entry_firstname, :entry_lastname, :entry_street_address, :entry_suburb, :entry_postcode, :entry_city, :entry_state, :entry_country_id, :entry_zone_id, :entry_telephone, :entry_fax)');
          $Qab->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
          $Qab->bindInt(':customers_id', $osC_Customer->getID());
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

          if ($Qab->affectedRows() === 1) {
            $address_book_id = $osC_Database->nextID();

            if ($osC_Customer->hasDefaultAddress() === false) {
              $Qcustomer = $osC_Database->query('update :table_customers set customers_default_address_id = :customers_default_address_id where customers_id = :customers_id');
              $Qcustomer->bindTable(':table_customers', TABLE_CUSTOMERS);
              $Qcustomer->bindInt(':customers_default_address_id', $address_book_id);
              $Qcustomer->bindInt(':customers_id', $osC_Customer->getID());
              $Qcustomer->execute();

              $osC_Customer->setCountryID($_POST['country']);
              $osC_Customer->setZoneID($zone_id);
              $osC_Customer->setDefaultAddressID($address_book_id);
            }

            $_SESSION['billto'] = $address_book_id;
            unset($_SESSION['payment']);

            tep_redirect(tep_href_link(FILENAME_CHECKOUT, 'payment', 'SSL'));
          } else {
            $messageStack->add('checkout_address', 'Error inserting into address book table.');
          }
        }
// process the selected billing destination
      } elseif (isset($_POST['address'])) {
        $reset_payment = false;
        if (isset($_SESSION['billto'])) {
          if ($_SESSION['billto'] != $_POST['address']) {
            if (isset($_SESSION['payment'])) {
              $reset_payment = true;
            }
          }
        }

        $_SESSION['billto'] = $_POST['address'];

        $Qcheck = $osC_Database->query('select count(*) as total from :table_address_book where customers_id = :customers_id and address_book_id = :address_book_id');
        $Qcheck->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
        $Qcheck->bindInt(':customers_id', $osC_Customer->getID());
        $Qcheck->bindInt(':address_book_id', $_SESSION['billto']);
        $Qcheck->execute();

        if ($Qcheck->valueInt('total') == 1) {
          if ($reset_payment == true) {
            unset($_SESSION['payment']);
          }

          tep_redirect(tep_href_link(FILENAME_CHECKOUT, 'payment', 'SSL'));
        } else {
          unset($_SESSION['billto']);
        }
// no addresses to select from - customer decided to keep the current assigned address
      } else {
        $_SESSION['billto'] = $osC_Customer->getDefaultAddressID();

        tep_redirect(tep_href_link(FILENAME_CHECKOUT, 'payment', 'SSL'));
      }
    }
  }
?>
