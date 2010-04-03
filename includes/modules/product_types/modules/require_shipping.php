<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2009 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_ProductTypes_Modules_require_shipping {
    public static function getTitle() {
      return 'Require Shipping';
    }

    public static function getDescription() {
      return 'Require shipping';
    }

    public static function isValid(osC_Product $osC_Product) {
      global $osC_Customer, $osC_ShoppingCart;

      if ( !$osC_ShoppingCart->hasShippingAddress() ) {
        if ( $osC_Customer->hasDefaultAddress() ) {
          $osC_ShoppingCart->setShippingAddress($osC_Customer->getDefaultAddressID());
          $osC_ShoppingCart->resetShippingMethod();
        } elseif ( $osC_ShoppingCart->hasBillingAddress() ) {
          $osC_ShoppingCart->setShippingAddress($osC_ShoppingCart->getBillingAddress());
          $osC_ShoppingCart->resetShippingMethod();
        }
      }

      return $osC_ShoppingCart->hasShippingAddress() && $osC_ShoppingCart->hasShippingMethod();
    }

    public static function onFail(osC_Product $osC_Product) {
      global $osC_NavigationHistory;

      $osC_NavigationHistory->setSnapshot();

      osc_redirect(osc_href_link(FILENAME_CHECKOUT, 'shipping', 'SSL'));
    }
  }
?>
