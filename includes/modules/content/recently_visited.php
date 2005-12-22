<?php
/*
  $Id: $
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class osC_Content_recently_visited extends osC_Modules {
    var $_title = 'Recently Visited',
        $_code = 'recently_visited',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'content';

/* Class constructor */

    function osC_Content_recently_visited() {
    }

    function initialize() {
      $this->_content = 'dummy text';
    }
  }
?>
