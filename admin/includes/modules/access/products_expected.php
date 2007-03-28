<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  class osC_Access_Products_expected extends osC_Access {
    var $_module = 'products_expected',
        $_group = 'content',
        $_icon = 'date.png',
        $_title,
        $_sort_order = 700;

    function osC_Access_Products_expected() {
      global $osC_Language;

      $this->_title = $osC_Language->get('access_products_expected_title');
    }
  }
?>
