<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  function osc_cfg_use_get_boolean_value($string) {
    global $osC_Language;

    switch ($string) {
      case -1:
      case '-1':
        return $osC_Language->get('parameter_false');
        break;

      case 0:
      case '0':
        return $osC_Language->get('parameter_optional');
        break;

      case 1:
      case '1':
        return $osC_Language->get('parameter_true');
        break;

      default:
        return $string;
        break;
    }
  }
?>
