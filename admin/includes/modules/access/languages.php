<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  class osC_Access_Languages extends osC_Access {
    var $_module = 'languages',
        $_group = 'configuration',
        $_icon = 'locale.png',
        $_title,
        $_sort_order = 400;

    function osC_Access_Languages() {
      global $osC_Language;

      $this->_title = $osC_Language->get('access_languages_title');
    }
  }
?>
