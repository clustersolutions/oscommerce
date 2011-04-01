<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\Site\Shop\Address;

  function osc_cfg_set_zones_pulldown_menu($default, $key = null) {
    $name = (!empty($key) ? 'configuration[' . $key . ']' : 'configuration_value');

    $zones_array = array();

    foreach ( Address::getZones() as $zone ) {
      $zones_array[] = array('id' => $zone['id'],
                             'text' => $zone['name'],
                             'group' => $zone['country_name']);
    }

    return HTML::selectMenu($name, $zones_array, $default);
  }
?>
