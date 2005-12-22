<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  class osC_Services_breadcrumb {
    var $title = 'Breadcrumb',
        $description = 'Breadcrumb builder for easy navigation.',
        $uninstallable = true,
        $depends,
        $preceeds;

    function start() {
      global $breadcrumb, $osC_Database, $cPath, $cPath_array;

      include('includes/classes/breadcrumb.php');
      $breadcrumb = new breadcrumb;

      $breadcrumb->add(HEADER_TITLE_TOP, HTTP_SERVER);
      $breadcrumb->add(HEADER_TITLE_CATALOG, tep_href_link(FILENAME_DEFAULT));

      return true;
    }

    function stop() {
      return true;
    }

    function install() {
      return false;
    }

    function remove() {
      return false;
    }

    function keys() {
      return false;
    }
  }
?>
