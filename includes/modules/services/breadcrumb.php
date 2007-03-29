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

  class osC_Services_breadcrumb {
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
  }
?>
