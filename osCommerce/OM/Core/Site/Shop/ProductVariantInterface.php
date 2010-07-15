<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Shop;

  interface ProductVariantInterface {
    public static function parse($data);

    public static function allowsMultipleValues();

    public static function hasCustomValue();

    public static function getGroupTitle($data);

    public static function getValueTitle($data);
  }
?>
