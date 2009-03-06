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
      global $osC_Breadcrumb, $osC_Language;

      include('includes/classes/breadcrumb.php');
      $osC_Breadcrumb = new osC_Breadcrumb();

      $osC_Breadcrumb->add($osC_Language->get('breadcrumb_top'), HTTP_SERVER);
      $osC_Breadcrumb->add($osC_Language->get('breadcrumb_shop'), osc_href_link(FILENAME_DEFAULT));

      return true;
    }

    function stop() {
      return true;
    }
  }
?>
