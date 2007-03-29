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

  function osc_cfg_set_textarea_field($default, $key = null) {
    $name = (!empty($key) ? 'configuration[' . $key . ']' : 'configuration_value');

    return osc_draw_textarea_field($name, $default, 35, 5);
  }
?>
