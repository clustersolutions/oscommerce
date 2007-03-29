<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
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
