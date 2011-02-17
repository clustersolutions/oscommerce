<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Currencies\Model;

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Cache;

  class save {
    public static function execute($data) {
      if ( OSCOM::callDB('Admin\Currencies\Save', $data) ) {
        Cache::clear('currencies');

        if ( $data['set_default'] === true ) {
          Cache::clear('configuration');
        }

        return true;
      }

      return false;
    }
  }
?>
