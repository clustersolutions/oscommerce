<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2009 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  require('includes/classes/address_book.php');
  require('includes/classes/shipping.php');

  class osC_Checkout_Shipping extends osC_Template {
    protected $_module = 'shipping';
    protected $_group = 'checkout';
    protected $_page_title;
    protected $_page_contents = 'shipping.php';
    protected $_page_image = 'table_background_delivery.gif';

    public function __construct() {
      global $osC_ShoppingCart, $osC_Customer, $osC_Services, $osC_Breadcrumb, $osC_Shipping, $osC_oiAddress;

// redirect to shopping cart if shopping cart is empty
      if ( !$osC_ShoppingCart->hasContents() ) {
        osc_redirect(osc_href_link(FILENAME_CHECKOUT, null, 'SSL'));
      }

// check product type perform_order conditions
      foreach ( $osC_ShoppingCart->getProducts() as $product ) {
        $osC_Product = new osC_Product($product['id']);

        if ( !$osC_Product->isTypeActionAllowed('perform_order', 'require_shipping') ) {
          osc_redirect(osc_href_link(FILENAME_CHECKOUT, null, 'SSL'));
        }
      }

// process new address
      if ( isset($_GET['address']) && ($_GET['address'] == 'process') ) {
        $this->_processAddress();
      }

      $this->_page_title = __('shipping_method_heading');

      if ( $osC_Services->isStarted('breadcrumb') ) {
        $osC_Breadcrumb->add(__('breadcrumb_checkout_shipping'), osc_href_link(FILENAME_CHECKOUT, $this->_module, 'SSL'));
      }

// load shipping address page if no default address exists or shipping address page is requested
      if ( isset($_GET['address']) || !$osC_ShoppingCart->hasShippingAddress() ) {
        $this->_page_title = __('shipping_address_heading');
        $this->_page_contents = 'shipping_address.php';

        $this->addJavascriptFilename('templates/' . $this->getCode() . '/javascript/checkout_shipping_address.js');
        $this->addJavascriptPhpFilename('includes/form_check.js.php');

        if ( !$osC_Customer->isLoggedOn() ) {
          $osC_oiAddress = new osC_ObjectInfo($osC_ShoppingCart->getShippingAddress());
        }
      } else {
        $this->addJavascriptFilename('templates/' . $this->getCode() . '/javascript/checkout_shipping.js');

// load all enabled shipping modules
        $osC_Shipping = new osC_Shipping();
      }

      if ($_GET[$this->_module] == 'process') {
        $this->_process();
      }
    }

    public function &getListing() {
      global $osC_Database, $osC_Customer;

      $Qaddresses = $osC_Database->query('select ab.address_book_id, ab.entry_firstname as firstname, ab.entry_lastname as lastname, ab.entry_company as company, ab.entry_street_address as street_address, ab.entry_suburb as suburb, ab.entry_city as city, ab.entry_postcode as postcode, ab.entry_state as state, ab.entry_zone_id as zone_id, ab.entry_country_id as country_id, z.zone_code as zone_code, c.countries_name as country_title from :table_address_book ab left join :table_zones z on (ab.entry_zone_id = z.zone_id), :table_countries c where ab.customers_id = :customers_id and ab.entry_country_id = c.countries_id');
      $Qaddresses->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
      $Qaddresses->bindTable(':table_zones', TABLE_ZONES);
      $Qaddresses->bindTable(':table_countries', TABLE_COUNTRIES);
      $Qaddresses->bindInt(':customers_id', $osC_Customer->getID());
      $Qaddresses->execute();

      return $Qaddresses;
    }

    protected function _process() {
      global $osC_ShoppingCart, $osC_Shipping;

      if (!empty($_POST['comments'])) {
        $_SESSION['comments'] = osc_sanitize_string($_POST['comments']);
      }

      if ($osC_Shipping->hasQuotes()) {
        if (isset($_POST['shipping_mod_sel']) && strpos($_POST['shipping_mod_sel'], '_')) {
          list($module, $method) = explode('_', $_POST['shipping_mod_sel']);
          $module = 'osC_Shipping_' . $module;

          if (is_object($GLOBALS[$module]) && $GLOBALS[$module]->isEnabled()) {
            $quote = $osC_Shipping->getQuote($_POST['shipping_mod_sel']);

            if (isset($quote['error'])) {
              $osC_ShoppingCart->resetShippingMethod();
            } else {
              $osC_ShoppingCart->setShippingMethod($quote);

              osc_redirect(osc_href_link(FILENAME_CHECKOUT, 'confirmation', 'SSL'));
            }
          } else {
            $osC_ShoppingCart->resetShippingMethod();
          }
        }
      } else {
        $osC_ShoppingCart->resetShippingMethod();

        osc_redirect(osc_href_link(FILENAME_CHECKOUT, 'confirmation', 'SSL'));
      }
    }

    function _processAddress() {
      global $osC_Database, $osC_ShoppingCart, $osC_Customer, $osC_MessageStack;

// process a new shipping address
      if ( !empty($_POST['firstname']) && !empty($_POST['lastname']) && !empty($_POST['street_address']) ) {
        $address_array = array('id' => 0,
                               'zone_id' => 0);

        $error = false;

        if ( ACCOUNT_GENDER > -1 ) {
          if ( isset($_POST['gender']) && ((ACCOUNT_GENDER == 0) || in_array($_POST['gender'], array('m', 'f'))) ) {
            if ( in_array($_POST['gender'], array('m', 'f')) ) {
              $address_array['gender'] = $_POST['gender'];
            }
          } else {
            $osC_MessageStack->add('checkout_address', __('field_customer_gender_error'));

            $error = true;
          }
        }

        if ( isset($_POST['firstname']) && (strlen(trim($_POST['firstname'])) >= ACCOUNT_FIRST_NAME) ) {
          $address_array['firstname'] = trim($_POST['firstname']);
        } else {
          $osC_MessageStack->add('checkout_address', sprintf(__('field_customer_first_name_error'), ACCOUNT_FIRST_NAME));

          $error = true;
        }

        if ( isset($_POST['lastname']) && (strlen(trim($_POST['lastname'])) >= ACCOUNT_LAST_NAME) ) {
          $address_array['lastname'] = trim($_POST['lastname']);
        } else {
          $osC_MessageStack->add('checkout_address', sprintf(__('field_customer_last_name_error'), ACCOUNT_LAST_NAME));

          $error = true;
        }

        if ( ACCOUNT_COMPANY > -1 ) {
          if ( isset($_POST['company']) && ((ACCOUNT_COMPANY == 0) || (strlen(trim($_POST['company'])) >= ACCOUNT_COMPANY)) ) {
            $address_array['company'] = trim($_POST['company']);
          } else {
            $osC_MessageStack->add('checkout_address', sprintf(__('field_customer_company_error'), ACCOUNT_COMPANY));

            $error = true;
          }
        }

        if ( isset($_POST['street_address']) && (strlen(trim($_POST['street_address'])) >= ACCOUNT_STREET_ADDRESS) ) {
          $address_array['street_address'] = trim($_POST['street_address']);
        } else {
          $osC_MessageStack->add('checkout_address', sprintf(__('field_customer_street_address_error'), ACCOUNT_STREET_ADDRESS));

          $error = true;
        }

        if ( ACCOUNT_SUBURB > -1 ) {
          if ( isset($_POST['suburb']) && ((ACCOUNT_SUBURB == 0) || (strlen(trim($_POST['suburb'])) >= ACCOUNT_SUBURB)) ) {
            $address_array['suburb'] = trim($_POST['suburb']);
          } else {
            $osC_MessageStack->add('checkout_address', sprintf(__('field_customer_suburb_error'), ACCOUNT_SUBURB));

            $error = true;
          }
        }

        if ( ACCOUNT_POST_CODE > -1 ) {
          if ( isset($_POST['postcode']) && ((ACCOUNT_POST_CODE == 0) || (strlen(trim($_POST['postcode'])) >= ACCOUNT_POST_CODE)) ) {
            $address_array['postcode'] = trim($_POST['postcode']);
          } else {
            $osC_MessageStack->add('checkout_address', sprintf(__('field_customer_post_code_error'), ACCOUNT_POST_CODE));

            $error = true;
          }
        }

        if ( isset($_POST['city']) && (strlen(trim($_POST['city'])) >= ACCOUNT_CITY) ) {
          $address_array['city'] = trim($_POST['city']);
        } else {
          $osC_MessageStack->add('checkout_address', sprintf(__('field_customer_city_error'), ACCOUNT_CITY));

          $error = true;
        }

        if ( ACCOUNT_STATE > -1 ) {
          $Qcheck = $osC_Database->query('select zone_id from :table_zones where zone_country_id = :zone_country_id limit 1');
          $Qcheck->bindTable(':table_zones', TABLE_ZONES);
          $Qcheck->bindInt(':zone_country_id', $_POST['country']);
          $Qcheck->execute();

          if ( $Qcheck->numberOfRows() ) {
            $Qzone = $osC_Database->query('select zone_id from :table_zones where zone_country_id = :zone_country_id and zone_code like :zone_code');
            $Qzone->bindTable(':table_zones', TABLE_ZONES);
            $Qzone->bindInt(':zone_country_id', $_POST['country']);
            $Qzone->bindValue(':zone_code', $_POST['state']);
            $Qzone->execute();

            if ( $Qzone->numberOfRows() === 1 ) {
              $address_array['zone_id'] = $Qzone->valueInt('zone_id');
            } else {
              $Qzone = $osC_Database->query('select zone_id from :table_zones where zone_country_id = :zone_country_id and zone_name like :zone_name');
              $Qzone->bindTable(':table_zones', TABLE_ZONES);
              $Qzone->bindInt(':zone_country_id', $_POST['country']);
              $Qzone->bindValue(':zone_name', $_POST['state'] . '%');
              $Qzone->execute();

              if ( $Qzone->numberOfRows() === 1 ) {
                $address_array['zone_id'] = $Qzone->valueInt('zone_id');
              } else {
                $osC_MessageStack->add('checkout_address', __('field_customer_state_select_pull_down_error'));

                $error = true;
              }
            }

            $Qzone->freeResult();
          } else {
            if ( isset($_POST['state']) && ((ACCOUNT_STATE == 0) || (strlen(trim($_POST['state'])) >= ACCOUNT_STATE)) ) {
              $address_array['state'] = trim($_POST['state']);
            } else {
              $osC_MessageStack->add('checkout_address', sprintf(__('field_customer_state_error'), ACCOUNT_STATE));

              $error = true;
            }
          }
        }

        if ( isset($_POST['country']) && is_numeric($_POST['country']) && ($_POST['country'] > 0) ) {
          $address_array['country_id'] = (int)$_POST['country'];
        } else {
          $osC_MessageStack->add('checkout_address', __('field_customer_country_error'));

          $error = true;
        }

        if ( ACCOUNT_TELEPHONE > -1 ) {
          if ( isset($_POST['telephone']) && ((ACCOUNT_TELEPHONE == 0) || (strlen(trim($_POST['telephone'])) >= ACCOUNT_TELEPHONE)) ) {
            $address_array['telephone'] = trim($_POST['telephone']);
          } else {
            $osC_MessageStack->add('checkout_address', sprintf(__('field_customer_telephone_number_error'), ACCOUNT_TELEPHONE));

            $error = true;
          }
        }

        if ( ACCOUNT_FAX > -1 ) {
          if ( isset($_POST['fax']) && ((ACCOUNT_FAX == 0) || (strlen(trim($_POST['fax'])) >= ACCOUNT_FAX)) ) {
            $address_array['fax'] = trim($_POST['fax']);
          } else {
            $osC_MessageStack->add('checkout_address', sprintf(__('field_customer_fax_number_error'), ACCOUNT_FAX));

            $error = true;
          }
        }

        if ( $error === false ) {
          if ( $osC_Customer->isLoggedOn() ) {
            $Qab = $osC_Database->query('insert into :table_address_book (customers_id, entry_gender, entry_company, entry_firstname, entry_lastname, entry_street_address, entry_suburb, entry_postcode, entry_city, entry_state, entry_country_id, entry_zone_id, entry_telephone, entry_fax) values (:customers_id, :entry_gender, :entry_company, :entry_firstname, :entry_lastname, :entry_street_address, :entry_suburb, :entry_postcode, :entry_city, :entry_state, :entry_country_id, :entry_zone_id, :entry_telephone, :entry_fax)');
            $Qab->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
            $Qab->bindInt(':customers_id', $osC_Customer->getID());
            $Qab->bindValue(':entry_gender', (isset($address_array['gender']) ? $address_array['gender'] : ''));
            $Qab->bindValue(':entry_company', (isset($address_array['company']) ? $address_array['company'] : ''));
            $Qab->bindValue(':entry_firstname', $address_array['firstname']);
            $Qab->bindValue(':entry_lastname', $address_array['lastname']);
            $Qab->bindValue(':entry_street_address', $address_array['street_address']);
            $Qab->bindValue(':entry_suburb', (isset($address_array['suburb']) ? $address_array['suburb'] : ''));
            $Qab->bindValue(':entry_postcode', (isset($address_array['postcode']) ? $address_array['postcode'] : ''));
            $Qab->bindValue(':entry_city', $address_array['city']);
            $Qab->bindValue(':entry_state', ($address_array['zone_id'] > 0 ? '' : $address_array['state']));
            $Qab->bindInt(':entry_country_id', $address_array['country_id']);
            $Qab->bindInt(':entry_zone_id', $address_array['zone_id']);
            $Qab->bindValue(':entry_telephone', (isset($address_array['telephone']) ? $address_array['telephone'] : ''));
            $Qab->bindValue(':entry_fax', (isset($address_array['fax']) ? $address_array['fax'] : ''));
            $Qab->execute();

            if ( $Qab->affectedRows() === 1 ) {
              $address_book_id = $osC_Database->nextID();

              if ( !$osC_Customer->hasDefaultAddress() ) {
                $Qcustomer = $osC_Database->query('update :table_customers set customers_default_address_id = :customers_default_address_id where customers_id = :customers_id');
                $Qcustomer->bindTable(':table_customers', TABLE_CUSTOMERS);
                $Qcustomer->bindInt(':customers_default_address_id', $address_book_id);
                $Qcustomer->bindInt(':customers_id', $osC_Customer->getID());
                $Qcustomer->execute();

                $osC_Customer->setCountryID($address_array['country_id']);
                $osC_Customer->setZoneID($address_array['zone_id']);
                $osC_Customer->setDefaultAddressID($address_book_id);
              }

              $osC_ShoppingCart->setShippingAddress($address_book_id);

              osc_redirect(osc_href_link(FILENAME_CHECKOUT, 'confirmation', 'SSL'));
            } else {
              $osC_MessageStack->add('checkout_address', 'Error inserting into address book table.');
            }
          } else {
            $osC_Customer->setGender((isset($address_array['gender']) ? $address_array['gender'] : null));
            $osC_Customer->setFirstName($address_array['firstname']);
            $osC_Customer->setLastName($address_array['lastname']);

            $osC_Customer->setCountryID($address_array['country_id']);
            $osC_Customer->setZoneID($address_array['zone_id']);

            $osC_ShoppingCart->setShippingAddress($address_array);

            osc_redirect(osc_href_link(FILENAME_CHECKOUT, 'confirmation', 'SSL'));
          }
        }
// process the selected shipping destination
      } elseif ( isset($_POST['ab']) && is_numeric($_POST['ab']) ) {
        if ( osC_AddressBook::checkEntry($_POST['ab']) ) {
          $osC_ShoppingCart->setShippingAddress($_POST['ab']);

          osc_redirect(osc_href_link(FILENAME_CHECKOUT, 'shipping', 'SSL'));
        }
      }
    }
  }
?>
