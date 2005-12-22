<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class order {
    var $info, $totals, $products, $customer, $delivery, $content_type;

/* Private variables */

    var $_id;

/* Class constructor */

    function order($order_id = '') {
      if (is_numeric($order_id)) {
        $this->_id = $order_id;
      }

      $this->info = array();
      $this->totals = array();
      $this->products = array();
      $this->customer = array();
      $this->delivery = array();

      if (tep_not_null($order_id)) {
        $this->query($order_id);
      } else {
        $this->cart();
      }
    }

/* Public methods */

    function &getListing($limit = null, $page_keyword = 'page') {
      global $osC_Database, $osC_Customer;

      $Qorders = $osC_Database->query('select o.orders_id, o.date_purchased, o.delivery_name, o.delivery_country, o.billing_name, o.billing_country, ot.text as order_total, s.orders_status_name from :table_orders o, :table_orders_total ot, :table_orders_status s where o.customers_id = :customers_id and o.orders_id = ot.orders_id and ot.class = "ot_total" and o.orders_status = s.orders_status_id and s.language_id = :language_id order by orders_id desc');
      $Qorders->bindTable(':table_orders', TABLE_ORDERS);
      $Qorders->bindTable(':table_orders_total', TABLE_ORDERS_TOTAL);
      $Qorders->bindTable(':table_orders_status', TABLE_ORDERS_STATUS);
      $Qorders->bindInt(':customers_id', $osC_Customer->getID());
      $Qorders->bindInt(':language_id', $_SESSION['languages_id']);

      if (is_numeric($limit)) {
        $Qorders->setBatchLimit(isset($_GET[$page_keyword]) && is_numeric($_GET[$page_keyword]) ? $_GET[$page_keyword] : 1, $limit);
      }

      $Qorders->execute();

      return $Qorders;
    }

    function &getStatusListing($id = null) {
      global $osC_Database;

      if ( ($id === null) && isset($this) ) {
        $id = $this->_id;
      }

      $Qstatus = $osC_Database->query('select os.orders_status_name, osh.date_added, osh.comments from :table_orders_status os, :table_orders_status_history osh where osh.orders_id = :orders_id and osh.orders_status_id = os.orders_status_id and os.language_id = :language_id order by osh.date_added');
      $Qstatus->bindTable(':table_orders_status', TABLE_ORDERS_STATUS);
      $Qstatus->bindTable(':table_orders_status_history', TABLE_ORDERS_STATUS_HISTORY);
      $Qstatus->bindInt(':orders_id', $id);
      $Qstatus->bindInt(':language_id', $_SESSION['languages_id']);

      return $Qstatus;
    }

    function getCustomerID($id = null) {
      global $osC_Database;

      if ( ($id === null) && isset($this) ) {
        $id = $this->_id;
      }

      $Qcustomer = $osC_Database->query('select customers_id from :table_orders where orders_id = :orders_id');
      $Qcustomer->bindTable(':table_orders', TABLE_ORDERS);
      $Qcustomer->bindInt(':orders_id', $id);
      $Qcustomer->execute();

      return $Qcustomer->valueInt('customers_id');
    }

    function numberOfEntries() {
      global $osC_Database, $osC_Customer;
      static $total_entries;

      if (is_numeric($total_entries) === false) {
        if ($osC_Customer->isLoggedOn()) {
          $Qorders = $osC_Database->query('select count(*) as total from :table_orders where customers_id = :customers_id');
          $Qorders->bindTable(':table_orders', TABLE_ORDERS);
          $Qorders->bindInt(':customers_id', $osC_Customer->getID());
          $Qorders->execute();

          $total_entries = $Qorders->valueInt('total');
        } else {
          $total_entries = 0;
        }
      }

      return $total_entries;
    }

    function numberOfProducts($id = null) {
      global $osC_Database;

      if ( ($id === null) && isset($this) ) {
        $id = $this->_id;
      }

      $Qproducts = $osC_Database->query('select count(*) as total from :table_orders_products where orders_id = :orders_id');
      $Qproducts->bindTable(':table_orders_products', TABLE_ORDERS_PRODUCTS);
      $Qproducts->bindInt(':orders_id', $id);
      $Qproducts->execute();

      return $Qproducts->valueInt('total');
    }



    function query($order_id) {
      global $osC_Database;

      $Qorder = $osC_Database->query('select customers_id, customers_name, customers_company, customers_street_address, customers_suburb, customers_city, customers_postcode, customers_state, customers_country, customers_telephone, customers_email_address, customers_address_format_id, delivery_name, delivery_company, delivery_street_address, delivery_suburb, delivery_city, delivery_postcode, delivery_state, delivery_country, delivery_address_format_id, billing_name, billing_company, billing_street_address, billing_suburb, billing_city, billing_postcode, billing_state, billing_country, billing_address_format_id, payment_method, cc_type, cc_owner, cc_number, cc_expires, currency, currency_value, date_purchased, orders_status, last_modified from :table_orders where orders_id = :orders_id');
      $Qorder->bindTable(':table_orders', TABLE_ORDERS);
      $Qorder->bindInt(':orders_id', $order_id);
      $Qorder->execute();

      $Qtotals = $osC_Database->query('select title, text, class from :table_orders_total where orders_id = :orders_id order by sort_order');
      $Qtotals->bindTable(':table_orders_total', TABLE_ORDERS_TOTAL);
      $Qtotals->bindInt(':orders_id', $order_id);
      $Qtotals->execute();

      $shipping_method_string = '';
      $order_total_string = '';

      while ($Qtotals->next()) {
        $this->totals[] = array('title' => $Qtotals->value('title'),
                                'text' => $Qtotals->value('text'));

        if ($Qtotals->value('class') == 'ot_shipping') {
          $shipping_method_string = strip_tags($Qtotals->value('title'));

          if (substr($shipping_method_string, -1) == ':') {
            $shipping_method_string = substr($Qtotals->value('title'), 0, -1);
          }
        }

        if ($Qtotals->value('class') == 'ot_total') {
          $order_total_string = strip_tags($Qtotals->value('text'));
        }
      }

      $Qstatus = $osC_Database->query('select orders_status_name from :table_orders_status where orders_status_id = :orders_status_id and language_id = :language_id');
      $Qstatus->bindTable(':table_orders_status', TABLE_ORDERS_STATUS);
      $Qstatus->bindInt(':orders_status_id', $Qorder->valueInt('orders_status'));
      $Qstatus->bindInt(':language_id', $_SESSION['languages_id']);
      $Qstatus->execute();

      $this->info = array('currency' => $Qorder->value('currency'),
                          'currency_value' => $Qorder->value('currency_value'),
                          'payment_method' => $Qorder->value('payment_method'),
                          'cc_type' => $Qorder->value('cc_type'),
                          'cc_owner' => $Qorder->valueProtected('cc_owner'),
                          'cc_number' => $Qorder->valueProtected('cc_number'),
                          'cc_expires' => $Qorder->valueProtected('cc_expires'),
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
                              'country' => $Qorder->valueProtected('customers_country'),
                              'format_id' => $Qorder->valueInt('customers_address_format_id'),
                              'telephone' => $Qorder->valueProtected('customers_telephone'),
                              'email_address' => $Qorder->valueProtected('customers_email_address'));

      $this->delivery = array('name' => $Qorder->valueProtected('delivery_name'),
                              'company' => $Qorder->valueProtected('delivery_company'),
                              'street_address' => $Qorder->valueProtected('delivery_street_address'),
                              'suburb' => $Qorder->valueProtected('delivery_suburb'),
                              'city' => $Qorder->valueProtected('delivery_city'),
                              'postcode' => $Qorder->valueProtected('delivery_postcode'),
                              'state' => $Qorder->valueProtected('delivery_state'),
                              'country' => $Qorder->valueProtected('delivery_country'),
                              'format_id' => $Qorder->valueInt('delivery_address_format_id'));

      if (empty($this->delivery['name']) && empty($this->delivery['street_address'])) {
        $this->delivery = false;
      }

      $this->billing = array('name' => $Qorder->valueProtected('billing_name'),
                             'company' => $Qorder->valueProtected('billing_company'),
                             'street_address' => $Qorder->valueProtected('billing_street_address'),
                             'suburb' => $Qorder->valueProtected('billing_suburb'),
                             'city' => $Qorder->valueProtected('billing_city'),
                             'postcode' => $Qorder->valueProtected('billing_postcode'),
                             'state' => $Qorder->valueProtected('billing_state'),
                             'country' => $Qorder->valueProtected('billing_country'),
                             'format_id' => $Qorder->valueInt('billing_address_format_id'));

      $Qproducts = $osC_Database->query('select orders_products_id, products_id, products_name, products_model, products_price, products_tax, products_quantity, final_price from :table_orders_products where orders_id = :orders_id');
      $Qproducts->bindTable(':table_orders_products', TABLE_ORDERS_PRODUCTS);
      $Qproducts->bindInt(':orders_id', $order_id);
      $Qproducts->execute();

      $index = 0;

      while ($Qproducts->next()) {
        $subindex = 0;

        $this->products[$index] = array('qty' => $Qproducts->valueInt('products_quantity'),
                                        'id' => $Qproducts->valueInt('products_id'),
                                        'name' => $Qproducts->value('products_name'),
                                        'model' => $Qproducts->value('products_model'),
                                        'tax' => $Qproducts->value('products_tax'),
                                        'price' => $Qproducts->value('products_price'),
                                        'final_price' => $Qproducts->value('final_price'));

        $Qattributes = $osC_Database->query('select products_options, products_options_values, options_values_price, price_prefix from :table_orders_products_attributes where orders_id = :orders_id and orders_products_id = :orders_products_id');
        $Qattributes->bindTable(':table_orders_products_attributes', TABLE_ORDERS_PRODUCTS_ATTRIBUTES);
        $Qattributes->bindInt(':orders_id', $order_id);
        $Qattributes->bindInt(':orders_products_id', $Qproducts->valueInt('orders_products_id'));
        $Qattributes->execute();

        if ($Qattributes->numberOfRows()) {
          while ($Qattributes->next()) {
            $this->products[$index]['attributes'][$subindex] = array('option' => $Qattributes->value('products_options'),
                                                                     'value' => $Qattributes->value('products_options_values'),
                                                                     'prefix' => $Qattributes->value('price_prefix'),
                                                                     'price' => $Qattributes->value('options_values_price'));

            $subindex++;
          }
        }

        $this->info['tax_groups']["{$this->products[$index]['tax']}"] = '1';

        $index++;
      }
    }

    function cart() {
      global $osC_Database, $osC_Customer, $osC_Tax, $osC_Currencies;

      $this->content_type = $_SESSION['cart']->get_content_type();

      $shipping =& $_SESSION['shipping'];
      $payment =& $_SESSION['payment'];

      $Qcustomer = $osC_Database->query('select c.customers_firstname, c.customers_lastname, c.customers_telephone, c.customers_email_address, ab.entry_company, ab.entry_street_address, ab.entry_suburb, ab.entry_postcode, ab.entry_city, ab.entry_zone_id, z.zone_name, co.countries_id, co.countries_name, co.countries_iso_code_2, co.countries_iso_code_3, co.address_format_id, ab.entry_state from :table_customers c, :table_address_book ab left join :table_zones z on (ab.entry_zone_id = z.zone_id) left join :table_countries co on (ab.entry_country_id = co.countries_id) where c.customers_id = :customers_id and ab.customers_id = :customers_id and c.customers_default_address_id = ab.address_book_id');
      $Qcustomer->bindTable(':table_customers', TABLE_CUSTOMERS);
      $Qcustomer->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
      $Qcustomer->bindTable(':table_zones', TABLE_ZONES);
      $Qcustomer->bindTable(':table_countries', TABLE_COUNTRIES);
      $Qcustomer->bindInt(':customers_id', $osC_Customer->getID());
      $Qcustomer->bindInt(':customers_id', $osC_Customer->getID());
      $Qcustomer->execute();

      $Qshipping = $osC_Database->query('select ab.entry_firstname, ab.entry_lastname, ab.entry_company, ab.entry_street_address, ab.entry_suburb, ab.entry_postcode, ab.entry_city, ab.entry_zone_id, z.zone_name, ab.entry_country_id, c.countries_id, c.countries_name, c.countries_iso_code_2, c.countries_iso_code_3, c.address_format_id, ab.entry_state from :table_address_book ab left join :table_zones z on (ab.entry_zone_id = z.zone_id) left join :table_countries c on (ab.entry_country_id = c.countries_id) where ab.customers_id = :customers_id and ab.address_book_id = :address_book_id');
      $Qshipping->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
      $Qshipping->bindTable(':table_zones', TABLE_ZONES);
      $Qshipping->bindTable(':table_countries', TABLE_COUNTRIES);
      $Qshipping->bindInt(':customers_id', $osC_Customer->getID());
      $Qshipping->bindInt(':address_book_id', $_SESSION['sendto']);
      $Qshipping->execute();

      $Qbilling = $osC_Database->query('select ab.entry_firstname, ab.entry_lastname, ab.entry_company, ab.entry_street_address, ab.entry_suburb, ab.entry_postcode, ab.entry_city, ab.entry_zone_id, z.zone_name, ab.entry_country_id, c.countries_id, c.countries_name, c.countries_iso_code_2, c.countries_iso_code_3, c.address_format_id, ab.entry_state from :table_address_book ab left join :table_zones z on (ab.entry_zone_id = z.zone_id) left join :table_countries c on (ab.entry_country_id = c.countries_id) where ab.customers_id = :customers_id and ab.address_book_id = :address_book_id');
      $Qbilling->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
      $Qbilling->bindTable(':table_zones', TABLE_ZONES);
      $Qbilling->bindTable(':table_countries', TABLE_COUNTRIES);
      $Qbilling->bindInt(':customers_id', $osC_Customer->getID());
      $Qbilling->bindInt(':address_book_id', $_SESSION['billto']);
      $Qbilling->execute();

      $Qtax = $osC_Database->query('select ab.entry_country_id, ab.entry_zone_id from :table_address_book ab left join :table_zones z on (ab.entry_zone_id = z.zone_id) where ab.customers_id = :customers_id and ab.address_book_id = :address_book_id');
      $Qtax->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
      $Qtax->bindTable(':table_zones', TABLE_ZONES);
      $Qtax->bindInt(':customers_id', $osC_Customer->getID());
      $Qtax->bindInt(':address_book_id', ($this->content_type == 'virtual' ? $_SESSION['billto'] : $_SESSION['sendto']));
      $Qtax->execute();

      $this->info = array('order_status' => DEFAULT_ORDERS_STATUS_ID,
                          'currency' => $_SESSION['currency'],
                          'currency_value' => $osC_Currencies->currencies[$_SESSION['currency']]['value'],
                          'payment_method' => $payment,
                          'cc_type' => (isset($GLOBALS['cc_type']) ? $GLOBALS['cc_type'] : ''),
                          'cc_owner' => (isset($GLOBALS['cc_owner']) ? $GLOBALS['cc_owner'] : ''),
                          'cc_number' => (isset($GLOBALS['cc_number']) ? $GLOBALS['cc_number'] : ''),
                          'cc_expires' => (isset($GLOBALS['cc_expires']) ? $GLOBALS['cc_expires'] : ''),
                          'shipping_method' => $shipping['title'],
                          'shipping_cost' => $shipping['cost'],
                          'subtotal' => 0,
                          'tax' => 0,
                          'tax_groups' => array(),
                          'comments' => (isset($_SESSION['comments']) ? $_SESSION['comments'] : ''));

      if (isset($GLOBALS[$payment]) && is_object($GLOBALS[$payment])) {
        $this->info['payment_method'] = $GLOBALS[$payment]->title;

        if ( isset($GLOBALS[$payment]->order_status) && is_numeric($GLOBALS[$payment]->order_status) && ($GLOBALS[$payment]->order_status > 0) ) {
          $this->info['order_status'] = $GLOBALS[$payment]->order_status;
        }
      }

      $this->customer = array('firstname' => $Qcustomer->valueProtected('customers_firstname'),
                              'lastname' => $Qcustomer->valueProtected('customers_lastname'),
                              'company' => $Qcustomer->valueProtected('entry_company'),
                              'street_address' => $Qcustomer->valueProtected('entry_street_address'),
                              'suburb' => $Qcustomer->valueProtected('entry_suburb'),
                              'city' => $Qcustomer->valueProtected('entry_city'),
                              'postcode' => $Qcustomer->valueProtected('entry_postcode'),
                              'state' => (tep_not_null($Qcustomer->valueProtected('entry_state')) ? $Qcustomer->valueProtected('entry_state') : $Qcustomer->valueProtected('zone_name')),
                              'zone_id' => $Qcustomer->valueInt('entry_zone_id'),
                              'country' => array('id' => $Qcustomer->valueInt('countries_id'), 'title' => $Qcustomer->value('countries_name'), 'iso_code_2' => $Qcustomer->value('countries_iso_code_2'), 'iso_code_3' => $Qcustomer->value('countries_iso_code_3')),
                              'format_id' => $Qcustomer->valueInt('address_format_id'),
                              'telephone' => $Qcustomer->valueProtected('customers_telephone'),
                              'email_address' => $Qcustomer->valueProtected('customers_email_address'));

      $this->delivery = array('firstname' => $Qshipping->valueProtected('entry_firstname'),
                              'lastname' => $Qshipping->valueProtected('entry_lastname'),
                              'company' => $Qshipping->valueProtected('entry_company'),
                              'street_address' => $Qshipping->valueProtected('entry_street_address'),
                              'suburb' => $Qshipping->valueProtected('entry_suburb'),
                              'city' => $Qshipping->valueProtected('entry_city'),
                              'postcode' => $Qshipping->valueProtected('entry_postcode'),
                              'state' => (tep_not_null($Qshipping->valueProtected('entry_state')) ? $Qshipping->valueProtected('entry_state') : $Qshipping->valueProtected('zone_name')),
                              'zone_id' => $Qshipping->valueInt('entry_zone_id'),
                              'country' => array('id' => $Qshipping->valueInt('countries_id'), 'title' => $Qshipping->value('countries_name'), 'iso_code_2' => $Qshipping->value('countries_iso_code_2'), 'iso_code_3' => $Qshipping->value('countries_iso_code_3')),
                              'country_id' => $Qshipping->valueInt('entry_country_id'),
                              'format_id' => $Qshipping->valueInt('address_format_id'));

      $this->billing = array('firstname' => $Qbilling->valueProtected('entry_firstname'),
                             'lastname' => $Qbilling->valueProtected('entry_lastname'),
                             'company' => $Qbilling->valueProtected('entry_company'),
                             'street_address' => $Qbilling->valueProtected('entry_street_address'),
                             'suburb' => $Qbilling->valueProtected('entry_suburb'),
                             'city' => $Qbilling->valueProtected('entry_city'),
                             'postcode' => $Qbilling->valueProtected('entry_postcode'),
                             'state' => (tep_not_null($Qbilling->valueProtected('entry_state')) ? $Qbilling->valueProtected('entry_state') : $Qbilling->valueProtected('zone_name')),
                             'zone_id' => $Qbilling->valueInt('entry_zone_id'),
                             'country' => array('id' => $Qbilling->valueInt('countries_id'), 'title' => $Qbilling->value('countries_name'), 'iso_code_2' => $Qbilling->value('countries_iso_code_2'), 'iso_code_3' => $Qbilling->value('countries_iso_code_3')),
                             'country_id' => $Qbilling->valueInt('entry_country_id'),
                             'format_id' => $Qbilling->valueInt('address_format_id'));

      $index = 0;
      $products = $_SESSION['cart']->get_products();
      for ($i=0, $n=sizeof($products); $i<$n; $i++) {
        $this->products[$index] = array('qty' => $products[$i]['quantity'],
                                        'name' => $products[$i]['name'],
                                        'model' => $products[$i]['model'],
                                        'tax' => $osC_Tax->getTaxRate($products[$i]['tax_class_id'], $Qtax->valueInt('entry_country_id'), $Qtax->valueInt('entry_zone_id')),
                                        'tax_description' => $osC_Tax->getTaxRateDescription($products[$i]['tax_class_id'], $Qtax->valueInt('entry_country_id'), $Qtax->valueInt('entry_zone_id')),
                                        'tax_class_id' => $products[$i]['tax_class_id'],
                                        'price' => $products[$i]['price'],
                                        'final_price' => $products[$i]['price'] + $_SESSION['cart']->attributes_price($products[$i]['id']),
                                        'weight' => $products[$i]['weight'],
                                        'id' => $products[$i]['id']);

        if ($products[$i]['attributes']) {
          $subindex = 0;
          reset($products[$i]['attributes']);
          while (list($option, $value) = each($products[$i]['attributes'])) {
            $Qattributes = $osC_Database->query('select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix from :table_products_options popt, :table_products_options_values poval, :table_products_attributes pa where pa.products_id = :products_id and pa.options_id = :options_id and pa.options_id = popt.products_options_id and pa.options_values_id = :options_values_id and pa.options_values_id = poval.products_options_values_id and popt.language_id = :language_id and poval.language_id = :language_id');
            $Qattributes->bindTable(':table_products_options', TABLE_PRODUCTS_OPTIONS);
            $Qattributes->bindTable(':table_products_options_values', TABLE_PRODUCTS_OPTIONS_VALUES);
            $Qattributes->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
            $Qattributes->bindInt(':products_id', $products[$i]['id']);
            $Qattributes->bindInt(':options_id', $option);
            $Qattributes->bindInt(':options_values_id', $value);
            $Qattributes->bindInt(':language_id', $_SESSION['languages_id']);
            $Qattributes->bindInt(':language_id', $_SESSION['languages_id']);
            $Qattributes->execute();

            $this->products[$index]['attributes'][$subindex] = array('option' => $Qattributes->value('products_options_name'),
                                                                     'value' => $Qattributes->value('products_options_values_name'),
                                                                     'option_id' => $option,
                                                                     'value_id' => $value,
                                                                     'prefix' => $Qattributes->value('price_prefix'),
                                                                     'price' => $Qattributes->value('options_values_price'));

            $subindex++;
          }
        }

        $shown_price = tep_add_tax($this->products[$index]['final_price'], $this->products[$index]['tax']) * $this->products[$index]['qty'];
        $this->info['subtotal'] += $shown_price;

        $products_tax = $this->products[$index]['tax'];
        $products_tax_description = $this->products[$index]['tax_description'];
        if (DISPLAY_PRICE_WITH_TAX == 'true') {
          $this->info['tax'] += $shown_price - ($shown_price / (($products_tax < 10) ? "1.0" . str_replace('.', '', $products_tax) : "1." . str_replace('.', '', $products_tax)));
          if (isset($this->info['tax_groups']["$products_tax_description"])) {
            $this->info['tax_groups']["$products_tax_description"] += $shown_price - ($shown_price / (($products_tax < 10) ? "1.0" . str_replace('.', '', $products_tax) : "1." . str_replace('.', '', $products_tax)));
          } else {
            $this->info['tax_groups']["$products_tax_description"] = $shown_price - ($shown_price / (($products_tax < 10) ? "1.0" . str_replace('.', '', $products_tax) : "1." . str_replace('.', '', $products_tax)));
          }
        } else {
          $this->info['tax'] += ($products_tax / 100) * $shown_price;
          if (isset($this->info['tax_groups']["$products_tax_description"])) {
            $this->info['tax_groups']["$products_tax_description"] += ($products_tax / 100) * $shown_price;
          } else {
            $this->info['tax_groups']["$products_tax_description"] = ($products_tax / 100) * $shown_price;
          }
        }

        $index++;
      }

      if (DISPLAY_PRICE_WITH_TAX == 'true') {
        $this->info['total'] = $this->info['subtotal'] + $this->info['shipping_cost'];
      } else {
        $this->info['total'] = $this->info['subtotal'] + $this->info['tax'] + $this->info['shipping_cost'];
      }
    }
  }
?>
