<?php
/*
  $Id:process.php 188 2005-09-15 02:25:52 +0200 (Do, 15 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Checkout_Process extends osC_Template {

/* Private variables */

    var $_module = 'process';

/* Class constructor */

    function osC_Checkout_Process() {
      global $osC_Session, $osC_ShoppingCart, $osC_Customer, $osC_NavigationHistory, $osC_Payment;

      if ($osC_Customer->isLoggedOn() === false) {
        $osC_NavigationHistory->setSnapshot();

        tep_redirect(osc_href_link(FILENAME_ACCOUNT, 'login', 'SSL'));
      }

      if ($osC_ShoppingCart->hasContents() === false) {
        tep_redirect(osc_href_link(FILENAME_CHECKOUT, null, 'SSL'));
      }

// if no shipping method has been selected, redirect the customer to the shipping method selection page
      if (($osC_ShoppingCart->hasShippingMethod() === false) && ($osC_ShoppingCart->getContentType() != 'virtual')) {
        tep_redirect(osc_href_link(FILENAME_CHECKOUT, 'shipping', 'SSL'));
      }

// load selected payment module
      include('includes/classes/payment.php');
      $osC_Payment = new osC_Payment($osC_ShoppingCart->getBillingMethod('id'));

      if ($osC_Payment->hasActive() && ($osC_ShoppingCart->hasBillingMethod() === false)) {
        tep_redirect(osc_href_link(FILENAME_CHECKOUT, 'payment', 'SSL'));
      }

      include('includes/classes/order.php');

      $osC_Payment->process();

      $osC_ShoppingCart->reset(true);

// unregister session variables used during checkout
      unset($_SESSION['comments']);

      tep_redirect(osc_href_link(FILENAME_CHECKOUT, 'success', 'SSL'));
    }

/* Private methods */

    function _process() {
      global $osC_Database, $osC_Session, $osC_ShoppingCart, $osC_Customer, $osC_Currencies, $osC_Language, $osC_Payment, $insert_id;

      $payment = 'osC_Payment_' . $osC_ShoppingCart->getBillingMethod('id');

// load the before_process function from the payment modules
      $osC_Payment->before_process();

      $order_status_id = DEFAULT_ORDERS_STATUS_ID;
      if ( isset($GLOBALS[$osC_ShoppingCart->getBillingMethod('id')]->order_status) && is_numeric($GLOBALS[$osC_ShoppingCart->getBillingMethod('id')]->order_status) ) {
        $order_status_id = $GLOBALS[$osC_ShoppingCart->getBillingMethod('id')]->order_status;
      }

      $Qorder = $osC_Database->query('insert into :table_orders (customers_id, customers_name, customers_company, customers_street_address, customers_suburb, customers_city, customers_postcode, customers_state, customers_country, customers_telephone, customers_email_address, customers_address_format_id, customers_ip_address, delivery_name, delivery_company, delivery_street_address, delivery_suburb, delivery_city, delivery_postcode, delivery_state, delivery_country, delivery_address_format_id, billing_name, billing_company, billing_street_address, billing_suburb, billing_city, billing_postcode, billing_state, billing_country, billing_address_format_id, payment_method, payment_module, cc_type, cc_owner, cc_number, cc_expires, date_purchased, orders_status, currency, currency_value) values (:customers_id, :customers_name, :customers_company, :customers_street_address, :customers_suburb, :customers_city, :customers_postcode, :customers_state, :customers_country, :customers_telephone, :customers_email_address, :customers_address_format_id, :customers_ip_address, :delivery_name, :delivery_company, :delivery_street_address, :delivery_suburb, :delivery_city, :delivery_postcode, :delivery_state, :delivery_country, :delivery_address_format_id, :billing_name, :billing_company, :billing_street_address, :billing_suburb, :billing_city, :billing_postcode, :billing_state, :billing_country, :billing_address_format_id, :payment_method, :payment_module, :cc_type, :cc_owner, :cc_number, :cc_expires, now(), :orders_status, :currency, :currency_value)');
      $Qorder->bindTable(':table_orders', TABLE_ORDERS);
      $Qorder->bindInt(':customers_id', $osC_Customer->getID());
      $Qorder->bindValue(':customers_name', $osC_Customer->getName());
      $Qorder->bindValue(':customers_company', '' /*$order->customer['company']*/);
      $Qorder->bindValue(':customers_street_address', '' /*$order->customer['street_address']*/);
      $Qorder->bindValue(':customers_suburb', '' /*$order->customer['suburb']*/);
      $Qorder->bindValue(':customers_city', '' /*$order->customer['city']*/);
      $Qorder->bindValue(':customers_postcode', '' /*$order->customer['postcode']*/);
      $Qorder->bindValue(':customers_state', '' /*$order->customer['state']*/);
      $Qorder->bindValue(':customers_country', '' /*$order->customer['country']['title']*/);
      $Qorder->bindValue(':customers_telephone', '' /*$order->customer['telephone']*/);
      $Qorder->bindValue(':customers_email_address', $osC_Customer->getEmailAddress());
      $Qorder->bindInt(':customers_address_format_id', $osC_Customer->getDefaultAddressID());
      $Qorder->bindValue(':customers_ip_address', tep_get_ip_address());
      $Qorder->bindValue(':delivery_name', $osC_ShoppingCart->getShippingAddress('firstname') . ' ' . $osC_ShoppingCart->getShippingAddress('lastname'));
      $Qorder->bindValue(':delivery_company', $osC_ShoppingCart->getShippingAddress('company'));
      $Qorder->bindValue(':delivery_street_address', $osC_ShoppingCart->getShippingAddress('street_address'));
      $Qorder->bindValue(':delivery_suburb', $osC_ShoppingCart->getShippingAddress('suburb'));
      $Qorder->bindValue(':delivery_city', $osC_ShoppingCart->getShippingAddress('city'));
      $Qorder->bindValue(':delivery_postcode', $osC_ShoppingCart->getShippingAddress('postcode'));
      $Qorder->bindValue(':delivery_state', $osC_ShoppingCart->getShippingAddress('state'));
      $Qorder->bindValue(':delivery_country', $osC_ShoppingCart->getShippingAddress('country_title'));
      $Qorder->bindInt(':delivery_address_format_id', $osC_ShoppingCart->getShippingAddress('format_id'));
      $Qorder->bindValue(':billing_name', $osC_ShoppingCart->getBillingAddress('firstname') . ' ' . $osC_ShoppingCart->getBillingAddress('lastname'));
      $Qorder->bindValue(':billing_company', $osC_ShoppingCart->getBillingAddress('company'));
      $Qorder->bindValue(':billing_street_address', $osC_ShoppingCart->getBillingAddress('street_address'));
      $Qorder->bindValue(':billing_suburb', $osC_ShoppingCart->getBillingAddress('suburb'));
      $Qorder->bindValue(':billing_city', $osC_ShoppingCart->getBillingAddress('city'));
      $Qorder->bindValue(':billing_postcode', $osC_ShoppingCart->getBillingAddress('postcode'));
      $Qorder->bindValue(':billing_state', $osC_ShoppingCart->getBillingAddress('state'));
      $Qorder->bindValue(':billing_country', $osC_ShoppingCart->getBillingAddress('country_id'));
      $Qorder->bindInt(':billing_address_format_id', $osC_ShoppingCart->getBillingAddress('format_id'));
      $Qorder->bindValue(':payment_method', $osC_ShoppingCart->getBillingMethod('title'));
      $Qorder->bindValue(':payment_module', $GLOBALS[$payment]->getCode());
      $Qorder->bindValue(':cc_type', '' /*$order->info['cc_type']*/);
      $Qorder->bindValue(':cc_owner', '' /*$order->info['cc_owner']*/);
      $Qorder->bindValue(':cc_number', '' /*$order->info['cc_number']*/);
      $Qorder->bindValue(':cc_expires', '' /*$order->info['cc_expires']*/);
      $Qorder->bindInt(':orders_status', $order_status_id);
      $Qorder->bindValue(':currency', $osC_Currencies->getCode());
      $Qorder->bindValue(':currency_value', $osC_Currencies->value($osC_Currencies->getCode()));
      $Qorder->execute();

      $insert_id = $osC_Database->nextID();

//      if ($osC_OrderTotal->hasActive()) {
//        foreach ($osC_OrderTotal->getResult() as $module) {
        foreach ($osC_ShoppingCart->getOrderTotals() as $module) {
          $Qtotals = $osC_Database->query('insert into :table_orders_total (orders_id, title, text, value, class, sort_order) values (:orders_id, :title, :text, :value, :class, :sort_order)');
          $Qtotals->bindTable(':table_orders_total', TABLE_ORDERS_TOTAL);
          $Qtotals->bindInt(':orders_id', $insert_id);
          $Qtotals->bindValue(':title', $module['title']);
          $Qtotals->bindValue(':text', $module['text']);
          $Qtotals->bindValue(':value', $module['value']);
          $Qtotals->bindValue(':class', $module['code']);
          $Qtotals->bindInt(':sort_order', $module['sort_order']);
          $Qtotals->execute();
        }
//      }

      $Qstatus = $osC_Database->query('insert into :table_orders_status_history (orders_id, orders_status_id, date_added, customer_notified, comments) values (:orders_id, :orders_status_id, now(), :customer_notified, :comments)');
      $Qstatus->bindTable(':table_orders_status_history', TABLE_ORDERS_STATUS_HISTORY);
      $Qstatus->bindInt(':orders_id', $insert_id);
      $Qstatus->bindInt(':orders_status_id', $order_status_id);
      $Qstatus->bindInt(':customer_notified', (SEND_EMAILS == '1') ? '1' : '0');
      $Qstatus->bindValue(':comments', (isset($_SESSION['comments']) ? $_SESSION['comments'] : ''));
      $Qstatus->execute();

// initialized for the email confirmation
      $products_ordered = '';
      $subtotal = 0;
      $total_tax = 0;
      $total_weight = 0;
      $total_cost = 0;

      foreach ($osC_ShoppingCart->getProducts() as $products) {
        if (STOCK_LIMITED == '1') {
          if (DOWNLOAD_ENABLED == '1') {
            $Qstock = $osC_Database->query('select products_quantity, pad.products_attributes_filename from :table_products p left join :table_products_attributes pa on (p.products_id = pa.products_id) left join :table_products_attributes_download pad on (pa.products_attributes_id = pad.products_attributes_id) where p.products_id = :products_id');
            $Qstock->bindTable(':table_products', TABLE_PRODUCTS);
            $Qstock->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
            $Qstock->bindTable(':table_products_attributes_download', TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD);
            $Qstock->bindInt(':products_id', tep_get_prid($products['id']));

// Will work with only one option for downloadable products otherwise, we have to build the query dynamically with a loop
            if ($osC_ShoppingCart->hasAttributes($products['id'])) {
              $products_attributes = $osC_ShoppingCart->getAttributes($products['id']);
              $products_attributes = array_shift($products_attributes);

              $Qstock->appendQuery('and pa.options_id = :options_id and pa.options_values_id = :options_values_id');
              $Qstock->bindInt(':options_id', $products_attributes['options_id']);
              $Qstock->bindInt(':options_values_id', $products_attributes['options_values_id']);
            }
          } else {
            $Qstock = $osC_Database->query('select products_quantity from :table_products where products_id = :products_id');
            $Qstock->bindTable(':table_products', TABLE_PRODUCTS);
            $Qstock->bindInt(':products_id', tep_get_prid($products['id']));
          }

          $Qstock->execute();

          if ($Qstock->numberOfRows() > 0) {
            $stock_left = $Qstock->valueInt('products_quantity');

// do not decrement quantities if products_attributes_filename exists
            if ((DOWNLOAD_ENABLED == '-1') || ((DOWNLOAD_ENABLED == '1') && (strlen($Qstock->value('products_attributes_filename')) < 1))) {
              $stock_left = $stock_left - $products['quantity'];

              $Qupdate = $osC_Database->query('update :table_products set products_quantity = :products_quantity where products_id = :products_id');
              $Qupdate->bindTable(':table_products', TABLE_PRODUCTS);
              $Qupdate->bindInt(':products_quantity', $stock_left);
              $Qupdate->bindInt(':products_id', tep_get_prid($products['id']));
              $Qupdate->execute();
            }

            if ((STOCK_ALLOW_CHECKOUT == '-1') && ($stock_left < 1)) {
              $Qupdate = $osC_Database->query('update :table_products set products_status = :products_status where products_id = :products_id');
              $Qupdate->bindTable(':table_products', TABLE_PRODUCTS);
              $Qupdate->bindInt(':products_status', 0);
              $Qupdate->bindInt(':products_id', tep_get_prid($products['id']));
              $Qupdate->execute();
            }
          }
        }

// Update products_ordered (for bestsellers list)
        $Qupdate = $osC_Database->query('update :table_products set products_ordered = products_ordered + :products_ordered where products_id = :products_id');
        $Qupdate->bindTable(':table_products', TABLE_PRODUCTS);
        $Qupdate->bindInt(':products_ordered', $products['quantity']);
        $Qupdate->bindInt(':products_id', tep_get_prid($products['id']));
        $Qupdate->execute();

        $Qproducts = $osC_Database->query('insert into :table_orders_products (orders_id, products_id, products_model, products_name, products_price, final_price, products_tax, products_quantity) values (:orders_id, :products_id, :products_model, :products_name, :products_price, :final_price, :products_tax, :products_quantity)');
        $Qproducts->bindTable(':table_orders_products', TABLE_ORDERS_PRODUCTS);
        $Qproducts->bindInt(':orders_id', $insert_id);
        $Qproducts->bindInt(':products_id', tep_get_prid($products['id']));
        $Qproducts->bindValue(':products_model', '' /*$products['model']*/);
        $Qproducts->bindValue(':products_name', $products['name']);
        $Qproducts->bindValue(':products_price', $products['price']);
        $Qproducts->bindValue(':final_price', $products['final_price']);
        $Qproducts->bindValue(':products_tax', '' /*$products['tax']*/);
        $Qproducts->bindInt(':products_quantity', $products['quantity']);
        $Qproducts->execute();

        $order_products_id = $osC_Database->nextID();

        $attributes_exist = '0';
        $products_ordered_attributes = '';

        if ($osC_ShoppingCart->hasAttributes($products['id'])) {
          $attributes_exist = '1';

          foreach ($osC_ShoppingCart->getAttributes($products['id']) as $atttributes) {
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
            } else {
              $Qattributes = $osC_Database->query('select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix from :table_products_options popt, :table_products_options_values poval, :table_products_attributes pa where pa.products_id = :products_id and pa.options_id = :options_id and pa.options_id = popt.products_options_id and pa.options_values_id = :options_values_id and pa.options_values_id = poval.products_options_values_id and popt.language_id = :popt_language_id and poval.language_id = :poval_language_id');
              $Qattributes->bindTable(':table_products_options', TABLE_PRODUCTS_OPTIONS);
              $Qattributes->bindTable(':table_products_options_values', TABLE_PRODUCTS_OPTIONS_VALUES);
              $Qattributes->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
              $Qattributes->bindInt(':products_id', $products['id']);
              $Qattributes->bindInt(':options_id', $attributes['options_id']);
              $Qattributes->bindInt(':options_values_id', $attributes['options_values_id']);
              $Qattributes->bindInt(':popt_language_id', $osC_Language->getID());
              $Qattributes->bindInt(':poval_language_id', $osC_Language->getID());
            }
            $Qattributes->execute();

            $Qopa = $osC_Database->query('insert into :table_orders_products_attributes (orders_id, orders_products_id, products_options, products_options_values, options_values_price, price_prefix) values (:orders_id, :orders_products_id, :products_options, :products_options_values, :options_values_price, :price_prefix)');
            $Qopa->bindTable(':table_orders_products_attributes', TABLE_ORDERS_PRODUCTS_ATTRIBUTES);
            $Qopa->bindInt(':orders_id', $insert_id);
            $Qopa->bindInt(':orders_products_id', $order_products_id);
            $Qopa->bindValue(':products_options', $Qattributes->value('products_options_name'));
            $Qopa->bindValue(':products_options_values', $Qattributes->value('products_options_values_name'));
            $Qopa->bindValue(':options_values_price', $Qattributes->value('options_values_price'));
            $Qopa->bindValue(':price_prefix', $Qattributes->value('price_prefix'));
            $Qopa->execute();

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

            $products_ordered_attributes .= "\n\t" . $Qattributes->value('products_options_name') . ' ' . $Qattributes->value('products_options_values_name');
          }
        }

        $total_weight += ($products['quantity'] * $products['weight']);
        $total_tax += tep_calculate_tax($products['final_price'], '' /*$products['tax']*/) * $products['quantity'];
        $total_cost += $products['final_price'];

        $products_ordered .= $products['quantity'] . ' x ' . $products['name'] . ' (' . '' /*$products['model']*/ . ') = ' . $osC_Currencies->displayPrice($products['final_price'], $products['tax_class_id'], $products['quantity']) . $products_ordered_attributes . "\n";
      }

// lets start with the email confirmation
      $email_order = STORE_NAME . "\n" .
                     $osC_Language->get('email_order_separator') . "\n" .
                     sprintf($osC_Language->get('email_order_order_number'), $insert_id) . "\n" .
                     sprintf($osC_Language->get('email_order_invoice_url'), osc_href_link(FILENAME_ACCOUNT, 'orders=' . $insert_id, 'SSL', false)) . "\n" .
                     sprintf($osC_Language->get('email_order_date_ordered'), osC_DateTime::getLong()) . "\n\n";
      if (isset($_SESSION['comments'])) {
        $email_order .= tep_output_string_protected($_SESSION['comments']) . "\n\n";
      }
      $email_order .= $osC_Language->get('email_order_products') . "\n" .
                      $osC_Language->get('email_order_separator') . "\n" .
                      $products_ordered .
                      $osC_Language->get('email_order_separator') . "\n";

//      if ($osC_OrderTotal->hasActive()) {
//        foreach ($osC_OrderTotal->getResult() as $module) {
        foreach ($osC_ShoppingCart->getOrderTotals() as $module) {
          $email_order .= strip_tags($module['title']) . ' ' . strip_tags($module['text']) . "\n";
        }
//      }

      if ($order->content_type != 'virtual') {
        $email_order .= "\n" . $osC_Language->get('email_order_delivery_address') . "\n" .
                        $osC_Language->get('email_order_separator') . "\n" .
                        tep_address_label($osC_Customer->getID(), $osC_ShoppingCart->getShippingAddress('id'), 0, '', "\n") . "\n";
      }

      $email_order .= "\n" . $osC_Language->get('email_order_billing_address') . "\n" .
                      $osC_Language->get('email_order_separator') . "\n" .
                      tep_address_label($osC_Customer->getID(), $osC_ShoppingCart->getBillingAddress('id'), 0, '', "\n") . "\n\n";

      if (is_object($GLOBALS[$payment])) {
        $email_order .= $osC_Language->get('email_order_payment_method') . "\n" .
                        $osC_Language->get('email_order_separator') . "\n";

        $email_order .= $osC_ShoppingCart->getBillingMethod('title') . "\n\n";
        if (isset($GLOBALS[$payment]->email_footer)) {
          $email_order .= $GLOBALS[$payment]->email_footer . "\n\n";
        }
      }

      tep_mail($osC_Customer->getName(), $osC_Customer->getEmailAddress(), $osC_Language->get('email_order_subject'), $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);

// send emails to other people
      if (SEND_EXTRA_ORDER_EMAILS_TO != '') {
        tep_mail('', SEND_EXTRA_ORDER_EMAILS_TO, $osC_Language->get('email_order_subject'), $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
      }

// load the after_process function from the payment modules
      $osC_Payment->after_process();

      $osC_ShoppingCart->reset(true);

// unregister session variables used during checkout
      unset($_SESSION['comments']);

      tep_redirect(osc_href_link(FILENAME_CHECKOUT, 'success', 'SSL'));
    }
  }
?>
