<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  class osC_Access_Countries extends osC_Access {
    var $_module = 'countries',
        $_group = 'configuration',
        $_icon = 'world.png',
        $_title,
        $_sort_order = 600;

    function osC_Access_Countries() {
      global $osC_Language;

      $this->_title = $osC_Language->get('access_countries_title');
    }
  }
?>
