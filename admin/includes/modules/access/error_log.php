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

  class osC_Access_Error_log extends osC_Access {
    var $_module = 'error_log',
        $_group = 'tools',
        $_icon = 'error.png',
        $_title,
        $_sort_order = 10;

    function osC_Access_Error_log() {
      global $osC_Language;

      $this->_title = $osC_Language->get('access_error_log_title');
    }
  }
?>
