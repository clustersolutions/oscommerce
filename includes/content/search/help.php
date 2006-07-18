<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Search_Help extends osC_Template {

/* Private variables */

    var $_module = 'help',
        $_group = 'search',
        $_page_title ,
        $_page_contents = 'help.php',
        $_has_header = false,
        $_has_footer = false,
        $_has_box_modules = false,
        $_has_content_modules = false,
        $_show_debug_messages = false;

/* Class constructor */

    function osC_Search_Help() {
      global $osC_Language, $osC_NavigationHistory;

      $this->_page_title = $osC_Language->get('search_heading');

      $osC_NavigationHistory->removeCurrentPage();
    }
  }
?>