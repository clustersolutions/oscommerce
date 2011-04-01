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

  function osc_cfg_set_countries_pulldown_menu($default, $key = null) {
    $name = (!empty($key) ? 'configuration[' . $key . ']' : 'configuration_value');

    $countries_array = array();

    foreach ( Address::getCountries() as $country ) {
      $countries_array[] = array('id' => $country['id'],
                                 'text' => $country['name']);
    }

    return HTML::selectMenu($name, $countries_array, $default);
  }
?>
