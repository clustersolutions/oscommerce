<?php
/*
  $Id: password.php 64 2005-03-12 16:36:16Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class osC_Info_Info extends osC_Template {

/* Private variables */

    var $_module = 'info',
        $_group = 'info',
        $_page_title,
        $_page_contents = 'info.php',
        $_page_image = 'table_background_account.gif';

    function osC_Info_Info() {
      global $osC_Language;

      $this->_page_title = $osC_Language->get('info_heading');
    }
  }
?>
