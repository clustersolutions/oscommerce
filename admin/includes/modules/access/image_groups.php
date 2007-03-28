<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  class osC_Access_Image_groups extends osC_Access {
    var $_module = 'image_groups',
        $_group = 'definitions',
        $_icon = 'status.png',
        $_title,
        $_sort_order = 300;

    function osC_Access_Image_groups() {
      global $osC_Language;

      $this->_title = $osC_Language->get('access_image_groups_title');
    }
  }
?>
