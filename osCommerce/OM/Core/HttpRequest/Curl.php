<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\HttpRequest;

  class Curl {
    public static function execute($parameters) {
      $curl = curl_init($parameters['server']['scheme'] . '://' . $parameters['server']['host'] . $parameters['server']['path'] . (isset($parameters['server']['query']) ? '?' . $parameters['server']['query'] : ''));

      curl_setopt($curl, CURLOPT_PORT, $parameters['server']['port']);

      if ( !empty($parameters['header']) ) {
        curl_setopt($curl, CURLOPT_HTTPHEADER, $parameters['header']);
      }

      if ( !empty($parameters['certificate']) ) {
        curl_setopt($curl, CURLOPT_SSLCERT, $parameters['certificate']);
      }

      curl_setopt($curl, CURLOPT_HEADER, 0);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
      curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
      curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($curl, CURLOPT_MAXREDIRS, 5);

      if ( $parameters['method'] == 'post' ) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $parameters['parameters']);
      }

      $result = curl_exec($curl);

      curl_close($curl);

      return $result;
    }

    public static function canUse() {
      return function_exists('curl_init');
    }
  }
?>
