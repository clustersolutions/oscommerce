<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  function osc_cfg_use_get_boolean_value($string) {
    switch ($string) {
      case -1:
      case '-1':
        return TEXT_FALSE;
        break;

      case 0:
      case '0':
        return TEXT_OPTIONAL;
        break;

      case 1:
      case '1':
        return TEXT_TRUE;
        break;

      default:
        return $string;
        break;
    }
  }
?>
