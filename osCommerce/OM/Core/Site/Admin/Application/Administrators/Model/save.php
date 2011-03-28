<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Administrators\Model;

  use osCommerce\OM\Core\Hash;
  use osCommerce\OM\Core\OSCOM;

  class save {
    public static function execute($data) {
      if ( !empty($data['password']) ) {
        $data['password'] = Hash::get(trim($data['password']));
      }

      return OSCOM::callDB('Admin\Administrators\Save', $data);
    }
  }
?>
