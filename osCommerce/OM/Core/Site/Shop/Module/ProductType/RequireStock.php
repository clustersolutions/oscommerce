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

  class RequireStock {
    public static function getTitle() {
      return 'Require Products In Stock';
    }

    public static function getDescription() {
      return 'Require products to be in stock';
    }

    public static function isValid(Product $OSCOM_Product) {
      $OSCOM_ShoppingCart = Registry::get('ShoppingCart');

      return ( ($OSCOM_Product->getQuantity() - $OSCOM_ShoppingCart->getQuantity( $OSCOM_ShoppingCart->getBasketID($OSCOM_Product->getID()) ))  > 0 );
    }

    public static function onFail(Product $OSCOM_Product) {
      $OSCOM_NavigationHistory = Registry::get('NavigationHistory');

      $OSCOM_NavigationHistory->setSnapshot();

      OSCOM::redirect(OSCOM::getLink(null, 'Cart', null, 'SSL'));
    }
  }
?>
