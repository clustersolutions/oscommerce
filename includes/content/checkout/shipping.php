<?php
/*
  $Id:shipping.php 188 2005-09-15 02:25:52 +0200 (Do, 15 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  require('includes/classes/address_book.php');

  class osC_Checkout_Shipping extends osC_Template {

/* Private variables */

    var $_module = 'shipping',
        $_group = 'checkout',
        $_page_title,
        $_page_contents = 'checkout_shipping.php',
        $_page_image = 'table_background_delivery.gif';

/* Class constructor */

    function osC_Checkout_Shipping() {
      global $osC_Database, $osC_ShoppingCart, $osC_Customer, $osC_Services, $osC_Language, $osC_NavigationHistory, $osC_Breadcrumb, $osC_Shipping;

      if ($osC_Customer->isLoggedOn() === false) {
        $osC_NavigationHistory->setSnapshot();

        osc_redirect(osc_href_link(FILENAME_ACCOUNT, 'login', 'SSL'));
      }

      if ($osC_ShoppingCart->hasContents() === false) {
        osc_redirect(osc_href_link(FILENAME_CHECKOUT, null, 'SSL'));
      }

// if the order contains only virtual products, forward the customer to the billing page as
// a shipping address is not needed
      if ($osC_ShoppingCart->getContentType() == 'virtual') {
        osc_redirect(osc_href_link(FILENAME_CHECKOUT, 'payment', 'SSL'));
      }

      $this->_page_title = $osC_Language->get('shipping_method_heading');

      if ($osC_Services->isStarted('breadcrumb')) {
        $osC_Breadcrumb->add($osC_Language->get('breadcrumb_checkout_shipping'), osc_href_link(FILENAME_CHECKOUT, $this->_module, 'SSL'));
      }

      if ($osC_Customer->hasDefaultAddress() === false) {
        $this->_page_title = $osC_Language->get('shipping_address_heading');
        $this->_page_contents = 'checkout_shipping_address.php';

        $this->addJavascriptFilename('templates/' . $this->getCode() . '/javascript/checkout_shipping_address.js');
        $this->addJavascriptPhpFilename('includes/form_check.js.php');
      } else {
        $this->addJavascriptFilename('templates/' . $this->getCode() . '/javascript/checkout_shipping.js');

// if no shipping destination address was selected, use the customers own address as default
        if ($osC_ShoppingCart->hasShippingAddress() === false) {
          $osC_ShoppingCart->setShippingAddress($osC_Customer->getDefaultAddressID());
        } else {
// verify the selected shipping address
          $Qcheck = $osC_Database->query('select address_book_id from :table_address_book where address_book_id = :address_book_id and customers_id = :customers_id limit 1');
          $Qcheck->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
          $Qcheck->bindInt(':address_book_id', $osC_ShoppingCart->getShippingAddress('id'));
          $Qcheck->bindInt(':customers_id', $osC_Customer->getID());
          $Qcheck->execute();

          if ($Qcheck->numberOfRows() !== 1) {
            $osC_ShoppingCart->setShippingAddress($osC_Customer->getDefaultAddressID());
          }
        }

// load all enabled shipping modules
        if (class_exists('osC_Shipping') === false) {
          include('includes/classes/shipping.php');
        }

        $osC_Shipping = new osC_Shipping();

// if no shipping method has been selected, automatically select the cheapest method.
        if ($osC_ShoppingCart->hasShippingMethod() === false) {
          $osC_ShoppingCart->setShippingMethod($osC_Shipping->getCheapestQuote());
        }
      }

      if ($_GET[$this->_module] == 'process') {
        $this->_process();
      }
    }

/* Private methods */

    function _process() {
      global $osC_ShoppingCart, $osC_Shipping;

      if (!empty($_POST['comments'])) {
        $_SESSION['comments'] = osc_sanitize_string($_POST['comments']);
      }

      if ($osC_Shipping->hasQuotes()) {
        if (isset($_POST['shipping_mod_sel']) && strpos($_POST['shipping_mod_sel'], '_')) {
          list($module, $method) = explode('_', $_POST['shipping_mod_sel']);
          $module = 'osC_Shipping_' . $module;

          if (is_object($GLOBALS[$module]) && $GLOBALS[$module]->isEnabled()) {
            $quote = $osC_Shipping->getQuote($_POST['shipping_mod_sel']);

            if (isset($quote['error'])) {
              $osC_ShoppingCart->resetShippingMethod();
            } else {
              $osC_ShoppingCart->setShippingMethod($quote);

              osc_redirect(osc_href_link(FILENAME_CHECKOUT, 'payment', 'SSL'));
            }
          } else {
            $osC_ShoppingCart->resetShippingMethod();
          }
        }
      } else {
        $osC_ShoppingCart->resetShippingMethod();

        osc_redirect(osc_href_link(FILENAME_CHECKOUT, 'payment', 'SSL'));
      }
    }
  }
?>
