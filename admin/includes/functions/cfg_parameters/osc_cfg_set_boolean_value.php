<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  function osc_cfg_set_boolean_value($select_array, $default, $key = null) {
    $string = '';

    $select_array = explode(',', substr($select_array, 6, -1));

    $name = (!empty($key) ? 'configuration[' . $key . ']' : 'configuration_value');

    for ($i=0, $n=sizeof($select_array); $i<$n; $i++) {
      $value = trim($select_array[$i]);

      if (strpos($value, '\'') !== false) {
        $value = substr($value, 1, -1);
      } else {
        $value = (int)$value;
      }

      $select_array[$i] = $value;

      if ($value === -1) {
        $value = TEXT_FALSE;
      } elseif ($value === 0) {
        $value = TEXT_OPTIONAL;
      } elseif ($value === 1) {
        $value = TEXT_TRUE;
      }

      $string .= '<input type="radio" name="' . $name . '" value="' . $select_array[$i] . '"';

      if ($default == $select_array[$i]) $string .= ' checked="checked"';

      $string .= '> ' . $value . '<br />';
    }

    if (!empty($string)) {
      $string = substr($string, 0, -6);
    }

    return $string;
  }
?>
