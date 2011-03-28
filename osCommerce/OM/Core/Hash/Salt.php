<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Hash;

  class Salt {
    public static function get($string) {
      $hash = '';

      for ($i=0; $i<10; $i++) {
        $hash .= mt_rand();
      }

      $salt = substr(md5($hash), 0, 2);

      $hash = md5($salt . $string) . ':' . $salt;

      return $hash;
    }

    public static function validate($plain, $hashed) {
      if ( !empty($plain) && !empty($hashed) ) {
  // split apart the hash / salt
        $stack = explode(':', $hashed);

        if ( count($stack) != 2 ) {
          return false;
        }

        return ( md5($stack[1] . $plain) == $stack[0] );
      }

      return false;
    }

    public static function canUse() {
      return function_exists('mt_rand');
    }
  }
?>
