<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Shop\Module\ProductType;

  use osCommerce\OM\Site\Shop\Product;

  class False {
    public static function getTitle() {
      return 'False';
    }

    public static function getDescription() {
      return 'Fail action with false';
    }

    public static function isValid(Product $OSCOM_Product) {
      return false;
    }
  }
?>
