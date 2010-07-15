<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Shop\Module\Service;

  use osCommerce\OM\Registry;
  use osCommerce\OM\Site\Shop\Customer;
  use osCommerce\OM\Site\Shop\Tax;
  use osCommerce\OM\Site\Shop\Weight;
  use osCommerce\OM\Site\Shop\ShoppingCart;
  use osCommerce\OM\Site\Shop\NavigationHistory;
  use osCommerce\OM\Site\Shop\Image;

  class Core implements \osCommerce\OM\Site\Shop\ServiceInterface {
    public static function start() {
      Registry::set('Customer', new Customer());

      Registry::set('Tax', new Tax());

      Registry::set('Weight', new Weight());

      Registry::set('ShoppingCart', new ShoppingCart());
      Registry::get('ShoppingCart')->refresh();

      Registry::set('NavigationHistory', new NavigationHistory(true));

      Registry::set('Image', new Image());

      return true;
    }

    public static function stop() {
      return true;
    }
  }
?>
