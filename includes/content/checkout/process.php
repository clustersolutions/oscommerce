<?php
/*
  $Id:process.php 188 2005-09-15 02:25:52 +0200 (Do, 15 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class osC_Checkout_Process extends osC_Template {

/* Private variables */

    var $_module = 'process';

/* Class constructor */

    function osC_Checkout_Process() {
      global $osC_Session, $osC_Customer, $osC_NavigationHistory;

      if ($osC_Customer->isLoggedOn() === false) {
        $osC_NavigationHistory->setSnapshot();

        tep_redirect(tep_href_link(FILENAME_ACCOUNT, 'login', 'SSL'));
      }

      if ($_SESSION['cart']->count_contents() < 1) {
        tep_redirect(tep_href_link(FILENAME_CHECKOUT, '', 'SSL'));
      }

// avoid hack attempts during the checkout procedure by checking the internal cartID
      if (isset($_SESSION['cart']->cartID) && isset($_SESSION['cartID'])) {
        if ($_SESSION['cart']->cartID != $_SESSION['cartID']) {
          tep_redirect(tep_href_link(FILENAME_CHECKOUT, 'shipping', 'SSL'));
        }
      }

// if no shipping method has been selected, redirect the customer to the shipping method selection page
      if (isset($_SESSION['sendto']) == false) {
        tep_redirect(tep_href_link(FILENAME_CHECKOUT, 'shipping', 'SSL'));
      }

      if (tep_not_null(MODULE_PAYMENT_INSTALLED) && (isset($_SESSION['payment']) == false)) {
        tep_redirect(tep_href_link(FILENAME_CHECKOUT, 'payment', 'SSL'));
      }

      $this->_process();
    }

/* Private methods */

    function _process() {
      global $osC_Database, $osC_Session, $osC_Customer, $osC_Currencies, $order, $payment_modules, $shipping_modules, $order_total_modules;

// load selected payment module
      require('includes/classes/payment.php');
      $payment_modules = new payment($_SESSION['payment']);

// load the selected shipping module
      require('includes/classes/shipping.php');
      $shipping_modules = new shipping($_SESSION['shipping']);

      $order = new order;

// load the before_process function from the payment modules
      $payment_modules->before_process();

      require('includes/classes/order_total.php');
      $order_total_modules = new order_total;

      $order_totals = $order_total_modules->process();

      $Qorder = $osC_Database->query('insert into :table_orders (customers_id, customers_name, customers_company, customers_street_address, customers_suburb, customers_city, customers_postcode, customers_state, customers_country, customers_telephone, customers_email_address, customers_address_format_id, customers_ip_address, delivery_name, delivery_company, delivery_street_address, delivery_suburb, delivery_city, delivery_postcode, delivery_state, delivery_country, delivery_address_format_id, billing_name, billing_company, billing_street_address, billing_suburb, billing_city, billing_postcode, billing_state, billing_country, billing_address_format_id, payment_method, cc_type, cc_owner, cc_number, cc_expires, date_purchased, orders_status, currency, currency_value) values (:customers_id, :customers_name, :customers_company, :customers_street_address, :customers_suburb, :customers_city, :customers_postcode, :customers_state, :customers_country, :customers_telephone, :customers_email_address, :customers_address_format_id, :customers_ip_address, :delivery_name, :delivery_company, :delivery_street_address, :delivery_suburb, :delivery_city, :delivery_postcode, :delivery_state, :delivery_country, :delivery_address_format_id, :billing_name, :billing_company, :billing_street_address, :billing_suburb, :billing_city, :billing_postcode, :billing_state, :billing_country, :billing_address_format_id, :payment_method, :cc_type, :cc_owner, :cc_number, :cc_expires, :date_purchased, :orders_status, :currency, :currency_value)');
      $Qorder->bindTable(':table_orders', TABLE_ORDERS);
      $Qorder->bindInt(':customers_id', $osC_Customer->getID());
      $Qorder->bindValue(':customers_name', $order->customer['firstname'] . ' ' . $order->customer['lastname']);
      $Qorder->bindValue(':customers_company', $order->customer['company']);
      $Qorder->bindValue(':customers_street_address', $order->customer['street_address']);
      $Qorder->bindValue(':customers_suburb', $order->customer['suburb']);
      $Qorder->bindValue(':customers_city', $order->customer['city']);
      $Qorder->bindValue(':customers_postcode', $order->customer['postcode']);
      $Qorder->bindValue(':customers_state', $order->customer['state']);
      $Qorder->bindValue(':customers_country', $order->customer['country']['title']);
      $Qorder->bindValue(':customers_telephone', $order->customer['telephone']);
      $Qorder->bindValue(':customers_email_address', $order->customer['email_address']);
      $Qorder->bindInt(':customers_address_format_id', $order->customer['format_id']);
      $Qorder->bindValue(':customers_ip_address', tep_get_ip_address());
      $Qorder->bindValue(':delivery_name', $order->delivery['firstname'] . ' ' . $order->delivery['lastname']);
      $Qorder->bindValue(':delivery_company', $order->delivery['company']);
      $Qorder->bindValue(':delivery_street_address', $order->delivery['street_address']);
      $Qorder->bindValue(':delivery_suburb', $order->delivery['suburb']);
      $Qorder->bindValue(':delivery_city', $order->delivery['city']);
      $Qorder->bindValue(':delivery_postcode', $order->delivery['postcode']);
      $Qorder->bindValue(':delivery_state', $order->delivery['state']);
      $Qorder->bindValue(':delivery_country', $order->delivery['country']['title']);
      $Qorder->bindInt(':delivery_address_format_id', $order->delivery['format_id']);
      $Qorder->bindValue(':billing_name', $order->billing['firstname'] . ' ' . $order->billing['lastname']);
      $Qorder->bindValue(':billing_company', $order->billing['company']);
      $Qorder->bindValue(':billing_street_address', $order->billing['street_address']);
      $Qorder->bindValue(':billing_suburb', $order->billing['suburb']);
      $Qorder->bindValue(':billing_city', $order->billing['city']);
      $Qorder->bindValue(':billing_postcode', $order->billing['postcode']);
      $Qorder->bindValue(':billing_state', $order->billing['state']);
      $Qorder->bindValue(':billing_country', $order->billing['country']['title']);
      $Qorder->bindInt(':billing_address_format_id', $order->billing['format_id']);
      $Qorder->bindValue(':payment_method', $order->info['payment_method']);
      $Qorder->bindValue(':cc_type', $order->info['cc_type']);
      $Qorder->bindValue(':cc_owner', $order->info['cc_owner']);
      $Qorder->bindValue(':cc_number', $order->info['cc_number']);
      $Qorder->bindValue(':cc_expires', $order->info['cc_expires']);
      $Qorder->bindRaw(':date_purchased', 'now()');
      $Qorder->bindValue(':orders_status', $order->info['order_status']);
      $Qorder->bindValue(':currency', $order->info['currency']);
      $Qorder->bindValue(':currency_value', $order->info['currency_value']);
      $Qorder->execute();

      $insert_id = $osC_Database->nextID();

      for ($i=0, $n=sizeof($order_totals); $i<$n; $i++) {
        $Qtotals = $osC_Database->query('insert into :table_orders_total (orders_id, title, text, value, class, sort_order) values (:orders_id, :title, :text, :value, :class, :sort_order)');
        $Qtotals->bindTable(':table_orders_total', TABLE_ORDERS_TOTAL);
        $Qtotals->bindInt(':orders_id', $insert_id);
        $Qtotals->bindValue(':title', $order_totals[$i]['title']);
        $Qtotals->bindValue(':text', $order_totals[$i]['text']);
        $Qtotals->bindValue(':value', $order_totals[$i]['value']);
        $Qtotals->bindValue(':class', $order_totals[$i]['code']);
        $Qtotals->bindInt(':sort_order', $order_totals[$i]['sort_order']);
        $Qtotals->execute();
      }

      $Qstatus = $osC_Database->query('insert into :table_orders_status_history (orders_id, orders_status_id, date_added, customer_notified, comments) values (:orders_id, :orders_status_id, :date_added, :customer_notified, :comments)');
      $Qstatus->bindTable(':table_orders_status_history', TABLE_ORDERS_STATUS_HISTORY);
      $Qstatus->bindInt(':orders_id', $insert_id);
      $Qstatus->bindInt(':orders_status_id', $order->info['order_status']);
      $Qstatus->bindRaw(':date_added', 'now()');
      $Qstatus->bindInt(':customer_notified', (SEND_EMAILS == 'true') ? '1' : '0');
      $Qstatus->bindValue(':comments', $order->info['comments']);
      $Qstatus->execute();

// initialized for the email confirmation
      $products_ordered = '';
      $subtotal = 0;
      $total_tax = 0;
      $total_weight = 0;
      $total_cost = 0;

      for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
// Stock Update - Joao Correia
        if (STOCK_LIMITED == 'true') {
          if (DOWNLOAD_ENABLED == 'true') {
            $Qstock = $osC_Database->query('select products_quantity, pad.products_attributes_filename from :table_products p left join :table_products_attributes pa on (p.products_id = pa.products_id) left join :table_products_attributes_download pad on (pa.products_attributes_id = pad.products_attributes_id) where p.products_id = :products_id');
            $Qstock->bindTable(':table_products', TABLE_PRODUCTS);
            $Qstock->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
            $Qstock->bindTable(':table_products_attributes_download', TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD);
            $Qstock->bindInt(':products_id', tep_get_prid($order->products[$i]['id']));

// Will work with only one option for downloadable products otherwise, we have to build the query dynamically with a loop
            $products_attributes = $order->products[$i]['attributes'];

            if (is_array($products_attributes)) {
              $Qstock->appendQuery('and pa.options_id = :options_id and pa.options_values_id = :options_values_id');
              $Qstock->bindInt(':options_id', $products_attributes[0]['option_id']);
              $Qstock->bindInt(':options_values_id', $products_attributes[0]['value_id']);
            }
          } else {
            $Qstock = $osC_Database->query('select products_quantity from :table_products where products_id = :products_id');
            $Qstock->bindTable(':table_products', TABLE_PRODUCTS);
            $Qstock->bindInt(':products_id', tep_get_prid($order->products[$i]['id']));
          }

          $Qstock->execute();

          if ($Qstock->numberOfRows() > 0) {
            $stock_left = $Qstock->valueInt('products_quantity');

// do not decrement quantities if products_attributes_filename exists
            if ((DOWNLOAD_ENABLED != 'true') || ((DOWNLOAD_ENABLED == 'true') && (strlen($Qstock->value('products_attributes_filename')) < 1))) {
              $stock_left = $stock_left - $order->products[$i]['qty'];

              $Qupdate = $osC_Database->query('update :table_products set products_quantity = :products_quantity where products_id = :products_id');
              $Qupdate->bindTable(':table_products', TABLE_PRODUCTS);
              $Qupdate->bindInt(':products_quantity', $stock_left);
              $Qupdate->bindInt(':products_id', tep_get_prid($order->products[$i]['id']));
              $Qupdate->execute();
            }

            if ((STOCK_ALLOW_CHECKOUT == 'false') && ($stock_left < 1)) {
              $Qupdate = $osC_Database->query('update :table_products set products_status = :products_status where products_id = :products_id');
              $Qupdate->bindTable(':table_products', TABLE_PRODUCTS);
              $Qupdate->bindInt(':products_status', 0);
              $Qupdate->bindInt(':products_id', tep_get_prid($order->products[$i]['id']));
              $Qupdate->execute();
            }
          }
        }

// Update products_ordered (for bestsellers list)
        $Qupdate = $osC_Database->query('update :table_products set products_ordered = products_ordered + :products_ordered where products_id = :products_id');
        $Qupdate->bindTable(':table_products', TABLE_PRODUCTS);
        $Qupdate->bindInt(':products_ordered', $order->products[$i]['qty']);
        $Qupdate->bindInt(':products_id', tep_get_prid($order->products[$i]['id']));
        $Qupdate->execute();

        $Qproducts = $osC_Database->query('insert into :table_orders_products (orders_id, products_id, products_model, products_name, products_price, final_price, products_tax, products_quantity) values (:orders_id, :products_id, :products_model, :products_name, :products_price, :final_price, :products_tax, :products_quantity)');
        $Qproducts->bindTable(':table_orders_products', TABLE_ORDERS_PRODUCTS);
        $Qproducts->bindInt(':orders_id', $insert_id);
        $Qproducts->bindInt(':products_id', tep_get_prid($order->products[$i]['id']));
        $Qproducts->bindValue(':products_model', $order->products[$i]['model']);
        $Qproducts->bindValue(':products_name', $order->products[$i]['name']);
        $Qproducts->bindValue(':products_price', $order->products[$i]['price']);
        $Qproducts->bindValue(':final_price', $order->products[$i]['final_price']);
        $Qproducts->bindValue(':products_tax', $order->products[$i]['tax']);
        $Qproducts->bindInt(':products_quantity', $order->products[$i]['qty']);
        $Qproducts->execute();

        $order_products_id = $osC_Database->nextID();

//------insert customer choosen option to order--------
        $attributes_exist = '0';
        $products_ordered_attributes = '';

        if (isset($order->products[$i]['attributes'])) {
          $attributes_exist = '1';

          for ($j=0, $n2=sizeof($order->products[$i]['attributes']); $j<$n2; $j++) {
            if (DOWNLOAD_ENABLED == 'true') {
              $Qattributes = $osC_Database->query('select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix, pad.products_attributes_maxdays, pad.products_attributes_maxcount, pad.products_attributes_filename from :table_products_options popt, :table_products_options_values poval, :table_products_attributes pa left join :table_products_attributes_download pad on (pa.products_attributes_id = pad.products_attributes_id) where pa.products_id = :products_id and pa.options_id = :options_id and pa.options_id = popt.products_options_id and pa.options_values_id = :options_values_id and pa.options_values_id = poval.products_options_values_id and popt.language_id = :popt_language_id and poval.language_id = :poval_language_id');
              $Qattributes->bindTable(':table_products_options', TABLE_PRODUCTS_OPTIONS);
              $Qattributes->bindTable(':table_products_options_values', TABLE_PRODUCTS_OPTIONS_VALUES);
              $Qattributes->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
              $Qattributes->bindTable(':table_products_attributes_download', TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD);
              $Qattributes->bindInt(':products_id', $order->products[$i]['id']);
              $Qattributes->bindInt(':options_id', $order->products[$i]['attributes'][$j]['option_id']);
              $Qattributes->bindInt(':options_values_id', $order->products[$i]['attributes'][$j]['value_id']);
              $Qattributes->bindInt(':popt_language_id', $_SESSION['languages_id']);
              $Qattributes->bindInt(':poval_language_id', $_SESSION['languages_id']);
            } else {
              $Qattributes = $osC_Database->query('select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix from :table_products_options popt, :table_products_options_values poval, :table_products_attributes pa where pa.products_id = :products_id and pa.options_id = :options_id and pa.options_id = popt.products_options_id and pa.options_values_id = :options_values_id and pa.options_values_id = poval.products_options_values_id and popt.language_id = :popt_language_id and poval.language_id = :poval_language_id');
              $Qattributes->bindTable(':table_products_options', TABLE_PRODUCTS_OPTIONS);
              $Qattributes->bindTable(':table_products_options_values', TABLE_PRODUCTS_OPTIONS_VALUES);
              $Qattributes->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
              $Qattributes->bindInt(':products_id', $order->products[$i]['id']);
              $Qattributes->bindInt(':options_id', $order->products[$i]['attributes'][$j]['option_id']);
              $Qattributes->bindInt(':options_values_id', $order->products[$i]['attributes'][$j]['value_id']);
              $Qattributes->bindInt(':popt_language_id', $_SESSION['languages_id']);
              $Qattributes->bindInt(':poval_language_id', $_SESSION['languages_id']);
            }
            $Qattributes->execute();

            $Qopa = $osC_Database->query('insert into :table_orders_products_attributes (orders_id, orders_products_id, products_options, products_options_values, options_values_price, price_prefix) values (:orders_id, :orders_products_id, :products_options, :products_options_values, :options_values_price, :price_prefix)');
            $Qopa->bindTable(':table_orders_products_attributes', TABLE_ORDERS_PRODUCTS_ATTRIBUTES);
            $Qopa->bindInt(':orders_id', $insert_id);
            $Qopa->bindInt(':orders_products_id', $order_products_id);
            $Qopa->bindValue(':products_options', $attributes_values['products_options_name']);
            $Qopa->bindValue(':products_options_values', $attributes_values['products_options_values_name']);
            $Qopa->bindValue(':options_values_price', $attributes_values['options_values_price']);
            $Qopa->bindValue(':price_prefix', $attributes_values['price_prefix']);
            $Qopa->execute();

            if ((DOWNLOAD_ENABLED == 'true') && (strlen($Qattributes->value('products_attributes_filename')) > 0)) {
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
//------insert customer choosen option eof ----

        $total_weight += ($order->products[$i]['qty'] * $order->products[$i]['weight']);
        $total_tax += tep_calculate_tax($order->products[$i]['final_price'], $order->products[$i]['tax']) * $order->products[$i]['qty'];
        $total_cost += $order->products[$i]['final_price'];

        $products_ordered .= $order->products[$i]['qty'] . ' x ' . $order->products[$i]['name'] . ' (' . $order->products[$i]['model'] . ') = ' . $osC_Currencies->displayPrice($order->products[$i]['final_price'], $order->products[$i]['tax_class_id'], $order->products[$i]['qty']) . $products_ordered_attributes . "\n";
      }

// lets start with the email confirmation
      $email_order = STORE_NAME . "\n" .
                     EMAIL_SEPARATOR . "\n" .
                     EMAIL_TEXT_ORDER_NUMBER . ' ' . $insert_id . "\n" .
                     EMAIL_TEXT_INVOICE_URL . ' ' . tep_href_link(FILENAME_ACCOUNT, 'orders=' . $insert_id, 'SSL', false) . "\n" .
                     EMAIL_TEXT_DATE_ORDERED . ' ' . strftime(DATE_FORMAT_LONG) . "\n\n";
      if ($order->info['comments']) {
        $email_order .= tep_output_string_protected($order->info['comments']) . "\n\n";
      }
      $email_order .= EMAIL_TEXT_PRODUCTS . "\n" .
                      EMAIL_SEPARATOR . "\n" .
                      $products_ordered .
                      EMAIL_SEPARATOR . "\n";

      for ($i=0, $n=sizeof($order_totals); $i<$n; $i++) {
        $email_order .= strip_tags($order_totals[$i]['title']) . ' ' . strip_tags($order_totals[$i]['text']) . "\n";
      }

      if ($order->content_type != 'virtual') {
        $email_order .= "\n" . EMAIL_TEXT_DELIVERY_ADDRESS . "\n" .
                        EMAIL_SEPARATOR . "\n" .
                        tep_address_label($osC_Customer->getID(), $_SESSION['sendto'], 0, '', "\n") . "\n";
      }

      $email_order .= "\n" . EMAIL_TEXT_BILLING_ADDRESS . "\n" .
                      EMAIL_SEPARATOR . "\n" .
                      tep_address_label($osC_Customer->getID(), $_SESSION['billto'], 0, '', "\n") . "\n\n";

      $payment =& $_SESSION['payment'];

      if (is_object($$payment)) {
        $email_order .= EMAIL_TEXT_PAYMENT_METHOD . "\n" .
                        EMAIL_SEPARATOR . "\n";
        $payment_class = $$payment;
        $email_order .= $payment_class->title . "\n\n";
        if (isset($payment_class->email_footer)) {
          $email_order .= $payment_class->email_footer . "\n\n";
        }
      }

      tep_mail($order->customer['firstname'] . ' ' . $order->customer['lastname'], $order->customer['email_address'], EMAIL_TEXT_SUBJECT, $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);

// send emails to other people
      if (SEND_EXTRA_ORDER_EMAILS_TO != '') {
        tep_mail('', SEND_EXTRA_ORDER_EMAILS_TO, EMAIL_TEXT_SUBJECT, $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
      }

// load the after_process function from the payment modules
      $payment_modules->after_process();

      $_SESSION['cart']->reset(true);

// unregister session variables used during checkout
      unset($_SESSION['sendto']);
      unset($_SESSION['billto']);
      unset($_SESSION['shipping']);
      unset($_SESSION['payment']);
      unset($_SESSION['comments']);

      tep_redirect(tep_href_link(FILENAME_CHECKOUT, 'success', 'SSL'));
    }
  }
?>
