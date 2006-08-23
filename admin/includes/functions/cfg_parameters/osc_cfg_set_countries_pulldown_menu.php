<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  function osc_cfg_set_countries_pulldown_menu($default, $key = null) {
    $name = (!empty($key) ? 'configuration[' . $key . ']' : 'configuration_value');

    $countries_array = array();

    foreach (osC_Address::getCountries() as $country) {
      $countries_array[] = array('id' => $country['id'],
                                 'text' => $country['name']);
    }

    return osc_draw_pull_down_menu($name, $countries_array, $default);
  }
?>
