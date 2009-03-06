<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

/**
 * The osC_Account class manages customer accounts
 */

  class osC_Account {

/**
 * Returns the account information for the current customer
 *
 * @access public
 * @return object
 */

    public static function &getEntry() {
      global $osC_Database, $osC_Customer;

      $Qaccount = $osC_Database->query('select customers_gender, customers_firstname, customers_lastname, date_format(customers_dob, "%Y") as customers_dob_year, date_format(customers_dob, "%m") as customers_dob_month, date_format(customers_dob, "%d") as customers_dob_date, customers_email_address from :table_customers where customers_id = :customers_id');
      $Qaccount->bindTable(':table_customers', TABLE_CUSTOMERS);
      $Qaccount->bindInt(':customers_id', $osC_Customer->getID());
      $Qaccount->execute();

      return $Qaccount;
    }

/**
 * Returns the customer ID from a given email address
 *
 * @param string $email_address The customers email address
 * @access public
 */

    public static function getID($email_address) {
      global $osC_Database;

      $Quser = $osC_Database->query('select customers_id from :table_customers where customers_email_address = :customers_email_address limit 1');
      $Quser->bindTable(':table_customers', TABLE_CUSTOMERS);
      $Quser->bindValue(':customers_email_address', $email_address);
      $Quser->execute();

      if ( $Quser->numberOfRows() === 1 ) {
        return $Quser->valueInt('customers_id');
      }

      return false;
    }

/**
 * Stores a new customer account entry in the database
 *
 * @param array $data An array containing the customers information
 * @access public
 * @return boolean
 */

    public static function createEntry($data) {
      global $osC_Database, $osC_Session, $osC_Language, $osC_ShoppingCart, $osC_Customer, $osC_NavigationHistory;

      $Qcustomer = $osC_Database->query('insert into :table_customers (customers_firstname, customers_lastname, customers_email_address, customers_newsletter, customers_status, customers_ip_address, customers_password, customers_gender, customers_dob, number_of_logons, date_account_created) values (:customers_firstname, :customers_lastname, :customers_email_address, :customers_newsletter, :customers_status, :customers_ip_address, :customers_password, :customers_gender, :customers_dob, :number_of_logons, :date_account_created)');
      $Qcustomer->bindTable(':table_customers', TABLE_CUSTOMERS);
      $Qcustomer->bindValue(':customers_firstname', $data['firstname']);
      $Qcustomer->bindValue(':customers_lastname', $data['lastname']);
      $Qcustomer->bindValue(':customers_email_address', $data['email_address']);
      $Qcustomer->bindValue(':customers_newsletter', (isset($data['newsletter']) && ($data['newsletter'] == '1') ? '1' : ''));
      $Qcustomer->bindValue(':customers_status', '1');
      $Qcustomer->bindValue(':customers_ip_address', osc_get_ip_address());
      $Qcustomer->bindValue(':customers_password', osc_encrypt_string($data['password']));
      $Qcustomer->bindValue(':customers_gender', (((ACCOUNT_GENDER > -1) && isset($data['gender']) && (($data['gender'] == 'm') || ($data['gender'] == 'f'))) ? $data['gender'] : ''));
      $Qcustomer->bindValue(':customers_dob', ((ACCOUNT_DATE_OF_BIRTH == '1') ? date('Ymd', $data['dob']) : ''));
      $Qcustomer->bindInt(':number_of_logons', 0);
      $Qcustomer->bindRaw(':date_account_created', 'now()');
      $Qcustomer->execute();

      if ( $Qcustomer->affectedRows() === 1 ) {
        $customer_id = $osC_Database->nextID();

        if ( SERVICE_SESSION_REGENERATE_ID == '1' ) {
          $osC_Session->recreate();
        }

        $osC_Customer->setCustomerData($customer_id);

// restore cart contents
        $osC_ShoppingCart->synchronizeWithDatabase();

        $osC_NavigationHistory->removeCurrentPage();

// build the welcome email content
        if ( (ACCOUNT_GENDER > -1) && isset($data['gender']) ) {
           if ( $data['gender'] == 'm' ) {
             $email_text = sprintf($osC_Language->get('email_addressing_gender_male'), $osC_Customer->getLastName()) . "\n\n";
           } else {
             $email_text = sprintf($osC_Language->get('email_addressing_gender_female'), $osC_Customer->getLastName()) . "\n\n";
           }
        } else {
          $email_text = sprintf($osC_Language->get('email_addressing_gender_unknown'), $osC_Customer->getName()) . "\n\n";
        }

        $email_text .= sprintf($osC_Language->get('email_create_account_body'), STORE_NAME, STORE_OWNER_EMAIL_ADDRESS);

        osc_email($osC_Customer->getName(), $osC_Customer->getEmailAddress(), sprintf($osC_Language->get('email_create_account_subject'), STORE_NAME), $email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);

        return true;
      }

      return false;
    }

/**
 * Update the current customer account record in the database
 *
 * @param array $data An array containing the customer account information
 * @access public
 * @return boolean
 */

    public static function saveEntry($data) {
      global $osC_Database, $osC_Customer;

      $Qcustomer = $osC_Database->query('update :table_customers set customers_gender = :customers_gender, customers_firstname = :customers_firstname, customers_lastname = :customers_lastname, customers_email_address = :customers_email_address, customers_dob = :customers_dob, date_account_last_modified = :date_account_last_modified where customers_id = :customers_id');
      $Qcustomer->bindTable(':table_customers', TABLE_CUSTOMERS);
      $Qcustomer->bindValue(':customers_gender', ((ACCOUNT_GENDER > -1) && isset($data['gender']) && (($data['gender'] == 'm') || ($data['gender'] == 'f'))) ? $data['gender'] : '');
      $Qcustomer->bindValue(':customers_firstname', $data['firstname']);
      $Qcustomer->bindValue(':customers_lastname', $data['lastname']);
      $Qcustomer->bindValue(':customers_email_address', $data['email_address']);
      $Qcustomer->bindValue(':customers_dob', (ACCOUNT_DATE_OF_BIRTH == '1') ? date('Ymd', $data['dob']) : '');
      $Qcustomer->bindRaw(':date_account_last_modified', 'now()');
      $Qcustomer->bindInt(':customers_id', $osC_Customer->getID());
      $Qcustomer->execute();

      return ( $Qcustomer->affectedRows() === 1 );
    }

/**
 * Updates the password in a customers account
 *
 * @param string $password The new password
 * @param integer $customer_id The ID of the customer account to update
 * @access public
 * @return boolean
 */

    public static function savePassword($password, $customer_id = null) {
      global $osC_Database, $osC_Customer;

      if ( !is_numeric($customer_id) ) {
        $customer_id = $osC_Customer->getID();
      }

      $Qcustomer = $osC_Database->query('update :table_customers set customers_password = :customers_password, date_account_last_modified = :date_account_last_modified where customers_id = :customers_id');
      $Qcustomer->bindTable(':table_customers', TABLE_CUSTOMERS);
      $Qcustomer->bindValue(':customers_password', osc_encrypt_string($password));
      $Qcustomer->bindRaw(':date_account_last_modified', 'now()');
      $Qcustomer->bindInt(':customers_id', $customer_id);
      $Qcustomer->execute();

      return ( $Qcustomer->affectedRows() === 1 );
    }

/**
 * Checks if a customer account record exists with the provided e-mail address
 *
 * @param string $email_address The e-mail address to check for
 * @access public
 * @return boolean
 */

    public static function checkEntry($email_address) {
      global $osC_Database;

      $Qcheck = $osC_Database->query('select customers_id from :table_customers where customers_email_address = :customers_email_address limit 1');
      $Qcheck->bindTable(':table_customers', TABLE_CUSTOMERS);
      $Qcheck->bindValue(':customers_email_address', $email_address);
      $Qcheck->execute();

      return ( $Qcheck->numberOfRows() === 1 );
    }

/**
 * Checks if a password matches the current or provided customer account
 *
 * @param string $password The unencrypted password to confirm
 * @param string $email_address The email address of the customer account to check against
 * @access public
 * @return boolean
 */

    public static function checkPassword($password, $email_address = null) {
      global $osC_Database, $osC_Customer;

      if ( empty($email_address) ) {
        $Qcheck = $osC_Database->query('select customers_password from :table_customers where customers_id = :customers_id');
        $Qcheck->bindTable(':table_customers', TABLE_CUSTOMERS);
        $Qcheck->bindInt(':customers_id', $osC_Customer->getID());
        $Qcheck->execute();
      } else {
        $Qcheck = $osC_Database->query('select customers_password from :table_customers where customers_email_address = :customers_email_address limit 1');
        $Qcheck->bindTable(':table_customers', TABLE_CUSTOMERS);
        $Qcheck->bindValue(':customers_email_address', $email_address);
        $Qcheck->execute();
      }

      if ( $Qcheck->numberOfRows() === 1 ) {
        if ( (strlen($password) > 0) && (strlen($Qcheck->value('customers_password')) > 0) ) {
          $stack = explode(':', $Qcheck->value('customers_password'));

          if ( sizeof($stack) === 2 ) {
            return ( md5($stack[1] . $password) == $stack[0] );
          }
        }
      }

      return false;
    }

/**
 * Checks if an e-mail address already exists in another customer account record
 *
 * @param string $email_address The e-mail address to check
 * @access public
 * @return boolean
 */

    public static function checkDuplicateEntry($email_address) {
      global $osC_Database, $osC_Customer;

      $Qcheck = $osC_Database->query('select customers_id from :table_customers where customers_email_address = :customers_email_address and customers_id != :customers_id limit 1');
      $Qcheck->bindTable(':table_customers', TABLE_CUSTOMERS);
      $Qcheck->bindValue(':customers_email_address', $email_address);
      $Qcheck->bindInt(':customers_id', $osC_Customer->getID());
      $Qcheck->execute();

      return ( $Qcheck->numberOfRows() === 1 );
    }
  }
?>
