<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  class osC_Access_Backup extends osC_Access {
    var $_module = 'backup',
        $_group = 'tools',
        $_icon = 'tape.png',
        $_title,
        $_sort_order = 100;

    function osC_Access_Backup() {
      global $osC_Language;

      $this->_title = $osC_Language->get('access_backup_title');
    }
  }
?>
