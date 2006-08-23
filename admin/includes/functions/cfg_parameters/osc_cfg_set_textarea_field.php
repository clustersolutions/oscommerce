<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  function osc_cfg_set_textarea_field($default, $key = null) {
    $name = (!empty($key) ? 'configuration[' . $key . ']' : 'configuration_value');

    return osc_draw_textarea_field($name, $default, 35, 5);
  }
?>
