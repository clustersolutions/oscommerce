<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  class osC_Access_Weight_classes extends osC_Access {
    var $_module = 'weight_classes',
        $_group = 'definitions',
        $_icon = 'weight.png',
        $_title,
        $_sort_order = 200;

    function osC_Access_Weight_classes() {
      global $osC_Language;

      $this->_title = $osC_Language->get('access_weight_classes_title');
    }
  }
?>
