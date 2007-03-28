<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  class osC_Access_Tax_classes extends osC_Access {
    var $_module = 'tax_classes',
        $_group = 'configuration',
        $_icon = 'classes.png',
        $_title,
        $_sort_order = 800;

    function osC_Access_Tax_classes() {
      global $osC_Language;

      $this->_title = $osC_Language->get('access_tax_classes_title');
    }
  }
?>
