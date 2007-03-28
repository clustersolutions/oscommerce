<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  class osC_Access_Cache extends osC_Access {
    var $_module = 'cache',
        $_group = 'tools',
        $_icon = 'log.png',
        $_title,
        $_sort_order = 300;

    function osC_Access_Cache() {
      global $osC_Language;

      $this->_title = $osC_Language->get('access_cache_title');
    }
  }
?>
