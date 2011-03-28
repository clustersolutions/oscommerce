<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\Site\Shop\Weight;

  function osc_cfg_set_weight_classes_pulldown_menu($default, $key = null) {
    $name = (empty($key)) ? 'configuration_value' : 'configuration[' . $key . ']';

    $weight_class_array = array();

    foreach ( Weight::getClasses() as $class ) {
      $weight_class_array[] = array('id' => $class['id'],
                                    'text' => $class['title']);
    }

    return HTML::selectMenu($name, $weight_class_array, $default);
  }
?>
