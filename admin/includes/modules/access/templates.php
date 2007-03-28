<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  class osC_Access_Templates extends osC_Access {
    var $_module = 'templates',
        $_group = 'templates',
        $_icon = 'file.png',
        $_title,
        $_sort_order = 100;

    function osC_Access_Templates() {
      global $osC_Language;

      $this->_title = $osC_Language->get('access_templates_title');
    }
  }
?>
