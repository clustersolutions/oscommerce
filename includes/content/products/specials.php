<?php
/*
  $Id: password.php 64 2005-03-12 16:36:16Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class osC_Products_Specials extends osC_Template {

/* Private variables */

    var $_module = 'specials',
        $_group = 'products',
        $_page_title = HEADING_TITLE_SPECIALS,
        $_page_contents = 'specials.php',
        $_page_image = 'table_background_specials.gif';

/* Class constructor */

    function osC_Products_Specials() {
      global $osC_Services, $breadcrumb;

      if ($osC_Services->isStarted('breadcrumb')) {
        $breadcrumb->add(NAVBAR_TITLE_SPECIALS, tep_href_link(FILENAME_PRODUCTS, $this->_module));
      }
    }
  }
?>
