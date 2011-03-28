<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core;

  class Hash {
    protected static $_drivers = array('Salt');

/**
 *
 * @param string $string
 * @param string $driver 
 */

    public static function get($string, $driver = null) {
      if ( !isset($driver) ) {
        foreach ( static::$_drivers as $d ) {
          if ( call_user_func(array('osCommerce\\OM\\Core\\Hash\\' . $d, 'canUse')) ) {
            $driver = $d;

            break;
          }
        }
      }

      return call_user_func(array('osCommerce\\OM\\Core\\Hash\\' . $driver, 'get'), $string);
    }

    public static function validate($plain, $hashed, $driver = null) {
      if ( !isset($driver) ) {
        foreach ( static::$_drivers as $d ) {
          if ( call_user_func(array('osCommerce\\OM\\Core\\Hash\\' . $d, 'canUse')) ) {
            $driver = $d;

            break;
          }
        }
      }

      return call_user_func(array('osCommerce\\OM\\Core\\Hash\\' . $driver, 'validate'), $plain, $hashed);
    }
  }
?>
