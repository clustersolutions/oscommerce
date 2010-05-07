<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Services_specials {
    function start() {
      global $osC_Specials;

      require('includes/classes/specials.php');
      $osC_Specials = new osC_Specials();

      $osC_Specials->activateAll();
      $osC_Specials->expireAll();

      return true;
    }

    function stop() {
      return true;
    }
  }
?>
