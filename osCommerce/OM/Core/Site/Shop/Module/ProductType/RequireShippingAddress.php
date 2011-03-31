<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Shop\Module\ProductType;

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Site\Shop\Product;

  class RequireShippingAddress {
    public static function getTitle() {
      return 'Require Shipping Address';
    }

    public static function getDescription() {
      return 'Require shipping address';
    }

    public static function isValid(Product $OSCOM_Product) {
      $OSCOM_ShoppingCart = Registry::get('ShoppingCart');

      return $OSCOM_ShoppingCart->hasShippingAddress();
    }

    public static function onFail(Product $OSCOM_Product) {
      $OSCOM_NavigationHistory = Registry::get('NavigationHistory');

      if ( !isset($_GET['Shipping']) || !isset($_GET['Address']) ) {
        $OSCOM_NavigationHistory->setSnapshot();

        OSCOM::redirect(OSCOM::getLink(null, 'Checkout', 'Shipping&Address', 'SSL'));
      }
    }
  }
?>
