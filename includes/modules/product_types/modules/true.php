<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2009 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_ProductTypes_Modules_true {
    public static function getTitle() {
      return 'True';
    }

    public static function getDescription() {
      return 'Pass action with true';
    }

    public static function isValid(osC_Product $osC_Product) {
      return true;
    }
  }
?>
