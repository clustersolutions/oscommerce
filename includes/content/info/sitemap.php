<?php
/*
  $Id: password.php 64 2005-03-12 16:36:16Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Info_Sitemap extends osC_Template {

/* Private variables */

    var $_module = 'sitemap',
        $_group = 'info',
        $_page_title,
        $_page_contents = 'info_sitemap.php',
        $_page_image = 'table_background_specials.gif';

/* Class constructor */

    function osC_Info_Sitemap() {
      global $osC_Services, $osC_Language, $osC_Breadcrumb;

      $this->_page_title = $osC_Language->get('info_sitemap_heading');

      if ($osC_Services->isStarted('breadcrumb')) {
        $osC_Breadcrumb->add($osC_Language->get('breadcrumb_sitemap'), osc_href_link(FILENAME_INFO, $this->_module));
      }
    }
  }
?>
