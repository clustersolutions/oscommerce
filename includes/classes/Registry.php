<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM;

  class Registry {
    static private $_data = array();

    static public function get($key) {
      if ( substr($key, 0, 4) == 'osC_' ) { // HPDL to delete
      } elseif ( substr($key, 0, 6) != 'OSCOM_' ) {
        $key = 'OSCOM_' . $key;
      }

      if ( !self::exists($key) ) {
        trigger_error('OSCOM_Registry::get - ' . $key . ' is not registered');

        return $GLOBALS[$key]; // HPDL to delete; return false
      }

      return self::$_data[$key];
    }

    static public function set($key, $value, $force = false) {
      if ( substr($key, 0, 4) == 'osC_' ) { // HPDL to delete
      } elseif ( substr($key, 0, 6) != 'OSCOM_' ) {
        $key = 'OSCOM_' . $key;
      }

      if ( self::exists($key) && ($force !== true) ) {
        trigger_error('OSCOM_Registry::set - ' . $key . ' already registered and is not forced to be replaced');

        return false;
      }

      $GLOBALS[$key] = self::$_data[$key] = $value; // HPDL remove GLOBALS alias?
    }

    static public function exists($key) {
      if ( substr($key, 0, 4) == 'osC_' ) { // HPDL to delete
      } elseif ( substr($key, 0, 6) != 'OSCOM_' ) {
        $key = 'OSCOM_' . $key;
      }

      return array_key_exists($key, self::$_data);
    }
  }
?>
