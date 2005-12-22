<?php
/*
  $Id: password.php 64 2005-03-12 16:36:16Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class osC_Info_Ssl_check extends osC_Template {

/* Private variables */

    var $_module = 'ssl_check',
        $_group = 'info',
        $_page_title = HEADING_INFO_SSL_CHECK,
        $_page_contents = 'ssl_check.php';

/* Class constructor */

    function osC_Info_Ssl_check() {
      global $osC_Services, $breadcrumb;

      if ($osC_Services->isStarted('breadcrumb')) {
        $breadcrumb->add(BREADCRUMB_INFO_SSL_CHECK, tep_href_link(FILENAME_INFO, $this->_module));
      }
    }
  }
?>
