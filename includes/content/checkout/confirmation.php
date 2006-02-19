<?php
/*
  $Id:confirmation.php 188 2005-09-15 02:25:52 +0200 (Do, 15 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Checkout_Confirmation extends osC_Template {

/* Private variables */

    var $_module = 'confirmation',
        $_group = 'checkout',
        $_page_title,
        $_page_contents = 'checkout_confirmation.php';

/* Class constructor */

    function osC_Checkout_Confirmation() {
      global $osC_Session, $osC_Services, $osC_Language, $osC_ShoppingCart, $osC_Customer, $messageStack, $osC_NavigationHistory, $breadcrumb, $osC_Payment;

      if ($osC_Customer->isLoggedOn() === false) {
        $osC_NavigationHistory->setSnapshot();

        tep_redirect(tep_href_link(FILENAME_ACCOUNT, 'login', 'SSL'));
      }

      if ($osC_ShoppingCart->hasContents() === false) {
        tep_redirect(tep_href_link(FILENAME_CHECKOUT, '', 'SSL'));
      }

// if no shipping method has been selected, redirect the customer to the shipping method selection page
      if ($osC_ShoppingCart->hasShippingAddress() == false) {
        tep_redirect(tep_href_link(FILENAME_CHECKOUT, 'shipping', 'SSL'));
      }

      $this->_page_title = $osC_Language->get('confirmation_heading');

      $osC_Language->load('order');

      if ($osC_Services->isStarted('breadcrumb')) {
        $breadcrumb->add($osC_Language->get('breadcrumb_checkout_confirmation'), tep_href_link(FILENAME_CHECKOUT, $this->_module, 'SSL'));
      }

      if ( (isset($_POST['comments'])) && (isset($_SESSION['comments'])) && (empty($_POST['comments'])) ) {
        unset($_SESSION['comments']);
      } else if (tep_not_null($_POST['comments'])) {
        $_SESSION['comments'] = tep_sanitize_string($_POST['comments']);
      }

      if (DISPLAY_CONDITIONS_ON_CHECKOUT == 'true') {
        if (!isset($_POST['conditions']) || ($_POST['conditions'] != '1')) {
          $messageStack->add_session('checkout_payment', $osC_Language->get('error_conditions_not_accepted'), 'error');
        }
      }

// load the selected payment module
      include('includes/classes/payment.php');
      $osC_Payment = new osC_Payment($_POST['payment_mod_sel']);
      $osC_Payment->update_status();

      if (isset($_POST['payment_mod_sel'])) {
        $osC_ShoppingCart->setBillingMethod(array('id' => $_POST['payment_mod_sel'], 'title' => $GLOBALS['osC_Payment_' . $_POST['payment_mod_sel']]->getTitle()));
      }

      if ( $osC_Payment->hasActive() && ((isset($GLOBALS['osC_Payment_' . $osC_ShoppingCart->getBillingMethod('id')]) === false) || (isset($GLOBALS['osC_Payment_' . $osC_ShoppingCart->getBillingMethod('id')]) && is_object($GLOBALS['osC_Payment_' . $osC_ShoppingCart->getBillingMethod('id')]) && ($GLOBALS['osC_Payment_' . $osC_ShoppingCart->getBillingMethod('id')]->getStatus() === false))) ) {
        $messageStack->add_session('checkout_payment', $osC_Language->get('error_no_payment_module_selected'), 'error');
      }

      if ($messageStack->size('checkout_payment') > 0) {
        tep_redirect(tep_href_link(FILENAME_CHECKOUT, 'payment', 'SSL'));
      }

      if ($osC_Payment->hasActive()) {
        $osC_Payment->pre_confirmation_check();
      }

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
