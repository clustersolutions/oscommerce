<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\PaymentModules\Model;

  use osCommerce\OM\Core\Site\Admin\Application\PaymentModules\PaymentModules;

  class findInstalled {
    public static function execute($search) {
      $modules = PaymentModules::getInstalled();

      $result = array('entries' => array());

      foreach ( $modules['entries'] as $module ) {
        if ( (stripos($module['code'], $search) !== false) || (stripos($module['title'], $search) !== false) ) {
          $result['entries'][] = $module;
        }
      }

      $result['total'] = count($result['entries']);

      return $result;
    }
  }
?>
