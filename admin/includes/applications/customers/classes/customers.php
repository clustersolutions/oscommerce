<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Customers_Admin {
    public static function getData($id, $key = null) {
      global $osC_Database;

      $Qcustomer = $osC_Database->query('select c.*, date_format(c.customers_dob, "%Y") as customers_dob_year, date_format(c.customers_dob, "%m") as customers_dob_month, date_format(c.customers_dob, "%d") as customers_dob_date, ab.* from :table_customers c left join :table_address_book ab on (c.customers_default_address_id = ab.address_book_id and c.customers_id = ab.customers_id) where c.customers_id = :customers_id');
      $Qcustomer->bindTable(':table_customers', TABLE_CUSTOMERS);
      $Qcustomer->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
      $Qcustomer->bindInt(':customers_id', $id);
      $Qcustomer->execute();

      $data = $Qcustomer->toArray();

      $Qreviews = $osC_Database->query('select count(*) as total from :table_reviews where customers_id = :customers_id');
      $Qreviews->bindTable(':table_reviews', TABLE_REVIEWS);
      $Qreviews->bindInt(':customers_id', $id);
      $Qreviews->execute();

      $data['total_reviews'] = $Qreviews->valueInt('total');

      $Qreviews->freeResult();
      $Qcustomer->freeResult();

      $data['customers_full_name'] = $data['customers_firstname'] . ' ' . $data['customers_lastname'];

      if ( !empty($key) ) {
        return $data[$key];
      }

      return $data;
    }

    public static function getAddressBookData($customer_id, $address_book_id = null) {
      global $osC_Database;

      $Qab = $osC_Database->query('select ab.address_book_id, ab.entry_gender as gender, ab.entry_firstname as firstname, ab.entry_lastname as lastname, ab.entry_company as company, ab.entry_street_address as street_address, ab.entry_suburb as suburb, ab.entry_city as city, ab.entry_postcode as postcode, ab.entry_state as state, ab.entry_zone_id as zone_id, ab.entry_country_id as country_id, ab.entry_telephone as telephone_number, ab.entry_fax as fax_number, z.zone_code as zone_code, c.countries_name as country_title from :table_address_book ab left join :table_zones z on (ab.entry_zone_id = z.zone_id), :table_countries c where');

      if ( is_numeric($address_book_id) ) {
        $Qab->appendQuery('ab.address_book_id = :address_book_id and');
        $Qab->bindInt(':address_book_id', $address_book_id);
      }

      $Qab->appendQuery('ab.customers_id = :customers_id and ab.entry_country_id = c.countries_id');
      $Qab->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
      $Qab->bindTable(':table_zones', TABLE_ZONES);
      $Qab->bindTable(':table_countries', TABLE_COUNTRIES);
      $Qab->bindInt(':customers_id', $customer_id);
      $Qab->execute();

      if ( is_numeric($address_book_id) ) {
        $data = $Qab->toArray();

        $Qab->freeResult();

        return $data;
      }

      return $Qab;
    }

    public static function save($id = null, $data, $send_email = true) {
      global $osC_Database, $osC_Language;

      $error = false;

      $osC_Database->startTransaction();

      if ( is_numeric($id) ) {
        $Qcustomer = $osC_Database->query('update :table_customers set customers_gender = :customers_gender, customers_firstname = :customers_firstname, customers_lastname = :customers_lastname, customers_email_address = :customers_email_address, customers_dob = :customers_dob, customers_newsletter = :customers_newsletter, customers_status = :customers_status, date_account_last_modified = :date_account_last_modified where customers_id = :customers_id');
        $Qcustomer->bindRaw(':date_account_last_modified', 'now()');
        $Qcustomer->bindInt(':customers_id', $id);
      } else {
        $Qcustomer = $osC_Database->query('insert into :table_customers (customers_gender, customers_firstname, customers_lastname, customers_email_address, customers_dob, customers_newsletter, customers_status, number_of_logons, date_account_created) values (:customers_gender, :customers_firstname, :customers_lastname, :customers_email_address, :customers_dob, :customers_newsletter, :customers_status, :number_of_logons, :date_account_created)');
        $Qcustomer->bindInt(':number_of_logons', 0);
        $Qcustomer->bindRaw(':date_account_created', 'now()');
      }

      $Qcustomer->bindTable(':table_customers', TABLE_CUSTOMERS);
      $Qcustomer->bindValue(':customers_gender', $data['gender']);
      $Qcustomer->bindValue(':customers_firstname', $data['firstname']);
      $Qcustomer->bindValue(':customers_lastname', $data['lastname']);
      $Qcustomer->bindValue(':customers_email_address', $data['email_address']);
      $Qcustomer->bindValue(':customers_dob', $data['dob_year'] . '-' . $data['dob_month'] . '-' . $data['dob_day'] . ' 00:00:00');
      $Qcustomer->bindInt(':customers_newsletter', $data['newsletter']);
      $Qcustomer->bindInt(':customers_status', $data['status']);
      $Qcustomer->setLogging($_SESSION['module'], $id);
      $Qcustomer->execute();

      if ( !$osC_Database->isError() ) {
        if ( !empty($data['password']) ) {
          $customer_id = ( !empty($id) ) ? $id : $osC_Database->nextID();

          $Qpassword = $osC_Database->query('update :table_customers set customers_password = :customers_password where customers_id = :customers_id');
          $Qpassword->bindTable(':table_customers', TABLE_CUSTOMERS);
          $Qpassword->bindValue(':customers_password', osc_encrypt_string(trim($data['password'])));
          $Qpassword->bindInt(':customers_id', $customer_id);
          $Qpassword->setLogging($_SESSION['module'], $customer_id);
          $Qpassword->execute();

          if ( $osC_Database->isError() ) {
            $error = true;
          }
        }
      }

      if ( $error === false ) {
        $osC_Database->commitTransaction();

        if ( $send_email === true ) {
          if ( empty($id) ) {
            $full_name = trim($data['firstname'] . ' ' . $data['lastname']);

            $email_text = '';

            if ( ACCOUNT_GENDER > -1 ) {
              if ( $data['gender'] == 'm' ) {
                $email_text .= sprintf($osC_Language->get('email_greet_mr'), trim($data['lastname'])) . "\n\n";
              } else {
                $email_text .= sprintf($osC_Language->get('email_greet_ms'), trim($data['lastname'])) . "\n\n";
              }
            } else {
              $email_text .= sprintf($osC_Language->get('email_greet_general'), $full_name) . "\n\n";
            }

            $email_text .= sprintf($osC_Language->get('email_text'), STORE_NAME, STORE_OWNER_EMAIL_ADDRESS, trim($data['password']));

            osc_email($full_name, $data['email_address'], $osC_Language->get('email_subject'), $email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
          }
        }

        return true;
      }

      $osC_Database->rollbackTransaction();

      return false;
    }

    public static function delete($id, $delete_reviews = true) {
      global $osC_Database, $osC_Session;

      $error = false;

      $osC_Database->startTransaction();

      if ( $delete_reviews === true ) {
        $Qreviews = $osC_Database->query('delete from :table_reviews where customers_id = :customers_id');
        $Qreviews->bindTable(':table_reviews', TABLE_REVIEWS);
        $Qreviews->bindInt(':customers_id', $id);
        $Qreviews->setLogging($_SESSION['module'], $id);
        $Qreviews->execute();

        if ( $osC_Database->isError() ) {
          $error = true;
        }
      } else {
        $Qcheck = $osC_Database->query('select reviews_id from :table_reviews where customers_id = :customers_id limit 1');
        $Qcheck->bindTable(':table_reviews', TABLE_REVIEWS);
        $Qcheck->bindInt(':customers_id', $id);
        $Qcheck->execute();

        if ( $Qcheck->numberOfRows() > 0 ) {
          $Qreviews = $osC_Database->query('update :table_reviews set customers_id = null where customers_id = :customers_id');
          $Qreviews->bindTable(':table_reviews', TABLE_REVIEWS);
          $Qreviews->bindInt(':customers_id', $id);
          $Qreviews->setLogging($_SESSION['module'], $id);
          $Qreviews->execute();

          if ( $osC_Database->isError() ) {
            $error = true;
          }
        }
      }

      if ( $error === false ) {
        $Qab = $osC_Database->query('delete from :table_address_book where customers_id = :customers_id');
        $Qab->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
        $Qab->bindInt(':customers_id', $id);
        $Qab->setLogging($_SESSION['module'], $id);
        $Qab->execute();

        if ( $osC_Database->isError() ) {
          $error = true;
        }
      }

      if ( $error === false ) {
        $Qsc = $osC_Database->query('delete from :table_shopping_carts where customers_id = :customers_id');
        $Qsc->bindTable(':table_shopping_carts', TABLE_SHOPPING_CARTS);
        $Qsc->bindInt(':customers_id', $id);
        $Qsc->execute();

        if ( $osC_Database->isError() ) {
          $error = true;
        }
      }

      if ( $error === false ) {
        $Qsccvv = $osC_Database->query('delete from :table_shopping_carts_custom_variants_values where customers_id = :customers_id');
        $Qsccvv->bindTable(':table_shopping_carts_custom_variants_values', TABLE_SHOPPING_CARTS_CUSTOM_VARIANTS_VALUES);
        $Qsccvv->bindInt(':customers_id', $id);
        $Qsccvv->execute();

        if ( $osC_Database->isError() ) {
          $error = true;
        }
      }

      if ( $error === false ) {
        $Qpn = $osC_Database->query('delete from :table_products_notifications where customers_id = :customers_id');
        $Qpn->bindTable(':table_products_notifications', TABLE_PRODUCTS_NOTIFICATIONS);
        $Qpn->bindInt(':customers_id', $id);
        $Qpn->setLogging($_SESSION['module'], $id);
        $Qpn->execute();

        if ( $osC_Database->isError() ) {
          $error = true;
        }
      }

      if ( $error === false ) {
        $Qcheck = $osC_Database->query('select session_id from :table_whos_online where customer_id = :customer_id');
        $Qcheck->bindTable(':table_whos_online', TABLE_WHOS_ONLINE);
        $Qcheck->bindInt(':customer_id', $id);
        $Qcheck->execute();

        if ( $Qcheck->numberOfRows() > 0 ) {
          $osC_Session->delete($Qcheck->value('session_id'));

          $Qwho = $osC_Database->query('delete from :table_whos_online where customer_id = :customer_id');
          $Qwho->bindTable(':table_whos_online', TABLE_WHOS_ONLINE);
          $Qwho->bindInt(':customer_id', $id);
          $Qwho->execute();

          if ( $osC_Database->isError() ) {
            $error = true;
          }
        }
      }

      if ( $error === false ) {
        $Qcustomers = $osC_Database->query('delete from :table_customers where customers_id = :customers_id');
        $Qcustomers->bindTable(':table_customers', TABLE_CUSTOMERS);
        $Qcustomers->bindInt(':customers_id', $id);
        $Qcustomers->setLogging($_SESSION['module'], $id);
        $Qcustomers->execute();

        if ( $osC_Database->isError() ) {
          $error = true;
        }
      }

      if ( $error === false ) {
        $osC_Database->commitTransaction();

        return true;
      }

      $osC_Database->rollbackTransaction();

      return false;
    }

    public static function saveAddress($id = null, $data) {
      global $osC_Database;

      $error = false;

      $osC_Database->startTransaction();

      $Qcustomer = $osC_Database->query('select customers_gender, customers_firstname, customers_lastname, customers_email_address, customers_default_address_id from :table_customers where customers_id = :customers_id');
      $Qcustomer->bindTable(':table_customers', TABLE_CUSTOMERS);
      $Qcustomer->bindInt(':customers_id', $data['customer_id']);
      $Qcustomer->execute();

      if ( is_numeric($id) ) {
        $Qab = $osC_Database->query('update :table_address_book set entry_gender = :entry_gender, entry_company = :entry_company, entry_firstname = :entry_firstname, entry_lastname = :entry_lastname, entry_street_address = :entry_street_address, entry_suburb = :entry_suburb, entry_postcode = :entry_postcode, entry_city = :entry_city, entry_state = :entry_state, entry_country_id = :entry_country_id, entry_zone_id = :entry_zone_id, entry_telephone = :entry_telephone, entry_fax = :entry_fax where address_book_id = :address_book_id and customers_id = :customers_id');
        $Qab->bindInt(':address_book_id', $id);
      } else {
        $Qab = $osC_Database->query('insert into :table_address_book (customers_id, entry_gender, entry_company, entry_firstname, entry_lastname, entry_street_address, entry_suburb, entry_postcode, entry_city, entry_state, entry_country_id, entry_zone_id, entry_telephone, entry_fax) values (:customers_id, :entry_gender, :entry_company, :entry_firstname, :entry_lastname, :entry_street_address, :entry_suburb, :entry_postcode, :entry_city, :entry_state, :entry_country_id, :entry_zone_id, :entry_telephone, :entry_fax)');
      }

      $Qab->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
      $Qab->bindInt(':customers_id', $data['customer_id']);
      $Qab->bindValue(':entry_gender', $data['gender']);
      $Qab->bindValue(':entry_company', $data['company']);
      $Qab->bindValue(':entry_firstname', $data['firstname']);
      $Qab->bindValue(':entry_lastname', $data['lastname']);
      $Qab->bindValue(':entry_street_address', $data['street_address']);
      $Qab->bindValue(':entry_suburb', $data['suburb']);
      $Qab->bindValue(':entry_postcode', $data['postcode']);
      $Qab->bindValue(':entry_city', $data['city']);
      $Qab->bindValue(':entry_state', $data['state']);
      $Qab->bindInt(':entry_country_id', $data['country_id']);
      $Qab->bindInt(':entry_zone_id', $data['zone_id']);
      $Qab->bindValue(':entry_telephone', $data['telephone']);
      $Qab->bindValue(':entry_fax', $data['fax']);
      $Qab->setLogging($_SESSION['module'], $id);
      $Qab->execute();

      if ( !$osC_Database->isError() ) {
        if ( ( $Qcustomer->valueInt('customers_default_address_id') < 1 ) || ( $data['primary'] === true ) ) {
          $address_book_id = ( is_numeric($id) ? $id : $osC_Database->nextID() );

          $Qupdate = $osC_Database->query('update :table_customers set customers_default_address_id = :customers_default_address_id where customers_id = :customers_id');
          $Qupdate->bindTable(':table_customers', TABLE_CUSTOMERS);
          $Qupdate->bindInt(':customers_default_address_id', $address_book_id);
          $Qupdate->bindInt(':customers_id', $data['customer_id']);
          $Qupdate->setLogging($_SESSION['module'], $address_book_id);
          $Qupdate->execute();

          if ( $osC_Database->isError() ) {
            $error = true;
          }
        }
      } else {
        $error = true;
      }

      if ( $error === false ) {
        $osC_Database->commitTransaction();

        return true;
      }

      $osC_Database->rollbackTransaction();

      return false;
    }

    public static function deleteAddress($id, $customer_id = null) {
      global $osC_Database;

      $Qdelete = $osC_Database->query('delete from :table_address_book where address_book_id = :address_book_id');

      if ( !empty($customer_id) ) {
        $Qdelete->appendQuery('and customers_id = :customers_id');
        $Qdelete->bindInt(':customers_id', $customer_id);
      }

      $Qdelete->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
      $Qdelete->bindInt(':address_book_id', $id);
      $Qdelete->setLogging($_SESSION['module'], $id);
      $Qdelete->execute();

      if ( !$osC_Database->isError() ) {
        return true;
      }

      return false;
    }
  }
?>
