<?php
/*
  $Id:confirmation.php 188 2005-09-15 02:25:52 +0200 (Do, 15 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class osC_Checkout_Confirmation extends osC_Template {

/* Private variables */

    var $_module = 'confirmation',
        $_group = 'checkout',
        $_page_title = HEADING_TITLE_CHECKOUT_CONFIRMATION,
        $_page_contents = 'checkout_confirmation.php';

/* Class constructor */

    function osC_Checkout_Confirmation() {
      global $osC_Session, $osC_Services, $osC_Customer, $messageStack, $osC_NavigationHistory, $breadcrumb, $order, $payment_modules, $shipping_modules, $order_total_modules, $any_out_of_stock;

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
      if (isset($_SESSION['shipping']) == false) {
        tep_redirect(tep_href_link(FILENAME_CHECKOUT, 'shipping', 'SSL'));
      }

      if ($osC_Services->isStarted('breadcrumb')) {
        $breadcrumb->add(NAVBAR_TITLE_CHECKOUT_CONFIRMATION, tep_href_link(FILENAME_CHECKOUT, $this->_module, 'SSL'));
      }

      if (isset($_POST['payment_mod_sel'])) {
        $_SESSION['payment'] = $_POST['payment_mod_sel'];
      }
      $payment =& $_SESSION['payment'];

      if ( (isset($_POST['comments'])) && (isset($_SESSION['comments'])) && (empty($_POST['comments'])) ) {
        unset($_SESSION['comments']);
      } else if (tep_not_null($_POST['comments'])) {
        $_SESSION['comments'] = tep_sanitize_string($_POST['comments']);
      }

      if (DISPLAY_CONDITIONS_ON_CHECKOUT == 'true') {
        if (!isset($_POST['conditions']) || ($_POST['conditions'] != '1')) {
          $messageStack->add_session('checkout_payment', ERROR_CONDITIONS_NOT_ACCEPTED, 'error');
        }
      }

// load the selected payment module
      require('includes/classes/payment.php');
      $payment_modules = new payment($_SESSION['payment']);

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
      require('includes/classes/shipping.php');
      $shipping_modules = new shipping($_SESSION['shipping']);

      require('includes/classes/order_total.php');
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
          tep_redirect(tep_href_link(FILENAME_CHECKOUT, '', 'AUTO'));
        }
      }
    }
  }
?>
