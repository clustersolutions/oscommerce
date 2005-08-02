<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  class osC_Customer {
    var $is_logged_on,
        $id,
        $gender,
        $first_name,
        $last_name,
        $full_name,
        $email_address,
        $default_address_id,
        $country_id,
        $zone_id;

// class constructor
    function osC_Customer() {
      $this->setIsLoggedOn(false);
    }

// class methods
    function setCustomerData($customer_id = -1) {
      if (is_numeric($customer_id) && ($customer_id > 0)) {
        global $osC_Database;

        $Qcustomer = $osC_Database->query('select customers_gender, customers_firstname, customers_lastname, customers_email_address, customers_default_address_id from :table_customers where customers_id = :customers_id');
        $Qcustomer->bindRaw(':table_customers', TABLE_CUSTOMERS);
        $Qcustomer->bindInt(':customers_id', $customer_id);
        $Qcustomer->execute();

        if ($Qcustomer->numberOfRows() === 1) {
          $this->setIsLoggedOn(true);
          $this->setID($customer_id);
          $this->setGender($Qcustomer->value('customers_gender'));
          $this->setFirstName($Qcustomer->value('customers_firstname'));
          $this->setLastName($Qcustomer->value('customers_lastname'));
          $this->setFullName();
          $this->setEmailAddress($Qcustomer->value('customers_email_address'));

          if (is_numeric($Qcustomer->value('customers_default_address_id')) && ($Qcustomer->value('customers_default_address_id') > 0)) {
            $Qab = $osC_Database->query('select entry_country_id, entry_zone_id from :table_address_book where address_book_id = :address_book_id and customers_id = :customers_id');
            $Qab->bindRaw(':table_address_book', TABLE_ADDRESS_BOOK);
            $Qab->bindInt(':address_book_id', $Qcustomer->value('customers_default_address_id'));
            $Qab->bindInt(':customers_id', $customer_id);
            $Qab->execute();

            if ($Qab->numberOfRows() === 1) {
              $this->setCountryID($Qab->value('entry_country_id'));
              $this->setZoneID($Qab->value('entry_zone_id'));
              $this->setDefaultAddressID($Qcustomer->value('customers_default_address_id'));

              $Qab->freeResult();
            }
          }
        }

        $Qcustomer->freeResult();
      }
    }

    function setIsLoggedOn($state) {
      if ($state === true) {
        $this->is_logged_on = true;
      } else {
        $this->is_logged_on = false;
      }
    }

    function isLoggedOn() {
      if ($this->is_logged_on === true) {
        return true;
      }

      return false;
    }

    function isGuest() {
      return !$this->isLoggedOn();
    }

    function setID($id) {
      $this->id = $id;
    }

    function setDefaultAddressID($id) {
      $this->default_address_id = $id;
    }

    function hasDefaultAddress() {
      if (is_numeric($this->default_address_id) && ($this->default_address_id > 0)) {
        return true;
      } else {
        return false;
      }
    }

    function setGender($gender) {
      $this->gender = $gender;
    }

    function setFirstName($firstname) {
      $this->first_name = $firstname;
    }

    function setLastName($lastname) {
      $this->last_name = $lastname;
    }

    function setFullName($fullname = '') {
      if (empty($fullname)) {
        $this->full_name = $this->first_name . ' ' . $this->last_name;
      } else {
        $this->full_name = $fullname;
      }
    }

    function setEmailAddress($email_address) {
      $this->email_address = $email_address;
    }

    function setCountryID($id) {
      $this->country_id = $id;
    }

    function setZoneID($id) {
      $this->zone_id = $id;
    }
  }
?>
