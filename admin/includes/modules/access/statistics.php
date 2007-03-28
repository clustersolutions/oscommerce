<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  class osC_Access_Statistics extends osC_Access {
    var $_module = 'statistics',
        $_group = 'tools',
        $_icon = 'statistics.png',
        $_title,
        $_sort_order = 700;

    function osC_Access_Statistics() {
      global $osC_Language;

      $this->_title = $osC_Language->get('access_statistics_title');
    }
  }
?>
