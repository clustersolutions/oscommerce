<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  require('includes/classes/http_client.php');

  class osC_Checkout_Shipping {

/* Public variables */

    var $page_contents = 'checkout_shipping.php';

/* Private variables */

    var $_module = 'shipping';

/* Class constructor */

    function osC_Checkout_Shipping() {
      global $osC_Database, $osC_Session, $osC_Customer, $osC_Services, $breadcrumb, $order, $cart, $total_weight, $total_count, $shipping_modules, $pass, $free_shipping, $quotes;

      if ($cart->count_contents() < 1) {
        tep_redirect(tep_href_link(FILENAME_CHECKOUT, '', 'SSL'));
      }

      if ($osC_Services->isStarted('breadcrumb')) {
        $breadcrumb->add(NAVBAR_TITLE_CHECKOUT_SHIPPING, tep_href_link(FILENAME_CHECKOUT, $this->_module, 'SSL'));
      }

      if ($osC_Customer->hasDefaultAddress() === false) {
        $this->page_contents = 'shipping_address.php';
      }

// if no shipping destination address was selected, use the customers own address as default
      if ($osC_Session->exists('sendto') == false) {
        $osC_Session->set('sendto', $osC_Customer->default_address_id);
      } else {
// verify the selected shipping address
        $Qcheck = $osC_Database->query('select count(*) as total from :table_address_book where customers_id = :customers_id and address_book_id = :address_book_id');
        $Qcheck->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
        $Qcheck->bindInt(':customers_id', $osC_Customer->id);
        $Qcheck->bindInt(':address_book_id', $osC_Session->value('sendto'));
        $Qcheck->execute();

        if ($Qcheck->valueInt('total') != 1) {
          $osC_Session->set('sendto', $osC_Customer->default_address_id);

          $osC_Session->remove('shipping');
        }
      }

      $order = new order;

// register a random ID in the session to check throughout the checkout procedure
// against alterations in the shopping cart contents
      $osC_Session->set('cartID', $cart->cartID);

// if the order contains only virtual products, forward the customer to the billing page as
// a shipping address is not needed
    if ($order->content_type == 'virtual') {
      $false = false;

      $osC_Session->set('shipping', $false);
      $osC_Session->set('sendto', $false);

      tep_redirect(tep_href_link(FILENAME_CHECKOUT, 'payment', 'SSL'));
    }

    $total_weight = $cart->show_weight();
    $total_count = $cart->count_contents();

// load all enabled shipping modules
    require(DIR_WS_CLASSES . 'shipping.php');
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

        include(DIR_WS_LANGUAGES . $osC_Session->value('language') . '/modules/order_total/ot_shipping.php');
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
    if ( ($osC_Session->exists('shipping') == false) || ($osC_Session->exists('shipping') && ($osC_Session->value('shipping') == false) && (tep_count_shipping_modules() > 1)) ) {
      $osC_Session->set('shipping', $shipping_modules->cheapest());
    }

    if ($_GET[$this->_module] == 'process') {
      $this->_process();
    }
  }

/* Public methods */

    function getPageContentsFile() {
      return $this->page_contents;
    }

/* Private methods */

    function _process() {
      global $osC_Session, $free_shipping, $shipping_modules;

      if (tep_not_null($_POST['comments'])) {
        $osC_Session->set('comments', tep_sanitize_string($_POST['comments']));
      }

      if ( (tep_count_shipping_modules() > 0) || ($free_shipping == true) ) {
        if (isset($_POST['shipping']) && strpos($_POST['shipping'], '_')) {
          $osC_Session->set('shipping', $_POST['shipping']);

          list($module, $method) = explode('_', $osC_Session->value('shipping'));
          if (is_object($GLOBALS[$module]) || ($osC_Session->value('shipping') == 'free_free')) {
            if ($osC_Session->value('shipping') == 'free_free') {
              $quote[0]['methods'][0]['title'] = FREE_SHIPPING_TITLE;
              $quote[0]['methods'][0]['cost'] = '0';
            } else {
              $quote = $shipping_modules->quote($method, $module);
            }

            if (isset($quote['error'])) {
              $osC_Session->remove('shipping');
            } else {
              if (isset($quote[0]['methods'][0]['title']) && isset($quote[0]['methods'][0]['cost'])) {
                $shipping = array('id' => $osC_Session->value('shipping'),
                                  'title' => (($free_shipping == true) ?  $quote[0]['methods'][0]['title'] : $quote[0]['module'] . ' (' . $quote[0]['methods'][0]['title'] . ')'),
                                  'cost' => $quote[0]['methods'][0]['cost']);

                $osC_Session->set('shipping', $shipping);

                tep_redirect(tep_href_link(FILENAME_CHECKOUT, 'payment', 'SSL'));
              }
            }
          } else {
            $osC_Session->remove('shipping');
          }
        }
      } else {
        $osC_Session->set('shipping', false);

        tep_redirect(tep_href_link(FILENAME_CHECKOUT, 'payment', 'SSL'));
      }
    }
  }
?>
