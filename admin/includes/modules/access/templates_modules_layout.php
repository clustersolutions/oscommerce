<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Access_Templates_modules_layout extends osC_Access {
    var $_module = 'templates_modules_layout',
        $_group = 'templates',
        $_icon = 'windows.png',
        $_title = ACCESS_TEMPLATES_MODULES_LAYOUT_TITLE,
        $_sort_order = 300;

    function osC_Access_Templates_modules_layout() {
      $this->_subgroups = array(array('icon' => 'modules.png',
                                      'title' => ACCESS_TEMPLATES_MODULES_LAYOUT_BOXES_TITLE,
                                      'identifier' => 'set=boxes'),
                                array('icon' => 'windows.png',
                                      'title' => ACCESS_TEMPLATES_MODULES_LAYOUT_CONTENT_TITLE,
                                      'identifier' => 'set=content'));
    }
  }
?>
