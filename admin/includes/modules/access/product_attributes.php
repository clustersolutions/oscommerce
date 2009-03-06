<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Access_Product_attributes extends osC_Access {
    var $_module = 'product_attributes',
        $_group = 'modules',
        $_icon = 'run.png',
        $_title,
        $_sort_order = 600;

    function __construct() {
      global $osC_Language;

      $this->_title = $osC_Language->get('access_product_attributes_title');
    }
  }
?>
