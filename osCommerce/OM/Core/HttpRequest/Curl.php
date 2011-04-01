<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
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
