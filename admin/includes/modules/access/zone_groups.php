<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  class osC_Access_Zone_groups extends osC_Access {
    var $_module = 'zone_groups',
        $_group = 'configuration',
        $_icon = 'relationships.png',
        $_title,
        $_sort_order = 700;

    function osC_Access_Zone_groups() {
      global $osC_Language;

      $this->_title = $osC_Language->get('access_zone_groups_title');
    }
  }
?>
