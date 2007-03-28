<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  class osC_Access_File_manager extends osC_Access {
    var $_module = 'file_manager',
        $_group = 'tools',
        $_icon = 'file_manager.png',
        $_title,
        $_sort_order = 400;

    function osC_Access_File_manager() {
      global $osC_Language;

      $this->_title = $osC_Language->get('access_file_manager_title');
    }
  }
?>
