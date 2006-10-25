<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Access_Templates_modules extends osC_Access {
    var $_module = 'templates_modules',
        $_group = 'templates',
        $_icon = 'modules.png',
        $_title = ACCESS_TEMPLATES_MODULES_TITLE,
        $_sort_order = 200;

    function osC_Access_Templates_modules() {
      $this->_subgroups = array(array('icon' => 'modules.png',
                                      'title' => ACCESS_TEMPLATES_MODULES_BOXES_TITLE,
                                      'identifier' => 'set=boxes'),
                                array('icon' => 'windows.png',
                                      'title' => ACCESS_TEMPLATES_MODULES_CONTENT_TITLE,
                                      'identifier' => 'set=content'));
    }
  }
?>
