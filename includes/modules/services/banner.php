<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  class osC_Services_banner {
    function start() {
      global $osC_Banner;

      require('includes/classes/banner.php');
      $osC_Banner = new osC_Banner();

      $osC_Banner->activateAll();
      $osC_Banner->expireAll();

      return true;
    }

    function stop() {
      return true;
    }
  }
?>
