<?php
/*
  $Id:confirmation.php 188 2005-09-15 02:25:52 +0200 (Do, 15 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  require('includes/classes/address_book.php');

  class osC_Checkout_Confirmation extends osC_Template {

/* Private variables */

    var $_module = 'confirmation',
        $_group = 'checkout',
        $_page_title,
        $_page_contents = 'checkout_confirmation.php',
        $_page_image = 'table_background_confirmation.gif';

/* Class constructor */

    function osC_Checkout_Confirmation() {
      global $osC_Session, $osC_Services, $osC_Language, $osC_ShoppingCart, $osC_Customer, $osC_MessageStack, $osC_NavigationHistory, $osC_Breadcrumb, $osC_Payment;

      if ($osC_Customer->isLoggedOn() === false) {
        $osC_NavigationHistory->setSnapshot();

        osc_redirect(osc_href_link(FILENAME_ACCOUNT, 'login', 'SSL'));
      }

      if ($osC_ShoppingCart->hasContents() === false) {
        osc_redirect(osc_href_link(FILENAME_CHECKOUT, null, 'SSL'));
      }

// if no shipping method has been selected, redirect the customer to the shipping method selection page
      if ($osC_ShoppingCart->hasShippingAddress() == false) {
        osc_redirect(osc_href_link(FILENAME_CHECKOUT, 'shipping', 'SSL'));
      }

      include('includes/classes/order.php');

      $this->_page_title = $osC_Language->get('confirmation_heading');

      $osC_Language->load('order');

      if ($osC_Services->isStarted('breadcrumb')) {
        $osC_Breadcrumb->add($osC_Language->get('breadcrumb_checkout_confirmation'), osc_href_link(FILENAME_CHECKOUT, $this->_module, 'SSL'));
      }

      if ( (isset($_POST['comments'])) && (isset($_SESSION['comments'])) && (empty($_POST['comments'])) ) {
        unset($_SESSION['comments']);
      } elseif (!empty($_POST['comments'])) {
        $_SESSION['comments'] = osc_sanitize_string($_POST['comments']);
      }

      if (DISPLAY_CONDITIONS_ON_CHECKOUT == '1') {
        if (!isset($_POST['conditions']) || ($_POST['conditions'] != '1')) {
          $osC_MessageStack->add('checkout_payment', $osC_Language->get('error_conditions_not_accepted'), 'error');
        }
      }

// load the selected payment module
      include('includes/classes/payment.php');
      $osC_Payment = new osC_Payment((isset($_POST['payment_method']) ? $_POST['payment_method'] : $osC_ShoppingCart->getBillingMethod('id')));

      if (isset($_POST['payment_method'])) {
        $osC_ShoppingCart->setBillingMethod(array('id' => $_POST['payment_method'], 'title' => $GLOBALS['osC_Payment_' . $_POST['payment_method']]->getMethodTitle()));
      }

      if ( $osC_Payment->hasActive() && ((isset($GLOBALS['osC_Payment_' . $osC_ShoppingCart->getBillingMethod('id')]) === false) || (isset($GLOBALS['osC_Payment_' . $osC_ShoppingCart->getBillingMethod('id')]) && is_object($GLOBALS['osC_Payment_' . $osC_ShoppingCart->getBillingMethod('id')]) && ($GLOBALS['osC_Payment_' . $osC_ShoppingCart->getBillingMethod('id')]->isEnabled() === false))) ) {
        $osC_MessageStack->add('checkout_payment', $osC_Language->get('error_no_payment_module_selected'), 'error');
      }

      if ($osC_MessageStack->size('checkout_payment') > 0) {
        osc_redirect(osc_href_link(FILENAME_CHECKOUT, 'payment', 'SSL'));
      }

      if ($osC_Payment->hasActive()) {
        $osC_Payment->pre_confirmation_check();
      }

// Stock Check
      if ( (STOCK_CHECK == '1') && (STOCK_ALLOW_CHECKOUT == '-1') ) {
        foreach ($osC_ShoppingCart->getProducts() as $product) {
          if (!$osC_ShoppingCart->isInStock($product['item_id'])) {
            osc_redirect(osc_href_link(FILENAME_CHECKOUT, null, 'AUTO'));
          }
        }
      }
    }
  }
?>
