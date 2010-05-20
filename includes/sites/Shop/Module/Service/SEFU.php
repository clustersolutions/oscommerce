<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Shop\Module\Service;

  class SEFU implements \osCommerce\OM\Site\Shop\ServiceInterface {
    public static function start() {
      if ( isset($_SERVER['ORIG_PATH_INFO']) ) {
        if ( isset($_SERVER['PATH_INFO']) && empty($_SERVER['PATH_INFO']) ) {
          $_SERVER['PATH_INFO'] = $_SERVER['ORIG_PATH_INFO'];
        }
      }

      if ( isset($_SERVER['PATH_INFO']) && (strlen($_SERVER['PATH_INFO']) > 1) ) {
        $parameters = explode('/', substr($_SERVER['PATH_INFO'], 1));

        $_GET = array();
        $GET_array = array();

        foreach ( $parameters as $parameter ) {
          $param_array = explode(',', $parameter, 2);

          if ( !isset($param_array[1]) ) {
            $param_array[1] = '';
          }

          if ( strpos($param_array[0], '[]') !== false ) {
            $GET_array[substr($param_array[0], 0, -2)][] = $param_array[1];
          } else {
            $_GET[$param_array[0]] = $param_array[1];
          }

          $i++;
        }

        if ( sizeof($GET_array) > 0 ) {
          foreach ( $GET_array as $key => $value ) {
            $_GET[$key] = $value;
          }
        }
      }

      return true;
    }

    public static function stop() {
      return true;
    }
  }
?>
