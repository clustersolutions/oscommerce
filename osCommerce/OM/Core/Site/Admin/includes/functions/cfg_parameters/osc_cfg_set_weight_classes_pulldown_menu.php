<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  function osc_cfg_set_weight_classes_pulldown_menu($default, $key = null) {
    global $osC_Database, $osC_Language;

    $name = (empty($key)) ? 'configuration_value' : 'configuration[' . $key . ']';

    $weight_class_array = array();

    foreach (osC_Weight::getClasses() as $class) {
      $weight_class_array[] = array('id' => $class['id'],
                                    'text' => $class['title']);
    }

    return osc_draw_pull_down_menu($name, $weight_class_array, $default);
  }
?>
