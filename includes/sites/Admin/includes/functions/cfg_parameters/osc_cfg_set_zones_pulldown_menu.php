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

  function osc_cfg_set_zones_pulldown_menu($default, $key = null) {
    $name = (!empty($key) ? 'configuration[' . $key . ']' : 'configuration_value');

    $zones_array = array();

    foreach (osC_Address::getZones() as $zone) {
      $zones_array[] = array('id' => $zone['id'],
                             'text' => $zone['name'],
                             'group' => $zone['country_name']);
    }

    return osc_draw_pull_down_menu($name, $zones_array, $default);
  }
?>
