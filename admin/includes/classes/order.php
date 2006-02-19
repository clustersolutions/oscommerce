<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class osC_Order {
// private variables
    var $_valid_order;

// class constructor
    function osC_Order($order_id = '') {
      $this->_valid_order = false;

      if (is_numeric($order_id)) {
        $this->_getSummary($order_id);
      }
    }

// private methods
    function _getSummary($order_id) {
      global $osC_Database;

      $Qorder = $osC_Database->query('select orders_id, customers_name, customers_company, customers_street_address, customers_suburb, customers_city, customers_postcode, customers_state, customers_country, customers_address_format_id, customers_telephone, customers_email_address, delivery_name, delivery_company, delivery_street_address, delivery_suburb, delivery_city, delivery_postcode, delivery_state, delivery_country, delivery_address_format_id, billing_name, billing_company, billing_street_address, billing_suburb, billing_city, billing_postcode, billing_state, billing_country, billing_address_format_id, payment_method, cc_type, cc_owner, cc_number, cc_expires, currency, currency_value, date_purchased, last_modified, orders_status from :table_orders where orders_id = :orders_id');
      $Qorder->bindTable(':table_orders', TABLE_ORDERS);
      $Qorder->bindInt(':orders_id', $order_id);
      $Qorder->execute();

      if ($Qorder->numberOfRows() === 1) {
        $this->_valid_order = true;

        $this->_order_id = $Qorder->valueInt('orders_id');

        $this->_customer = array('name' => $Qorder->valueProtected('customers_name'),
                                 'company' => $Qorder->valueProtected('customers_company'),
                                 'street_address' => $Qorder->valueProtected('customers_street_address'),
                                 'suburb' => $Qorder->valueProtected('customers_suburb'),
                                 'city' => $Qorder->valueProtected('customers_city'),
                                 'postcode' => $Qorder->valueProtected('customers_postcode'),
                                 'state' => $Qorder->valueProtected('customers_state'),
                                 'country' => $Qorder->value('customers_country'),
                                 'format_id' => $Qorder->valueInt('customers_address_format_id'),
                                 'telephone' => $Qorder->valueProtected('customers_telephone'),
                                 'email_address' => $Qorder->valueProtected('customers_email_address'));

        $this->_delivery = array('name' => $Qorder->valueProtected('delivery_name'),
                                 'company' => $Qorder->valueProtected('delivery_company'),
                                 'street_address' => $Qorder->valueProtected('delivery_street_address'),
                                 'suburb' => $Qorder->valueProtected('delivery_suburb'),
                                 'city' => $Qorder->valueProtected('delivery_city'),
                                 'postcode' => $Qorder->valueProtected('delivery_postcode'),
                                 'state' => $Qorder->valueProtected('delivery_state'),
                                 'country' => $Qorder->value('delivery_country'),
                                 'format_id' => $Qorder->valueInt('delivery_address_format_id'));

        $this->_billing = array('name' => $Qorder->valueProtected('billing_name'),
                                'company' => $Qorder->valueProtected('billing_company'),
                                'street_address' => $Qorder->valueProtected('billing_street_address'),
                                'suburb' => $Qorder->valueProtected('billing_suburb'),
                                'city' => $Qorder->valueProtected('billing_city'),
                                'postcode' => $Qorder->valueProtected('billing_postcode'),
                                'state' => $Qorder->valueProtected('billing_state'),
                                'country' => $Qorder->value('billing_country'),
                                'format_id' => $Qorder->valueInt('billing_address_format_id'));

        $this->_payment_method = $Qorder->value('payment_method');
        $this->_credit_card = array('type' => $Qorder->value('cc_type'),
                                    'owner' => $Qorder->valueProtected('cc_owner'),
                                    'number' => $Qorder->valueProtected('cc_number'),
                                    'expires' => $Qorder->value('cc_expires'));

        $this->_currency = array('code' => $Qorder->value('currency'),
                                 'value' => $Qorder->value('currency_value'));

        $this->_date_purchased = $Qorder->value('date_purchased');
        $this->_last_modified = $Qorder->value('last_modified');

        $this->_status_id = $Qorder->value('orders_status');
      }
    }

    function _getStatus() {
      global $osC_Database, $osC_Language;

      $Qstatus = $osC_Database->query('select orders_status_name from :table_orders_status where orders_status_id = :orders_status_id and language_id = :language_id');
      $Qstatus->bindTable(':table_orders_status', TABLE_ORDERS_STATUS);
      $Qstatus->bindInt(':orders_status_id', $this->_status_id);

/* HPDL - DEFAULT_LANGUAGE is the language code, not the language id */
//        $Qstatus->bindInt(':language_id', (isset($_SESSION['languages_id']) ? $_SESSION['languages_id'] : DEFAULT_LANGUAGE));
      $Qstatus->bindInt(':language_id', $osC_Language->getID());
      $Qstatus->execute();

      if ($Qstatus->numberOfRows() === 1) {
        $this->_status = $Qstatus->value('orders_status_name');
      } else {
        $this->_status = $this->_status_id;
      }
    }

    function _getStatusHistory() {
      global $osC_Database, $osC_Language;

      $history_array = array();

      $Qhistory = $osC_Database->query('select osh.orders_status_id, osh.date_added, osh.customer_notified, osh.comments, os.orders_status_name from :table_orders_status_history osh left join :table_orders_status os on (osh.orders_status_id = os.orders_status_id and os.language_id = :language_id) where osh.orders_id = :orders_id order by osh.date_added');
      $Qhistory->bindTable(':table_orders_status_history', TABLE_ORDERS_STATUS_HISTORY);
      $Qhistory->bindTable(':table_orders_status', TABLE_ORDERS_STATUS);

/* HPDL - DEFAULT_LANGUAGE is the language code, not the language id */
//        $Qstatus->bindInt(':language_id', (isset($_SESSION['languages_id']) ? $_SESSION['languages_id'] : DEFAULT_LANGUAGE));
      $Qhistory->bindInt(':language_id', $osC_Language->getID());

      $Qhistory->bindInt(':orders_id', $this->_order_id);
      $Qhistory->execute();

      while ($Qhistory->next()) {
        $history_array[] = array('status_id' => $Qhistory->valueInt('orders_status_id'),
                                 'status' => $Qhistory->value('orders_status_name'),
                                 'date_added' => $Qhistory->value('date_added'),
                                 'customer_notified' => $Qhistory->valueInt('customer_notified'),
                                 'comment' => $Qhistory->valueProtected('comments'));
      }

      $this->_status_history = $history_array;
    }

    function _getProducts() {
      global $osC_Database;

      $products_array = array();
      $key = 0;

      $Qproducts = $osC_Database->query('select orders_products_id, products_name, products_model, products_price, products_tax, products_quantity, final_price from :table_orders_products where orders_id = :orders_id');
      $Qproducts->bindTable(':table_orders_products', TABLE_ORDERS_PRODUCTS);
      $Qproducts->bindInt(':orders_id', $this->_order_id);
      $Qproducts->execute();

      while ($Qproducts->next()) {
        $products_array[$key] = array('quantity' => $Qproducts->valueInt('products_quantity'),
                                      'name' => $Qproducts->value('products_name'),
                                      'model' => $Qproducts->value('products_model'),
                                      'tax' => $Qproducts->value('products_tax'),
                                      'price' => $Qproducts->value('products_price'),
                                      'final_price' => $Qproducts->value('final_price'));

        $Qattributes = $osC_Database->query('select products_options, products_options_values, options_values_price, price_prefix from :table_orders_products_attributes where orders_id = :orders_id and orders_products_id = :orders_products_id');
        $Qattributes->bindTable(':table_orders_products_attributes', TABLE_ORDERS_PRODUCTS_ATTRIBUTES);
        $Qattributes->bindInt(':orders_id', $this->_order_id);
        $Qattributes->bindInt(':orders_products_id', $Qproducts->valueInt('orders_products_id'));
        $Qattributes->execute();

        if ($Qattributes->numberOfRows() > 0) {
          while ($Qattributes->next()) {
            $products_array[$key]['attributes'][] = array('option' => $Qattributes->value('products_options'),
                                                          'value' => $Qattributes->value('products_options_values'),
                                                          'prefix' => $Qattributes->value('price_prefix'),
                                                          'price' => $Qattributes->value('options_values_price'));
          }
        }

        $key++;
      }

      $this->_products = $products_array;
    }

    function _getTotals() {
      global $osC_Database;

      $totals_array = array();

      $Qtotals = $osC_Database->query('select title, text, value, class from :table_orders_total where orders_id = :orders_id order by sort_order');
      $Qtotals->bindTable(':table_orders_total', TABLE_ORDERS_TOTAL);
      $Qtotals->bindInt(':orders_id', $this->_order_id);
      $Qtotals->execute();

      while ($Qtotals->next()) {
        $totals_array[] = array('title' => $Qtotals->value('title'),
                                'text' => $Qtotals->value('text'),
                                'value' => $Qtotals->value('value'),
                                'class' => $Qtotals->value('class'));
      }

      $this->_totals = $totals_array;
    }

// public methods
    function isValid() {
      if ($this->_valid_order === true) {
        return true;
      } else {
        return false;
      }
    }

    function getOrderID() {
      return $this->_order_id;
    }

    function getCustomer($id = '') {
      if (empty($id)) {
        return $this->_customer;
      } elseif (isset($this->_customer[$id])) {
        return $this->_customer[$id];
      }

      return false;
    }

    function getDelivery($id = '') {
      if (empty($id)) {
        return $this->_delivery;
      } elseif (isset($this->_delivery[$id])) {
        return $this->_delivery[$id];
      }

      return false;
    }

    function getBilling($id = '') {
      if (empty($id)) {
        return $this->_billing;
      } elseif (isset($this->_billing[$id])) {
        return $this->_billing[$id];
      }

      return false;
    }

    function getPaymentMethod() {
      return $this->_payment_method;
    }

    function getCreditCardDetails($id = '') {
      if (empty($id)) {
        return $this->_credit_card;
      } elseif (isset($this->_credit_card[$id])) {
        return $this->_credit_card[$id];
      }

      return false;
    }

    function isValidCreditCard() {
      if (!empty($this->_credit_card['owner']) && !empty($this->_credit_card['number']) && !empty($this->_credit_card['expires'])) {
        return true;
      }

      return false;
    }

    function getCurrency($id = 'code') {
      if (isset($this->_currency[$id])) {
        return $this->_currency[$id];
      }

      return false;
    }

    function getCurrencyValue() {
      return $this->getCurrency('value');
    }

    function getDateCreated() {
      return $this->_date_purchased;
    }

    function getDateLastModified() {
      return $this->_last_modified;
    }

    function getStatusID() {
      return $this->_status_id;
    }

    function getStatus() {
      if (!isset($this->_status)) {
        $this->_getStatus();
      }

      return $this->_status;
    }

    function getNumberOfComments() {
      $number_of_comments = 0;

      if (!isset($this->_status_history)) {
        $this->_getStatusHistory();
      }

      foreach ($this->_status_history as $status_history) {
        if (!empty($status_history['comment'])) {
          $number_of_comments++;
        }
      }

      return $number_of_comments;
    }

    function getProducts() {
      if (!isset($this->_products)) {
        $this->_getProducts();
      }

      return $this->_products;
    }

    function getNumberOfProducts() {
      if (!isset($this->_products)) {
        $this->_getProducts();
      }

      return sizeof($this->_products);
    }

    function getNumberOfItems() {
      $number_of_items = 0;

      if (!isset($this->_products)) {
        $this->_getProducts();
      }

      foreach ($this->_products as $product) {
        $number_of_items += $product['quantity'];
      }

      return $number_of_items;
    }

    function getTotal($id = 'total') {
      if (!isset($this->_totals)) {
        $this->_getTotals();
      }

      foreach ($this->_totals as $total) {
        if ($total['class'] == $id) {
          return strip_tags($total['text']);
        }
      }

      return false;
    }

    function getTotals() {
      if (!isset($this->_totals)) {
        $this->_getTotals();
      }

      return $this->_totals;
    }

    function getStatusHistory() {
      if (!isset($this->_status_history)) {
        $this->_getStatusHistory();
      }

      return $this->_status_history;
    }
  }
?>
