<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Access_Product_types extends osC_Access {
    var $_module = 'product_types',
        $_group = 'content',
        $_icon = 'attach.png',
        $_title,
        $_sort_order = 250;

    function osC_Access_Product_types() {
      global $osC_Language;

      $this->_title = $osC_Language->get('access_product_types_title');
    }
  }
?>
