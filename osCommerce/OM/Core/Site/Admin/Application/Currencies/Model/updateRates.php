<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Currencies\Model;

  use osCommerce\OM\Core\Site\Admin\Application\Currencies\Currencies;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Cache;

  class updateRates {
    public static function execute($service) {
      $updated = array('0' => array(),
                       '1' => array());

      $currencies = Currencies::getAll(-1);
      $currencies = $currencies['entries'];

      foreach ( $currencies as $currency ) {
        $data = array('id' => $currency['currencies_id'],
                      'rate' => call_user_func('quote_' . $service . '_currency', $currency['code']));

        if ( !empty($data['rate']) && OSCOM::callDB('Admin\Currencies\UpdateRate', $data) ) {
          $updated[1][] = array('title' => $currency['title'],
                                'code' => $currency['code']);
        } else {
          $updated[0][] = array('title' => $currency['title'],
                                'code' => $currency['code']);
        }
      }

      if ( !empty($updated[1]) ) {
        Cache::clear('currencies');
      }

      return $updated;
    }
  }
?>
