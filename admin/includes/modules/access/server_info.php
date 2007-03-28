<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  class osC_Access_Server_info extends osC_Access {
    var $_module = 'server_info',
        $_group = 'tools',
        $_icon = 'server_info.png',
        $_title,
        $_sort_order = 900;

    function osC_Access_Server_info() {
      global $osC_Language;

      $this->_title = $osC_Language->get('access_server_info_title');
    }
  }
?>
