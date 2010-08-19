<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Shop;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Site\Shop\AddressBook;
  use osCommerce\OM\Core\Site\Shop\Address;
  use osCommerce\OM\Core\DateTime;

  class Order {
// HPDL add getter methods for the following and set as protected
    public $info;
    public $totals;
    public $products;
    public $customer;
    public $delivery;
    public $content_type;

    protected $_id;

    public function __construct($id = null) {
      if ( is_numeric($id) ) {
        $this->_id = $id;
      }

      $this->info = array();
      $this->totals = array();
      $this->products = array();
      $this->customer = array();
      $this->delivery = array();

      if ( is_numeric($id) ) {
        $this->query($id);
      } else {
        $this->cart();
      }
    }

    function getStatusID($id) {
      $OSCOM_Database = Registry::get('Database');

      $Qorder = $OSCOM_Database->query('select orders_status from :table_orders where orders_id = :orders_id');
      $Qorder->bindInt(':orders_id', $id);
      $Qorder->execute();

      if ( $Qorder->numberOfRows() ) {
        return $Qorder->valueInt('orders_status');
      }

      return false;
    }

    function remove($id) {
      $OSCOM_Database = Registry::get('Database');

      $Qcheck = $OSCOM_Database->query('select orders_status from :table_orders where orders_id = :orders_id');
      $Qcheck->bindInt(':orders_id', $id);
      $Qcheck->execute();

      if ( $Qcheck->valueInt('orders_status') === 4 ) {
        $Qdel = $OSCOM_Database->query('delete from :table_orders where orders_id = :orders_id');
        $Qdel->bindInt(':orders_id', $id);
        $Qdel->execute();
      }

      if ( isset($_SESSION['prepOrderID']) ) {
        unset($_SESSION['prepOrderID']);
      }
    }

    public static function insert() {
      $OSCOM_ShoppingCart = Registry::get('ShoppingCart');
      $OSCOM_Customer = Registry::get('Customer');
      $OSCOM_Currencies = Registry::get('Currencies');
      $OSCOM_Database = Registry::get('Database');
      $OSCOM_Tax = Registry::get('Tax');

      if ( isset($_SESSION['prepOrderID']) ) {
        $_prep = explode('-', $_SESSION['prepOrderID']);

        if ( $_prep[0] == $OSCOM_ShoppingCart->getCartID() ) {
          return $_prep[1]; // order_id
        } else {
          if ( self::getStatusID($_prep[1]) === 4 ) {
            self::remove($_prep[1]);
          }
        }
      }

      if ( $OSCOM_Customer->isLoggedOn() ) {
        $customer_address = AddressBook::getEntry($OSCOM_Customer->getDefaultAddressID())->toArray();
      } else {
        $customer_address = array('entry_company' => $OSCOM_ShoppingCart->getShippingAddress('company'),
                                  'entry_street_address' => $OSCOM_ShoppingCart->getShippingAddress('street_address'),
                                  'entry_suburb' => $OSCOM_ShoppingCart->getShippingAddress('suburb'),
                                  'entry_city' => $OSCOM_ShoppingCart->getShippingAddress('city'),
                                  'entry_postcode' => $OSCOM_ShoppingCart->getShippingAddress('postcode'),
                                  'entry_state' => $OSCOM_ShoppingCart->getShippingAddress('state'),
                                  'entry_zone_id' => $OSCOM_ShoppingCart->getShippingAddress('zone_id'),
                                  'entry_country_id' => $OSCOM_ShoppingCart->getShippingAddress('country_id'),
                                  'entry_telephone' => $OSCOM_ShoppingCart->getShippingAddress('telephone'));
      }

      $Qorder = $OSCOM_Database->query('insert into :table_orders (customers_id, customers_name, customers_company, customers_street_address, customers_suburb, customers_city, customers_postcode, customers_state, customers_state_code, customers_country, customers_country_iso2, customers_country_iso3, customers_telephone, customers_email_address, customers_address_format, customers_ip_address, delivery_name, delivery_company, delivery_street_address, delivery_suburb, delivery_city, delivery_postcode, delivery_state, delivery_state_code, delivery_country, delivery_country_iso2, delivery_country_iso3, delivery_address_format, billing_name, billing_company, billing_street_address, billing_suburb, billing_city, billing_postcode, billing_state, billing_state_code, billing_country, billing_country_iso2, billing_country_iso3, billing_address_format, payment_method, payment_module, date_purchased, orders_status, currency, currency_value) values (:customers_id, :customers_name, :customers_company, :customers_street_address, :customers_suburb, :customers_city, :customers_postcode, :customers_state, :customers_state_code, :customers_country, :customers_country_iso2, :customers_country_iso3, :customers_telephone, :customers_email_address, :customers_address_format, :customers_ip_address, :delivery_name, :delivery_company, :delivery_street_address, :delivery_suburb, :delivery_city, :delivery_postcode, :delivery_state, :delivery_state_code, :delivery_country, :delivery_country_iso2, :delivery_country_iso3, :delivery_address_format, :billing_name, :billing_company, :billing_street_address, :billing_suburb, :billing_city, :billing_postcode, :billing_state, :billing_state_code, :billing_country, :billing_country_iso2, :billing_country_iso3, :billing_address_format, :payment_method, :payment_module, now(), :orders_status, :currency, :currency_value)');
      $Qorder->bindInt(':customers_id', $OSCOM_Customer->getID());
      $Qorder->bindValue(':customers_name', $OSCOM_Customer->getName());
      $Qorder->bindValue(':customers_company', $customer_address['entry_company']);
      $Qorder->bindValue(':customers_street_address', $customer_address['entry_street_address']);
      $Qorder->bindValue(':customers_suburb', $customer_address['entry_suburb']);
      $Qorder->bindValue(':customers_city', $customer_address['entry_city']);
      $Qorder->bindValue(':customers_postcode', $customer_address['entry_postcode']);
      $Qorder->bindValue(':customers_state', $customer_address['entry_state']);
      $Qorder->bindValue(':customers_state_code', Address::getZoneCode($customer_address['entry_zone_id']));
      $Qorder->bindValue(':customers_country', Address::getCountryName($customer_address['entry_country_id']));
      $Qorder->bindValue(':customers_country_iso2', Address::getCountryIsoCode2($customer_address['entry_country_id']));
      $Qorder->bindValue(':customers_country_iso3', Address::getCountryIsoCode3($customer_address['entry_country_id']));
      $Qorder->bindValue(':customers_telephone', $customer_address['entry_telephone']);
      $Qorder->bindValue(':customers_email_address', $OSCOM_Customer->getEmailAddress());
      $Qorder->bindValue(':customers_address_format', Address::getFormat($customer_address['entry_country_id']));
      $Qorder->bindValue(':customers_ip_address', osc_get_ip_address());
      $Qorder->bindValue(':delivery_name', $OSCOM_ShoppingCart->getShippingAddress('firstname') . ' ' . $OSCOM_ShoppingCart->getShippingAddress('lastname'));
      $Qorder->bindValue(':delivery_company', $OSCOM_ShoppingCart->getShippingAddress('company'));
      $Qorder->bindValue(':delivery_street_address', $OSCOM_ShoppingCart->getShippingAddress('street_address'));
      $Qorder->bindValue(':delivery_suburb', $OSCOM_ShoppingCart->getShippingAddress('suburb'));
      $Qorder->bindValue(':delivery_city', $OSCOM_ShoppingCart->getShippingAddress('city'));
      $Qorder->bindValue(':delivery_postcode', $OSCOM_ShoppingCart->getShippingAddress('postcode'));
      $Qorder->bindValue(':delivery_state', $OSCOM_ShoppingCart->getShippingAddress('state'));
      $Qorder->bindValue(':delivery_state_code', $OSCOM_ShoppingCart->getShippingAddress('zone_code'));
      $Qorder->bindValue(':delivery_country', $OSCOM_ShoppingCart->getShippingAddress('country_title'));
      $Qorder->bindValue(':delivery_country_iso2', $OSCOM_ShoppingCart->getShippingAddress('country_iso_code_2'));
      $Qorder->bindValue(':delivery_country_iso3', $OSCOM_ShoppingCart->getShippingAddress('country_iso_code_3'));
      $Qorder->bindValue(':delivery_address_format', $OSCOM_ShoppingCart->getShippingAddress('format'));
      $Qorder->bindValue(':billing_name', $OSCOM_ShoppingCart->getBillingAddress('firstname') . ' ' . $OSCOM_ShoppingCart->getBillingAddress('lastname'));
      $Qorder->bindValue(':billing_company', $OSCOM_ShoppingCart->getBillingAddress('company'));
      $Qorder->bindValue(':billing_street_address', $OSCOM_ShoppingCart->getBillingAddress('street_address'));
      $Qorder->bindValue(':billing_suburb', $OSCOM_ShoppingCart->getBillingAddress('suburb'));
      $Qorder->bindValue(':billing_city', $OSCOM_ShoppingCart->getBillingAddress('city'));
      $Qorder->bindValue(':billing_postcode', $OSCOM_ShoppingCart->getBillingAddress('postcode'));
      $Qorder->bindValue(':billing_state', $OSCOM_ShoppingCart->getBillingAddress('state'));
      $Qorder->bindValue(':billing_state_code', $OSCOM_ShoppingCart->getBillingAddress('zone_code'));
      $Qorder->bindValue(':billing_country', $OSCOM_ShoppingCart->getBillingAddress('country_title'));
      $Qorder->bindValue(':billing_country_iso2', $OSCOM_ShoppingCart->getBillingAddress('country_iso_code_2'));
      $Qorder->bindValue(':billing_country_iso3', $OSCOM_ShoppingCart->getBillingAddress('country_iso_code_3'));
      $Qorder->bindValue(':billing_address_format', $OSCOM_ShoppingCart->getBillingAddress('format'));
      $Qorder->bindValue(':payment_method', $OSCOM_ShoppingCart->getBillingMethod('title'));
// HPDL verify payment module class
      $Qorder->bindValue(':payment_module', $OSCOM_ShoppingCart->getBillingMethod('id'));
      $Qorder->bindInt(':orders_status', 4);
// HPDL move currencies to the products level
      $Qorder->bindValue(':currency', $OSCOM_Currencies->getCode());
      $Qorder->bindValue(':currency_value', $OSCOM_Currencies->value($OSCOM_Currencies->getCode()));
      $Qorder->execute();

      $insert_id = $OSCOM_Database->nextID();

      foreach ( $OSCOM_ShoppingCart->getOrderTotals() as $module ) {
        $Qtotals = $OSCOM_Database->query('insert into :table_orders_total (orders_id, title, text, value, class, sort_order) values (:orders_id, :title, :text, :value, :class, :sort_order)');
        $Qtotals->bindInt(':orders_id', $insert_id);
        $Qtotals->bindValue(':title', $module['title']);
        $Qtotals->bindValue(':text', $module['text']);
        $Qtotals->bindValue(':value', $module['value']);
        $Qtotals->bindValue(':class', $module['code']);
        $Qtotals->bindInt(':sort_order', $module['sort_order']);
        $Qtotals->execute();
      }

      $Qstatus = $OSCOM_Database->query('insert into :table_orders_status_history (orders_id, orders_status_id, date_added, customer_notified, comments) values (:orders_id, :orders_status_id, now(), :customer_notified, :comments)');
      $Qstatus->bindInt(':orders_id', $insert_id);
      $Qstatus->bindInt(':orders_status_id', 4);
      $Qstatus->bindInt(':customer_notified', '0');
      $Qstatus->bindValue(':comments', (isset($_SESSION['comments']) ? $_SESSION['comments'] : ''));
      $Qstatus->execute();

      foreach ( $OSCOM_ShoppingCart->getProducts() as $products ) {
        $Qproducts = $OSCOM_Database->query('insert into :table_orders_products (orders_id, products_id, products_model, products_name, products_price, products_tax, products_quantity) values (:orders_id, :products_id, :products_model, :products_name, :products_price, :products_tax, :products_quantity)');
        $Qproducts->bindInt(':orders_id', $insert_id);
        $Qproducts->bindInt(':products_id', osc_get_product_id($products['id']));
        $Qproducts->bindValue(':products_model', $products['model']);
        $Qproducts->bindValue(':products_name', $products['name']);
        $Qproducts->bindValue(':products_price', $products['price']);
        $Qproducts->bindValue(':products_tax', $OSCOM_Tax->getTaxRate($products['tax_class_id']));
        $Qproducts->bindInt(':products_quantity', $products['quantity']);
        $Qproducts->execute();

        $order_products_id = $OSCOM_Database->nextID();

        if ( $OSCOM_ShoppingCart->isVariant($products['item_id']) ) {
          foreach ( $OSCOM_ShoppingCart->getVariant($products['item_id']) as $variant ) {
/* HPDL
            if (DOWNLOAD_ENABLED == '1') {
              $Qattributes = $osC_Database->query('select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix, pad.products_attributes_maxdays, pad.products_attributes_maxcount, pad.products_attributes_filename from :table_products_options popt, :table_products_options_values poval, :table_products_attributes pa left join :table_products_attributes_download pad on (pa.products_attributes_id = pad.products_attributes_id) where pa.products_id = :products_id and pa.options_id = :options_id and pa.options_id = popt.products_options_id and pa.options_values_id = :options_values_id and pa.options_values_id = poval.products_options_values_id and popt.language_id = :popt_language_id and poval.language_id = :poval_language_id');
              $Qattributes->bindTable(':table_products_options', TABLE_PRODUCTS_OPTIONS);
              $Qattributes->bindTable(':table_products_options_values', TABLE_PRODUCTS_OPTIONS_VALUES);
              $Qattributes->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
              $Qattributes->bindTable(':table_products_attributes_download', TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD);
              $Qattributes->bindInt(':products_id', $products['id']);
              $Qattributes->bindInt(':options_id', $attributes['options_id']);
              $Qattributes->bindInt(':options_values_id', $attributes['options_values_id']);
              $Qattributes->bindInt(':popt_language_id', $osC_Language->getID());
              $Qattributes->bindInt(':poval_language_id', $osC_Language->getID());
              $Qattributes->execute();
            }
*/

            $Qvariant = $OSCOM_Database->query('insert into :table_orders_products_variants (orders_id, orders_products_id, group_title, value_title) values (:orders_id, :orders_products_id, :group_title, :value_title)');
            $Qvariant->bindInt(':orders_id', $insert_id);
            $Qvariant->bindInt(':orders_products_id', $order_products_id);
            $Qvariant->bindValue(':group_title', $variant['group_title']);
            $Qvariant->bindValue(':value_title', $variant['value_title']);
            $Qvariant->execute();

/*HPDL
            if ((DOWNLOAD_ENABLED == '1') && (strlen($Qattributes->value('products_attributes_filename')) > 0)) {
              $Qopd = $osC_Database->query('insert into :table_orders_products_download (orders_id, orders_products_id, orders_products_filename, download_maxdays, download_count) values (:orders_id, :orders_products_id, :orders_products_filename, :download_maxdays, :download_count)');
              $Qopd->bindTable(':table_orders_products_download', TABLE_ORDERS_PRODUCTS_DOWNLOAD);
              $Qopd->bindInt(':orders_id', $insert_id);
              $Qopd->bindInt(':orders_products_id', $order_products_id);
              $Qopd->bindValue(':orders_products_filename', $Qattributes->value('products_attributes_filename'));
              $Qopd->bindValue(':download_maxdays', $Qattributes->value('products_attributes_maxdays'));
              $Qopd->bindValue(':download_count', $Qattributes->value('products_attributes_maxcount'));
              $Qopd->execute();
            }
*/
          }
        }
      }

      $_SESSION['prepOrderID'] = $OSCOM_ShoppingCart->getCartID() . '-' . $insert_id;

      return $insert_id;
    }

    public static function process($order_id, $status_id = null) {
      $OSCOM_Database = Registry::get('Database');

      if ( !is_numeric($status_id) ) {
        $status_id = DEFAULT_ORDERS_STATUS_ID;
      }

      $Qstatus = $OSCOM_Database->query('insert into :table_orders_status_history (orders_id, orders_status_id, date_added, customer_notified, comments) values (:orders_id, :orders_status_id, now(), :customer_notified, :comments)');
      $Qstatus->bindInt(':orders_id', $order_id);
      $Qstatus->bindInt(':orders_status_id', $status_id);
      $Qstatus->bindInt(':customer_notified', (SEND_EMAILS == '1') ? '1' : '0');
      $Qstatus->bindValue(':comments', '');
      $Qstatus->execute();

      $Qupdate = $OSCOM_Database->query('update :table_orders set orders_status = :orders_status where orders_id = :orders_id');
      $Qupdate->bindInt(':orders_status', $status_id);
      $Qupdate->bindInt(':orders_id', $order_id);
      $Qupdate->execute();

      $Qproducts = $OSCOM_Database->query('select products_id, products_quantity from :table_orders_products where orders_id = :orders_id');
      $Qproducts->bindInt(':orders_id', $order_id);
      $Qproducts->execute();

      while ( $Qproducts->next() ) {
        if (STOCK_LIMITED == '1') {

/********** HPDL ; still uses logic from the shopping cart class
          if (DOWNLOAD_ENABLED == '1') {
            $Qstock = $osC_Database->query('select products_quantity, pad.products_attributes_filename from :table_products p left join :table_products_attributes pa on (p.products_id = pa.products_id) left join :table_products_attributes_download pad on (pa.products_attributes_id = pad.products_attributes_id) where p.products_id = :products_id');
            $Qstock->bindTable(':table_products', TABLE_PRODUCTS);
            $Qstock->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
            $Qstock->bindTable(':table_products_attributes_download', TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD);
            $Qstock->bindInt(':products_id', $Qproducts->valueInt('products_id'));

// Will work with only one option for downloadable products otherwise, we have to build the query dynamically with a loop
            if ($osC_ShoppingCart->hasAttributes($products['id'])) {
              $products_attributes = $osC_ShoppingCart->getAttributes($products['id']);
              $products_attributes = array_shift($products_attributes);

              $Qstock->appendQuery('and pa.options_id = :options_id and pa.options_values_id = :options_values_id');
              $Qstock->bindInt(':options_id', $products_attributes['options_id']);
              $Qstock->bindInt(':options_values_id', $products_attributes['options_values_id']);
            }
          } else {
************/
            $Qstock = $OSCOM_Database->query('select products_quantity from :table_products where products_id = :products_id');
            $Qstock->bindInt(':products_id', $Qproducts->valueInt('products_id'));
// HPDL          }

          $Qstock->execute();

          if ( $Qstock->numberOfRows() > 0 ) {
            $stock_left = $Qstock->valueInt('products_quantity');

// do not decrement quantities if products_attributes_filename exists
// HPDL            if ((DOWNLOAD_ENABLED == '-1') || ((DOWNLOAD_ENABLED == '1') && (strlen($Qstock->value('products_attributes_filename')) < 1))) {
              $stock_left = $stock_left - $Qproducts->valueInt('products_quantity');

              $Qupdate = $OSCOM_Database->query('update :table_products set products_quantity = :products_quantity where products_id = :products_id');
              $Qupdate->bindInt(':products_quantity', $stock_left);
              $Qupdate->bindInt(':products_id', $Qproducts->valueInt('products_id'));
              $Qupdate->execute();
// HPDL            }

            if ( (STOCK_ALLOW_CHECKOUT == '-1') && ($stock_left < 1) ) {
              $Qupdate = $OSCOM_Database->query('update :table_products set products_status = 0 where products_id = :products_id');
              $Qupdate->bindInt(':products_id', $Qproducts->valueInt('products_id'));
              $Qupdate->execute();
            }
          }
        }

// Update products_ordered (for bestsellers list)
        $Qupdate = $OSCOM_Database->query('update :table_products set products_ordered = products_ordered + :products_ordered where products_id = :products_id');
        $Qupdate->bindInt(':products_ordered', $Qproducts->valueInt('products_quantity'));
        $Qupdate->bindInt(':products_id', $Qproducts->valueInt('products_id'));
        $Qupdate->execute();
      }

      self::sendEmail($order_id);

      unset($_SESSION['prepOrderID']);
    }

    public static function sendEmail($id) {
      $OSCOM_Database = Registry::get('Database');
      $OSCOM_Currencies = Registry::get('Currencies');
      $OSCOM_Language = Registry::get('Language');

      $Qorder = $OSCOM_Database->query('select * from :table_orders where orders_id = :orders_id limit 1');
      $Qorder->bindInt(':orders_id', $id);
      $Qorder->execute();

      if ( $Qorder->numberOfRows() === 1 ) {
        $email_order = STORE_NAME . "\n" .
                       OSCOM::getDef('email_order_separator') . "\n" .
                       sprintf(OSCOM::getDef('email_order_order_number'), $id) . "\n" .
                       sprintf(OSCOM::getDef('email_order_invoice_url'), OSCOM::getLink(null, null, 'Orders=' . $id, 'SSL', false, true, true)) . "\n" .
                       sprintf(OSCOM::getDef('email_order_date_ordered'), DateTime::getLong()) . "\n\n" .
                       OSCOM::getDef('email_order_products') . "\n" .
                       OSCOM::getDef('email_order_separator') . "\n";

        $Qproducts = $OSCOM_Database->query('select orders_products_id, products_model, products_name, products_price, products_tax, products_quantity from :table_orders_products where orders_id = :orders_id order by orders_products_id');
        $Qproducts->bindInt(':orders_id', $id);
        $Qproducts->execute();

        while ( $Qproducts->next() ) {
          $email_order .= $Qproducts->valueInt('products_quantity') . ' x ' . $Qproducts->value('products_name') . ' (' . $Qproducts->value('products_model') . ') = ' . $OSCOM_Currencies->displayPriceWithTaxRate($Qproducts->value('products_price'), $Qproducts->value('products_tax'), $Qproducts->valueInt('products_quantity'), false, $Qorder->value('currency'), $Qorder->value('currency_value')) . "\n";

          $Qvariants = $OSCOM_Database->query('select group_title, value_title from :table_orders_products_variants where orders_id = :orders_id and orders_products_id = :orders_products_id order by id');
          $Qvariants->bindInt(':orders_id', $id);
          $Qvariants->bindInt(':orders_products_id', $Qproducts->valueInt('orders_products_id'));
          $Qvariants->execute();

          while ( $Qvariants->next() ) {
            $email_order .= "\t" . $Qvariants->value('group_title') . ': ' . $Qvariants->value('value_title') . "\n";
          }
        }

        $email_order .= OSCOM::getDef('email_order_separator') . "\n";

        $Qtotals = $OSCOM_Database->query('select title, text from :table_orders_total where orders_id = :orders_id order by sort_order');
        $Qtotals->bindInt(':orders_id', $id);
        $Qtotals->execute();

        while ( $Qtotals->next() ) {
          $email_order .= strip_tags($Qtotals->value('title') . ' ' . $Qtotals->value('text')) . "\n";
        }

        if ( !osc_empty($Qorder->value('delivery_name')) && !osc_empty($Qorder->value('delivery_street_address')) ) {
          $address = array('name' => $Qorder->value('delivery_name'),
                           'company' => $Qorder->value('delivery_company'),
                           'street_address' => $Qorder->value('delivery_street_address'),
                           'suburb' => $Qorder->value('delivery_suburb'),
                           'city' => $Qorder->value('delivery_city'),
                           'state' => $Qorder->value('delivery_state'),
                           'zone_code' => $Qorder->value('delivery_state_code'),
                           'country_title' => $Qorder->value('delivery_country'),
                           'country_iso2' => $Qorder->value('delivery_country_iso2'),
                           'country_iso3' => $Qorder->value('delivery_country_iso3'),
                           'postcode' => $Qorder->value('delivery_postcode'),
                           'format' => $Qorder->value('delivery_address_format'));

          $email_order .= "\n" . OSCOM::getDef('email_order_delivery_address') . "\n" .
                          OSCOM::getDef('email_order_separator') . "\n" .
                          Address::format($address) . "\n";
        }

        $address = array('name' => $Qorder->value('billing_name'),
                         'company' => $Qorder->value('billing_company'),
                         'street_address' => $Qorder->value('billing_street_address'),
                         'suburb' => $Qorder->value('billing_suburb'),
                         'city' => $Qorder->value('billing_city'),
                         'state' => $Qorder->value('billing_state'),
                         'zone_code' => $Qorder->value('billing_state_code'),
                         'country_title' => $Qorder->value('billing_country'),
                         'country_iso2' => $Qorder->value('billing_country_iso2'),
                         'country_iso3' => $Qorder->value('billing_country_iso3'),
                         'postcode' => $Qorder->value('billing_postcode'),
                         'format' => $Qorder->value('billing_address_format'));

        $email_order .= "\n" . OSCOM::getDef('email_order_billing_address') . "\n" .
                        OSCOM::getDef('email_order_separator') . "\n" .
                        Address::format($address) . "\n\n";

        $Qstatus = $OSCOM_Database->query('select orders_status_name from :table_orders_status where orders_status_id = :orders_status_id and language_id = :language_id');
        $Qstatus->bindInt(':orders_status_id', $Qorder->valueInt('orders_status'));
        $Qstatus->bindInt(':language_id', $OSCOM_Language->getID());
        $Qstatus->execute();

        $email_order .= sprintf(OSCOM::getDef('email_order_status'), $Qstatus->value('orders_status_name')) . "\n" .
                        OSCOM::getDef('email_order_separator') . "\n";

        $Qstatuses = $OSCOM_Database->query('select date_added, comments from :table_orders_status_history where orders_id = :orders_id and comments != "" order by orders_status_history_id');
        $Qstatuses->bindInt(':orders_id', $id);
        $Qstatuses->execute();

        while ( $Qstatuses->next() ) {
          $email_order .= DateTime::getLong($Qstatuses->value('date_added')) . "\n\t" . wordwrap(str_replace("\n", "\n\t", $Qstatuses->value('comments')), 60, "\n\t", 1) . "\n\n";
        }

// HPDL
//        if (is_object($GLOBALS[$payment])) {
//          $email_order .= OSCOM::getDef('email_order_payment_method') . "\n" .
//                          OSCOM::getDef('email_order_separator') . "\n";

//          $email_order .= $osC_ShoppingCart->getBillingMethod('title') . "\n\n";
//          if (isset($GLOBALS[$payment]->email_footer)) {
//            $email_order .= $GLOBALS[$payment]->email_footer . "\n\n";
//          }
//        }

        osc_email($Qorder->value('customers_name'), $Qorder->value('customers_email_address'), OSCOM::getDef('email_order_subject'), $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);

// send emails to other people
        if ( SEND_EXTRA_ORDER_EMAILS_TO != '' ) {
          osc_email('', SEND_EXTRA_ORDER_EMAILS_TO, OSCOM::getDef('email_order_subject'), $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
        }
      }
    }

    public static function getListing($limit = null, $page_keyword = 'page') {
      $OSCOM_Database = Registry::get('Database');
      $OSCOM_Customer = Registry::get('Customer');
      $OSCOM_Language = Registry::get('Language');

      $Qorders = $OSCOM_Database->query('select o.orders_id, o.date_purchased, o.delivery_name, o.delivery_country, o.billing_name, o.billing_country, ot.text as order_total, s.orders_status_name from :table_orders o, :table_orders_total ot, :table_orders_status s where o.customers_id = :customers_id and o.orders_id = ot.orders_id and ot.class = "total" and o.orders_status = s.orders_status_id and s.language_id = :language_id order by orders_id desc');
      $Qorders->bindInt(':customers_id', $OSCOM_Customer->getID());
      $Qorders->bindInt(':language_id', $OSCOM_Language->getID());

      if ( is_numeric($limit) ) {
        $Qorders->setBatchLimit(isset($_GET[$page_keyword]) && is_numeric($_GET[$page_keyword]) ? $_GET[$page_keyword] : 1, $limit);
      }

      $Qorders->execute();

      return $Qorders;
    }

    function getStatusListing($id = null) {
      $OSCOM_Database = Registry::get('Database');
      $OSCOM_Language = Registry::get('Language');

      if ( ($id === null) && isset($this) ) {
        $id = $this->_id;
      }

      $Qstatus = $OSCOM_Database->query('select os.orders_status_name, osh.date_added, osh.comments from :table_orders_status os, :table_orders_status_history osh where osh.orders_id = :orders_id and osh.orders_status_id = os.orders_status_id and os.language_id = :language_id order by osh.date_added');
      $Qstatus->bindInt(':orders_id', $id);
      $Qstatus->bindInt(':language_id', $OSCOM_Language->getID());

      return $Qstatus;
    }

    function getCustomerID($id = null) {
      $OSCOM_Database = Registry::get('Database');

      if ( ($id === null) && isset($this) ) {
        $id = $this->_id;
      }

      $Qcustomer = $OSCOM_Database->query('select customers_id from :table_orders where orders_id = :orders_id');
      $Qcustomer->bindInt(':orders_id', $id);
      $Qcustomer->execute();

      return $Qcustomer->valueInt('customers_id');
    }

    public static function numberOfEntries() {
      $OSCOM_Customer = Registry::get('Customer');
      $OSCOM_Database = Registry::get('Database');

      static $total_entries;

      if ( is_numeric($total_entries) === false ) {
        if ( $OSCOM_Customer->isLoggedOn() ) {
          $Qorders = $OSCOM_Database->query('select count(*) as total from :table_orders where customers_id = :customers_id');
          $Qorders->bindInt(':customers_id', $OSCOM_Customer->getID());
          $Qorders->execute();

          $total_entries = $Qorders->valueInt('total');
        } else {
          $total_entries = 0;
        }
      }

      return $total_entries;
    }

    function numberOfProducts($id = null) {
      $OSCOM_Database = Registry::get('Database');

      if ( ($id === null) && isset($this) ) {
        $id = $this->_id;
      }

      $Qproducts = $OSCOM_Database->query('select count(*) as total from :table_orders_products where orders_id = :orders_id');
      $Qproducts->bindInt(':orders_id', $id);
      $Qproducts->execute();

      return $Qproducts->valueInt('total');
    }

    function exists($id, $customer_id = null) {
      $OSCOM_Database = Registry::get('Database');

      $Qorder = $OSCOM_Database->query('select orders_id from :table_orders where orders_id = :orders_id');

      if ( isset($customer_id) && is_numeric($customer_id) ) {
        $Qorder->appendQuery('and customers_id = :customers_id');
        $Qorder->bindInt(':customers_id', $customer_id);
      }

      $Qorder->appendQuery('limit 1');
      $Qorder->bindInt(':orders_id', $id);
      $Qorder->execute();

      return ($Qorder->numberOfRows() === 1);
    }

    function query($order_id) {
      $OSCOM_Database = Registry::get('Database');
      $OSCOM_Language = Registry::get('Language');

      $Qorder = $OSCOM_Database->query('select * from :table_orders where orders_id = :orders_id');
      $Qorder->bindInt(':orders_id', $order_id);
      $Qorder->execute();

      $Qtotals = $OSCOM_Database->query('select title, text, class from :table_orders_total where orders_id = :orders_id order by sort_order');
      $Qtotals->bindInt(':orders_id', $order_id);
      $Qtotals->execute();

      $shipping_method_string = '';
      $order_total_string = '';

      while ( $Qtotals->next() ) {
        $this->totals[] = array('title' => $Qtotals->value('title'),
                                'text' => $Qtotals->value('text'));

        if ( $Qtotals->value('class') == 'shipping' ) {
          $shipping_method_string = strip_tags($Qtotals->value('title'));

          if ( substr($shipping_method_string, -1) == ':' ) {
            $shipping_method_string = substr($Qtotals->value('title'), 0, -1);
          }
        }

        if ( $Qtotals->value('class') == 'total' ) {
          $order_total_string = strip_tags($Qtotals->value('text'));
        }
      }

      $Qstatus = $OSCOM_Database->query('select orders_status_name from :table_orders_status where orders_status_id = :orders_status_id and language_id = :language_id');
      $Qstatus->bindInt(':orders_status_id', $Qorder->valueInt('orders_status'));
      $Qstatus->bindInt(':language_id', $OSCOM_Language->getID());
      $Qstatus->execute();

      $this->info = array('currency' => $Qorder->value('currency'),
                          'currency_value' => $Qorder->value('currency_value'),
                          'payment_method' => $Qorder->value('payment_method'),
                          'date_purchased' => $Qorder->value('date_purchased'),
                          'orders_status' => $Qstatus->value('orders_status_name'),
                          'last_modified' => $Qorder->value('last_modified'),
                          'total' => $order_total_string,
                          'shipping_method' => $shipping_method_string);

      $this->customer = array('id' => $Qorder->valueInt('customers_id'),
                              'name' => $Qorder->valueProtected('customers_name'),
                              'company' => $Qorder->valueProtected('customers_company'),
                              'street_address' => $Qorder->valueProtected('customers_street_address'),
                              'suburb' => $Qorder->valueProtected('customers_suburb'),
                              'city' => $Qorder->valueProtected('customers_city'),
                              'postcode' => $Qorder->valueProtected('customers_postcode'),
                              'state' => $Qorder->valueProtected('customers_state'),
                              'zone_code' => $Qorder->value('customers_state_code'),
                              'country_title' => $Qorder->valueProtected('customers_country'),
                              'country_iso2' => $Qorder->value('customers_country_iso2'),
                              'country_iso3' => $Qorder->value('customers_country_iso3'),
                              'format' => $Qorder->value('customers_address_format'),
                              'telephone' => $Qorder->valueProtected('customers_telephone'),
                              'email_address' => $Qorder->valueProtected('customers_email_address'));

      $this->delivery = array('name' => $Qorder->valueProtected('delivery_name'),
                              'company' => $Qorder->valueProtected('delivery_company'),
                              'street_address' => $Qorder->valueProtected('delivery_street_address'),
                              'suburb' => $Qorder->valueProtected('delivery_suburb'),
                              'city' => $Qorder->valueProtected('delivery_city'),
                              'postcode' => $Qorder->valueProtected('delivery_postcode'),
                              'state' => $Qorder->valueProtected('delivery_state'),
                              'zone_code' => $Qorder->value('delivery_state_code'),
                              'country_title' => $Qorder->valueProtected('delivery_country'),
                              'country_iso2' => $Qorder->value('delivery_country_iso2'),
                              'country_iso3' => $Qorder->value('delivery_country_iso3'),
                              'format' => $Qorder->value('delivery_address_format'));

      if ( empty($this->delivery['name']) && empty($this->delivery['street_address']) ) {
        $this->delivery = false;
      }

      $this->billing = array('name' => $Qorder->valueProtected('billing_name'),
                             'company' => $Qorder->valueProtected('billing_company'),
                             'street_address' => $Qorder->valueProtected('billing_street_address'),
                             'suburb' => $Qorder->valueProtected('billing_suburb'),
                             'city' => $Qorder->valueProtected('billing_city'),
                             'postcode' => $Qorder->valueProtected('billing_postcode'),
                             'state' => $Qorder->valueProtected('billing_state'),
                             'zone_code' => $Qorder->value('billing_state_code'),
                             'country_title' => $Qorder->valueProtected('billing_country'),
                             'country_iso2' => $Qorder->value('billing_country_iso2'),
                             'country_iso3' => $Qorder->value('billing_country_iso3'),
                             'format' => $Qorder->value('billing_address_format'));

      $Qproducts = $OSCOM_Database->query('select orders_products_id, products_id, products_name, products_model, products_price, products_tax, products_quantity from :table_orders_products where orders_id = :orders_id');
      $Qproducts->bindInt(':orders_id', $order_id);
      $Qproducts->execute();

      $index = 0;

      while ( $Qproducts->next() ) {
        $subindex = 0;

        $this->products[$index] = array('qty' => $Qproducts->valueInt('products_quantity'),
                                        'id' => $Qproducts->valueInt('products_id'),
                                        'name' => $Qproducts->value('products_name'),
                                        'model' => $Qproducts->value('products_model'),
                                        'tax' => $Qproducts->value('products_tax'),
                                        'price' => $Qproducts->value('products_price'));

        $Qvariants = $OSCOM_Database->query('select group_title, value_title from :table_orders_products_variants where orders_id = :orders_id and orders_products_id = :orders_products_id order by id');
        $Qvariants->bindInt(':orders_id', $order_id);
        $Qvariants->bindInt(':orders_products_id', $Qproducts->valueInt('orders_products_id'));
        $Qvariants->execute();

        if ( $Qvariants->numberOfRows() ) {
          while ( $Qvariants->next() ) {
            $this->products[$index]['attributes'][$subindex] = array('option' => $Qvariants->value('group_title'),
                                                                     'value' => $Qvariants->value('value_title'));

            $subindex++;
          }
        }

        $this->info['tax_groups']["{$this->products[$index]['tax']}"] = '1';

        $index++;
      }
    }
  }
?>
