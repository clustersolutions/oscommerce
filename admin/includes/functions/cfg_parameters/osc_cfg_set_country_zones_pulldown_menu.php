<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  function osc_cfg_set_country_zones_pulldown_menu($country_id, $default_zone_id = null, $key) {
    $name = (!empty($key) ? 'configuration[' . $key . ']' : 'configuration_value');

    if (!is_numeric($country_id) && is_string($country_id)) {
      if (defined($country_id)) {
        $country_id = constant($country_id);
      }
    }

    $zones_array = array();

    foreach (osC_Address::getCountryZones($country_id) as $zone) {
      $zones_array[] = array('id' => $zone['id'],
                             'text' => $zone['name']);
    }

    return osc_draw_pull_down_menu($name, $zones_array, $default_zone_id);
  }
?>
