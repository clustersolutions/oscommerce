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

  class osC_Access_Products extends osC_Access {
    var $_module = 'products',
        $_group = 'content',
        $_icon = 'products.png',
        $_title,
        $_sort_order = 200;

    function osC_Access_Products() {
      global $osC_Language;

      $this->_title = $osC_Language->get('access_products_title');

      $this->_subgroups = array(array('icon' => 'specials.png',
                                      'title' => $osC_Language->get('access_products_new_title'),
                                      'identifier' => 'action=save'));
    }
  }
?>
