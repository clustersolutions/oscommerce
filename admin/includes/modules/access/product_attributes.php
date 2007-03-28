<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  class osC_Access_Product_attributes extends osC_Access {
    var $_module = 'product_attributes',
        $_group = 'content',
        $_icon = 'run.png',
        $_title,
        $_sort_order = 300;

    function osC_Access_Product_attributes() {
      global $osC_Language;

      $this->_title = $osC_Language->get('access_product_attributes_title');
    }
  }
?>
