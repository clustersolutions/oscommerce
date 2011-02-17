<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Languages\Model;

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Cache;

  class update {
    public static function execute($data) {
      if ( OSCOM::callDB('Admin\Languages\Update', $data) ) {
        Cache::clear('languages');

        if ( $data['set_default'] === true ) {
          Cache::clear('configuration');
        }

        return true;
      }

      return false;
    }
  }
?>
