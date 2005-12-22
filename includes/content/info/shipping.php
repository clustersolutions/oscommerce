<?php
/*
  $Id: password.php 64 2005-03-12 16:36:16Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class osC_Info_Shipping extends osC_Template {

/* Private variables */

    var $_module = 'shipping',
        $_group = 'info',
        $_page_title = HEADING_INFO_SHIPPING,
        $_page_contents = 'info_shipping.php';

/* Class constructor */

    function osC_Info_Shipping() {
      global $osC_Services, $breadcrumb;

      if ($osC_Services->isStarted('breadcrumb')) {
        $breadcrumb->add(BREADCRUMB_INFO_SHIPPING, tep_href_link(FILENAME_INFO, $this->_module));
      }
    }
  }
?>
