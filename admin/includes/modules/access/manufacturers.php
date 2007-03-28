<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  class osC_Access_Manufacturers extends osC_Access {
    var $_module = 'manufacturers',
        $_group = 'content',
        $_icon = 'home.png',
        $_title,
        $_sort_order = 400;

    function osC_Access_Manufacturers() {
      global $osC_Language;

      $this->_title = $osC_Language->get('access_manufacturers_title');
    }
  }
?>
