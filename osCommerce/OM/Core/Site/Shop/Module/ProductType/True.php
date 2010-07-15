<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Shop\Module\ProductType;

  use osCommerce\OM\Core\Site\Shop\Product;

  class True {
    public static function getTitle() {
      return 'True';
    }

    public static function getDescription() {
      return 'Pass action with true';
    }

    public static function isValid(Product $OSCOM_Product) {
      return true;
    }
  }
?>
