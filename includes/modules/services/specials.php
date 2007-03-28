<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
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
