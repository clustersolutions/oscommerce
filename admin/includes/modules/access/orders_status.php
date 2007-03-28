<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  class osC_Access_Orders_status extends osC_Access {
    var $_module = 'orders_status',
        $_group = 'definitions',
        $_icon = 'status.png',
        $_title,
        $_sort_order = 100;

    function osC_Access_Orders_status() {
      global $osC_Language;

      $this->_title = $osC_Language->get('access_orders_status_title');
    }
  }
?>
