<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

/**
 * The osC_Actions class loads action modules to execute specific tasks
 */

  class osC_Actions {

/**
 * Loads the action module to execute
 *
 * @param string $module The name of the module to execute
 * @access public
 */

    public static function parse($module) {
      $module = basename($module);

      if ( !empty($module) && file_exists('includes/modules/actions/' . $module . '.php') ) {
        include('includes/modules/actions/' . $module . '.php');

        call_user_func(array('osC_Actions_' . $module, 'execute'));
      }
    }
  }
?>
