<?php
/*
  $Id: whos_online.php 399 2006-01-25 06:08:03Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Content_Whos_online extends osC_Template {

/* Private variables */

    var $_module = 'whos_online',
        $_page_title = HEADING_TITLE,
        $_page_contents = 'whos_online.php';

/* Class constructor */

    function osC_Content_Whos_online() {
      if (!isset($_GET['action'])) {
        $_GET['action'] = '';
      }
    }
  }
?>
