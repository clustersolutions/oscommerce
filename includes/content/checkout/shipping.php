<?php
/*
  $Id:shipping.php 188 2005-09-15 02:25:52 +0200 (Do, 15 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  require('includes/classes/http_client.php');

  class osC_Checkout_Shipping extends osC_Template {

/* Private variables */

    var $_module = 'shipping',
        $_group = 'checkout',
        $_page_title = HEADING_TITLE_CHECKOUT_SHIPPING,
        $_page_contents = 'checkout_shipping.php';

/* Class constructor */

    function osC_Checkout_Shipping() {
      global $osC_Database, $osC_Session, $osC_Customer, $osC_Services, $osC_NavigationHistory, $breadcrumb, $order, $total_weight, $total_count, $shipping_modules, $pass, $free_shipping, $quotes;

      if ($osC_Customer->isLoggedOn() === false) {
        $osC_NavigationHistory->setSnapshot();

        tep_redirect(tep_href_link(FILENAME_ACCOUNT, 'login', 'SSL'));
      }

      if ($_SESSION['cart']->count_contents() < 1) {
        tep_redirect(tep_href_link(FILENAME_CHECKOUT, '', 'SSL'));
      }

      if ($osC_Services->isStarted('breadcrumb')) {
        $breadcrumb->add(NAVBAR_TITLE_CHECKOUT_SHIPPING, tep_href_link(FILENAME_CHECKOUT, $this->_module, 'SSL'));
      }

      if ($osC_Customer->hasDefaultAddress() === false) {
        $this->_page_title = HEADING_TITLE_CHECKOUT_SHIPPING_ADDRESS;
        $this->_page_contents = 'checkout_shipping_address.php';

        $this->addJavascriptFilename('templates/' . $this->_template . '/javascript/checkout_shipping_address.js');
        $this->addJavascriptPhpFilename('includes/form_check.js.php');
      } else {
        $this->addJavascriptFilename('templates/' . $this->_template . '/javascript/checkout_shipping.js');
      }

// if no shipping destination address was selected, use the customers own address as default
      if (isset($_SESSION['sendto']) == false) {
        $_SESSION['sendto'] = $osC_Customer->getDefaultAddressID();
      } else {
// verify the selected shipping address
        $Qcheck = $osC_Database->query('select count(*) as total from :table_address_book where customers_id = :customers_id and address_book_id = :address_book_id');
        $Qcheck->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
        $Qcheck->bindInt(':customers_id', $osC_Customer->getID());
        $Qcheck->bindInt(':address_book_id', $_SESSION['sendto']);
        $Qcheck->execute();

        if ($Qcheck->valueInt('total') != 1) {
          $_SESSION['sendto'] = $osC_Customer->getDefaultAddressID();

          unset($_SESSION['shipping']);
        }
      }

      $order = new order;

// register a random ID in the session to check throughout the checkout procedure
// against alterations in the shopping cart contents
      $_SESSION['cartID'] = $_SESSION['cart']->cartID;

// if the order contains only virtual products, forward the customer to the billing page as
// a shipping address is not needed
    if ($order->content_type == 'virtual') {
      $false = false;

      $_SESSION['shipping'] = $false;
      $_SESSION['sendto'] = $false;

      tep_redirect(tep_href_link(FILENAME_CHECKOUT, 'payment', 'SSL'));
    }

    $total_weight = $_SESSION['cart']->show_weight();
    $total_count = $_SESSION['cart']->count_contents();

// load all enabled shipping modules
    require('includes/classes/shipping.php');
    $shipping_modules = new shipping;

    if (defined('MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING') && (MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING == 'true')) {
      $pass = false;

      switch (MODULE_ORDER_TOTAL_SHIPPING_DESTINATION) {
        case 'national':
          if ($order->delivery['country_id'] == STORE_COUNTRY) {
            $pass = true;
          }
          break;
        case 'international':
          if ($order->delivery['country_id'] != STORE_COUNTRY) {
            $pass = true;
          }
          break;
        case 'both':
          $pass = true;
          break;
      }

      $free_shipping = false;
      if ( ($pass == true) && ( ($order->info['total'] - $order->info['shipping_cost']) >= MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER) ) {
        $free_shipping = true;

        include('includes/languages/' . $_SESSION['language'] . '/modules/order_total/ot_shipping.php');
      }
    } else {
      $free_shipping = false;
    }

// get all available shipping quotes
    $quotes = $shipping_modules->quote();

// if no shipping method has been selected, automatically select the cheapest method.
// if the modules status was changed when none were available, to save on implementing
// a javascript force-selection method, also automatically select the cheapest shipping
// method if more than one module is now enabled
    if ( (isset($_SESSION['shipping']) == false) || (isset($_SESSION['shipping']) && ($_SESSION['shipping'] == false) && (tep_count_shipping_modules() > 1)) ) {
      $_SESSION['shipping'] = $shipping_modules->cheapest();
    }

    if ($_GET[$this->_module] == 'process') {
      $this->_process();
    }
  }

/* Private methods */

    function _process() {
      global $osC_Session, $free_shipping, $shipping_modules;

      if (tep_not_null($_POST['comments'])) {
        $_SESSION['comments'] = tep_sanitize_string($_POST['comments']);
      }

      if ( (tep_count_shipping_modules() > 0) || ($free_shipping == true) ) {
        if (isset($_POST['shipping_mod_sel']) && strpos($_POST['shipping_mod_sel'], '_')) {
          $_SESSION['shipping'] = $_POST['shipping_mod_sel'];

          list($module, $method) = explode('_', $_SESSION['shipping']);
          if (is_object($GLOBALS[$module]) || ($_SESSION['shipping'] == 'free_free')) {
            if ($_SESSION['shipping'] == 'free_free') {
              $quote[0]['methods'][0]['title'] = FREE_SHIPPING_TITLE;
              $quote[0]['methods'][0]['cost'] = '0';
            } else {
              $quote = $shipping_modules->quote($method, $module);
            }

            if (isset($quote['error'])) {
              unset($_SESSION['shipping']);
            } else {
              if (isset($quote[0]['methods'][0]['title']) && isset($quote[0]['methods'][0]['cost'])) {
                $shipping = array('id' => $_SESSION['shipping'],
                                  'title' => (($free_shipping == true) ?  $quote[0]['methods'][0]['title'] : $quote[0]['module'] . ' (' . $quote[0]['methods'][0]['title'] . ')'),
                                  'cost' => $quote[0]['methods'][0]['cost']);

                $_SESSION['shipping'] = $shipping;

                tep_redirect(tep_href_link(FILENAME_CHECKOUT, 'payment', 'SSL'));
              }
            }
          } else {
            unset($_SESSION['shipping']);
          }
        }
      } else {
        $_SESSION['shipping'] = false;

        tep_redirect(tep_href_link(FILENAME_CHECKOUT, 'payment', 'SSL'));
      }
    }
  }
?>
