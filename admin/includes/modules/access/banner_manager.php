<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  class osC_Access_Banner_manager extends osC_Access {
    var $_module = 'banner_manager',
        $_group = 'tools',
        $_icon = 'windows.png',
        $_title,
        $_sort_order = 200;

    function osC_Access_Banner_manager() {
      global $osC_Language;

      $this->_title = $osC_Language->get('access_banner_manager_title');
    }
  }
?>
