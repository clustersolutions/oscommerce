<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class osC_Checkout_Confirmation {

/* Public variables */

    var $page_contents = 'checkout_confirmation.php';

/* Private variables */

    var $_module = 'confirmation';

/* Class constructor */

    function osC_Checkout_Confirmation() {
      global $osC_Session, $osC_Services, $messageStack, $breadcrumb, $order, $cart, $payment_modules, $shipping_modules, $order_total_modules, $any_out_of_stock;

      if ($cart->count_contents() < 1) {
        tep_redirect(tep_href_link(FILENAME_CHECKOUT, '', 'SSL'));
      }

// avoid hack attempts during the checkout procedure by checking the internal cartID
      if (isset($cart->cartID) && $osC_Session->exists('cartID')) {
        if ($cart->cartID != $osC_Session->value('cartID')) {
          tep_redirect(tep_href_link(FILENAME_CHECKOUT, 'shipping', 'SSL'));
        }
      }

// if no shipping method has been selected, redirect the customer to the shipping method selection page
      if ($osC_Session->exists('shipping') == false) {
        tep_redirect(tep_href_link(FILENAME_CHECKOUT, 'shipping', 'SSL'));
      }

      if ($osC_Services->isStarted('breadcrumb')) {
        $breadcrumb->add(NAVBAR_TITLE_CHECKOUT_CONFIRMATION, tep_href_link(FILENAME_CHECKOUT, $this->_module, 'SSL'));
      }

      if (isset($_POST['payment'])) {
        $osC_Session->set('payment', $_POST['payment']);
      }
      $payment =& $osC_Session->value('payment');

      if ( (isset($_POST['comments'])) && ($osC_Session->exists('comments')) && (empty($_POST['comments'])) ) {
        $osC_Session->remove('comments');
      } else if (tep_not_null($_POST['comments'])) {
        $osC_Session->set('comments', tep_sanitize_string($_POST['comments']));
      }

      if (DISPLAY_CONDITIONS_ON_CHECKOUT == 'true') {
        if (!isset($_POST['conditions']) || ($_POST['conditions'] != '1')) {
          $messageStack->add_session('checkout_payment', ERROR_CONDITIONS_NOT_ACCEPTED, 'error');
        }
      }

// load the selected payment module
      require(DIR_WS_CLASSES . 'payment.php');
      $payment_modules = new payment($osC_Session->value('payment'));

      $order = new order;

      $payment_modules->update_status();

      if ( (is_array($payment_modules->modules) && (sizeof($payment_modules->modules) > 1) && !isset($GLOBALS[$payment])) || (isset($GLOBALS[$payment]) && is_object($GLOBALS[$payment]) && ($GLOBALS[$payment]->enabled == false)) ) {
        $messageStack->add_session('checkout_payment', ERROR_NO_PAYMENT_MODULE_SELECTED, 'error');
      }

      if ($messageStack->size('checkout_payment') > 0) {
        tep_redirect(tep_href_link(FILENAME_CHECKOUT, 'payment', 'SSL'));
      }

      if (is_array($payment_modules->modules)) {
        $payment_modules->pre_confirmation_check();
      }

// load the selected shipping module
      require(DIR_WS_CLASSES . 'shipping.php');
      $shipping_modules = new shipping($osC_Session->value('shipping'));

      require(DIR_WS_CLASSES . 'order_total.php');
      $order_total_modules = new order_total;

// Stock Check
      $any_out_of_stock = false;
      if (STOCK_CHECK == 'true') {
        for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
          if (tep_check_stock($order->products[$i]['id'], $order->products[$i]['qty'])) {
            $any_out_of_stock = true;
          }
        }
// Out of Stock
        if ( (STOCK_ALLOW_CHECKOUT != 'true') && ($any_out_of_stock == true) ) {
          tep_redirect(tep_href_link(FILENAME_CHECKOUT));
        }
      }
    }

/* Public methods */

    function getPageContentsFile() {
      return $this->page_contents;
    }

/* Private methods */

    function _process() {
    }
  }
?>
