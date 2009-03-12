<?php
/*
  $Id:payment.php 188 2005-09-15 02:25:52 +0200 (Do, 15 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  require('includes/classes/address_book.php');

  class osC_Checkout_Payment extends osC_Template {

/* Private variables */

    var $_module = 'payment',
        $_group = 'checkout',
        $_page_title,
        $_page_contents = 'checkout_payment.php',
        $_page_image = 'table_background_payment.gif';

/* Class constructor */

    function osC_Checkout_Payment() {
      global $osC_Database, $osC_Session, $osC_ShoppingCart, $osC_Customer, $osC_Services, $osC_Language, $osC_NavigationHistory, $osC_Breadcrumb, $osC_Payment;

      if ($osC_Customer->isLoggedOn() === false) {
        $osC_NavigationHistory->setSnapshot();

        osc_redirect(osc_href_link(FILENAME_ACCOUNT, 'login', 'SSL'));
      }

      if ($osC_ShoppingCart->hasContents() === false) {
        osc_redirect(osc_href_link(FILENAME_CHECKOUT, null, 'SSL'));
      }

// if no shipping method has been selected, redirect the customer to the shipping method selection page
      if ($osC_ShoppingCart->hasShippingMethod() === false) {
        osc_redirect(osc_href_link(FILENAME_CHECKOUT, 'shipping', 'SSL'));
      }

// Stock Check
      if ( (STOCK_CHECK == '1') && (STOCK_ALLOW_CHECKOUT == '-1') ) {
        foreach ($osC_ShoppingCart->getProducts() as $products) {
          if ($osC_ShoppingCart->isInStock($products['item_id']) === false) {
            osc_redirect(osc_href_link(FILENAME_CHECKOUT, 'SSL'));
            break;
          }
        }
      }

      $this->_page_title = $osC_Language->get('payment_method_heading');

      if ($osC_Services->isStarted('breadcrumb')) {
        $osC_Breadcrumb->add($osC_Language->get('breadcrumb_checkout_payment'), osc_href_link(FILENAME_CHECKOUT, $this->_module, 'SSL'));
      }

// redirect to the billing address page when no default address exists
      if ($osC_Customer->hasDefaultAddress() === false) {
        $this->_page_title = $osC_Language->get('payment_address_heading');
        $this->_page_contents = 'checkout_payment_address.php';

        $this->addJavascriptFilename('templates/' . $this->getCode() . '/javascript/checkout_payment_address.js');
        $this->addJavascriptPhpFilename('includes/form_check.js.php');
      } else {
        $this->addJavascriptFilename('templates/' . $this->getCode() . '/javascript/checkout_payment.js');

// if no billing destination address was selected, use the customers own address as default
        if ($osC_ShoppingCart->hasBillingAddress() == false) {
          $osC_ShoppingCart->setBillingAddress($osC_Customer->getDefaultAddressID());
        } else {
// verify the selected billing address
          $Qcheck = $osC_Database->query('select address_book_id from :table_address_book where address_book_id = :address_book_id and customers_id = :customers_id limit 1');
          $Qcheck->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
          $Qcheck->bindInt(':address_book_id', $osC_ShoppingCart->getBillingAddress('id'));
          $Qcheck->bindInt(':customers_id', $osC_Customer->getID());
          $Qcheck->execute();

          if ($Qcheck->numberOfRows() !== 1) {
            $osC_ShoppingCart->setBillingAddress($osC_Customer->getDefaultAddressID());
            $osC_ShoppingCart->resetBillingMethod();
          }
        }

// load all enabled payment modules
        include('includes/classes/payment.php');
        $osC_Payment = new osC_Payment();

        $this->addJavascriptBlock($osC_Payment->getJavascriptBlocks());
      }

      if (isset($_GET['payment_error']) && is_object(${$_GET['payment_error']}) && ($error = ${$_GET['payment_error']}->get_error())) {
        $osC_MessageStack->add('checkout_payment', $error['error'], 'error');
      }
    }
  }
?>
