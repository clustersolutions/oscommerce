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
        $_page_title,
        $_page_contents = 'specials.php',
        $_page_image = 'table_background_specials.gif';

/* Class constructor */

    function osC_Products_Specials() {
      global $osC_Services, $osC_Language, $breadcrumb;

      $this->_page_title = $osC_Language->get('specials_heading');

      if ($osC_Services->isStarted('breadcrumb')) {
        $breadcrumb->add($osC_Language->get('breadcrumb_specials'), tep_href_link(FILENAME_PRODUCTS, $this->_module));
      }
    }
  }
?>
