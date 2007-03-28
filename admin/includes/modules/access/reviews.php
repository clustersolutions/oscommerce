<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  class osC_Access_Reviews extends osC_Access {
    var $_module = 'reviews',
        $_group = 'content',
        $_icon = 'write.png',
        $_title,
        $_sort_order = 600;

    function osC_Access_Reviews() {
      global $osC_Language;

      $this->_title = $osC_Language->get('access_reviews_title');
    }
  }
?>
