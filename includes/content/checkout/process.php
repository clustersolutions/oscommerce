<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  require('includes/classes/address_book.php');

  class osC_Checkout_Process extends osC_Template {

/* Private variables */

    var $_module = 'process';

/* Class constructor */

    function osC_Checkout_Process() {
      global $osC_Session, $osC_ShoppingCart, $osC_Customer, $osC_NavigationHistory, $osC_Payment;

      if ($osC_Customer->isLoggedOn() === false) {
        $osC_NavigationHistory->setSnapshot();

        osc_redirect(osc_href_link(FILENAME_ACCOUNT, 'login', 'SSL'));
      }

      if ($osC_ShoppingCart->hasContents() === false) {
        osc_redirect(osc_href_link(FILENAME_CHECKOUT, null, 'SSL'));
      }

// if no shipping method has been selected, redirect the customer to the shipping method selection page
      if (($osC_ShoppingCart->hasShippingMethod() === false) && ($osC_ShoppingCart->getContentType() != 'virtual')) {
        osc_redirect(osc_href_link(FILENAME_CHECKOUT, 'shipping', 'SSL'));
      }

// load selected payment module
      include('includes/classes/payment.php');
      $osC_Payment = new osC_Payment($osC_ShoppingCart->getBillingMethod('id'));

      if ($osC_Payment->hasActive() && ($osC_ShoppingCart->hasBillingMethod() === false)) {
        osc_redirect(osc_href_link(FILENAME_CHECKOUT, 'payment', 'SSL'));
      }

      include('includes/classes/order.php');

      $osC_Payment->process();

      $osC_ShoppingCart->reset(true);

// unregister session variables used during checkout
      unset($_SESSION['comments']);

      osc_redirect(osc_href_link(FILENAME_CHECKOUT, 'success', 'SSL'));
    }
  }
?>
