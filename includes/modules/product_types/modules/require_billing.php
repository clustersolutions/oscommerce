<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2009 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_ProductTypes_Modules_require_billing {
    public static function getTitle() {
      return 'Require Billing';
    }

    public static function getDescription() {
      return 'Require billing';
    }

    public static function isValid(osC_Product $osC_Product) {
      global $osC_Customer, $osC_ShoppingCart;

      if ( !$osC_ShoppingCart->hasBillingAddress() ) {
        if ( $osC_Customer->hasDefaultAddress() ) {
          $osC_ShoppingCart->setBillingAddress($osC_Customer->getDefaultAddressID());
          $osC_ShoppingCart->resetBillingMethod();
        } elseif ( $osC_ShoppingCart->hasShippingAddress() ) {
          $osC_ShoppingCart->setBillingAddress($osC_ShoppingCart->getShippingAddress());
          $osC_ShoppingCart->resetBillingMethod();
        }
      }

      if ( !$osC_ShoppingCart->hasBillingMethod() ) {
        if ( !class_exists('osC_Payment') ) {
          include('includes/classes/payment.php');
        }

        $osC_Payment = new osC_Payment();

        if ( $osC_Payment->numberOfActive() === 1 ) {
          $osC_ShoppingCart->setBillingMethod(array('id' => $GLOBALS['osC_Payment_' . reset($osC_Payment->getActive())]->getCode(), 'title' => $GLOBALS['osC_Payment_' . reset($osC_Payment->getActive())]->getMethodTitle()));
        }
      }

      return $osC_ShoppingCart->hasBillingAddress() && $osC_ShoppingCart->hasBillingMethod();
    }

    public static function onFail(osC_Product $osC_Product) {
      global $osC_NavigationHistory;

      $osC_NavigationHistory->setSnapshot();

      osc_redirect(osc_href_link(FILENAME_CHECKOUT, 'billing', 'SSL'));
    }
  }
?>
