<?php
/*
  $Id:breadcrumb.php 293 2005-11-29 17:34:26Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Services_breadcrumb {
    var $title = 'Breadcrumb',
        $description = 'Breadcrumb builder for easy navigation.',
        $uninstallable = true,
        $depends,
        $precedes;

    function start() {
      global $breadcrumb, $osC_Database, $osC_Language, $cPath, $cPath_array;

      include('includes/classes/breadcrumb.php');
      $breadcrumb = new breadcrumb;

      $breadcrumb->add($osC_Language->get('breadcrumb_top'), HTTP_SERVER);
      $breadcrumb->add($osC_Language->get('breadcrumb_shop'), osc_href_link(FILENAME_DEFAULT));

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
