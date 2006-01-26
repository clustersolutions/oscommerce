<?php
/*
  $Id: $
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Content_recently_visited extends osC_Modules {
    var $_title,
        $_code = 'recently_visited',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'content';

/* Class constructor */

    function osC_Content_recently_visited() {
      global $osC_Language;

      $this->_title = $osC_Language->get('recently_visited_title');
    }

    function initialize() {
      $this->_content = 'dummy text';
    }
  }
?>
