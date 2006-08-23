<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
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
