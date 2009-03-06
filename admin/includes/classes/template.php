<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  require('../includes/classes/template.php');

  class osC_Template_Admin extends osC_Template {
    function &setup($module) {
      $class = 'osC_Application_' . ucfirst($module);

      if ( isset($_GET['action']) && !empty($_GET['action']) ) {
        $_action = osc_sanitize_string(basename($_GET['action']));

        if ( file_exists('includes/applications/' . $module . '/actions/' . $_action . '.php') ) {
          include('includes/applications/' . $module . '/actions/' . $_action . '.php');

          $class = 'osC_Application_' . ucfirst($module) . '_Actions_' . $_action;
        }
      }

      $object = new $class();

      return $object;
    }
  }
?>
