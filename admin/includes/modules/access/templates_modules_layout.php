<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  class osC_Access_Templates_modules_layout extends osC_Access {
    var $_module = 'templates_modules_layout',
        $_group = 'templates',
        $_icon = 'windows.png',
        $_title,
        $_sort_order = 300;

    function osC_Access_Templates_modules_layout() {
      global $osC_Language;

      $this->_title = $osC_Language->get('access_templates_modules_layout_title');

      $this->_subgroups = array(array('icon' => 'modules.png',
                                      'title' => $osC_Language->get('access_templates_modules_layout_boxes_title'),
                                      'identifier' => 'set=boxes'),
                                array('icon' => 'windows.png',
                                      'title' => $osC_Language->get('access_templates_modules_layout_content_title'),
                                      'identifier' => 'set=content'));
    }
  }
?>
