<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  class osC_Services_recently_visited {
    function start() {
      global $osC_Services, $osC_RecentlyVisited;

      include('includes/classes/recently_visited.php');

      $osC_RecentlyVisited = new osC_RecentlyVisited();

      $osC_Services->addCallBeforePageContent('osC_RecentlyVisited', 'initialize');

      return true;
    }

    function stop() {
      return true;
    }
  }
?>
