<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Core\OSCOM;

  function osc_cfg_use_get_boolean_value($string) {
    switch ($string) {
      case -1:
      case '-1':
        return OSCOM::getDef('parameter_false');
        break;

      case 0:
      case '0':
        return OSCOM::getDef('parameter_optional');
        break;

      case 1:
      case '1':
        return OSCOM::getDef('parameter_true');
        break;

      default:
        return $string;
        break;
    }
  }
?>
