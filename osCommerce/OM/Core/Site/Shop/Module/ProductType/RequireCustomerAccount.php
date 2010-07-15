<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Shop\Module\ProductType;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Site\Shop\Product;

  class RequireCustomerAccount {
    public static function getTitle() {
      return 'Require Customer Account';
    }

    public static function getDescription() {
      return 'Require customer account';
    }

    public static function isValid(Product $OSCOM_Product) {
      $OSCOM_Customer = Registry::get('Customer');

      return $OSCOM_Customer->isLoggedOn();
    }

    public static function onFail(Product $OSCOM_Product) {
      $OSCOM_NavigationHistory = Registry::get('NavigationHistory');

      $OSCOM_NavigationHistory->setSnapshot();

      osc_redirect(OSCOM::getLink(null, 'Account', 'LogIn', 'SSL'));
    }
  }
?>
