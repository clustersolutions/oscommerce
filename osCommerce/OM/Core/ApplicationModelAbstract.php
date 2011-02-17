<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core;

  use osCommerce\OM\Core\OSCOM;

  abstract class ApplicationModelAbstract {
    public static function __callStatic($name, $arguments) {
      $class = get_called_class();

      $ns = substr($class, 0, strrpos($class, '\\'));

      return call_user_func_array(array($ns . '\\Model\\' . $name, 'execute'), $arguments);
    }
  }
?>
