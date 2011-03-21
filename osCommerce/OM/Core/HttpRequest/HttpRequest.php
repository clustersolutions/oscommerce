<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\HttpRequest;

  class HttpRequest {
    protected static $_methods = array('get' => HTTP_METH_GET,
                                       'post' => HTTP_METH_POST);

    public static function execute($parameters) {
      $h = new \HttpRequest($parameters['server']['scheme'] . '://' . $parameters['server']['host'] . $parameters['server']['path'] . (isset($parameters['server']['query']) ? '?' . $parameters['server']['query'] : ''), static::$_methods[$parameters['method']], array('redirect' => 5));

      if ( $parameters['method'] == 'post' ) {
        $h->setRawPostData($parameters['parameters']);
      }

      $h->send();

      return $h->getResponseBody();
    }

    public static function canUse() {
      return class_exists('\HttpRequest');
    }
  }
?>
